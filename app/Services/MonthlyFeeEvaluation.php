<?php

namespace App\Services;

class MonthlyFeeEvaluation
{
    public const ACTION_CREATE = 'create';
    public const ACTION_SKIP = 'skip';

    public const REASON_ELIGIBLE = 'eligible';
    public const REASON_ENROLLMENT_INELIGIBLE = 'enrollment_ineligible';
    public const REASON_ZERO_PRICE = 'zero_price';
    public const REASON_MISSING_COURSE_OR_STUDENT = 'missing_course_or_student';
    public const REASON_PENDING_CART_EXISTS = 'pending_cart_exists';
    public const REASON_PAID_ORDER_EXISTS = 'paid_order_exists';
    public const REASON_ALREADY_INVOICED_THIS_PERIOD = 'already_invoiced_this_month';
    public const REASON_TOO_SOON_SINCE_LAST_INVOICE = 'too_soon_since_last_invoice';

    public string $action;
    public string $reason;
    public int $enrollmentId;
    public int $userId;
    public ?string $studentName;
    public ?string $studentEmail;
    public int $courseId;
    public ?string $courseTitle;
    public int $billingMonth;
    public int $billingYear;
    public ?int $existingCartId;
    public ?int $existingOrderId;
    public float $amount;

    public function __construct(
        string $action,
        string $reason,
        int $enrollmentId,
        int $userId,
        ?string $studentName,
        ?string $studentEmail,
        int $courseId,
        ?string $courseTitle,
        int $billingMonth,
        int $billingYear,
        ?int $existingCartId = null,
        ?int $existingOrderId = null,
        float $amount = 0.0
    ) {
        $this->action = $action;
        $this->reason = $reason;
        $this->enrollmentId = $enrollmentId;
        $this->userId = $userId;
        $this->studentName = $studentName;
        $this->studentEmail = $studentEmail;
        $this->courseId = $courseId;
        $this->courseTitle = $courseTitle;
        $this->billingMonth = $billingMonth;
        $this->billingYear = $billingYear;
        $this->existingCartId = $existingCartId;
        $this->existingOrderId = $existingOrderId;
        $this->amount = $amount;
    }

    public function shouldCreate(): bool
    {
        return $this->action === self::ACTION_CREATE;
    }

    public function hasDuplicateRisk(): bool
    {
        return $this->shouldCreate()
            && ($this->existingCartId !== null || $this->existingOrderId !== null);
    }

    public function billingPeriodLabel(): string
    {
        return sprintf('%04d-%02d', $this->billingYear, $this->billingMonth);
    }

    public function toArray(): array
    {
        return [
            'enrollment_id' => $this->enrollmentId,
            'user_id' => $this->userId,
            'student_name' => $this->studentName,
            'student_email' => $this->studentEmail,
            'course_id' => $this->courseId,
            'course_title' => $this->courseTitle,
            'billing_period' => $this->billingPeriodLabel(),
            'billing_month' => $this->billingMonth,
            'billing_year' => $this->billingYear,
            'action' => strtoupper($this->action),
            'reason' => $this->reason,
            'existing_cart_id' => $this->existingCartId,
            'existing_order_id' => $this->existingOrderId,
            'amount' => $this->amount,
            'duplicate_risk' => $this->hasDuplicateRisk(),
        ];
    }
}
