<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Services\VaultLicenseAdminService;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(protected VaultLicenseAdminService $service) {}

    public function index(Request $request)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $paginator = $this->service->paginateResources(
            $request->only('search', 'category_id'),
            (int) $request->get('per_page', 20)
        );
        $paginator->getCollection()->transform(fn (Resource $r) => $this->service->serializeResourceListItem($r));

        return response()->json(['success' => true, 'data' => $paginator]);
    }

    public function store(Request $request)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $validated = $this->validateResourceRequest($request, isCreate: true);

        $files = $this->collectResourceFiles($request);
        $courseId = $request->filled('course_id') ? (int) $request->course_id : null;

        try {
            $resource = $this->service->createVaultResource($validated, $files, $courseId);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeResourceListItem($resource),
        ], 201);
    }

    public function show(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        $resource = Resource::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => array_merge(
                $this->service->serializeResourceListItem($resource),
                [
                    'youtube_url_path' => $resource->youtube_url_path,
                    'youtube_file_duration' => $resource->youtube_video_duration,
                    'text' => $resource->text,
                ]
            ),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $resource = Resource::findOrFail($id);
        $validated = $this->validateResourceRequest($request, isCreate: false, resource: $resource);
        $files = $this->collectResourceFiles($request);

        if ($request->filled('course_id')) {
            $validated['course_id'] = (int) $request->course_id;
        }

        try {
            $resource = $this->service->updateVaultResource($resource, $validated, $files);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeResourceListItem($resource),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'delete_license');

        $resource = Resource::findOrFail($id);
        $this->service->deleteVaultResource($resource);

        return response()->json(['success' => true]);
    }

    public function preview(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        return response()->json([
            'success' => true,
            'data' => $this->service->resourcePreview($id),
        ]);
    }

    public function retryVaultDownload(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        try {
            $data = $this->service->retryVaultDownload($id);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vault download queued.',
            'data' => $data,
        ]);
    }

    public function categories(Request $request)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        return response()->json([
            'success' => true,
            'data' => $this->service->listResourceCategories(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateResourceRequest(Request $request, bool $isCreate, ?Resource $resource = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|integer|exists:resource_categories,id',
            'sort_number' => 'nullable|integer',
            'course_id' => 'nullable|integer|exists:courses,id',
            'text' => 'nullable|string',
            'youtube_url_path' => 'nullable|string',
            'youtube_file_duration' => 'nullable|string',
        ]);

        $category = ResourceCategory::findOrFail($validated['category_id']);
        $categoryName = $category->name;

        switch ($categoryName) {
            case 'Video':
                $request->validate([
                    'video_file' => ($isCreate ? 'required|' : 'nullable|').'file|mimes:mp4,webm,mkv',
                ]);
                break;
            case 'Audio':
                if ($isCreate || $request->hasFile('audio')) {
                    $request->validate(['audio' => ($isCreate ? 'required|' : '').'file|mimes:mp3,wav,m4a']);
                }
                break;
            case 'Image':
                if ($isCreate || $request->hasFile('image')) {
                    $request->validate(['image' => ($isCreate ? 'required|' : '').'file|mimes:jpg,png,gif,webp']);
                }
                break;
            case 'PDF':
                if ($isCreate || $request->hasFile('pdf')) {
                    $request->validate(['pdf' => ($isCreate ? 'required|' : '').'file|mimes:pdf']);
                }
                break;
            case 'Slide Document':
                if ($isCreate || $request->hasFile('slide_document')) {
                    $request->validate(['slide_document' => ($isCreate ? 'required|' : '').'file|mimes:zip']);
                }
                break;
            case 'Text':
                $request->validate(['text' => ($isCreate ? 'required|' : '').'string']);
                break;
            case 'Youtube':
                $request->validate([
                    'youtube_url_path' => ($isCreate ? 'required|' : '').'string',
                    'youtube_file_duration' => ($isCreate ? 'required|' : '').'string',
                ]);
                break;
        }

        return $validated;
    }

    /**
     * @return array<string, \Illuminate\Http\UploadedFile|null>
     */
    protected function collectResourceFiles(Request $request): array
    {
        return [
            'video_file' => $request->file('video_file'),
            'audio' => $request->file('audio'),
            'image' => $request->file('image'),
            'pdf' => $request->file('pdf'),
            'slide_document' => $request->file('slide_document'),
        ];
    }
}
