<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Serves vault video files via a secure route.
 * Validates license; returns file content encrypted (AES-256-CBC) so only the vault app can decrypt and play.
 */
class VaultStreamController extends Controller
{
    /**
     * Stream encrypted vault video. License must be valid and resource must belong to a course linked to the license.
     * Response: raw bytes = IV (16) + AES-256-CBC encrypted file. Header X-Vault-Encrypted: 1.
     */
    public function streamVideo(Request $request, int $resourceId): Response|JsonResponse
    {
        $licenseKey = $request->query('license') ?? $request->header('X-Vault-License');
        if (empty($licenseKey)) {
            return response()->json(['success' => false, 'message' => 'License required'], 403);
        }

        $license = License::with(['courses'])->where('key', $licenseKey)->first();
        if (!$license) {
            return response()->json(['success' => false, 'message' => 'Invalid license'], 403);
        }

        $endDate = $license->end_date ?? null;
        if (!$endDate || Carbon::parse($endDate)->isPast()) {
            return response()->json(['success' => false, 'message' => 'License expired'], 403);
        }

        $resource = Resource::find($resourceId);
        if (!$resource || strtolower((string) $resource->type) !== 'youtube') {
            return response()->json(['success' => false, 'message' => 'Resource not found'], 404);
        }

        if (empty($resource->vault_video_path)) {
            return response()->json(['success' => false, 'message' => 'Video not ready for vault'], 404);
        }

        $disk = Storage::disk('local');
        if (!$disk->exists($resource->vault_video_path)) {
            return response()->json(['success' => false, 'message' => 'Video file missing'], 404);
        }

        // Check resource is in a course linked to this license
        $licenseCourseIds = $license->courses->pluck('id')->toArray();
        $linked = DB::table('course_resources')
            ->where('resource_id', $resourceId)
            ->whereIn('course_id', $licenseCourseIds)
            ->exists();
        if (!$linked) {
            return response()->json(['success' => false, 'message' => 'Resource not in license'], 403);
        }

        $fullPath = $disk->path($resource->vault_video_path);
        $content = file_get_contents($fullPath);
        if ($content === false) {
            return response()->json(['success' => false, 'message' => 'Read error'], 500);
        }

        $key = base64_decode(Config::get('app.asset_encryption_key'));
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt(
            $content,
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        if ($encrypted === false) {
            return response()->json(['success' => false, 'message' => 'Encryption error'], 500);
        }

        $body = $iv . $encrypted;
        return response($body, 200, [
            'Content-Type' => 'application/octet-stream',
            'X-Vault-Encrypted' => '1',
            'Content-Length' => (string) strlen($body),
        ]);
    }
}
