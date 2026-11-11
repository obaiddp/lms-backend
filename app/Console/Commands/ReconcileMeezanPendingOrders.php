<?php

namespace App\Console\Commands;

use App\Models\DonationOrder;
use App\Models\Order;
use App\Services\MeezanPaymentCompletionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcileMeezanPendingOrders extends Command
{
    protected $signature = 'meezan:reconcile-pending-orders
                            {--hours=48 : Only consider orders created within this many hours}
                            {--limit=50 : Max LMS and donation orders to check per run}';

    protected $description = 'Poll Meezan getOrderStatusExtended for unpaid LMS/donation orders and complete paid ones';

    public function handle(MeezanPaymentCompletionService $service): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $limit = max(1, (int) $this->option('limit'));
        $since = Carbon::now()->subHours($hours);

        $lmsCounts = [
            MeezanPaymentCompletionService::RESULT_COMPLETED => 0,
            MeezanPaymentCompletionService::RESULT_ALREADY_PAID => 0,
            MeezanPaymentCompletionService::RESULT_PENDING => 0,
            MeezanPaymentCompletionService::RESULT_DECLINED => 0,
            MeezanPaymentCompletionService::RESULT_ERROR => 0,
        ];
        $donationCounts = $lmsCounts;

        $orders = Order::query()
            ->where('payment_method', MEEZAN_BANK)
            ->whereNotIn('payment_status', ['paid', 'free'])
            ->whereNotNull('meezan_transaction_id')
            ->where('meezan_transaction_id', '!=', '')
            ->where('created_at', '>=', $since)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        foreach ($orders as $order) {
            try {
                $outcome = $service->reconcileOrder($order);
                $key = $outcome['result'] ?? MeezanPaymentCompletionService::RESULT_ERROR;
                if (!isset($lmsCounts[$key])) {
                    $key = MeezanPaymentCompletionService::RESULT_ERROR;
                }
                $lmsCounts[$key]++;
                if (in_array($key, [
                    MeezanPaymentCompletionService::RESULT_COMPLETED,
                    MeezanPaymentCompletionService::RESULT_ERROR,
                    MeezanPaymentCompletionService::RESULT_DECLINED,
                ], true)) {
                    Log::info('[meezan:reconcile-pending-orders] LMS', [
                        'order_id' => $order->id,
                        'result' => $outcome['result'],
                        'message' => $outcome['message'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                $lmsCounts[MeezanPaymentCompletionService::RESULT_ERROR]++;
                Log::error('[meezan:reconcile-pending-orders] LMS exception', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $donationOrders = DonationOrder::query()
            ->where('payment_method', MEEZAN_BANK)
            ->whereNotIn('payment_status', ['paid', 'free'])
            ->whereNotNull('meezan_transaction_id')
            ->where('meezan_transaction_id', '!=', '')
            ->where('created_at', '>=', $since)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        foreach ($donationOrders as $donationOrder) {
            try {
                $outcome = $service->reconcileDonationOrder($donationOrder);
                $key = $outcome['result'] ?? MeezanPaymentCompletionService::RESULT_ERROR;
                if (!isset($donationCounts[$key])) {
                    $key = MeezanPaymentCompletionService::RESULT_ERROR;
                }
                $donationCounts[$key]++;
                if (in_array($key, [
                    MeezanPaymentCompletionService::RESULT_COMPLETED,
                    MeezanPaymentCompletionService::RESULT_ERROR,
                    MeezanPaymentCompletionService::RESULT_DECLINED,
                ], true)) {
                    Log::info('[meezan:reconcile-pending-orders] Donation', [
                        'order_id' => $donationOrder->id,
                        'result' => $outcome['result'],
                        'message' => $outcome['message'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                $donationCounts[MeezanPaymentCompletionService::RESULT_ERROR]++;
                Log::error('[meezan:reconcile-pending-orders] Donation exception', [
                    'order_id' => $donationOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info(sprintf(
            'LMS checked=%d completed=%d already_paid=%d pending=%d declined=%d error=%d',
            $orders->count(),
            $lmsCounts[MeezanPaymentCompletionService::RESULT_COMPLETED],
            $lmsCounts[MeezanPaymentCompletionService::RESULT_ALREADY_PAID],
            $lmsCounts[MeezanPaymentCompletionService::RESULT_PENDING],
            $lmsCounts[MeezanPaymentCompletionService::RESULT_DECLINED],
            $lmsCounts[MeezanPaymentCompletionService::RESULT_ERROR]
        ));
        $this->info(sprintf(
            'Donations checked=%d completed=%d already_paid=%d pending=%d declined=%d error=%d',
            $donationOrders->count(),
            $donationCounts[MeezanPaymentCompletionService::RESULT_COMPLETED],
            $donationCounts[MeezanPaymentCompletionService::RESULT_ALREADY_PAID],
            $donationCounts[MeezanPaymentCompletionService::RESULT_PENDING],
            $donationCounts[MeezanPaymentCompletionService::RESULT_DECLINED],
            $donationCounts[MeezanPaymentCompletionService::RESULT_ERROR]
        ));

        return self::SUCCESS;
    }
}
