<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationOrder;
use App\Models\DonationRecurringSchedule;
use App\Services\DonationWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DonationWebhookResendController extends Controller
{
    /**
     * Re-send donation webhooks for a given order using ERP-aligned logic.
     * POST /api/donation-resend-webhook
     * Body: donation_order_id (int)
     */
    public function __invoke(Request $request): JsonResponse
    {
        $donationOrderId = $request->input('donation_order_id') ?? $request->query('donation_order_id');
        if ($donationOrderId === null || $donationOrderId === '') {
            return response()->json([
                'success' => false,
                'message' => 'donation_order_id is required',
            ], 422);
        }

        $order = DonationOrder::with(['donation_user', 'donation_items'])->find($donationOrderId);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Donation order not found',
            ], 404);
        }

        try {
            $webhookService = app(DonationWebhookService::class);
            $hasRecurring = DonationRecurringSchedule::where('order_id', $order->id)->exists();
            if (!$hasRecurring) {
                $hasRecurring = $order->donation_items->contains(function ($item) {
                    return in_array(strtolower($item->cycle ?? ''), ['monthly', 'annually', 'daily', 'quarterly']);
                });
            }
            $hasOneTime = $order->donation_items->contains(function ($item) {
                return !in_array(strtolower($item->cycle ?? ''), ['monthly', 'annually', 'daily', 'quarterly']);
            });

            $sentEvents = [];

            if ($hasRecurring) {
                $regularImportId = 'RG' . $order->id;
                $webhookService->sendRegularGivingCreated($order, [
                    'import_id' => $regularImportId,
                    'provider_recurrence_id' => $order->meezan_recurrence_id ?: null,
                ]);
                $sentEvents[] = 'regular_giving.created';

                $webhookService->sendDonationCreatedRegular($order, [
                    'import_id' => 'DN' . $order->id,
                    'regular_giving_import_id' => $regularImportId,
                    'provider_recurrence_id' => $order->meezan_recurrence_id ?: null,
                    'payment_status' => 'Paid',
                ]);
                $sentEvents[] = 'donation.created:regularDonation';
            }

            if ($hasOneTime || !$hasRecurring) {
                $webhookService->sendDonationCreatedSingle($order, [
                    'import_id' => 'DN' . $order->id . ($hasRecurring && $hasOneTime ? '-OT' : ''),
                    'payment_status' => 'Paid',
                ]);
                $sentEvents[] = 'donation.created:singleDonation';
            }

            $payload = [
                'success' => true,
                'message' => 'Webhook(s) sent successfully',
                'events' => $sentEvents,
            ];
            return response()->json($payload);
        } catch (\Throwable $e) {
            Log::warning('[DonationWebhookResend] Webhook send failed', [
                'donation_order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to send webhook: ' . $e->getMessage(),
            ], 500);
        }
    }
}
