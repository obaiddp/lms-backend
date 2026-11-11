<?php

namespace App\Http\Controllers\Api\VaultAdmin;

use App\Http\Controllers\Controller;
use App\Services\VaultLicenseAdminService;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use ChecksVaultAdminPermission;

    public function __construct(protected VaultLicenseAdminService $service) {}

    public function curriculum(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'all_licenses');

        return response()->json([
            'success' => true,
            'data' => $this->service->courseCurriculum($id),
        ]);
    }

    public function storeLesson(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
        ]);

        try {
            $lesson = $this->service->createLesson($id, $validated);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $lesson,
        ], 201);
    }

    public function updateLesson(Request $request, int $id, int $lessonId)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'short_description' => 'nullable|string',
        ]);

        try {
            $lesson = $this->service->updateLesson($id, $lessonId, $validated);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $lesson]);
    }

    public function destroyLesson(Request $request, int $id, int $lessonId)
    {
        $this->authorizeVaultPermission($request, 'delete_license');

        try {
            $this->service->deleteLesson($id, $lessonId);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    public function reorderLessons(Request $request, int $id)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $validated = $request->validate([
            'lesson_ids' => 'required|array|min:1',
            'lesson_ids.*' => 'integer',
        ]);

        try {
            $this->service->reorderLessons($id, $validated['lesson_ids']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    public function storeLecture(Request $request, int $id, int $lessonId)
    {
        $this->authorizeVaultPermission($request, 'add_license');

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'resource_id' => 'required|integer|exists:resources,id',
        ]);

        try {
            $lecture = $this->service->createLecture($id, $lessonId, $validated);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $lecture], 201);
    }

    public function updateLecture(Request $request, int $id, int $lessonId, int $lectureId)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'resource_id' => 'sometimes|integer|exists:resources,id',
        ]);

        try {
            $lecture = $this->service->updateLecture($id, $lessonId, $lectureId, $validated);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $lecture]);
    }

    public function destroyLecture(Request $request, int $id, int $lessonId, int $lectureId)
    {
        $this->authorizeVaultPermission($request, 'delete_license');

        try {
            $this->service->deleteLecture($id, $lessonId, $lectureId);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }

    public function reorderLectures(Request $request, int $id, int $lessonId)
    {
        $this->authorizeVaultPermission($request, 'update_license');

        $validated = $request->validate([
            'lecture_ids' => 'required|array|min:1',
            'lecture_ids.*' => 'integer',
        ]);

        try {
            $this->service->reorderLectures($id, $lessonId, $validated['lecture_ids']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true]);
    }
}
