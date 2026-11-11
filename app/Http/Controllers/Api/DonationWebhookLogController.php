<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DonationWebhookLogController extends Controller
{
    /**
     * Return webhook logs for a donation order (for headless donation detail page).
     * GET /api/donation-webhook-logs?donation_order_id=4
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

        $logs = DonationWebhookLog::where('donation_order_id', $id)
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'donation_order_id',
                'event',
                'url',
                'payload',
                'request_headers',
                'response_status',
                'response_body',
                'response_headers',
                'duration_ms',
                'error_message',
                'created_at',
            ]);

        return response()->json(['data' => $logs]);
    }
}
