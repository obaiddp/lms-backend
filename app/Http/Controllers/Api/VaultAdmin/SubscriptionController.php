<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Models\VaultSubscription;
use App\Services\VaultBillingAdminService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(protected VaultBillingAdminService $service) {}

    public function index(Request $request)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $paginator = $this->service->paginateSubscriptions(
            $request->only('search', 'status', 'vault_user_id', 'license_id'),
            (int) $request->get('per_page', 15)
        );

        $paginator->getCollection()->transform(fn (VaultSubscription $s) => $this->service->serializeSubscription($s));

        return response()->json(['success' => true, 'data' => $paginator]);
    }

    public function store(Request $request)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $validated = $this->validatePayload($request);

        try {
            $subscription = $this->service->createSubscription($validated, $request->user()?->id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeSubscription($subscription),
        ], 201);
    }

    public function show(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $subscription = VaultSubscription::with(['vaultUser', 'license'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeSubscription($subscription),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $subscription = VaultSubscription::findOrFail($id);
        $validated = $this->validatePayload($request, $subscription->id);

        try {
            $subscription = $this->service->updateSubscription($subscription, $validated);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeSubscription($subscription),
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $subscription = VaultSubscription::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:active,cancelled,expired,paused',
        ]);

        try {
            $subscription = $this->service->updateSubscriptionStatus($subscription, $validated['status']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeSubscription($subscription),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'delete_license');

        $subscription = VaultSubscription::findOrFail($id);

        try {
            $this->service->deleteSubscription($subscription);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    protected function validatePayload(Request $request, ?int $subscriptionId = null): array
    {
        return $request->validate([
            'vault_user_id' => 'required|exists:vault_users,id',
            'license_id' => 'required|exists:licenses,id',
            'plan_name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:8',
            'billing_interval' => 'nullable|in:monthly,quarterly,yearly,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,cancelled,expired,paused',
            'notes' => 'nullable|string',
        ]);
    }
}
