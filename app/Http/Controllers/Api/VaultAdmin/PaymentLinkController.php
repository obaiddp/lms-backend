<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\VaultPaymentLink;
use App\Services\VaultBillingAdminService;
use Illuminate\Http\Request;

class PaymentLinkController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(protected VaultBillingAdminService $billing) {}

    public function index(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $license = License::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->billing->listPaymentLinks($license),
        ]);
    }

    public function store(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $license = License::with(['licenseDevices', 'vaultUser'])->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:8',
            'vault_subscription_id' => 'nullable|exists:vault_subscriptions,id',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'notes' => 'nullable|string',
            'include_offline_pack' => 'nullable|boolean',
            'expires_days' => 'nullable|integer|min:1|max:90',
        ]);

        try {
            $link = $this->billing->createPaymentLink($license, $validated, $request->user()?->id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->billing->serializePaymentLink($link),
        ], 201);
    }

    public function revoke(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $link = VaultPaymentLink::findOrFail($id);

        try {
            $link = $this->billing->revokePaymentLink($link);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->billing->serializePaymentLink($link),
        ]);
    }
}
