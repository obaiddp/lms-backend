<?php

namespace App\Jobs;

use App\Models\DonationOrder;
use App\Models\DonationReceiptEmailLog;
use App\Services\DonationReceiptMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDonationReceiptEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $donationOrderId;
    public string $trigger;

    /**
     * @param int $donationOrderId
     * @param string $trigger 'automatic' or 'resend'
     */
    public function __construct(int $donationOrderId, string $trigger = 'automatic')
    {
        $this->donationOrderId = $donationOrderId;
        $this->trigger = $trigger;
    }

    public function handle(DonationReceiptMailService $mailService): void
    {
        $order = DonationOrder::with(['donation_user', 'donation_items'])->find($this->donationOrderId);
        if (!$order) {
            Log::warning("SendDonationReceiptEmailJob: DonationOrder {$this->donationOrderId} not found.");
            return;
        }

        $user = $order->donation_user;
        if (!$user || empty(trim($user->email ?? ''))) {
            DonationReceiptEmailLog::create([
                'donation_order_id' => $this->donationOrderId,
                'recipient_email' => '',
                'status' => 'failed',
                'sent_at' => null,
                'error_message' => 'Donation order has no donor email.',
                'trigger' => $this->trigger,
            ]);
            return;
        }

        $recipientEmail = $user->email;

        try {
            $mailService->sendReceipt($order);
            DonationReceiptEmailLog::create([
                'donation_order_id' => $this->donationOrderId,
                'recipient_email' => $recipientEmail,
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
                'trigger' => $this->trigger,
            ]);
        } catch (\Throwable $e) {
            Log::error("SendDonationReceiptEmailJob failed for order {$this->donationOrderId}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            DonationReceiptEmailLog::create([
                'donation_order_id' => $this->donationOrderId,
                'recipient_email' => $recipientEmail,
                'status' => 'failed',
                'sent_at' => null,
                'error_message' => $e->getMessage(),
                'trigger' => $this->trigger,
            ]);
        }
    }
}
