<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationOrder;
use App\Services\DonationWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationWebhookPreviewController extends Controller
{
    /**
     * Return URL and payload that would be sent for this donation (no webhook is sent).
     * GET /api/donation-webhook-preview?donation_order_id={id}
     */
    public function __invoke(Request $request): JsonResponse
    {
        $donationOrderId = $request->query('donation_order_id');
        if ($donationOrderId === null || $donationOrderId === '') {
            return response()->json([
                'error' => 'donation_order_id is required',
            ], 422);
        }

        $order = DonationOrder::with(['donation_user', 'donation_items'])->find($donationOrderId);
        if (!$order) {
            return response()->json([
                'error' => 'Donation order not found',
            ], 404);
        }

        $webhookService = app(DonationWebhookService::class);
        $preview = $webhookService->getPreview($order);

        return response()->json($preview);
    }
}
