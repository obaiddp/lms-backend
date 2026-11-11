<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\PaymentReceiptEmailService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResendPaymentReceiptBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;
    public $tries = 3;

    /** @var array<int, int> */
    protected $orderIds;

    /** @var array */
    protected $recipientOptions;

    /** @var int|null */
    protected $adminId;

    /**
     * @param array<int, int> $orderIds
     * @param array $recipientOptions
     */
    public function __construct(array $orderIds, array $recipientOptions = [], ?int $adminId = null)
    {
        $this->orderIds = $orderIds;
        $this->recipientOptions = $recipientOptions;
        $this->adminId = $adminId;
    }

    public function handle(PaymentReceiptEmailService $emailService): void
    {
        Log::info('[PaymentReceiptResend] Batch started for ' . count($this->orderIds) . ' order(s) at ' . now());

        foreach ($this->orderIds as $orderId) {
            try {
                $order = Order::with(['user', 'items.course', 'items.product', 'items.cart'])->find($orderId);
                if (!$order) {
                    Log::warning("[PaymentReceiptResend] Order #{$orderId} not found. Skipping.");
                    continue;
                }

                $ok = $emailService->send($order, $this->recipientOptions, 'resend', $this->adminId);
                Log::info(sprintf(
                    '[PaymentReceiptResend] Order #%d %s',
                    $orderId,
                    $ok ? 'sent' : 'failed'
                ));

                sleep(5);
            } catch (Exception $e) {
                Log::error("[PaymentReceiptResend] Order #{$orderId} exception: " . $e->getMessage());
            }
        }

        Log::info('[PaymentReceiptResend] Batch completed at ' . now());
    }

    public function failed(Exception $exception): void
    {
        Log::error('[PaymentReceiptResend] Batch failed: ' . $exception->getMessage());
    }
}
