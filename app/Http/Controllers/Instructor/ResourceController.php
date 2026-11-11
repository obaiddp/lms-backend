<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseResource;
use App\Models\ResourceCategory;
use App\Models\Resource;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    use General, ImageSaveTrait;

    protected $resourceModel, $courseModel;

    public function __construct(CourseResource $resources, Course $course)
    {
        $this->resourceModel = new CRUD($resources);
        $this->courseModel = new CRUD($course);
    }

    public function index($course_uuid)
    {
        $resources_array = array();
        $category_ids_array = array();
        $result_array = array();
        $data['title'] = 'Resources';
        $data['navCourseActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $course_id = $data['course']->id;
        $data['courseID'] = $course_id;
        $data['resources'] = CourseResource::where('course_id', $data['course']->id)->paginate();
        $getCourseResources =  CourseResource::where('course_id',$course_id)->get();
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
        $category_ids_array = array_unique($category_ids_array);
      
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            $data['categories'] = $getCategories;
    

        return view('instructor.course.resources.index', $data);
    }

    public function getResources(Request $request){
        $courseID = $request->course_id;
        $categoryID = $request->category_id;
        $result_array = array();
        $resources = CourseResource::where('course_id',$courseID)
        ->whereHas('resource', function ($query) use ($categoryID) {
            $query->where('category_id', $categoryID);
        })->get();
        if(!$resources->isEmpty()){
            foreach ($resources as $key => $value) {
                $result_array[] = [
					'resource_id'=>$value->resource->id,
					'resource_name'=>$value->resource->name,
					'resource_ext'=>$value->resource->file_extension,
					'resource_path'=>$value->resource->file_name,
                    'resource_type'=>$value->resource->type,
				];
            }
        }
        echo json_encode($result_array);
        // dd($result_array);
    }


    public function viewFullResource($file_ext , $file_name){
        $base_url = env('APP_URL');
        if($base_url == "http://localhost:8000"){
            $base_url = "http://localhost:8000/";
        }
     
        $data['baseURL'] = $base_url;
        $data['file_ext']=$file_ext;
		$data['file_name']=$file_name;
        // dd($data['baseURL']);
        return view('instructor.course.resources.viewfull', $data);
      
    }

    public function viewFullResourceOther($resource_id){
        $base_url = env('APP_URL');
        if($base_url == "http://localhost:8000"){
            $base_url = "http://localhost:8000/";
        }
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
        return view('instructor.course.resources.viewfullother', $data);
      
    }

    public function create($course_uuid)
    {
        $data['title'] = 'Resource Add';
        $data['navCourseActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        return view('instructor.course.resources.create', $data);
    }

    public function store(Request $request, $course_uuid)
    {
        $request->validate([
            "file" => "required|mimes:zip"
        ]);

        $course = $this->courseModel->getRecordByUuid($course_uuid);

        $resource = new CourseResource();
        $resource->course_id = $course->id;

        if ($request->hasFile('file')) {
            $image_details = $this->uploadFileWithDetails('course_resource', $request->file);
            if (!$image_details['is_uploaded']) {
                $this->showToastrMessage('error', __('Something went wrong! Failed to upload file'));
                return redirect()->route('resource.index', $course_uuid);
            }
            $resource->file = $image_details['path'];
            $resource->original_filename = $image_details['original_filename'];
        }

        $resource->save();

        $this->showToastrMessage('success', __('Resource Created Successfully'));
        return redirect()->route('resource.index', $course_uuid);
    }

    public function delete($uuid)
    {
        $resource = $this->resourceModel->getRecordByUuid($uuid);
        $this->deleteVideoFile($resource->file);
        $this->resourceModel->deleteByUuid($uuid);
        $this->showToastrMessage('success', __('Resource Deleted Successfully'));
        return redirect()->back();
    }
}
