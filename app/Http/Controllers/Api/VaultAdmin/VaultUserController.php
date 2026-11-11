<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Models\VaultUser;
use App\Services\VaultBillingAdminService;
use App\Services\VaultLicenseAdminService;
use Illuminate\Http\Request;

class VaultUserController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(
        protected VaultLicenseAdminService $service,
        protected VaultBillingAdminService $billingService
    ) {}

    public function index(Request $request)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        return response()->json([
            'success' => true,
            'data' => $this->service->paginateVaultUsers($request->only('search'), (int) $request->get('per_page', 15)),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:vault_users,email',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $user = $this->service->createVaultUser($validated);

        return response()->json(['success' => true, 'data' => $user], 201);
    }

    public function show(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        return response()->json([
            'success' => true,
            'data' => VaultUser::withCount('licenses')->findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $vaultUser = VaultUser::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:vault_users,email,'.$vaultUser->id,
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->service->updateVaultUser($vaultUser, $validated),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'delete_license');

        $vaultUser = VaultUser::findOrFail($id);
        if ($vaultUser->licenses()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete vault user with active licenses.',
            ], 422);
        }
        $vaultUser->delete();

        return response()->json(['success' => true]);
    }

    public function billing(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $vaultUser = VaultUser::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->billingService->vaultUserBilling($vaultUser),
        ]);
    }
}
