<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\VaultOfflinePack;
use App\Services\VaultOfflinePackService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OfflinePackController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(
        protected VaultOfflinePackService $service
    ) {}

    public function index(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');
        $license = License::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->service->listPacks($license),
        ]);
    }

    public function store(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');
        $license = License::findOrFail($id);

        $validated = $request->validate([
            'validity_mode' => 'required|in:absolute,duration',
            'duration_days' => 'nullable|integer|min:1|max:3650',
        ]);

        if ($validated['validity_mode'] === 'duration' && empty($validated['duration_days'])) {
            return response()->json([
                'success' => false,
                'message' => 'duration_days is required when validity_mode is duration.',
            ], 422);
        }

        try {
            $pack = $this->service->queuePack(
                $license,
                $validated,
                $request->user()?->id
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializePack($pack),
        ], 201);
    }

    public function download(Request $request, int $id, string $packId): BinaryFileResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorizeVaultPermission($request, 'update_license');
        $license = License::findOrFail($id);
        $pack = VaultOfflinePack::query()
            ->where('license_id', $license->id)
            ->where('pack_id', $packId)
            ->firstOrFail();

        if ($pack->status !== 'ready') {
            return response()->json([
                'success' => false,
                'message' => 'Pack is not ready for download (status: '.$pack->status.').',
            ], 409);
        }

        $path = $this->service->absoluteStoragePath($pack);
        if ($path === null) {
            return response()->json(['success' => false, 'message' => 'Pack file missing on disk.'], 404);
        }

        $downloadName = 'cef-vault-offline-'.$license->id.'-'.$pack->pack_id.'.zip';

        return response()->download($path, $downloadName, [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function destroy(Request $request, int $id, string $packId)
    {
        $this->authorizeVaultPermission($request, 'delete_license');
        $license = License::findOrFail($id);
        $pack = VaultOfflinePack::query()
            ->where('license_id', $license->id)
            ->where('pack_id', $packId)
            ->firstOrFail();

        $this->service->deletePack($pack);

        return response()->json(['success' => true]);
    }
}
