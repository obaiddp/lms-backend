<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Models\VaultPayment;
use App\Services\VaultBillingAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(protected VaultBillingAdminService $service) {}

    public function index(Request $request)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $paginator = $this->service->paginatePayments(
            $request->only('search', 'status', 'vault_user_id', 'license_id', 'vault_subscription_id', 'paid_from', 'paid_to'),
            (int) $request->get('per_page', 15)
        );

        $paginator->getCollection()->transform(fn (VaultPayment $p) => $this->service->serializePayment($p));

        return response()->json(['success' => true, 'data' => $paginator]);
    }

    public function store(Request $request)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $validated = $this->validatePayload($request);

        try {
            $payment = $this->service->recordPayment(
                $validated,
                $request->user()?->id,
                $request->file('receipt')
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializePayment($payment),
        ], 201);
    }

    public function show(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $payment = VaultPayment::with(['vaultUser', 'subscription', 'license', 'recorder'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->service->serializePayment($payment),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $payment = VaultPayment::findOrFail($id);
        $validated = $this->validatePayload($request, true);

        try {
            $payment = $this->service->updatePayment($payment, $validated, $request->file('receipt'));
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializePayment($payment),
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $payment = VaultPayment::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,void',
        ]);

        try {
            $payment = $this->service->updatePaymentStatus($payment, $validated['status']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializePayment($payment),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'delete_license');

        $payment = VaultPayment::findOrFail($id);
        $this->service->deletePayment($payment);

        return response()->json(['success' => true]);
    }

    public function receipt(Request $request, int $id): StreamedResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $payment = VaultPayment::findOrFail($id);
        if (! $payment->receipt_path || ! Storage::disk('local')->exists($payment->receipt_path)) {
            return response()->json(['success' => false, 'message' => 'Receipt not found.'], 404);
        }

        return Storage::disk('local')->download($payment->receipt_path, 'receipt-'.$payment->id);
    }

    protected function validatePayload(Request $request, bool $partial = false): array
    {
        $rules = [
            'vault_user_id' => ($partial ? 'sometimes|' : '').'required|exists:vault_users,id',
            'vault_subscription_id' => 'nullable|exists:vault_subscriptions,id',
            'license_id' => 'nullable|exists:licenses,id',
            'amount' => ($partial ? 'sometimes|' : '').'required|numeric|min:0',
            'currency' => 'nullable|string|max:8',
            'paid_at' => ($partial ? 'sometimes|' : '').'required|date',
            'method' => 'nullable|in:cash,bank_transfer,cheque,other,faysal_bank,meezan_bank',
            'reference_number' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|max:10240',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'status' => 'nullable|in:pending,completed,void',
            'notes' => 'nullable|string',
        ];

        return $request->validate($rules);
    }
}
