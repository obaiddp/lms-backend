<?php

use App\Mail\EmailNotify;
use App\Models\Addon\AI\OpenAIPrompt;
use App\Models\AffiliateHistory;
use App\Models\BookingHistory;
use App\Models\Bundle;

use GuzzleHttp\Client;

use App\Models\CartManagement;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use App\Models\Course_lecture;
use App\Models\CourseInstructor;
use App\Models\Currency;
use App\Models\TrialClass;
use App\Models\AffiliateRequest;

use App\Models\Policy;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\InstructorCoordinator;
use App\Models\LiveClassStudentLink;
use App\Models\ForumPostComment;
use App\Models\Instructor;
use App\Models\Coordinator;
use App\Models\InstructorConsultationDayStatus;
use App\Models\Language;
use App\Models\Meta;
use App\Models\Exam;
use App\Models\Take_exam;
use App\Models\Answer;
use App\Models\Question;
use App\Models\NotificationEmailTemplate;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\RankingLevel;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Lesson;
use App\Models\ClassDetail;
use App\Models\UserBadge;
use App\Models\Withdraw;
use App\Models\ZoomSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\UserPackage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use App\Models\Student;
// use DateTimeZone;
// use DateTime;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
// use IntlTimeZone;
use App\Services\GoogleMeetService;
use App\Services\GoogleOAuthService;


function timezones_array(){
    $continents = [
        'Africa'     => DateTimeZone::AFRICA,
        'America'    => DateTimeZone::AMERICA,
        'Antarctica' => DateTimeZone::ANTARCTICA,
        'Arctic'     => DateTimeZone::ARCTIC,
        'Asia'       => DateTimeZone::ASIA,
        'Atlantic'   => DateTimeZone::ATLANTIC,
        'Australia'  => DateTimeZone::AUSTRALIA,
        'Europe'     => DateTimeZone::EUROPE,
        'Indian'     => DateTimeZone::INDIAN,
        'Pacific'    => DateTimeZone::PACIFIC,
    ];
    $output = [];
    foreach ($continents as $continent => $mask) {
        $timezones = DateTimeZone::listIdentifiers($mask);
        foreach ($timezones as $timezone) {
            $output[$timezone] = formatTimezone($timezone, null, false);
        }
    }
    return $output;
}

    function formatTimezone($timezone, $cutOffContinent = null, $htmlencode = true)
    {
        $offsetPrefix = 'GMT/UTC';
        $displayedTimezone  = empty($cutOffContinent) ? $timezone : substr($timezone, strlen($cutOffContinent) + 1);
        $normalizedTimezone = normalizeTimezone($displayedTimezone, $htmlencode);

        
        $notmalizedOffset = normalizeOffset(getOffset($timezone), $htmlencode);
        $separator        = normalizeSeparator($htmlencode);

        return  $normalizedTimezone . $separator . '(' . $offsetPrefix . $notmalizedOffset . ')';
    }

    function normalizeTimezone($timezone, $htmlencode = true)
    {
        $search  = ['St_', '/', '_'];
        $replace = ['St. ', ' / ', ' '];

        return str_replace($search, $replace, $timezone);
    }

    function normalizeOffset($offset, $htmlencode = true)
    {
        $MINUS      = '&#8722;';
        $PLUS       = '&#43;';
        $search  = ['-', '+'];
        $replace = $htmlencode ? [' ' . $MINUS . ' ', ' ' . $PLUS . ' '] : [' - ', ' + '];
        return str_replace($search, $replace, $offset);
    }

    function getOffset($timezone)
    {
        $time = new DateTime('', new DateTimeZone($timezone));
        return $time->format('P');
    }

    function normalizeSeparator($htmlencode = true)
    {
        $WHITESPACE = '&#160;';
        return $htmlencode ? str_repeat($WHITESPACE, 5) : ' ';
    }

function checkfreeRecordTime($created_date){
    $todayDate = date('Y-m-d');
    $givenDate = Carbon::createFromFormat('Y-m-d H:i:s', $created_date);
    $currentDate = Carbon::createFromFormat('Y-m-d',  $todayDate);
    $dateThreeDaysAgo = $currentDate->subDays(3);

    if ($givenDate->greaterThanOrEqualTo($dateThreeDaysAgo)) {
        return true;
        // return response()->json(['message' => 'Given date is greater than or equal to 3 days prior to the current date.']);
    } else {
        return false;
        // return response()->json(['message' => 'Given date is not greater than or equal to 3 days prior to the current date.']);
    }
}

function getStudentURI($live_class_id,$student_id){
    $StudentURI = null;
    $find = LiveClassStudentLink::where('live_class_id',$live_class_id)->where('student_id',$student_id)->first();
    if($find){
        $StudentURI = $find->StudentURI;
    }
    return $StudentURI;
}



function getAcademicURI($live_class_id,$student_id){
    $StudentURI = null;
    $find = LiveClass::where('id',$live_class_id)->where('student_id',$student_id)->first();
    if($find){
        $StudentURI = $find->StudentURI;
    }
    return $StudentURI;
}


function changetimeByZone($date_time,$id,$level)
{
    $timezone = null;
    if($level == 'student'){
        $getStudentData = Student::where('id',$id)->first();
        if($getStudentData){
        $user_id = $getStudentData->user_id;
        }
        else{
             $user_id = null; 
        }

        if( $user_id){
          $getData = User::where('id',$user_id)->first();
          $timezone = $getData->time_zone;
        }
    
    }
    else if($level == 'instructor'){
           $getTutorData = Instructor::where('id',$id)->first();
             if($getTutorData){
        $user_id = $getTutorData->user_id;
        }
        else{
            $user_id = null; 
        }
        if( $user_id){
            $getData = User::where('id',$user_id)->first();
            $timezone = $getData->time_zone;
          }
    }
    else if($level == 'coordinator'){
            $getData = Coordinator::where('id',$id)->first();
              if($getData){
         $user_id = $getData->user_id;
         }
         else{
             $user_id = null; 
         }

         if( $user_id){
             $getData = User::where('id',$user_id)->first();
             $timezone = $getData->time_zone;
           }
    }
      
        if($timezone){
        }
        else{
$timezone = 'Asia/Karachi';
        }
     
        $datetime = new DateTime($date_time);
 
        $la_time = new DateTimeZone($timezone);
        $datetime->setTimezone($la_time);
        // $time = $datetime->format('h:i A');
        $time = $datetime->format('Y-m-d h:i A');

    return $time;
}







function getCurrentServerTimeByZone($id,$level)
{
    $timezone = null;
    if($level == 'student'){
        $getStudentData = Student::where('id',$id)->first();
        if($getStudentData){
        $user_id = $getStudentData->user_id;
        }
        else{
             $user_id = null; 
        }

        if( $user_id){
          $getData = User::where('id',$user_id)->first();
          $timezone = $getData->time_zone;
        }
    }
        else if($level == 'instructor'){
           $getTutorData = Instructor::where('id',$id)->first();
             if($getTutorData){
        $user_id = $getTutorData->user_id;
        }
        else{
            $user_id = null; 
        }
        if( $user_id){
            $getData = User::where('id',$user_id)->first();
            $timezone = $getData->time_zone;
          }
        }
        else if($level == 'coordinator'){
            $getData = Coordinator::where('id',$id)->first();
              if($getData){
         $user_id = $getData->user_id;
         }
         else{
             $user_id = null; 
         }
         if( $user_id){
             $getData = User::where('id',$user_id)->first();
             $timezone = $getData->time_zone;
           }
         }
      
        if($timezone){
        }
        else{
$timezone = 'Asia/Karachi';
        }
    
        $datetime = new DateTime('@' . time());
       
        $la_time = new DateTimeZone($timezone);
        $datetime->setTimezone($la_time);
        $time = $datetime->format('h:i A');
    return $time;
}

    function updateLessonStatusMissedZoom($lesson_id,$zoom_meeting_id = null,$group_id = null)
    {
        $today_date= date("Y-m-d");
        if($group_id != null){
            $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$today_date)->get();
            foreach ($getarray as $key => $value) {
                $l_id = $value->id;
                $find = ClassDetail::where('id',$l_id)->where('meeting_id',$zoom_meeting_id)->first();
                // $find->status = 'Missed';
                // $res = $find->update();
            }
        }
        else{
            if($zoom_meeting_id != null){
                $find = ClassDetail::where('id',$lesson_id)->where('meeting_id',$zoom_meeting_id)->first();
                // $find->status = 'Missed';
                // $res = $find->update();
            }
            else{
                $find = ClassDetail::where('id',$lesson_id)->first();
                // $find->status = 'Missed';
                // $res = $find->update();
            }
        }

        return $res ?? 'N/A';        
    }   




    function updateLessonStatusCompleteZoom($lesson_id,$zoom_meeting_id = null,$group_id = null)
    {
        $today_date= date("Y-m-d");
        if($group_id != null){
            $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$today_date)->get();
            foreach ($getarray as $key => $value) {
                $l_id = $value->id;
                $find = ClassDetail::where('id',$l_id)->where('meeting_id',$zoom_meeting_id)->first();
                // $find->status = 'Completed';
                // $res = $find->update();
            }
        }
        else{
            if($zoom_meeting_id != null){
                $find = ClassDetail::where('id',$lesson_id)->where('meeting_id',$zoom_meeting_id)->first();
                // $find->status = 'Completed';
                // $res = $find->update();
            }
            else{
                $find = ClassDetail::where('id',$lesson_id)->first();
                // $find->status = 'Completed';
                // $res = $find->update();
            }
        }

        return $res ?? 'N/A';            
    }   



    function updateLessonStatusCompleteZoomInstructor($lesson_id,$zoom_meeting_id = null,$group_id = null)
    {
        $today_date= date("Y-m-d");
    
            if($zoom_meeting_id != null){
                $find = LiveClass::where('id',$lesson_id)->where('meeting_id',$zoom_meeting_id)->first();
                // $find->status = 'Completed';
                // $res = $find->update();
            }
            else{
                $find = LiveClass::where('id',$lesson_id)->first();
                // $find->status = 'Completed';
                // $res = $find->update();
            }
        

        return $res ?? 'N/A';            
    }   


    function updateLessonStatusMissedZoomInstructor($lesson_id,$zoom_meeting_id = null,$group_id = null)
    {
        $today_date= date("Y-m-d");
       
            if($zoom_meeting_id != null){
                $find = LiveClass::where('id',$lesson_id)->where('meeting_id',$zoom_meeting_id)->first();
                // $find->status = 'Missed';
                // $res = $find->update();
            }
            else{
                $find = LiveClass::where('id',$lesson_id)->first();
                // $find->status = 'Missed';
                // $res = $find->update();
            }
     

        return $res ?? 'N/A';        
    } 


    function generateUniqueMeetingId($existingMeetings = [])
    {
        do {
            $meeting_id = generateUniqueNumericHash(); 
        } while (
            // Check against existing meetings to ensure absolute uniqueness
            ClassDetail::where('meeting_id', $meeting_id)->exists() ||  LiveClass::where('meeting_id', $meeting_id)->exists() ||
            in_array($meeting_id, $existingMeetings)
        );
    
        return $meeting_id;
    }



    function generateUniqueMeetingIdLiveClass($existingMeetings = [])
    {
        do {
            $meeting_id = generateUniqueNumericHash(); 
        } while (
            // Check against existing meetings to ensure absolute uniqueness
            ClassDetail::where('meeting_id', $meeting_id)->exists() ||  LiveClass::where('meeting_id', $meeting_id)->exists() ||
            in_array($meeting_id, $existingMeetings)
        );
    
        return $meeting_id;
    }



    function generateUniqueBBBMeetingId($existingMeetings = [])
    {
        do {
            $meeting_id = Uuid::uuid4()->toString(); 
        } while (
            // Check against existing meetings to ensure absolute uniqueness
            ClassDetail::where('meeting_id', $meeting_id)->exists() ||  LiveClass::where('meeting_id', $meeting_id)->exists() ||
            in_array($meeting_id, $existingMeetings)
        );
    
        return $meeting_id;
    }



    function generateUniqueBBBMeetingIdLiveClass($existingMeetings = [])
    {
        do {
            $meeting_id = Uuid::uuid4()->toString(); 
        } while (
            // Check against existing meetings to ensure absolute uniqueness
            ClassDetail::where('meeting_id', $meeting_id)->exists() ||  LiveClass::where('meeting_id', $meeting_id)->exists() ||
            in_array($meeting_id, $existingMeetings)
        );
    
        return $meeting_id;
    }




    function generateUniqueNumericHash(int $minLength = 5, int $maxLength = 10): string
    {
        // Validate and adjust length parameters
        $minLength = max(5, $minLength);
        $maxLength = min(10, $maxLength);
        
        if ($minLength > $maxLength) {
            [$minLength, $maxLength] = [$maxLength, $minLength];
        }

        // Get current timestamp with microseconds
        $timestamp = intval(microtime(true) * 1000000);

        // Generate a random seed
        $randomSeed = random_int(0, 999999);

        // Combine timestamp and random seed
        $combinedValue = $timestamp + $randomSeed;

        // Determine hash length
        $hashLength = random_int($minLength, $maxLength);

        // Convert to string
        $hash = (string)$combinedValue;

        // Truncate or pad the hash
        if (strlen($hash) > $hashLength) {
            $hash = substr($hash, -$hashLength);
        }
        
        while (strlen($hash) < $hashLength) {
            $hash = random_int(0, 9) . $hash;
        }

        // Ensure first digit is not zero
        if ($hash[0] === '0') {
            $hash = random_int(1, 9) . substr($hash, 1);
        }

        return $hash;
    }


    function generateAdvancedPasscode($length = 6) {
        // Combine current timestamp with random number
        $timestamp = time();
        $random = rand(10000, 999999999);
        return substr($timestamp . $random, -$length);
    }

    
    // function checkBBBMeetingIDExists($meetingID,$getAllMeetings = array()){
    //     $isFound = false;
    //     if(!empty($getAllMeetings)){
    //         foreach ($getAllMeetings as $key => $Meeting) {
    //             if($Meeting->meetingID == $meetingID){
    //                 $isFound = true;
    //                 break;
    //             }
    //         }
    //     }
    //     return $isFound;
    // }

    function checkuniqueid($meetingID,$getAllMeetings = array())
    {
      
        $meeting_id = $meetingID;
        $isFound = 0;
        while (true) {
            $check_data_exists = ClassDetail::where('meeting_id',$meeting_id)->first();
            if($check_data_exists){
                // $meeting_id = Uuid::uuid4()->toString();
                // $meeting_id=rand(1,9999999);
                $meeting_id = generateAdvancedPasscode();
            }
            else{
                if(!empty($getAllMeetings)){
                    foreach ($getAllMeetings as $key => $Meeting) {
                        if($Meeting->meetingID == $meetingID){
                            // $meeting_id = Uuid::uuid4()->toString();
                            // $meeting_id=rand(1,9999999);
                            $meeting_id = generateAdvancedPasscode();
                            $isFound = 1;
                            break;
                        }
                    }
                }
                if(!$isFound){
                    break;
                }          
            }
            $isFound = 0;
        }
        return $meeting_id;
    }   



    function checkuniqueidliveclass($meetingID,$getAllMeetings = array())
    {
       
        $meeting_id = $meetingID;
        $isFound = 0;
        while (true) {
            $check_data_exists = LiveClass::where('meeting_id',$meeting_id)->first();
            if($check_data_exists){
                // $meeting_id=rand(1,9999999);
                $meeting_id = generateAdvancedPasscode();
            }
            else{
                if(!empty($getAllMeetings)){
                    foreach ($getAllMeetings as $key => $Meeting) {
                        if($Meeting->meetingID == $meetingID){
                            // $meeting_id = Uuid::uuid4()->toString();
                            // $meeting_id=rand(1,9999999);
                            $meeting_id = generateAdvancedPasscode();
                            $isFound = 1;
                            break;
                        }
                    }
                }
                if(!$isFound){
                    break;
                }          
            }
            $isFound = 0;
        }
        return $meeting_id;
    }   

    function startmeetclassindividual($l_id,$studentid,$instructorid,$meeting_id=null,$group_id = null,$learning_tool = null,$is_created = 1)
{
        Log::info("Entered helper(startmeetclassindividual)");
        Log::info("Entered helper(startmeetclassindividual) With Class id {$l_id} and met_id {$meeting_id}");
        Log::info("<!-----------------------------------------------------------------------------------!>");
            $todayDate = date('Y-m-d');
            $is_group = 'No';
       
    
            if(!empty($l_id)){
        if($learning_tool == 'Google_Meet'){
        if($meeting_id != null){
                    // Validate the meeting ID before proceeding
        if (ClassDetail::where('meeting_id', $meeting_id)->exists()) {
            Log::info("Closing helper(startmeetclassindividual) With Class id {$l_id} and met_id {$meeting_id}.  met_id/MEETING_ID ALREADY EXISTS");
            Log::info("<!-----------------------------------------------------------------------------------!>");
            return false; // Reject if meeting ID already exists
        }
    
    
                $getStudentDetail = Student::where('id',$studentid)->first();
                $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
                $studentID = $getStudentDetail->id ;
                $studentType = "Student" ;
    
                $getInstructorDetail = Instructor::where('id',$instructorid)->first();
                $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
                $tutorID = $getInstructorDetail->id ;
                $tutorType = "Instructor" ;
    
                    $academicName = "Quran Education Coordinator";
                    $academicType = "Academic" ;  
                    $academicID = null ; 
                
                $ldata['meeting_id'] = $meeting_id;
        
                if($group_id ==  null){
                    $getLessonDetail2 = ClassDetail::where('id',$l_id)->where('class_date',$todayDate)->first();
                    $googleMeetLink = $getLessonDetail2->google_meet_link;
                    $LessonDurationZoom = $getLessonDetail2->class->duration;
                    $LessonDateZoom = date('Y-m-d',strtotime($getLessonDetail2->class_date));
                    $LessonTimeZoom = date("H:i:s", strtotime($getLessonDetail2->class_time));
                    $resultStartTimeZoom = $LessonDateZoom . ' ' . $LessonTimeZoom;
                
                    $newDate = date("Y-m-d", strtotime($getLessonDetail2->class_date));
                    $newTime = "T" . $LessonTimeZoom;
                    $startingTime = $newDate . $newTime;

                    $check_old_record = ClassDetail::where('id',$l_id)->first();
    
                   
                    $old_end_time = $check_old_record->end_time_zoom;
                    $lessonDuration = $check_old_record->class->duration;
                    if($old_end_time != null){
                    }
                    else{
                        $updated_current_time = getCurrentServerTimeByZone($instructorid,'instructor');
                        $given_start_time = strtotime($updated_current_time);
                        $duration = $lessonDuration * 60;
                        $given_end_time = $given_start_time + $duration; // Calculate the end time   
                        $ldata['end_time_zoom'] = $given_end_time;
                        $check_old_record->end_time_zoom = $given_end_time;
                    }

                    if($is_created == 0){
                        $check_old_record->meeting_id = $meeting_id;
                        $instructorInfo = Instructor::where('id',$check_old_record->instructor_id)->first();
                        if($instructorInfo){
                            $instructor_user_id = $instructorInfo->user_id;
                        }
                        else{
                            $instructor_user_id = 0;
                        }
                        $meetService = new GoogleMeetService(new GoogleOAuthService, new ClassDetail,$instructor_user_id);
                        $coordinator_ids = array();
                        $coordinator_user_ids = array();
                        $courseInfo = Course::where('id',$check_old_record->course_id)->first();
                        $studentInfo = Student::where('id',$check_old_record->student_id)->first();
                        $getInstructorCoordinators = InstructorCoordinator::where('instructor_id',$check_old_record->instructor_id)->get();
                        if($courseInfo){
                            $courseNameMeet = "Course: ".$courseInfo->title;
                        }
                        else{
                            $courseNameMeet = "CEF ONLINE Course";
                        }
                        if($studentInfo){
                            $student_user_id = $studentInfo->user_id;
                        }
                        else{
                            $student_user_id = 0;
                        }
                        if(!$getInstructorCoordinators->isEmpty()){
                            foreach ($getInstructorCoordinators as $key => $ins_coor) {
                                $coordinator_ids[] = $ins_coor->coordinator_id;
                            }
                            $coordinator_ids = array_unique($coordinator_ids);
                            $coordinatorsInfo = Coordinator::whereIn('id',$coordinator_ids)->get();
                            if(!$coordinatorsInfo->isEmpty()){
                                foreach ($coordinatorsInfo as $key => $coor_info) {
                                    $coordinator_user_ids[] = $coor_info->user_id;
                                }
                                $coordinator_user_ids = array_unique($coordinator_user_ids);
                            }
                        }


                        // Build base timestamps
                        $startTimeParsed = Carbon::parse($check_old_record->class_date . ' ' . $check_old_record->start_time);
                        $endTimeParsed = Carbon::parse($check_old_record->class_date . ' ' . $check_old_record->end_time);

                        // Fix: If end time is before or equal to start time, assume it’s on the next day
                        if ($endTimeParsed->lessThanOrEqualTo($startTimeParsed)) {
                            $endTimeParsed->addDay(); // move to next day
                        }
                            
                        $classData = [
                            'class_id' => $check_old_record->class_id,
                            'student_id' => $check_old_record->student_id,
                            'instructor_id' => $check_old_record->instructor_id,
                            'course_id' => $check_old_record->course_id,
                            'class_date' => $check_old_record->class_date,
                            'class_time' => $check_old_record->class_time,
                            'class_status' => $check_old_record->status,
                            'start_time_unix' => $check_old_record->start_time_unix,
                            'end_time_unix' => $check_old_record->end_time_unix,
                            'start_time_2' => $check_old_record->start_time,
                            'end_time_2' => $check_old_record->end_time,
        
                            'title_meet' => "Individual Google Meet Class",
                            'description_meet' => "Individual Google Meet Class For ".$courseNameMeet,
                            // 'start_time_meet' => Carbon::parse($check_old_record->class_date . ' ' . $check_old_record->start_time),
                            // 'end_time_meet' => Carbon::parse($check_old_record->class_date . ' ' . $check_old_record->end_time),
                            'start_time_meet' => $startTimeParsed,
                            'end_time_meet' => $endTimeParsed,
                            'instructor_user_id_meet' => $instructor_user_id,
                            // 'coordinator_user_id_meet' => $request->coordinator_id,
                            'coordinator_user_ids_meet' => $coordinator_user_ids, // Mutiple Coordinators
                            'student_user_ids_meet' => [$student_user_id] // Single student
                        ];
                        $meet_classes = $meetService->createClass($classData,$check_old_record->id);
                        $res= $check_old_record->update();

                        $old_record_lesson_id = $check_old_record->class_id;
                        $check_old_record_lesson = Lesson::where('id',$old_record_lesson_id)->first();
                        if($check_old_record_lesson){
                            $check_old_record_lesson->meeting_id = $ldata['meeting_id'];
                             $check_old_record_lesson->status = 'In Progress';
                            $check_old_record_lesson->update();
                        }
                        if($googleMeetLink == null){
                            $check_old_record_safe = ClassDetail::where('id',$l_id)->first();
                            if($check_old_record_safe != null || $check_old_record_safe){
                                $googleMeetLink = $check_old_record_safe->google_meet_link;
                            }
                            else{
                                $googleMeetLink = $check_old_record->google_meet_link;
                            } 
                        }
                    }
                    else{
                        $check_old_record->meeting_id = $meeting_id;
                         $check_old_record->status = 'In Progress';
                        $res= $check_old_record->update();
        
                        /////////////////// NEW CODE//////////////////////////////
                        $old_record_lesson_id = $check_old_record->class_id;
                        $check_old_record_lesson = Lesson::where('id',$old_record_lesson_id)->first();
                        if($check_old_record_lesson){
                            $check_old_record_lesson->meeting_id = $ldata['meeting_id'];
                            $check_old_record_lesson->update();
                        }
                        /////////////////// NEW CODE//////////////////////////////
                    }                 
                }
                else{
                    $res = 0;
                    Log::info("Closing helper(startmeetclassindividual) With Class id {$l_id} and met_id {$meeting_id}.  INDIVIDUAL HELPER HAS GROUP ID");
                    Log::info("<!-----------------------------------------------------------------------------------!>");
                }
                if($res){
                    $adata['l_id'] = $l_id ;
                    $adata['group_id'] = $group_id ;
                    $adata['AcademicURI'] = null;
                    $adata['TutorURI'] = null;
                    $adata['StudentURI'] = null;
                    $adata['google_meet_link'] = $googleMeetLink;
                    $adata['meet_meeting_id'] = $meeting_id;
                    $adata['student_name'] = $studentName;
                    $adata['student_id'] = $studentID;
                    $adata['tutor_name'] = $tutorName;
                    $adata['tutor_id'] = $tutorID;
                    $adata['academic_name'] = $academicName;
                    $adata['academic_id'] = $academicID;
                    $adata['student_type'] = $studentType;
                    $adata['tutor_type'] = $tutorType;
                    $adata['academic_type'] = $academicType;
                    $adata['is_group'] = $is_group ;
                    
                    Log::info("Closing SUCCESSFULLY helper(startmeetclassindividual) With Class id {$l_id} and met_id {$meeting_id}.");
                    Log::info("<!-----------------------------------------------------------------------------------!>");
                    return $adata; 
                  
                }
                else{
                    Log::info("Closing FAILED helper(startmeetclassindividual) With Class id {$l_id} and met_id {$meeting_id}.");
                    Log::info("<!-----------------------------------------------------------------------------------!>");
                    return false;
                }
            
        }
        else{
            Log::info("Closing helper(startmeetclassindividual) With Class id {$l_id} and met_id {$meeting_id}.  EMPTY met_id/meeting_id");
            Log::info("<!-----------------------------------------------------------------------------------!>");
        }
        }
        else{
            Log::info("Closing helper(startmeetclassindividual) With Class id {$l_id} and met_id {$meeting_id}.  INVALID LEARNING_TYPE");
            Log::info("<!-----------------------------------------------------------------------------------!>");
        }
        }
        else{
            Log::info("Closing helper(startmeetclassindividual) With Class id {$l_id} and met_id {$meeting_id}.  EMPTY L_ID");
            Log::info("<!-----------------------------------------------------------------------------------!>");
        }  
}






function startmeetclassgroup($l_id,$studentid,$instructorid,$meeting_id=null,$group_id = null,$learning_tool = null)
{
    Log::info("Entered helper(startmeetclassgroup)");
    Log::info("Entered helper(startmeetclassgroup) With Class id {$l_id} and met_id {$meeting_id} and group_id {$group_id}");
    Log::info("<!-----------------------------------------------------------------------------------!>");
    $todayDate = date('Y-m-d');
    $is_group = 'No';
    if(!empty($l_id)){
    if($learning_tool == 'Google_Meet'){
    if($meeting_id != null){
            // Validate the meeting ID before proceeding
    if (ClassDetail::where('meeting_id', $meeting_id)->exists()) {
        Log::info("Closing helper(startmeetclassgroup) With Class id {$l_id} and met_id {$meeting_id} and group_id {$group_id}.  met_id/MEETING_ID EXISTS");
        Log::info("<!-----------------------------------------------------------------------------------!>");
        return false; // Reject if meeting ID already exists
    }

        $getStudentDetail = Student::where('id',$studentid)->first();
        $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
        $studentID = $getStudentDetail->id ;
        $studentType = "Student" ;

        $getInstructorDetail = Instructor::where('id',$instructorid)->first();
        $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
        $tutorID = $getInstructorDetail->id ;
        $tutorType = "Student" ;

            $academicName = "Quran Education Coordinator";
            $academicType = "Academic" ;  
            $academicID = null ; 
        
        $ldata['meeting_id'] = $meeting_id;
        if($group_id != null){
                                $getLessonDetail2 = ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->first();
                                $googleMeetLink = $getLessonDetail2->google_meet_link;
                                $LessonDurationZoom = $getLessonDetail2->class->duration;
                                $LessonDateZoom = date('Y-m-d',strtotime($getLessonDetail2->class_date));
                                $LessonTimeZoom = date("H:i:s", strtotime($getLessonDetail2->class_time));
                                $resultStartTimeZoom = $LessonDateZoom . ' ' . $LessonTimeZoom;

                                $newDate = date("Y-m-d", strtotime($getLessonDetail2->class_date));
                                $newTime = "T" . $LessonTimeZoom;
                                $startingTime = $newDate . $newTime;
                            
            $is_group = 'Yes';
            $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->get();
            foreach ($getarray as $key => $value) {
                $old_end_time = $value->end_time_zoom;
                $lessonDuration = $value->class->duration;
                if($old_end_time != null){
                }
                else{
                    $updated_current_time = getCurrentServerTimeByZone($instructorid,'instructor');
                    $given_start_time = strtotime($updated_current_time);
                    $duration = $lessonDuration * 60;
                    $given_end_time = $given_start_time + $duration; // Calculate the end time
                    $ldata['end_time_zoom'] = $given_end_time;
                    $value->end_time_zoom = $given_end_time;
                }
                $value->meeting_id = $meeting_id;
                 $value->status = 'In Progress';

                $instructorInfo = Instructor::where('id',$value->instructor_id)->first();
                if($instructorInfo){
                    $instructor_user_id = $instructorInfo->user_id;
                }
                else{
                    $instructor_user_id = 0;
                }

                $meetService = new GoogleMeetService(new GoogleOAuthService, new ClassDetail,$instructor_user_id);
                $coordinator_ids = array();
                $coordinator_user_ids = array();
                $courseInfo = Course::where('id',$value->course_id)->first();
                $studentInfo = Student::where('id',$value->student_id)->first();
               
                $getInstructorCoordinators = InstructorCoordinator::where('instructor_id',$value->instructor_id)->get();
                if($courseInfo){
                    $courseNameMeet = "Course: ".$courseInfo->title;
                }
                else{
                    $courseNameMeet = "CEF ONLINE Course";
                }
                if($studentInfo){
                    $student_user_id = $studentInfo->user_id;
                }
                else{
                    $student_user_id = 0;
                }
             
                if(!$getInstructorCoordinators->isEmpty()){
                    foreach ($getInstructorCoordinators as $key => $ins_coor) {
                        $coordinator_ids[] = $ins_coor->coordinator_id;
                    }
                    $coordinator_ids = array_unique($coordinator_ids);
                    $coordinatorsInfo = Coordinator::whereIn('id',$coordinator_ids)->get();
                    if(!$coordinatorsInfo->isEmpty()){
                        foreach ($coordinatorsInfo as $key => $coor_info) {
                            $coordinator_user_ids[] = $coor_info->user_id;
                        }
                        $coordinator_user_ids = array_unique($coordinator_user_ids);
                    }
                }

                  // Build base timestamps
                    $startTimeParsed = Carbon::parse($value->class_date . ' ' . $value->start_time);
                    $endTimeParsed = Carbon::parse($value->class_date . ' ' . $value->end_time);

                    // Fix: If end time is before or equal to start time, assume it’s on the next day
                    if ($endTimeParsed->lessThanOrEqualTo($startTimeParsed)) {
                        $endTimeParsed->addDay(); // move to next day
                    }

                $classData = [
                    'class_id' => $value->class_id,
                    'student_id' => $value->student_id,
                    'instructor_id' => $value->instructor_id,
                    'course_id' => $value->course_id,
                    'class_date' => $value->class_date,
                    'class_time' => $value->class_time,
                    'class_status' => $value->status,
                    'start_time_unix' => $value->start_time_unix,
                    'end_time_unix' => $value->end_time_unix,
                    'start_time_2' => $value->start_time,
                    'end_time_2' => $value->end_time,

                    'title_meet' => "Group Google Meet Class",
                    'description_meet' => "Group Google Meet Class For ".$courseNameMeet,
                    // 'start_time_meet' => Carbon::parse($value->class_date . ' ' . $value->start_time),
                    // 'end_time_meet' => Carbon::parse($value->class_date . ' ' . $value->end_time),
                    'start_time_meet' => $startTimeParsed,
                    'end_time_meet' => $endTimeParsed,
                    'instructor_user_id_meet' => $instructor_user_id,
                    // 'coordinator_user_id_meet' => $request->coordinator_id,
                    'coordinator_user_ids_meet' => $coordinator_user_ids, // Mutiple Coordinators
                    'student_user_ids_meet' => [$student_user_id] // Single student
                ];
                $meet_classes = $meetService->createClass($classData,$value->id,$group_id);

                $res= $value->update();

                if($googleMeetLink == null){
                    $check_old_record_safe = ClassDetail::where('id',$value->id)->first();
                    if($check_old_record_safe != null || $check_old_record_safe){
                        $googleMeetLink = $check_old_record_safe->google_meet_link;
                    }
                    else{
                        $googleMeetLink = $value->google_meet_link;
                    } 
                }

                $old_record_lesson_id = $value->class_id;
                $check_old_record_lesson = Lesson::where('id',$old_record_lesson_id)->first();
                if($check_old_record_lesson){
                    $check_old_record_lesson->meeting_id = $ldata['meeting_id'];
                    
                    $check_old_record_lesson->update();
                }

            }
            
        }
        else{
            $res = 0;
            Log::info("Closing helper(startmeetclassgroup) With Class id {$l_id} and met_id {$meeting_id} and group_id {$group_id}.  GROUP HELPER HAS NO GROUP ID");
            Log::info("<!-----------------------------------------------------------------------------------!>");
        }
        if($res){
            $adata['l_id'] = $l_id ;
            $adata['group_id'] = $group_id ;
            $adata['AcademicURI'] = null;
            $adata['TutorURI'] = null;
            $adata['StudentURI'] = null;
            $adata['google_meet_link'] = $googleMeetLink;
            $adata['meet_meeting_id'] = $meeting_id;
            $adata['student_name'] = $studentName;
            $adata['student_id'] = $studentID;
            $adata['tutor_name'] = $tutorName;
            $adata['tutor_id'] = $tutorID;
            $adata['academic_name'] = $academicName;
            $adata['academic_id'] = $academicID;
            $adata['student_type'] = $studentType;
            $adata['tutor_type'] = $tutorType;
            $adata['academic_type'] = $academicType;
            $adata['is_group'] = $is_group ;
            
            Log::info("Closing SUCCESSFULLY helper(startmeetclassgroup) With Class id {$l_id} and met_id {$meeting_id} and group_id {$group_id}.");
            Log::info("<!-----------------------------------------------------------------------------------!>");

            return $adata; 
        }
        else{
            Log::info("Closing FAILED helper(startmeetclassgroup) With Class id {$l_id} and met_id {$meeting_id} and group_id {$group_id}.");
            Log::info("<!-----------------------------------------------------------------------------------!>");
            return false;
        }
    
    }
    else{
        Log::info("Closing helper(startmeetclassgroup) With Class id {$l_id} and met_id {$meeting_id} and group_id {$group_id}.  EMPTY met_id/MEETING_ID");
        Log::info("<!-----------------------------------------------------------------------------------!>");
    }
    }
    else{
        Log::info("Closing helper(startmeetclassgroup) With Class id {$l_id} and met_id {$meeting_id} and group_id {$group_id}.  INVALID LEARNING_TOOL");
        Log::info("<!-----------------------------------------------------------------------------------!>");
    }
    } 
    else{
        Log::info("Closing helper(startmeetclassgroup) With Class id {$l_id} and met_id {$meeting_id} and group_id {$group_id}.  EMPTY L_ID");
        Log::info("<!-----------------------------------------------------------------------------------!>");
           
    }  
}



    function startzoomclassindividual($l_id,$studentid,$instructorid,$meeting_id=null,$group_id = null,$learning_tool = null)
{
        $todayDate = date('Y-m-d');
        $is_group = 'No';
   
    

        if(!empty($l_id)){
    if($learning_tool == 'Zoom'){
        if($meeting_id != null){
                // Validate the meeting ID before proceeding
    if (ClassDetail::where('meeting_id', $meeting_id)->exists()) {
        return false; // Reject if meeting ID already exists
    }


            $getStudentDetail = Student::where('id',$studentid)->first();
            $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
            $studentID = $getStudentDetail->id ;
            $studentType = "Student" ;

            $getInstructorDetail = Instructor::where('id',$instructorid)->first();
            $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
            $tutorID = $getInstructorDetail->id ;
            $tutorType = "Instructor" ;

                $academicName = "Quran Education Coordinator";
                $academicType = "Academic" ;  
                $academicID = null ; 
            
            $ldata['meeting_id'] = $meeting_id;
    
            if($group_id ==  null){
                $getLessonDetail2 = ClassDetail::where('id',$l_id)->where('class_date',$todayDate)->first();
                $LessonDurationZoom = $getLessonDetail2->class->duration;
                $LessonDateZoom = date('Y-m-d',strtotime($getLessonDetail2->class_date));
                $LessonTimeZoom = date("H:i:s", strtotime($getLessonDetail2->class_time));
                $resultStartTimeZoom = $LessonDateZoom . ' ' . $LessonTimeZoom;
            
                $newDate = date("Y-m-d", strtotime($getLessonDetail2->class_date));
                $newTime = "T" . $LessonTimeZoom;
                $startingTime = $newDate . $newTime;

                $zoom_array['start_date_only'] = $startingTime;
                $zoom_array['start_date'] = $resultStartTimeZoom;
                $zoom_array['duration'] = $LessonDurationZoom;
     
                $check_old_record = ClassDetail::where('id',$l_id)->first();

               
                $old_end_time = $check_old_record->end_time_zoom;
                $lessonDuration = $check_old_record->class->duration;
                if($old_end_time != null){
                }
                else{
                    $updated_current_time = getCurrentServerTimeByZone($instructorid,'instructor');
                    $given_start_time = strtotime($updated_current_time);
                    $duration = $lessonDuration * 60;
                    $given_end_time = $given_start_time + $duration; // Calculate the end time   
                    $ldata['end_time_zoom'] = $given_end_time;
                    $check_old_record->end_time_zoom = $given_end_time;
                }
                $check_old_record->meeting_id = $meeting_id;
                  $check_old_record->status = 'In Progress';
                $res= $check_old_record->update();


                /////////////////// NEW CODE//////////////////////////////
                $old_record_lesson_id = $check_old_record->class_id;
                $check_old_record_lesson = Lesson::where('id',$old_record_lesson_id)->first();
                if($check_old_record_lesson){
                    $check_old_record_lesson->meeting_id = $ldata['meeting_id'];
                  
                    $check_old_record_lesson->update();
                }
                /////////////////// NEW CODE//////////////////////////////
                
            }

            if($res){
                $adata['l_id'] = $l_id ;
                $adata['group_id'] = $group_id ;
                $adata['AcademicURI'] = null;
                $adata['TutorURI'] = null;
                $adata['StudentURI'] = null;
                $adata['zoom_meeting_id'] = $meeting_id;
                $adata['student_name'] = $studentName;
                $adata['student_id'] = $studentID;
                $adata['tutor_name'] = $tutorName;
                $adata['tutor_id'] = $tutorID;
                $adata['academic_name'] = $academicName;
                $adata['academic_id'] = $academicID;
                $adata['student_type'] = $studentType;
                $adata['tutor_type'] = $tutorType;
                $adata['academic_type'] = $academicType;
                $adata['is_group'] = $is_group ;
                
                return $adata; 
            }
            else{
                return false;
            }
        
    }
    }
    }   
}



// function startzoomclassindividual2($l_id,$studentid,$instructorid,$meeting_id=null,$group_id = null,$learning_tool = null)
// {
//     $todayDate = date('Y-m-d');
//     $is_group = 'No';

//     if(!empty($l_id)){
//     if($meeting_id != null){
//         $getStudentDetail = Student::where('id',$studentid)->first();
//         $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
//         $studentID = $getStudentDetail->id ;
//         $studentType = "Student" ;

//         $getInstructorDetail = Instructor::where('id',$instructorid)->first();
//         $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
//         $tutorID = $getInstructorDetail->id ;
//         $tutorType = "Student" ;

//             $academicName = "Quran Education Coordinator";
//             $academicType = "Academic" ;  
//             $academicID = null ; 
        
//         $ldata['meeting_id'] = $meeting_id;

//         if($group_id ==  null){
//             $getLessonDetail2 = ClassDetail::where('id',$l_id)->where('class_date',$todayDate)->first();
//             $LessonDurationZoom = $getLessonDetail2->class->duration;
//             $LessonDateZoom = date('Y-m-d',strtotime($getLessonDetail2->class_date));
//             $LessonTimeZoom = date("H:i:s", strtotime($getLessonDetail2->class_time));
//             $resultStartTimeZoom = $LessonDateZoom . ' ' . $LessonTimeZoom;
        
//             $newDate = date("Y-m-d", strtotime($getLessonDetail2->class_date));
//             $newTime = "T" . $LessonTimeZoom;
//             $startingTime = $newDate . $newTime;

//             $zoom_array['start_date_only'] = $startingTime;
//             $zoom_array['start_date'] = $resultStartTimeZoom;
//             $zoom_array['duration'] = $LessonDurationZoom;
 
//             $check_old_record = ClassDetail::where('id',$l_id)->first();
//             $old_end_time = $check_old_record->end_time_zoom;
//             $lessonDuration = $check_old_record->class->duration;
//             if($old_end_time != null){
//             }
//             else{
//                 $updated_current_time = getCurrentServerTimeByZone($instructorid,'instructor');
//                 $given_start_time = strtotime($updated_current_time);
//                 $duration = $lessonDuration * 60;
//                 $given_end_time = $given_start_time + $duration; // Calculate the end time   
//                 $ldata['end_time_zoom'] = $given_end_time;
//                 $check_old_record->end_time_zoom = $given_end_time;
//             }
//             $check_old_record->meeting_id = $meeting_id;
//             $res= $check_old_record->update();
//         }

//         if($res){
//             $adata['l_id'] = $l_id ;
//             $adata['group_id'] = $group_id ;
//             $adata['AcademicURI'] = null;
//             $adata['TutorURI'] = null;
//             $adata['StudentURI'] = null;
//             $adata['zoom_meeting_id'] = $meeting_id;
//             $adata['student_name'] = $studentName;
//             $adata['student_id'] = $studentID;
//             $adata['tutor_name'] = $tutorName;
//             $adata['tutor_id'] = $tutorID;
//             $adata['academic_name'] = $academicName;
//             $adata['academic_id'] = $academicID;
//             $adata['student_type'] = $studentType;
//             $adata['tutor_type'] = $tutorType;
//             $adata['academic_type'] = $academicType;
//             $adata['is_group'] = $is_group ;
            
//             return $adata; 
//         }
//         else{
//             return false;
//         }
    
// }
// }
  
// }







function startzoomclassgroup($l_id,$studentid,$instructorid,$meeting_id=null,$group_id = null,$learning_tool = null)
{
    $todayDate = date('Y-m-d');
    $is_group = 'No';
    if(!empty($l_id)){
    if($learning_tool == 'Zoom'){
    if($meeting_id != null){
            // Validate the meeting ID before proceeding
    if (ClassDetail::where('meeting_id', $meeting_id)->exists()) {
        return false; // Reject if meeting ID already exists
    }

        $getStudentDetail = Student::where('id',$studentid)->first();
        $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
        $studentID = $getStudentDetail->id ;
        $studentType = "Student" ;

        $getInstructorDetail = Instructor::where('id',$instructorid)->first();
        $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
        $tutorID = $getInstructorDetail->id ;
        $tutorType = "Student" ;

            $academicName = "Quran Education Coordinator";
            $academicType = "Academic" ;  
            $academicID = null ; 
        
        $ldata['meeting_id'] = $meeting_id;
        if($group_id != null){
                                $getLessonDetail2 = ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->first();
                                $LessonDurationZoom = $getLessonDetail2->class->duration;
                                $LessonDateZoom = date('Y-m-d',strtotime($getLessonDetail2->class_date));
                                $LessonTimeZoom = date("H:i:s", strtotime($getLessonDetail2->class_time));
                                $resultStartTimeZoom = $LessonDateZoom . ' ' . $LessonTimeZoom;

                                $newDate = date("Y-m-d", strtotime($getLessonDetail2->class_date));
                                $newTime = "T" . $LessonTimeZoom;
                                $startingTime = $newDate . $newTime;
                                $zoom_array['start_date_only'] = $startingTime;
                                $zoom_array['start_date'] = $resultStartTimeZoom;
                                $zoom_array['duration'] = $LessonDurationZoom;
                        
            $is_group = 'Yes';
            $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->get();
            foreach ($getarray as $key => $value) {
                $old_end_time = $value->end_time_zoom;
                $lessonDuration = $value->class->duration;
                if($old_end_time != null){
                }
                else{
                    $updated_current_time = getCurrentServerTimeByZone($instructorid,'instructor');
                    $given_start_time = strtotime($updated_current_time);
                    $duration = $lessonDuration * 60;
                    $given_end_time = $given_start_time + $duration; // Calculate the end time
                    $ldata['end_time_zoom'] = $given_end_time;
                    $value->end_time_zoom = $given_end_time;
                }
                $value->meeting_id = $meeting_id;
                  $value->status = 'In Progress';
                $res= $value->update();

                $old_record_lesson_id = $value->class_id;
                $check_old_record_lesson = Lesson::where('id',$old_record_lesson_id)->first();
                if($check_old_record_lesson){
                    $check_old_record_lesson->meeting_id = $ldata['meeting_id'];
                   
                    $check_old_record_lesson->update();
                }

            }
            
        }
    
        if($res){
            $adata['l_id'] = $l_id ;
            $adata['group_id'] = $group_id ;
            $adata['AcademicURI'] = null;
            $adata['TutorURI'] = null;
            $adata['StudentURI'] = null;
            $adata['zoom_meeting_id'] = $meeting_id;
            $adata['student_name'] = $studentName;
            $adata['student_id'] = $studentID;
            $adata['tutor_name'] = $tutorName;
            $adata['tutor_id'] = $tutorID;
            $adata['academic_name'] = $academicName;
            $adata['academic_id'] = $academicID;
            $adata['student_type'] = $studentType;
            $adata['tutor_type'] = $tutorType;
            $adata['academic_type'] = $academicType;
            $adata['is_group'] = $is_group ;
            
            return $adata; 
        }
        else{
            return false;
        }
    
    }
    }
    }   
}






// function startzoomclassgroup2($l_id,$studentid,$instructorid,$meeting_id=null,$group_id = null,$learning_tool = null)
// {
//     $todayDate = date('Y-m-d');
//     $is_group = 'No';
//     if($learning_tool == 'Zoom'){
//     if($meeting_id != null){
//         $getStudentDetail = Student::where('id',$studentid)->first();
//         $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
//         $studentID = $getStudentDetail->id ;
//         $studentType = "Student" ;

//         $getInstructorDetail = Instructor::where('id',$instructorid)->first();
//         $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
//         $tutorID = $getInstructorDetail->id ;
//         $tutorType = "Student" ;

//             $academicName = "Quran Education Coordinator";
//             $academicType = "Academic" ;  
//             $academicID = null ; 
        
//         $ldata['meeting_id'] = $meeting_id;
//         if($group_id != null){
//                                 $getLessonDetail2 = ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->first();
//                                 $LessonDurationZoom = $getLessonDetail2->class->duration;
//                                 $LessonDateZoom = date('Y-m-d',strtotime($getLessonDetail2->class_date));
//                                 $LessonTimeZoom = date("H:i:s", strtotime($getLessonDetail2->class_time));
//                                 $resultStartTimeZoom = $LessonDateZoom . ' ' . $LessonTimeZoom;

//                                 $newDate = date("Y-m-d", strtotime($getLessonDetail2->class_date));
//                                 $newTime = "T" . $LessonTimeZoom;
//                                 $startingTime = $newDate . $newTime;
//                                 $zoom_array['start_date_only'] = $startingTime;
//                                 $zoom_array['start_date'] = $resultStartTimeZoom;
//                                 $zoom_array['duration'] = $LessonDurationZoom;
                        
//             $is_group = 'Yes';
//             $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->get();
//             foreach ($getarray as $key => $value) {
//                 $check_old_record = ClassDetail::where('id',$value->id)->first();
//                 $old_end_time = $check_old_record->end_time_zoom;
//                 $lessonDuration = $check_old_record->class->duration;
//                 if($old_end_time != null){
//                 }
//                 else{
//                     $updated_current_time = getCurrentServerTimeByZone($instructorid,'instructor');
//                     $given_start_time = strtotime($updated_current_time);
//                     $duration = $lessonDuration * 60;
//                     $given_end_time = $given_start_time + $duration; // Calculate the end time
//                     $ldata['end_time_zoom'] = $given_end_time;
//                     $check_old_record->end_time_zoom = $given_end_time;
//                 }
//                 $check_old_record->meeting_id = $meeting_id;
//                 $res= $check_old_record->update();
//             }
            
//         }
    
//         if($res){
//             $adata['l_id'] = $l_id ;
//             $adata['group_id'] = $group_id ;
//             $adata['AcademicURI'] = null;
//             $adata['TutorURI'] = null;
//             $adata['StudentURI'] = null;
//             $adata['zoom_meeting_id'] = $meeting_id;
//             $adata['student_name'] = $studentName;
//             $adata['student_id'] = $studentID;
//             $adata['tutor_name'] = $tutorName;
//             $adata['tutor_id'] = $tutorID;
//             $adata['academic_name'] = $academicName;
//             $adata['academic_id'] = $academicID;
//             $adata['student_type'] = $studentType;
//             $adata['tutor_type'] = $tutorType;
//             $adata['academic_type'] = $academicType;
//             $adata['is_group'] = $is_group ;
            
//             return $adata; 
//         }
//         else{
//             return false;
//         }
    
// }
// }
   
// }



function check_missed_status($classid)
{
    if(!empty($classid)){
        $checkAll = ClassDetail::where('meeting_id',$classid)->get();
        if(!$checkAll->isEmpty()){
            foreach ($checkAll as $key => $value) {
                if($value->status == 'Completed'){
                    return "Completed";
                }
            }
        }
        else{
            return false;
        }
    }
}



function check_missed_status_instructor($classid)
{
    if(!empty($classid)){
        $checkAll = LiveClass::where('meeting_id',$classid)->get();
        if(!$checkAll->isEmpty()){
            foreach ($checkAll as $key => $value) {
                if($value->status == 'Completed'){
                    return "Completed";
                }
            }
        }
        else{
            return false;
        }
    }
}
function getStudentNameFromID($student_id)
    {
        $data = Student::where('id',$student_id)->first();
        return $data->first_name.' '.$data->last_name ?? Null;        
    }   



    function getCoordinatorNameFromID($coordinator_id)
    {
        $data = Coordinator::where('id',$coordinator_id)->first();
        return $data->first_name.' '.$data->last_name ?? Null;        
    }   


    function getTutorNameFromID($tutor_id)
    {
        $data = Instructor::where('id',$tutor_id)->first();
        return $data->first_name.' '.$data->last_name ?? Null;        
    }   



    function countActiveTodayClasses($gender = '')
    {   
     
$toady = date('Y-m-d');
// $toady = date('d-m-Y');
// $query = ClassDetail::where('class_date',$toady)->whereHas('course', function ($query) {
//     $query->where('course_type', 3);
// })->count();


// $query1 = ClassDetail::where('class_date',$toady)->wherehas('class' , function($query){
//     $query->where('type', 'Group');
// })
// ->orderBy('id','DESC')
// ->groupBy('class_id')
// ->groupBy('class_date')
// ->count();

$query1 = ClassDetail::where('class_date', $toady)->where('status','Scheduled')
    ->whereHas('class', function($query) {
        $query->where('type', 'Group');
    })
    ->groupBy('class_id', 'class_date')
    ->havingRaw('COUNT(*) >= 1')
    ->count(DB::raw('DISTINCT class_id'));

$query2 = ClassDetail::where('class_date',$toady)->wherehas('class' , function($query){
    $query->where('type', 'Individual');
})
->orderBy('id','DESC')
->count();

        if($query1 || $query2){
        return $query1;    
        }
        else{
            return 0;
        }
     
    }




    function countActiveScheduledClasses($gender = '')
    {   
     
        $toady = date('Y-m-d');

// $query = ClassDetail::where('status','Scheduled')->whereHas('course', function ($query) {
//     $query->where('course_type', 3);
// })->count();

// $query = ClassDetail::where('status','Scheduled')->count();

// $query1 = ClassDetail::where('class_date','>',$toady)->where('status','Scheduled')->wherehas('class' , function($query){
//     $query->where('type', 'Group');
// })
// ->orderBy('id','DESC')
// // ->groupBy('class_id')
// // ->groupBy('class_date')
// ->count();

$query1 = ClassDetail::where('class_date', $toady)->where('status','Scheduled')
    ->whereHas('class', function($query) {
        $query->where('type', 'Group');
    })
    ->groupBy('class_id', 'class_date')
    ->havingRaw('COUNT(*) >= 1')
    ->count(DB::raw('DISTINCT class_id'));

$query2 = ClassDetail::where('class_date','>',$toady)->where('status','Scheduled')->wherehas('class' , function($query){
    $query->where('type', 'Individual');
})
->orderBy('id','DESC')
->count();

        if($query1 || $query1){
            return $query1 + $query2;    
        }
        else{
            return 0;
        }
     
    }



    
    function countActiveCompletedClasses($gender = '')
    {   
     
       
// $query = ClassDetail::where('status','Completed')->whereHas('course', function ($query) {
//     $query->where('course_type', 3);
// })->count();


// $query1 = ClassDetail::where('status','Completed')->wherehas('class' , function($query){
//     $query->where('type', 'Group');
// })
// ->orderBy('id','DESC')
// // ->groupBy('group_id')
// // ->groupBy('class_id')
// // ->groupBy('class_date')
// ->count();


$query1 = ClassDetail::where('status','Completed')
    ->whereHas('class', function($query) {
        $query->where('type', 'Group');
    })
    ->groupBy('group_id', 'class_date')
    ->havingRaw('COUNT(*) >= 1')
    ->count(DB::raw('DISTINCT class_id'));


$query2 = ClassDetail::where('status','Completed')->wherehas('class' , function($query){
    $query->where('type', 'Individual');
})
->orderBy('id','DESC')
->count();

if($query1 || $query2){
    return $query1 + $query2;    
}
        else{
            return 0;
        }
     
    }




    function countActiveMissedClasses($gender = '')
    {   
     
       
// $query = ClassDetail::where('status','Missed')->whereHas('course', function ($query) {
//     $query->where('course_type', 3);
// })->count();


// $query1 = ClassDetail::where('status','Missed')->wherehas('class' , function($query){
//     $query->where('type', 'Group');
// })
// ->orderBy('id','DESC')
// // ->groupBy('class_id')
// // ->groupBy('class_date')
// ->count();


$query1 = ClassDetail::where('status','Missed')
    ->whereHas('class', function($query) {
        $query->where('type', 'Group');
    })
    ->groupBy('group_id', 'class_date')
    ->havingRaw('COUNT(*) >= 1')
    ->count(DB::raw('DISTINCT class_id'));

$query2 = ClassDetail::where('status','Missed')->wherehas('class' , function($query){
    $query->where('type', 'Individual');
})
->orderBy('id','DESC')
->count();


if($query1 || $query2){
    return $query1 + $query2;    
}
        else{
            return 0;
        }
     
    }




function staticMeta($id)
{
    $meta = \App\Models\Meta::find($id);
    $metaData = [];
    if ($meta) {
        $metaData['title'] = $meta->meta_title;
        $metaData['meta_description'] = $meta->meta_description;
        $metaData['meta_keyword'] = $meta->meta_keyword;
    }

    return $metaData;
}



function active_if_match($path)
{
    if (auth::user()->is_admin()) {
        return Request::is($path . '*') ? 'mm-active' : '';
    } else {
        return Request::is($path . '*') ? 'active' : '';
    }
}

function active_if_full_match($path)
{
    if (auth::user()->is_admin()) {
        return Request::is($path) ? 'mm-active' : '';
    } else {
        return Request::is($path) ? 'active' : '';
    }
}

function open_if_full_match($path)
{
    return Request::is($path) ? 'has-open' : '';
}


function toastrMessage($message_type, $message)
{
    Toastr::$message_type($message, '', ['progressBar' => true, 'closeButton' => true, 'positionClass' => 'toast-top-right']);
}

function get_option($option_key, $default = NULL)
{
    $system_settings = config('settings');

    if ($option_key && isset($system_settings[$option_key])) {
        return $system_settings[$option_key];
    } elseif ($option_key && isset($system_settings[strtolower($option_key)])) {
        return $system_settings[strtolower($option_key)];
    } elseif ($option_key && isset($system_settings[strtoupper($option_key)])) {
        return $system_settings[strtoupper($option_key)];
    } else {
        return $default;
    }
}


function zoom_status()
{
    // $zoom = ZoomSetting::whereUserId(Auth::id())->first();
    // $status = 0;
    // if ($zoom) {
    //     $status = $zoom->status;
    // }

  

    $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    $status = 0;
    if($zoomSettings){
        $status = $zoomSettings->status;
    }
    return $status;
}

function get_default_language()
{
    $language = Language::where('default_language', 'on')->first();
    if ($language) {
        $iso_code = $language->iso_code;
        return $iso_code;
    }

    return 'en';
}

function get_currency_symbol()
{
    $currency = Currency::where('current_currency', 'on')->first();
    if ($currency) {
        $symbol = $currency->symbol;
        return $symbol;
    }

    return '';
}

function get_current_currency()
{
    return Currency::where('current_currency', 'on')->first();
}

function get_currency_code()
{
    $currency = Currency::where('current_currency', 'on')->first();
    if ($currency) {
        $currency_code = $currency->currency_code;
        return $currency_code;
    }

    return '';
}

function get_currency_placement()
{
    $currency = Currency::where('current_currency', 'on')->first();
    $placement = 'before';
    if ($currency) {
        $placement = $currency->currency_placement;
        return $placement;
    }

    return $placement;
}

function get_platform_charge($sub_total)
{
    return ($sub_total * get_option('platform_charge')) / 100;
}

function admin_sell_commission($amount)
{
    return ($amount * get_option('sell_commission')) / 100;
}

function admin_commission_by_percentage($amount, $percentage)
{
    return ($amount * $percentage) / 100;
}

function referral_sell_commission($amount)
{
    return ($amount * get_option('referral_commission_percentage')) / 100;
}

function userBalance($userId = null)
{
    if ($userId == null) {
        return int_to_decimal(Auth::user()->balance);
    }
    $user = User::find($userId);
    return int_to_decimal($user->balance);
}

function instructor_available_balance()
{
    //Start::  Cancel Consultation Money Calculation
    $cancelConsultationOrderItemIds = BookingHistory::whereStatus(2)->where('send_back_money_status', 1)->whereHas('order', function ($q) {
        $q->where('payment_status', 'paid');
    })->pluck('order_item_id')->toArray();
    $orderItems = Order_item::whereIn('id', $cancelConsultationOrderItemIds);
    $cancel_consultation_money = $orderItems->sum('admin_commission') + $orderItems->sum('owner_balance');
    //Start::  Cancel Consultation Money Calculation

    $total_balance = Order_item::where('owner_user_id', Auth::id())->whereHas('order', function ($q) {
        $q->where('payment_status', 'paid');
    })->sum('owner_balance');
    $total_withdraw_balance = Withdraw::where('user_id', auth()->user()->id)->whereIn('status', [0, 1])->sum('amount');
    $available_balance = $total_balance - $total_withdraw_balance - $cancel_consultation_money;
    return number_format($available_balance, 2);
}


function get_number_format($amount)
{
    return number_format($amount, 2, '.', '');
}

function decimal_to_int($amount)
{
    return number_format(number_format($amount, 2, '.', '') * 100, 0, '.', '');
}

function int_to_decimal($amount)
{
    return number_format($amount / 100, 2, '.', '');
}

function createTransaction($user_id, $amount, $type, $narration, $reference = null, $order_item_id = null)
{
    $trn = new Transaction();
    $trn->hash = Str::uuid()->getHex();
    $trn->user_id = $user_id;
    $trn->order_item_id = $order_item_id;
    $trn->amount = $amount;
    $trn->narration = $narration;
    $trn->type = $type;
    $trn->reference = $reference;
    $trn->save();
}

function appLanguages()
{
    return Language::where('status', 1)->get();
}

function selectedLanguage()
{
    $language = Language::where('iso_code', config('app.locale'))->first();
    if (!$language) {
        $language = Language::find(1);
        if ($language) {
            $ln = $language->iso_code;
            session(['local' => $ln]);
            App::setLocale(session()->get('local'));
        }
    }

    return $language;
}

function take_exam($exam_id)
{
    if (\App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->count() > 0) {
        return 'yes';
    } else {
        return 'no';
    }
}



function take_exam_lecture_uuid($exam_id)
{
    $lecture_uuid = null;
    if (\App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->count() > 0) {
        $take_exam = \App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->first();
        if($take_exam){
            if($take_exam->lecture){
                $lecture_uuid = $take_exam->lecture->uuid;
            }
        }
        return $lecture_uuid;
    } else {
        return $lecture_uuid;
    }
}




function check_take_attempt_exam($exam_id)
{
 
  
    $result = 'no';
  
    $exam = Exam::where('id',$exam_id)->first();
                if (Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->count() == 0) {
                }
                else{
                
                    $take_exam = Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->orderBy('id', 'DESC')->first();
                
                    $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->pluck('question_id')->toArray();
                    $question = Question::whereExamId($exam_id)->whereNotIn('id', $question_ids)->first();
                    $number_of_answer= Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->count();
                    $now = Carbon::now();
                    $expend_second = $now->diffInSeconds($take_exam->created_at);
                    if (Carbon::parse($exam->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($exam->duration * 60)->format('H:i:s')) {
                        $isPassed =  $take_exam->passed;
                        if($isPassed){
                            $result = 'yes';
                        }
                        else{
                            // $result = 'no_restart';
                            $result = 'no';
                        }     
                    }
                    else{
                        $total_questions = Question::whereExamId($exam_id)->count();
                        $attempted_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)
                        ->where('take_exam_id',$take_exam->id)->count();
    
                        if($attempted_answers == $total_questions){  // USER Attempted All Question Before EndTime
                            $isPassed =  $take_exam->passed;
                            if($isPassed){
                                $result = 'yes';
                            }
                            else{
                                // $result = 'no_restart';
                                $result = 'no';
                            }
                        }
                    }
                   
                }
    return $result;
}


function check_original_answer($exam_id, $question_id, $question_option_id)
{
    $option = \App\Models\Question_option::find($question_option_id);
    if ($option->is_correct_answer == 'yes') {
        return 'original-correct-answer';
    } else {
        return '';
    }
}


function check_user_answer($exam_id, $question_id, $question_option_id)
{
    $latestTakenExam = \App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->orderBy('id', 'DESC')->first();
    if($latestTakenExam){
        if (\App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->whereQuestionId($question_id)->whereQuestionOptionId($question_option_id)
        ->where('take_exam_id',$latestTakenExam->id)->count() > 0) {
            $answer = \App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->whereQuestionId($question_id)->whereQuestionOptionId($question_option_id)->orderBy('id', 'DESC')
            ->where('take_exam_id',$latestTakenExam->id)->first();
            if ($answer->is_correct == 'yes') {
                return 'given-answer-right';
            } else {
                return 'given-answer-wrong';
            }
        } 
        else 
        {
            return '';
        }
    }
    else{
        return '';  
    }

}


function get_answer_class($exam_id, $question_id, $question_option_id)
{
    $latestTakenExam = \App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->orderBy('id', 'DESC')->first();
    if($latestTakenExam){
        if (\App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->whereQuestionId($question_id)->whereQuestionOptionId($question_option_id)
        ->where('take_exam_id',$latestTakenExam->id)->count() > 0) {
            $answer = \App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)->whereQuestionId($question_id)->whereQuestionOptionId($question_option_id)->orderBy('id', 'DESC')
            ->where('take_exam_id',$latestTakenExam->id)->first();
            if ($answer->is_correct == 'yes') {
                return 'given-answer-right';
            } else {
                return 'given-answer-wrong';
            }
        } 
        else 
        {
            $option = \App\Models\Question_option::find($question_option_id);
            if ($option->is_correct_answer == 'yes') {
                return 'original-correct-answer';
            } else {
                return '';
            }
        }
    }
    else{
        return '';  
    }

}

function get_total_score($exam_id)
{
    $exam = \App\Models\Exam::find($exam_id);
    return $exam->marks_per_question * $exam->questions->count();
}



function get_total_percentage($exam_id)
{
    $exam = \App\Models\Exam::find($exam_id);
    return $exam->passing_percentage;
}

function get_student_score($exam_id)
{
    // $correct_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
    // ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
    $number_of_correct_answer = 0;
    $latestTakenExam = \App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->orderBy('id', 'DESC')->first();
    if($latestTakenExam){
        $exam = \App\Models\Exam::find($exam_id);
        $number_of_correct_answer = \App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)
        ->where('take_exam_id',$latestTakenExam->id)->whereIsCorrect('yes')->count();
        return $exam->marks_per_question * $number_of_correct_answer;
    }
    else{
        return $number_of_correct_answer;
    }
   
  
}



function get_student_percentage($exam_id)
{
    // $correct_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
    // ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
    $obtained_percentage = 0;
    $latestTakenExam = \App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->orderBy('id', 'DESC')->first();
    if($latestTakenExam){
        $obtained_percentage = $latestTakenExam->obtained_percentage;
    }
   
    return $obtained_percentage;
}



function get_student_percentage_with_take_exam($take_exam_id)
{
    // $correct_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
    // ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
    $obtained_percentage = 0;
    $latestTakenExam = \App\Models\Take_exam::where('id',$take_exam_id)->orderBy('id', 'DESC')->first();
    if($latestTakenExam){
        $obtained_percentage = $latestTakenExam->obtained_percentage;
    }
   
    return $obtained_percentage;
}




function get_student_exam_status($exam_id)
{
    // $correct_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
    // ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
    $isPassed = 0;
    $latestTakenExam = \App\Models\Take_exam::whereUserId(auth()->user()->id)->whereExamId($exam_id)->orderBy('id', 'DESC')->first();
    if($latestTakenExam){
        $isPassed = $latestTakenExam->passed;
    }
   
    return $isPassed;
}



function get_student_take_exam_status($take_exam_id)
{
    // $correct_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
    // ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
    $isPassed = 0;
    $latestTakenExam = \App\Models\Take_exam::where('id',$take_exam_id)->orderBy('id', 'DESC')->first();
    if($latestTakenExam){
        $isPassed = $latestTakenExam->passed;
    }
   
    return $isPassed;
}



function get_student_by_student_score($exam_id, $user_id,$take_exam_id)
{
    $number_of_correct_answer = 0;
    $takenExam = \App\Models\Take_exam::where('id',$take_exam_id)->orderBy('id', 'DESC')->first();
    if($takenExam){
    $exam = \App\Models\Exam::find($exam_id);
    $number_of_correct_answer = \App\Models\Answer::whereUserId(auth()->user()->id)->whereExamId($exam_id)
    ->where('take_exam_id',$takenExam->id)->whereIsCorrect('yes')->count();
    }
    return $exam->marks_per_question * $number_of_correct_answer;
}

function get_position($exam_id)
{
    $take_exams = \App\Models\Take_exam::whereExamId($exam_id)->orderBy('number_of_correct_answer', 'DESC')->get();
    $list = [];
    foreach ($take_exams as $key => $take_exam) {
        $list[$take_exam->user_id] = $key + 1;
    }

    if (array_key_exists(auth()->user()->id, $list)) {
        return $list[auth()->user()->id];
    } else {
        return '0';
    }
}

function set_instructor_ranking_level($user_id)
{
    $userCourseIds = Course::where(['course_instructor.instructor_id' => $user_id, 'course_instructor.status' => STATUS_ACCEPTED])->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')->groupBy('courses.id')->pluck('courses.id')->toArray();
    $userBundleIds = Bundle::whereUserId($user_id)->pluck('id')->toArray();

    $orderBundleItemsCount = Order_item::whereIn('bundle_id', $userBundleIds)->where('course_id', null)
        ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
        ->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->count();

    $allOrderItems = Order_item::whereIn('course_id', $userCourseIds)->orWhereIn('bundle_id', $userBundleIds)->whereHas('order', function ($q) {
        $q->where('payment_status', 'paid');
    });

    $grand_total_earning = $allOrderItems->sum('owner_balance');
    $grand_total_enroll = $allOrderItems->count('id') - $orderBundleItemsCount;

    $rankLevel = RankingLevel::where('earning', '<=', $grand_total_earning)->where('student', '<=', $grand_total_enroll)->first();

    if (is_null($rankLevel)) {
        return null;
    } else {
        Instructor::where('user_id', $user_id)->update(['level_id' => $rankLevel->id]);
    }
}


function get_instructor_ranking_level($badges)
{
    $badge = $badges->where('type', RANKING_LEVEL_EARNING)->first();
    return !is_null($badge) ? $badge->name : NULL;
}

function getImageFile($file)
{
    if ($file != '') {
        return asset($file);
    } else {
        return asset('frontend/assets/img/no-image.png');
    }
}

function getVideoFile($file)
{
    if ($file == '' || $file == null) {
        return null;
    }

    try {
        if (env('STORAGE_DRIVER') != "public") {
            if (Storage::disk(env('STORAGE_DRIVER'))->exists($file)) {
                $storage = Storage::disk(env('STORAGE_DRIVER'));
                return $storage->url($file);
            }
        } else {
            return asset($file);
        }
    } catch (Exception $e) {
    }

    return asset($file);
}

function notificationForUser()
{
   
    $instructor_notifications = \App\Models\Notification::where('user_id', auth()->user()->id)->where('user_type', 2)->where('is_seen', 'no')->orderBy('created_at', 'DESC')->get();
    $student_notifications = \App\Models\Notification::where('user_id', auth()->user()->id)->where('user_type', 3)->where('is_seen', 'no')->orderBy('created_at', 'DESC')->get();
    return array('instructor_notifications' => $instructor_notifications, 'student_notifications' => $student_notifications);
}

function adminNotifications()
{
    return \App\Models\Notification::where('user_type', 1)->where('is_seen', 'no')->orderBy('created_at', 'DESC')->paginate(5);
}



function checkStudentCourseIsLock($course_lecture_views, $course, $lecture, $enrollment = NULL, $isFirst = false)
{
    $lock = 0;
    if ($course->drip_content == DRIP_SEQUENCE) {
        $views = $course_lecture_views->where('course_id', $course->id)->where('course_lecture_id', $lecture->id)->first();
        if ($views) {
            $lock = 0;
        } else {
            $lock = 1;
        }

        if ($isFirst) {
            $lock = 0;
        } else {
            $oldLecture = Course_lecture::where('course_id', $course->id)->where('id', '<', $lecture->id)->orderBy('id', 'DESC')->select('id')->first();
            $oldView = $course_lecture_views->where('course_id', $course->id)->where('course_lecture_id', @$oldLecture->id)->first();
            if ($lock && $oldView) {
                $lock = 0;
            }
        }
    } elseif ($course->drip_content == DRIP_AFTER_DAY) {
        $today = Carbon::now();
        $afterDay = $lecture->after_day ? $lecture->after_day : 0;
        $unlockDay = Carbon::parse($enrollment->start_date)->addDays($afterDay);

        if ($unlockDay && Carbon::parse($unlockDay)->lte($today)) {
            $lock = 0;
        } else {
            $lock = 1;
        }
    } elseif ($course->drip_content == DRIP_UNLOCK_DATE) {
        $today = Carbon::now();
        if ($lecture->unlock_date && Carbon::parse($lecture->unlock_date)->lte($today)) {
            $lock = 0;
        } else {
            $lock = 1;
        }
    } elseif ($course->drip_content == DRIP_PRE_IDS) {
        $pre_ids = $lecture->pre_ids ? json_decode($lecture->pre_ids) : [];
        $viewedIds = $course_lecture_views->pluck('course_lecture_id')->toArray();
        if (!array_diff($pre_ids, $viewedIds)) {
            $lock = 0;
        } else {
            $lock = 1;
        }
    }

    return $lock;
}

function studentCourseProgressApi($course_id, $enrollment_id,$user_id)
{
 
 
    $course = \App\Models\Course::whereId($course_id)->with('scorm_course')->first();
 
    if ($course->course_type == COURSE_TYPE_GENERAL) {
        $number_of_total_lecture = \App\Models\Course_lecture::where('course_id', $course_id)->count();
        $number_of_total_view_lecture = \App\Models\Course_lecture_views::where('course_id', $course_id)->where('enrollment_id', $enrollment_id)->where('user_id', $user_id)->count();
        $result = 0;
        if ($number_of_total_lecture) {
            $result = (($number_of_total_view_lecture * 100) / $number_of_total_lecture ?? 1);
        }
    
    }
    else if($course->course_type == 3){
        $number_of_total_lecture = \App\Models\Course_lecture::where('course_id', $course_id)->count();
        $number_of_total_view_lecture = \App\Models\Course_lecture_views::where('course_id', $course_id)->where('enrollment_id', $enrollment_id)->where('user_id', $user_id)->count();
        $result = 0;
        if ($number_of_total_lecture) {
            $result = (($number_of_total_view_lecture * 100) / $number_of_total_lecture ?? 1);
        }
    }
    else {
        $enrollment = Enrollment::whereId($enrollment_id)->first();
        if($course->scorm_course){
        $result = ($enrollment) ? ($enrollment->completed_time / $course->scorm_course?->duration_in_second) * 100 : 0;
        }
        else{
            $result = 0;
        }
    }

    return min($result, 100);
}



function studentCourseProgress($course_id, $enrollment_id)
{
 
 
    $course = \App\Models\Course::whereId($course_id)->with('scorm_course')->first();
 
    if ($course->course_type == COURSE_TYPE_GENERAL) {
        $number_of_total_lecture = \App\Models\Course_lecture::where('course_id', $course_id)->count();
        $number_of_total_view_lecture = \App\Models\Course_lecture_views::where('course_id', $course_id)->where('enrollment_id', $enrollment_id)->where('user_id', auth()->user()->id)->count();
        $result = 0;
        if ($number_of_total_lecture) {
            $result = (($number_of_total_view_lecture * 100) / $number_of_total_lecture ?? 1);
        }
    
    }
    else if($course->course_type == 3){
        $number_of_total_lecture = \App\Models\Course_lecture::where('course_id', $course_id)->count();
        $number_of_total_view_lecture = \App\Models\Course_lecture_views::where('course_id', $course_id)->where('enrollment_id', $enrollment_id)->where('user_id', auth()->user()->id)->count();
        $result = 0;
        if ($number_of_total_lecture) {
            $result = (($number_of_total_view_lecture * 100) / $number_of_total_lecture ?? 1);
        }
    }
    else {
        $enrollment = Enrollment::whereId($enrollment_id)->first();
        if($course->scorm_course){
        $result = ($enrollment) ? ($enrollment->completed_time / $course->scorm_course?->duration_in_second) * 100 : 0;
        }
        else{
            $result = 0;
        }
    }

    return min($result, 100);
}





function studentCourseProgressRemaining($course_id, $enrollment_id)
{
 
    $course = \App\Models\Course::whereId($course_id)->with('scorm_course')->first();
 
$lecture_ids = array();
    if ($course->course_type == COURSE_TYPE_GENERAL) {
        $number_of_total_lecture = \App\Models\Course_lecture::where('course_id', $course_id)->count();
        $total_view_lecture = \App\Models\Course_lecture_views::where('course_id', $course_id)
        ->where('enrollment_id', $enrollment_id)
        ->where('user_id', auth()->user()->id)
        // ->groupBy('course_lecture_id')
        ->count();

        // if(!empty($total_view_lecture)){
        //     foreach ($total_view_lecture as $key => $value) {
        //         $lecture_ids[] = $value->course_lecture_id;
        //     }
        //     $lecture_ids[] = array_unique($lecture_ids);
        // }
        $remaining = $number_of_total_lecture - $total_view_lecture;
     
     
    }
    else{
        $remaining  = 0;
    }
 

    return min($remaining, 100);
}



function getLeftDuration($start_date, $end_date)
{
    $startDate = date('d-m-Y H:i:s', strtotime($start_date));
    $endDate = date('d-m-Y H:i:s', strtotime($end_date));

    $secondsDifference = strtotime($endDate) - strtotime($startDate);

    //converting seconds to hours, minutes, seconds.
    $day = floor($secondsDifference / 86400);
    $hour = floor(($secondsDifference - ($day * 86400)) / 3600);
    $minute = floor(($secondsDifference / 60) % 60);
    $second = floor($secondsDifference % 60);

    if ($day > 0) {
        $day = $day . ($day > 1 ? ' days ' : ' day ');
        if ($hour > 0) {
            $hour = $hour . ($hour > 1 ? ' hours ' : ' hour ');
            return $day . $hour;
        }
        return $day;
    } elseif ($hour > 0) {
        $hour = $hour . ($hour > 1 ? ' hours ' : ' hour ');
        if ($minute) {
            $minute = $minute . ($minute > 1 ? ' minutes ' : ' minute ');
            return $hour . $minute;
        }
        return $hour;
    } elseif ($minute > 0) {
        $minute = $minute . ($minute > 1 ? ' minutes ' : ' minute ');
        return $minute;
    } elseif ($second > 0) {
        return $second;
    }
}

function lessonVideoDuration($course_id, $lesson_id)
{
    $lectures = \App\Models\Course_lecture::where('course_id', $course_id)->where('lesson_id', $lesson_id)->get();
    $video_duration = 0;
    $total_video_duration_in_seconds = 0;

    if ($lectures->count() > 0) {
        foreach ($lectures as $lecture) {
            if ($lecture->file_duration_second) {
                $total_video_duration_in_seconds += $lecture->file_duration_second;
            }
        }

        $h = floor($total_video_duration_in_seconds / 3600);
        $m = floor($total_video_duration_in_seconds % 3600 / 60);
        $s = floor($total_video_duration_in_seconds % 3600 % 60);

        if ($h > 0) {
            return "$h h $m m $s s";
        } elseif ($m > 0) {
            return "$m min $s sec";
        } elseif ($s > 0) {
            return "$s sec";
        }
    }

    return $video_duration;
}

function checkStudentCourseView($course_lecture_views, $course_id, $lecture_id)
{
    $views = $course_lecture_views->where('course_id', $course_id)->where('course_lecture_id', $lecture_id)->first();

    return $views;
}



function free_self_learning_courses(){
    $data = Course::where('status',1)->where('course_type',1)->where('learner_accessibility','free')->get();
    return $data;
}

function paid_self_learning_courses(){
    $data = Course::where('status',1)->where('course_type',1)->where('learner_accessibility','paid')->get();
    return $data;
}



function live_courses(){
    $data = Course::where('status',1)->where('course_type',3)->get();
    return $data;
}


function free_course_enroll($courseExits){
        DB::beginTransaction();
        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->order_number = rand(100000, 999999);
        $order->payment_status = 'free';
        $order->delivery_status = 1;
        $order->save();
        
        $order_item = new Order_item();
        $order_item->order_id = $order->id;
        $order_item->user_id = Auth::user()->id;
        $order_item->course_id = $courseExits->id;
        $order_item->owner_user_id = $courseExits->user_id ?? null;
        $order_item->unit_price = 0;
        $order_item->admin_commission = 0;
        $order_item->owner_balance = 0;
        $order_item->sell_commission = 0;
        $order_item->delivery_status = 3;
        $order_item->save();
        
       
     
        $enrollment = Enrollment::where('user_id',auth()->id())->where('course_id',$courseExits->id)->orderBy('id','DESC')->first();
        if($enrollment){
            $enrollment->order_id = $order->id;
            $enrollment->user_id = auth()->id();
            $enrollment->course_id = $courseExits->id;
            $enrollment->owner_user_id = $courseExits->user_id ?? null;
            $enrollment->start_date = now();
            $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
            $enrollment->request_enrollment = $order_item->request_enrollment ?? 0;
            $enrollment->save();
        }
        else{
            $enrollment = new Enrollment();
            $enrollment->order_id = $order->id;
            $enrollment->user_id = auth()->id();
            $enrollment->course_id = $courseExits->id;
            $enrollment->owner_user_id = $courseExits->user_id ?? null;
            $enrollment->start_date = now();
            $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
            $enrollment->request_enrollment = $order_item->request_enrollment ?? 0;
            $enrollment->save();

            Log::info("NEW ENROLLMENT BEING CREATED IN (free_course_enroll) IN HELPER CLASS AT ".Carbon::now());
            Log::info("NEW ENROLLMENT DETAILS IN (free_course_enroll) IN HELPER CLASS:- ".$enrollment);
        }
     
      
         DB::commit();
        return __("Free Self Learning Course added to your dashboard!");
}



function checkPaymentPending($user){
        $getData = CartManagement::whereUserId($user->id)
                ->whereNotNull('course_id')
                ->where('created_by_type',1)->get();
        if($getData->empty()){
            $getStudent = Student::where('user_id',$user->id)->first();
            if($getStudent->status == 0){
                $getStudent = Student::where('id',$getStudent->id)->first();
                if($getStudent){
                    $getStudent->status = 1;
                    $getStudent->save();
                }
            }
            return true;
        }
        else{
            return false;
        }
}



function paid_course_checkout($courseExits){
        $result_array = array();
        DB::beginTransaction();
        $enrollment = Enrollment::where(['course_id' => $courseExits->id , 'user_id' => Auth::user()->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
        if ($enrollment) {
            $order = Order::find($enrollment->order_id);
            if ($order) {
                if ($order->payment_status == 'due') {
                    Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                        $q->delete();
                    });
                    $order->delete();
                } elseif ($order->payment_status == 'pending') {
                    $result_array = [
                        'msg' => __("You've already request the course & status is pending!"),
                        'location' => 'course-details-page'
                    ];
                    // $response['msg'] = __("You've already request the course & status is pending!");
                    // $response['location'] = 'course-details-page';
                    DB::rollBack();
                    return $result_array;
                } 
                elseif ($order->payment_status == 'paid') {
                    $result_array = [
                        'msg' => __("You've already bought the course!"),
                        'location' => 'course-details-page'
                    ];
                    // $response['msg'] = __("You've already bought the course!");
                    // $response['location'] = 'course-details-page';
                    DB::rollBack();
                    return $result_array;
                }
                elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                    $result_array = [
                        'msg' => __("You've already enrolled the course!"),
                        'location' => 'course-details-page'
                    ];
                    // $response['msg'] = __("You've already enrolled the course!");
                    // $response['location'] = 'course-details-page';
                    DB::rollBack();
                    return $result_array;
                }
            } else {
                $result_array = [
                        'msg' => __("Something is wrong! Try again."),
                        'location' => 'course-details-page'
                ];
                // $response['msg'] = __("Something is wrong! Try again.");
                // $response['location'] = 'course-details-page';
                DB::rollBack();
                return $result_array;
            }
        }
        $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereCourseId($courseExits->id)->first();
        if ($cartExists) {
                $result_array = [
                        'msg' => __("Already added to cart!"),
                        'location' => 'course-details-page'
                ];
                // $response['msg'] = __("Already added to cart!");
                // $response['location'] = 'course-details-page';
                DB::rollBack();
                return $result_array;
        }
        $quantity = 1;
        $cart = NULL;
        if(is_null($cart)){
            $cart = new CartManagement();
        }else{
            $quantity = $cart->quantity;
        }
        $cart->user_id = Auth::user()->id;
        $cart->receiver_info = [];
        $cart->course_id = $courseExits->id;
        $cart->product_id = null;
        $cart->quantity = $quantity ?? 0;
        $cart->bundle_id = null;
        $cart->is_subscription_enable = $courseExits->is_subscription_enable;
        $cart->main_price = $courseExits->price;
        $cart->price = $courseExits->price;
        $cart->month = date('m');
        $cart->year = date('Y');
        $refData = '';
        if ($refData != '') {
                $refList = json_decode($refData);
                foreach ($refList as $refHash) {
                    $refPair = explode('$', $refHash);
                    if ($refPair[0] == $cart->course_id) {
                        $refRequest = AffiliateRequest::where(['affiliate_code' => $refPair[1], 'status' => STATUS_APPROVED])->first();
                        if (!is_null($refRequest)) {
                            $refUser = User::where(['id' => $refRequest->user_id])->first();
                            if ($refUser->is_affiliator == AFFILIATOR) {
                                $cart->reference = $refPair[1];
                                break;
                            }
                        }
                    }
                }
        }
        $cart->save();
        DB::commit();
   
        $carts = CartManagement::whereUserId(Auth::user()->id)->with('course.promotionCourse.promotion', 'bundle', 'consultationSlot.user.instructor')
        ->where('created_by_type',1)->get();
        $cartFinalPrice  = 0;
        foreach ($carts as $cart) {
            $cartFinalPrice += $cart->price;
        }
        $createdByType = 1;
        if(count($carts) > 0){
            if ($cartFinalPrice <= 0){   // FRee Cart
                // return redirect(route('student.checkoutFree',['type'=>$createdByType]));
                $result_array = [
                        'msg' => __("Added to cart"),
                        'location' => 'checkout-page-free'
                ];
                // $response['msg'] = __("Added to cart");
                // $response['location'] = 'checkout-page-free';
            }
            else{
                $result_array = [
                        'msg' => __("Added to cart"),
                        'location' => 'checkout-page'
                ];
                // $response['msg'] = __("Added to cart");
                // $response['location'] = 'checkout-page';
            }
            return $result_array;
        }
        else{
            $result_array = [
                        'msg' => __("Cart Is Empty"),
                        'location' => 'course-details-page'
            ];
            // $response['msg'] = __("Cart Is Empty");
            // $response['location'] = 'course-details-page';
            return $result_array;
        }
      
}




function live_course_checkout($courseExits, $Student, $trial_id, $requestData = array()){
        $user_id = $Student->user_id;
        $result_array = array();
        DB::beginTransaction();
        $enrollment = Enrollment::where(['course_id' => $courseExits->id , 'user_id' => $user_id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
      
        if ($enrollment) {
            $order = Order::find($enrollment->order_id);
           
            if ($order) {
                if ($order->payment_status == 'due') {
                    Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                        $q->delete();
                    });
                    $order->delete();
                } 
                elseif ($order->payment_status == 'pending') {
                    $result_array = [
                        'msg' => __("You've already request the course & status is pending!"),
                        'location' => 'course-details-page',
                        'is_true' => 0,
                         'cart_id' => null,
                    ];
                
                    DB::rollBack();
                    return $result_array;
                } 
                elseif ($order->payment_status == 'paid') {
                    $result_array = [
                    'msg' => __("You've already bought the course!"),
                        'location' => 'course-details-page',
                        'is_true' => 0,
                         'cart_id' => null,
                    ];
                 
                    DB::rollBack();
                    return $result_array;
                }
                elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                    $result_array = [
                        'msg' => __("You've already enrolled the course!"),
                        'location' => 'course-details-page',
                         'is_true' => 0,
                          'cart_id' => null,
                    ];
                 
                    DB::rollBack();
                    return $result_array;
                }
            } 
            else {
                $result_array = [
                        'msg' => __("Something is wrong! Try again."),
                        'location' => 'course-details-page',
                         'is_true' => 0,
                          'cart_id' => null,
                ];
              
                DB::rollBack();
                return $result_array;
            }
        }
        $cartExists = CartManagement::whereUserId($user_id)->whereCourseId($courseExits->id)
                        ->where('request_enrollment',1)
                        ->where('trial_id',$trial_id)->first();
        if ($cartExists) {
                $result_array = [
                        'msg' => __("Already added to cart!"),
                        'location' => 'course-details-page',
                         'is_true' => 0,
                          'cart_id' => $cartExists,
                ];
              
                DB::rollBack();
                return $result_array;
        }
        $quantity = 1;
        $cart = NULL;
        if(is_null($cart)){
            $cart = new CartManagement();
        }else{
            $quantity = $cart->quantity;
        }
        $cart->user_id = $user_id;
        $cart->receiver_info = [];
        $cart->course_id = $courseExits->id;
        $cart->product_id = null;
        $cart->request_enrollment = 1;
        $cart->quantity = $quantity ?? 0;
        $cart->students_count = $requestData['students_count'];
        $cart->bundle_id = null;
        $cart->is_subscription_enable = $courseExits->is_subscription_enable;
        $cart->original_price = $requestData['course_price'];
        $cart->discount_type = $requestData['discount_type_course_price'];
        $cart->discount = $requestData['discount_course_price'];
      
       
        $cart->net_original_price = $requestData['net_course_price'];
        $cart->discount_type_admission_fee = $requestData['discount_type_admission_fee'];
        $cart->discount_admission_fee = $requestData['discount_admission_fee'];
        $cart->net_admission_fee = $requestData['net_admission_fee'];

        // $cart->main_price = $courseExits->price;
        // $cart->price = $courseExits->price;
        $cart->admission_fee = $requestData['admission_fee'];
        $cart->main_price = $requestData['net_price'];
        $cart->price = $requestData['net_price'];
        $cart->trial_id = $trial_id;
        $cart->is_monthly_cron = 1;
        // $cart->created_by_type = 1; //Student
        $cart->month = date('m');
        $cart->year = date('Y');
        $refData = '';
        if ($refData != '') {
                $refList = json_decode($refData);
                foreach ($refList as $refHash) {
                    $refPair = explode('$', $refHash);
                    if ($refPair[0] == $cart->course_id) {
                        $refRequest = AffiliateRequest::where(['affiliate_code' => $refPair[1], 'status' => STATUS_APPROVED])->first();
                        if (!is_null($refRequest)) {
                            $refUser = User::where(['id' => $refRequest->user_id])->first();
                            if ($refUser->is_affiliator == AFFILIATOR) {
                                $cart->reference = $refPair[1];
                                break;
                            }
                        }
                    }
                }
        }
        $cart->save();
        DB::commit();
   
        $carts = CartManagement::whereUserId($user_id)->with('course.promotionCourse.promotion', 'bundle', 'consultationSlot.user.instructor')
        ->where('created_by_type',1)->get();
        $cartFinalPrice  = 0;
        foreach ($carts as $cart) {
            $cartFinalPrice += $cart->price;
        }
        $createdByType = 1;
        if(count($carts) > 0){
            if ($cartFinalPrice <= 0){   // FRee Cart
              
                $result_array = [
                        'msg' => __("Added to cart"),
                        'location' => 'checkout-page-free',
                         'is_true' => 0,
                        'cart_id' => $cart->id,
                ];
              
            }
            else{
                $result_array = [
                        'msg' => __("Added to cart"),
                        'location' => 'checkout-page',
                         'is_true' => 1,
                         'cart_id' => $cart->id,
                ];
              
            }
            return $result_array;
        }
        else{
            $result_array = [
                        'msg' => __("Cart Is Empty"),
                        'location' => 'course-details-page',
                         'is_true' => 0,
                        'cart_id' => null,
            ];
          
            return $result_array;
        }
      
}

/**
 * Admin/cron monthly fee cart for the current calendar month (billing period).
 * Used to avoid duplicate pending rows for the same student + course + month.
 */
function existing_pending_monthly_cron_cart($userId, $courseId): ?CartManagement
{
    $service = app(\App\Services\MonthlyFeeBillingService::class);
    $period = $service->billingPeriod();

    return $service->hasPendingCart($userId, $courseId, $period['month'], $period['year']);
}

/**
 * Returns a skip payload when a monthly fee already exists for the billing period, or null if clear.
 */
function monthly_fee_period_blocked($userId, $courseId, ?\Carbon\Carbon $forDate = null): ?array
{
    $service = app(\App\Services\MonthlyFeeBillingService::class);
    $period = $service->billingPeriod($forDate);

    $pending = $service->hasPendingCart($userId, $courseId, $period['month'], $period['year']);
    if ($pending !== null) {
        return [
            'reason' => 'pending_cart_exists',
            'cart_id' => $pending->id,
            'msg' => __('A pending monthly payment for this course and period already exists.'),
        ];
    }

    $paid = $service->hasPaidFee($userId, $courseId, $period['month'], $period['year']);
    if ($paid !== null) {
        return [
            'reason' => 'paid_order_exists',
            'order_id' => $paid->id,
            'msg' => __('A paid monthly payment for this course and period already exists.'),
        ];
    }

    return null;
}

function live_course_checkout_cron($courseExits, $Student, $trial_id, $requestData = array()){
        $user_id = $Student->user_id;
        $result_array = array();

        $billingService = app(\App\Services\MonthlyFeeBillingService::class);
        $period = $billingService->billingPeriod();
        $lock = $billingService->tryAcquireLock($user_id, $courseExits->id, $period['month'], $period['year']);

        if ($lock !== null && !$lock->block(10)) {
            return [
                'msg' => __('Could not acquire billing lock. Please try again.'),
                'location' => 'checkout-page',
                'is_true' => 0,
                'cart_id' => null,
            ];
        }

        try {
            $blocked = monthly_fee_period_blocked($user_id, $courseExits->id);
            if ($blocked !== null) {
                return [
                    'msg' => $blocked['msg'],
                    'location' => 'checkout-page',
                    'is_true' => 1,
                    'cart_id' => $blocked['cart_id'] ?? null,
                ];
            }

            DB::beginTransaction();

            $blocked = monthly_fee_period_blocked($user_id, $courseExits->id);
            if ($blocked !== null) {
                DB::rollBack();
                return [
                    'msg' => $blocked['msg'],
                    'location' => 'checkout-page',
                    'is_true' => 1,
                    'cart_id' => $blocked['cart_id'] ?? null,
                ];
            }
       
        $quantity = 1;
        $cart = NULL;
        if(is_null($cart)){
            $cart = new CartManagement();
        }else{
            $quantity = $cart->quantity;
        }
        $cart->user_id = $user_id;
        $cart->receiver_info = [];
        $cart->course_id = $courseExits->id;
        $cart->product_id = null;
        $cart->request_enrollment = 1;
        $cart->quantity = $quantity ?? 0;
        $cart->bundle_id = null;
        $cart->is_subscription_enable = $courseExits->is_subscription_enable;
        $cart->original_price = $requestData['course_price'];
        $cart->discount_type = $requestData['discount_type_course_price'];
        $cart->discount = $requestData['discount_course_price'];
      
       
        $cart->net_original_price = $requestData['net_course_price'];
        $cart->discount_type_admission_fee = $requestData['discount_type_admission_fee'];
        $cart->discount_admission_fee = $requestData['discount_admission_fee'];
        $cart->net_admission_fee = $requestData['net_admission_fee'];

        // $cart->main_price = $courseExits->price;
        // $cart->price = $courseExits->price;
        $cart->admission_fee = $requestData['admission_fee'];
        $cart->main_price = $requestData['net_price'];
        $cart->price = $requestData['net_price'];
        $cart->trial_id = $trial_id;
        $cart->is_monthly_cron = 1;
        $cart->created_by_type = 2; //ADMIN
        $cart->month = (int) date('n');
        $cart->year = (int) date('Y');

        $refData = '';
        if ($refData != '') {
                $refList = json_decode($refData);
                foreach ($refList as $refHash) {
                    $refPair = explode('$', $refHash);
                    if ($refPair[0] == $cart->course_id) {
                        $refRequest = AffiliateRequest::where(['affiliate_code' => $refPair[1], 'status' => STATUS_APPROVED])->first();
                        if (!is_null($refRequest)) {
                            $refUser = User::where(['id' => $refRequest->user_id])->first();
                            if ($refUser->is_affiliator == AFFILIATOR) {
                                $cart->reference = $refPair[1];
                                break;
                            }
                        }
                    }
                }
        }
        $cart->save();
        DB::commit();
   
        $carts = CartManagement::whereUserId($user_id)->with('course.promotionCourse.promotion', 'bundle', 'consultationSlot.user.instructor')
        ->where('created_by_type',1)->get();
        $cartFinalPrice  = 0;
        foreach ($carts as $cart) {
            $cartFinalPrice += $cart->price;
        }
        $createdByType = 1;
        if(count($carts) > 0){
            if ($cartFinalPrice <= 0){   // FRee Cart
              
                $result_array = [
                        'msg' => __("Added to cart"),
                        'location' => 'checkout-page-free',
                         'is_true' => 0,
                        'cart_id' => $cart->id,
                ];
              
            }
            else{
                $result_array = [
                        'msg' => __("Added to cart"),
                        'location' => 'checkout-page',
                         'is_true' => 1,
                         'cart_id' => $cart->id,
                ];
              
            }
            return $result_array;
        }
        else{
            $result_array = [
                        'msg' => __("Cart Is Empty"),
                        'location' => 'course-details-page',
                         'is_true' => 0,
                        'cart_id' => null,
            ];
          
            return $result_array;
        }
        } finally {
            if ($lock !== null) {
                $lock->release();
            }
        }
      
}



function checkIfExpired($enrollment)
{
    $today = now();
    $isExpired = Carbon::parse($enrollment->end_date)->gte($today);
    return $isExpired;
}

function checkIfLifetime($date)
{
    $isEqual = Carbon::parse($date)->eq(MAX_EXPIRED_DATE);
    return $isEqual;
}

function studentCoursesCount($user_id)
{
    $orderItems = Enrollment::where('user_id', $user_id)->count();

    return $orderItems;
}

function countUserReplies($user_id = null)
{
    return ForumPostComment::whereUserId($user_id)->count();
}

function getDayAvailableStatus($day)
{
    $item = InstructorConsultationDayStatus::where('user_id', Auth::id())->where('day', $day)->first();
    if ($item) {
        $status = 1;
    } else {
        $status = 0;
    }

    return $status;
}

function getInstructorTotalReview($user_id)
{
    $courseIds = Course::where('user_id', $user_id)->pluck('id')->toArray();
    return Review::whereIn('course_id', $courseIds)->count();
}

function getInstructorName($id)
{
    $user = Instructor::whereUserId($id)->first();
    return @$user->full_name ?? '';
}

function getBookingHistoryDetails($consultation_slot_id)
{
    $booking = BookingHistory::where('consultation_slot_id', $consultation_slot_id)->first();
    $bookingArray = [
        'time' => $booking->time ?? '',
        'type' => $booking->type ?? ''
    ];

    return $bookingArray;
}

function getBundleDetails($id)
{
    $bundle = Bundle::find($id);
    return $bundle;
}

function getCourseStudents($course_id,$user_id){
      $instructorCourseIds = array();
        $getInstructorCourses = CourseInstructor::where('instructor_id',$user_id)->whereHas('course', function ($query) {
                    $query->where('status',1); // user has at least one review
                })->get();
        if(!$getInstructorCourses->isEmpty()){
            foreach ($getInstructorCourses as $key => $value) {
                $instructorCourseIds[] = $value->course_id;
            }
         
        }
        else{
            $getInstructorCourses = Course::where('instructor_id',$instructor->id)->get();
            if(!$getInstructorCourses->isEmpty()){
                foreach ($getInstructorCourses as $key => $value) {
                    $instructorCourseIds[] = $value->id;
                }
            }
        }
        $instructorCourseIds = array_unique( $instructorCourseIds);
          $totalEnrollments = Enrollment::whereIn('course_id', $instructorCourseIds)->where('status',ACCESS_PERIOD_ACTIVE)->where('end_date', '>=', now())->count();
        // $data['totalStudent'] = Enrollment::where('owner_user_id', $user->id)->groupBy('user_id')->count();
        return $totalEnrollments;

}
function getUserAverageRating($user_id)
{
    $courseIds = Course::where('user_id', $user_id)->pluck('id')->toArray();

    $data['five_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(5)->count();
    $data['four_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(4)->count();
    $data['three_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(3)->count();
    $data['two_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(2)->count();
    $data['first_star_count'] = Review::whereIn('course_id', $courseIds)->whereRating(1)->count();

    $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
        (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
    $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];

    if ($data['total_user_review'] > 0) {
        $average_rating = $data['total_reviews'] / $data['total_user_review'];
    } else {
        $average_rating = 0;
    }

    return $average_rating;
}

function courseStudents($course_id)
{
    $total_course_students = Enrollment::where('course_id', $course_id)->count();
    return $total_course_students;
}

function getCourseAffiliateAmount($course_id)
{
    return AffiliateHistory::where(['course_id' => $course_id, 'status' => AFFILIATE_HISTORY_STATUS_PAID])->sum('commission');
}

function cart_total_with_conversion_rate($payment_method, $carts = null)
{
    if (is_null($carts)) {
        $carts = CartManagement::whereUserId(@Auth::user()->id)->get();
    }
    $grand_total = get_platform_charge($carts->sum('price')) + $carts->sum('price');

    if ($payment_method == 'paypal') {
        $conversion_rate = get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 0;
    } elseif ($payment_method == 'stripe') {
        $conversion_rate = get_option('stripe_conversion_rate') ? get_option('stripe_conversion_rate') : 0;
    } elseif ($payment_method == 'bank') {
        $conversion_rate = get_option('bank_conversion_rate') ? get_option('bank_conversion_rate') : 0;
    } elseif ($payment_method == 'mollie') {
        $conversion_rate = get_option('mollie_conversion_rate') ? get_option('mollie_conversion_rate') : 0;
    } elseif ($payment_method == 'instamojo') {
        $conversion_rate = get_option('im_conversion_rate') ? get_option('im_conversion_rate') : 0;
    } elseif ($payment_method == 'paystack') {
        $conversion_rate = get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0;
    } elseif ($payment_method == 'sslcommerz') {
        $conversion_rate = get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0;
    } elseif ($payment_method == 'mercadopago') {
        $conversion_rate = get_option('mercado_conversion_rate') ? get_option('mercado_conversion_rate') : 0;
    } elseif ($payment_method == 'flutterwave') {
        $conversion_rate = get_option('flutterwave_conversion_rate') ? get_option('flutterwave_conversion_rate') : 0;
    } elseif ($payment_method == 'coinbase') {
        $conversion_rate = get_option('coinbase_conversion_rate') ? get_option('coinbase_conversion_rate') : 0;
    } elseif ($payment_method == 'zitopay') {
        $conversion_rate = get_option('zitopay_conversion_rate') ? get_option('zitopay_conversion_rate') : 0;
    } elseif ($payment_method == 'iyzipay') {
        $conversion_rate = get_option('iyzipay_conversion_rate') ? get_option('iyzipay_conversion_rate') : 0;
    } elseif ($payment_method == 'bitpay') {
        $conversion_rate = get_option('bitpay_conversion_rate') ? get_option('bitpay_conversion_rate') : 0;
    } elseif ($payment_method == 'braintree') {
        $conversion_rate = get_option('braintree_conversion_rate') ? get_option('braintree_conversion_rate') : 0;
    }

    return $grand_total * $conversion_rate;
}

function distributeCommission($order)
{
    $cashbackType = get_option('cashback_type');
    $cashbackAmount = get_option('cashback_amount');

    foreach ($order->items as $order_item) {
        $totalCashback = 0;
        $ownerId = $order_item->owner_user_id;
        $distributedAmount = 0;
        if (!is_null($order_item->course_id)) {
            $courseInstructors = CourseInstructor::where('course_id', $order_item->course_id)->whereStatus(STATUS_ACCEPTED)->get();
            foreach ($courseInstructors as $courseInstructor) {
                $user_id = $courseInstructor->instructor_id;
                if ($ownerId != $user_id) {
                    $balance = ($order_item->owner_balance / 100) * ($courseInstructor->share);
                    $distributedAmount += $balance;
                    if ($balance > 0) {
                        createTransaction($user_id, $balance, TRANSACTION_SELL, 'Earning via sell', 'Order_item (' . $order_item->id . ')', $order_item->id);
                        $owner_user = User::find($user_id);
                        if ($owner_user) {
                            $owner_user->increment('balance', decimal_to_int($balance));
                        }
                    }
                }
            }

            if (get_option('cashback_system_mode')) {
                $unitPrice = $order_item->unit_price;
                $unit = $order_item->unit;
                $amount = $unit * $unitPrice;
                if ($cashbackType == 1) {
                    $totalCashback += ($amount / 100) * $cashbackAmount;
                } else {
                    $totalCashback += $cashbackAmount;
                }
            }

            //setCashback
            if ($totalCashback > 0) {
                $order->user->increment('balance', decimal_to_int($totalCashback));
                createTransaction($order->user_id, $totalCashback, TRANSACTION_CASHBACK, 'Cashback', 'Order_item (' . $order_item->id . ')', $order_item->id);
            }
        }

        $remainingBalance = $order_item->owner_balance - $distributedAmount;
        if ($remainingBalance > 0) {
            createTransaction($ownerId, $remainingBalance, TRANSACTION_SELL, 'Earning via sell', 'Order_item (' . $order_item->id . ')', $order_item->id);
            $owner_user = User::find($ownerId);
            if ($owner_user) {
                $owner_user->increment('balance', decimal_to_int($remainingBalance));
            }
        }

        if (!is_null($order_item->course_id)) {
            setEnrollment($order_item);
            setBadge($order_item->user_id);
        }

        $affiliateHistory = AffiliateHistory::whereOrderItemId($order_item->id)->first();
        if ($affiliateHistory) {
            $refUser = User::find($affiliateHistory->user_id);
            $refUser->increment('balance', decimal_to_int($affiliateHistory->commission));
            createTransaction($refUser->id, $affiliateHistory->commission, TRANSACTION_AFFILIATE, AFFILIATE_NARRATION,);
            $affiliateHistory->update(['status' => AFFILIATE_HISTORY_STATUS_PAID]);
        }
    }
}


if (!function_exists('hasLimit')) {
    function hasLimit($type, $count)
    {
        if (get_option('subscription_mode')) {
            $userPackage = UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->where('package_type', PACKAGE_TYPE_SUBSCRIPTION)->where('user_packages.user_id', auth()->id())->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->with('enrollments')->select('user_packages.*')->first();
            if ($type == PACKAGE_RULE_COURSE) {
                $limit = @$userPackage->course;
            } else if ($type == PACKAGE_RULE_BUNDLE_COURSE) {
                $limit = @$userPackage->bundle_course;
            } else if ($type == PACKAGE_RULE_CONSULTANCY) {
                $limit = @$userPackage->consultancy;
            }

            $used = (@$userPackage->enrollments) ? @$userPackage->enrollments->count() : 0;

            if ($limit >= ($used + $count)) {
                return true;
            }

            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('hasLimitSaaS')) {
    function hasLimitSaaS($type, $package_type, $count)
    {
        if (get_option('saas_mode')) {
            $userPackage = UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->where('package_type', $package_type)->where('user_packages.user_id', auth()->id())->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
            if ($type == PACKAGE_RULE_STUDENT) {
                $limit = @$userPackage->student;
            } else if ($type == PACKAGE_RULE_COURSE) {
                $limit = @$userPackage->course;
            } else if ($type == PACKAGE_RULE_BUNDLE_COURSE) {
                $limit = @$userPackage->bundle_course;
            } else if ($type == PACKAGE_RULE_SUBSCRIPTION_COURSE) {
                $limit = @$userPackage->subscription_course;
            } else if ($type == PACKAGE_RULE_CONSULTANCY) {
                $limit = @$userPackage->consultancy;
            }

            if ($limit >= $count + 1) {
                return true;
            }

            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('getSlug')) {
    function getSlug($text)
    {
        if ($text) {
            $data = preg_replace("/[~`{}.'\"\!\@\#\$\%\^\&\*\(\)\_\=\+\/\?\>\<\,\[\]\:\;\|\\\]/", "", $text);
            $slug = preg_replace("/[\/_|+ -]+/", "-", $data);
            return $slug;
        }
        return '';
    }
}

if (!function_exists('setEnrollment')) {
    function setEnrollment($item, $expiredDays = NULL)
    {
        
        if($item->consultation_slot_id != NULL){
            $enrollment = Enrollment::where('user_id',auth()->id())->where('consultation_slot_id',$item->consultation_slot_id)->orderBy('id','DESC')->first();
        }
        else if($item->bundle_id != NULL){
            $enrollment = Enrollment::where('user_id',auth()->id())->where('bundle_id',$item->bundle_id)->orderBy('id','DESC')->first();
        }
        else{
            $enrollment = Enrollment::where('user_id',auth()->id())->where('course_id',$item->course_id)->orderBy('id','DESC')->first();
        }

        if($enrollment){
            $enrollment->order_id = $item->order_id;
            if (is_array($item->receiver_info) && count($item->receiver_info)) {
                $user = User::where('email', $item->receiver_info['receiver_email'])->first();
                $enrollment->user_id = $user->id;
            } else {
                $enrollment->user_id = $item->user_id;
            }

            $enrollment->owner_user_id = $item->owner_user_id;
            $enrollment->bundle_id = $item->bundle_id;
            $enrollment->course_id = $item->course_id;
            $enrollment->consultation_slot_id = $item->consultation_slot_id;
            if ($item->consultation_slot_id != NULL) {
                $consultationSlot = $item->consultationSlot;
                if (!is_null($consultationSlot)) {
                    $fullTime = explode(' - ', $consultationSlot->time);
                    $startDate = $consultationSlot->date . ' ' . date("H:i", strtotime($fullTime[0]));
                    $endDate = $consultationSlot->date . ' ' . date("H:i", strtotime($fullTime[1]));
                    $enrollment->start_date = date("Y-m-d H:i:s", strtotime($startDate));
                    $enrollment->end_date = date("Y-m-d H:i:s", strtotime($endDate));
                }
            } 
            elseif ($item->bundle_id != NULL) {
                //update status to deactivated if already valid in enrollment table
                Enrollment::where(['course_id' => $item->course_id, 'user_id' => auth()->id(), 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->update(['status' => ACCESS_PERIOD_DEACTIVATE]);
                $enrollment->start_date = now();
                $enrollment->end_date = ($item->bundle->access_period) ? Carbon::now()->addDays($item->bundle->access_period) : MAX_EXPIRED_DATE;
            } 
            else {
                $enrollment->start_date = now();
                if (!is_null($expiredDays)) {
                    $enrollment->end_date = Carbon::now()->addDays($expiredDays);
                } else {
                    $enrollment->end_date = ($item->course->access_period) ? Carbon::now()->addDays($item->course->access_period) : MAX_EXPIRED_DATE;
                }
            }
            $enrollment->save();
        }
        else{
            $enrollment = new Enrollment();
            $enrollment->order_id = $item->order_id;
            if (is_array($item->receiver_info) && count($item->receiver_info)) {
                $user = User::where('email', $item->receiver_info['receiver_email'])->first();
                $enrollment->user_id = $user->id;
            } else {
                $enrollment->user_id = $item->user_id;
            }

            $enrollment->owner_user_id = $item->owner_user_id;
            $enrollment->bundle_id = $item->bundle_id;
            $enrollment->course_id = $item->course_id;
            $enrollment->consultation_slot_id = $item->consultation_slot_id;
            if ($item->consultation_slot_id != NULL) {
                $consultationSlot = $item->consultationSlot;
                if (!is_null($consultationSlot)) {
                    $fullTime = explode(' - ', $consultationSlot->time);
                    $startDate = $consultationSlot->date . ' ' . date("H:i", strtotime($fullTime[0]));
                    $endDate = $consultationSlot->date . ' ' . date("H:i", strtotime($fullTime[1]));
                    $enrollment->start_date = date("Y-m-d H:i:s", strtotime($startDate));
                    $enrollment->end_date = date("Y-m-d H:i:s", strtotime($endDate));
                }
            } 
            elseif ($item->bundle_id != NULL) {
                //update status to deactivated if already valid in enrollment table
                Enrollment::where(['course_id' => $item->course_id, 'user_id' => auth()->id(), 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->update(['status' => ACCESS_PERIOD_DEACTIVATE]);
                $enrollment->start_date = now();
                $enrollment->end_date = ($item->bundle->access_period) ? Carbon::now()->addDays($item->bundle->access_period) : MAX_EXPIRED_DATE;
            } 
            else {
                $enrollment->start_date = now();
                if (!is_null($expiredDays)) {
                    $enrollment->end_date = Carbon::now()->addDays($expiredDays);
                } else {
                    $enrollment->end_date = ($item->course->access_period) ? Carbon::now()->addDays($item->course->access_period) : MAX_EXPIRED_DATE;
                }
            }

          
            $enrollment->save();
            Log::info("NEW ENROLLMENT BEING CREATED IN (setEnrollment) IN HELPER CLASS AT ".Carbon::now());
             Log::info("NEW ENROLLMENT DETAILS IN (setEnrollment) IN HELPER CLASS:- ".$enrollment);
        }
      
        return $enrollment;
    }
}


if (!function_exists('getUserRoleRelation')) {
    function getUserRoleRelation($user)
    {
        if ($user->role == USER_ROLE_INSTRUCTOR) {
            $return = 'instructor';
        } elseif ($user->role == USER_ROLE_ORGANIZATION) {
            $return = 'organization';
        } 
        elseif ($user->role == 5) {
            $return = 'coordinator';
        } else {
            $return = 'student';
        }

        return $return;
    }
}

if (!function_exists('selectStatement')) {
    function selectStatement()
    {
        return 'case when org.id is null then ins.uuid else org.uuid end as uuid,
        case when org.id is null then ins.country_id else org.country_id end as country_id,
        case when org.id is null then ins.province_id else org.province_id end as province_id,
        case when org.id is null then ins.state_id else org.state_id end as state_id,
        case when org.id is null then ins.city_id else org.city_id end as city_id,
        case when org.id is null then ins.professional_title else org.professional_title end as professional_title,
        case when org.id is null then ins.postal_code else org.postal_code end as postal_code,
        case when org.id is null then ins.address else org.address end as address,
        case when org.id is null then ins.about_me else org.about_me end as about_me,
        case when org.id is null then ins.gender else org.gender end as gender,
        case when org.id is null then ins.social_link else org.social_link end as social_link,
        case when org.id is null then ins.slug else org.slug end as slug,
        case when org.id is null then ins.is_private else org.is_private end as is_private,
        case when org.id is null then ins.remove_from_web_search else org.remove_from_web_search end as remove_from_web_search,
        case when org.id is null then ins.is_offline else org.is_offline end as is_offline,
        case when org.id is null then ins.offline_message else org.offline_message end as offline_message,
        case when org.id is null then ins.consultation_available else org.consultation_available end as consultation_available,
        case when org.id is null then ins.hourly_rate else org.hourly_rate end as hourly_rate,
        case when org.id is null then ins.hourly_old_rate else org.hourly_old_rate end as hourly_old_rate,
        case when org.id is null then ins.available_type else org.available_type end as available_type,
        case when org.id is null then ins.created_at else org.created_at end as approval_date';
    }
}


if (!function_exists('setBadge')) {
    function setBadge($id)
    {
        $user = User::whereId($id)->with('instructor.enrollments.order')->with('organization.enrollments.order')->first();
        if ($user->role == USER_ROLE_INSTRUCTOR || $user->role == USER_ROLE_ORGANIZATION) {
            $badges = RankingLevel::all();
            $relation = getUserRoleRelation($user);

            if (!is_null($user->$relation)) {
                $enrollments = $user->$relation->enrollments;
                $approvalDuration = Carbon::parse($user->$relation->created_at)->diffInDays(now());
                $totalEarning = $enrollments->map(function ($enrollment) {
                    if ($enrollment->order) {
                        return $enrollment->order->sum('sub_total');
                    }
                })->sum();
                $totalSale = $enrollments->count();
            } else {
                $approvalDuration = Carbon::parse($user->created_at)->diffInDays(now());
                $totalSale = 0;
                $totalEarning = 0;
            }

            $totalStudentCount = Enrollment::where('owner_user_id', $user->id)->groupBy('user_id')->count();
            $totalCourse = Course::where('user_id', $user->id)->count();

            //set membership badge
            $typeArray = [RANKING_LEVEL_REGISTRATION => $approvalDuration, RANKING_LEVEL_EARNING => $totalEarning, RANKING_LEVEL_COURSES_COUNT => $totalCourse, RANKING_LEVEL_STUDENTS_COUNT => $totalStudentCount, RANKING_LEVEL_COURSES_SALE_COUNT => $totalSale];
            UserBadge::where('user_id', $user->id)->delete();
            foreach ($typeArray as $type => $value) {
                $rule = $badges->where('type', $type)->where('from', '<=', $value)->where('to', '>=', $value)->first();
                $maxRule = $badges->where('type', $type)->where('to', '<=', $value)->sortByDesc('to')->first();
                Log::info('value' . $value);
                Log::info('rule' . $rule);
                Log::info('maxRule' . $maxRule);
                $ranking_level_id = NULL;
                if (!is_null($rule)) {
                    $ranking_level_id = $rule->id;
                } elseif (!is_null($maxRule)) {
                    $ranking_level_id = $maxRule->id;
                }

                if (!is_null($ranking_level_id)) {
                    UserBadge::create(['user_id' => $user->id, 'ranking_level_id' => $ranking_level_id]);
                }
            }
        }
    }
}


if (!function_exists('getCustomerCurrentBuildVersion')) {
    function getCustomerCurrentBuildVersion()
    {
        $buildVersion = get_option('app_version');

        if (is_null($buildVersion)) {
            return 1;
        }

        return (int)$buildVersion;
    }
}

if (!function_exists('setCustomerBuildVersion')) {
    function setCustomerBuildVersion($version)
    {
        $option = Setting::firstOrCreate(['option_key' => 'app_version']);
        $option->option_value = $version;
        $option->save();
    }
}

if (!function_exists('setCustomerCurrentVersion')) {
    function setCustomerCurrentVersion()
    {
        $option = Setting::firstOrCreate(['option_key' => 'current_version']);
        $option->option_value = config('app.current_version');
        $option->save();
    }
}

if (!function_exists('get_domain_name')) {
    function get_domain_name($url)
    {
        $parseUrl = parse_url(trim($url));
        if (isset($parseUrl['host'])) {
            $host = $parseUrl['host'];
        } else {
            $path = explode('/', $parseUrl['path']);
            $host = $path[0];
        }
        return trim($host);
    }
}

if (!function_exists('getBeneficiaryDetails')) {
    function getBeneficiaryDetails($item)
    {
        $returnData = '';
        if (!is_null($item)) {
            if ($item->type == BENEFICIARY_BANK) {
                $returnData .= '<span><strong>Bank Name : </strong>' . $item->bank_name . '</span>
                <span><strong>Bank Account Number : </strong>' . $item->bank_account_number . '</span>
                <span><strong>Bank Account Name : </strong>' . $item->bank_account_name . '</span>
                <span><strong>Routing Number : </strong>' . $item->bank_routing_number . '</span>';
            } elseif ($item->type == BENEFICIARY_CARD) {
                $returnData .= '<span><strong>Card Holder Name : </strong>' . $item->card_holder_name . '</span>
                <span><strong>Card Number : </strong>' . $item->card_number . '</span>
                <span><strong>Expired Date : </strong>' . $item->expire_month . '/' . $item->expire_year . '</span>';
            } elseif ($item->type == BENEFICIARY_PAYPAL) {
                $returnData .= '<span><strong>Paypal Email : </strong>' . $item->paypal_email . '</span>';
            }
        }

        return $returnData;
    }
}

if (!function_exists('getBeneficiaryDetails')) {
    function getBeneficiaryDetails($item)
    {
        $returnData = '';
        if (!is_null($item)) {
            if ($item->type == BENEFICIARY_BANK) {
                $returnData .= '<span><strong>Bank Name : </strong>' . $item->bank_name . '</span>
                <span><strong>Bank Account Number : </strong>' . $item->bank_account_number . '</span>
                <span><strong>Bank Account Name : </strong>' . $item->bank_account_name . '</span>
                <span><strong>Routing Number : </strong>' . $item->bank_routing_number . '</span>';
            } elseif ($item->type == BENEFICIARY_CARD) {
                $returnData .= '<span><strong>Card Holder Name : </strong>' . $item->card_holder_name . '</span>
                <span><strong>Card Number : </strong>' . $item->card_number . '</span>
                <span><strong>Expired Date : </strong>' . $item->expire_month . '/' . $item->expire_year . '</span>';
            } elseif ($item->type == BENEFICIARY_PAYPAL) {
                $returnData .= '<span><strong>Paypal Email : </strong>' . $item->paypal_email . '</span>';
            }
        }

        return $returnData;
    }
}

if (!function_exists('getBeneficiaryAccountDetails')) {
    function getBeneficiaryAccountDetails($item)
    {
        $returnData = '';
        if (!is_null($item)) {
            if ($item->type == BENEFICIARY_BANK) {
                $returnData .= $item->bank_account_name . ' - ' . $item->bank_name . '[' . $item->bank_account_number . ']';
            } elseif ($item->type == BENEFICIARY_CARD) {
                $returnData .= $item->card_holder_name . ' - ' . $item->card_number;
            } elseif ($item->type == BENEFICIARY_PAYPAL) {
                $returnData .= 'Paypal - ' . $item->paypal_email;
            }
        }

        return $returnData;
    }
}
if (!function_exists('updateEnv')) {
    function updateEnv($values)
    {
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                setEnvironmentValue($envKey, $envValue);
            }
            return true;
        }
    }
}
if (!function_exists('updateManifest')) {
    function updateManifest()
    {
        $manifest = [
            "name" => get_option('app_name', 'LMSZAI'),
            "short_name" => get_option('app_name', 'LMSZAI'),
            "start_url" => route('main.index'),
            "background_color" => get_option('app_theme_color', '#5e3fd7'),
            "description" => get_option('app_name', 'LMSZAI'),
            "display" => "fullscreen",
            "theme_color" => get_option('app_theme_color', '#5e3fd7'),
            "icons" => [
                [
                    "src" => getImageFile(get_option('app_pwa_icon')),
                    "sizes" => "512x512",
                    "type" => "image/png",
                    "purpose" => "any maskable"
                ]
            ]
        ];

        file_put_contents(public_path("manifest.json"), json_encode($manifest));
    }
}
function setEnvironmentValue($envKey, $envValue)
{
    try {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        if ($keyPosition) {
            if (PHP_OS_FAMILY === 'Windows') {
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
            } else {
                $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
            }
            $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
            $envValue = str_replace(chr(92), "\\\\", $envValue);
            $envValue = str_replace('"', '\"', $envValue);
            $newLine = "{$envKey}=\"{$envValue}\"";
            if ($oldLine != $newLine) {
                $str = str_replace($oldLine, $newLine, $str);
                $str = substr($str, 0, -1);
                $fp = fopen($envFile, 'w');
                fwrite($fp, $str);
                fclose($fp);
            }
        } else if (strtoupper($envKey) == $envKey) {
            $envValue = str_replace(chr(92), "\\\\", $envValue);
            $envValue = str_replace('"', '\"', $envValue);
            $newLine = "{$envKey}=\"{$envValue}\"\n";
            $str .= $newLine;
            $str = substr($str, 0, -1);
            $fp = fopen($envFile, 'w');
            fwrite($fp, $str);
            fclose($fp);
        }
        return true;
    } catch (\Exception $e) {
        return false;
    }


}

function getStudentFromUser($user){
    $get1 = Student::where('user_id',$user->id)->first();
    return $get1;
}

if (!function_exists('getTimeZone')) {
    function getTimeZone()
    {
        return DateTimeZone::listIdentifiers(
            DateTimeZone::ALL
        );
    }
}

if (!function_exists('calculateCashback')) {
    function calculateCashback($amount)
    {
        $cashbackType = get_option('cashback_type');
        $cashbackAmount = get_option('cashback_amount');
        if ($cashbackType == 1) {
            return ($amount / 100) * $cashbackAmount;
        } else {
            return $cashbackAmount;
        }
    }
}


if (!function_exists('getCustomerCurrentBuildVersion')) {
    function getCustomerCurrentBuildVersion()
    {
        $buildVersion = get_option('build_version');

        if (is_null($buildVersion)) {
            return 1;
        }

        return (int)$buildVersion;
    }
}

if (!function_exists('getCustomerAddonBuildVersion')) {
    function getCustomerAddonBuildVersion($code)
    {
        $buildVersion = get_option($code . '_build_version', 0);
        if (is_null($buildVersion)) {
            return 0;
        }
        return (int)$buildVersion;
    }
}

if (!function_exists('isAddonInstalled')) {
    function isAddonInstalled($code)
    {
        $buildVersion = get_option($code . '_build_version', 0);
        $codeBuildVersion = getAddonCodeBuildVersion($code);
        if ($buildVersion == 0 || $codeBuildVersion == 0) {
            return false;
        }
        return true;
    }
}

if (!function_exists('setCustomerAddonCurrentVersion')) {
    function setCustomerAddonCurrentVersion($code)
    {
        $option = Setting::firstOrCreate(['option_key' => $code . '_current_version']);
        $option->option_value = getAddonCodeCurrentVersion($code);
        $option->save();
    }
}

if (!function_exists('setCustomerAddonBuildVersion')) {
    function setCustomerAddonBuildVersion($code, $version)
    {
        $option = Setting::firstOrCreate(['option_key' => $code . '_build_version']);
        $option->option_value = $version;
        $option->save();
    }
}


if (!function_exists('getAddonCodeCurrentVersion')) {
    function getAddonCodeCurrentVersion($appCode)
    {
        return config('Addon.' . $appCode . '.current_version', 0);
    }
}

if (!function_exists('getAddonCodeBuildVersion')) {
    function getAddonCodeBuildVersion($appCode)
    {
        return config('Addon.' . $appCode . '.build_version', 0);
    }
}

if (!function_exists('openAIService')) {
    function openAIService($input = NULL)
    {
        if (is_null($input)) {
            return OpenAIPrompt::where('status', STATUS_APPROVED)->select(['id', 'is_image', 'category'])->get();
        } else {
            return OpenAIPrompt::where('status', STATUS_APPROVED)->where('id', $input)->first();
        }
    }
}

if (!function_exists('isEnableOpenAI')) {
    function isEnableOpenAI()
    {
        $role = auth()->user()?->role;
        return isAddonInstalled('LMSZAIAI') && in_array($role, [USER_ROLE_INSTRUCTOR, USER_ROLE_ORGANIZATION, USER_ROLE_ADMIN]) && get_option('open_ai_system');
    }
}

if (!function_exists('getMeta')) {
    function getMeta($slug)
    {
        $metaData = [
            'meta_title' => null,
            'meta_description' => null,
            'meta_keyword' => null,
            'og_image' => null,
        ];

        $meta = Meta::where('slug', $slug)->select([
            'meta_title',
            'meta_description',
            'meta_keyword',
            'og_image',
        ])->first();

        if (!is_null($meta)) {
            $metaData = $meta->toArray();
        } else {
            $meta = Meta::where('slug', 'default')->select([
                'meta_title',
                'meta_description',
                'meta_keyword',
                'og_image',
            ])->first();

            if (!is_null($meta)) {
                $metaData = $meta->toArray();
            }
        }

        $metaData['meta_title'] = $metaData['meta_title'] != NULL ? $metaData['meta_title'] : get_option('app_name');
        $metaData['meta_description'] = $metaData['meta_description'] != NULL ? $metaData['meta_description'] : get_option('app_name');
        $metaData['meta_keyword'] = $metaData['meta_keyword'] != NULL ? $metaData['meta_keyword'] : get_option('app_name');
        $metaData['og_image'] = $metaData['og_image'] != NULL ? getImageFile($metaData['og_image']) : getImageFile(get_option('app_logo'));

        return $metaData;
    }
}


if (!function_exists('saveUpload')) {
  function saveUpload($destination, $fileDetail , $width = NULL, $height = NULL): array
{
  
    $result = array();
    $fileOriginalName = $fileDetail->getClientOriginalName();
    $fileExt = $fileDetail->getClientOriginalExtension();
   
  
    $filePath = $fileDetail->getRealPath();
    $fileSize = $fileDetail->getSize();
    $fileMime = $fileDetail->getMimeType();
    if (!File::isDirectory(base_path().'/public/uploads/'.$destination)){
        File::makeDirectory(base_path().'/public/uploads/'.$destination, 0777, true, true);
    }
    
  
    if ($fileExt == 'zip'){
        $fileTempName =  time().Str::random(10);
        $fileFullName =  $fileTempName.'.'.$fileExt;
        $fileFullPath =  public_path('uploads/' . $destination .'/').$fileTempName.'.'.$fileExt;
        $fileFullPath2 =  public_path('uploads/' . $destination .'/').$fileTempName;
     
                $ZIP = new ZipArchive();
                if($fileDetail->move(public_path('uploads/' . $destination .'/'),$fileFullName)){
                    $result = [
                        'fileName' => $fileTempName, 
                        'fileExt' => $fileExt, 
                    ];
                    if($ZIP->open($fileFullPath)){
                        $ZIP->extractTo($fileFullPath2);
                        $ZIP->close();
                        // var_dump($this->zip->open($location));
                        // exit;
                    }
                    unlink(public_path('uploads/' . $destination .'/').$fileFullName);
                }
            
            
              
    }
    else{
        $fileTempName =  time().Str::random(10).'.'.$fileExt;
        if($fileDetail->move(public_path('uploads/' . $destination .'/'),$fileTempName)){
            $result = [
                'fileName' => $fileTempName, 
                'fileExt' => $fileExt, 
            ];
        }
    }
    return $result;
}
}

if (!function_exists('getThemePath')) {
    function getThemePath()
    {
        $theme = get_option('theme', THEME_DEFAULT);
        if ($theme == THEME_DEFAULT) {
            return 'frontend';
        }

        return 'frontend-theme-' . $theme;
    }
}


if (!function_exists('getEmailTemplate')) {
    function getEmailTemplate($slug, $data)
    {
        $template = NotificationEmailTemplate::where(['slug' => $slug])->where('status', 1)->first();
        if($template){
            $emailData['content'] = replaceTemplateFields($template->body ?? '', $data);
            $emailData['subject'] = replaceTemplateFields($template->subject ?? '', $data);
            $emailData['status'] = !is_null($template);
        }
        else{
            $emailData['status'] = !is_null($template);
        }

        return $emailData;
    }

}

if (!function_exists('replaceTemplateFields')) {
    function replaceTemplateFields($template, $data)
    {
        $replacements = [
            '{{app_contact_number}}' => get_option('app_contact_number'),
            '{{app_email}}' => get_option('app_email'),
            '{{app_name}}' => get_option('app_name'),
        ];

        // Exclude 'user' key from $data
        unset($data['user']);

        $replacements = array_merge($replacements, $data);

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}

if (!function_exists('commonEmailSend')) {
    /**
     * Send notification email.
     *
     * Backward compatible: string $email = To only; array $email = [to, ...legacyBcc].
     * Prefer explicit $cc / $bcc arrays for new callers.
     *
     * @param string|array $email
     * @param array $emailData
     * @param int $force
     * @param array<int, array{path:string,name?:string,mime?:string}> $attachments
     * @param array<int, string> $cc
     * @param array<int, string> $bcc
     */
    function commonEmailSend($email, $emailData, $force = 0, $attachments = [], $cc = [], $bcc = [])
    {
        try {
            if (get_option('enable_email_notification', false) || $force) {
                $to = [];
                $legacyBcc = [];

                if (is_array($email)) {
                    $emailList = array_values(array_filter(array_map('trim', $email)));
                    if (empty($emailList)) {
                        Log::info('[HELPER] commonEmailSend skipped: empty recipient list.');
                        return false;
                    }
                    $to = [$emailList[0]];
                    $legacyBcc = array_slice($emailList, 1);
                } else {
                    $trimmed = trim((string) $email);
                    if ($trimmed === '') {
                        Log::info('[HELPER] commonEmailSend skipped: empty recipient.');
                        return false;
                    }
                    $to = [$trimmed];
                }

                $ccList = array_values(array_unique(array_filter(array_map('trim', (array) $cc))));
                $bccList = array_values(array_unique(array_filter(array_map('trim', array_merge((array) $bcc, $legacyBcc)))));

                // Avoid duplicating addresses across To/CC/BCC
                $toLookup = array_map('strtolower', $to);
                $ccList = array_values(array_filter($ccList, function ($addr) use ($toLookup) {
                    return !in_array(strtolower($addr), $toLookup, true);
                }));
                $ccLookup = array_map('strtolower', $ccList);
                $bccList = array_values(array_filter($bccList, function ($addr) use ($toLookup, $ccLookup) {
                    $lower = strtolower($addr);
                    return !in_array($lower, $toLookup, true) && !in_array($lower, $ccLookup, true);
                }));

                $mailable = new EmailNotify($emailData, $attachments);
                $pending = Mail::to($to);
                if (!empty($ccList)) {
                    $pending->cc($ccList);
                }
                if (!empty($bccList)) {
                    $pending->bcc($bccList);
                }
                $pending->send($mailable);

                Log::info(sprintf(
                    '[HELPER] commonEmailSend sent (to=%s, cc=%d, bcc=%d, attachments=%d)',
                    implode(',', $to),
                    count($ccList),
                    count($bccList),
                    count($attachments)
                ));
            }
            return true;
        } catch (\Exception $exception) {
            Log::info("[HELPER] commonEmailSend function. Exited due to following error \n. {$exception->getMessage()}");
            return false;
        }
    }
}



function startbbbclassindividual($l_id,$studentid,$instructorid,$meeting_id,$bb,$group_id = null,$learning_tool = null,$meeting = array())
{
    $todayDate = date('Y-m-d');
    $is_group = 'No';
   
    if(!empty($l_id)){
if($learning_tool == 'BBB'){
    if(!empty($meeting)){
        if (ClassDetail::where('meeting_id', $meeting_id)->exists()) {
            return false; // Reject if meeting ID already exists
        }

        $getStudentDetail = Student::where('id',$studentid)->first();
        $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
        $studentID = $getStudentDetail->id ;
        $studentType = "Student" ;

        $getInstructorDetail = Instructor::where('id',$instructorid)->first();
        $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
        $tutorID = $getInstructorDetail->id ;
        $tutorType = "Instructor" ;

            $academicName = "Quran Education Coordinator";
            $academicType = "Academic" ;  
            $academicID = null ; 
    
            $start_url = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Admin", $meeting->moderatorPW, 'MODERATOR');
            $start_url_tutor = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Instructor", $meeting->moderatorPW, 'MODERATOR');
            // $join_url = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Student - ". $getStudentDetail['s_name'], $meeting->attendeePW, 'VIEWER');
            $join_url = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Student - ". $studentName, $meeting->attendeePW, 'VIEWER');
            $start_url_academic = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Coordinator", $meeting->moderatorPW, 'MODERATOR');
            // print_r($start_url);
            // print_r('<br>');
            // print_r('<br>');
            // print_r($start_url_tutor);
            // print_r('<br>');
            // print_r('<br>');
            // print_r($join_url);
            // print_r('<br>');
            // print_r('<br>');
            // print_r($start_url_academic);
            // print_r('<br>');
            // print_r('<br>');
            // dd("YEEEES2");
            $ldata['InstructorURI'] = $start_url_tutor;
            $ldata['StudentURI'] = $join_url;

            

            $ldata['AcademicURI'] = $start_url_academic;
            $ldata['meeting_id'] = $meeting_id;	

        if($group_id ==  null){
            $getLesson = ClassDetail::where('id',$l_id)->first();
            $getLesson->meeting_id = $ldata['meeting_id'];
            $getLesson->InstructorURI = $ldata['InstructorURI'] ;
            $getLesson->StudentURI = $ldata['StudentURI'] ;
            $getLesson->AcademicURI = $ldata['AcademicURI'] ;
            $res=$getLesson->update();
        }

        if($res){
            $adata['l_id'] = $l_id ;
            $adata['group_id'] = $group_id ;
            $adata['AcademicURI'] = $ldata['AcademicURI'];
            $adata['TutorURI'] = $ldata['InstructorURI'];
            $adata['StudentURI'] = $ldata['StudentURI'];
            $adata['zoom_meeting_id'] = $meeting_id;
            $adata['student_name'] = $studentName;
            $adata['student_id'] = $studentID;
            $adata['tutor_name'] = $tutorName;
            $adata['tutor_id'] = $tutorID;
            $adata['academic_name'] = $academicName;
            $adata['academic_id'] = $academicID;
            $adata['student_type'] = $studentType;
            $adata['tutor_type'] = $tutorType;
            $adata['academic_type'] = $academicType;
            $adata['is_group'] = $is_group ;
            
            return $adata; 
        }
        else{
            return false;
        }
    
    }
    else{
      
        return false;
    }
}
}   
}



function startbbbclassgroup($l_id,$studentid,$instructorid,$meeting_id,$bb,$group_id = null,$learning_tool = null,$meeting = array())
{
    $todayDate = date('Y-m-d');
    $is_group = 'Yes';

    if(!empty($l_id)){
if($learning_tool == 'BBB'){
    if(!empty($meeting)){
        if (ClassDetail::where('meeting_id', $meeting_id)->exists()) {
            return false; // Reject if meeting ID already exists
        }

        $getStudentDetail = Student::where('id',$studentid)->first();
        $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
        $studentID = $getStudentDetail->id ;
        $studentType = "Student" ;

        $getInstructorDetail = Instructor::where('id',$instructorid)->first();
        $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
        $tutorID = $getInstructorDetail->id ;
        $tutorType = "Instructor" ;

            $academicName = "Quran Education Coordinator";
            $academicType = "Academic" ;  
            $academicID = null ; 
        
            $start_url = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Admin", $meeting->moderatorPW, 'MODERATOR');
            $start_url_tutor = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Instructor", $meeting->moderatorPW, 'MODERATOR');

            $join_url = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Student - ". $studentName, $meeting->attendeePW, 'VIEWER');

            $ldata['InstructorURI'] = $start_url_tutor;
            $ldata['StudentURI'] = $join_url;

            $start_url_academic = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Coordinator", $meeting->moderatorPW, 'MODERATOR');

            $ldata['AcademicURI'] = $start_url_academic;
            $ldata['meeting_id'] = $meeting_id;	

        if($group_id !=  null){
            // $getarray=ClassDetail::where('group_id',$group_id)->get();
            $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->get();
            if(!$getarray->isEmpty()){
                foreach ($getarray as $key => $value) {
                    $getStudentDetail = Student::where('id',$value->student_id)->first();
                    $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
                    $join_url2 = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Student - ". $studentName, $meeting->attendeePW, 'VIEWER');
                    $ldata['StudentURI'] = $join_url2;
                    $getLesson = ClassDetail::where('id',$value->id)->first();
                    $getLesson->meeting_id = $ldata['meeting_id'];
                    $getLesson->InstructorURI = $ldata['InstructorURI'] ;
                    $getLesson->StudentURI = $ldata['StudentURI'] ;
                    $getLesson->AcademicURI = $ldata['AcademicURI'] ;
                    $res=$getLesson->update();
                }
            }
                  
        }

        if($res){
            $adata['l_id'] = $l_id ;
            $adata['group_id'] = $group_id ;
            $adata['AcademicURI'] = $ldata['AcademicURI'];
            $adata['TutorURI'] = $ldata['InstructorURI'];
            $adata['StudentURI'] = $ldata['StudentURI'];
            $adata['zoom_meeting_id'] = $meeting_id;
            $adata['student_name'] = $studentName;
            $adata['student_id'] = $studentID;
            $adata['tutor_name'] = $tutorName;
            $adata['tutor_id'] = $tutorID;
            $adata['academic_name'] = $academicName;
            $adata['academic_id'] = $academicID;
            $adata['student_type'] = $studentType;
            $adata['tutor_type'] = $tutorType;
            $adata['academic_type'] = $academicType;
            $adata['is_group'] = $is_group ;
            
            return $adata; 
        }
        else{
            return false;
        }
    
    }
    else{
        return false;
    }
}
}   
}









function startbbbliveclass($l_id,$studentid=null,$instructorid=null,$meeting_id=null,$bb=null,$group_id = null,$learning_tool = null,$meeting = array())
{
    $todayDate = date('Y-m-d');
    $is_group = 'Yes';
    $res = 0;
    if(!empty($l_id)){
if($learning_tool == 'bbb'){
    if(!empty($meeting)){
        if (ClassLive::where('meeting_id', $meeting_id)->exists()) {
            return false; // Reject if meeting ID already exists
        }
        // $getStudentDetail = Student::where('id',$studentid)->first();
        // $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
        // $studentID = $getStudentDetail->id ;
        // $studentType = "Student" ;
        $studentName = null;
        $studentID = null;
        $studentType = "Student" ;

        $getInstructorDetail = Instructor::where('id',$instructorid)->first();
        $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
        $tutorID = $getInstructorDetail->id ;
        $tutorType = "Instructor" ;

            $academicName = "Quran Education Coordinator";
            $academicType = "Academic" ;  
            $academicID = null ; 
        
            $start_url = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Admin", $meeting->moderatorPW, 'MODERATOR');
            $start_url_tutor = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Instructor", $meeting->moderatorPW, 'MODERATOR');

            $ldata['InstructorURI'] = $start_url_tutor;
            $ldata['StudentURI'] = null;
          

            $start_url_academic = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Quran Education Coordinator", $meeting->moderatorPW, 'MODERATOR');

            $ldata['AcademicURI'] = $start_url_academic;
            $ldata['meeting_id'] = $meeting_id;	

            $getarray=LiveClassStudentLink::where('live_class_id',$l_id)->get();
            if(!$getarray->isEmpty()){
                $getLiveClass = LiveClass::where('id',$l_id)->first();
                $getLiveClass->meeting_id = $ldata['meeting_id'];
                $getLiveClass->InstructorURI = $ldata['InstructorURI'];
                $getLiveClass->AcademicURI = $ldata['AcademicURI'];
                $res=$getLiveClass->update();
                foreach ($getarray as $key => $value) {
                    $getStudentDetail = Student::where('id',$value->student_id)->first();
                    $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
                    $join_url2 = $bb->join_meeting($meeting->meetingID, rand(500,5000), "Student - ". $studentName, $meeting->attendeePW, 'VIEWER');
                    $lsdata['StudentURI'] = $join_url2;
                    $getLiveClassLink = LiveClassStudentLink::where('id',$value->id)->first();
                    $getLiveClassLink->StudentURI = $lsdata['StudentURI'] ;
                    $res=$getLiveClassLink->update();
                }
            }
                  
       

        if($res){
            $adata['l_id'] = $l_id ;
            $adata['group_id'] = $group_id ;
            $adata['AcademicURI'] = $ldata['AcademicURI'];
            $adata['TutorURI'] = $ldata['InstructorURI'];
            $adata['StudentURI'] = $ldata['StudentURI'];
            $adata['bbb_meeting_id'] = $meeting_id;
            $adata['student_name'] = $studentName;
            $adata['student_id'] = $studentID;
            $adata['tutor_name'] = $tutorName;
            $adata['tutor_id'] = $tutorID;
            $adata['academic_name'] = $academicName;
            $adata['academic_id'] = $academicID;
            $adata['student_type'] = $studentType;
            $adata['tutor_type'] = $tutorType;
            $adata['academic_type'] = $academicType;
            $adata['is_group'] = $is_group ;
            
            return $adata; 
        }
        else{
            return false;
        }
    
    }
    else{
        return false;
    }
}
}   
}







function startzoomliveclass($l_id,$studentid=null,$instructorid=null,$meeting_id=null,$group_id = null,$learning_tool = null)
{
    $todayDate = date('Y-m-d');
    $res = 0;
    $is_group = 'No';
    if(!empty($l_id)){
    if($learning_tool == 'zoom'){
    if($meeting_id != null){
            // Validate the meeting ID before proceeding
    if (LiveClass::where('meeting_id', $meeting_id)->exists()) {
        return false; // Reject if meeting ID already exists
    }

        $getStudentDetail = Student::where('id',$studentid)->first();
        // $studentName = $getStudentDetail->first_name.' '.$getStudentDetail->last_name;
        // $studentID = $getStudentDetail->id ;
        // $studentType = "Student" ;
        $studentName = null;
        $studentID = null;
        $studentType = "Student" ;

        $getInstructorDetail = Instructor::where('id',$instructorid)->first();
        $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
        $tutorID = $getInstructorDetail->id ;
        $tutorType = "Instructor" ;

            $academicName = "Quran Education Coordinator";
            $academicType = "Academic" ;  
            $academicID = null ; 
        
        $ldata['meeting_id'] = $meeting_id;
           
        $check_old_record = LiveClass::where('id',$l_id)->first();
        $old_end_time = $check_old_record->end_time_zoom;
        $lessonDuration = $check_old_record->duration;
        if($old_end_time != null){
        }
        else{
            $updated_current_time = getCurrentServerTimeByZone($instructorid,'instructor');
            $given_start_time = strtotime($updated_current_time);
            $duration = $lessonDuration * 60;
            $given_end_time = $given_start_time + $duration; // Calculate the end time
            $ldata['end_time_zoom'] = $given_end_time;
            $check_old_record->end_time_zoom = $given_end_time;
        }
        $check_old_record->meeting_id = $meeting_id;
          $check_old_record->status = 'In Progress';
        $res= $check_old_record->update();

            $is_group = 'No';
            // $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->get();
        //     $getarray=LiveClassStudentLink::where('live_class_id',$l_id)->get();
        // if(!$getarray->isEmpty()){
        //     foreach ($getarray as $key => $value) {         
        //     }
        // }
            
    
        if($res){
            $adata['l_id'] = $l_id ;
            $adata['group_id'] = $group_id ;
            $adata['AcademicURI'] = null;
            $adata['TutorURI'] = null;
            $adata['StudentURI'] = null;
            $adata['zoom_meeting_id'] = $meeting_id;
            $adata['student_name'] = $studentName;
            $adata['student_id'] = $studentID;
            $adata['tutor_name'] = $tutorName;
            $adata['tutor_id'] = $tutorID;
            $adata['academic_name'] = $academicName;
            $adata['academic_id'] = $academicID;
            $adata['student_type'] = $studentType;
            $adata['tutor_type'] = $tutorType;
            $adata['academic_type'] = $academicType;
            $adata['is_group'] = $is_group ;
            
            return $adata; 
        }
        else{
            return false;
        }
    
    }
    }
    }   
}






function startmeetliveclass($l_id,$studentid=null,$instructorid=null,$meeting_id=null,$group_id = null,$learning_tool = null)
{
    $todayDate = date('Y-m-d');
    $res = 0;
    $is_group = 'No';
    if(!empty($l_id)){
    if($learning_tool == 'google_meet'){
    if($meeting_id != null){
            // Validate the meeting ID before proceeding
    if (LiveClass::where('meeting_id', $meeting_id)->exists()) {
        return false; // Reject if meeting ID already exists
    }

        $getStudentDetail = Student::where('id',$studentid)->first();
      
        $studentName = null;
        $studentID = null;
        $studentType = "Student" ;

        $getInstructorDetail = Instructor::where('id',$instructorid)->first();
        $tutorName = $getInstructorDetail->first_name.' '.$getInstructorDetail->last_name;
        $tutorID = $getInstructorDetail->id ;
        $tutorType = "Instructor" ;

            $academicName = "Quran Education Coordinator";
            $academicType = "Academic" ;  
            $academicID = null ; 
        
        $ldata['meeting_id'] = $meeting_id;
           
        $check_old_record = LiveClass::where('id',$l_id)->first();
        $googleMeetLink  = $check_old_record->google_meet_link;
        $old_end_time = $check_old_record->end_time_zoom;
        $lessonDuration = $check_old_record->duration;
        if($old_end_time != null){
        }
        else{
            $updated_current_time = getCurrentServerTimeByZone($instructorid,'instructor');
            $given_start_time = strtotime($updated_current_time);
            $duration = $lessonDuration * 60;
            $given_end_time = $given_start_time + $duration; // Calculate the end time
            $ldata['end_time_zoom'] = $given_end_time;
            $check_old_record->end_time_zoom = $given_end_time;
        }
        $check_old_record->meeting_id = $meeting_id;
          $check_old_record->status = 'In Progress';
        $res= $check_old_record->update();

            $is_group = 'No';
     
    
        if($res){
            $adata['l_id'] = $l_id ;
            $adata['group_id'] = $group_id ;
            $adata['AcademicURI'] = null;
            $adata['TutorURI'] = null;
            $adata['StudentURI'] = null;
            $adata['google_meet_link'] = $googleMeetLink;
            $adata['meet_meeting_id'] = $meeting_id;
            $adata['student_name'] = $studentName;
            $adata['student_id'] = $studentID;
            $adata['tutor_name'] = $tutorName;
            $adata['tutor_id'] = $tutorID;
            $adata['academic_name'] = $academicName;
            $adata['academic_id'] = $academicID;
            $adata['student_type'] = $studentType;
            $adata['tutor_type'] = $tutorType;
            $adata['academic_type'] = $academicType;
            $adata['is_group'] = $is_group ;
            
            return $adata; 
        }
        else{
            return false;
        }
    
    }
    }
    }   
}



function updateLessonStatusComplete($lesson_id,$bbb_meeting_id = null,$group_id = null)
{
    $today_date= date("Y-m-d");
    if($group_id != null){
        $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$today_date)->get();
        foreach ($getarray as $key => $value) {
            $l_id = $value->id;
            $find = ClassDetail::where('id',$l_id)->where('meeting_id',$bbb_meeting_id)->first();
            // $find->status = 'Completed';
            // $res = $find->update();
        }
    }
    else{
        if($bbb_meeting_id != null){
            $find = ClassDetail::where('id',$lesson_id)->where('meeting_id',$bbb_meeting_id)->first();
            // $find->status = 'Completed';
            // $res = $find->update();
        }
        else{
            $find = ClassDetail::where('id',$lesson_id)->first();
            // $find->status = 'Completed';
            // $res = $find->update();
        }
    }

    return $res ?? 'N/A';            
} 




function updateLessonStatusMissed($lesson_id,$bbb_meeting_id = null,$group_id = null)
{
    $today_date= date("Y-m-d");
    if($group_id != null){
        $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$today_date)->get();
        foreach ($getarray as $key => $value) {
            $l_id = $value->id;
            $find = ClassDetail::where('id',$l_id)->where('meeting_id',$bbb_meeting_id)->first();
            // $find->status = 'Missed';
            // $res = $find->update();
        }
    }
    else{
        if($bbb_meeting_id != null){
            $find = ClassDetail::where('id',$lesson_id)->where('meeting_id',$bbb_meeting_id)->first();
            // $find->status = 'Missed';
            // $res = $find->update();
        }
        else{
            $find = ClassDetail::where('id',$lesson_id)->first();
            // $find->status = 'Missed';
            // $res = $find->update();
        }
    }

    return $res ?? 'N/A';        
}   



    function updateTutorJoinStatus($lesson_id,$class_id,$group_id = null)
    {
    
        $todayDate = date('Y-m-d');
        if($group_id != null){
            // $getarray=ClassDetail::where('group_id',$group_id)->get();
            $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->get();
            if(!$getarray->isEmpty()){
                foreach ($getarray as $key => $value) {
                    $getLessonGroup = ClassDetail::where('id',$value->id)->where('meeting_id',$class_id)->first();
                    $getLessonGroup->is_instructor_joined = 1;
                    $getLessonGroup->status = 'In Progress';
                    $res =  $getLessonGroup->update();
                }
            }   
        }
        else{
            $getLesson = ClassDetail::where('id',$lesson_id)->where('meeting_id',$class_id)->first();
            if($getLesson){
                $getLesson->is_instructor_joined = 1;
                $getLesson->status = 'In Progress';
               $res = $getLesson->update();
            }
         
        }

        return $res;        
    }   

    function updateTutorJoinStatusLiveClass($lesson_id,$class_id,$group_id = null)
    {

            $getLesson = LiveClass::where('id',$lesson_id)->where('meeting_id',$class_id)->first();
            if($getLesson){
                // $getLesson->is_instructor_joined = '1';
                $getLesson->is_instructor_joined = 1;
                 $getLesson->status = 'In Progress';
               $res = $getLesson->update();
            }

        // return $res ?? 'N/A'; 
          return $res;        
    }  


    

    
    function updateCoordinatorJoinStatusLiveClass($lesson_id,$class_id,$group_id = null)
    {

            $getLesson = LiveClass::where('id',$lesson_id)->where('meeting_id',$class_id)->first();
            if($getLesson){
           
                $getLesson->is_coordinator_joined = 1;
                 $getLesson->status = 'In Progress';
               $res = $getLesson->update();
            }

        // return $res ?? 'N/A'; 
          return $res;        
    }  




    function updateCoordinatorJoinStatus($lesson_id,$class_id,$group_id = null)
    {
    
        $todayDate = date('Y-m-d');
        if($group_id != null){
            // $getarray=ClassDetail::where('group_id',$group_id)->get();
            $getarray=ClassDetail::where('group_id',$group_id)->where('class_date',$todayDate)->get();
            if(!$getarray->isEmpty()){
                foreach ($getarray as $key => $value) {
                    $getLessonGroup = ClassDetail::where('id',$value->id)->where('meeting_id',$class_id)->first();
                    $getLessonGroup->is_coordinator_joined = 1;
                    $getLessonGroup->status = 'In Progress';
                    $res =  $getLessonGroup->update();
                }
            }   
        }
        else{
            $getLesson = ClassDetail::where('id',$lesson_id)->where('meeting_id',$class_id)->first();
            if($getLesson){
                $getLesson->is_coordinator_joined = 1;
                $getLesson->status = 'In Progress';
               $res = $getLesson->update();
            }
         
        }

        return $res ?? 'N/A';        
    }   




    function meeting_id_classes($meeting_id = null)
    {
        $result = null;
        $class_ids = array();
        $class_ids_string = null;
        if($meeting_id != null){
            $getarray=ClassDetail::where('meeting_id',$meeting_id)
            ->groupBy('class_id')->get();
            if(!$getarray->isEmpty()){
                foreach ($getarray as $key => $value) {
                    $class_ids[] = $value->class_id;
                }
                $class_ids = array_unique($class_ids);
                $class_ids_string = implode(',',$class_ids);
            }   
        }
     

        if($class_ids_string != null){
            $result = '( '.$class_ids_string.' )';
        }
        else{
            $result = null;  
        }
        return $result;        
    } 






    function convertDaysToWeeks($duration)
{
    try {
        // Validate input
        if (!is_numeric($duration) || $duration < 0) {
            throw new \Exception('Invalid duration provided. Duration must be a non-negative number.');
        }

        // Calculate weeks (integer only)
        $weeks = (int) floor($duration / 7);

        return $weeks;
        // return response()->json([
        //     'total_days' => (int) $duration,
        //     'weeks' => $weeks,
        // ]);
    } catch (\Exception $e) {
        return $e->getMessage();
        // Handle errors gracefully
        // return response()->json([
        //     'error' => $e->getMessage(),
        // ], 400);
    }



   
}




function check_and_activate_zoom_account()
{
    $activated = 0;

    Log::info('Zoom Account Shift Check Started in (Helper Class)', [
        'timestamp' => now()->format('Y-m-d H:i:s'),
    ]);

    $currentZoomAccount = ZoomSetting::where('status',1)->latest()->first();
    if($currentZoomAccount){
        $current_active_zoom_id = $currentZoomAccount->id;
        $current_active_zoom_minutes =  $currentZoomAccount->meeting_minutes_used;
        if((int)$current_active_zoom_minutes >= 9800  || (int)$current_active_zoom_minutes > 9800){       // Means current accounts minutes used time to shift
            $activated = 2;
            Log::info('Zoom Current Account Has Minutes Used Fully. Now Account Is Being Shited in (Helper Class)', [
                'current_account_api_key' => $currentZoomAccount->api_key,
                'current_account_sdk_key' => $currentZoomAccount->sdk_key,
                'current_account_minutes_used' => $currentZoomAccount->meeting_minutes_used,
                'data_type' => gettype($current_active_zoom_minutes),
                'timestamp' => now()->format('Y-m-d H:i:s'),
              
            ]);

            $getAvailableZoomAccounts = ZoomSetting::where('id','<>',$current_active_zoom_id)
            ->where('meeting_minutes_used','<',9800)->get();
            if(!empty($getAvailableZoomAccounts)){
                foreach ($getAvailableZoomAccounts as $key => $newActiveZoomAccount) {
                    $zoomVideoSDKJWT = $newActiveZoomAccount->api_jwt_token;
                    // $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
                    $zoomResult = zoomCurl($zoomVideoSDKJWT);
                    if(empty($zoomResult)){
                        // $zoomErrorMessage =__('Invalid Or Expired Zoom Access Token');
                        Log::info('Not Able To Shift Zoom Account. Invalid or Expired Zoom Access Token in (Helper Class)', [
                            'new_account_api_key' => $newActiveZoomAccount->api_key,
                            'new_account_sdk_key' => $newActiveZoomAccount->sdk_key,
                            'new_account_minutes_used' => $newActiveZoomAccount->meeting_minutes_used,
                            'data_type' => gettype($newActiveZoomAccount->meeting_minutes_used),
                            'timestamp' => now()->format('Y-m-d H:i:s'),     
                        ]);
                    }
                    else{                      
                        if(isset($zoomResult['code']) && isset($zoomResult['message'])){
                            // $zoomErrorMessage =__($zoomResult['message']);
                        }
                        else{
                            $currentZoomAccount->status = 0;
                            $currentZoomAccount->update();

                            $zoomResultArray = $zoomResult;
                            $newActiveZoomAccount->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
                            $newActiveZoomAccount->meeting_minutes_used = $zoomResultArray['Total_Minutes'];
                            $newActiveZoomAccount->status = 1;
                            $newActiveZoomAccount->update();

                            $activated = 1;

                            Log::info('Zoom Account Shifted Successfully in (Helper Class)', [
                                'new_account_api_key' => $newActiveZoomAccount->api_key,
                                'new_account_sdk_key' => $newActiveZoomAccount->sdk_key,
                                'new_account_minutes_used' => $newActiveZoomAccount->meeting_minutes_used,
                                'data_type' => gettype($newActiveZoomAccount->meeting_minutes_used),
                                'timestamp' => now()->format('Y-m-d H:i:s'),        
                            ]);

                            break;
                            // dd(($zoomResultArray));
                        }       
                    }
                }       
            }  
            else{
                Log::info('No Other Zoom Accounts Available For Shifting As All Have Miniutes Fully Used. Please Add More in (Helper Class)', [
                    'current_account_api_key' => $currentZoomAccount->api_key,
                    'current_account_sdk_key' => $currentZoomAccount->sdk_key,
                    'current_account_minutes_used' => $currentZoomAccount->meeting_minutes_used,
                    'data_type' => gettype($currentZoomAccount->meeting_minutes_used),
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                ]);
            }      
        }
        else{
            Log::info('No Shifting Yet As Current Active Zoom Account Has Still Less Than 9800 Minutes in (Helper Class)', [
                'current_account_api_key' => $currentZoomAccount->api_key,
                'current_account_sdk_key' => $currentZoomAccount->sdk_key,
                'current_account_minutes_used' => $currentZoomAccount->meeting_minutes_used,
                'data_type' => gettype($currentZoomAccount->meeting_minutes_used),
                'timestamp' => now()->format('Y-m-d H:i:s'),
              
            ]);
        }
    }
    else{
        Log::info('No Active Zoom Account Currently Set in (Helper Class)', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            
        ]);
    }
  
    return $activated;        
} 














function check_and_update_zoom_account_minutes()
{
    $activated = 0;
    $activated = 2;
    $errorCount = 0;

    Log::info('Checking And Updating Zoom Account Minutes Function Executed in (Helper Class)', [
        'timestamp' => now()->format('Y-m-d H:i:s'),  
    ]);

            $getAvailableZoomAccounts = ZoomSetting::orderBy('id','DESC')->get();
         
            if(!$getAvailableZoomAccounts->isEmpty()){
                foreach ($getAvailableZoomAccounts as $key => $newActiveZoomAccount) {
                    $zoomVideoSDKJWT = $newActiveZoomAccount->api_jwt_token;
                    // $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
                    $zoomResult = zoomCurl($zoomVideoSDKJWT);
                    if(empty($zoomResult)){
                        $errorCount++;
                        // $zoomErrorMessage =__('Invalid Or Expired Zoom Access Token');
                        Log::info('Invalid Or Expired Access Token Of Following Zoom Account in ( Helper Class )', [
                            'zoom_table_id' => $newActiveZoomAccount->id,
                            'api_key' => $newActiveZoomAccount->api_key,
                            'sdk_key' => $newActiveZoomAccount->sdk_key,
                            'timestamp' => now()->format('Y-m-d H:i:s'),
                        
                        ]);
                    }
                    else{                      
                        if(isset($zoomResult['code']) && isset($zoomResult['message'])){
                            $errorCount++;
                            // $zoomErrorMessage =__($zoomResult['message']);
                            Log::info('Something Went wrong after getting minutes Of Following Zoom Account in ( Helper Class )', [
                                'zoom_table_id' => $newActiveZoomAccount->id,
                                'api_key' => $newActiveZoomAccount->api_key,
                                'sdk_key' => $newActiveZoomAccount->sdk_key,
                                'timestamp' => now()->format('Y-m-d H:i:s'),
                            
                            ]);
                        }
                        else{
                            $zoomResultArray = $zoomResult;
                            $newActiveZoomAccount->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
                            $newActiveZoomAccount->meeting_minutes_used = $zoomResultArray['Total_Minutes'];

                            // dd($newActiveZoomAccount);
                            $client = new Client();
                            $accountApiKey = $newActiveZoomAccount->api_key;
                            $accountSecretKey = $newActiveZoomAccount->api_secret;
                            $restApiJwt = generateZoomRestApiJWT($accountApiKey, $accountSecretKey);
                            try {
                                $response = $client->request('GET', 'https://api.zoom.us/v2/accounts/me/plans', [
                                    'headers' => [
                                        'Authorization' => 'Bearer ' . $restApiJwt,
                                        'Content-Type' => 'application/json'
                                    ]
                                ]);
                                
                                $planInfo = json_decode($response->getBody());
                                // Get current date
                                $currentDate2 = Carbon::now();
                                // $LastRenewalDate = Carbon::parse($planInfo->plan_base->service_effective_date)->format('d M Y h:i A');
                                // $nextRenewalDate = Carbon::parse($planInfo->plan_base->next_invoice_date)->format('d M Y h:i A');
                                $LastRenewalDate = Carbon::parse($planInfo->plan_base->service_effective_date)->format('Y-m-d H:i:s');
                                $nextRenewalDate = Carbon::parse($planInfo->plan_base->next_invoice_date)->format('Y-m-d H:i:s');
                                // Get difference in days

                                $serviceDate = Carbon::parse($planInfo->plan_base->service_effective_date ?? null);
                                $diffInDays = $serviceDate->diffInDays($currentDate2);
                               
                                $newActiveZoomAccount->last_renewed_on = $LastRenewalDate;
                                $newActiveZoomAccount->days_passed = $diffInDays;
                                $newActiveZoomAccount->next_renewel_on = $nextRenewalDate;
                             
                            } 
                            catch (\Exception $e) {
                                $errorCount++;
                                Log::info('Error Occured When Checking Renewal Info Of Following Zoom Account in (Helper Class)', [
                                    'zoom_table_id' => $newActiveZoomAccount->id,
                                    'api_key' => $newActiveZoomAccount->api_key,
                                    'sdk_key' => $newActiveZoomAccount->sdk_key,
                                    'timestamp' => now()->format('Y-m-d H:i:s'),
                                
                                ]);
                            }
                          
                            $newActiveZoomAccount->update();
                            $activated = 1;
                          
                        }       
                    }
                }       
            } 
            else{
                $errorCount++;
                Log::info('No Zoom Accounts Available In Records in DB Error in (Helper Class)', [
                    'timestamp' => now()->format('Y-m-d H:i:s'),  
                ]);
            }       
  
            if($errorCount == 0){
                Log::info('All Zoom Account Minutes and Renewal Info Updated Successfully in (Helper Class)', [
                    'timestamp' => now()->format('Y-m-d H:i:s'),  
                ]);
            }
    return $activated;        
} 

function generateZoomRestApiJWT($apiKey, $apiSecret)
    {
        $token = array(
            "iss" => $apiKey,
            "exp" => time() + 3600
        );
        
        return \Firebase\JWT\JWT::encode($token, $apiSecret, 'HS256');
    }

function zoomCurl($zoomVideoSDKJWT)
{

    $result_array = array();
    $currentMonth = date('m');
    $currentMonth = (int)$currentMonth;
    $currentYear = date('Y');
    $currentYear = (int)$currentYear;
 
    $curl = curl_init();
    curl_setopt_array($curl, [
        // CURLOPT_URL => "https://api.zoom.us/v2/videosdk/report/daily",
        CURLOPT_URL => "https://api.zoom.us/v2/videosdk/report/daily?year=$currentYear&month=$currentMonth",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $zoomVideoSDKJWT,
      
        ],
        // CURLOPT_HTTPHEADER=>[
        //     "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IllvM18tZzFDVG4ycDMxbk4wTndsNVEiLCJleHAiOjIwNTEyMDQxMDAsImlhdCI6MTczMDExOTIwNH0.seU7u-7VUEKrMjP4d0nzVtooLdlMJq2TiW7afLNgal8",
        // ],
      ]);

      $response = curl_exec($curl);
      $err = curl_error($curl);
      
      curl_close($curl);
      $result = json_decode($response);
 
      if(isset($result->code) && isset($result->message)){
         
        $result_array = [
            'code' => $result->code,
            'message' => $result->message,
        ];
        return $result_array;
      }
      else{
        if(isset($result->dates) && !empty($result->dates)){
            $TotalSessions = 0;
            $TotalMinutes = 0;
            foreach ($result->dates as $key => $value) {
                $TotalSessions += $value->sessions;
                $TotalMinutes += $value->session_minutes;
            }
            $result_array = [
                'Total_Sessions'=> $TotalSessions,
                'Total_Minutes'=> $TotalMinutes,
            ]; 
            return $result_array;  
        }
        else{
            $result_array = [
                'Total_Sessions'=> 0,
                'Total_Minutes'=> 0,
            ]; 
            return $result_array;   
        }
      }
      dd($result->code);

}

function sendEmailNotification($viewData, $userEmail, $template, $attachments = [], $cc = [], $bcc = [])
{
    $emailData = getEmailTemplate($template, $viewData);

    if ($emailData['status']) {
        return commonEmailSend($userEmail, $emailData, 1, $attachments, $cc, $bcc);
    }

    Log::info("[HELPER] sendEmailNotification function. Exited because given template #{$template} does not exists in db .");
    return false;
}


function getFeaturedCourseEnrollments($course_id){
    $enrolled_students = Enrollment::where('course_id', $course_id)->take(4)->get();
    return $enrolled_students;
}


function getFeaturedCourseAverageRating($course_id){
    $result_array = array();

    $five_star_count = Review::whereCourseId($course_id)->whereRating(5)->count();
    $four_star_count = Review::whereCourseId($course_id)->whereRating(4)->count();
    $three_star_count = Review::whereCourseId($course_id)->whereRating(3)->count();
    $two_star_count = Review::whereCourseId($course_id)->whereRating(2)->count();
    $first_star_count = Review::whereCourseId($course_id)->whereRating(1)->count();
    $total_reviews = (5 * $five_star_count) + (4 * $four_star_count) + (3 * $three_star_count) +
    (2 * $two_star_count) + (1 * $first_star_count);

    $total_user_review = $five_star_count + $four_star_count + $three_star_count + $two_star_count + $first_star_count;
    if ($total_user_review > 0) {
        $average_rating = $total_reviews  / $total_user_review;
    } else {
        $average_rating = 0;
    }
    $result_array = [
        'average_rating' => $average_rating,
        'total_user_review' => $total_user_review
    ];

    return $result_array;
}


function getStudentLatestReview($user_id){
    $result = null;
    $review = Review::where('user_id', $user_id)->latest()->first();
    if($review){
        $result = $review->comment;
    }
    else{
        // $result = 'None';
    }
    return $result;
}


function getVideoUrl($video){
    $lecture_preview_src = null;
    if (Str::contains($video, 'https://www.youtube.com')) {
        $lecture_preview_src = $video;    
    }
    else if(Str::contains($video, 'https://youtu.be')){
        $lecture_preview_src = $video;    
    }
    else{
        $lecture_preview_src = "https://www.youtube.com/embed/".$video;
    }
    return $lecture_preview_src;
}


function checkRefundPolicy(){
    $check = Policy::whereType(4)->first();
    if($check){
        return 1;
    }
    else{
        return 0;
    }
}



function checkPrivacyPolicy(){
    $check = Policy::whereType(1)->first();
    if($check){
        return 1;
    }
    else{
        return 0;
    }
}





function checkTermsPolicy(){
    $check = Policy::whereType(3)->first();
    if($check){
        return 1;
    }
    else{
        return 0;
    }
}

function getStudentReviewVideo($user_id){
    $result = array();
    $review = Review::where('user_id', $user_id)->where('video_check','<>',null)->latest()->first();
    if($review){
        $result = [
            'video_check' => $review->video_check,
            'video' => $review->video,
        ];
    }
   
    return $result;
}






function stripHeadTags($html) {
    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $tagsToRemove = ['meta', 'title', 'head'];
    foreach ($tagsToRemove as $tag) {
        $elements = $dom->getElementsByTagName($tag);
        while ($elements->length > 0) {
            $elements->item(0)->parentNode->removeChild($elements->item(0));
        }
    }

    $body = $dom->getElementsByTagName('body')->item(0);
    $cleanedHtml = '';
    foreach ($body->childNodes as $node) {
        $cleanedHtml .= $dom->saveHTML($node);
    }

    return $cleanedHtml;
}





function check_request_enrollment($student,$course_id){
   
    $check = TrialClass::where('student_id',$student->id)->where('course_id',$course_id)->first();
    if($check){
        return true;
    }
    else{
        return false;
    }
}










