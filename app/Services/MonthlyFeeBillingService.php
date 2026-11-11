<?php

namespace App\Services;

use App\Models\CartManagement;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MonthlyFeeBillingService
{
    public const MIN_DAYS_BETWEEN_INVOICES = 30;

    public function billingPeriod(?Carbon $forDate = null): array
    {
        $date = $forDate ?? Carbon::now();

        return [
            'month' => (int) $date->format('n'),
            'year' => (int) $date->format('Y'),
        ];
    }

    /**
     * Active trial enrollments, one row per (user_id, course_id) — latest enrollment id.
     */
    public function eligibleEnrollments(?Carbon $now = null): Collection
    {
        $now = $now ?? Carbon::now();

        $latestIds = Enrollment::query()
            ->selectRaw('MAX(id) as id')
            ->where('from_license', 0)
            ->whereNotNull('main_price')
            ->whereNotNull('price')
            ->where('status', 1)
            ->where('end_date', '>', $now)
            ->whereHas('course', function ($q) {
                $q->where('course_type', 3);
            })
            ->groupBy('user_id', 'course_id')
            ->pluck('id');

        return Enrollment::query()
            ->with(['course', 'user'])
            ->whereIn('id', $latestIds)
            ->orderByDesc('id')
            ->get();
    }

    public function hasPendingCart(int $userId, int $courseId, int $month, int $year): ?CartManagement
    {
        return CartManagement::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('created_by_type', 2)
            ->where('request_enrollment', 1)
            ->where('is_monthly_cron', 1)
            ->whereRaw('CAST(`year` AS UNSIGNED) = ?', [$year])
            ->whereRaw('CAST(`month` AS UNSIGNED) = ?', [$month])
            ->orderByDesc('id')
            ->first();
    }

    public function hasPaidFee(int $userId, int $courseId, int $month, int $year): ?Order
    {
        return Order::query()
            ->select('orders.*')
            ->join('order_items as oi', 'oi.order_id', '=', 'orders.id')
            ->where('oi.user_id', $userId)
            ->where('oi.course_id', $courseId)
            ->whereRaw('LOWER(TRIM(orders.payment_status)) = ?', ['paid'])
            ->where(function ($q) use ($month, $year) {
                $q->where(function ($inner) use ($month, $year) {
                    $inner->whereRaw('CAST(COALESCE(oi.`month`, orders.`month`) AS UNSIGNED) = ?', [$month])
                        ->whereRaw('CAST(COALESCE(oi.`year`, orders.`year`) AS UNSIGNED) = ?', [$year]);
                });
            })
            ->orderByDesc('orders.id')
            ->first();
    }

    public function alreadyInvoicedThisPeriod(Enrollment $enrollment, int $month, int $year): bool
    {
        if (!$enrollment->invoice_generated_at) {
            return false;
        }

        $invoicedAt = Carbon::parse($enrollment->invoice_generated_at);

        return (int) $invoicedAt->format('n') === $month
            && (int) $invoicedAt->format('Y') === $year;
    }

    public function tooSoonSinceLastInvoice(Enrollment $enrollment, Carbon $forDate): bool
    {
        $reference = $enrollment->invoice_generated_at ?? $enrollment->created_at;
        if (!$reference) {
            return false;
        }

        $referenceDate = Carbon::parse($reference)->startOfDay();
        $period = $this->billingPeriod($forDate);

        if ($this->alreadyInvoicedThisPeriod($enrollment, $period['month'], $period['year'])) {
            return true;
        }

        $billingPeriodStart = Carbon::create($period['year'], $period['month'], 1)->startOfDay();
        $daysSince = $referenceDate->diffInDays($billingPeriodStart);

        return $daysSince < self::MIN_DAYS_BETWEEN_INVOICES
            && $referenceDate->format('Y-m') === $billingPeriodStart->format('Y-m');
    }

    public function isEligible(Enrollment $enrollment, ?Carbon $now = null): bool
    {
        $now = $now ?? Carbon::now();

        if ((int) $enrollment->from_license !== 0) {
            return false;
        }

        if ((int) $enrollment->status !== 1) {
            return false;
        }

        if (!$enrollment->end_date || Carbon::parse($enrollment->end_date)->lte($now)) {
            return false;
        }

        if ($enrollment->main_price === null || $enrollment->price === null) {
            return false;
        }

        if ((float) $enrollment->main_price <= 0 || (float) $enrollment->price <= 0) {
            return false;
        }

        $course = $enrollment->relationLoaded('course')
            ? $enrollment->course
            : $enrollment->course()->first();

        return $course && (int) $course->course_type === 3;
    }

    public function calculateNetPrice(Enrollment $enrollment): float
    {
        $originalPrice = (float) ($enrollment->original_price ?? 0);
        $discount = (float) ($enrollment->discount ?? 0);
        $discountType = (int) ($enrollment->discount_type ?? 0);

        if ($discountType === 1) {
            $net = $originalPrice - (($discount / 100) * $originalPrice);
        } else {
            $net = $originalPrice - $discount;
        }

        return max(0, $net);
    }

    public function evaluate(Enrollment $enrollment, ?Carbon $forDate = null): MonthlyFeeEvaluation
    {
        $forDate = $forDate ?? Carbon::now();
        $period = $this->billingPeriod($forDate);
        $month = $period['month'];
        $year = $period['year'];

        $user = $enrollment->relationLoaded('user') ? $enrollment->user : $enrollment->user()->first();
        $course = $enrollment->relationLoaded('course') ? $enrollment->course : $enrollment->course()->first();
        $student = Student::where('user_id', $enrollment->user_id)->first();

        $base = new MonthlyFeeEvaluation(
            MonthlyFeeEvaluation::ACTION_SKIP,
            MonthlyFeeEvaluation::REASON_ENROLLMENT_INELIGIBLE,
            (int) $enrollment->id,
            (int) $enrollment->user_id,
            $user?->name,
            $user?->email,
            (int) $enrollment->course_id,
            $course?->title,
            $month,
            $year,
            null,
            null,
            $this->calculateNetPrice($enrollment)
        );

        if (!$this->isEligible($enrollment, $forDate)) {
            return $base;
        }

        if (!$course || !$student) {
            $base->reason = MonthlyFeeEvaluation::REASON_MISSING_COURSE_OR_STUDENT;
            return $base;
        }

        $amount = $this->calculateNetPrice($enrollment);
        if ($amount <= 0) {
            $base->reason = MonthlyFeeEvaluation::REASON_ZERO_PRICE;
            $base->amount = 0;
            return $base;
        }

        $base->amount = $amount;

        $pendingCart = $this->hasPendingCart($enrollment->user_id, $enrollment->course_id, $month, $year);
        if ($pendingCart !== null) {
            $base->reason = MonthlyFeeEvaluation::REASON_PENDING_CART_EXISTS;
            $base->existingCartId = (int) $pendingCart->id;
            return $base;
        }

        $paidOrder = $this->hasPaidFee($enrollment->user_id, $enrollment->course_id, $month, $year);
        if ($paidOrder !== null) {
            $base->reason = MonthlyFeeEvaluation::REASON_PAID_ORDER_EXISTS;
            $base->existingOrderId = (int) $paidOrder->id;
            return $base;
        }

        if ($this->alreadyInvoicedThisPeriod($enrollment, $month, $year)) {
            $base->reason = MonthlyFeeEvaluation::REASON_ALREADY_INVOICED_THIS_PERIOD;
            return $base;
        }

        if ($this->tooSoonSinceLastInvoice($enrollment, $forDate)) {
            $base->reason = MonthlyFeeEvaluation::REASON_TOO_SOON_SINCE_LAST_INVOICE;
            return $base;
        }

        return new MonthlyFeeEvaluation(
            MonthlyFeeEvaluation::ACTION_CREATE,
            MonthlyFeeEvaluation::REASON_ELIGIBLE,
            (int) $enrollment->id,
            (int) $enrollment->user_id,
            $user?->name,
            $user?->email,
            (int) $enrollment->course_id,
            $course?->title,
            $month,
            $year,
            null,
            null,
            $amount
        );
    }

    public function lockKey(int $userId, int $courseId, int $month, int $year): string
    {
        return sprintf('monthly_fee:%d:%d:%d:%d', $userId, $courseId, $year, $month);
    }

    public function acquireLock(int $userId, int $courseId, int $month, int $year, int $seconds = 120): Lock
    {
        return Cache::lock($this->lockKey($userId, $courseId, $month, $year), $seconds);
    }

    /**
     * Acquire billing lock when cache is available; return null if file/redis cache is broken.
     * Duplicate DB checks still run when lock is unavailable.
     */
    public function tryAcquireLock(int $userId, int $courseId, int $month, int $year, int $seconds = 120): ?Lock
    {
        try {
            return $this->acquireLock($userId, $courseId, $month, $year, $seconds);
        } catch (\Throwable $e) {
            Log::warning('Monthly fee cache lock unavailable; proceeding without lock.', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'month' => $month,
                'year' => $year,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return array<int, MonthlyFeeEvaluation>
     */
    public function previewAll(?int $month = null, ?int $year = null): array
    {
        if ($month !== null && $year !== null) {
            $forDate = Carbon::create($year, $month, 1);
        } else {
            $forDate = Carbon::now();
        }

        $evaluations = [];
        foreach ($this->eligibleEnrollments($forDate) as $enrollment) {
            $evaluations[] = $this->evaluate($enrollment, $forDate);
        }

        return $evaluations;
    }

    /**
     * @param array<int, MonthlyFeeEvaluation> $evaluations
     * @return array<string, int|array<string, int>>
     */
    public function summarizePreview(array $evaluations): array
    {
        $wouldCreate = 0;
        $wouldSkip = 0;
        $riskDuplicates = 0;
        $reasons = [];

        foreach ($evaluations as $evaluation) {
            if ($evaluation->shouldCreate()) {
                $wouldCreate++;
            } else {
                $wouldSkip++;
            }

            if ($evaluation->hasDuplicateRisk()) {
                $riskDuplicates++;
            }

            $reasons[$evaluation->reason] = ($reasons[$evaluation->reason] ?? 0) + 1;
        }

        return [
            'total' => count($evaluations),
            'would_create' => $wouldCreate,
            'would_skip' => $wouldSkip,
            'risk_duplicates' => $riskDuplicates,
            'skip_reasons' => $reasons,
        ];
    }
}
