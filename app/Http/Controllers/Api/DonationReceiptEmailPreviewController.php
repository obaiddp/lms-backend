<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationOrder;
use App\Services\DonationReceiptMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationReceiptEmailPreviewController extends Controller
{
    /**
     * Return rendered receipt email HTML (no send). For headless donation detail preview.
     * GET /api/donation-receipt-email-preview?donation_order_id={id}
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

        $mailService = app(DonationReceiptMailService::class);
        $html = $mailService->renderPreviewHtml($order);

        return response()->json(['html' => $html]);
    }
}
