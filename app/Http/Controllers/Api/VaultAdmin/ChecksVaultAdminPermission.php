<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

trait ChecksVaultAdminPermission
{
    protected function authorizeVaultPermission(Request $request, string $permission): void
    {
        if (! $request->user()?->can($permission)) {
            abort(403, 'Forbidden');
        }
    }
}
