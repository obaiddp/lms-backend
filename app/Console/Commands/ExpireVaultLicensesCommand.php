<?php

namespace App\Console\Commands;

use App\Services\VaultBillingAdminService;
use Illuminate\Console\Command;

class ExpireVaultLicensesCommand extends Command
{
    protected $signature = 'vault:expire-licenses';

    protected $description = 'Deactivate expired vault licenses and mark subscriptions expired';

    public function handle(VaultBillingAdminService $billingService): int
    {
        $result = $billingService->expireOverdueLicensesAndSubscriptions();

        $this->info(sprintf(
            'Expired %d license(s) and %d subscription(s).',
            $result['licenses'],
            $result['subscriptions']
        ));

        return self::SUCCESS;
    }
}
