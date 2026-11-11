<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationReceiptEmailLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationReceiptEmailLogController extends Controller
{
    /**
     * Return receipt email logs for a donation order (for headless donation detail page).
     * GET /api/donation-receipt-email-logs?donation_order_id={id}
     */
    public function index(Request $request): JsonResponse
    {
        $donationOrderId = $request->query('donation_order_id');
        if ($donationOrderId === null || $donationOrderId === '') {
            return response()->json(['data' => []]);
        }

        $id = (int) $donationOrderId;
        if ($id === 0) {
            return response()->json(['data' => []]);
        }

        $logs = DonationReceiptEmailLog::where('donation_order_id', $id)
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'donation_order_id',
                'recipient_email',
                'status',
                'sent_at',
                'error_message',
                'trigger',
                'created_at',
            ]);

        return response()->json(['data' => $logs]);
    }
}
