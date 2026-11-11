<?php

namespace App\Console\Commands;

use App\Services\MonthlyFeeBillingService;
use Illuminate\Console\Command;

class PreviewMonthlyFeesCommand extends Command
{
    protected $signature = 'monthly-fees:preview
                            {--month= : Billing month (1-12), defaults to current month}
                            {--year= : Billing year, defaults to current year}
                            {--json : Output JSON instead of a table}';

    protected $description = 'Preview monthly fee challan generation (read-only, no carts created)';

    public function handle(MonthlyFeeBillingService $billingService): int
    {
        $month = $this->option('month') !== null ? (int) $this->option('month') : null;
        $year = $this->option('year') !== null ? (int) $this->option('year') : null;

        if ($month !== null && ($month < 1 || $month > 12)) {
            $this->error('Month must be between 1 and 12.');
            return 1;
        }

        $evaluations = $billingService->previewAll($month, $year);
        $summary = $billingService->summarizePreview($evaluations);

        $period = $billingService->billingPeriod(
            $month !== null && $year !== null
                ? \Carbon\Carbon::create($year, $month, 1)
                : null
        );

        if ($this->option('json')) {
            $this->line(json_encode([
                'billing_period' => sprintf('%04d-%02d', $period['year'], $period['month']),
                'summary' => $summary,
                'rows' => array_map(fn ($e) => $e->toArray(), $evaluations),
            ], JSON_PRETTY_PRINT));
            return $summary['risk_duplicates'] > 0 ? 1 : 0;
        }

        $this->info(sprintf(
            'Monthly fee preview for billing period %04d-%02d (read-only)',
            $period['year'],
            $period['month']
        ));

        $this->table(
            [
                'enrollment_id',
                'student',
                'email',
                'course',
                'action',
                'reason',
                'amount',
                'cart_id',
                'order_id',
            ],
            array_map(function ($evaluation) {
                return [
                    $evaluation->enrollmentId,
                    $evaluation->studentName,
                    $evaluation->studentEmail,
                    $evaluation->courseTitle,
                    strtoupper($evaluation->action),
                    $evaluation->reason,
                    $evaluation->amount,
                    $evaluation->existingCartId ?? '-',
                    $evaluation->existingOrderId ?? '-',
                ];
            }, $evaluations)
        );

        $this->newLine();
        $this->info('Summary');
        $this->line('  Total evaluated: ' . $summary['total']);
        $this->line('  Would CREATE:    ' . $summary['would_create']);
        $this->line('  Would SKIP:      ' . $summary['would_skip']);
        $this->line('  Duplicate risk:  ' . $summary['risk_duplicates']);

        if (!empty($summary['skip_reasons'])) {
            $this->newLine();
            $this->info('Skip reasons');
            foreach ($summary['skip_reasons'] as $reason => $count) {
                $this->line("  {$reason}: {$count}");
            }
        }

        if ($summary['risk_duplicates'] > 0) {
            $this->error('Duplicate risk detected — review rows marked CREATE that still have existing cart/order.');
            return 1;
        }

        return 0;
    }
}
