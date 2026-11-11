<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AffiliateRequest;
use App\Models\CartManagement;
use App\Models\Category;
use App\Models\Course;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\TimeSlot;
use App\Models\BookedSlot;
use App\Models\CourseResource;
use App\Models\Difficulty_level;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Country;
use App\Models\TrialClass;
use App\Models\Review;
use App\Models\User;
use App\Models\Subcategory;
use App\Traits\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\SendNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\Instructor;

class CourseController extends Controller
{
    use General,SendNotification;
    private $coursePaginateValue = 9;

      public function categoryCourses(Request $request,$slug)
    {
        $data['pageTitle'] = "Courses";
        $data['metaData'] = staticMeta(4);
        $data['categories'] = Category::with('subcategories')->active()->get();
        $data['category'] = Category::whereSlug($slug)->firstOrFail();
        if($request->ajax()){
            $lastPage = false;
            $data['courses'] = $this->filterCourseData($request);
            $data['topCourse'] = Enrollment::query()
            ->whereMonth('created_at', now()->month)
            ->select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->limit(10)
            ->orderBy('total','desc')
            ->get()
            ->pluck('course_id')
            ->toArray();

            $data['courses'] = $data['courses']->paginate($this->coursePaginateValue);

            if( $data['courses']->lastPage() == $request->page){
                $lastPage = true;
            }

            $data['lastPage'] = $lastPage;
            $data['html'] = View::make('frontend.course.render-course-list-only', $data)->render();
            $data['status'] = true;
            return response()->json($data);
        }
        else{
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($data['category']->id)->active()->paginate($this->coursePaginateValue);
        }
   
        $data['topCourse'] = Enrollment::query()
                ->whereMonth('created_at', now()->month)
                ->select('course_id', DB::raw('count(*) as total'))
                ->groupBy('course_id')
                ->limit(10)
                ->orderBy('total','desc')
                ->get()
                ->pluck('course_id')
                ->toArray();
        $data['total_courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($data['category']->id)->active()->count();
        $data['difficulty_levels'] = Difficulty_level::all();
        $data['highest_price'] = Course::where('private_mode', '!=', 1)->publicCatalog()->max('price');
        $data['random_four_categories'] = Category::active()->inRandomOrder()->limit(4)->get();
        // $data['instructors'] = Instructor::all();
        $data['instructors'] = Instructor::where('is_public',1)->get();
        return view('frontend.course.category.category-course-list', $data);
    }




       public function subCategoryCourses(Request $request, $slug)
    {
        $data['pageTitle'] = "Subcategory Courses";
        $data['metaData'] = staticMeta(4);
        $data['categories'] = Category::with('subcategories')->active()->get();
        $data['subcategory'] = Subcategory::whereSlug($slug)->firstOrFail();
          if($request->ajax()){
            $lastPage = false;
            $data['courses'] = $this->filterCourseData($request);
            $data['topCourse'] = Enrollment::query()
            ->whereMonth('created_at', now()->month)
            ->select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->limit(10)
            ->orderBy('total','desc')
            ->get()
            ->pluck('course_id')
            ->toArray();

            $data['courses'] = $data['courses']->paginate($this->coursePaginateValue);

            if( $data['courses']->lastPage() == $request->page){
                $lastPage = true;
            }

            $data['lastPage'] = $lastPage;
            $data['html'] = View::make('frontend.course.render-course-list-only', $data)->render();
            $data['status'] = true;
            return response()->json($data);
        }
        else{
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereSubcategoryId($data['subcategory']->id)->active()->paginate($this->coursePaginateValue);
        }
    
        $data['topCourse'] = Enrollment::query()
                ->whereMonth('created_at', now()->month)
                ->select('course_id', DB::raw('count(*) as total'))
                ->groupBy('course_id')
                ->limit(10)
                ->orderBy('total','desc')
                ->get()
                ->pluck('course_id')
                ->toArray();
        $data['total_courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereSubcategoryId($data['subcategory']->id)->active()->count();
        $data['difficulty_levels'] = Difficulty_level::all();
        $data['highest_price'] = Course::where('private_mode', '!=', 1)->publicCatalog()->max('price');
        $data['random_four_categories'] = Category::active()->inRandomOrder()->limit(4)->get();
        // $data['instructors'] = Instructor::all();
        $data['instructors'] = Instructor::where('is_public',1)->get();
        return view('frontend.course.category.subcategory-course-list', $data);
    }



       public function getFilterCourse(Request $request)
    {
     
      
        // $data['courses'] = $this->filterCourseData($request);
        $data['courses'] = $this->filterCourseDataNew($request);
   
        $data['topCourse'] = Enrollment::query()
        ->whereMonth('created_at', now()->month)
        ->select('course_id', DB::raw('count(*) as total'))
        ->groupBy('course_id')
        ->limit(10)
        ->orderBy('total','desc')
        ->get()
        ->pluck('course_id')
        ->toArray();
        $data['courses'] = $data['courses']->paginate($this->coursePaginateValue);
    
        return view('frontend.course.render-course-list-filtered')->with($data);
        // return view('frontend.course.render-course-list')->with($data);
    }



    public function allCourses(Request $request)
    {
        $data['pageTitle'] = "Courses";
        $data['metaData'] = staticMeta(2);
        $data['categories'] = Category::with('subcategories')->active()->get();
        // dd($request->ajax());
        if($request->ajax()){
            $lastPage = false;
            $data['courses'] = $this->filterCourseData($request);
            $data['topCourse'] = Enrollment::query()
            ->whereMonth('created_at', now()->month)
            ->select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->limit(10)
            ->orderBy('total','desc')
            ->get()
            ->pluck('course_id')
            ->toArray();

            $data['courses'] = $data['courses']->paginate($this->coursePaginateValue);

            if( $data['courses']->lastPage() == $request->page){
                $lastPage = true;
            }

            $data['lastPage'] = $lastPage;
            $data['html'] = View::make('frontend.course.render-course-list-only', $data)->render();
            $data['status'] = true;
            return response()->json($data);
        }
        else{
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->with('course_instructors.user.instructor.instructor_courses')->active()->paginate($this->coursePaginateValue);
        }

      
        $data['total_courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->active()->count();
        $data['difficulty_levels'] = Difficulty_level::all();
        $data['highest_price'] = Course::where('private_mode', '!=', 1)->publicCatalog()->max('price');
        // $data['instructors'] = Instructor::all();
        $data['instructors'] = Instructor::where('is_public',1)->get();
        $data['random_four_categories'] = Category::active()->inRandomOrder()->limit(4)->get();
        // dd($data);
        return view('frontend.course.course-list', $data);
    }



    
    public function searchCourseList(Request $request)
    {
        if (!get_option('private_mode') || Auth::user()) {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->active()->where('title', 'like', '%' . $request->title . '%')->get();
        } else {
            $data['courses'] = [];
        }

        return view('frontend.render-search-course-list', $data);
    }

    public function courseDetails(Request $request, $slug)
    {
       
        $errCode = $request->err_code;
		$errMsg = $request->err_msg;
		if(isset($errCode) && isset($errMsg)){
            if ($errCode == '001') { 
                $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                return redirect()->route('course-details',['slug'=>$slug]);
            }
             else if($errCode == '90'){
                $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                return redirect()->route('course-details',['slug'=>$slug]);
             }
        }
        $data['pageTitle'] = "Course Details";
        $data['metaData'] = staticMeta(3);
        $getCourse = Course::whereSlug($slug)->first();
        $courseType = $getCourse->course_type;
        $isInstructorHidden = 1;
   
        $data['course'] = Course::whereSlug($slug)->first();
     
        foreach ($data['course']->course_instructors as $key => $course_instructor) {
            if($course_instructor->user?->is_public === 1){
                $isInstructorHidden = 0;
            }
        }
        $data['isInstructorHidden'] = $isInstructorHidden;
        $affilate_request = AffiliateRequest::where(['user_id' => Auth::id(), 'status' => STATUS_APPROVED])->first();
        if (!is_null($affilate_request)) {
            $ref_cod = $data['course']->id . '$' . $affilate_request->affiliate_code;
            $data['ref_link'] = route('course-details', $slug) . '?code=' . $ref_cod;
        } else {
            $data['ref_link'] = null;
        }

        if ($data['course']->status != STATUS_APPROVED && $data['course']->status != STATUS_UPCOMING_APPROVED){
            $this->showToastrMessage('error', __('This course is not active'));
            return redirect()->back();
        }

        $authUser = auth()->user();
     
      
        if($authUser){
        $getStudent = Student::where('user_id',$authUser->id)->first();
        $authRole = $authUser->role;
        $data['authRole'] = $authRole;
        $data['authUserStudent'] = $getStudent;
        }
        else{
            $data['authRole'] = null;  
            $data['authUserStudent'] = null;  
        }
        // dd($data['authRole']);
        $relation = ($authUser) ? getUserRoleRelation($authUser) : NULL;

        if($data['course']->private_mode == 1 && (is_null($authUser) || ($authUser->$relation->organization_id != $data['course']->organization_id && $authUser->id != $data['course']->user_id))){
            abort(403);
        }

        //Start:: Course Review
        $data['reviews'] = Review::whereCourseId($data['course']->id)->latest()->get();
    
        $data['loadMoreShowButtonReviews'] = Review::whereCourseId($data['course']->id)->paginate(4);
        $data['five_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(5)->count();
        $data['four_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(4)->count();
        $data['three_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(3)->count();
        $data['two_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(2)->count();
        $data['first_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(1)->count();

        $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
            (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
        $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];
        if ($data['total_user_review'] > 0) {
            $data['average_rating'] = $data['total_reviews']  / $data['total_user_review'];
        } else {
            $data['average_rating'] = 0;
        }

        $total_reviews = Review::whereCourseId($data['course']->id)->count();
        if ($data['total_reviews'] > 0) {
            $data['five_star_percentage'] =  100 * ($data['five_star_count'] / $total_reviews);
            $data['four_star_percentage'] =  100 * ($data['four_star_count'] / $total_reviews);
            $data['three_star_percentage'] = 100 * ($data['three_star_count'] / $total_reviews);
            $data['two_star_percentage'] = 100 * ($data['two_star_count'] / $total_reviews);
            $data['first_star_percentage'] = 100 * ($data['first_star_count'] / $total_reviews);
        } else {
            $data['five_star_percentage'] =  0;
            $data['four_star_percentage'] =  0;
            $data['three_star_percentage'] = 0;
            $data['two_star_percentage'] = 0;
            $data['first_star_percentage'] = 0;
        }
        //End:: Course Review

        $data['discussions'] = Discussion::whereCourseId($data['course']->id)->whereNull('parent_id')->active()->get();

        // Start:: Check student enrolled or cart list or nothing
        $user = Auth::user();
        $data['course_exits'] = 0;
      
        if ($user) {
            if($courseType == 3){       // Live(Standard Course)
              
                $courseIds = Enrollment::where(['user_id' => $user->id, 'course_id' => $data['course']->id, 'status' => ACCESS_PERIOD_ACTIVE])->pluck('course_id')->count();
               
            }
            else{       // Self Learning Course
                $courseIds = Enrollment::where(['user_id' => $user->id, 'course_id' => $data['course']->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->pluck('course_id')->count();
             
            }
          
        
            if ($courseIds) {
                $data['course_exits'] = 'enrolled';
            }
       
            // End:: Checking enrolled or not

            // Start:: Checking Cart list or not
            // $cartExists = CartManagement::whereUserId($user->id)->whereCourseId($data['course']->id)->first();
            $cartExists = CartManagement::whereUserId($user->id)->whereCourseId($data['course']->id)
            ->where('created_by_type',1)->first();
            if ($cartExists) {
                $data['course_exits'] = 'cartList';
            }
        
            // End:: Checking Cart list or not
        }
        // End:: Check student enrolled or cart list or nothing

        //Start:: Instructor rating
        $data['instructor_average_rating'] = getUserAverageRating($data['course']->user_id);
        //End:: Instructor rating

        //Start:: Instructor students
        $instructorCourseIds = Course::where('private_mode', '!=', 1)->publicCatalog()->where('user_id', $data['course']->user_id)->pluck('id')->toArray();

        $data['total_instructor_students'] = Enrollment::whereIn('course_id', $instructorCourseIds)->count();
        //End:: Instructor students

        //Start:: Course studentsallCourses
        $data['countries'] = Country::all();
        $data['total_course_students'] = Enrollment::where('course_id', $data['course']->id)->count();

        $timezones_array = timezones_array();
        $data['timeZonesArray'] = $timezones_array;
        // dd($data['timeZonesArray']);
        //End:: Course students
        $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->where('average_rating','!=',0.00)->active()->latest()->take(3)->get();
         
   
      
        if($courseType == 3){
          $data['courseType'] = $courseType;
          $data['typeCourse'] = 1;
    
            $resources_array = array();
            $category_ids_array = array();
            $course_id = $data['course']->id;
            $getCourseResources =  CourseResource::where('course_id',$course_id)->where('resource_id','<>',null)->get();
            if(!$getCourseResources->isEmpty()){
                foreach ($getCourseResources as $key => $course_resource) {
                    $resources_array[] = $course_resource->resource_id;
                }
            }
            $resources_array = array_unique($resources_array);
            if(!empty($resources_array)){
                $getResourceCategories = Resource::whereIn('id',$resources_array)->get();
                if(!$getResourceCategories->isEmpty()){
                    foreach ($getResourceCategories as $key => $resource) {
                        $category_ids_array[] = $resource->category_id;
                    }
                }
            }


            $result_data = array();
            $course_content_list_section = array();
            $category_ids_array = array_unique($category_ids_array);
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            if(!$getCategories->isEmpty()){
                foreach ($getCategories as $key => $value) {
                    $resource_array = array();
                    $categoryID = $value->id;
                    $resources = CourseResource::where('course_id',$course_id)
                    ->whereHas('resource', function ($query) use ($categoryID) {
                        $query->where('category_id', $categoryID);
                    })->get();
                    if(!$resources->isEmpty()){
                        foreach ($resources as $key => $value2) {
                            if($value2->resource?->type == 'Video' || $value2->resource?->type == 'video'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'Youtube' || $value2->resource?->type == 'youtube'){
                                if (Str::contains($value2->resource?->youtube_url_path, 'https://www.youtube.com')) {
                                     $preview_src = $value2->resource?->youtube_url_path;    
                                 }
                                 else if(Str::contains($value2->resource?->youtube_url_path, 'https://youtu.be')){
                                     $preview_src = $value2->resource?->youtube_url_path;    
                                 }
                                 else{
                                     $preview_src = "https://www.youtube.com/embed/".$value2->resource?->youtube_url_path;
                                 }
                            }
                            elseif($value2->resource?->type == 'Text' || $value2->resource?->type == 'text'){
                                $preview_src =  $value2->resource?->text;
                            }
                            elseif($value2->resource?->type == 'Image' || $value2->resource?->type == 'image'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'PDF' || $value2->resource?->type == 'pdf'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'Slide Document' || $value2->resource?->type == 'slide document'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name.'/index.html';
                            }
                            elseif($value2->resource?->type == 'Audio' || $value2->resource?->type == 'audio'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            else{
                                $preview_src = null;
                            }
                            $resource_array[] = [
                                'resource_id'=>$value2->resource->id,
                                'resource_name'=>$value2->resource->name,
                                'resource_ext'=>$value2->resource->file_extension,
                                'resource_path'=>$value2->resource->file_name,
                                'resource_type'=>$value2->resource->type,
                                'resource_preview_src'=>$preview_src,
                            ];
                        }
                    }
                    $result_data[] = [
                        'category_id' => $categoryID,
                        'category_name' => $value->name,
                        'category_resources' => $resource_array,
                    ];
                }
            }
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            // $categories = $getCategories;
            $categories_with_resources = $result_data;
            foreach ($categories_with_resources as $key => $category) {
                $category_resources_array = array();
                foreach ($category['category_resources'] as $key2=> $resource) {
                    if ($resource['resource_type'] == 'Text' || $resource['resource_type'] == 'Youtube'){
                        $redirectSRC =  $resource['resource_preview_src']; 
                    }
                    else{
                        $redirectSRC =  $resource['resource_preview_src'];
                    }
                    $category_resources_array[] = [
                        'sr_no' => $key2 + 1,
                        'id' => $resource['resource_id'],
                        'name' => $resource['resource_name'],
                        'type' => $resource['resource_type'],
                        'preview_src' => $redirectSRC,
                    ];
                }
                $course_content_list_section[]=[
                        'sr_no' => $key+1,
                        'name' => $category['category_name'],
                        'category_resources' => $category_resources_array,
                ];
            }
            $data['course_content_list_section'] = $course_content_list_section;
            // return view('frontend.course.standard-course-details', $data);
            return view('frontend.course.course-details', $data);
        }
        else{
          
            $data['courseType'] = $courseType;
            $data['typeCourse'] = 2;
            return view('frontend.course.course-details', $data);
        }
     
    }





    public function liveCourseEnrollmentForm(Request $request, $type = null , $slug = null, $user_id = null)
    {
     
        $form1Data = $request->session()->get('form1Data', []);
        $data['form1Data'] = $form1Data;
     
        $data['pageTitle'] = __('Live Course Enrollment Form');
        $data['title'] = __('Live Course Enrollment Form');
        $data['countries'] = Country::all();
        // Get both live (type 3) and self-learning (type 2) courses
        $data['courses'] = Course::whereIn('course_type', [1,2, 3])
                                ->where('status', 1)
                                ->orderBy('id', 'DESC')
                                ->get();
       
        $coursesQuery = Course::whereIn('course_type', [1, 2, 3])
            ->where('status', 1)
            ->orderBy('id', 'DESC');

            // If type is provided, filter courses
        if (!is_null($type)) {
            if($type == 1){   // live
                $coursesQuery->where('course_type', 3);
            }
            else{
                $coursesQuery->whereIn('course_type', [1,2]);
            }
        }
        $courses = $coursesQuery->get();
        $data['courses'] = $courses;
      
        // If slug is provided, find the matching course
        $selectedCourse = null;
        if (!is_null($slug)) {
            $selectedCourse = Course::where('slug', $slug)->first();
        }
        $data['selectedCourse'] = $selectedCourse;
        $data['type'] = $type;
        $data['slug'] = $slug;
        $timezones_array = timezones_array();
        $data['timeZonesArray'] = $timezones_array;

         // Get available time slots (next 30 days)
         $startDate = now();
         $endDate = now()->addDays(30);

     
         $availableSlots = TimeSlot::where('is_active', true)
         ->orderBy('start_time')
         ->get();
   
       
         $getStudent = Student::where('user_id',$user_id)->first();
         if($getStudent){
            $data['student'] = $getStudent;
         }
         else{
            $data['student'] = null;
         }
         $data['availableSlots'] = $availableSlots;
       
        return view('frontend.auth.live_course_enrollment_form',$data); 
     
        // return view('auth.sign-up-as-instructor', $data);
    }




    public function saveLiveRequestEnrollment(Request $request)
    {
    
        // Form validation
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string',
            'age' => 'required|numeric',
            'preferDate' => 'required|date',
            'preferSlot' => 'required|numeric',
            'slug' => 'string|nullable',
            'learning_goals' => 'required',
        ], 
        [
            'fullname.required' => 'Your full name is required.',
            'phone.required' => 'Your phone number is required.',
            'age.required' => 'Age Field is required.',
            'age.numeric' => 'Age Field Must Be Numeric.',
            'learning_goals.required' => 'Learning Goals is required.',
            'preferDate.required' => 'Please select calender date.',
            'preferDate.date' => 'Please select a valid date.',
            'preferSlot.required' => 'Please select time slot.',
            'preferSlot.numeric' => 'Please choose a valid slot.',
        ]);

        try {
        
            DB::beginTransaction();  // Start transaction to ensure atomicity
        
            $userID = $request->userID;
            $studyType = $request->preCourseType;
            $courseSlug = $request->preCourseSlug;
            $slugUpdated = $courseSlug;

            $user = User::where('id',$userID)->first();
            if($user == null){
                $this->showToastrMessage('error', __('User Does Not Exists'));
                return redirect()->back();
            }
         
            // Extract first and last name from fullname
            $nameParts = explode(' ', $request->input('fullname'), 2);
            $fname = $nameParts[0];
            $lname = $nameParts[1] ?? '';

            $preferDateInput = $request->input('preferDate'); // Example: "March 2025 25"
            // $formattedPreferDate = $preferDateInput ? Carbon::createFromFormat('F Y d', $preferDateInput)->format('Y-m-d') : null;

            $areaCode2 = $request->input('area_code_2');
            // If the value doesn't start with '+', prepend it
            if (!str_starts_with($areaCode2, '+')) {
                $areaCode2 = '+' . ltrim($areaCode2, '+'); // ltrim removes any existing + just in case
            }
            $PhoneNumber = $request->input('phone');

            // Case 4: Already has + and area code (+923125577435) - do nothing
            if (str_starts_with($PhoneNumber, $areaCode2)) {
                // Format is correct, no changes needed
            }
            // Case 3: Has area code without + (923125577435) - just add +
            elseif (str_starts_with($PhoneNumber, ltrim($areaCode2, '+'))) {
                $PhoneNumber = '+' . $PhoneNumber;
            }
            // Case 2: Starts with 0 (03125577435) - remove 0 and add area code
            elseif (str_starts_with($PhoneNumber, '0')) {
                $PhoneNumber = $areaCode2 . substr($PhoneNumber, 1);
            }
            // Case 1: Just local number (3125577435) - add full area code
            else {
                $PhoneNumber = $areaCode2 . $PhoneNumber;
            }

            $checkPhone = User::where('id','!=',$userID)->where('mobile_number',$PhoneNumber)->first();
       
            if($checkPhone){
                $this->showToastrMessage('error', __('Given WhatsApp Number Already Exists For Another User'));
                return redirect()->back();
            
            }
        
            $course = Course::where('slug', $courseSlug)->first();
              
            if($course){
                $course_id = $course->id;
            }
            else{
                $this->showToastrMessage('error', __('Given Course Does Not  Exists'));
                return redirect()->back();
            }

            // Create new User
            $user->name = $request->input('fullname');
            $user->area_code = $areaCode2;
            $user->mobile_number = $PhoneNumber;
            $user->save();


            $study_type = $studyType; // 1 for live, 2 for self-learning
            $type = $study_type;
        
            $getStudent = Student::where('user_id',$userID)->first();
            $getStudent->first_name = $fname;
            $getStudent->last_name = $lname;
            $getStudent->phone_number = $user->mobile_number;
            $getStudent->type = $type;
            $getStudent->age = $request->input('age');
            $getStudent->learning_goals = $request->input('learning_goals');
            $getStudent->save();
            $getStudent = Student::where('user_id',$userID)->first();
        
            $getCourse = Course::where('id',$course_id)->first();
            if($getCourse){
                $courseTITLE = $getCourse->title;
            }
            else{
                $courseTITLE = 'NONE';
            }
            $getTimeSlotInfo = TimeSlot::where('id',$request->preferSlot)->first();
           
            $preferTime = $getTimeSlotInfo->start_time;
            TrialClass::create([
                'student_id' => $getStudent->id,
                'prefer_date' => $preferDateInput, // Fixed date format
                'prefer_time' => $preferTime,
                'course_id' => $course_id,
            ]);

            BookedSlot::create([
                'time_slot_id' => $getTimeSlotInfo->id,
                'user_id' => $user->id,
                'status' => 'booked'
            ]);

          
            DB::commit();

            if($type == 1 || $type == 3){
                $template_name =  'student-sign-up-with-trial';
            }
            else{
                $template_name =  'student-sign-up'; 
            }
        
            
            sendEmailNotification([
                '{{USER_NAME}}' => $user->name,
                '{{COURSE}}' => $courseTITLE,
                // '{{EMAIL}}' => $user->email,
                // '{{PASSWORD}}' => $request->input('password'),
            ], $user->email, $template_name);

            // $this->send(__('Student Free Class Request'), 1, route('student.free_classes'), null, 1);
            $this->send(__('Student Enrollment Request For Live Course'), 1, route('student.free_classes'), null, 1);

            $getStudentSchedule = TrialClass::where('student_id',$getStudent->id)->where('course_id',$course_id)->first();
            if($getStudentSchedule){
                $studentSchedule =  date('d-m-Y',strtotime($getStudentSchedule->prefer_date)).' ( '.$getStudentSchedule->prefer_time. ')';
            }
            else{
                $studentSchedule = 'NONE';
            }
            $getAdminUsers = User::where('role',1)->get();
            if(!$getAdminUsers->isEmpty()){
                $template_name2 =  'student-enrollment-request'; 
                foreach ($getAdminUsers as $key => $getAdminUser) {
                    sendEmailNotification([
                        '{{NAME}}' => $user->name,
                        '{{AGE}}' => $getStudent->age,
                        '{{CONTACT}}' => $getStudent->phone_number,
                        '{{SCHEDULE}}' => $studentSchedule,
                        '{{COURSE}}' => $courseTITLE,
                        '{{GOALS}}' => $getStudent->learning_goals,
                    ], $getAdminUser->email, $template_name2);
                }      
            }

            $msg = __("Registration successful. Enrollment Request Sent To Admin. Wait For Approval");
            $this->showToastrMessage('success', __($msg));
            if($type && $slugUpdated){
                // $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'course-detail-page-live',$type,$slugUpdated);
                // return redirect()->to($redirectUrl);
                return redirect(route('course-details',['slug'=>$slugUpdated]));
            }
            else{
                // $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'main-page',$type,$slugUpdated);
                // return redirect()->to($redirectUrl);
                return redirect()->route('main.index');
            }
        
     
            // return redirect()->to($redirectUrl);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback transaction on error
            dd('Live Course Request Enrollment Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            // Log the error for debugging
            Log::error('Live Course Request Enrollment Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors(__('An unexpected error occurred. Please try again later.'));
        }
    }

    /**
     * JSON API version of saveLiveRequestEnrollment – called from the
     * React front-end popup instead of the Blade form.
     */
    public function saveLiveEnrollmentRequestApi(Request $request)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($bearer);
        if (!$accessToken) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        $user = $accessToken->tokenable;
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string',
            'age' => 'required|numeric',
            'preferDate' => 'required|date',
            'preferSlot' => 'required|numeric',
            'slug' => 'required|string',
            'learning_goals' => 'required',
        ], [
            'fullname.required' => 'Your full name is required.',
            'phone.required' => 'Your phone number is required.',
            'age.required' => 'Age field is required.',
            'age.numeric' => 'Age field must be numeric.',
            'learning_goals.required' => 'Learning Goals is required.',
            'preferDate.required' => 'Please select a calendar date.',
            'preferDate.date' => 'Please select a valid date.',
            'preferSlot.required' => 'Please select a time slot.',
            'preferSlot.numeric' => 'Please choose a valid slot.',
        ]);

        try {
            DB::beginTransaction();

            $userID = $user->id;
            $courseSlug = $request->input('slug');

            $nameParts = explode(' ', $request->input('fullname'), 2);
            $fname = $nameParts[0];
            $lname = $nameParts[1] ?? '';

            $preferDateInput = $request->input('preferDate');

            $areaCode2 = $request->input('area_code_2', '+1');
            if (!str_starts_with($areaCode2, '+')) {
                $areaCode2 = '+' . ltrim($areaCode2, '+');
            }
            $PhoneNumber = $request->input('phone');

            if (str_starts_with($PhoneNumber, $areaCode2)) {
                // already correct
            } elseif (str_starts_with($PhoneNumber, ltrim($areaCode2, '+'))) {
                $PhoneNumber = '+' . $PhoneNumber;
            } elseif (str_starts_with($PhoneNumber, '0')) {
                $PhoneNumber = $areaCode2 . substr($PhoneNumber, 1);
            } else {
                $PhoneNumber = $areaCode2 . $PhoneNumber;
            }

            $checkPhone = User::where('id', '!=', $userID)->where('mobile_number', $PhoneNumber)->first();
            if ($checkPhone) {
                return response()->json(['status' => false, 'message' => 'Given WhatsApp number already exists for another user.'], 422);
            }

            $course = Course::where('slug', $courseSlug)->first();
            if (!$course) {
                return response()->json(['status' => false, 'message' => 'Course not found.'], 404);
            }
            $course_id = $course->id;

            $user->name = $request->input('fullname');
            $user->area_code = $areaCode2;
            $user->mobile_number = $PhoneNumber;
            $user->save();

            $studyType = 1; // live
            $getStudent = Student::where('user_id', $userID)->first();
            if (!$getStudent) {
                return response()->json(['status' => false, 'message' => 'Student record not found.'], 404);
            }
            $getStudent->first_name = $fname;
            $getStudent->last_name = $lname;
            $getStudent->phone_number = $user->mobile_number;
            $getStudent->type = $studyType;
            $getStudent->age = $request->input('age');
            $getStudent->learning_goals = $request->input('learning_goals');
            $getStudent->save();
            $getStudent = Student::where('user_id', $userID)->first();

            $courseTITLE = $course->title ?? 'NONE';

            $getTimeSlotInfo = TimeSlot::where('id', $request->preferSlot)->first();
            if (!$getTimeSlotInfo) {
                return response()->json(['status' => false, 'message' => 'Invalid time slot.'], 422);
            }

            $preferTime = $getTimeSlotInfo->start_time;
            TrialClass::create([
                'student_id' => $getStudent->id,
                'prefer_date' => $preferDateInput,
                'prefer_time' => $preferTime,
                'course_id' => $course_id,
            ]);

            BookedSlot::firstOrCreate(
                ['time_slot_id' => $getTimeSlotInfo->id, 'user_id' => $user->id],
                ['status' => 'booked'],
            );

            DB::commit();

            if (app()->environment('production')) {
                $template_name = 'student-sign-up-with-trial';

                sendEmailNotification([
                    '{{USER_NAME}}' => $user->name,
                    '{{COURSE}}' => $courseTITLE,
                ], $user->email, $template_name);

                $this->send(__('Student Enrollment Request For Live Course'), 1, route('student.free_classes'), null, 1);

                $getStudentSchedule = TrialClass::where('student_id', $getStudent->id)->where('course_id', $course_id)->first();
                $studentSchedule = $getStudentSchedule
                    ? date('d-m-Y', strtotime($getStudentSchedule->prefer_date)) . ' ( ' . $getStudentSchedule->prefer_time . ' )'
                    : 'NONE';

                $getAdminUsers = User::where('role', 1)->get();
                if (!$getAdminUsers->isEmpty()) {
                    $template_name2 = 'student-enrollment-request';
                    foreach ($getAdminUsers as $getAdminUser) {
                        sendEmailNotification([
                            '{{NAME}}' => $user->name,
                            '{{AGE}}' => $getStudent->age,
                            '{{CONTACT}}' => $getStudent->phone_number,
                            '{{SCHEDULE}}' => $studentSchedule,
                            '{{COURSE}}' => $courseTITLE,
                            '{{GOALS}}' => $getStudent->learning_goals,
                        ], $getAdminUser->email, $template_name2);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Enrollment request sent to admin. Wait for approval.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Live Enrollment Request API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }









    

    public function enrollCourse(Request $request)
    {
        if (!Auth::check()) {
            $response['msg'] = __("You need to login first!");
            $response['status'] = 302;
            session(['type' => '1']);
            return response()->json($response);
        }
        // dd($request->course_id);
        if ($request->course_id) {
            $courseExists = Enrollment::where(['user_id' => Auth::user()->id, 'course_id' => $request->course_id, 'status' => ACCESS_PERIOD_ACTIVE])->first();
        }
        DB::beginTransaction();
        try {
            if ($courseExists) {
                $response['msg'] = __("Live Course Already Enrolled!");
                $response['status'] = 302;
                DB::rollBack();
                return response()->json($response);
            }
            if ($request->course_id) {
                $courseExits = Course::find($request->course_id);
                if (!$courseExits) {
                    $response['msg'] = __("Course not found!");
                    $response['status'] = 302;
                    DB::rollBack();
                    return response()->json($response);
                }


                $enrollment = Enrollment::where('user_id',Auth::user()->id)->where('course_id',$request->course_id)->orderBy('id','DESC')->first();
                if($enrollment){
                    $enrollment->order_id = 0;
                    $enrollment->user_id = Auth::user()->id;
                    $enrollment->course_id = $request->course_id;
                    $enrollment->owner_user_id = $courseExits->user_id ?? null;
                    $enrollment->course_type = 2;
                    // $enrollment->start_date = date("Y-m-d H:i:s");
                    $enrollment->start_date = now();
                    $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                    $enrollment->save();
                }
                else{
                    $enrollment = new Enrollment();
                    $enrollment->order_id = 0;
                    $enrollment->user_id = Auth::user()->id;
                    $enrollment->course_id = $request->course_id;
                    $enrollment->owner_user_id = $courseExits->user_id ?? null;
                    $enrollment->course_type = 2;
                    // $enrollment->start_date = date("Y-m-d H:i:s");
                    $enrollment->start_date = now();
                    $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                    $enrollment->save();
                    Log::info("NEW ENROLLMENT BEING CREATED IN (enrollCourse) IN FRONTEND/COURSECONTROLLER AT ".Carbon::now());
                    Log::info("NEW ENROLLMENT DETAILS IN (enrollCourse) IN FRONTEND/COURSECONTROLLER:- ".$enrollment);
                }
               
            }
            $response['quantity'] = Enrollment::where(['user_id' => Auth::user()->id,'status' => ACCESS_PERIOD_ACTIVE])->count();
            $response['msg'] = __("Live Course Enrolled");
            $response['msgInfoChange'] = __("Live Course Enrolled");
            $response['status'] = 200;
            DB::commit();
            return response()->json($response);
        } catch (\Throwable $th) {
            DB::rollBack();
            $response['msg'] = __("Something is wrong! Try again.");
            $response['status'] = 402;
            return response()->json($response);
        }
      
      

    }
  

 
 
    public function paginationFetchData(Request $request)
    {
        $data['courses'] = $this->filterCourseData($request);
        if ($request->ajax()) {
            $data['courses'] = $data['courses']->paginate($this->coursePaginateValue);
            return view('frontend.course.render-course-list')->with($data);
        }
    }

    public function filterCourseData($request)
    {
        if ($request->category_id) {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($request->category_id)->active();
        } elseif ($request->sub_category_id) {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereSubcategoryId($request->sub_category_id)->active();
        } else {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->active();
        }

        $subCategoryIds = $request->subCategoryIds ?? [];
        $difficultyLevelIds = $request->difficultyLevelIds ?? [];
        $ratingIds = $request->ratingIds ?? [];
        $min_price = (int)$request->min_price;
        $max_price = (int)$request->max_price;
        $learnerAccessibilityTypes = $request->learnerAccessibilityTypes  ?? [];
        $durationIds = $request->durationIds ?? [];
        $sortBy_id = $request->sortBy_id;

        $data['courses'] = $data['courses']->where(function ($q) use ($subCategoryIds, $difficultyLevelIds, $ratingIds, $min_price, $max_price, $learnerAccessibilityTypes) {

            if (!empty($subCategoryIds)) {
                $q->whereIn('subcategory_id', $subCategoryIds);
            }

            if (!empty($difficultyLevelIds)) {
                $q->whereIn('difficulty_level_id', $difficultyLevelIds);
            }
            if ($ratingIds) {
                foreach ($ratingIds as $rating) {
                    $q->where('average_rating', '>=', $rating);
                }
            }

            if ($min_price && $max_price) {
                $q->whereBetween('price', [$min_price, $max_price]);
            } else if ($max_price) {
                $q->whereBetween('price', [0, $max_price]);
            }

            if (!empty($learnerAccessibilityTypes)) {
                $q->whereIn('learner_accessibility', $learnerAccessibilityTypes);
            }
        });

        /*
         * duration_id = 1 // less than 24 hours
         * duration_id = 2 // 24 to 36 hours
         * duration_id = 3 // 36 to 72 hours
         * duration_id = 4 // above 72 hours
         */

        if ($durationIds) {
            $courses = $data['courses']->get();
            $courseIds = collect([]);
            foreach ($courses as $course) {
                $duration = $course->filter_video_duration;
                foreach ($durationIds as $durationId) {
                    if ($durationId == 1) {
                        if ($duration <= 24) {
                            $courseIds->push($course->id);
                        }
                    } elseif ($durationId == 2) {
                        if ($duration >= 24 && $duration <= 36) {
                            $courseIds->push($course->id);
                        }
                    } elseif ($durationId == 3) {
                        if ($duration >= 36 && $duration <= 72) {
                            $courseIds->push($course->id);
                        }
                    } elseif ($durationId == 4) {
                        if ($duration >= 72) {
                            $courseIds->push($course->id);
                        }
                    }
                }
            }

            $data['courses'] = $data['courses']->whereIn('id', $courseIds);
        }

        if ($sortBy_id) {
            if ($sortBy_id == 2) {
                $data['courses'] = $data['courses']->orderBy('id', 'DESC');
            }
            if ($sortBy_id == 3) {
                $data['courses'] = $data['courses']->orderBy('id', 'ASC');
            }
        }

        return $data['courses'];
    }






    public function filterCourseDataNew($request)
    {
        if ($request->categoryId) {
            // $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($request->category_id)->active();
               $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($request->categoryId)->active();
        } elseif ($request->sub_category_id) {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereSubcategoryId($request->sub_category_id)->active();
        } else {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->active();
        }

        $categoryId = $request->categoryId ?? null;
        $difficultyLevelId = $request->difficultyLevelId ?? null;
        $ratingId = $request->ratingId ?? null;
        // $min_price = (int)$request->min_price;
        // $max_price = (int)$request->max_price;
        $learnerAccessibilityType = $request->learnerAccessibilityType  ?? null;
        $durationId = $request->durationId ?? null;
        $instructorId = $request->instructorId ?? null;
        $language = $request->language ?? null;
        $sortBy_id = $request->sortBy_id;

        $data['courses'] = $data['courses']->where(function ($q) use ($categoryId,$instructorId, $difficultyLevelId, $ratingId, $learnerAccessibilityType) {

            if ($categoryId) {
                $q->where('category_id', $categoryId);
            }

            if ($difficultyLevelId) {
                $q->where('difficulty_level_id', $difficultyLevelId);
            }
            if ($ratingId) {
                    $q->where('average_rating', '>=', $ratingId);
            }

            // if ($min_price && $max_price) {
            //     $q->whereBetween('price', [$min_price, $max_price]);
            // } else if ($max_price) {
            //     $q->whereBetween('price', [0, $max_price]);
            // }

            if ($learnerAccessibilityType) {
                if($learnerAccessibilityType == 'free'){
                    $q->where('price', 0);
                }
                else{
                    $q->where('price','>', 0); 
                }
                $q->where('learner_accessibility', $learnerAccessibilityType);
            }

            if ($instructorId) {
                $q->whereHas('course_instructors', function ($query2) use ($instructorId) {
                    $query2->where('instructor_id', $instructorId);
                });
            }


        });

        /*
         * duration_id = 1 // less than 24 hours
         * duration_id = 2 // 24 to 36 hours
         * duration_id = 3 // 36 to 72 hours
         * duration_id = 4 // above 72 hours
         */

        if ($durationId) {
            $courses = $data['courses']->get();
            $courseIds = collect([]);
            foreach ($courses as $course) {
                // $duration = $course->filter_video_duration;
                $duration = $course->duration_in_hours;
                if($duration){
                    if ($durationId == 1) { // Less than 24 Hours
                        if ($duration <= 24) {
                            $courseIds->push($course->id);
                        }
                    } elseif ($durationId == 2) { // 24 Hour to 36
                        if ($duration >= 24 && $duration <= 36) {
                            $courseIds->push($course->id);
                        }
                    } elseif ($durationId == 3) { // 36 Hour to 72
                        if ($duration >= 36 && $duration <= 72) {
                            $courseIds->push($course->id);
                        }
                    } elseif ($durationId == 4) { // 72 +
                        if ($duration >= 72) {
                            $courseIds->push($course->id);
                        }
                    }
                }
                   
            }

            $data['courses'] = $data['courses']->whereIn('id', $courseIds);
        }

        if ($sortBy_id) {
            if ($sortBy_id == 2) {
                $data['courses'] = $data['courses']->orderBy('id', 'DESC');
            }
            if ($sortBy_id == 3) {
                $data['courses'] = $data['courses']->orderBy('id', 'ASC');
            }
        }

        return $data['courses'];
    }

    public function reviewPaginate(Request $request, $courseId)
    {
        $data['reviews'] = Review::whereCourseId($courseId)->latest()->paginate(3);
        $response['appendReviews'] = View::make('frontend.course.partial.render-partial-review-list', $data)->render();
        $response['reviews'] = Review::whereCourseId($courseId)->latest()->paginate(3);
        return response()->json($response);
    }


     /**
     * Fetch course details by slug
     */
    public function getCourseDetails($slug)
    {
        // Find course by slug
        $course = Course::where('slug', $slug)->first();

        // If not found, return error
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        // Return course data
        return response()->json([
            'success' => true,
            'id'      => $course->id,
            'title'   => $course->title,
            'slug'    => $course->slug,
        ]);
    }


}
