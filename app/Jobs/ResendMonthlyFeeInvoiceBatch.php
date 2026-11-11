<?php

namespace App\Jobs;

use App\Models\CartManagement;
use App\Services\MonthlyFeeInvoiceEmailService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResendMonthlyFeeInvoiceBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;
    public $tries = 3;

    /** @var array<int, int> */
    protected $cartIds;

    /** @var array */
    protected $recipientOptions;

    /** @var int|null */
    protected $adminId;

    /**
     * @param array<int, int> $cartIds
     * @param array $recipientOptions
     */
    public function __construct(array $cartIds, array $recipientOptions = [], ?int $adminId = null)
    {
        $this->cartIds = $cartIds;
        $this->recipientOptions = $recipientOptions;
        $this->adminId = $adminId;
    }

    public function handle(MonthlyFeeInvoiceEmailService $emailService): void
    {
        Log::info('[MonthlyFeeResend] Batch started for ' . count($this->cartIds) . ' cart(s) at ' . now());

        foreach ($this->cartIds as $cartId) {
            try {
                $cart = CartManagement::find($cartId);
                if (!$cart) {
                    Log::warning("[MonthlyFeeResend] Cart #{$cartId} not found. Skipping.");
                    continue;
                }

                if ((float) ($cart->main_price ?? 0) <= 0 && empty($this->recipientOptions['include_zero_price'])) {
                    Log::info("[MonthlyFeeResend] Cart #{$cartId} skipped (zero price).");
                    continue;
                }

                $ok = $emailService->send($cart, $this->recipientOptions, 'resend', $this->adminId);
                Log::info(sprintf(
                    '[MonthlyFeeResend] Cart #%d %s',
                    $cartId,
                    $ok ? 'sent' : 'failed'
                ));

                // Pace SMTP similar to reminder jobs (shorter pause inside batch)
                sleep(5);
            } catch (Exception $e) {
                Log::error("[MonthlyFeeResend] Cart #{$cartId} exception: " . $e->getMessage());
            }
        }

        Log::info('[MonthlyFeeResend] Batch completed at ' . now());
    }

    public function failed(Exception $exception): void
    {
        Log::error('[MonthlyFeeResend] Batch failed: ' . $exception->getMessage());
    }
}
