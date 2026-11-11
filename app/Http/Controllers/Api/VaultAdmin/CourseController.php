<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseResource;
use App\Services\VaultLicenseAdminService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(protected VaultLicenseAdminService $service) {}

    public function index(Request $request)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        if ($request->boolean('for_license_picker')) {
            $includeIds = array_filter(array_map('intval', explode(',', (string) $request->get('include_ids', ''))));

            return response()->json([
                'success' => true,
                'data' => $this->service->coursesForLicensePicker($includeIds),
            ]);
        }

        $paginator = $this->service->paginateVaultCourses(
            $request->only('search'),
            (int) $request->get('per_page', 20)
        );
        $paginator->getCollection()->transform(fn (Course $c) => $this->service->serializeCourse($c));

        return response()->json(['success' => true, 'data' => $paginator]);
    }

    public function store(Request $request)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subtitle' => 'nullable|string|max:255',
            'intro_video_check' => 'nullable|integer|in:0,2',
            'youtube_video_id' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'resource_ids' => 'nullable|array',
            'resource_ids.*' => 'integer|exists:resources,id',
        ]);

        if ((int) ($validated['intro_video_check'] ?? 0) === 2) {
            $request->validate(['youtube_video_id' => 'required|string|max:255']);
        }

        try {
            $course = $this->service->createVaultCourse($validated, $request->file('image'));
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeCourseDetail(
                $course,
                $validated['resource_ids'] ?? []
            ),
        ], 201);
    }

    public function show(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        return response()->json([
            'success' => true,
            'data' => $this->service->courseDetail($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subtitle' => 'nullable|string|max:255',
            'intro_video_check' => 'nullable|integer|in:0,2',
            'youtube_video_id' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'resource_ids' => 'nullable|array',
            'resource_ids.*' => 'integer|exists:resources,id',
        ]);

        if ((int) ($validated['intro_video_check'] ?? 0) === 2) {
            $request->validate(['youtube_video_id' => 'required|string|max:255']);
        }

        try {
            $course = $this->service->updateVaultCourse($course, $validated, $request->file('image'));
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $resourceIds = CourseResource::where('course_id', $course->id)->pluck('resource_id')->all();
        if (array_key_exists('resource_ids', $validated)) {
            $resourceIds = $validated['resource_ids'] ?? [];
        }

        return response()->json([
            'success' => true,
            'data' => $this->service->serializeCourseDetail($course, $resourceIds),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'delete_license');

        $course = Course::findOrFail($id);

        try {
            $this->service->deleteVaultCourse($course);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    public function resources(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        return response()->json([
            'success' => true,
            'data' => $this->service->courseResources($id),
        ]);
    }

    public function detachResource(Request $request, int $id, int $resourceId)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        try {
            $this->service->detachResourceFromCourse($id, $resourceId);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    public function rebuildManifest(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        try {
            $data = $this->service->rebuildVaultCourseManifest($id);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}
