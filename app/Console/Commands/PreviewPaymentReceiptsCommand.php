<?php

namespace App\Console\Commands;

use App\Services\PaymentReceiptEmailService;
use Illuminate\Console\Command;

class PreviewPaymentReceiptsCommand extends Command
{
    protected $signature = 'payment-receipts:preview
                            {--month= : Calendar month (1-12), defaults to current}
                            {--year= : Calendar year, defaults to current}
                            {--course= : Optional course_id filter}
                            {--method= : Optional payment_method filter}';

    protected $description = 'Preview payment receipt email resend targets (read-only, no emails sent)';

    public function handle(PaymentReceiptEmailService $emailService): int
    {
        $month = (int) ($this->option('month') ?: date('n'));
        $year = (int) ($this->option('year') ?: date('Y'));

        $filters = [
            'month' => $month,
            'year' => $year,
            'course_id' => $this->option('course') ? (int) $this->option('course') : null,
            'payment_method' => $this->option('method') ?: null,
        ];

        $preview = $emailService->preview($filters, [
            'include_student' => true,
            'include_cc' => true,
            'include_bcc' => true,
        ]);

        $this->info(sprintf(
            'Payment receipt email preview for %04d-%02d: %d order(s), ~%d batch(es)',
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
            ['Order', 'Student', 'Email', 'Course', 'Amount', 'Method'],
            collect($preview['rows'])->map(function ($row) {
                return [
                    $row['order_id'],
                    $row['student_name'],
                    $row['student_email'],
                    $row['course_title'],
                    $row['amount'],
                    $row['payment_method'],
                ];
            })->all()
        );

        return 0;
    }
}
