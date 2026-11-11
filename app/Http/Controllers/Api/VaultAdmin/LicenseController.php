<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Services\VaultBillingAdminService;
use App\Services\VaultLicenseAdminService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(
        protected VaultLicenseAdminService $service,
        protected VaultBillingAdminService $billingService
    ) {}

    public function index(Request $request)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $paginator = $this->service->paginateLicenses(
            $request->only('search', 'status'),
            (int) $request->get('per_page', 15)
        );

        $paginator->getCollection()->transform(fn (License $l) => $this->service->serializeLicenseSummary($l));

        return response()->json(['success' => true, 'data' => $paginator]);
    }

    public function store(Request $request)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $validated = $this->validateLicensePayload($request);

        try {
            $license = $this->service->createLicense($validated);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeLicenseDetail($license),
        ], 201);
    }

    public function show(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $license = License::with(['vaultUser', 'courses', 'licenseDevices'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeLicenseDetail($license),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $license = License::findOrFail($id);
        $validated = $this->validateLicensePayload($request, $license->id);

        try {
            $license = $this->service->updateLicense($license, $validated);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeLicenseDetail($license),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'delete_license');

        $license = License::findOrFail($id);
        $this->service->deleteLicense($license);

        return response()->json(['success' => true]);
    }

    public function usage(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $license = License::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->service->licenseUsage($license, (int) $request->get('per_page', 40)),
        ]);
    }

    public function billing(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $license = License::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->billingService->licenseBilling($license),
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $license = License::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,revoked',
        ]);

        try {
            $license = $this->service->updateLicenseStatus($license, $validated['status']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeLicenseDetail($license),
        ]);
    }

    protected function validateLicensePayload(Request $request, ?int $licenseId = null): array
    {
        $uniqueRule = 'unique:licenses,key';
        if ($licenseId) {
            $uniqueRule .= ','.$licenseId;
        }

        return $request->validate([
            'vault_user_id' => 'required|exists:vault_users,id',
            'license_key' => 'required|string|'.$uniqueRule,
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:active,inactive,revoked',
            'notes' => 'nullable|string',
            'courses' => 'required|array|min:1',
            'courses.*' => 'integer|exists:courses,id',
            'devices' => 'nullable|array',
            'devices.*.name' => 'nullable|string',
            'devices.*.k_sign' => 'nullable|string',
        ]);
    }
}
