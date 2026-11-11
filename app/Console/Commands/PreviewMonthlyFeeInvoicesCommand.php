<?php

namespace App\Console\Commands;

use App\Services\MonthlyFeeInvoiceEmailService;
use Illuminate\Console\Command;

class PreviewMonthlyFeeInvoicesCommand extends Command
{
    protected $signature = 'monthly-fee-invoices:preview
                            {--month= : Calendar month (1-12), defaults to current}
                            {--year= : Calendar year, defaults to current}
                            {--course= : Optional course_id filter}
                            {--unpaid : Only unpaid (request_enrollment=1) carts}';

    protected $description = 'Preview monthly fee invoice email resend targets (read-only, no emails sent)';

    public function handle(MonthlyFeeInvoiceEmailService $emailService): int
    {
        $month = (int) ($this->option('month') ?: date('n'));
        $year = (int) ($this->option('year') ?: date('Y'));

        $filters = [
            'month' => $month,
            'year' => $year,
            'course_id' => $this->option('course') ? (int) $this->option('course') : null,
            'payment_status' => $this->option('unpaid') ? 'unpaid' : null,
        ];

        $preview = $emailService->preview($filters, [
            'include_student' => true,
            'include_cc' => true,
            'include_bcc' => true,
        ]);

        $this->info(sprintf(
            'Monthly fee invoice email preview for %04d-%02d: %d cart(s), ~%d batch(es)',
            $year,
            $month,
            $preview['count'],
            $preview['batch_estimate']
        ));

        $this->line('CC: ' . (implode(', ', $preview['recipients_summary']['cc']) ?: '(none)'));
        $this->line('BCC: ' . (implode(', ', $preview['recipients_summary']['bcc']) ?: '(none)'));

        if ($preview['count'] === 0) {
            return 0;
        }

        $this->table(
            ['Cart', 'Student', 'Email', 'Course', 'Amount', 'PDF', 'Paid'],
            collect($preview['rows'])->map(function ($row) {
                return [
                    $row['cart_id'],
                    $row['student_name'],
                    $row['student_email'],
                    $row['course_title'],
                    $row['amount'],
                    $row['pdf_exists'] ? 'yes' : 'no',
                    $row['paid'] ? 'yes' : 'no',
                ];
            })->all()
        );

        return 0;
    }
}
