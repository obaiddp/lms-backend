<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VaultBillingAdminService;
use App\Services\VaultOfflinePackService;
use App\Services\VaultOnlineCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VaultPublicPaymentController extends Controller
{
    public function __construct(
        protected VaultBillingAdminService $billing,
        protected VaultOnlineCheckoutService $checkout,
        protected VaultOfflinePackService $packs
    ) {}

    public function show(string $token)
    {
        $link = $this->billing->findOpenLinkByToken($token);
        if (! $link) {
            return response()->json(['success' => false, 'message' => 'Payment link not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->billing->serializePublicLinkSummary($link),
        ]);
    }

    public function status(string $token)
    {
        return $this->show($token);
    }

    public function checkout(Request $request, string $token)
    {
        $link = $this->billing->findOpenLinkByToken($token);
        if (! $link) {
            return response()->json(['success' => false, 'message' => 'Payment link not found.'], 404);
        }

        if (! $link->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'This payment link is no longer open.',
                'data' => $this->billing->serializePublicLinkSummary($link),
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:faysal_bank,meezan_bank',
            'dashboard_origin' => 'nullable|url',
        ]);

        $origin = $validated['dashboard_origin']
            ?? rtrim((string) env('CEF_DASHBOARD_URL', ''), '/')
            ?: $request->headers->get('Origin')
            ?: rtrim((string) env('APP_URL', ''), '/');

        try {
            $result = $this->checkout->startCheckout($link->fresh(['payment', 'vaultUser']), $validated['payment_method'], $origin);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Vault public checkout failed', ['error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function meezanComplete(string $token)
    {
        $link = $this->billing->findOpenLinkByToken($token);
        if (! $link) {
            return response()->json(['success' => false, 'message' => 'Payment link not found.'], 404);
        }

        $result = $this->checkout->completeMeezan($link);

        $status = match ($result['result']) {
            'completed', 'already_paid' => 200,
            'pending' => 202,
            'declined' => 402,
            default => 422,
        };

        return response()->json([
            'success' => in_array($result['result'], ['completed', 'already_paid'], true),
            'result' => $result['result'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $status);
    }

    public function download(string $downloadToken): BinaryFileResponse|\Illuminate\Http\JsonResponse
    {
        $link = $this->billing->findLinkByDownloadToken($downloadToken);
        if (! $link || $link->status !== 'paid') {
            return response()->json(['success' => false, 'message' => 'Download not found.'], 404);
        }

        if ($link->download_expires_at && $link->download_expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'Download link has expired.'], 410);
        }

        $pack = $link->offlinePack;
        if (! $pack || $pack->status !== 'ready') {
            return response()->json([
                'success' => false,
                'message' => 'Offline pack is not ready yet.',
                'pack_status' => $pack?->status,
            ], 409);
        }

        $path = $this->packs->absoluteStoragePath($pack);
        if (! $path) {
            return response()->json(['success' => false, 'message' => 'Pack file missing.'], 404);
        }

        return response()->download($path, 'cef-vault-pack-'.$pack->pack_id.'.zip');
    }

    /**
     * Faysal / PayFast IPN for vault payment links.
     */
    public function faysalIpn(Request $request, int $link_id)
    {
        $link = $this->billing->findLinkById($link_id);
        if (! $link) {
            Log::warning('Vault Faysal IPN: unknown link', ['link_id' => $link_id]);

            return response('INVALID', 404);
        }

        try {
            $result = $this->checkout->handleFaysalIpn($link, $request->all());
        } catch (\Throwable $e) {
            Log::error('Vault Faysal IPN error', ['link_id' => $link_id, 'error' => $e->getMessage()]);

            return response('ERROR', 500);
        }

        return response($result['message'] === 'OK' || ($result['result'] ?? '') === 'already_paid' ? 'OK' : 'FAIL');
    }
}
