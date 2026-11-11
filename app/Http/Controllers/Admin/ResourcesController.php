<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Resource;
use ZipArchive;
use App\Models\ResourceCategory;
use App\Jobs\DownloadYoutubeForVaultJob;
use App\Models\CourseResource;
use App\Services\CourseVaultManifestService;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use App\Traits\SendNotification;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResourcesController extends Controller
{
    use General, ImageSaveTrait, SendNotification;
    public function __construct(Course $course, Resource $resource)
    {
        $this->model = new Crud($course);
        $this->resourceModel = new Crud($resource);
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('manage_resources')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'All Resources';
        // $data['resources'] = $this->resourceModel->getOrderById('DESC', 25);
        $query = Resource::query(); // Assuming you have these relationships
        $data['resources'] = $query->orderBy('sort_number','ASC')->paginate(25);
        if ($request->has('search')) {
            $searchTerm = $request->search;
         
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'LIKE', "%{$searchTerm}%")
                ->orWhere('name', 'LIKE', "%{$searchTerm}%")
                ->orWhereHas('category', function($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhere('type', 'LIKE', "%{$searchTerm}%")
                ->orwhere('file_extension', 'LIKE', "%{$searchTerm}%");  
            });
        }
        $data['resources'] = $query->orderBy('sort_number','ASC')->paginate(25);
        if ($request->ajax()) {
            return response()->json([
                'resources' => view('admin.resource.table-data', $data)->render(),
                'pagination' => $data['resources']->links()->toHtml()
            ]);
        }
   
        // $data['resources'] = Resource::orderBy('sort_number','ASC')->paginate(25);
     
        return view('admin.resource.index', $data);
    }



    // public function getData(Request $request)
    // {
    //     $start = (int) ($request->start ?? 0);  // Default to 0 if not provided
    //     $length = (int) ($request->length ?? 2);  // Default to 10 rows per page if not provided
    //     // $length = 2;
    //     $query = Resource::query();
    //     // Search functionality
     
    //     if (!empty($request->search)) {
        
    //         $searchValue = $request->search;

       
    //             $query->where('id', 'LIKE', '%' . $searchValue . '%')
    //             ->orWhere('name', 'LIKE', '%' . $searchValue . '%')
    //             ->orWhereHas('category', function($subQuery) use ($searchValue) {
    //                 $subQuery->where('name', 'LIKE', '%' . $searchValue . '%');
    //             })
    //             ->orWhere('type', 'LIKE', '%' . $searchValue . '%')
    //             ->orwhere('file_extension', '%' . $searchValue . '%');  
    //     }

    //     $data = $query->orderBy('sort_number','ASC')->skip($start)->take($length)->get();

    
    //     $data = $data->map(function ($resource) {
        
    //         return [
    //             'image_path' => getImageFile($student->user ? $student->user->image_path : ''),
    //             'id' => $student->id,
    //             'name' => $student->name,
    //             'email' => $student->user ? $student->user->email : null,
    //             'phone_number' => $student->phone_number ?? ($student->user ? $student->user->phone_number : null),
    //             'details_link' => route('student.view', ['uuid' => $student->uuid]),
    //             'uuid' => $student->uuid,
    //             'country' => $student->country ? $student->country->country_name : '',
    //         ];
    //     });
     
    //     return response()->json([
    //         'draw' => $request->draw,
    //         'recordsTotal' => Resource::count(),
    //         'recordsFiltered' => $data->count(),
    //         'data' => $data,
    //     ]);

     
    // }

    

    public function add()
    {
        $data['categories'] = ResourceCategory::all();
        $data['courses'] = Course::where('status', 1)->get();
        $data['title'] = 'Add Resource';
        return view('admin.resource.add', $data);
    }

    
    public function getCategoryTypes(Request $request)
    {
        $result_array = array();
        $types = '';
        $categoryID = $request->category_id;
        $find = ResourceCategory::where('id',$categoryID)->first();
        if($find){
            $result_array = [
                'category_types' => $find->category_types,
                'category_name' => $find->name,
            ];
           
        }
        echo json_encode($result_array);
    }


    public function store(Request $request)
{
    $request->validate([
        'resource_title' => ['required', 'string', 'max:100'],
        'resource_category_id' => ['required', 'integer'],
    ]);
//   dd($request->all());
    $categoryTypeID = $request->resource_category_id;
    $getCategory = ResourceCategory::where('id',$categoryTypeID)->first();
    $getCategoryName = $getCategory->name;
  
    if($getCategory){
        $types = $getCategory->category_types;
    }
    $types = explode(',',$types);
    $courses = $request->course;
    if(empty($courses)){
        $this->showToastrMessage('error', __('Please Check Atleast One Course!'));
        return redirect()->back();
    }
  
    if(isset($types)){
      
        if($getCategoryName == "Video"){
            if(in_array('mp4',$types)||in_array('webm',$types)||in_array('mkv',$types)){
                $request->validate([
                    'video_file' => 'mimes:mp4,webm,mkv'
                ]);
            }
        }
        else if($getCategoryName == "Audio"){
            if(in_array('mp3',$types)||in_array('wav',$types)||in_array('m4a',$types)){
                $request->validate([
                    'audio' => 'mimes:mp3,wav,m4a'
                ]);
            }
        }
        else if($getCategoryName == "Image"){
            if(in_array('jpg',$types)||in_array('png',$types)||in_array('gif',$types)||in_array('webp',$types)){
                $request->validate([
                    'image' => 'mimes:jpg,png,gig,webp'
                ]);
            }
        }
        else if($getCategoryName == "PDF"){
            if(in_array('pdf',$types)){
                $request->validate([
                    'pdf' => 'mimes:pdf'
                ]);
            }
        }
        else if($getCategoryName == "Slide Document"){
            if(in_array('zip',$types)){
                $request->validate([
                    'slide_document' => 'mimes:zip'
                ]);
            }
        }
        else if($getCategoryName == "Text"){
            if(in_array('text',$types)){
                $request->validate([
                    'text_description' => ['required', 'string'],
                ]);
            }
        }
        else if($getCategoryName == "Youtube"){
            if(in_array('youtube',$types)){
                $request->validate([
                    'youtube_url_path' => ['required'],
                    'youtube_file_duration' => ['required'],
                ]);
                if ($request->youtube_file_duration){
                    if(preg_match('/^([0-9][0-9]):[0-5][0-9]$/',$request->youtube_file_duration)) {
    
                    } else {
                        $request->validate([
                            'youtube_file_duration' => 'date_format:H:i'
                        ]);
                    }
                }
            }
        }

        // if(in_array('pdf',$types)||in_array('docx',$types)||in_array('zip',$types) || in_array('pptx',$types) ||in_array('xlsx',$types)){
        //     $request->validate([
        //         'resource_file' => 'mimes:pdf,docx,zip,pptx,xlsx'
        //     ]);
        // }
    }
  
  
    $resource = new Resource();
    $resource->name = $request->resource_title;
    $resource->type = $getCategoryName;
    $resource->category_id = $categoryTypeID;
    $resource->created_by = auth()->id();

    // dd($resource);
    if($getCategoryName == "Video"){
        $file= $request->video_file;
        if($request->hasFile('video_file')){
            $filedetail= $request->file('video_file');
            $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
            $resource->file_name = $dataResult['fileName'];
            $resource->file_extension = $dataResult['fileExt'];
        
            // $this->deleteFile( $file); // delete file from server
            // $ZIP = new ZipArchive();
            // $data['cover_image'] = $this->saveUpload('resources', $request->resource_file, null, null); // new file upload into server
    
        }
    }
    else if($getCategoryName == "Youtube"){
        $youtube_url_path = $request->youtube_url_path;
        $youtube_file_duration = $request->youtube_file_duration;

        $resource->youtube_url_path = $youtube_url_path;
        $resource->youtube_video_duration = $youtube_file_duration;
        $resource->youtube_video_duration_second = $this->timeToSeconds($youtube_file_duration);
      
    }
    else if($getCategoryName == "Image"){
        $file= $request->image;
        if($request->hasFile('image')){
            $filedetail= $request->file('image');
            $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
            $resource->file_name = $dataResult['fileName'];
            $resource->file_extension = $dataResult['fileExt'];
        }
    }
    else if($getCategoryName == "PDF"){
        $file= $request->pdf;
        if($request->hasFile('pdf')){
            $filedetail= $request->file('pdf');
            $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
            $resource->file_name = $dataResult['fileName'];
            $resource->file_extension = $dataResult['fileExt'];
        }
    }
    else if($getCategoryName == "Audio"){
        $file= $request->audio;
        if($request->hasFile('audio')){
            $filedetail= $request->file('audio');
            $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
            $resource->file_name = $dataResult['fileName'];
            $resource->file_extension = $dataResult['fileExt'];
        }
    }
    else if($getCategoryName == "Slide Document"){
        $file= $request->slide_document;
        if($request->hasFile('slide_document')){
            $filedetail= $request->file('slide_document');
            $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
            // if(!empty($dataResult)){
            //     if($dataResult['fileExt'] == 'zip'){
            //         $file = new Filesystem;
            //         $file->cleanDirectory(base_path().'/public/uploads/course_resource/'.$existingPath);
            //         rmdir(base_path().'/public/uploads/course_resource/'.$existingPath);
            //     }
            //     else{
            //         unlink(base_path().'/public/uploads/course_resource/'.$existingPath);    
            //     }
            // }
            $resource->file_name = $dataResult['fileName'];
            $resource->file_extension = $dataResult['fileExt'];
        }
    }
    else if($getCategoryName == "Text"){
        $text= $request->text_description;
        $resource->text = $text;
    }
 
   
  
    $sortingNumber = $request->sorting_number;
    if($request->sorting_number && isset( $sortingNumber)){
        $resource->sort_number =  $sortingNumber;
    }
 
    $resource->save();

    // $delete = CourseResource::where('course_id',$courseID)->delete();
    foreach ($courses as $key => $course_id) {
        $getResourceDetail = Resource::where('id',$resource->id)->first();
        $courseResource = new CourseResource();
        $courseResource->course_id = $course_id;
        $courseResource->resource_id = $resource->id;

        if($getResourceDetail->file_name != null){
            $courseResource->original_filename = $getResourceDetail->file_name;
            $courseResource->file =   'uploads/course_resource/' .$getResourceDetail->file_name;
        }
        
        $courseResource->save();
      
    }
    // dd($courses);

    $manifestService = app(CourseVaultManifestService::class);
    foreach (array_unique(array_map('intval', (array) $courses)) as $courseId) {
        $manifestService->markStaleAndRebuild($courseId);
    }
    if ($getCategoryName === 'Youtube') {
        DownloadYoutubeForVaultJob::dispatch($resource->id);
    }

   

    $this->showToastrMessage('success', __('Resource added successfully'));
    return redirect()->route('admin.resource.index');
}


public function edit($id)
{
   
    $data['title'] = 'Edit Resource';
    $data['categories'] = ResourceCategory::all();
    $data['courses'] = Course::where('status', 1)->get();
    $data['resource'] = Resource::where('id',$id)->first();
    $data['existingPath'] = $data['resource']->file_name;

  
    $getResourceCourses = CourseResource::where('resource_id',$id)->get();
    if(! $getResourceCourses->isEmpty()){
        foreach ( $getResourceCourses as $key => $value) {
            $given_course_ids[] = $value->course_id;
        }
    }
    $given_course_ids = array_unique($given_course_ids);
    $data['courseIDs'] = $given_course_ids;
    // dd($data);
 
    return view('admin.resource.edit', $data);
}






// public function update(Request $request , $id)
// {
//     $request->validate([
//         'resource_title' => ['required', 'string', 'max:100'],
//         'resource_category_id' => ['required', 'integer'],
//     ]);
//     $existingPath = $request->existing_path;
  
//     $categoryTypeID = $request->resource_category_id;
//     $getCategory = ResourceCategory::where('id',$categoryTypeID)->first();
//     $getCategoryName = $getCategory->name;
//     if($getCategory){
//         $types = $getCategory->category_types;
//     }
//     $types = explode(',',$types);
   
//     if(isset($types)){
//         if($getCategoryName == "Video"){
//             if(in_array('mp4',$types)||in_array('webm',$types)||in_array('mkv',$types)){
//                 $request->validate([
//                     'video_file' => 'mimes:mp4,webm,mkv'
//                 ]);
//             }
//         }
//         else if($getCategoryName == "Audio"){
//             if(in_array('mp3',$types)||in_array('wav',$types)||in_array('m4a',$types)){
//                 $request->validate([
//                     'audio' => 'mimes:mp3,wav,m4a'
//                 ]);
//             }
//         }
//         else if($getCategoryName == "Image"){
//             if(in_array('jpg',$types)||in_array('png',$types)||in_array('gif',$types)||in_array('webp',$types)){
//                 $request->validate([
//                     'image' => 'mimes:jpg,png,gig,webp'
//                 ]);
//             }
//         }
//         else if($getCategoryName == "PDF"){
//             if(in_array('pdf',$types)){
//                 $request->validate([
//                     'pdf' => 'mimes:pdf'
//                 ]);
//             }
//         }
//         else if($getCategoryName == "Slide Document"){
//             if(in_array('zip',$types)){
//                 $request->validate([
//                     'slide_document' => 'mimes:zip'
//                 ]);
//             }
//         }
//         else if($getCategoryName == "Text"){
//             if(in_array('text',$types)){
//                 $request->validate([
//                     'text_description' => ['required', 'string'],
//                 ]);
//             }
//         }
//         else if($getCategoryName == "Youtube"){
//             if(in_array('youtube',$types)){
//                 $request->validate([
//                     'youtube_url_path' => ['required'],
//                     'youtube_file_duration' => ['required'],
//                 ]);
//                 if ($request->youtube_file_duration){
//                     if(preg_match('/^([0-9][0-9]):[0-5][0-9]$/',$request->youtube_file_duration)) {
    
//                     } else {
//                         $request->validate([
//                             'youtube_file_duration' => 'date_format:H:i'
//                         ]);
//                     }
//                 }
//             }
//         }


//         // else if(in_array('pdf',$types)||in_array('docx',$types)||in_array('zip',$types) || in_array('pptx',$types) ||in_array('xlsx',$types)){
//         //     $request->validate([
//         //         'resource_file' => 'mimes:pdf,docx,zip,pptx,xlsx'
//         //     ]);
//         // }
//     }
  
//     $courses = $request->course;
//     if(empty($courses)){
//         $this->showToastrMessage('error', __('Please Check Atleast One Course!'));
//         return redirect()->back();
//     }

//     $resource =Resource::where('id',$id)->first();
  
//     $resource->name = $request->resource_title;
//     $resource->type = $getCategoryName;
//     $resource->category_id = $categoryTypeID;
//     $resource->updated_by = auth()->id();

//     if($getCategoryName == "Video"){
//         $file= $request->video_file;
//         if($request->hasFile('video_file')){
//             $filedetail= $request->file('video_file');
//             $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
//             if(!empty($dataResult)){
//                 if($dataResult['fileExt'] == 'zip'){
//                     $file = new Filesystem;
//                     $file->cleanDirectory(base_path().'/public/uploads/course_resource/'.$existingPath);
//                     rmdir(base_path().'/public/uploads/course_resource/'.$existingPath);
//                 }
//                 else{
//                     unlink(base_path().'/public/uploads/course_resource/'.$existingPath);    
//                 }
//             }
//             $resource->file_name = $dataResult['fileName'];
//             $resource->file_extension = $dataResult['fileExt'];
//         }
//     }
//     else if($getCategoryName == "Youtube"){
//         $youtube_url_path = $request->youtube_url_path;
//         $youtube_file_duration = $request->youtube_file_duration;

//         $resource->youtube_url_path = $youtube_url_path;
//         $resource->youtube_video_duration = $youtube_file_duration;
//         $resource->youtube_video_duration_second = $this->timeToSeconds($youtube_file_duration);
//     }
//     else if($getCategoryName == "Image"){
//         $file= $request->image;
//         if($request->hasFile('image')){
//             $filedetail= $request->file('image');
//             $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
//             if(!empty($dataResult)){
//                 if($dataResult['fileExt'] == 'zip'){
//                     $file = new Filesystem;
//                     $file->cleanDirectory(base_path().'/public/uploads/course_resource/'.$existingPath);
//                     rmdir(base_path().'/public/uploads/course_resource/'.$existingPath);
//                 }
//                 else{
//                     unlink(base_path().'/public/uploads/course_resource/'.$existingPath);    
//                 }
//             }
//             $resource->file_name = $dataResult['fileName'];
//             $resource->file_extension = $dataResult['fileExt'];
//         }
//     }
//     else if($getCategoryName == "PDF"){
//         $file= $request->pdf;
//         if($request->hasFile('pdf')){
//             $filedetail= $request->file('pdf');
//             $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
//             if(!empty($dataResult)){
//                 if($dataResult['fileExt'] == 'zip'){
//                     $file = new Filesystem;
//                     $file->cleanDirectory(base_path().'/public/uploads/course_resource/'.$existingPath);
//                     rmdir(base_path().'/public/uploads/course_resource/'.$existingPath);
//                 }
//                 else{
//                     unlink(base_path().'/public/uploads/course_resource/'.$existingPath);    
//                 }
//             }
//             $resource->file_name = $dataResult['fileName'];
//             $resource->file_extension = $dataResult['fileExt'];
//         }
//     }
//     else if($getCategoryName == "Audio"){
//         $file= $request->audio;
//         if($request->hasFile('audio')){
//             $filedetail= $request->file('audio');
//             $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
//             if(!empty($dataResult)){
//                 if($dataResult['fileExt'] == 'zip'){
//                     $file = new Filesystem;
//                     $file->cleanDirectory(base_path().'/public/uploads/course_resource/'.$existingPath);
//                     rmdir(base_path().'/public/uploads/course_resource/'.$existingPath);
//                 }
//                 else{
//                     unlink(base_path().'/public/uploads/course_resource/'.$existingPath);    
//                 }
//             }
//             $resource->file_name = $dataResult['fileName'];
//             $resource->file_extension = $dataResult['fileExt'];
//         }
//     }
//     else if($getCategoryName == "Slide Document"){
//         $file= $request->slide_document;
//         if($request->hasFile('slide_document')){
//             $filedetail= $request->file('slide_document');
//             $dataResult = saveUpload('course_resource', $filedetail, null, null); // new file upload into server
//             if(!empty($dataResult)){
//                 if($dataResult['fileExt'] == 'zip'){
//                     $file = new Filesystem;
//                     $file->cleanDirectory(base_path().'/public/uploads/course_resource/'.$existingPath);
//                     rmdir(base_path().'/public/uploads/course_resource/'.$existingPath);
//                 }
//                 else{
//                     unlink(base_path().'/public/uploads/course_resource/'.$existingPath);    
//                 }
//             }
//             $resource->file_name = $dataResult['fileName'];
//             $resource->file_extension = $dataResult['fileExt'];
//         }
//     }
//     else if($getCategoryName == "Text"){
//         $text= $request->text_description;
//         $resource->text = $text;
//     }

  
//     $sortingNumber = $request->sorting_number;
//     if($request->sorting_number && isset( $sortingNumber)){
//         $resource->sort_number =  $sortingNumber;
//     }

//     $resource->update();

   
//       $delete = CourseResource::where('resource_id',$id)->delete();
//       foreach ($courses as $key => $course_id) {
//         $getResourceDetail = Resource::where('id',$id)->first();
//         $courseResource = new CourseResource();
//         $courseResource->course_id = $course_id;
//         $courseResource->resource_id = $id;

//         if($getResourceDetail->file_name != null){
//             $courseResource->original_filename = $getResourceDetail->file_name;
//             $courseResource->file =   'uploads/course_resource/' .$getResourceDetail->file_name;
//         }
      
//         $courseResource->save();
      
//     }


//     $this->showToastrMessage('success', __('Resource updated successfully'));
//     return redirect()->route('admin.resource.index');
// }




public function update(Request $request , $id)
{
    $request->validate([
        'resource_title' => ['required', 'string', 'max:100'],
        'resource_category_id' => ['required', 'integer'],
    ]);

    $existingPath = $request->existing_path;

    $categoryTypeID = $request->resource_category_id;
    $getCategory = ResourceCategory::where('id', $categoryTypeID)->firstOrFail();
    $getCategoryName = $getCategory->name;

    $types = explode(',', $getCategory->category_types ?? '');

    /* ============================
       Dynamic Validation
    ============================= */
    if ($getCategoryName == "Video" && array_intersect($types, ['mp4','webm','mkv'])) {
        $request->validate(['video_file' => 'mimes:mp4,webm,mkv']);
    }
    elseif ($getCategoryName == "Audio" && array_intersect($types, ['mp3','wav','m4a'])) {
        $request->validate(['audio' => 'mimes:mp3,wav,m4a']);
    }
    elseif ($getCategoryName == "Image" && array_intersect($types, ['jpg','png','gif','webp'])) {
        $request->validate(['image' => 'mimes:jpg,png,gif,webp']);
    }
    elseif ($getCategoryName == "PDF" && in_array('pdf', $types)) {
        $request->validate(['pdf' => 'mimes:pdf']);
    }
    elseif ($getCategoryName == "Slide Document" && in_array('zip', $types)) {
        $request->validate(['slide_document' => 'mimes:zip']);
    }
    elseif ($getCategoryName == "Text" && in_array('text', $types)) {
        $request->validate(['text_description' => ['required','string']]);
    }
    elseif ($getCategoryName == "Youtube" && in_array('youtube', $types)) {
        $request->validate([
            'youtube_url_path' => ['required'],
            'youtube_file_duration' => ['required','date_format:H:i']
        ]);
    }

    /* ============================
       Course Validation
    ============================= */
    if (empty($request->course)) {
        $this->showToastrMessage('error', __('Please Check Atleast One Course!'));
        return redirect()->back();
    }

    $resource = Resource::findOrFail($id);

    $resource->name        = $request->resource_title;
    $resource->type        = $getCategoryName;
    $resource->category_id = $categoryTypeID;
    $resource->updated_by  = auth()->id();

    /* ============================
       Safe Old File Deletion Helper
    ============================= */
    $deleteOld = function () use ($existingPath) {
        if (!$existingPath) return;

        $oldPath = public_path('uploads/course_resource/' . $existingPath);

        if (is_dir($oldPath)) {
            $filesystem = new Filesystem();
            $filesystem->cleanDirectory($oldPath);
            @rmdir($oldPath);
        }
        elseif (is_file($oldPath)) {
            @unlink($oldPath);
        }
    };

    /* ============================
       File Handling
    ============================= */
    if ($getCategoryName == "Video" && $request->hasFile('video_file')) {
        $dataResult = saveUpload('course_resource', $request->file('video_file'), null, null);
        $deleteOld();
        $resource->file_name = $dataResult['fileName'];
        $resource->file_extension = $dataResult['fileExt'];
    }

    elseif ($getCategoryName == "Image" && $request->hasFile('image')) {
        $dataResult = saveUpload('course_resource', $request->file('image'), null, null);
        $deleteOld();
        $resource->file_name = $dataResult['fileName'];
        $resource->file_extension = $dataResult['fileExt'];
    }

    elseif ($getCategoryName == "PDF" && $request->hasFile('pdf')) {
        $dataResult = saveUpload('course_resource', $request->file('pdf'), null, null);
        $deleteOld();
        $resource->file_name = $dataResult['fileName'];
        $resource->file_extension = $dataResult['fileExt'];
    }

    elseif ($getCategoryName == "Audio" && $request->hasFile('audio')) {
        $dataResult = saveUpload('course_resource', $request->file('audio'), null, null);
        $deleteOld();
        $resource->file_name = $dataResult['fileName'];
        $resource->file_extension = $dataResult['fileExt'];
    }

    elseif ($getCategoryName == "Slide Document" && $request->hasFile('slide_document')) {
        $dataResult = saveUpload('course_resource', $request->file('slide_document'), null, null);
        $deleteOld();
        $resource->file_name = $dataResult['fileName'];
        $resource->file_extension = $dataResult['fileExt'];
    }

    elseif ($getCategoryName == "Youtube") {
        $resource->youtube_url_path = $request->youtube_url_path;
        $resource->youtube_video_duration = $request->youtube_file_duration;
        $resource->youtube_video_duration_second = $this->timeToSeconds($request->youtube_file_duration);
    }

    elseif ($getCategoryName == "Text") {
        $resource->text = $request->text_description;
    }

    if ($request->sorting_number) {
        $resource->sort_number = $request->sorting_number;
    }

    $resource->save();

    /* ============================
       Course Mapping
    ============================= */
    CourseResource::where('resource_id', $id)->delete();

    foreach ($request->course as $course_id) {
        $courseResource = new CourseResource();
        $courseResource->course_id = $course_id;
        $courseResource->resource_id = $id;

        if ($resource->file_name) {
            $courseResource->original_filename = $resource->file_name;
            $courseResource->file = 'uploads/course_resource/' . $resource->file_name;
        }

        $courseResource->save();
    }

    $manifestService = app(CourseVaultManifestService::class);
    foreach (array_unique(array_map('intval', (array) $request->course)) as $courseId) {
        $manifestService->markStaleAndRebuild($courseId);
    }
    if ($getCategoryName === 'Youtube') {
        DownloadYoutubeForVaultJob::dispatch((int) $id);
    }

    $this->showToastrMessage('success', __('Resource updated successfully'));
    return redirect()->route('admin.resource.index');
}


public function delete(Request $request, $id){
    $find = Resource::where('id',$id)->first();
    if($find){
        $courseIdsAffected = CourseResource::where('resource_id', $id)->pluck('course_id')->unique()->all();
        $filename = $find->file_name;
        $file_ext = $find->file_extension;
        if($find->delete()){
            if($file_ext == 'zip'){
                $file = new Filesystem;
                $file->cleanDirectory(base_path().'/public/uploads/course_resource/'.$filename);
                rmdir(base_path().'/public/uploads/course_resource/'.$filename);
            }
            else{
                if($file_ext){
                    unlink(base_path().'/public/uploads/course_resource/'.$filename);
                }   
            }
          
            app(CourseVaultManifestService::class)->markStaleAndRebuildCourseIds(array_map('intval', $courseIdsAffected));
            $this->showToastrMessage('success', __('Resource deleted successfully'));
             return redirect()->route('admin.resource.index'); 
        }
        else{
            $this->showToastrMessage('warning', __('Something went wrong'));
            return redirect()->route('admin.resource.index'); 
        }
    }
    else{
        $this->showToastrMessage('warning', __('Something went wrong'));
        return redirect()->route('admin.resource.index'); 
    }
 
}



public function viewFullResource($file_ext , $file_name){
    $base_url = env('APP_URL');
    $data['baseURL'] = $base_url;
    $data['file_ext']=$file_ext;
    $data['file_name']=$file_name;
    // dd($data['baseURL']);
    return view('admin.resource.viewfull', $data);
  
}


public function viewFullResourceOther($resource_id){
    $base_url = env('APP_URL');
    $data['baseURL'] = $base_url;
    $getResource = Resource::where('id',$resource_id)->first();
    $data['resourceType']=$getResource->type;
    if($getResource->type == 'Youtube'){
        $data['youtube_url_path']=$getResource->youtube_url_path;
        $data['youtube_video_duration']=$getResource->youtube_video_duration;
        $data['youtube_video_duration_second']=$getResource->youtube_video_duration_second;
    }
    $data['text_description']=$getResource->text;
//  dd($data);
    return view('admin.resource.viewfullother', $data);
  
}



public function timeToSeconds(string $time): int
{
    $arr = explode(':', $time);
    if (count($arr) === 3) {
        return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
    }
    return $arr[0] * 60 + $arr[1];
}


}
