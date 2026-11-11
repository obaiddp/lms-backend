<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AboutUsGeneral;
use App\Models\Assignment;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\AffiliateRequest;
use App\Models\TimeSlot;
use App\Models\Language;

use App\Models\Discussion;
use Illuminate\Support\Facades\Mail;
use App\Models\City;
use App\Models\ClientLogo;
use App\Models\Country;
use App\Models\ClassDetail;
use App\Models\Course;
use App\Models\Course_lecture;
use App\Models\Difficulty_level;
use App\Models\Enrollment;
use Illuminate\Support\Facades\App;
use App\Models\Exam;
use App\Models\FaqQuestion;
use App\Models\Home;
use App\Models\InstructorSupport;
use App\Models\Package;
use App\Models\Review;
use App\Models\State;
use App\Models\User;
use App\Models\Skill;

use App\Models\UserPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiStatusTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Student_certificate;
use App\Models\Certificate;
use App\Models\Certificate_by_instructor;
use App\Models\Order;
use App\Models\Addon\Product\ProductReview;
use App\Models\Order_item;
use App\Models\CourseResource;
use App\Models\CartManagement;
use App\Models\Student;
use App\Models\Course_lecture_views;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Addon\Product\ProductCategory;
use App\Models\Addon\Product\ProductTag;

use function Ramsey\Uuid\v1;

class HomeController extends Controller
{
    use ApiStatusTrait;


    public function homeApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }

          
        $data['pageTitle'] = "Home";
        $data['metaData'] = staticMeta(2);
        $data['categories'] = Category::with('subcategories')->active()->get();

            $lastPage = false;
            $trendingCoursesData = array();

            $trendingCoursesArray = Enrollment::query()
            // ->whereMonth('created_at', now()->month)
            ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->orderBy('total','desc')
            ->limit(5)
            ->get()
            ->pluck('course_id')
            ->filter() // Remove null or empty values
            ->toArray();
            
            if (!empty($trendingCoursesArray)) {
                $trendingCourses = Course::whereIn('id',$trendingCoursesArray)
                ->orderByRaw('FIELD(id, ' . implode(',', $trendingCoursesArray) . ')')->get();
            }
            else{
                $trendingCourses = collect();
            }


            $latestCourses = Course::limit(10)
            ->orderBy('id','desc')
            ->get();
          
        
            $banner = Home::first();
         
            $bannerData = null;
            $categoriesData = array();
        
            $latestCoursesData = array();
            if($banner){
               
             
                $bannerData = [
                    'id' => $banner->id,
                    'title' => trim(($banner->banner_first_line_title ?? '') . ' ' . ($banner->banner_second_line_title ?? '') . ' ' . ($banner->banner_third_line_title ?? '')),
                    'image' => getImageFile($banner->banner_image_url),
                    // 'image' => getImageFile($bannerImage),
                    'link' => $banner->banner_second_button_link,
                ];
            }
            if(!$data['categories']->isEmpty()){
                foreach ($data['categories'] as $key => $cat) {
                    $category_subcategories_3 = array();
                    if(count($cat->subcategories) > 0){
                        foreach ($cat->subcategories as $key => $subcategory) {
                            $category_subcategories_3[]=[
                                'id' => $subcategory->id,
                                'name' => $subcategory->name,
                                'image' => getImageFile($subcategory->cover_image_path),
                            ];
                        }
                    }

                    $categoriesData[] = [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'image' => getImageFile($cat->image_path),
                        'sub_categories' => $category_subcategories_3,
                    ];
                }
            }
            if(!$trendingCourses->isEmpty()){
                foreach ($trendingCourses as $key => $course) {  
                    $trendingCoursesData[] = [
                        'id' => $course->id,
                        'language' => $course->language?->name,
                        'slug' => $course->slug,
                        'title' => $course->title,
                        'average_rating' => $course->average_rating,
                        'image' => getImageFile($course->image),
                        'subtitle' => $course->subtitle,
                        'price' => number_format((float) $course->price, fmod($course->price, 1) == 0 ? 0 : 2).' '.get_currency_symbol(),
                        'no_of_classes' => $course->classes > 0 ? @$course->classes : '0'
                    ];
                }
                // $trendingCoursesData[] = [
                //     'id' => 28,
                //     'title' => "Test Course",
                //     'slug' => "test-course",
                //     'image' => "https://cefonlineacademy.com/uploads/course/1736594924-zEDvZC1HJi.jpg",
                // ];
            }


        // [MODULE: cef-backend] myCourses only when authenticated — guest/optional.auth must not touch $user->id
        $my_courses_data = array();
        if ($user) {
            $enrollmentsGeneral = Enrollment::where('enrollments.user_id', $user->id)->select('enrollments.*', 'order_items.unit_price')
            ->join('orders', 'orders.id', '=', 'enrollments.order_id')
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
            ->whereHas('course', function ($query) {
                $query->where('id','<>',null);
            })
            ->where('enrollments.course_type',1)
            ->orderBy('enrollments.course_id', 'ASC')
            ->orderBy('enrollments.id', 'DESC') // Ensure latest record is selected
            ->groupBy('enrollments.course_id')
            ->latest()->get();


            $enrollmentsLive = Enrollment::where('enrollments.user_id', $user->id)->select('enrollments.*')
            ->whereHas('course', function ($query) {
                $query->where('id','<>',null);
            })
            ->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
            ->where('enrollments.course_type',2)
            ->orderBy('enrollments.course_id', 'ASC')
            ->orderBy('enrollments.id', 'DESC') // Ensure latest record is selected
            ->groupBy('enrollments.course_id')
            ->latest()->get();



             if(!$enrollmentsGeneral->isEmpty()){
                foreach ($enrollmentsGeneral as $key => $value) {
                    if($value->course?->course_type == 3){
                        $courseType = __('Live');
                    }
                    else if($value->course?->course_type == 1){
                        $courseType = __('General');
                    }
                    else{
                        $courseType = __('Scorm');
                    }
                    // $check_progress = (int)round(studentCourseProgress($value->course->id, $value->id));
                    $check_progress = number_format(studentCourseProgressApi($value->course->id, $value->id,$user->id),2);
               
                    if(checkIfExpired($value)){
                        $actionRoute = route('my-course-show-student',['slug'=>$value->course?->slug]);
                    }
                    else{
                        $actionRoute = route('frontend-course-detail',['slug'=>$value->course?->slug]);
                    }
                    if($check_progress  != 100){ // On Going Course
                        $my_courses_data[] = [
                            'id' => $value->course?->id,
                            'type' =>  $courseType,
                            'language' => $value->course?->language?->name,
                            'slug' => $value->course?->slug,
                            'title' => $value->course?->title,
                            'average_rating' => $value->course?->average_rating,
                            'image' => getImageFile($value->course?->image),
                            'subtitle' => $value->course?->subtitle,
                            'price' => number_format((float) $value->course?->price, fmod($value->course?->price, 1) == 0 ? 0 : 2).' '.get_currency_symbol(),
                            'no_of_classes' => $value->course?->classes > 0 ? @$value->course?->classes : '0',
                            'progress'=>  number_format(studentCourseProgressApi($value->course?->id, $value->id,$user->id),2),
                            'actionRoute' => $actionRoute,
                        ];
                    }
                }
             }

               if(!$enrollmentsLive->isEmpty()){
                foreach ($enrollmentsLive as $key => $value) {
                    if($value->course?->course_type == 3){
                        $courseType = __('Live');
                    }
                    else if($value->course?->course_type == 1){
                        $courseType = __('General');
                    }
                    else{
                        $courseType = __('Scorm');
                    }
               
                    if(checkIfExpired($value)){
                        $actionRoute = route('my-course-show-student',['slug'=>$value->course?->slug]);
                    }
                    else{
                        $actionRoute = route('frontend-course-detail',['slug'=>$value->course?->slug]);
                    }
                        $my_courses_data[] = [
                            'id' => $value->course?->id,
                            'type' =>  $courseType,
                            'language' => $value->course?->language?->name,
                            'slug' => $value->course?->slug,
                            'title' => $value->course?->title,
                            'average_rating' => $value->course?->average_rating,
                            'image' => getImageFile($value->course?->image),
                            'subtitle' => $value->course?->subtitle,
                            'price' => number_format((float) $value->course?->price, fmod($value->course?->price, 1) == 0 ? 0 : 2).' '.get_currency_symbol(),
                            'no_of_classes' => $value->course?->classes > 0 ? @$value->course?->classes : '0',
                            'actionRoute' => $actionRoute,
                        ];
                }
             }
        }


        // if(!$latestCourses->isEmpty()){
        //         foreach ($latestCourses as $key => $course) {  
        //             if($course->course_type == 3){ // LIVE Course
        //                 $courseTitle1 = Str::limit($course->title.' (Live)', 80); 
        //             }
        //             else{ // General Course
        //                 $courseTitle1 = Str::limit($course->title, 40);
        //             }


        //     ////////////////////////////////////////////////////////
        //     if($course->learner_accessibility == 'paid'){
        //         $courseAccess = __('Paid');
        //         $startDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->start_date));
        //         $endDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->end_date));
        //         $percentage = $course->promotionCourse?->promotion?->percentage;
        //         $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
        //         if(now()->gt($startDate) && now()->lt($endDate)){
        //             if(get_currency_placement() == 'after'){
        //                 $coursePrice = $course->price.' '.get_currency_symbol();
        //             }
        //             else{
        //                 $coursePrice = get_currency_symbol().' '.$course->price; 
        //             }
        //         }
        //         elseif ($course->price <= $course->old_price){
        //             if(get_currency_placement() == 'after'){
        //                 $coursePrice = $course->price.' '.get_currency_symbol();
        //             }
        //             else{
        //                 $coursePrice = get_currency_symbol().' '.$course->price; 
        //             }
        //         }
        //         else{
        //             if(get_currency_placement() == 'after'){
        //                 $coursePrice = $course->price.' '.get_currency_symbol();
        //             }
        //             else{
        //                 $coursePrice = get_currency_symbol().' '.$course->price; 
        //             }
        //         }
        //     }
        //     elseif($course->learner_accessibility == 'free'){
        //         if(get_currency_placement() == 'after'){
        //             $coursePrice = number_format(0,2).' '.get_currency_symbol();
        //         }
        //         else{
        //             $coursePrice = get_currency_symbol().' '.number_format(0,2); 
        //         }
        //         // $coursePrice = null;
        //         $courseAccess = __('Free');
        //     }
        //     //////////////////////////////////////////////////////////

        //             $latestCoursesData[] = [
        //                 'id' => $course->id,
        //                 'title' => $courseTitle1,
        //                 'slug' => $course->slug,
        //                 'image' => getImageFile($course->image),
        //                 // 'price' => $course->price,
        //                 'price' => $coursePrice,
        //                 'user_name' => $course->user?->name,
        //                 'user_pic' => getImageFile($course->user?->image),
        //                 'category_name' => $course->category?->name,
        //                 'star_rating' => $course->average_rating,
        //                 'duration' => $course->access_period,
        //             ];
        //         }
        // }
            $result_array = [
                'banners' => $bannerData,
                'categories' => $categoriesData,
                'trendingCourses' => $trendingCoursesData,
                // 'latestCourses' => $latestCoursesData,
                'myCourses' => $my_courses_data,
                // 'filters_section_data_with_values' => $filters_section_data_with_values,
                // 'courses_section_data' => $courses_section_data,
            ];
            return response()->json($result_array);
     
        } catch (\Throwable $th) {
            // [MODULE: cef-backend] Never dd() on API — mobile expects JSON; log for ops
            Log::error('frontend/home failed', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
            return response()->json([
                'status' => 0,
                'message' => $th->getMessage(),
            ], 500);
        }
    }



    public function allCoursesApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }

        $data['pageTitle'] = "Courses";
        $data['metaData'] = staticMeta(2);
        $data['categories'] = Category::with('subcategories')->active()->get();

   
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
    
            $data['courses'] = $data['courses']->paginate(9);
            // $data['courses'] = $data['courses']->get();
            // $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->with('course_instructors.user.instructor.instructor_courses')->active()->paginate(9);
            if( $data['courses']->lastPage() == $request->page){
                $lastPage = true;
            }

            $data['lastPage'] = $lastPage;
            // $data['html'] = View::make('frontend.course.render-course-list-only', $data)->render();
            $data['status'] = true;
            $data['total_courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->active()->count();
            $data['difficulty_levels'] = Difficulty_level::all();
            $data['highest_price'] = Course::where('private_mode', '!=', 1)->publicCatalog()->max('price');
    
            $data['random_four_categories'] = Category::active()->inRandomOrder()->limit(4)->get();
            $filters_section_data_with_values = array();
            $people_also_search_section = array();
            $courses_section_data = array();
            $mainArray = array();
            $difficultyLevelIds = array();
         
            foreach($data['random_four_categories'] as $random_four_category){
                $people_also_search_section[] =[
                    'course_category_id' => $random_four_category->id,
                    'course_category_slug' => $random_four_category->slug,
                    'course_category_name' => $random_four_category->name,
                    'course_category_image' => getImageFile($random_four_category->image_path),
                    'course_category_details_web_route' => route('category-courses', $random_four_category->slug),
                    'course_category_details_api_route' => route('frontend-all-categorywise-courses'),
                ];
            }
            foreach ( $data['categories'] as $key=> $category){
                $subCategoriesArray = array();
                foreach($category->subcategories as $subCategoryKey => $subcategoryItem){
                    $subCategoriesArray[] =[
                        $subcategoryItem->id => $subcategoryItem->name,
                    ];
                }
                $mainArray[] =[
                    $category->name.'('.$category->id.')' => $subCategoriesArray,
                ];
            }
            foreach ( $data['difficulty_levels'] as $level){
                $difficultyLevelIds[] =[
                    $level->id => $level->name,
                ];
            }
            $filters_section_data_with_values = [
                'sortBy_id' => [
                    1 => 'Default',
                    2 => 'Newest Courses',
                    3 => 'Oldest Courses'
                ],
                'subCategoryIds' => $mainArray,
                'difficultyLevelIds' => $difficultyLevelIds,
                'ratingIds' => [
                    5 => __('5 star'),
                    4 => __('4 star or above'),
                    3 => __('3 star or above'),
                    2 => __('2 star or above'),
                    1 => __('1 star or above'),
                ],
                'min_price' => 0,
                'max_price' => $data['highest_price'] ? (int)$data['highest_price'] : 10000,
                'learnerAccessibilityTypes' => [
                    'free' => __('Free'),
                    'paid' => __('Paid'),
                ],
                'durationIds' => [
                    1 => __('Less Than 24 Hours'),
                    2 => __('24 to 36 Hours'),
                    3 => __('36 to 72 Hours'),
                    4 => __('Above 72 Hours'),
                ],
            ];
          
       
            foreach($data['courses'] as $course){
                if($course->status != STATUS_UPCOMING_APPROVED){
                    if (date('Y-m-d H:i:s', strtotime('-7 days')) >= $course->created_at){
                        if (in_array($course->id, $data['topCourse'])){
                            $courseTag = __('Best Seller');
                        }
                        else{
                            $courseTag = __('New course');
                        }
                    }
                    else{
                        $courseTag = null;
                    }
                    $special = $course->specialPromotionTagCourse?->specialPromotionTag?->name;
                    if($special){
                        $coursePromotionTag = $special;
                    }
                    else{
                        $coursePromotionTag = null; 
                    }
                }
                else{
                    $coursePromotionTag = null;  
                    $courseTag = __('Upcoming');
                }

                if($course->learner_accessibility == 'paid'){
                    $courseAccess = __('Paid');
                    $startDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->start_date));
                    $endDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->end_date));
                    $percentage = $course->promotionCourse?->promotion?->percentage;
                    $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
                    if(now()->gt($startDate) && now()->lt($endDate)){
                        if(get_currency_placement() == 'after'){
                            $courseDiscountPrice = $discount_price.' '.get_currency_symbol();
                        }
                        else{
                            $courseDiscountPrice = get_currency_symbol().' '.$discount_price; 
                        }
                        if(get_currency_placement() == 'after'){
                            // $coursePrice = $course->price.' '.get_currency_symbol();
                            $coursePrice = number_format((float) $course->price, fmod($course->price, 1) == 0 ? 0 : 2).' '.get_currency_symbol();
                        }
                        else{
                            // $coursePrice = get_currency_symbol().' '.$course->price; 
                            $coursePrice = get_currency_symbol().' '.number_format((float) $course->price, fmod($course->price, 1) == 0 ? 0 : 2);
                        }
                        $courseOldPrice = $coursePrice;
                    }
                    elseif ($course->price <= $course->old_price){
                        $courseDiscountPrice = null;
                        if(get_currency_placement() == 'after'){
                            // $coursePrice = $course->price.' '.get_currency_symbol();
                            $coursePrice = number_format((float) $course->price, fmod($course->price, 1) == 0 ? 0 : 2).' '.get_currency_symbol();
                        }
                        else{
                            // $coursePrice = get_currency_symbol().' '.$course->price; 
                            $coursePrice = get_currency_symbol().' '.number_format((float) $course->price, fmod($course->price, 1) == 0 ? 0 : 2);
                        }
                        if(get_currency_placement() == 'after'){
                            $courseOldPrice = $course->old_price.' '.get_currency_symbol();
                        }
                        else{
                            $courseOldPrice = get_currency_symbol().' '.$course->old_price; 
                        }
                    }
                    else{
                        $courseDiscountPrice = null;
                        if(get_currency_placement() == 'after'){
                            // $coursePrice = $course->price.' '.get_currency_symbol();
                              $coursePrice = number_format((float) $course->price, fmod($course->price, 1) == 0 ? 0 : 2).' '.get_currency_symbol();
                        }
                        else{
                            $coursePrice = get_currency_symbol().' '.$course->price; 
                              $coursePrice = get_currency_symbol().' '.number_format((float) $course->price, fmod($course->price, 1) == 0 ? 0 : 2);
                        }
                        $courseOldPrice = $coursePrice;
                    }
                }
                elseif($course->learner_accessibility == 'free'){
                    $coursePrice = null;
                    $courseOldPrice = null;
                    $courseDiscountPrice = null;
                    $courseAccess = __('Free');
                }

                if($course->course_type == 3){
                    $accessPeriod = (!$course->access_period) ? __('Full Lifetime Access') : $course->access_period;
                    $courseType = __('Live');
                    $courses_section_data[] =[
                        'course_id' => $course->id,
                        'course_tag' => $courseTag,
                        'course_promotion_tag' => $coursePromotionTag,
                        'course_slug' => $course->slug,
                        'course_title' => Str::limit($course->title.' (Live)', 80),
                        'course_type' => $courseType,
                        'course_image' => getImageFile($course->image_path),
                        'course_price' => $coursePrice,
                        'course_user_name' => $course->user?->name,
                        'course_user_pic' => getImageFile($course->user?->image),
                        'course_detail_web_route' => route('course-details', $course->slug),
                        // 'course_detail_api_route' => route('frontend-course-detail'),
                        'course_detail_api_route' => route('frontend-course-detail',$course->slug),
                        'course_average_rating' => $course->average_rating,
                        'course_duration' => $accessPeriod,
                        'course_average_rating_percent' => ($course->average_rating * 20).'%',
                        'course_total_reviews' => $course->reviews->count(),
                        'course_accessibility' => $courseAccess,
                    ];
                }
                elseif($course->course_type == 2 || $course->course_type == 1){
                    if($course->course_type == 2){
                        $courseType = __('Scorm');
                    }
                    else{
                        $courseType = __('General'); 
                    }

                   $accessPeriod = (!$course->access_period) ? __('Full Lifetime Access') : $course->access_period;
                    $courses_section_data[] =[
                        'course_id' => $course->id,
                        'course_tag' => $courseTag,
                        'course_promotion_tag' => $coursePromotionTag,
                        'course_slug' => $course->slug,
                        'course_title' => Str::limit($course->title, 40),
                        'course_type' => $courseType,
                        'course_image' => getImageFile($course->image_path),
                        'course_price' => $coursePrice,
                        'course_user_name' => $course->user?->name,
                        'course_user_pic' => getImageFile($course->user?->image),
                        // 'course_current_price' => $coursePrice,
                        // 'course_old_price' => $courseOldPrice,
                        // 'course_discount_price' => $courseDiscountPrice,
                        'course_detail_web_route' => route('course-details', $course->slug),
                        'course_detail_api_route' => route('frontend-course-detail',$course->slug),
                        'course_average_rating' => $course->average_rating,
                        'course_duration' => $accessPeriod,
                        'course_average_rating_percent' => ($course->average_rating * 20).'%',
                        'course_total_reviews' => $course->reviews->count(),
                      
                        'course_accessibility' => $courseAccess,
                    ];
                }
            }
            $result_array = [
                'people_also_search_section' => $people_also_search_section,
                'filters_section_data_with_values' => $filters_section_data_with_values,
                'courses_section_data' => $courses_section_data,
                'course_section_pagination_links' => [
                    'total' => $data['courses']->total(),
                    'count' => $data['courses']->count(),
                    'per_page' => $data['courses']->perPage(),
                    'current_page' => $data['courses']->currentPage(),
                    'total_pages' => $data['courses']->lastPage(),
                    'links' => [
                        'first' => $data['courses']->url(1), // URL for the first page
                        'last' => $data['courses']->url($data['courses']->lastPage()), // URL for the last page
                        'prev' => $data['courses']->previousPageUrl(), // URL for the previous page
                        'next' => $data['courses']->nextPageUrl(), // URL for the next page
                    ]
                ]
            ];
            return response()->json($result_array);
     
        } catch (\Throwable $th) {
            dd($th);
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }




    public function allProductsApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }

            $data['pageTitle'] = "All Products";
            $data['categories'] = ProductCategory::get();
            $data['tags'] = ProductTag::get();
    
                $products = Product::where('status', STATUS_SUCCESS);
           
                if($request->search_key != NULL && $request->search_key != ''){
                    $products->where('title', 'LIKE',"%{$request->search_key}%");
                }
                if($request->categories != NULL && is_array($request->categories)){
                 
                    $products->whereIn('product_category_id', $request->categories);
                }
                if($request->tags != NULL && is_array($request->tags)){
                    // $products->join('tag_products', 'tag_products.product_id', '=', 'products.id') // Correct alias
                    // ->whereIn('tag_products.product_tag_id', $request->tags);

                    $products->from('products as p')
                    ->select('p.*')
                    ->join('tag_products as tp', 'tp.product_id', '=', 'p.id')
                    ->whereIn('tp.product_tag_id', $request->tags)
                    ->orderBy('p.id', 'desc');
                 }
                 if($request->min_price >= 0){
                    $products->where('current_price', '>=' , $request->min_price ?? 0);
                }
                if($request->max_price >= 0){
                    $products->where('current_price', '<=' , $request->max_price ?? 0);
                }
                if($request->discount != NULL){
                    $products->where('discount_percentage', '>=', $request->discount);
                }
                if($request->rating != NULL){
                    $products->where('average_review', '>=', $request->rating);
                }
               
                if($request->sort_by == 1){
                    $products->orderBy('id', 'DESC');
                }else{
                    $products->orderBy('id', 'ASC');
                }
                $data['products'] = $products->paginate(9);
                // $data['products'] = $products->get();
                $data['highest_price'] = Product::where('status', STATUS_SUCCESS)->max('current_price');
                $filters_section_data_with_values = array();
                $products_section_data = array();

                foreach ( $data['categories'] as $category){
                    $categoriesArray[] =[
                        $category->id => $category->name,
                    ];
                }
                foreach ( $data['tags'] as $tag){
                    $tagsArray[] =[
                        $tag->id => $tag->name,
                    ];
                }
                $filters_section_data_with_values = [
                    'sort_by' => [
                        1 => 'Newest',
                        2 => 'Oldest'
                    ],
                    'categories' => $categoriesArray,
                    'tags' => $tagsArray,
                    'min_price' => 0,
                    'max_price' => 10000,
                    'discount' => [
                        0 => __('0% off or more'),
                        10 => __('10% off or more'),
                        25 => __('25% off or more'),
                        50 => __('50% off or more'),
                        70 => __('70% off or more'),
                    ],
                    'rating' => [
                        5 => __('5 star'),
                        4 => __('4 star or above'),
                        3 => __('3 star or above'),
                        2 => __('2 star or above'),
                        1 => __('1 star or above'),
                        0 => __('0 star or above'),
                    ],
                ];

                foreach ($data['products'] as $product){
                    $averate_percent = $product->average_review * 20;
                    $productReviewsCount = ProductReview::where('product_id',$product->id)->count();
                    if($product->old_price > $product->current_price){
                        if(get_currency_placement() == 'after'){
                            $productOldPrice= $product->old_price.' '.get_currency_symbol();
                        }
                        else{
                            $productOldPrice= get_currency_symbol().' '.$product->old_price;
                        }
                        if(get_currency_placement() == 'after'){
                            $productCurrentPrice= $product->current_price.' '.get_currency_symbol();
                        }
                        else{
                            $productCurrentPrice= get_currency_symbol().' '.$product->current_price;
                        }
                    }
                    else{
                        if(get_currency_placement() == 'after'){
                            $productCurrentPrice= $product->current_price.' '.get_currency_symbol();
                        }
                        else{
                            $productCurrentPrice= get_currency_symbol().' '.$product->current_price;
                        }
                        $productOldPrice = $productCurrentPrice;
                    }

                    if($user != null){
                        if($product->quantity > 0){
                            if($role == 3){
                                $addToCartProduct = route('student-cart-add');
                            }
                            else{
                                $addToCartProduct = null; 
                            }
                        }
                        else{
                            $addToCartProduct = __('Out of stock');
                        }
                    }
                    else{
                        $addToCartProduct = null; 
                    }
                   
                    if($addToCartProduct != null){
                        $products_section_data[] = [
                            'product_id' => $product->id,
                            'product_image' => getImageFile($product->thumbnail_path),
                            'product_title' => $product->title,
                            'product_slug' => $product->slug,
                            'product_view_web_route' => route('lms_product.frontend.view', $product->slug),
                            'product_view_api_route' => route('frontend-product-detail-view'),
                            'product_average_review' => number_format($product->average_review, 1),
                            'product_average_review_percent' => $averate_percent."%",
                            'product_total_reviews' => $productReviewsCount,
                            'product_old_price' => $productOldPrice,
                            'product_current_price' => $productCurrentPrice,
                            'product_action_btn_details' => $addToCartProduct,
                        ];
                    }
                    else{
                        $products_section_data[] = [
                            'product_id' => $product->id,
                            'product_image' => getImageFile($product->thumbnail_path),
                            'product_title' => $product->title,
                            'product_slug' => $product->slug,
                            'product_view_web_route' => route('lms_product.frontend.view', $product->slug),
                            'product_view_api_route' => route('frontend-product-detail-view'),
                            'product_average_review' => number_format($product->average_review, 1),
                            'product_average_review_percent' => $averate_percent."%",
                            'product_total_reviews' => $productReviewsCount,
                            'product_old_price' => $productOldPrice,
                            'product_current_price' => $productCurrentPrice,
                        ];
                    }
                  
                }
           
                $result_array = [
                    'filters_section_data_with_values' => $filters_section_data_with_values,
                    'products_section_data' => $products_section_data,
                    'product_section_pagination_links' => [
                        'total' => $data['products']->total(),
                        'count' => $data['products']->count(),
                        'per_page' => $data['products']->perPage(),
                        'current_page' => $data['products']->currentPage(),
                        'total_pages' => $data['products']->lastPage(),
                        'links' => [
                            'first' => $data['products']->url(1), // URL for the first page
                            'last' => $data['products']->url($data['products']->lastPage()), // URL for the last page
                            'prev' => $data['products']->previousPageUrl(), // URL for the previous page
                            'next' => $data['products']->nextPageUrl(), // URL for the next page
                        ]
                    ]
                ];
                return response()->json($result_array);
            // return view('addon.product.frontend.list', $data);

          
        } catch (\Throwable $th) {
     
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
      
    }



    public function productDetailViewApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }
            $messages = [
                'product_slug.required' => __('The Product Slug Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'product_slug' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isExists = Product::where('slug',$request->product_slug)->first();
            if($isExists == null){
                return response()->json(['message' => 'Given Product Slug Does Not Exists'], 302); 
            }
            $slug = $request->product_slug;
            $data['pageTitle'] = __("Product Details");
            $data['product'] = Product::whereSlug($slug)->with('user')->first();
          
            // if ((auth()->user() && auth()->user()->role != USER_ROLE_ADMIN) && $data['product']->status != STATUS_APPROVED && $data['product']->status != STATUS_UPCOMING_APPROVED){
            //     $this->showToastrMessage('error', __('This product is not active'));
            //     return redirect()->back();
            // }
            if (($user && $role != USER_ROLE_ADMIN) && $data['product']->status != STATUS_APPROVED && $data['product']->status != STATUS_UPCOMING_APPROVED){
                return response()->json(['message' => __('This product is not active')], 302); 
            }
            $productSliderTab = [
                'image_1' => getImageFile($data['product']->image_1),
                'image_2' => getImageFile($data['product']->image_2),
                'image_3' => getImageFile($data['product']->image_3),
                'image_4' => getImageFile($data['product']->image_4),
            ];
            $productSliderTabContent = getImageFile($data['product']->image_1);
            $averate_percent = $data['product']->average_review * 20;
            $total_reviews = ProductReview::where('product_id',$data['product']->id)->count();
            if($data['product']->old_price > $data['product']->current_price){
                if(get_currency_placement() == 'after'){
                    $productOldPrice= $data['product']->old_price.' '.get_currency_symbol();
                }
                else{
                    $productOldPrice= get_currency_symbol().' '.$data['product']->old_price;
                }
                if(get_currency_placement() == 'after'){
                    $productCurrentPrice= $data['product']->current_price.' '.get_currency_symbol();
                }
                else{
                    $productCurrentPrice= get_currency_symbol().' '.$data['product']->current_price;
                }
            }
            else{
                if(get_currency_placement() == 'after'){
                    $productCurrentPrice= $data['product']->current_price.' '.get_currency_symbol();
                }
                else{
                    $productCurrentPrice= get_currency_symbol().' '.$data['product']->current_price;
                }
                $productOldPrice = $productCurrentPrice;
            }

            if($data['product']->quantity > 0){
                $productStock = "In Stock"." (".$data['product']->quantity.")";
            }
            else{
                $productStock = __('Out of stock');
            }
            $productDescriptionTab = [
                $data['product']->description,
            ];
            $productAdditionalTab = [
                $data['product']->additional_information,
            ];
            $productShippingReturnTab = [
                $data['product']->shipping_return,
            ];

            if($user != null && $role == 3 && $data['product']->quantity > 0){
                 $addToCartProduct = route('student-cart-add');
            }
            else{
                $addToCartProduct = null; 
            }
            
        if($addToCartProduct != null){
            $result_array = [
                'product_id' => $data['product']->id,
                'product_images_slider_tab' => $productSliderTab,
                'product_images_slider_tab_content' => $productSliderTabContent,
                'product_description_tab' => $productDescriptionTab,
                'product_additional_information_tab' => $productAdditionalTab,
                'product_shipping_return_tab' => $productShippingReturnTab,
                'product_title' => $data['product']->title,
                'product_created_by' => $data['product']->user?->name,
                'product_average_reviews' => number_format($data['product']->average_review, 1),
                'product_average_reviews_percent' => $averate_percent,
                'product_total_reviews' =>  $total_reviews,
                'product_old_price' =>  $productOldPrice,
                'product_current_price' =>  $productCurrentPrice,
                'product_stock' =>  $productStock,
                'product_action_btn' =>  $addToCartProduct,
            ];
        }
        else{
            $result_array = [
                'product_id' => $data['product']->id,
                'product_images_slider_tab' => $productSliderTab,
                'product_images_slider_tab_content' => $productSliderTabContent,
                'product_description_tab' => $productDescriptionTab,
                'product_additional_information_tab' => $productAdditionalTab,
                'product_shipping_return_tab' => $productShippingReturnTab,
                'product_title' => $data['product']->title,
                'product_created_by' => $data['product']->user?->name,
                'product_average_reviews' => number_format($data['product']->average_review, 1),
                'product_average_reviews_percent' => $averate_percent,
                'product_total_reviews' =>  $total_reviews,
                'product_old_price' =>  $productOldPrice,
                'product_current_price' =>  $productCurrentPrice,
                'product_stock' =>  $productStock,
            ];
        }
          
            return response()->json($result_array);
         
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }    
   
    }



    public function filterCourseData($request)
    {
        if ($request->category_id) {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($request->category_id)->with('course_instructors.user.instructor.instructor_courses')->active();
        } elseif ($request->sub_category_id) {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereSubcategoryId($request->sub_category_id)->with('course_instructors.user.instructor.instructor_courses')->active();
        } else {
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->with('course_instructors.user.instructor.instructor_courses')->active();
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


  


    public function allCategoryWiseCoursesApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }
            $messages = [
                'category_id.required' => __('The Category ID Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isExists = Category::where('id',$request->category_id)->first();
            if($isExists == null){
                return response()->json(['message' => 'Given Category ID Does Not Exists'], 302); 
            }
            $categoryID = $request->category_id;

            $data['pageTitle'] = "Courses";
            $data['metaData'] = staticMeta(4);
            $data['categories'] = Category::with('subcategories')->active()->get();
            $data['category'] = Category::where('id',$request->category_id)->first();
            $data['courses'] = $this->filterCourseDataWithSearch($request);
            // $data['courses'] = $this->filterCourseData($request);
            // $data['courses'] = $data['courses']->whereCategoryId($data['category']->id)->get();
        
            // $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($data['category']->id)->active()->paginate(9);
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

            $filters_section_data_with_values = array();
            $people_also_search_section = array();
            $courses_section_data = array();
            $difficultyLevelIds = array();

            foreach($data['random_four_categories'] as $random_four_category){
                $people_also_search_section[] =[
                    'course_category_id' => $random_four_category->id,
                    'course_category_slug' => $random_four_category->slug,
                    'course_category_name' => $random_four_category->name,
                    'course_category_details_web_route' => route('category-courses', $random_four_category->slug),
                    'course_category_details_api_route' => route('frontend-all-categorywise-courses'),
                ];
            }
            foreach ( $data['difficulty_levels'] as $level){
                $difficultyLevelIds[] =[
                    $level->id => $level->name,
                ];
            }
            $filters_section_data_with_values = [
                'sortBy_id' => [
                    1 => 'Default',
                    2 => 'Newest Courses',
                    3 => 'Oldest Courses'
                ],
                'difficultyLevelIds' => $difficultyLevelIds,
                'ratingIds' => [
                    5 => __('5 star'),
                    4 => __('4 star or above'),
                    3 => __('3 star or above'),
                    2 => __('2 star or above'),
                    1 => __('1 star or above'),
                ],
                'min_price' => 0,
                'max_price' => $data['highest_price'] ? (int)$data['highest_price'] : 10000,
                'learnerAccessibilityTypes' => [
                    'free' => __('Free'),
                    'paid' => __('Paid'),
                ],
                'durationIds' => [
                    1 => __('Less Than 24 Hours'),
                    2 => __('24 to 36 Hours'),
                    3 => __('36 to 72 Hours'),
                    4 => __('Above 72 Hours'),
                ],
            ];
            foreach($data['courses'] as $course){
                if($course->status != STATUS_UPCOMING_APPROVED){
                    if (date('Y-m-d H:i:s', strtotime('-7 days')) >= $course->created_at){
                        if (in_array($course->id, $data['topCourse'])){
                            $courseTag = __('Best Seller');
                        }
                        else{
                            $courseTag = __('New course');
                        }
                    }
                    $special = $course->specialPromotionTagCourse?->specialPromotionTag?->name;
                    if($special){
                        $coursePromotionTag = $special;
                    }
                    else{
                        $coursePromotionTag = null; 
                    }
                }
                else{
                    $coursePromotionTag = null;  
                    $courseTag = __('Upcoming');
                }

                if($course->learner_accessibility == 'paid'){
                    $courseAccess = __('Paid');
                    $startDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->start_date));
                    $endDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->end_date));
                    $percentage = $course->promotionCourse?->promotion?->percentage;
                    $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
                    if(now()->gt($startDate) && now()->lt($endDate)){
                        if(get_currency_placement() == 'after'){
                            $courseDiscountPrice = $discount_price.' '.get_currency_symbol();
                        }
                        else{
                            $courseDiscountPrice = get_currency_symbol().' '.$discount_price; 
                        }
                        if(get_currency_placement() == 'after'){
                            $coursePrice = $course->price.' '.get_currency_symbol();
                        }
                        else{
                            $coursePrice = get_currency_symbol().' '.$course->price; 
                        }
                        $courseOldPrice = $coursePrice;
                    }
                    elseif ($course->price <= $course->old_price){
                        $courseDiscountPrice = null;
                        if(get_currency_placement() == 'after'){
                            $coursePrice = $course->price.' '.get_currency_symbol();
                        }
                        else{
                            $coursePrice = get_currency_symbol().' '.$course->price; 
                        }
                        if(get_currency_placement() == 'after'){
                            $courseOldPrice = $course->old_price.' '.get_currency_symbol();
                        }
                        else{
                            $courseOldPrice = get_currency_symbol().' '.$course->old_price; 
                        }
                    }
                    else{
                        $courseDiscountPrice = null;
                        if(get_currency_placement() == 'after'){
                            $coursePrice = $course->price.' '.get_currency_symbol();
                        }
                        else{
                            $coursePrice = get_currency_symbol().' '.$course->price; 
                        }
                        $courseOldPrice = $coursePrice;
                    }
                }
                elseif($course->learner_accessibility == 'free'){
                    if(get_currency_placement() == 'after'){
                        $coursePrice = number_format(0,2).' '.get_currency_symbol();
                    }
                    else{
                        $coursePrice = get_currency_symbol().' '.number_format(0,2); 
                    }
                    $courseAccess = __('Free');
                }

                
                if($course->course_type == 3){
                    $courseType = __('Live');
                    $courses_section_data[] =[
                        'course_id' => $course->id,
                        'course_slug' => $course->slug,
                        'course_type' => $courseType,
                        'course_title' => Str::limit($course->title, 40),
                        'course_image' => getImageFile($course->image_path),
                        'course_price' => $coursePrice,
                        'user_name' => $course->user?->name,
                        'user_pic' => getImageFile($course->user?->image),
                        'star_rating' => $course->average_rating,
                        'duration' => $course->access_period,
                        'course_detail_api_route' => route('frontend-course-detail',['slug'=>$course->slug]),
                    ];
                }
                elseif($course->course_type == 2 || $course->course_type == 1){
                    if($course->course_type == 2){
                        $courseType = __('Scorm');
                    }
                    else{
                        $courseType = __('General'); 
                    }
                    $courses_section_data[] =[
                        'course_id' => $course->id,
                        'course_slug' => $course->slug,
                        'course_type' => $courseType,
                        'course_title' => Str::limit($course->title, 40),
                        'course_image' => getImageFile($course->image_path),
                        'course_price' => $coursePrice,
                        'user_name' => $course->user?->name,
                        'user_pic' => getImageFile($course->user?->image),
                        'star_rating' => $course->average_rating,
                        'duration' => $course->access_period,
                        'course_detail_api_route' => route('frontend-course-detail',['slug'=>$course->slug]),
                     
                    ];
                }
            }
            
            $result_array = [
                'category_id' => $data['category']->id,
                'category_cover_image' => asset($data['category']->cover_image_path),
                'category_name' => $data['category']->name,
                'category_courses_section_data' => $courses_section_data,
            ];
            return response()->json($result_array);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    
    }






    public function allSubCategoryWiseCoursesApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }
            $messages = [
                'sub_category_id.required' => __('The SubCategory ID Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'sub_category_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isExists = Subcategory::where('id',$request->sub_category_id)->first();
            if($isExists == null){
                return response()->json(['message' => 'Given SubCategory ID Does Not Exists'], 302); 
            }
            $subCategoryID = $request->sub_category_id;

            $data['pageTitle'] = "Courses";
            $data['metaData'] = staticMeta(4);
            $data['categories'] = Category::with('subcategories')->active()->get();
            $data['subcategory'] = Subcategory::where('id',$request->sub_category_id)->first();

            $data['courses'] = $this->filterCourseDataWithSearch($request);
            // $data['courses'] = $this->filterCourseData($request);
            // $data['courses'] = $data['courses']->whereSubcategoryId($data['subcategory']->id)->paginate(9);
        
            // $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($data['category']->id)->active()->paginate(9);
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

            $filters_section_data_with_values = array();
            $people_also_search_section = array();
            $courses_section_data = array();
            $difficultyLevelIds = array();

            foreach($data['random_four_categories'] as $random_four_category){
                $people_also_search_section[] =[
                    'course_category_id' => $random_four_category->id,
                    'course_category_slug' => $random_four_category->slug,
                    'course_category_name' => $random_four_category->name,
                    'course_category_details_web_route' => route('category-courses', $random_four_category->slug),
                    'course_category_details_api_route' => route('frontend-all-categorywise-courses'),
                ];
            }
            foreach ( $data['difficulty_levels'] as $level){
                $difficultyLevelIds[] =[
                    $level->id => $level->name,
                ];
            }
            $filters_section_data_with_values = [
                'sortBy_id' => [
                    1 => 'Default',
                    2 => 'Newest Courses',
                    3 => 'Oldest Courses'
                ],
                'difficultyLevelIds' => $difficultyLevelIds,
                'ratingIds' => [
                    5 => __('5 star'),
                    4 => __('4 star or above'),
                    3 => __('3 star or above'),
                    2 => __('2 star or above'),
                    1 => __('1 star or above'),
                ],
                'min_price' => 0,
                'max_price' => $data['highest_price'] ? (int)$data['highest_price'] : 10000,
                'learnerAccessibilityTypes' => [
                    'free' => __('Free'),
                    'paid' => __('Paid'),
                ],
                'durationIds' => [
                    1 => __('Less Than 24 Hours'),
                    2 => __('24 to 36 Hours'),
                    3 => __('36 to 72 Hours'),
                    4 => __('Above 72 Hours'),
                ],
            ];
            foreach($data['courses'] as $course){
                if($course->status != STATUS_UPCOMING_APPROVED){
                    if (date('Y-m-d H:i:s', strtotime('-7 days')) >= $course->created_at){
                        if (in_array($course->id, $data['topCourse'])){
                            $courseTag = __('Best Seller');
                        }
                        else{
                            $courseTag = __('New course');
                        }
                    }
                    $special = $course->specialPromotionTagCourse?->specialPromotionTag?->name;
                    if($special){
                        $coursePromotionTag = $special;
                    }
                    else{
                        $coursePromotionTag = null; 
                    }
                }
                else{
                    $coursePromotionTag = null;  
                    $courseTag = __('Upcoming');
                }

                if($course->learner_accessibility == 'paid'){
                    $courseAccess = __('Paid');
                    $startDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->start_date));
                    $endDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->end_date));
                    $percentage = $course->promotionCourse?->promotion?->percentage;
                    $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
                    if(now()->gt($startDate) && now()->lt($endDate)){
                        if(get_currency_placement() == 'after'){
                            $courseDiscountPrice = $discount_price.' '.get_currency_symbol();
                        }
                        else{
                            $courseDiscountPrice = get_currency_symbol().' '.$discount_price; 
                        }
                        if(get_currency_placement() == 'after'){
                            $coursePrice = $course->price.' '.get_currency_symbol();
                        }
                        else{
                            $coursePrice = get_currency_symbol().' '.$course->price; 
                        }
                        $courseOldPrice = $coursePrice;
                    }
                    elseif ($course->price <= $course->old_price){
                        $courseDiscountPrice = null;
                        if(get_currency_placement() == 'after'){
                            $coursePrice = $course->price.' '.get_currency_symbol();
                        }
                        else{
                            $coursePrice = get_currency_symbol().' '.$course->price; 
                        }
                        if(get_currency_placement() == 'after'){
                            $courseOldPrice = $course->old_price.' '.get_currency_symbol();
                        }
                        else{
                            $courseOldPrice = get_currency_symbol().' '.$course->old_price; 
                        }
                    }
                    else{
                        $courseDiscountPrice = null;
                        if(get_currency_placement() == 'after'){
                            $coursePrice = $course->price.' '.get_currency_symbol();
                        }
                        else{
                            $coursePrice = get_currency_symbol().' '.$course->price; 
                        }
                        $courseOldPrice = $coursePrice;
                    }
                }
                elseif($course->learner_accessibility == 'free'){
                    if(get_currency_placement() == 'after'){
                        $coursePrice = number_format(0,2).' '.get_currency_symbol();
                    }
                    else{
                        $coursePrice = get_currency_symbol().' '.number_format(0,2); 
                    }
                    $courseAccess = __('Free');
                }
                if($course->course_type == 3){
                    $courseType = __('Live');
                    $courses_section_data[] =[
                        'course_id' => $course->id,
                        'course_slug' => $course->slug,
                        'course_type' => $courseType,
                        'course_title' => Str::limit($course->title, 40),
                        'course_image' => getImageFile($course->image_path),
                        'course_price' => $coursePrice,
                        'user_name' => $course->user?->name,
                        'user_pic' => getImageFile($course->user?->image),
                        'star_rating' => $course->average_rating,
                        'duration' => $course->access_period,
                        'course_detail_api_route' => route('frontend-course-detail',['slug'=>$course->slug]),
                    ];
                }
                elseif($course->course_type == 2 || $course->course_type == 1){
                    if($course->course_type == 2){
                        $courseType = __('Scorm');
                    }
                    else{
                        $courseType = __('General'); 
                    }
                    $courses_section_data[] =[
                        'course_id' => $course->id,
                        'course_slug' => $course->slug,
                        'course_type' => $courseType,
                        'course_title' => Str::limit($course->title, 40),
                        'course_image' => getImageFile($course->image_path),
                        'course_price' => $coursePrice,
                        'user_name' => $course->user?->name,
                        'user_pic' => getImageFile($course->user?->image),
                        'star_rating' => $course->average_rating,
                        'duration' => $course->access_period,
                        'course_detail_api_route' => route('frontend-course-detail',['slug'=>$course->slug]),
                    ];
                }
            }
            
            $result_array = [
                'subcategory_id' => $data['subcategory']->id,
                'subcategory_cover_image' => asset($data['subcategory']->cover_image),
                'subcategory_name' => $data['subcategory']->name,
                'subcategory_courses_section_data' => $courses_section_data,
            ];
            return response()->json($result_array,200);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    
    }


    public function allCategoriesWithResourcesApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }

            $categories = Category::with('subcategories')->active()->get();
            foreach($categories as $category){
                $category_subcategories = array();
                if(count($category->subcategories) > 0){
                    foreach ($category->subcategories as $key => $subcategory) {
                        $category_subcategories[]=[
                            'subcategory_id' => $subcategory->id,
                            'subcategory_slug' => $subcategory->slug,
                            'subcategory_name' => $subcategory->name,
                            'subcategory_courses_web_route' => route('subcategory-courses', $subcategory->slug),
                            'subcategory_courses_api_route' => route('frontend-all-subcategorywise-courses'),
                        ];
                    }
                }
                $result_array[] = [
                    'category_id' => $category->id,
                    'category_slug' => $category->slug,
                    'category_name' => $category->name,
                    'category_courses_web_route' => route('category-courses', $category->slug),
                    'category_courses_api_route' => route('frontend-all-categorywise-courses'),
                    'category_subcategories' => $category_subcategories,
                ];
            }
            $result_array[] =[
                'all_courses_web_route' => route('courses'),
                'all_courses_api_route' => route('frontend-all-courses'),
            ];
            return response()->json($result_array);
            // dd($categories);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }



    public function filterCourseDataWithSearchBackUp($request)
    {
      
       
        if($request->keyword && isset($request->keyword)){
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()
            ->with('course_instructors.user.instructor.instructor_courses')
            ->active()
            ->where('title', 'like', '%' . $request->keyword . '%');
        }
        else{
            $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()
            ->with('course_instructors.user.instructor.instructor_courses')
            ->active();
        }
    
        $categoryIds = $request->category_ids ?? [];
        $rating = (int)$request->rating;
        $min_price = (int)$request->min_price;
        $max_price = (int)$request->max_price;
        $sortBy_id = $request->sortBy_id;

        $data['courses'] = $data['courses']->where(function ($q) use ($categoryIds, $rating, $min_price, $max_price) {

          
            if (!empty($categoryIds)) {
                $q->whereIn('category_id', $categoryIds);
            }

            if ($rating) {
                $q->where('average_rating', '>=', $rating);
            }

            if ($min_price && $max_price) {
                $q->whereBetween('price', [$min_price, $max_price]);
            } 
            else if ($max_price) {
                $q->whereBetween('price', [0, $max_price]);
            }

        });

    
        if ($sortBy_id) {
            if ($sortBy_id == 2) {
                $data['courses'] = $data['courses']->orderBy('id', 'DESC');
            }
            if ($sortBy_id == 3) {
                $data['courses'] = $data['courses']->orderBy('id', 'ASC');
            }
        }
        return $data['courses']->get();
    }




    public function filterCourseDataWithSearch($request)
{
    if ($request->category_id) {
        $query = Course::where('private_mode', '!=', 1)->publicCatalog()->whereCategoryId($request->category_id)->with('course_instructors.user.instructor.instructor_courses')->active();
    } elseif ($request->sub_category_id) {
        $query = Course::where('private_mode', '!=', 1)->publicCatalog()->whereSubcategoryId($request->sub_category_id)->with('course_instructors.user.instructor.instructor_courses')->active();
    } else {
        $query = Course::where('private_mode', '!=', 1)->publicCatalog()->with('course_instructors.user.instructor.instructor_courses')->active();
    }

// Apply search keyword filter if present
if ($request->keyword) {
$query->where('title', 'like', '%' . $request->keyword . '%');
}


// Apply category filter if category_ids are present
$categoryIds = $request->category_ids ?? [];
$categoryIds = array_filter($categoryIds); // Removes null or empty values
if (!empty($categoryIds)) {
$query->whereIn('category_id', $categoryIds);
}


// Apply sub-category filter if subcategory_ids are present
$subCategoryIds = $request->subcategory_ids ?? [];
$subCategoryIds = array_filter($subCategoryIds); // Removes null or empty values
if (!empty($subCategoryIds)) {
$query->whereIn('subcategory_id', $subCategoryIds);
}


// Apply rating filter if rating is present
$rating = (int)$request->rating;
if ($rating) {
$query->where('average_rating', '>=', $rating);
}

// Apply price filter if min_price and max_price are provided
$min_price = (int)$request->min_price;
$max_price = (int)$request->max_price;
if ($min_price && $max_price) {
$query->whereBetween('price', [$min_price, $max_price]);
} elseif ($max_price) {
$query->whereBetween('price', [0, $max_price]);
}

// Apply sorting if sortBy_id is provided
$sortBy_id = $request->sortBy_id;
if ($sortBy_id) {
if ($sortBy_id == 2) {
    $query->orderBy('id', 'DESC');
} elseif ($sortBy_id == 3) {
    $query->orderBy('id', 'ASC');
}
}

// Return the results of the query
return $query->get();
}


    public function courseSearchApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 302);
               }
            }
            else{
                $user = null;
                $role = null;
            }

            $messages = [
                // 'keyword.required' => __('The Keyword Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'keyword' => 'nullable',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            if (!get_option('private_mode') || Auth::user()) {
                // if(isset($request->keyword)){
                //     $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->active()->where('title', 'like', '%' . $request->keyword . '%')->get();
                // }
                // else{
                //     $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->active()->get();
                // }
                $data['courses'] = $this->filterCourseDataWithSearch($request);
              
            } else {
                $data['courses'] = [];
            }
            $course_results = array();
            $simple_categories = array();
            $categories_with_images = array();
            $random_four_categories = Category::with('subcategories')->active()->inRandomOrder()->limit(4)->get();
            if(!$random_four_categories->isEmpty()){
                foreach ($random_four_categories as $key => $cat) {
                    $category_subcategories_1 = array();
                    $category_subcategories_2 = array();
                    if(count($cat->subcategories) > 0){
                        foreach ($cat->subcategories as $key => $subcategory) {
                            $category_subcategories_1[]=[
                                'id' => $subcategory->id,
                                'name' => $subcategory->name,
                                'image' => getImageFile($subcategory->cover_image_path),
                            ];
                            $category_subcategories_2[]=[
                                'id' => $subcategory->id,
                                'name' => $subcategory->name,
                            ];
                        }
                    }
                    $categories_with_images[] = [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'image' => getImageFile($cat->image_path),
                        'sub_categories' => $category_subcategories_1,
                    ];
                    $simple_categories[] = [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'sub_categories' => $category_subcategories_2,
                    ];
                }
            }

            foreach ($data['courses'] as $key => $course) {
                if($course->course_type == 3){ // LIVE Course
                    $courseTitle2 = Str::limit($course->title.' (Live)', 80); 
                }
                else{ // General Course
                    $courseTitle2 = Str::limit($course->title, 40);
                }


            ////////////////////////////////////////////////////////
            if($course->learner_accessibility == 'paid'){
                $courseAccess = __('Paid');
                $startDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->start_date));
                $endDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->end_date));
                $percentage = $course->promotionCourse?->promotion?->percentage;
                $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
                if(now()->gt($startDate) && now()->lt($endDate)){
                    if(get_currency_placement() == 'after'){
                        $coursePrice = $course->price.' '.get_currency_symbol();
                    }
                    else{
                        $coursePrice = get_currency_symbol().' '.$course->price; 
                    }
                }
                elseif ($course->price <= $course->old_price){
                    if(get_currency_placement() == 'after'){
                        $coursePrice = $course->price.' '.get_currency_symbol();
                    }
                    else{
                        $coursePrice = get_currency_symbol().' '.$course->price; 
                    }
                }
                else{
                    if(get_currency_placement() == 'after'){
                        $coursePrice = $course->price.' '.get_currency_symbol();
                    }
                    else{
                        $coursePrice = get_currency_symbol().' '.$course->price; 
                    }
                }
            }
            elseif($course->learner_accessibility == 'free'){
                if(get_currency_placement() == 'after'){
                    $coursePrice = number_format(0,2).' '.get_currency_symbol();
                }
                else{
                    $coursePrice = get_currency_symbol().' '.number_format(0,2); 
                }
                // $coursePrice = null;
                $courseAccess = __('Free');
            }
            //////////////////////////////////////////////////////////
                $course_results[] = [
                    'course_id' => $course->id,
                    'course_slug' => $course->slug,
                    'course_title' => $courseTitle2,
                    'course_image' => getImageFile($course->image_path),
                    // 'course_price' => $course->price,
                    'course_price' => $coursePrice,
                    'user_name' => $course->user?->name,
                    'user_pic' => getImageFile($course->user?->image),
                    'star_rating' => $course->average_rating,
                    'duration' => $course->access_period,
                    'course_detail_api_route' => route('frontend-course-detail',['slug'=>$course->slug]),
                ];
            }
            // $result_array = [
            //     'simple_categories' => $simple_categories,
            //     'categories_with_images' => $categories_with_images,
            //     'course_results' => $course_results,
            // ];
            $result_array = [
                'simple_categories' => $simple_categories,
                'categories_with_images' => $categories_with_images,
                'course_results' => $course_results,
            ];
            return response()->json($result_array,200);
         
            return view('frontend.render-search-course-list', $data);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
      
    }





    public function allCategoriesWithNamesApi(Request $request)
    {
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 302);
               }
            }
            else{
                $user = null;
                $role = null;
            }

            $categories = Category::active()->get();
            foreach($categories as $category){
                $result_array[] = [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                ];
            }
            return response()->json($result_array,200);
            // dd($categories);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }



    public function getAllTimeZonesApi(Request $request)
    {   
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }
          
            $timezones_array = timezones_array();
            
          
            return response()->json($timezones_array);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }





      public function getAllCoursesByTypeApi(Request $request)
    {   
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }
            if($request->type_id){
                  $type = $request->type_id;
                  if($type != 1 && $type != 2){
                        return response()->json(['message' => 'Invalid Course Type. Must Be either "1" or "2"'], 302);
                  }
                  if($type == 1){
                          $courses = Course::whereIn('course_type', [3])
                                ->where('status', 1)
                                ->orderBy('id', 'DESC')
                                ->get();
                  }
                  else{
                         $courses = Course::whereIn('course_type', [1,2])
                                ->where('status', 1)
                                ->orderBy('id', 'DESC')
                                ->get();
                  }

                  if(!$courses->isEmpty()){
                        foreach ($courses as $key => $course) {
                            $result_array[] = [
                                'course_id' => $course->id,
                                'course_name' => $course->title,
                            ];
                        }
                  }
            }
            else{
                 return response()->json(['message' => 'Please Select Course Type'], 302);
            }
          
          
            
          
            return response()->json($result_array);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }




    public function getAllCountriesListsApi(Request $request)
    {   
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }
          
            $getAllCountries = Country::orderBy('id','ASC')->get();
               if(!$getAllCountries->isEmpty()){
                    foreach ($getAllCountries as $key => $country) {
                            $result_array[] = [
                                'country_id' => $country->id,
                                'country_name' => $country->country_name,
                            ];
                    }
               }
            
          
            return response()->json($result_array);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }


      public function getTimeSlotsApi(Request $request)
    {   
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }
          
             $getTimeSlots = TimeSlot::where('is_active',1)->orderBy('id','ASC')->get();
               if(!$getTimeSlots->isEmpty()){
                    foreach ($getTimeSlots as $key => $slot) {
                        $formattedTime = Carbon::parse($slot->start_time)->format('g:i A');
                            $result_array[] = [
                                'slot_id' => $slot->id,
                                'slot_time' => $formattedTime,
                            ];
                    }
               }
            
          
            return response()->json($result_array);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }


    public function getAllLanguagesApi(Request $request)
    {   
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }

            foreach(appLanguages() as $app_lang){
                $result_array[] =[
                    'lang_id' => $app_lang->id,
                    'lang_iso_code' => $app_lang->iso_code,
                    'lang_flag_image' => asset($app_lang->flag),
                    'lang_name' => $app_lang->language,
                    'update_lang_web_route' => url('/local/'.$app_lang->iso_code),
                    'update_lang_api_route' => route('frontend-local-update-language'),
                ];
            }
            return response()->json($result_array);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }





    public function updateLanguageApi(Request $request)
    {   
        try {
            $result_array = array();
            if (Auth::guard('sanctum')->check()) {
               $user = auth('sanctum')->user();
               if($user){
                    $role = $user->role;
               }
               else{
                    return response()->json(['message' => 'User Is Not Logged In'], 200);
               }
            }
            else{
                $user = null;
                $role = null;
            }

            $messages = [
                'lang_iso_code.required' => __('The Language ISO Code Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'lang_iso_code' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
            $ln = $request->lang_iso_code;
            $language = Language::where('iso_code', $request->lang_iso_code)->first();
        if (!$language){
            $language = Language::find(1);
            if ($language){
                $ln = $language->iso_code;
                session(['local' => $ln]);
                App::setLocale(session()->get('local'));
            }
        }
     
        session(['local' => $ln]);
        App::setLocale(session()->get('local'));
    
        return response()->json(['message' => 'App Language Updated Successfully'], 200);
    
        // return redirect()->back();
            return response()->json($result_array);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }






    public function courseDetailApi(Request $request,$slug)
    {
       try {
        $result_array = array();
        if (Auth::guard('sanctum')->check()) {
           $user = auth('sanctum')->user();
           $role = $user ? $user->role : null;
        }
        else{
            $user = null;
            $role = null;
        }
    
     
     
        // Match slug case-insensitively so URLs work regardless of casing
        $isExists = Course::whereRaw('LOWER(slug) = ?', [strtolower($slug)])->first();
        if($isExists == null){
            return response()->json(['message' => 'Given Course Slug Does Not Exists'], 404);
        }

        $slug = $isExists->slug; // use stored slug for rest of logic

        $data['pageTitle'] = "Course Details";
        $data['metaData'] = staticMeta(3);
        $getCourse = $isExists;
        $courseType = $getCourse->course_type;
        $courseTypeValue = $getCourse->course_type;
        $data['course'] = $isExists;
        $course = $data['course'];

        if($course->course_type == 3){
            $courseType = __('Live');
            $courseTypeValue = $course->course_type;
        }
        else if($course->course_type == 2 || $course->course_type == 1){
            if($course->course_type == 2){
                $courseType = __('Scorm');
                $courseTypeValue = $course->course_type;
            }
            else{
                $courseType = __('General'); 
                $courseTypeValue = $course->course_type;
            } 
        }


        $course_preview_src = null;
        if($course->intro_video_check == 1 && getVideoFile($course->video)) {
            $course_preview_src = getVideoFile($course->video);
            $course_preview_src_type =  __('course_intro_video');
        }
        else if($course->intro_video_check == 2 && $course->youtube_video_id){
            if (Str::contains($course->youtube_video_id, 'https://www.youtube.com')) {
                $course_preview_src = $course->youtube_video_id;    
            }
            else if(Str::contains($course->youtube_video_id, 'https://youtu.be')){
                $course_preview_src = $course->youtube_video_id;
            }
            else{
                $course_preview_src = "https://www.youtube.com/embed/".$course->youtube_video_id;
            } 
              $course_preview_src_type =  __('course_intro_youtube_video');     
        }
        else{
            $course_preview_src =  $course->cover_image ? getImageFile($course->cover_image) : getImageFile($course->image);
            
            $course_preview_src_type =  __('course_intro_image');
        }


        if($course->course_type == COURSE_TYPE_GENERAL){
            $course_duration = $course->VideoDuration;
            $videoLectures = $course->lectures->count() > 0 ? $course->video_duration : '0';
        }
        else{
            
            $course_duration = $course->scorm_course? $course->scorm_course->duration : 0;
            $videoLectures = null;
        }

    
    
        $overView = array();
        $curriculum = array();
        $reviewsArray = array();
        $user_reviews = array();
        $instructorsArray = array();
        $skillsArray = array();
        $getActiveSkills = Skill::where('status',1)->get();
        if(!$getActiveSkills->isEmpty()){
            foreach ($getActiveSkills as $key => $skill) {
                $skillsArray[] = $skill->title;
            }
        }
           

        if($course->course_type == 3){  // Live
        
            if($course->instructor){
                if($course->instructor?->user->role == USER_ROLE_INSTRUCTOR){
                    $userType = 'instructor';
                    if (!$course->enrollments->IsEmpty()){
                        $total_instructor_students = Enrollment::where('course_id', $course->id)->where('status',ACCESS_PERIOD_ACTIVE)->where('end_date', '>=', now())->count();
                        // $total_instructor_students = $course_instructor->user?->$userType?->enrollments?->count();
                    }
                    else{
                        $total_instructor_students = 0;
                    }
                }
                else if($course->instructor?->user->role == USER_ROLE_ORGANIZATION){
                    $userType = 'organization';
                    if (!$course->enrollments->IsEmpty()){
                        $total_instructor_students = Enrollment::where('course_id', $course->id)->where('status',ACCESS_PERIOD_ACTIVE)->where('end_date', '>=', now())->count();
                        // $total_instructor_students = $course_instructor->user?->$userType?->enrollments?->count();
                    }
                    else{
                        $total_instructor_students = 0;
                    }
                }
                else{
                    $userType = null;
                    if (!$course->enrollments->IsEmpty()){
                        $total_instructor_students = Enrollment::where('course_id', $course->id)->where('status',ACCESS_PERIOD_ACTIVE)->where('end_date', '>=', now())->count();
                        // $total_instructor_students = $course_instructor->user?->$userType?->enrollments?->count();
                    }
                    else{
                        $total_instructor_students = 0;
                    }
                
                }

                 $instructorsArray[] =[
                            'id' => $course->instructor?->user?->id,
                            'image' => asset($course->instructor?->user?->image_path),
                            'name' => $course->instructor?->user?->name,
                            'type' => $userType,
                            'professional_title' => $course->instructor?->user?->professional_title,
                            'total_students' => $total_instructor_students.__(' Students'),
                            'about' => $course->instructor->user?->$userType?->about_me,
                ];
            }
            else{
                $instructorsArray = [];
            }
        }
        else{
            if (isset($course->course_instructors)){
            foreach ($course->course_instructors->where('status', STATUS_APPROVED) as $course_instructor){
                if ($course_instructor->user?->is_public === 0 || $course_instructor->user?->is_public == null){   
                    continue;
                }
                if($course_instructor->user->role != USER_ROLE_ADMIN){
                    if($course_instructor->user->role == USER_ROLE_INSTRUCTOR){
                        $userType = 'instructor';
                        $total_instructor_course =  count($course_instructor->user?->$userType?->courses);
                        $total_instructor_students = $course_instructor->user?->$userType?->enrollments?->count();
                    }
                    else if($course_instructor->user->role == USER_ROLE_ORGANIZATION){
                        $userType = 'organization';
                        $total_instructor_course =  count($course_instructor->user?->$userType?->courses);
                        $total_instructor_students = $course_instructor->user?->$userType?->enrollments?->count();
                    }
                    else{
                        $userType = null;
                        $total_instructor_course =  count($course_instructor->user?->courses);
                        $total_instructor_students = $course_instructor->user?->enrollments?->count();   
                    }

                    $instructorsArray[] =[
                        'id' => $course_instructor->user?->id,
                        'image' => asset($course_instructor->user?->image_path),
                        'name' => $course_instructor->user?->name,
                        'type' => $userType,
                        'professional_title' => $course_instructor->user?->professional_title,
                        'total_students' => $total_instructor_students.__(' Students'),
                        'about' => $course_instructor->user?->$userType?->about_me,
                    ];
                }
            }
            }
        }
       


        // $accessPeriod = (!$course->access_period) ? __('Full Lifetime Access') : $course->access_period.' '.__('days after the enrollment');
        $accessPeriod = (!$course->access_period) ? __('Full Lifetime Access') : convertDaysToWeeks($course->access_period).' '.__('weeks');

        // Match frontend course-details blade: classes from courses.classes; duration from duration + duration_type (fallback: computed video/scorm duration)
        $durationTypeSuffix = 'hrs';
        if ((int) $course->duration_type === 2) {
            $durationTypeSuffix = 'd';
        } elseif ((int) $course->duration_type === 3) {
            $durationTypeSuffix = 'mo';
        }
        $instructionalDurationDisplay = null;
        if ($course->duration !== null && $course->duration !== '' && is_numeric($course->duration) && (float) $course->duration > 0) {
            $instructionalDurationDisplay = trim((string) $course->duration) . $durationTypeSuffix;
        } elseif ($course_duration !== null && $course_duration !== '' && $course_duration !== 0 && $course_duration !== '0') {
            $instructionalDurationDisplay = is_string($course_duration) ? $course_duration : (string) $course_duration;
        }

        $courseClassesCount = (int) ($course->classes ?? 0);

        $overView = [
            
            'title' =>  $course->course_type == 3 ? $course->title.' (Live)': $course->title,
            'description' => $course->description,
            'total_lessons' => count($course->lessons),
            /** Number of classes (courses.classes) — same as category cards / course-details blade */
            'classes' => $courseClassesCount,
            /** Instructional length, e.g. "48hrs" — not enrollment access period */
            'instructional_duration' => $instructionalDurationDisplay,
            'level' => $course->difficultyLevel?->name,
            /** Enrollment access window (weeks / lifetime) — sidebar "Access" row */
            'duration' => $accessPeriod,
            'language' => $course->language?->name,
            // 'instructors' => $instructorsArray,
            'skills' => $skillsArray,
        ];





        // foreach($data['reviews'] as $review){
        //     if($review->user == null){
        //         continue;
        //     }
        //     $user_reviews[] = [
        //         'id' => $review->id,
        //         'user_id' => $review->user?->id,
        //         'user_image' => asset($review->user->image_path),
        //         'user_name' => $review->user?->name,
        //         'comment' => $review->comment,
        //         'created_at' => $review->created_at->diffForHumans(),
        //         'rating' => $review->rating,         
        //     ];
        // }


        if($user){
            $checkReview = Review::where('user_id',$user->id)->where('course_id',$data['course']->id)->first();
            if($checkReview){
                $flag = false;

            }
            else{
                 $flag = true;
            }
        }
        else{
            // $flag = true;
            $flag = false;
        }
     
        // Base query
        $allReviewsQuery = Review::where('course_id', $data['course']->id);

        // Stats
        $total_reviews = $allReviewsQuery->count();
        $ratingCounts = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingCounts[$i] = (clone $allReviewsQuery)->where('rating', $i)->count();
        }

        $total_rating_sum = array_sum(array_map(fn($rating, $count) => $rating * $count, array_keys($ratingCounts), $ratingCounts));
        $average_rating = $total_reviews > 0 ? $total_rating_sum / $total_reviews : 0;

        $ratingPercentages = [];
        foreach ($ratingCounts as $stars => $count) {
            $ratingPercentages[$stars] = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
        }

        // User's review (if exists)
        if($user){
            $userReview = (clone $allReviewsQuery)->where('user_id', $user->id)->first();
        }
        else{
            $userReview = null;
        }
       
        $userReviewData = null;
        if ($userReview) {
            $userReviewData = [
                'id' => $userReview->id,
                'user_id' => $userReview->user?->id,
                'user_image' => asset($userReview->user?->image_path),
                'user_name' => $userReview->user?->name,
                'comment' => $userReview->comment,
                'created_at' => $userReview->created_at->diffForHumans(),
                'rating' => $userReview->rating,
            ];
        }

        // Paginate other reviews
         if($user){
            $otherReviewsQuery = (clone $allReviewsQuery)->when($userReview, fn($q) => $q->where('user_id', '!=', $user->id));
            $paginatedReviews = $otherReviewsQuery->orderBy('id', 'DESC')->paginate(4);
         }
         else{
            $paginatedReviews = $allReviewsQuery->orderBy('id', 'DESC')->paginate(4);
         }
       
        $customerReviewsArray = $paginatedReviews->getCollection()->map(function ($review) {
            return [
                'id' => $review->id,
                'user_id' => $review->user?->id,
                'user_image' => asset($review->user?->image_path),
                'user_name' => $review->user?->name,
                'comment' => $review->comment,
                'created_at' => $review->created_at->diffForHumans(),
                'rating' => $review->rating,
            ];
        })->toArray();

        // If user has a review, put it first
        if ($userReviewData) {
            array_unshift($customerReviewsArray, $userReviewData);
        }

         // Recalculate pagination meta so it accounts for user's review
        $paginationMeta = [
            'current_page' => $paginatedReviews->currentPage(),
            'last_page' => $paginatedReviews->lastPage(),
            'per_page' => $paginatedReviews->perPage(),
            'total' => $total_reviews,
            'has_more_pages' => $paginatedReviews->currentPage() < $paginatedReviews->lastPage(),
            'next_page_url' => $paginatedReviews->currentPage() < $paginatedReviews->lastPage()
                ? $paginatedReviews->nextPageUrl()
                : null,
        ];

         // Special adjustment for page 1 when user review is added on top
        if ($userReview && $paginatedReviews->currentPage() === 1) {
            $paginationMeta['has_more_pages'] = $total_reviews > ($paginatedReviews->perPage() + 1);
            $paginationMeta['next_page_url'] = $paginationMeta['has_more_pages']
                ? $paginatedReviews->url(2)
                : null;
        }

        $reviewsArray = [
            'can_review' => $flag,
            'average_rating' => number_format($average_rating, 1),
            'total_user_reviews' => $total_reviews,
            'five_star_percentage' => round($ratingPercentages[5], 2),
            'four_star_percentage' => round($ratingPercentages[4], 2),
            'three_star_percentage' => round($ratingPercentages[3], 2),
            'two_star_percentage' => round($ratingPercentages[2], 2),
            'first_star_percentage' => round($ratingPercentages[1], 2),
            'user_reviews' => $customerReviewsArray,
            'pagination' => $paginationMeta,
        ];

;
        // Enrollment/cart logic must match Frontend\CourseController::courseDetails (course_exits, btn_text, btn_api_route)
        $data['course_exits'] = 0;
    
        if ($user) {
          
            if($courseTypeValue == 3){       // Live(Standard Course)
             
                $courseIds = Enrollment::where(['user_id' => $user->id, 'course_id' => $data['course']->id, 'status' => ACCESS_PERIOD_ACTIVE])->pluck('course_id')->count();    
            }
            else{       // Self Learning Course
               
                $courseIds = Enrollment::where(['user_id' => $user->id, 'course_id' => $data['course']->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->pluck('course_id')->count();     
            }
            if ($courseIds) {
                $data['course_exits'] = 'enrolled';
            }
            $cartExists = CartManagement::whereUserId($user->id)->whereCourseId($data['course']->id)
            ->where('created_by_type',1)->first();
            if ($cartExists) {
             
                $data['course_exits'] = 'cartList';
            }
        }

        $auth_user_student_id = null;
        $already_requested_enrollment = false;
        if ($user) {
            $getStudent = Student::where('user_id', $user->id)->first();
            if ($getStudent) {
                $auth_user_student_id = $getStudent->id;
                if ($course->course_type == 3) {
                    $already_requested_enrollment = check_request_enrollment($getStudent, $data['course']->id);
                }
            }
        }

        if($data['course_exits'] == 'enrolled') {
            $btnText = __('Go to Course');
            $btnApiRoute = "https://portal.cefonlineacademy.com/mycourses/$course->slug";
        }
        elseif($data['course_exits'] == 'cartList'){
            $btnText = __('Added to Cart');
            $btnApiRoute = null;
        }
        else{
            if($course->status == STATUS_APPROVED){
                if($user){
                    if($user->role == 3){
                        if (($course->course_type == 1 || $course->course_type == 2) && $course->learner_accessibility == 'free') {
                             $btnText = __('Enroll Now');
                             $btnApiRoute = route('student-enroll-course');
                        }
                        else if (($course->course_type == 1 || $course->course_type == 2) && $course->learner_accessibility == 'paid') {
                             $btnText = __('Buy Now');
                             $btnApiRoute = route('student-buy-course');
                        }
                        else if ($course->course_type == 3) {
                             $getStudent = Student::where('user_id',$user->id)->first();
                             if (check_request_enrollment($getStudent,$data['course']->id)){
                                $btnText = __('Already Requested');
                                $btnApiRoute = null; 
                             }
                             else{
                                $btnText = __('Request Enrollment');
                                $btnApiRoute = route('student-get-edit-enrollment-form-data',[$data['course']->id]);
                             }    
                        }  
                    }
                    else{
                        $btnText = null;
                        $btnApiRoute = null;  
                    }
                }
                else{
                    if (($course->course_type == 1 || $course->course_type == 2) && $course->learner_accessibility == 'free') {
                         $btnText = __('Enroll Now');
                         $btnApiRoute = route('student-enroll-course');
                    }
                    else if (($course->course_type == 1 || $course->course_type == 2) && $course->learner_accessibility == 'paid') {
                         $btnText = __('Buy Now');
                         $btnApiRoute = route('student-buy-course');
                    }
                    else if ($course->course_type == 3) {
                         $btnText = __('Request Enrollment');
                        //  $btnApiRoute = route('student-get-edit-enrollment-form-data');
                         $btnApiRoute = route('student-get-edit-enrollment-form-data',[$data['course']->id]);
                    }  
                }
            }
            else{
                $btnText = null; 
                $btnApiRoute = null;
            }
        }




        if($course->course_type == COURSE_TYPE_GENERAL){
            foreach($course->lessons as $key => $lesson){
                $lesson_lectures = array();
                foreach($lesson->lectures as  $lecture){
                
                    if($lecture->lecture_type == 1){ // Free Lectures Guest  User Can Preview Them
                        $free_lecture = __('Free');
                        if($lecture->resource?->type == 'Video' || $lecture->resource?->type == 'video'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif($lecture->resource?->type == 'Youtube' || $lecture->resource?->type == 'youtube'){
                        
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  "https://www.youtube.com/embed/".$lecture->resource?->youtube_url_path;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                          
                        }
                        elseif($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'text'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  $lecture->resource?->text;
                            $img_src =  asset('frontend/assets/img/courses-img/text.svg');
                        }
                        elseif($lecture->resource?->type == 'Image' || $lecture->resource?->type == 'image'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                            $img_src =  asset('frontend/assets/img/courses-img/preview-image.svg');
                        }
                        elseif($lecture->resource?->type == 'PDF' || $lecture->resource?->type == 'pdf'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                            $img_src =  asset('frontend/assets/img/courses-img/file-pdf.svg');
                        }
                        elseif($lecture->resource?->type == 'Slide Document' || $lecture->resource?->type == 'slide document'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name.'/index.html';
                            $img_src =  asset('frontend/assets/img/courses-img/slide-preview.svg');
                        }
                        elseif($lecture->resource?->type == 'Audio' || $lecture->resource?->type == 'audio'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                            $img_src =  asset('frontend/assets/img/courses-img/preview-audio-o.svg');
                        }
                        elseif ($lecture->type == 'Video' || $lecture->type == 'video') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $preview_src =  asset($lecture->file_path);
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif ($lecture->type == 'Youtube' || $lecture->type == 'youtube') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $preview_src =  $lecture->url_path;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif ($lecture->type == 'Image' || $lecture->type == 'image') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $preview_src = asset($lecture->image);
                            $img_src =  asset('frontend/assets/img/courses-img/preview-image.svg');
                        }
                        elseif ($lecture->type == 'PDF' || $lecture->type == 'pdf') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $preview_src = asset($lecture->pdf);
                            $img_src =  asset('frontend/assets/img/courses-img/file-pdf.svg');
                        }
                        elseif ($lecture->type == 'Slide Document' || $lecture->type == 'slide_document') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $preview_src = $lecture->slide_document;
                            $img_src =  asset('frontend/assets/img/courses-img/slide-preview.svg');
                        }
                        elseif ($lecture->type == 'Audio' || $lecture->type == 'audio') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $preview_src = asset($lecture->audio);
                            $img_src =  asset('frontend/assets/img/courses-img/preview-audio-o.svg');
                        }
                        elseif ($lecture->type == 'Text' || $lecture->type == 'text') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $preview_src = $lecture->text;
                            $img_src =  asset('frontend/assets/img/courses-img/text.svg');
                        }
                        else{
                            $lecture_type = null;
                        }
                    }
                    else{   // Paid Lectures Only Registered Users Can Preview Them
                        $free_lecture = __('Locked');
                        if($lecture->resource?->type == 'Video' || $lecture->resource?->type == 'video'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif($lecture->resource?->type == 'Youtube' || $lecture->resource?->type == 'youtube'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'text'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/text.svg');
                        }
                        elseif($lecture->resource?->type == 'Image' || $lecture->resource?->type == 'image'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/preview-image.svg');
                        }
                        elseif($lecture->resource?->type == 'PDF' || $lecture->resource?->type == 'pdf'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/file-pdf.svg');
                        }
                        elseif($lecture->resource?->type == 'Slide Document' || $lecture->resource?->type == 'slide document'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/slide-preview.svg');
                        }
                        elseif($lecture->resource?->type == 'Audio' || $lecture->resource?->type == 'audio'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/preview-audio-o.svg');
                        }
                        elseif ($lecture->type == 'Video' || $lecture->type == 'video') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif ($lecture->type == 'Youtube' || $lecture->type == 'youtube') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif ($lecture->type == 'Image' || $lecture->type == 'image') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $img_src =  asset('frontend/assets/img/courses-img/preview-image.svg');
                        }
                        elseif ($lecture->type == 'PDF' || $lecture->type == 'pdf') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $img_src =  asset('frontend/assets/img/courses-img/file-pdf.svg');
                        }
                        elseif ($lecture->type == 'Slide Document' || $lecture->type == 'slide_document') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $img_src =  asset('frontend/assets/img/courses-img/slide-preview.svg');
                        }
                        elseif ($lecture->type == 'Audio' || $lecture->type == 'audio') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $img_src =  asset('frontend/assets/img/courses-img/preview-audio-o.svg');
                        }
                        elseif ($lecture->type == 'Text' || $lecture->type == 'text') {
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->type;
                            $img_src =  asset('frontend/assets/img/courses-img/text.svg');
                        }
                        else{
                            $lecture_type = null;
                        }
                    }
                  
                    if($lecture_type != null){
                      
                        if($free_lecture == 'Free'){
                            $lesson_lectures[] = [
                                'lecture_icon_src' => $img_src,
                                'lecture_id' => $lecture->id,
                                'lecture_title' => $lecture_title,
                                'lecture_type' => $lecture_type,
                                'lecture_file_duration' => $lecture->file_duration? $lecture->file_duration : null,
                                'lecture_is' => $free_lecture,
                                'lecture_preview_btn_src' => $preview_src,
                            ];
                        }
                        else{
                            $lesson_lectures[] = [
                                'lecture_icon_src' => $img_src,
                                'lecture_id' => $lecture->id,
                                'lecture_title' => $lecture->title,
                                'lecture_type' => $lecture_type,
                                'lecture_file_duration' => $lecture->file_duration? $lecture->file_duration : null,
                                'lecture_is' => $free_lecture,
                                'lecture_preview_btn_src' => null,
                              
                            ];
                        }     
                    }
                   
                }
                $curriculum[] = [
                    // 'lesson_id' => $lesson->id,
                    'lesson_name' => $lesson->name,
                    'lesson_no' => (int)$key + 1,
                    'lesson_lectures' => $lesson_lectures,
                ];
            }
        }


        $studentsEnrolled = Enrollment::where('course_id', $data['course']->id)->count();

        $currencySymbol = function_exists('get_currency_symbol') ? get_currency_symbol() : 'Rs';
        $priceAmount = (float) ($data['course']->price ?? 0);
        $coursePriceFormatted = null;
        if ($priceAmount > 0) {
            $coursePriceFormatted = $currencySymbol . ' ' . ((floor($priceAmount) == $priceAmount)
                ? number_format((int) $priceAmount, 0)
                : number_format($priceAmount, 2));
        }

        $result_array = [
            'course_id' =>  $data['course']->id,
            'course_slug' =>  $data['course']->slug,
            'course_type' =>  $courseType,
            'course_image' => $data['course']->cover_image ? getImageFile($data['course']->cover_image) : getImageFile($data['course']->image),    
            'course_preview_src' => $course_preview_src,
            'course_preview_src_type' => $course_preview_src_type,
            'overview' => $overView,
            'lessons' => $curriculum,
            'reviews' => $reviewsArray,
            'instructors' => $instructorsArray,
            'btn_text' => $btnText,
            'btn_api_route' => $btnApiRoute,
            'students_enrolled' => $studentsEnrolled,
            'course_exits' => $data['course_exits'],
            'auth_role' => $role,
            'already_requested_enrollment' => $already_requested_enrollment,
            'course_learner_accessibility' => $course->learner_accessibility ?? null,
            /** Numeric price for academy (0 = free); formatted label for CTAs */
            'price' => $priceAmount,
            'currency_symbol' => $currencySymbol,
            'course_price' => $coursePriceFormatted,
        ];
     
        return response()->json($result_array);

        // dd($result_array);
        // return response()->json($result_array);
        // return view('frontend.course.course-details', $data);
       } catch (\Throwable $th) {
        dd($th);
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
       }  
    }





    public function courseDetailApi2(Request $request,$slug)
    {
       try {
        $result_array = array();
        if (Auth::guard('sanctum')->check()) {
           $user = auth('sanctum')->user();
           if($user){
                $role = $user->role;
           }
           else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
           }
        }
        else{
            $user = null;
            $role = null;
        }
     
        $isExists = Course::where('slug',$slug)->first();
        if($isExists == null){
            return response()->json(['message' => 'Given Course Slug Does Not Exists'], 302); 
        }
    
        $data['pageTitle'] = "Course Details";
        $data['metaData'] = staticMeta(3);
        $getCourse = Course::whereSlug($slug)->first();
        $courseType = $getCourse->course_type;
     
        $data['course'] = Course::whereSlug($slug)->first();
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
        $authRole = $authUser->role;
        $data['authRole'] = $authRole;
        }
        else{
            $data['authRole'] = null;  
        }
        // dd($data['authRole']);
        $relation = ($authUser) ? getUserRoleRelation($authUser) : NULL;

        if($data['course']->private_mode == 1 && (is_null($authUser) || ($authUser->$relation->organization_id != $data['course']->organization_id && $authUser->id != $data['course']->user_id))){
            abort(403);
        }

        //Start:: Course Review
        $data['reviews'] = Review::whereCourseId($data['course']->id)->latest()->paginate(3);
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
            $cartExists = CartManagement::whereUserId($user->id)->whereCourseId($data['course']->id)->first();
            if ($cartExists) {
                $data['course_exits'] = 'cartList';
            }
        }
        $data['instructor_average_rating'] = getUserAverageRating($data['course']->user_id);
        $instructorCourseIds = Course::where('private_mode', '!=', 1)->publicCatalog()->where('user_id', $data['course']->user_id)->pluck('id')->toArray();

        $data['total_instructor_students'] = Enrollment::whereIn('course_id', $instructorCourseIds)->count();
        $data['countries'] = Country::all();
        $data['total_course_students'] = Enrollment::where('course_id', $data['course']->id)->count();

        $timezones_array = timezones_array();
        $data['timeZonesArray'] = $timezones_array;

        if($courseType == 3){
          $data['courseType'] = $courseType;
          $data['typeCourse'] = 1;
            // return view('frontend.course.standard-course-details', $data);
            // return view('frontend.course.course-details', $data);
        }
        else{
            $data['courseType'] = $courseType;
            $data['typeCourse'] = 2;
            
        }
        $course = $data['course'];
        $course_details_left_content_area = array();
        $course_details_right_content_area = array();
        $overview_tab = array();
        $curriculum_tab = array();
        $discussion_tab = array();
        $review_tab = array();
        $instructor_tab = array();
        $user_reviews = array();
        $course_keypoints = array();

        if(count($course->keyPoints) > 0){
            foreach($course->keyPoints as $key => $point){
                if($key % 2 == 0){
                    $course_keypoints[] = $point->name;
                }
            }
            foreach($course->keyPoints as $key => $point){
                if($key % 2 == 1){
                    $course_keypoints[] = $point->name;
                }
            }
        }
        $overview_tab  = [
            'course_subtitle' => $course->subtitle,
            'what_you_will_learn' => $course_keypoints,
            'course_description' => nl2br($course->description),
        ];
        if(count($data['discussions']) > 0){
            foreach($data['discussions'] as $discussion){
                $discussion_replies = array();
                foreach($discussion->replies as $reply){
                    if($reply->comment_as == 1){
                        $user_type_2 = __('Instructor');
                    }
                    else{
                        $user_type_2 =__('Student');
                    }
                    $discussion_replies[] = [
                        'id' => $reply->id,
                        'user_id' => $reply->user?->id,
                        'user_image' => asset($reply->user->image_path),
                        'user_name' => $reply->user?->name,
                        'user_type' => $user_type_2,
                        'comment' => $reply->comment,
                        'created_at' => $reply->created_at->diffforhumans(),
                    ];
                }
                if($discussion->comment_as == 1){
                    $user_type = __('Instructor');
                }
                else{
                    $user_type =__('Student');
                }
                $discussion_tab[] = [
                    'id' => $discussion->id,
                    'user_id' => $discussion->user?->id,
                    'user_image' => asset($discussion->user->image_path),
                    'user_name' => $discussion->user?->name,
                    'user_type' => $user_type,
                    'comment' => $discussion->comment,
                    'created_at' => $discussion->created_at->diffforhumans(),
                    'total_replies' => count($discussion->replies),
                    'replies' => $discussion_replies,
                ];
            }
        }
        foreach($data['reviews'] as $review){
            $user_reviews[] = [
                'id' => $review->id,
                'user_id' => $review->user?->id,
                'user_image' => asset($review->user->image_path),
                'user_name' => $review->user?->name,
                'created_at' => $review->created_at->diffForHumans(),
                'rating' => $review->rating,
                'comment' => $review->comment,
            ];
        }
        $review_tab = [
            'average_rating' => number_format($course->average_rating, 1),
            'total_user_reviews' => $data['total_user_review'],
            'five_star_percentage' => number_format($data['five_star_percentage'], 2).'%',
            'five_star_count' => $data['five_star_count'],
            'four_star_percentage' => number_format($data['four_star_percentage'], 2).'%',
            'four_star_count' => $data['four_star_count'],
            'three_star_percentage' => number_format($data['three_star_percentage'], 2).'%',
            'three_star_count' => $data['three_star_count'],
            'two_star_percentage' => number_format($data['two_star_percentage'], 2).'%',
            'two_star_count' => $data['two_star_count'],
            'first_star_percentage' => number_format($data['first_star_percentage'], 2).'%',
            'first_star_count' => $data['first_star_count'],
            'user_reviews' => $user_reviews
        ];
        if (isset($course->course_instructors)){
            foreach ($course->course_instructors->where('status', STATUS_APPROVED) as $course_instructor){
                if($course_instructor->user->role != USER_ROLE_ADMIN){
                    if($course_instructor->user->role == USER_ROLE_INSTRUCTOR){
                        $userType = 'instructor';
                        $total_instructor_course =  count($course_instructor->user->$userType->courses);
                        $total_instructor_students = $course_instructor->user->$userType->enrollments?->count();
                    }
                    else if($course_instructor->user->role == USER_ROLE_ORGANIZATION){
                        $userType = 'organization';
                        $total_instructor_course =  count($course_instructor->user?->$userType->courses);
                        $total_instructor_students = $course_instructor->user?->$userType->enrollments?->count();
                    }
                    else{
                        $userType = null;
                        $total_instructor_course =  count($course_instructor->user?->courses);
                        $total_instructor_students = $course_instructor->user?->enrollments?->count();   
                    }

                    $instructor_tab[] =[
                        'user_id' => $course_instructor->user?->id,
                        'user_image' => asset($course_instructor->user?->image_path),
                        'user_name' => $course_instructor->user?->name,
                        'user_type' => $userType,
                        'user_profile_detail_web_route' => route('userProfile',$course_instructor->user?->id),
                        'user_profile_detail_api_route' => route('frontend-course-instructor-profile'),
                        'user_professional_title' => $course_instructor->user?->professional_title,
                        'user_average_rating' => number_format(getUserAverageRating($course_instructor->instructor_id), 1). __(' Rating'),
                        'user_total_students' => $total_instructor_students.__(' Students'),
                        'user_ranking_level' => get_instructor_ranking_level($course_instructor->user?->badges),
                        'user_total_courses' => $total_instructor_course.__(' Courses'),
                        'about_instructor_user' => $course_instructor->user?->$userType->about_me,
                    ];
                }
            }
        }
        if($course->course_type == COURSE_TYPE_GENERAL){
            foreach($course->lessons as $key => $lesson){
                $lesson_lectures = array();
               
                foreach($lesson->lectures as  $lecture){
                   
                    if($lecture->lecture_type == 1){ // Free Lectures Guest  User Can Preview Them
                        $free_lecture = __('Yes');
                        if($lecture->resource?->type == 'Video' || $lecture->resource?->type == 'video'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                        }
                        elseif($lecture->resource?->type == 'Youtube' || $lecture->resource?->type == 'youtube'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  "https://www.youtube.com/embed/".$lecture->resource?->youtube_url_path;
                        }
                        elseif($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'text'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  $lecture->resource?->text;
                        }
                        elseif($lecture->resource?->type == 'Image' || $lecture->resource?->type == 'image'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                        }
                        elseif($lecture->resource?->type == 'PDF' || $lecture->resource?->type == 'pdf'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                        }
                        elseif($lecture->resource?->type == 'Slide Document' || $lecture->resource?->type == 'slide document'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name.'/index.html';
                        }
                        elseif($lecture->resource?->type == 'Audio' || $lecture->resource?->type == 'audio'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                        }
                        else{
                            $lecture_type = null;
                        }
                    }
                    else{   // Paid Lectures Only Registered Users Can Preview Them
                        $free_lecture = __('No');
                        if($lecture->resource?->type == 'Video' || $lecture->resource?->type == 'video'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif($lecture->resource?->type == 'Youtube' || $lecture->resource?->type == 'youtube'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/play.svg');
                        }
                        elseif($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'text'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/text.svg');
                        }
                        elseif($lecture->resource?->type == 'Image' || $lecture->resource?->type == 'image'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/preview-image.svg');
                        }
                        elseif($lecture->resource?->type == 'PDF' || $lecture->resource?->type == 'pdf'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/file-pdf.svg');
                        }
                        elseif($lecture->resource?->type == 'Slide Document' || $lecture->resource?->type == 'slide document'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/slide-preview.svg');
                        }
                        elseif($lecture->resource?->type == 'Audio' || $lecture->resource?->type == 'audio'){
                            $lecture_title = $lecture->title;
                            $lecture_type =  $lecture->resource?->type;
                            $img_src =  asset('frontend/assets/img/courses-img/preview-audio-o.svg');
                        }
                        else{
                            $lecture_type = null;
                        }
                    }
                    if($lecture_type != null){
                        if($free_lecture == 'Yes'){
                            $lesson_lectures[] = [
                                'lecture_id' => $lecture->id,
                                'lecture_title' => $lecture_title,
                                'lecture_type' => $lecture_type,
                                'is_lecture_free' => $free_lecture,
                                'lecture_file_duration' => $lecture->file_duration? $lecture->file_duration : null,
                                'lecture_preview_src' => $preview_src,
                            ];
                        }
                        else{
                            $lesson_lectures[] = [
                                'lecture_id' => $lecture->id,
                                'lecture_title' => $lecture->title,
                                'lecture_type' => $lecture_type,
                                'is_lecture_free' => $free_lecture,
                                'lecture_file_duration' => $lecture->file_duration? $lecture->file_duration : null,
                                'lecture_image_src' => $img_src,
                            ];
                        }     
                    }
                   
                }
                $curriculum_tab[] = [
                    'lesson_id' => $lesson->id,
                    'lesson_name' => $lesson->name,
                    'lesson_lectures' => $lesson_lectures,
                ];
            }
            $course_details_left_content_area = [
                'title' => $course->title,
                'overview_tab' => $overview_tab,
                'curriculum_tab' => $curriculum_tab,
                'discussion_tab' => $discussion_tab,
                'review_tab' => $review_tab,
                'instructor_tab' => $instructor_tab,
            ];
        }
        else{
            $course_details_left_content_area = [
                'title' => $course->title,
                'overview_tab' => $overview_tab,
                'discussion_tab' => $discussion_tab,
                'review_tab' => $review_tab,
                'instructor_tab' => $instructor_tab,
            ]; 
        }
       
        $course_preview_src = null;
        if($course->intro_video_check == 1 &&
                                        getVideoFile($course->video)) {
                $course_preview_src = getVideoFile($course->video);
        }
        if($course->intro_video_check == 2 && $course->youtube_video_id){
                $course_preview_src = "https://www.youtube.com/embed/".$course->youtube_video_id; 
        }

        $courseDiscountPrice = null;
        $coursePrice = null;
        $courseOldPrice = null;
        $courseLeftPrice = null;
        $coursePromotionPercentage = null;
        if($course->learner_accessibility == 'paid') {
            $startDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->start_date));
            $endDate = date('d-m-Y H:i:s', strtotime($course->promotionCourse?->promotion?->end_date));
            $percentage = $course->promotionCourse?->promotion?->percentage;
            $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
            if(now()->gt($startDate) && now()->lt($endDate)) {
                if(get_currency_placement() == 'after'){
                    $courseDiscountPrice = $discount_price.' '.get_currency_symbol();
                }
                else{
                    $courseDiscountPrice = get_currency_symbol().' '.$discount_price; 
                }

                if(get_currency_placement() == 'after'){
                    $coursePrice = $course->price.' '.get_currency_symbol();
                }
                else{
                    $coursePrice = get_currency_symbol().' '.$course->price; 
                }
                $courseOldPrice = $coursePrice;
                $courseLeftPrice = getLeftDuration($startDate, $endDate);
                $coursePromotionPercentage = $percentage.'% off';
                
            }
            elseif($course->price <= $course->old_price){
                $courseDiscountPrice = null;
                if(get_currency_placement() == 'after'){
                    $coursePrice = $course->price.' '.get_currency_symbol();
                }
                else{
                    $coursePrice = get_currency_symbol().' '.$course->price; 
                }
                if(get_currency_placement() == 'after'){
                    $courseOldPrice = $course->old_price.' '.get_currency_symbol();
                }
                else{
                    $courseOldPrice = get_currency_symbol().' '.$course->old_price; 
                }
                $coursePromotionPercentage = number_format(((($course->old_price - $course->price) /
                                            $course->old_price) * 100), 2); 
                $coursePromotionPercentage = $coursePromotionPercentage.'% off';
            }
            else{
                $courseDiscountPrice = null;
                if(get_currency_placement() == 'after'){
                    $coursePrice = $course->price.' '.get_currency_symbol();
                }
                else{
                    $coursePrice = get_currency_symbol().' '.$course->price; 
                }
                $courseOldPrice = $coursePrice;
            }
        }
        elseif($course->learner_accessibility == 'free'){
        }


        if($course->course_type == COURSE_TYPE_GENERAL){
            $course_duration = $course->VideoDuration;
            $videoLectures = $course->lectures->count() > 0 ? $course->video_duration : '0';
        }
        else{
            // $course_duration = $course->scorm_course->duration;
            $course_duration = $course->scorm_course? $course->scorm_course->duration : 0;
            $videoLectures = null;
        }
        $quizzes = $course->quizzes->count().' '.__('Quizzes');
        $assignments = $course->assignments->count().' '.__('Assignments');
        $downloads = $course->resources->count().' '.__('Downloadable Resources');
        $accessPeriod = (!$course->access_period) ? __('Full Lifetime Access') : $course->access_period.' '.__('days after the enrollment');
        if(($user && $role == 3) || $user == null){
            if($course->course_type == 3){
                $courseType = __('Live');
                $course_details_right_content_area= [
                    'course_image' => getImageFile($course->image),
                    'course_type' => $courseType,
                    'course_preview_src' => $course_preview_src,
                    'course_duration' => $course_duration,
                    'course_level' => $course->difficultyLevel?->name,
                    'students_enrolled' => $data['total_course_students'],
                    'course_language' => $course->language->name,
                    'add_to_wishlist_web_route' => route('student.addToWishlist'),
                    'add_to_wishlist_api_route' => route('student-course-add-to-wishlist'),
                    'share_course_api_route' => route('student-course-share'),
                    'course_video_lectures' => $videoLectures,
                    'course_quizzes' => $quizzes,
                    'course_assignments' => $assignments,
                    'course_downloads' => $downloads,
                    'course_accessPeriod' => $accessPeriod,
                ];
            }
            elseif($course->course_type == 2 || $course->course_type == 1){
                if($course->course_type == 2){
                    $courseType = __('Scorm');
                }
                else{
                    $courseType = __('General'); 
                }
                
                $course_details_right_content_area= [
                    'course_image' => getImageFile($course->image),
                    'course_type' => $courseType,
                    'course_preview_src' => $course_preview_src,
                    'course_price' => $coursePrice,
                    'course_old_price' => $courseOldPrice,
                    'course_discount_price' => $courseDiscountPrice,
                    'course_promotion_percentage_off' => $coursePromotionPercentage,
                    'course_left_price' => $courseLeftPrice,
                    'course_learner_accessibility' => $course->learner_accessibility,
                    'course_duration' => $course_duration,
                    'course_level' => $course->difficultyLevel->name,
                    'students_enrolled' => $data['total_course_students'],
                    'course_language' => $course->language->name,
                    'add_to_wishlist_web_route' => route('student.addToWishlist'),
                    'add_to_wishlist_api_route' => route('student-course-add-to-wishlist'),
                    'share_course_api_route' => route('student-course-share'),
                    'course_video_lectures' => $videoLectures,
                    'course_quizzes' => $quizzes,
                    'course_assignments' => $assignments,
                    'course_downloads' => $downloads,
                    'course_accessPeriod' => $accessPeriod,
                ];
            }
        }
      
        $result_array[] = [
            'course_id' => $course->id,
            'course_slug' => $course->slug,
            'course_cover_image' => $course->cover_image ? asset($course->cover_image) : getImageFile($course->image),
            'course_details_left_content_area' => $course_details_left_content_area,
            'course_details_right_content_area' => $course_details_right_content_area,
            'course_exits' => $data['course_exits'],
            'auth_role' => $role,
            'auth_user_student_id' => $auth_user_student_id,
            'already_requested_enrollment' => $already_requested_enrollment,
        ];
        return response()->json($result_array);
        // return view('frontend.course.course-details', $data);
       } catch (\Throwable $th) {
        return response()->json(['status' => 0, 'message' => __('Course detail could not be loaded.')], 500);
       }  
    }








    public function courseInstructorProfileApi(Request $request, User $user)
    {
        try {
            $result_array = array();
        if (Auth::guard('sanctum')->check()) {
           $user = auth('sanctum')->user();
           if($user){
                $role = $user->role;
           }
           else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
           }
        }
        else{
            $user = null;
            $role = null;
        }
        $messages = [
            'user_id.required' => __('The User ID Is Required'),
        ];
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }
        $isExists = User::where('id',$request->user_id)->first();
        if($isExists == null){
            return response()->json(['message' => 'Given User ID Does Not Exists'], 302); 
        }

        if($user->role != USER_ROLE_INSTRUCTOR && $user->role != USER_ROLE_ORGANIZATION){
            return response()->json(['message' => 'Given User ID Does Not Belong To Instructor Role'], 302); 
            // abort(404);
        }

        $userRelation = getUserRoleRelation($user);

        $user->with('followings', 'followers', $userRelation, 'badges');
        $data['courses'] = Course::where('private_mode', '!=', 1)->publicCatalog()->active()->whereUserId($user->id)->paginate(3);
        $data['topCourse'] = Enrollment::query()
                ->whereMonth('created_at', now()->month)
                ->select('course_id', DB::raw('count(*) as total'))
                ->groupBy('course_id')
                ->limit(10)
                ->orderBy('total','desc')
                ->get()
                ->pluck('course_id')
                ->toArray();
        $data['average_rating'] = getUserAverageRating($user->id);
        $courseIds = Course::where('private_mode', '!=', 1)->publicCatalog()->where('user_id', $user->id)->where('status', 1)->select('id')->pluck('id')->toArray();
        $data['total_rating'] = Review::whereIn('course_id', $courseIds)->count();
        $data['totalStudent'] = Enrollment::where('owner_user_id', $user->id)->groupBy('user_id')->count();
        $data['totalMeeting'] = Enrollment::where('owner_user_id', $user->id)->whereNull('consultation_slot_id')->count();
        $data['total_assignments'] = Assignment::whereIn('course_id', $courseIds)->count();
        $data['total_lectures'] = Course_lecture::whereIn('course_id', $courseIds)->count();
        $data['total_quizzes'] = Exam::whereIn('course_id', $courseIds)->count();
        $data['user'] = $user;
        if($user->role == USER_ROLE_INSTRUCTOR){
            $data['pageTitle'] = __('About Instructor');
        }
        elseif($user->role == USER_ROLE_ORGANIZATION){
            $data['pageTitle'] = __('About Organization');
        }
        elseif($user->role == USER_ROLE_STUDENT){
            $data['pageTitle'] = __('About Student');
        }
        if($data['user']->role == USER_ROLE_ORGANIZATION){
            $data['instructors'] = User::join('instructors as ins', 'ins.user_id', '=', 'users.id')->where('ins.status', STATUS_APPROVED)->where('organization_id', $data['user']->organization->id)->select('*', 'users.id')->paginate(3);
            $data['consultationInstructors'] = User::join('instructors as ins', 'ins.user_id', '=', 'users.id')->where('ins.status', STATUS_APPROVED)->where('ins.consultation_available', STATUS_APPROVED)->where('organization_id', $data['user']->organization->id)->select('*', 'users.id')->paginate(3);
        }

        return view('frontend.user-details', $data);


        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }





    }




    public function index()
    {
        $data['metaData'] = staticMeta(1);
        $data['aboutUsGeneral'] = AboutUsGeneral::first();
        $data['instructorSupports'] = InstructorSupport::all();
        $data['home'] = Home::first();
        $data['currencyPlacement'] = get_currency_placement();
        $data['currencySymbol'] = get_currency_symbol();
        return $this->success($data);
    }

    public function settings()
    {
        $setting = config('settings');
        $settings['app_logo'] =  asset($setting['app_logo'] ?? '');
        $settings['app_fav_icon'] = asset($setting['app_fav_icon'] ?? '');
        $settings['bundle_course_logo'] = asset($setting['bundle_course_logo'] ?? '');
        $settings['app_preloader'] = asset($setting['app_preloader'] ?? '');
        $settings['achievement_four_logo'] = asset($setting['achievement_four_logo'] ?? '');
        $settings['achievement_third_logo'] = asset($setting['achievement_third_logo'] ?? '');
        $settings['achievement_second_logo'] = asset($setting['achievement_second_logo'] ?? '');
        $settings['achievement_first_logo'] = asset($setting['achievement_first_logo'] ?? '');
        $settings['customer_say_logo'] = asset($setting['customer_say_logo'] ?? '');
        $settings['customer_say_logo'] = asset($setting['customer_say_logo'] ?? '');
        $settings['sign_up_left_image'] = asset($setting['sign_up_left_image'] ?? '');
        $settings['faq_image'] = asset($setting['faq_image'] ?? '');
        $settings['course_logo'] = asset($setting['course_logo'] ?? '');
        $settings['top_category_logo'] = asset($setting['top_category_logo'] ?? '');
        $settings['top_instructor_logo'] = asset($setting['top_instructor_logo'] ?? '');
        $settings['become_instructor_video'] = getVideoFile($setting['become_instructor_video'] ?? '');
        $settings['become_instructor_video_preview_image'] = asset($setting['become_instructor_video_preview_image'] ?? '');
        $settings['become_instructor_video_logo'] = asset($setting['become_instructor_video_logo'] ?? '');
        $settings['app_name'] = ($setting['app_name'] ?? '');
        $settings['app_email'] = ($setting['app_email'] ?? '');
        $settings['app_contact_number'] = ($setting['app_contact_number'] ?? '');
        $settings['app_location'] = ($setting['app_location'] ?? '');
        $settings['app_date_format'] = ($setting['app_date_format'] ?? '');
        $settings['app_copyright'] = ($setting['app_copyright'] ?? '');
        $settings['app_developed'] = ($setting['app_developed'] ?? '');
        $settings['footer_quote'] = ($setting['footer_quote'] ?? '');
        $settings['sign_up_left_text'] = ($setting['sign_up_left_text'] ?? '');
        $settings['sign_up_left_image'] = ($setting['sign_up_left_image'] ?? '');
        $settings['forgot_title'] = ($setting['forgot_title'] ?? '');
        $settings['forgot_subtitle'] = ($setting['forgot_subtitle'] ?? '');
        $settings['forgot_btn_name'] = ($setting['forgot_btn_name'] ?? '');
        $settings['facebook_url'] = ($setting['facebook_url'] ?? '');
        $settings['twitter_url'] = ($setting['twitter_url'] ?? '');
        $settings['linkedin_url'] = ($setting['linkedin_url'] ?? '');
        $settings['pinterest_url'] = ($setting['pinterest_url'] ?? '');
        $settings['app_instructor_footer_title'] = ($setting['app_instructor_footer_title'] ?? '');
        $settings['app_instructor_footer_subtitle'] = ($setting['app_instructor_footer_subtitle'] ?? '');
        $settings['get_in_touch_title'] = ($setting['get_in_touch_title'] ?? '');
        $settings['send_us_msg_title'] = ($setting['send_us_msg_title'] ?? '');
        $settings['contact_us_location'] = ($setting['contact_us_location'] ?? '');
        $settings['contact_us_email_one'] = ($setting['contact_us_email_one'] ?? '');
        $settings['contact_us_email_two'] = ($setting['contact_us_email_two'] ?? '');
        $settings['contact_us_phone_one'] = ($setting['contact_us_phone_one'] ?? '');
        $settings['contact_us_phone_two'] = ($setting['contact_us_phone_two'] ?? '');
        $settings['contact_us_map_link'] = ($setting['contact_us_map_link'] ?? '');
        $settings['contact_us_description'] = ($setting['contact_us_description'] ?? '');
        $settings['home_special_feature_first_logo'] = asset($setting['home_special_feature_first_logo'] ?? '');
        $settings['home_special_feature_first_title'] = ($setting['home_special_feature_first_title'] ?? '');
        $settings['home_special_feature_first_subtitle'] = ($setting['home_special_feature_first_subtitle'] ?? '');
        $settings['home_special_feature_second_logo'] = asset($setting['home_special_feature_second_logo'] ?? '');
        $settings['home_special_feature_second_title'] = ($setting['home_special_feature_second_title'] ?? '');
        $settings['home_special_feature_second_subtitle'] = ($setting['home_special_feature_second_subtitle'] ?? '');
        $settings['home_special_feature_third_logo'] = asset($setting['home_special_feature_third_logo'] ?? '');
        $settings['home_special_feature_third_title'] = ($setting['home_special_feature_third_title'] ?? '');
        $settings['home_special_feature_third_subtitle'] = ($setting['home_special_feature_third_subtitle'] ?? '');
        $settings['course_title'] = ($setting['course_title'] ?? '');
        $settings['course_subtitle'] = ($setting['course_subtitle'] ?? '');
        $settings['top_category_title'] = ($setting['top_category_title'] ?? '');
        $settings['top_category_subtitle'] = ($setting['top_category_subtitle'] ?? '');
        $settings['top_instructor_title'] = ($setting['top_instructor_title'] ?? '');
        $settings['top_instructor_subtitle'] = ($setting['top_instructor_subtitle'] ?? '');
        $settings['become_instructor_video_title'] = ($setting['become_instructor_video_title'] ?? '');
        $settings['become_instructor_video_subtitle'] = ($setting['become_instructor_video_subtitle'] ?? '');
        $settings['customer_say_logo'] = ($setting['customer_say_logo'] ?? '');
        $settings['customer_say_title'] = ($setting['customer_say_title'] ?? '');
        $settings['customer_say_first_name'] = ($setting['customer_say_first_name'] ?? '');
        $settings['customer_say_first_position'] = ($setting['customer_say_first_position'] ?? '');
        $settings['customer_say_first_comment_title'] = ($setting['customer_say_first_comment_title'] ?? '');
        $settings['customer_say_first_comment_description'] = ($setting['customer_say_first_comment_description'] ?? '');
        $settings['customer_say_first_comment_rating_star'] = ($setting['customer_say_first_comment_rating_star'] ?? '');
        $settings['customer_say_second_name'] = ($setting['customer_say_second_name'] ?? '');
        $settings['customer_say_second_position'] = ($setting['customer_say_second_position'] ?? '');
        $settings['customer_say_second_comment_title'] = ($setting['customer_say_second_comment_title'] ?? '');
        $settings['customer_say_second_comment_description'] = ($setting['customer_say_second_comment_description'] ?? '');
        $settings['customer_say_second_comment_rating_star'] = ($setting['customer_say_second_comment_rating_star'] ?? '');
        $settings['customer_say_third_name'] = ($setting['customer_say_third_name'] ?? '');
        $settings['customer_say_third_position'] = ($setting['customer_say_third_position'] ?? '');
        $settings['customer_say_third_comment_title'] = ($setting['customer_say_third_comment_title'] ?? '');
        $settings['customer_say_third_comment_description'] = ($setting['customer_say_third_comment_description'] ?? '');
        $settings['customer_say_third_comment_rating_star'] = ($setting['customer_say_third_comment_rating_star'] ?? '');
        $settings['achievement_first_title'] = ($setting['achievement_first_title'] ?? '');
        $settings['achievement_first_subtitle'] = ($setting['achievement_first_subtitle'] ?? '');
        $settings['achievement_second_title'] = ($setting['achievement_second_title'] ?? '');
        $settings['achievement_second_subtitle'] = ($setting['achievement_second_subtitle'] ?? '');
        $settings['achievement_third_title'] = ($setting['achievement_third_title'] ?? '');
        $settings['achievement_third_subtitle'] = ($setting['achievement_third_subtitle'] ?? '');
        $settings['achievement_four_title'] = ($setting['achievement_four_title'] ?? '');
        $settings['achievement_four_subtitle'] = ($setting['achievement_four_subtitle'] ?? '');
        $settings['support_faq_title'] = ($setting['support_faq_title'] ?? '');
        $settings['support_faq_subtitle'] = ($setting['support_faq_subtitle'] ?? '');
        $settings['ticket_title'] = ($setting['ticket_title'] ?? '');
        $settings['ticket_subtitle'] = ($setting['ticket_subtitle'] ?? '');
        $settings['allow_preloader'] = ($setting['allow_preloader'] ?? '');
        $settings['registration_email_verification'] = ($setting['registration_email_verification'] ?? '');
        $settings['courseUploadRuleTitle'] = ($setting['courseUploadRuleTitle'] ?? '');
        $settings['course_upload_rules'] = ($setting['course_upload_rules'] ?? '');
        $settings['bundle_course_title'] = ($setting['bundle_course_title'] ?? '');
        $settings['bundle_course_subtitle'] = ($setting['bundle_course_subtitle'] ?? '');
        $settings['bundle_course_logo'] = asset($setting['bundle_course_logo'] ?? '');
        $settings['app_color_design_type'] = ($setting['app_color_design_type'] ?? '');
        $settings['app_theme_color'] = ($setting['app_theme_color'] ?? '');
        $settings['app_navbar_background_color'] = ($setting['app_navbar_background_color'] ?? '');
        $settings['app_body_font_color'] = ($setting['app_body_font_color'] ?? '');
        $settings['app_heading_color'] = ($setting['app_heading_color'] ?? '');
        $settings['app_gradiant_banner_color1'] = ($setting['app_gradiant_banner_color1'] ?? '');
        $settings['app_gradiant_banner_color2'] = ($setting['app_gradiant_banner_color2'] ?? '');
        $settings['app_gradiant_banner_color'] = ($setting['app_gradiant_banner_color'] ?? '');
        $settings['app_gradiant_footer_color1'] = ($setting['app_gradiant_footer_color1'] ?? '');
        $settings['app_gradiant_footer_color2'] = ($setting['app_gradiant_footer_color2'] ?? '');
        $settings['app_gradiant_footer_color'] = ($setting['app_gradiant_footer_color'] ?? '');
        $settings['app_gradiant_overlay_background_color_opacity'] = ($setting['app_gradiant_overlay_background_color_opacity'] ?? '');
        $settings['og_title'] = ($setting['og_title'] ?? '');
        $settings['og_description'] = ($setting['og_description'] ?? '');
        $settings['app_version'] = ($setting['app_version'] ?? '');
        $settings['referral_status'] = ($setting['referral_status'] ?? '');
        $settings['referral_commission_percentage'] = ($setting['referral_commission_percentage'] ?? '');
        $settings['current_version'] = ($setting['current_version'] ?? '');
        $settings['saas_mode'] = ($setting['saas_mode'] ?? '');
        $settings['subscription_mode'] = ($setting['subscription_mode'] ?? '');
        $settings['registration_system_bonus_mode'] = ($setting['registration_system_bonus_mode'] ?? '');
        $settings['registration_bonus_amount'] = ($setting['registration_bonus_amount'] ?? '');
        $settings['refund_system_mode'] = ($setting['refund_system_mode'] ?? '');
        $settings['cashback_system_mode'] = ($setting['cashback_system_mode'] ?? '');
        $settings['cashback_type'] = ($setting['cashback_type'] ?? '');
        $settings['cashback_amount'] = ($setting['cashback_amount'] ?? '');
        $settings['upcoming_course_title'] = ($setting['upcoming_course_title'] ?? '');
        $settings['upcoming_course_subtitle'] = ($setting['upcoming_course_subtitle'] ?? '');
        $settings['upcoming_course_logo'] = asset($setting['upcoming_course_logo'] ?? '');
        $settings['instagram_url'] = ($setting['instagram_url'] ?? '');
        $settings['tiktok_url'] = ($setting['tiktok_url'] ?? '');
        $settings['course_gift_mode'] = ($setting['course_gift_mode'] ?? '');
        $settings['wallet_recharge_system'] = ($setting['wallet_recharge_system'] ?? '');
        $settings['wallet_checkout_system'] = ($setting['wallet_checkout_system'] ?? '');
        $settings['LMSZAIAI_current_version'] = ($setting['LMSZAIAI_current_version'] ?? '');
        $settings['GOOGLE_LOGIN_STATUS'] = env('GOOGLE_LOGIN_STATUS', 0);
        $settings['FACEBOOK_LOGIN_STATUS'] = env('FACEBOOK_LOGIN_STATUS', 0);
        $settings['TWITTER_LOGIN_STATUS'] = env('TWITTER_LOGIN_STATUS', 0);
        return $this->success($settings);
    }

    public function courses()
    {
        $data['topCourse'] = Enrollment::query()
            ->whereMonth('created_at', now()->month)
            ->whereNotNull('course_id')
            ->select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->limit(10)
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('course_id')
            ->toArray();

        $courses = Course::active()->featured()->take(12)->select(['title', 'image', 'user_id', 'instructor_id', 'organization_id', 'slug', 'price', 'old_price', 'id', 'average_rating', 'created_at', 'learner_accessibility'])->with('promotionCourse.promotion')->withCount('reviews')->with(['user', 'instructor', 'organization'])->get();
        foreach($courses as $course){
            $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
            $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
            $percentage = @$course->promotionCourse->promotion->percentage;
            $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);

            if(now()->gt($startDate) && now()->lt($endDate)){
                $filterData['discount_price'] = $discount_price;
                $filterData['price'] = $course->price;
            }elseif($course->price <= $course->old_price){
                $filterData['price'] = $course->old_price;
                $filterData['discount_price'] = $course->price;
            }else{
                $filterData['price'] = $course->price;
                $filterData['discount_price'] = $course->price;
            }

            if($course->learner_accessibility != 'free' && get_option('cashback_system_mode', 0)){
                $filterData['cashback'] = calculateCashback($course->price) ;
            }

            $userRelation = getUserRoleRelation($course->user);

            $filterData['title'] = $course->title;
            $filterData['average_rating'] = $course->average_rating;
            $filterData['slug'] = $course->slug;
            $filterData['id'] = $course->id;
            $filterData['created_at'] = $course->created_at;
            $filterData['image_url'] = $course->image_url;
            $filterData['learner_accessibility'] = $course->learner_accessibility;
            $filterData['total_review'] = $course->reviews_count;
            $filterData['author'] = $course->$userRelation->name;
            $filterData['author_user_id'] = $course->user_id;
            $awards = '';
            foreach($course->$userRelation->awards as $award){
                $awards .= ' | '. $award->name;
            }

            $filterData['author_awards'] = $awards;

            $data['courses'][] = $filterData;
        }

        return $this->success($data);
    }

    public function upcomingCourses()
    {
        $courses = Course::with('reviews')->with('user')->with('promotionCourse.promotion')->with('instructor.ranking_level')->with('specialPromotionTagCourse.specialPromotionTag')->upcoming()->take(12)->get();
        foreach($courses as $course){
            $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
            $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
            $percentage = @$course->promotionCourse->promotion->percentage;
            $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);

            if(now()->gt($startDate) && now()->lt($endDate)){
                $filterData['discount_price'] = $discount_price;
                $filterData['price'] = $course->price;
            }elseif($course->price <= $course->old_price){
                $filterData['price'] = $course->old_price;
                $filterData['discount_price'] = $course->price;
            }else{
                $filterData['price'] = $course->price;
                $filterData['discount_price'] = $course->price;
            }

            if($course->learner_accessibility != 'free' && get_option('cashback_system_mode', 0)){
                $filterData['cashback'] = calculateCashback($course->price) ;
            }

            $userRelation = getUserRoleRelation($course->user);

            $filterData['title'] = $course->title;
            $filterData['slug'] = $course->slug;
            $filterData['id'] = $course->id;
            $filterData['created_at'] = $course->created_at;
            $filterData['image_url'] = $course->image_url;
            $filterData['learner_accessibility'] = $course->learner_accessibility;
            $filterData['author'] = $course->$userRelation->name;
            $filterData['author_user_id'] = $course->user_id;
            $awards = '';
            foreach($course->$userRelation->awards as $award){
                $awards .= ' | '. $award->name;
            }

            $filterData['author_awards'] = $awards;

            $data['courses'][] = $filterData;
        }

        return $this->success($data);
    }

    public function categoryList()
    {
        $data = Category::active()->feature()->get();
        return $this->success($data);
    }

    public function categoryCourse($slug)
    {
        $featureCategory = Category::where('slug', $slug)->with('activeCourses')->with('courses.reviews')->with('courses.user')->with('courses.promotionCourse.promotion')->with('courses.instructor.ranking_level')->with('courses.specialPromotionTagCourse.specialPromotionTag')->first();

        $data['topCourse'] = Enrollment::query()
            ->whereMonth('created_at', now()->month)
            ->whereNotNull('course_id')
            ->select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->limit(10)
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('course_id')
            ->toArray();

        if(count($featureCategory->activeCourses)){
            foreach($featureCategory->activeCourses->take(12) as $course){
                $filterData = [];
                $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
                $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
                $percentage = @$course->promotionCourse->promotion->percentage;
                $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);

                if(now()->gt($startDate) && now()->lt($endDate)){
                    $filterData['discount_price'] = $discount_price;
                    $filterData['price'] = $course->price;
                }elseif($course->price <= $course->old_price){
                    $filterData['price'] = $course->old_price;
                    $filterData['discount_price'] = $course->price;
                }else{
                    $filterData['price'] = $course->price;
                    $filterData['discount_price'] = $course->price;
                }

                if($course->learner_accessibility != 'free' && get_option('cashback_system_mode', 0)){
                    $filterData['cashback'] = calculateCashback($course->price) ;
                }

                $userRelation = getUserRoleRelation($course->user);

                $filterData['title'] = $course->title;
                $filterData['average_rating'] = $course->average_rating;
                $filterData['slug'] = $course->slug;
                $filterData['id'] = $course->id;
                $filterData['created_at'] = $course->created_at;
                $filterData['image_url'] = $course->image_url;
                $filterData['learner_accessibility'] = $course->learner_accessibility;
                $filterData['total_review'] = $course->reviews->count();
                $filterData['author'] = $course->$userRelation->name;
                $filterData['author_user_id'] = $course->user_id;
                $awards = '';

                foreach($course->$userRelation->awards as $award){
                    $awards .= ' | '. $award->name;
                }

                $filterData['author_awards'] = $awards;

                $data['courses'][] = $filterData;
            }
        }
        else{
            $data['courses'] = [];
        }

        return $this->success($data);
    }

    public function faqQuestions()
    {
        $faqQuestions = FaqQuestion::get();
        return $this->success($faqQuestions);
    }

    public function clients()
    {
        $clients = ClientLogo::all();
        return $this->success($clients);
    }

    public function bundleCourses()
    {
        $bundles = Bundle::withCount('bundleCourses')->with('instructor:uuid,first_name,last_name,professional_title,hourly_rate,consultation_available')->with('organization:uuid,first_name,last_name,professional_title,hourly_rate,consultation_available')->active()->latest()->take(12)->get();
        return $this->success($bundles);
    }

    public function instructors()
    {
        $instructors = User::query()
            ->leftJoin('instructors as ins', 'ins.user_id', '=', 'users.id')
            ->leftJoin('organizations as org', 'org.user_id', '=', 'users.id')
            ->whereIn('users.role', [USER_ROLE_INSTRUCTOR, USER_ROLE_ORGANIZATION])
            ->where(function ($q) {
                $q->where('ins.status', STATUS_APPROVED)
                    ->orWhere('org.status', STATUS_APPROVED);
            })
            ->with('badges:name,type,from,to,description')
            ->select('users.*', 'ins.organization_id', DB::raw(selectStatement()))
            ->paginate(8);

        $data = [];
        foreach($instructors as $instructor){
            $data[] = [
                "id" => $instructor->id,
                "name" => $instructor->name,
                "email" => $instructor->email,
                "image_url" => $instructor->image_url,
                "area_code" => $instructor->area_code,
                "mobile_number" => $instructor->mobile_number,
                "email_verified_at" => $instructor->email_verified_at,
                "role" => $instructor->role,
                "phone_number" => $instructor->phone_number,
                "is_affiliator" => $instructor->is_affiliator,
                "organization_id" => $instructor->organization_id,
                "uuid" => $instructor->uuid,
                "professional_title" => $instructor->professional_title,
                "postal_code" => $instructor->postal_code,
                "about_me" => $instructor->about_me,
                "gender" => $instructor->gender,
                "social_link" => $instructor->social_link,
                "slug" => $instructor->slug,
                "remove_from_web_search" => $instructor->remove_from_web_search,
                "is_offline" => $instructor->is_offline,
                "offline_message" => $instructor->offline_message,
                "consultation_available" => $instructor->consultation_available,
                "hourly_rate" => $instructor->hourly_rate,
                "hourly_old_rate" => $instructor->hourly_old_rate,
                "available_type" => $instructor->available_type,
                "approval_date" => $instructor->approval_date,
                "address" => $instructor->address,
                'average_rating' => $instructor->courses->where('average_rating', '>', 0)->avg('average_rating') ?? 0,
                'total_rating' => count($instructor->courses->where('average_rating', '>', 0)),
                'badges' => $instructor->badges,
            ];
        }

        return $this->success($data);
    }

    public function consultationInstructors()
    {
        $consultationInstructors = User::query()
            ->leftJoin('instructors as ins', 'ins.user_id', '=', 'users.id')
            ->leftJoin('organizations as org', 'org.user_id', '=', 'users.id')
            ->whereIn('users.role', [USER_ROLE_INSTRUCTOR, USER_ROLE_ORGANIZATION])
            ->where(function ($q) {
                $q->where('ins.status', STATUS_APPROVED)
                    ->orWhere('org.status', STATUS_APPROVED);
            })
            ->where(function ($q) {
                $q->where('ins.consultation_available', STATUS_APPROVED)
                    ->orWhere('org.consultation_available', STATUS_APPROVED);
            })
            ->with('badges:name,type,from,to,description')
            ->select('users.*', 'ins.organization_id', DB::raw(selectStatement()))
            ->limit(8)
            ->get();

        $data = [];
        foreach($consultationInstructors as $consultationInstructor){
            $data[] = [
                "id" => $consultationInstructor->id,
                "name" => $consultationInstructor->name,
                "email" => $consultationInstructor->email,
                "image_url" => $consultationInstructor->image_url,
                "area_code" => $consultationInstructor->area_code,
                "mobile_number" => $consultationInstructor->mobile_number,
                "email_verified_at" => $consultationInstructor->email_verified_at,
                "role" => $consultationInstructor->role,
                "phone_number" => $consultationInstructor->phone_number,
                "is_affiliator" => $consultationInstructor->is_affiliator,
                "organization_id" => $consultationInstructor->organization_id,
                "uuid" => $consultationInstructor->uuid,
                "professional_title" => $consultationInstructor->professional_title,
                "postal_code" => $consultationInstructor->postal_code,
                "about_me" => $consultationInstructor->about_me,
                "gender" => $consultationInstructor->gender,
                "social_link" => $consultationInstructor->social_link,
                "slug" => $consultationInstructor->slug,
                "remove_from_web_search" => $consultationInstructor->remove_from_web_search,
                "is_offline" => $consultationInstructor->is_offline,
                "offline_message" => $consultationInstructor->offline_message,
                "consultation_available" => $consultationInstructor->consultation_available,
                "hourly_rate" => $consultationInstructor->hourly_rate,
                "hourly_old_rate" => $consultationInstructor->hourly_old_rate,
                "available_type" => $consultationInstructor->available_type,
                "approval_date" => $consultationInstructor->approval_date,
                'average_rating' => $consultationInstructor->courses->where('average_rating', '>', 0)->avg('average_rating') ?? 0,
                'total_rating' => count($consultationInstructor->courses->where('average_rating', '>', 0)),
                'badges' => $consultationInstructor->badges,
            ];
        }

        return $this->success($data);
    }

    public function subscriptions()
    {
        $data['subscriptions'] = Package::where('status', PACKAGE_STATUS_ACTIVE)->where('package_type', PACKAGE_TYPE_SUBSCRIPTION)->where('in_home', PACKAGE_STATUS_ACTIVE)->orderBy('order', 'ASC')->get();
        $data['mySubscriptionPackage'] = UserPackage::where('user_packages.user_id', auth()->id())->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->where('package_type', PACKAGE_TYPE_SUBSCRIPTION)->join('packages', 'packages.id', '=', 'user_packages.package_id')->select('package_id', 'package_type', 'subscription_type')->orderBy('user_packages.id','desc')->first();
        return $this->success($data);
    }

    public function saas()
    {
        $packages = Package::where('status', PACKAGE_STATUS_ACTIVE)->where('in_home', PACKAGE_STATUS_ACTIVE)->whereIn('package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->orderBy('order', 'ASC')->get();
        $data['instructorSaas'] = $packages->where('package_type', PACKAGE_TYPE_SAAS_INSTRUCTOR);
        $data['organizationSaas'] = $packages->where('package_type', PACKAGE_TYPE_SAAS_ORGANIZATION);
        $data['mySaasPackage'] = UserPackage::where('user_packages.user_id', auth()->id())->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->whereIn('package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->join('packages', 'packages.id', '=', 'user_packages.package_id')->select('package_id', 'package_type', 'subscription_type')->orderBy('user_packages.id','desc')->first();
        return $this->success($data);
    }

    public function getCurrentCurrency(){
        $data = get_current_currency();
        return $this->success($data);
    }

    public function getLanguage(){
        $data = appLanguages();
        return $this->success($data);
    }

    public function getLanguageJson($code){
        $data = json_decode(file_get_contents(resource_path('lang/'.$code.'.json')));
        return $this->success($data);
    }

    public function getCountry(){
        $data = Country::all();
        return $this->success($data);
    }

    public function getState($country_id){
        $data = State::where('country_id', $country_id)->get();
        return $this->success($data);
    }

    public function getCity($state_id){
        $data = City::where('state_id', $state_id)->get();
        return $this->success($data);
    }

    public function instructorDetails($userId){
        $instructor = User::query()
            ->leftJoin('instructors as ins', 'ins.user_id', '=', 'users.id')
            ->leftJoin('organizations as org', 'org.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->where(function ($q) {
                $q->where('ins.status', STATUS_APPROVED)
                    ->orWhere('org.status', STATUS_APPROVED);
            })
            ->with('badges:name,type,from,to,description')
            ->with('courses')
            ->select('users.*', 'ins.organization_id', DB::raw(selectStatement()))
            ->first();

        if(is_null($instructor)){
            return $this->error([], __('Data Not Found'));
        }


        $courseIds = Course::where('private_mode', '!=', 1)->publicCatalog()->where('user_id', $instructor->id)->where('status', 1)->select('id')->pluck('id')->toArray();

        $data = [
            "id" => $instructor->id,
            "name" => $instructor->name,
            "email" => $instructor->email,
            "image_url" => $instructor->image_url,
            "area_code" => $instructor->area_code,
            "mobile_number" => $instructor->mobile_number,
            "email_verified_at" => $instructor->email_verified_at,
            "role" => $instructor->role,
            "phone_number" => $instructor->phone_number,
            "is_affiliator" => $instructor->is_affiliator,
            "organization_id" => $instructor->organization_id,
            "uuid" => $instructor->uuid,
            "professional_title" => $instructor->professional_title,
            "postal_code" => $instructor->postal_code,
            "about_me" => $instructor->about_me,
            "gender" => $instructor->gender,
            "social_link" => $instructor->social_link,
            "slug" => $instructor->slug,
            "remove_from_web_search" => $instructor->remove_from_web_search,
            "is_offline" => $instructor->is_offline,
            "offline_message" => $instructor->offline_message,
            "consultation_available" => $instructor->consultation_available,
            "hourly_rate" => $instructor->hourly_rate,
            "hourly_old_rate" => $instructor->hourly_old_rate,
            "available_type" => $instructor->available_type,
            "approval_date" => $instructor->approval_date,
            "address" => $instructor->address,
            'average_rating' => $instructor->courses->where('average_rating', '>', 0)->avg('average_rating') ?? 0,
            'total_rating' => count($instructor->courses->where('average_rating', '>', 0)),
            'badges' => $instructor->badges,
            'totalCourse' => count($instructor->courses),
            'totalStudent' => Enrollment::where('owner_user_id', $instructor->id)->groupBy('user_id')->count(),
            'totalMeeting' => Enrollment::where('owner_user_id', $instructor->id)->whereNull('consultation_slot_id')->count(),
            'total_assignments' => Assignment::whereIn('course_id', $courseIds)->count(),
            'total_lectures' => Course_lecture::whereIn('course_id', $courseIds)->count(),
            'total_quizzes' => Exam::whereIn('course_id', $courseIds)->count(),
        ];

        foreach($instructor->courses as $course){
            $filterData = [];
            $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
            $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
            $percentage = @$course->promotionCourse->promotion->percentage;
            $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);

            if(now()->gt($startDate) && now()->lt($endDate)){
                $filterData['discount_price'] = $discount_price;
                $filterData['price'] = $course->price;
            }elseif($course->price <= $course->old_price){
                $filterData['price'] = $course->old_price;
                $filterData['discount_price'] = $course->price;
            }else{
                $filterData['price'] = $course->price;
                $filterData['discount_price'] = $course->price;
            }

            if($course->learner_accessibility != 'free' && get_option('cashback_system_mode', 0)){
                $filterData['cashback'] = calculateCashback($course->price) ;
            }

            $userRelation = getUserRoleRelation($course->user);

            $filterData['title'] = $course->title;
            $filterData['average_rating'] = $course->average_rating;
            $filterData['slug'] = $course->slug;
            $filterData['id'] = $course->id;
            $filterData['created_at'] = $course->created_at;
            $filterData['image_url'] = $course->image_url;
            $filterData['learner_accessibility'] = $course->learner_accessibility;
            $filterData['total_review'] = $course->reviews->count();
            $filterData['author'] = $course->$userRelation->name;
            $filterData['author_user_id'] = $course->user_id;
            $awards = '';

            foreach($course->$userRelation->awards as $award){
                $awards .= ' | '. $award->name;
            }

            $filterData['author_awards'] = $awards;

            $data['courses'][] = $filterData;
        }

        return $this->success($data);
    }



      public function sendPaymentEmail($payment_id = null,$payment_order_id = null){
     
        $receipt_number = rand(0,9999999);
        $getPaymentData = Order_item::where('order_id',$payment_id)->first();
        $getItems =Order_item::where('order_id',$payment_id)->get();
        if(!$getItems->isEmpty()){
            foreach ($getItems as $key => $item) {
                if($item->course){
                    $name = $item->course?->title;
                }
                else if($item->product){
                    $name = $item->product?->title;
                }
                else{
                    $name = 'Item';
                }


                if($item->month && $item->year){
                    $month = $item->month;
                    $Updated_month = date('F', strtotime("2024-$month-01"));
                    $year = $item->year;
                }
                else if($item->cart){
                    $month = $item->cart?->month;
                    $Updated_month = date('F', strtotime("2024-$month-01"));
                    $year = $item->cart?->year;
                }
                else{
                    $month = date('M',strtotime($item->created_at));
                    $Updated_month = date('F', strtotime("2024-$month-01"));
                    $year = date('Y',strtotime($item->created_at));
                }  

                $monthYear = $Updated_month.'/'.$year;

                $symbol='RS ';
                $amount1 =(int)$item->unit_price;
               
                $Amount2 = $symbol.$amount1;
                $itemsArray[] = [
                    'name' => $name,
                    'monthYear' => $monthYear,
                    'amount' => $Amount2,
                ];
            }
        }

        if($getPaymentData->order->payment_method == 'meezan_bank'){
            $data2['order_payment_date'] = $getPaymentData->order->meezan_payment_date;
            $data2['payment_source'] = $getPaymentData->order->meezan_payment_source;
            $data2['payment_method'] = $getPaymentData->order->payment_method;
        }
        else if($getPaymentData->order->payment_method == 'faysal_bank'){
            $data2['order_payment_date'] = $getPaymentData->order->faysal_payment_date;
            $data2['payment_source'] = $getPaymentData->order->faysal_payment_source;
            $data2['payment_method'] = $getPaymentData->order->payment_method;
        }
        else{
            $data2['order_payment_date'] = date('Y-m-d h:i:s');
            $data2['payment_source'] = 'Card';
            $data2['payment_method'] = $getPaymentData->order?->payment_method;
        }
      
        if($payment_order_id != null){
            $ID = $payment_order_id;
        }
        else{
            // $ID = $getPaymentData->order_id; 
            $ID = 'LMS-P-'.$getPaymentData->order_id; 
        }

        $data2['result'] = $getPaymentData;
        $data2['receipt_number_new']  = $receipt_number;
        $toEmail = $getPaymentData->user?->email;
        if(get_option('app_email')){
            $data2['app_email']  = get_option('app_email');
        }
        else{
            $data2['app_email']  = env('MAIL_FROM_ADDRESS');
        }
        if(get_option('app_contact_number')){
            $data2['app_contact_number']  = get_option('app_contact_number');
        }
        else{
            $data2['app_contact_number']  = env('APP_CONTACT_NUMBER');
        }
        if(get_option('facebook_url') && get_option('facebook_url') != '#'){
            $data2['facebook_url']  = get_option('facebook_url');
        }
        else{
            $data2['facebook_url']  = env('FACEBOOK_URL');
        }
        if(get_option('twitter_url') && get_option('twitter_url') != '#'){
            $data2['twitter_url']  = get_option('twitter_url');
        }
        else{
            $data2['twitter_url']  = env('TWITTER_URL');
        }
        if(get_option('instagram_url') && get_option('instagram_url') != '#'){
            $data2['instagram_url']  = get_option('instagram_url');
        }
        else{
            $data2['instagram_url']  = env('INSTAGRAM_URL');
        }
        if(get_option('youtube_url') && get_option('youtube_url') != '#'){
            $data2['youtube_url']  = get_option('youtube_url');
        }
        else{
            $data2['youtube_url']  = env('YOUTUBE_URL');
        }
        if(get_option('app_location')){
            $data2['app_location']  = get_option('app_location');
        }
        else{
            $data2['app_location']  = env('APP_LOCATION');
        }
        $mainArray = array();
        $mainArray = [
            'data2' => $data2,
            'Items' => $itemsArray,
            'ID' => $ID,
        ];
        // $view = view('frontend.student.emails.completed_payment',compact('data2'));
     
        $adminEmail = get_option('admin_email');
        $FinancialEmail = get_option('financial_email');
      
        // multi-select JSON emails
        $otherEmails = json_decode(get_option('other_emails'), true) ?? [];
    
        // build CC list safely
        $ccEmails = array_filter(array_unique(array_merge(
            [$adminEmail, $FinancialEmail],
            $otherEmails
        )));

        Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$ccEmails) {
                $message->to($toEmail)
                ->subject('Payment Received | CEF Online');

                 // add CC only if exists
                if (!empty($ccEmails)) {
                    $message->cc($ccEmails);
                }
        });


        // if($adminEmail && $FinancialEmail){
        //     Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$adminEmail,$FinancialEmail) {
        //         $message->to($toEmail)
        //                 ->cc([
        //                     // 'bankalerts@cef.org.pk',
        //                     // 'quranedu.online@cef.org.pk',
        //                     $adminEmail,
        //                     $FinancialEmail,
                        
                            
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else if($adminEmail){
        //      Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$adminEmail) {
        //         $message->to($toEmail)
        //                 ->cc([
        //                     // 'bankalerts@cef.org.pk',
        //                     // 'quranedu.online@cef.org.pk',
        //                     $adminEmail,
        //                     'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else if($FinancialEmail){
        //      Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$FinancialEmail) {
        //         $message->to($toEmail)
        //                 ->cc([
        //                     // 'bankalerts@cef.org.pk',
        //                     // 'quranedu.online@cef.org.pk',
        //                     $FinancialEmail,
        //                     'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else{
        //     Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail) {
        //         $message->to($toEmail)
        //          ->cc([
                        
        //                     'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        
        return true;
}




     public function sendPaymentEmailWithTransactionID(Request $request)
    {
        try {
        
        $messages = [
            'transaction_id.required' => __('The Transaction ID Is Required'),
        ];
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],302);
        }
        $transaction_id = $request->transaction_id;
        $getCheck = Order::where('faysal_transaction_id',$transaction_id)
        ->orWhere('meezan_transaction_id',$transaction_id)
        ->orWhere('payment_id',$transaction_id)
        ->first();

        if($getCheck == null){
             return response()->json(['message' => 'Given Transaction ID Does not exists in records'], 302); 
        }
        $order_id = $getCheck->id;
          $faysal_order_id = $getCheck->faysal_order_id;
        $cart_id = $getCheck->cart_id;
         $created_by_type = $getCheck->created_by_type;
           $cart_type = $getCheck->cart_type;

        $email_sent = $this->sendPaymentEmail($order_id,$faysal_order_id);
        if($email_sent){
             return response()->json(['message' => 'Mail Sent'], 200); 
        }
        else{
               return response()->json(['message' => 'Mail Not Sent'], 500); 
        }
     


        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }





    }




        public function getPendingOrders(Request $request)
    {
        try {
        
            $result_array = array();
        $messages = [
            'payment_method.required' => __('The Payment Method Is Required'),
        ];
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:meezan_bank,faysal_bank',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],302);
        }
        $payment_method = $request->payment_method;
        $getRecords = Order::where('payment_method',$payment_method)
        ->Where('created_by_type',2)
        ->orWhere('cart_type',2)
        ->orderBy('id','DESC')
        ->get();

        if($getRecords->isEmpty()){
             return response()->json(['message' => 'Given Transaction ID Does not exists in records'], 302); 
        }
      
        foreach ($getRecords as $key => $value) {
              $result_array [] = [
                'id'=> $value->id,
                'cart_id'=> $value->cart_id,
                'user_id'=> $value->user_id,
                'user_name'=> $value->user?->name,
                'user_email'=> $value->user?->email,
                'order_number'=> $value->order_number,
                'sub_total'=> $value->sub_total,
                'grand_total'=> $value->grand_total,
                'payment_method'=> $value->payment_method,
                'payment_status'=> $value->payment_status,
                'delivery_status'=> $value->delivery_status,
                'created_by_type'=> $value->created_by_type,
                'month'=> $value->month,
                'year'=> $value->year,
                'meezan_transaction_id'=> $value->meezan_transaction_id,
                'meezan_payment_source'=> $value->meezan_payment_source,
                'meezan_payment_date'=> $value->meezan_payment_date,
                'meezan_order_number'=> $value->meezan_order_number,
                'meezan_order_id'=> $value->meezan_order_id,
                'meezan_trans_response'=> $value->meezan_trans_response,
                'faysal_transaction_id'=> $value->faysal_transaction_id,
                'faysal_payment_source'=> $value->faysal_payment_source,
                'faysal_payment_date'=> $value->faysal_payment_date,
                'faysal_order_id'=> $value->faysal_order_id,
            
            ];
        }
      
             return response()->json(['message' => 'Results Retrieved','data'=>$result_array], 200); 


        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }





    }



          public function getClassDetails(Request $request)
    {
        try {
        
        $messages = [
            'lesson_id.required' => __('The Lesson ID Is Required'),
            'lesson_id.numeric' => __('The Lesson ID Must Be Numeric'),
            'group_id.numeric' => __('The Group ID Must Be Numeric'),
        ];
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|numeric',
            'group_id' => 'nullable|numeric',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],302);
        }
        $lesson_id = $request->lesson_id;
        $check = ClassDetail::where('id',$lesson_id)
        ->first();
        if($check == null){
            return response()->json(['message' => 'Invalid Lesson ID.'], 302); 
        }

        if($request->group_id){
            $check = ClassDetail::where('group_id',$request->group_id)
            ->get();
            if($check->isEmpty()){
                return response()->json(['message' => 'Invalid Group ID.'], 302); 
            }
        }
      
             return response()->json(['message' => 'Result Retrieved','data'=>$check], 200); 


        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }





    }





      public function sendPaymentEmailWithOrderID(Request $request)
    {
        try {
        
        $messages = [
            'order_id.required' => __('The Order ID Is Required'),
            'order_id.numeric' => __('The Order ID Must Be Numeric'),
        ];
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|numeric',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],302);
        }
        $order_id = $request->order_id;
        $getCheck = Order::where('id',$order_id)
        ->first();

        if($getCheck == null){
             return response()->json(['message' => 'Given Order ID Does not exists in records'], 302); 
        }
        $order_id = $getCheck->id;
          $faysal_order_id = $getCheck->faysal_order_id;
    
        $email_sent = $this->sendPaymentEmail($order_id,$faysal_order_id);
        if($email_sent){
             return response()->json(['message' => 'Mail Sent'], 200); 
        }
        else{
               return response()->json(['message' => 'Mail Not Sent'], 500); 
        }
     


        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }





    }


}
