<?php

namespace App\Http\Middleware;

use App\Services\VaultLicenseAdminService;
use Closure;
use Illuminate\Http\Request;

class EnsureVaultAdminLicensePermission
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (! $user->is_admin()) {
            return response()->json(['message' => 'Vault admin access requires an admin account.'], 403);
        }

        $hasAny = false;
        foreach (VaultLicenseAdminService::LICENSE_PERMISSIONS as $perm) {
            if ($user->can($perm)) {
                $hasAny = true;
                break;
            }
        }

        if (! $hasAny) {
            return response()->json(['message' => 'You do not have license management permissions.'], 403);
        }

        return $next($request);
    }
}
