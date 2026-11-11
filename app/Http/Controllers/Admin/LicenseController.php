<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\License;
use App\Models\VaultUser;
use App\Services\VaultLicenseAdminService;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    use General, ImageSaveTrait;

    public function __construct(protected VaultLicenseAdminService $vaultLicenseAdminService) {}

    public function index()
    {
        if (! Auth::user()->can('all_licenses')) {
            abort('403');
        }

        $data['title'] = 'Licenses';
        $data['licenses'] = License::with(['vaultUser', 'courses'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.license.index', $data);
    }

    public function create()
    {
        if (! Auth::user()->can('add_license')) {
            abort('403');
        }

        $data['title'] = 'License Create';
        $data['vaultUsers'] = VaultUser::orderBy('name')->get();
        $data['courses'] = Course::all();

        return view('admin.license.create', $data);
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('add_license')) {
            abort('403');
        }

        $request->validate([
            'vault_user_id' => 'required|exists:vault_users,id',
            'license_key' => 'required|string|unique:licenses,key',
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after:start_date',
            'courses' => 'required|array|min:1',
            'devices' => 'nullable|array',
        ]);

        try {
            $this->vaultLicenseAdminService->createLicense([
                'vault_user_id' => $request->vault_user_id,
                'license_key' => $request->license_key,
                'start_date' => $request->start_date,
                'expiry_date' => $request->expiry_date,
                'status' => 'active',
                'courses' => $request->courses,
                'devices' => $request->devices ?? [],
            ]);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }

        return redirect()->route('admin.license.index')->with('success', 'License created successfully.');
    }

    public function show($id)
    {
        if (! Auth::user()->can('all_licenses')) {
            abort('403');
        }

        $license = License::with(['vaultUser', 'courses', 'licenseDevices'])->findOrFail($id);

        return view('admin.license.show', compact('license'));
    }

    public function usageHistory($id)
    {
        if (! Auth::user()->can('all_licenses')) {
            abort(403);
        }

        $license = License::with(['vaultUser', 'licenseDevices'])->findOrFail($id);
        $usage = $this->vaultLicenseAdminService->licenseUsage($license, 40);

        return view('admin.license.usage_history', [
            'license' => $license,
            'devices' => $usage['devices'],
            'activeActivations' => $usage['active_activations'],
            'revokedDevices' => $usage['revoked_devices'],
            'events' => $usage['events'],
        ]);
    }

    public function edit($id)
    {
        if (! Auth::user()->can('update_license')) {
            abort('403');
        }

        $license = License::with(['vaultUser', 'licenseCourses', 'licenseDevices'])->findOrFail($id);

        $data['title'] = 'Edit License';
        $data['license'] = $license;
        $data['vaultUsers'] = VaultUser::orderBy('name')->get();
        $data['courses'] = Course::all();
        $data['startDate'] = Carbon::parse($license->start_date)->toDateString();
        $data['endDate'] = Carbon::parse($license->end_date)->toDateString();
        $data['selected_courses'] = $license->licenseCourses->pluck('course_id')->toArray();

        return view('admin.license.edit', $data);
    }

    public function update(Request $request, $id)
    {
        if (! Auth::user()->can('update_license')) {
            abort('403');
        }

        $license = License::findOrFail($id);

        $request->validate([
            'vault_user_id' => 'required|exists:vault_users,id',
            'license_key' => 'required|string|unique:licenses,key,'.$license->id,
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after:start_date',
            'courses' => 'required|array|min:1',
            'devices' => 'nullable|array',
        ]);

        try {
            $this->vaultLicenseAdminService->updateLicense($license, [
                'vault_user_id' => $request->vault_user_id,
                'license_key' => $request->license_key,
                'start_date' => $request->start_date,
                'expiry_date' => $request->expiry_date,
                'status' => $license->status,
                'courses' => $request->courses,
                'devices' => $request->devices ?? [],
            ]);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }

        return redirect()->route('admin.license.index')->with('success', 'License updated successfully.');
    }

    public function destroy($id)
    {
        if (! Auth::user()->can('delete_license')) {
            abort('403');
        }

        try {
            $license = License::findOrFail($id);
            $this->vaultLicenseAdminService->deleteLicense($license);

            $this->showToastrMessage('success', __('License Successfully'));

            return redirect()->back();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete license: '.$e->getMessage(),
            ]);
        }
    }
}
