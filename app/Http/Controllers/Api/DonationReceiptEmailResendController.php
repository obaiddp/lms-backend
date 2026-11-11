<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendDonationReceiptEmailJob;
use App\Models\DonationOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationReceiptEmailResendController extends Controller
{
    /**
     * Queue receipt email for resend. Returns 202 Accepted.
     * POST /api/donation-resend-receipt-email
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

        $id = (int) $donationOrderId;
        $order = DonationOrder::with('donation_user')->find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Donation order not found',
            ], 404);
        }

        $user = $order->donation_user;
        if (!$user || empty(trim($user->email ?? ''))) {
            return response()->json([
                'success' => false,
                'message' => 'Donation order has no donor email.',
            ], 422);
        }

        SendDonationReceiptEmailJob::dispatch($id, 'resend');

        return response()->json([
            'success' => true,
            'message' => 'Receipt email queued for sending.',
        ], 202);
    }
}
