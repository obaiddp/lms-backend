<?php

namespace App\Jobs;

use App\Models\VaultOfflinePack;
use App\Services\VaultOfflinePackService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BuildVaultOfflinePackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 7200;

    public function __construct(
        public int $vaultOfflinePackId
    ) {}

    public function handle(VaultOfflinePackService $service): void
    {
        $pack = VaultOfflinePack::query()->find($this->vaultOfflinePackId);
        if (! $pack) {
            Log::warning('BuildVaultOfflinePackJob: pack row missing', ['id' => $this->vaultOfflinePackId]);

            return;
        }

        try {
            $service->buildPack($pack);
        } catch (\Throwable $e) {
            Log::error('BuildVaultOfflinePackJob failed', [
                'pack_id' => $pack->pack_id,
                'message' => $e->getMessage(),
            ]);
            $pack->status = 'failed';
            $pack->error = $e->getMessage();
            $pack->save();

            throw $e;
        }
    }
}
