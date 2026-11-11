<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Instructor;
use App\Models\Instructor_awards;
use App\Models\Instructor_certificate;
use App\Models\Organization;
use App\Models\Skill;
use App\Models\State;
use App\Models\User;
use App\Tools\Repositories\Crud;
use App\Traits\ApiStatusTrait;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InstructorProfileController extends Controller
{
    use  ImageSaveTrait, General, SendNotification, ApiStatusTrait;
    protected $organizationModel;
    protected $instructorModel;

    public function __construct(Instructor $instructor, Organization $organization)
    {
        $this->instructorModel = new Crud($instructor);
        $this->organizationModel = new Crud($organization);
    }

    // public function profileApiBackUp()
    // {
    //     $user = auth()->user();
    //     $instructor = $user->instructor;

    //     if (!$instructor) {
    //         return $this->error('Instructor profile not found', 404);
    //     }

    //     // Get all available skills
    //     $allSkills = Skill::where('status', 1)->get(['id', 'title']);

    //     // Get instructor's associated skills (from pivot table)
    //     $instructorSkills = $instructor->skills()->get(['id', 'title']);

    //     // Get instructor awards
    //     $awards = Instructor_awards::where('instructor_id', $instructor->id)
    //         ->get(['id', 'name', 'winning_year']);

    //     // Get instructor certificates
    //     $certificates = Instructor_certificate::where('instructor_id', $instructor->id)
    //         ->select('id', 'name as title', 'passing_year as show_date')
    //         ->get();

    //     // Build the response structure
    //     $response = [
    //         'instructor' => [
    //             'id' => $instructor->id,
    //             'uuid' => $instructor->uuid,
    //             'user_id' => $user->id,
    //             'first_name' => $instructor->name,
    //             'last_name' => $instructor->last_name,
    //             'email' => $user->email,
    //             'phone' => $instructor->phone_number,
    //             'bio' => $instructor->about_me,
    //             'professional_title' => $instructor->professional_title,
    //             'gender' => $instructor->gender,
    //             'social_links' => json_decode($instructor->social_link, true),
    //             'image_url' => $user->image
    //                 ? url($user->image)
    //                 : url('uploads/default/instructor-default.png'),
    //             'skills' => $instructorSkills,
    //             // Add any other relevant user fields here
    //         ],

    //         'awards' => $awards,
    //         'certificates' => $certificates,
    //         'all_skills' => $allSkills, // All available skills
    //     ];

    //     return $this->success($response);
    // }



       public function profileApi()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'User Is Not Logged In'], 403);
        }

        if ($user->role != 2 && $user->role != 5) { // Instructor
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 403);
        }

        if($user->role == 2){
        $instructor = $user->instructor;

        if (!$instructor) {
            return $this->error('Instructor profile not found', 404);
        }

        // Get all available skills
        $allSkills = Skill::where('status', 1)->get(['id', 'title']);

        // Get instructor's associated skills (from pivot table)
        $instructorSkills = $instructor->skills()->get(['id', 'title']);

        // Get instructor awards
        $awards = Instructor_awards::where('instructor_id', $instructor->id)
            ->get(['id', 'name', 'winning_year']);

        // Get instructor certificates
        $certificates = Instructor_certificate::where('instructor_id', $instructor->id)
            ->select('id', 'name as title', 'passing_year as show_date')
            ->get();

        // Build the response structure
        $response = [
            'instructor' => [
                'id' => $instructor->id,
                'uuid' => $instructor->uuid,
                'user_id' => $user->id,
                'first_name' => $instructor->first_name,
                'last_name' => $instructor->last_name,
                'email' => $user->email,
                'phone' => $instructor->phone_number,
                'bio' => $instructor->about_me,
                'professional_title' => $instructor->professional_title,
                'gender' => $instructor->gender,
                'social_links' => json_decode($instructor->social_link, true),
                'image_url' => $user->image
                    ? url($user->image)
                    : url('uploads/default/instructor-default.png'),
                'skills' => $instructorSkills,
                // Add any other relevant user fields here
            ],

            'awards' => $awards,
            'certificates' => $certificates,
            'all_skills' => $allSkills, // All available skills
        ];

        return $this->success($response);
        }
        else{
        $coordinator = $user->coordinator;

        if (!$coordinator) {
            return $this->error('Coordinator profile not found', 404);
        }



        // Build the response structure
        $response = [
            'instructor' => [
                'id' => $coordinator->id,
                'uuid' => $coordinator->uuid,
                'user_id' => $user->id,
                'first_name' => $coordinator->first_name,
                'last_name' => $coordinator->last_name,
                'email' => $user->email,
                'phone' => $coordinator->phone_number,
                'bio' => $coordinator->about_me,
                'professional_title' => $coordinator->professional_title,
                'gender' => $coordinator->gender,
                'social_links' => json_decode($coordinator->social_link, true),
                'image_url' => $user->image
                    ? url($user->image)
                    : url('uploads/default/instructor-default.png'),
                // 'skills' => $instructorSkills,
                // Add any other relevant user fields here
            ],

            // 'awards' => $awards,
            // 'certificates' => $certificates,
            // 'all_skills' => $allSkills, // All available skills
        ];

        return $this->success($response);
        } 
    }

    // public function saveProfileApiBackUp(Request $request, $uuid)
    // {
    //     try {

    //         $user = auth()->user();

    //         if (!$user) {
    //             return response()->json(['message' => 'User Is Not Logged In'], 403);
    //         }

    //         if ($user->role != 2 && $user->role != 5) { // Instructor
    //             return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 403);
    //         }

    //         $instructor = $this->instructorModel->getRecordByUuid($uuid);

    //         if (!$instructor) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Instructor not found'
    //             ], 404);
    //         }

    //         $user = User::find($instructor->user_id);

    //         // Validate required fields
    //         $request->validate([
    //             'first_name' => 'required|string',
    //             'last_name' => 'required|string',
    //             'email' => 'required|email|unique:users,email,' . $user->id,
    //             'phone_number' => 'nullable',
    //             'gender' => 'required'
    //         ], [
    //             'gender.required' => 'The gender field is required.',
    //             'first_name.required' => 'The first name field is required.',
    //             'last_name.required' => 'The last name field is required.',
    //             'email.required' => 'The email field is required.',
    //             'email.email' => 'The email must be a valid email address.',
    //             'email.unique' => 'The email has already been taken.',
    //         ]);

    //         // Update user email and name
    //         $user->email = $request->email;
    //         $user->name = $request->first_name . ' ' . $request->last_name;

    //         // Handle image if present
    //         if ($request->hasFile('image')) {
    //             $request->validate([
    //                 'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
    //             ]);
    //             $this->deleteFile($user->image);
    //             $user->image = $this->saveImage('user', $request->file('image'));
    //         }

    //         $user->update();

    //         // Prepare instructor data
    //         $instructorData = [
    //             'first_name' => $request->first_name,
    //             'last_name' => $request->last_name,
    //             'professional_title' => $request->professional_title ?? "",
    //             'phone_number' => $request->phone_number,
    //             'about_me' => $request->about_me ?? "",
    //             'gender' => $request->gender,
    //             'social_link' => $request->has('social_link')
    //                 ? json_encode($request->social_link)
    //                 : json_encode([
    //                     'facebook' => null,
    //                     'twitter' => null,
    //                     'linkedin' => null,
    //                     'pinterest' => null
    //                 ])
    //         ];

    //         $instructor = $this->instructorModel->updateByUuid($instructorData, $uuid);

    //         // Sync skills if present
    //         if ($request->has('skills')) {
    //             $request->validate([
    //                 'skills' => 'sometimes|array',
    //             ]);
    //             $instructor->skills()->sync($request->skills ?? []);
    //         }

    //         // Handle certificates if present (now nullable)
    //         if ($request->has('certificate_title') && $request->has('certificate_date')) {
    //             $certificate_title = $request->certificate_title;
    //             $certificate_date = $request->certificate_date;

    //             Instructor_certificate::where('instructor_id', $instructor->id)->delete();

    //             if (is_array($certificate_title)) {
    //                 for ($c = 0; $c < count($certificate_title); $c++) {
    //                     if (!empty($certificate_title[$c]) && !empty($certificate_date[$c])) {
    //                         Instructor_certificate::create([
    //                             'instructor_id' => $instructor->id,
    //                             'name' => $certificate_title[$c],
    //                             'passing_year' => $certificate_date[$c],
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }

    //         // Handle awards if present (now nullable)
    //         if ($request->has('award_title') && $request->has('award_year')) {
    //             $award_title = $request->award_title;
    //             $award_year = $request->award_year;

    //             Instructor_awards::where('instructor_id', $instructor->id)->delete();

    //             if (is_array($award_title)) {
    //                 for ($a = 0; $a < count($award_title); $a++) {
    //                     if (!empty($award_title[$a]) && !empty($award_year[$a])) {
    //                         Instructor_awards::create([
    //                             'instructor_id' => $instructor->id,
    //                             'name' => $award_title[$a],
    //                             'winning_year' => $award_year[$a],
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Profile updated successfully',
    //         ], 200);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation error',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Server error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }




      public function saveProfileApi(Request $request, $uuid)
    {
        try {

            $user = auth()->user();
    
            if (!$user) {
                return response()->json(['message' => 'User Is Not Logged In'], 403);
            }
    
            if ($user->role != 2 && $user->role != 5) { // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 403);
            }

        if($user->role == 2){
            $instructor = $this->instructorModel->getRecordByUuid($uuid);

            if (!$instructor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instructor not found'
                ], 404);
            }

            $user = User::find($instructor->user_id);

            // Validate required fields
            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone_number' => 'nullable',
                'gender' => 'required'
            ], [
                'gender.required' => 'The gender field is required.',
                'first_name.required' => 'The first name field is required.',
                'last_name.required' => 'The last name field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
                'email.unique' => 'The email has already been taken.',
            ]);

            // Update user email and name
            $user->email = $request->email;
            $user->name = $request->first_name . ' ' . $request->last_name;

            // Handle image if present
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
                $this->deleteFile($user->image);
                $user->image = $this->saveImage('user', $request->file('image'));
            }

            $user->update();

            // Prepare instructor data
            $instructorData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'professional_title' => $request->professional_title ?? "",
                'phone_number' => $request->phone_number,
                'about_me' => $request->about_me ?? "",
                'gender' => $request->gender,
                'social_link' => $request->has('social_link')
                    ? json_encode($request->social_link)
                    : json_encode([
                        'facebook' => null,
                        'twitter' => null,
                        'linkedin' => null,
                        'pinterest' => null
                    ])
            ];

            $instructor = $this->instructorModel->updateByUuid($instructorData, $uuid);

            // Sync skills if present
            if ($request->has('skills')) {
                $request->validate([
                    'skills' => 'sometimes|array',
                ]);
                $instructor->skills()->sync($request->skills ?? []);
            }

            // Handle certificates if present (now nullable)
            if ($request->has('certificate_title') && $request->has('certificate_date')) {
                $certificate_title = $request->certificate_title;
                $certificate_date = $request->certificate_date;

                Instructor_certificate::where('instructor_id', $instructor->id)->delete();

                if (is_array($certificate_title)) {
                    for ($c = 0; $c < count($certificate_title); $c++) {
                        if (!empty($certificate_title[$c]) && !empty($certificate_date[$c])) {
                            Instructor_certificate::create([
                                'instructor_id' => $instructor->id,
                                'name' => $certificate_title[$c],
                                'passing_year' => $certificate_date[$c],
                            ]);
                        }
                    }
                }
            }

            // Handle awards if present (now nullable)
            if ($request->has('award_title') && $request->has('award_year')) {
                $award_title = $request->award_title;
                $award_year = $request->award_year;

                Instructor_awards::where('instructor_id', $instructor->id)->delete();

                if (is_array($award_title)) {
                    for ($a = 0; $a < count($award_title); $a++) {
                        if (!empty($award_title[$a]) && !empty($award_year[$a])) {
                            Instructor_awards::create([
                                'instructor_id' => $instructor->id,
                                'name' => $award_title[$a],
                                'winning_year' => $award_year[$a],
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
            ], 200);
        }
        else{
            $coordinatoor = Coordinator::where('user_id', $user->id)->first();

            if (!$coordinatoor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coordinator not found'
                ], 404);
            }

            $user = User::find($coordinatoor->user_id);

            // Validate required fields
            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone_number' => 'nullable',
                'gender' => 'required'
            ], [
                'gender.required' => 'The gender field is required.',
                'first_name.required' => 'The first name field is required.',
                'last_name.required' => 'The last name field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
                'email.unique' => 'The email has already been taken.',
            ]);

            // Update user email and name
            $user->email = $request->email;
            $user->name = $request->first_name . ' ' . $request->last_name;

            // Handle image if present
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);
                $this->deleteFile($user->image);
                $user->image = $this->saveImage('user', $request->file('image'));
            }

            $user->update();


            $coordinatoor->first_name = $request->first_name;
            $coordinatoor->last_name = $request->last_name;
            $coordinatoor->professional_title = $request->professional_title ?? '';
            $coordinatoor->phone_number = $request->phone_number;
            $coordinatoor->about_me = $request->about_me ?? '';
            $coordinatoor->gender = $request->gender;
            $coordinatoor->social_link = $request->has('social_link')
                ? json_encode($request->social_link)
                : json_encode([
                    'facebook' => null,
                    'twitter' => null,
                    'linkedin' => null,
                    'pinterest' => null,
                ]);

            $coordinatoor->save();

        

        
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
            ], 200);
        }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAddress()
    {
        $instructor = Auth::user()->instructor;
        $user = Auth::user();

        // Load relationships
        $instructor->load(['country', 'state', 'city']);

        $data = [
            'country' => $instructor->country ? [
                'id' => $instructor->country->id,
                'name' => $instructor->country->country_name
            ] : null,
            'state' => $instructor->state ? [
                'id' => $instructor->state->id,
                'name' => $instructor->state->name
            ] : null,
            'city' => $instructor->city ? [
                'id' => $instructor->city->id,
                'name' => $instructor->city->name
            ] : null,
            'postal_code' => $instructor->postal_code ?? null,
            'address' => $instructor->address ?? null,
            'coordinates' => [
                'lat' => $user->lat ?? null,
                'long' => $user->long ?? null,
            ],
            'countries' => Country::orderBy('country_name', 'asc')
                ->get(['id', 'country_name as name']),
        ];

        // Load states if country is set
        if ($instructor->country_id) {
            $data['states'] = State::where('country_id', $instructor->country_id)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);
        }

        // Load cities if state is set
        if ($instructor->state_id) {
            $data['cities'] = City::where('state_id', $instructor->state_id)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update instructor address
     */
    public function updateAddress(Request $request)
    {
        $validtion = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'lat' => 'nullable|string',
            'long' => 'nullable|string',
        ], [
            'country_id.required' => 'The country field is required.',
            'address.required' => 'The address field is required.',
            // Add other custom messages as needed
        ]);
        if ($validtion->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validtion->errors(),
            ], 422);
        }

        $instructor = Auth::user()->instructor;
        $user = Auth::user();

        // Update user location
        $user->update([
            'lat' => $request->lat,
            'long' => $request->long,
        ]);

        // Update instructor address
        $instructor->update([
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $instructor->fresh()
        ]);
    }


    public function getStateByCountry($country_id)
    {
        $states = State::where('country_id', $country_id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $states
        ]);
    }
    public function getCityByState($state_id)
    {
        $cities = City::where('state_id', $state_id)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }
}
