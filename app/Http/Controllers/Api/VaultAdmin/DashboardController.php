<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Services\VaultBillingAdminService;
use App\Services\VaultLicenseAdminService;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
            'data' => array_merge(
                $this->service->dashboardStats(),
                $this->billingService->billingDashboardStats()
            ),
        ]);
    }
}
