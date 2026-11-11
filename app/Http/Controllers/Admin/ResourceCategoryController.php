<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\CourseResource;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Traits\SendNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResourceCategoryController extends Controller
{
    use General, ImageSaveTrait, SendNotification;
    public function __construct(Course $course, ResourceCategory $category)
    {
        $this->model = new Crud($course);
        $this->categoryModel = new Crud($category);
    }

    public function index()
    {
        if (!Auth::user()->can('manage_resource_categories')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'All Resource Categories';
        $data['categories'] = $this->categoryModel->getOrderById('DESC', 25);
        return view('admin.resource_category.index', $data);
    }


    public function add()
{
   
    $data['title'] = 'Add Resource Category';
    return view('admin.resource_category.add', $data);
}



public function store(Request $request)
{
    $request->validate([
        'category_name' => ['required', 'string', 'max:100'],
    ]);
    $categoryTypes = $request->categorytype;
    if(empty($categoryTypes)){
        $this->showToastrMessage('warning', __('Please check atleast one category type'));
        return redirect()->route('admin.resource.category.add');
    }
    $categoryTypeString = implode(',',$request->categorytype);
  
   

    $category = new ResourceCategory();
    $category->name = $request->category_name;
    $category->category_types = $categoryTypeString;
    $category->description = $request->category_desc;
    $category->created_at = date('y-m-d h:i:sa' );
    $category->created_by =  Auth::id();
  
    $category->save();

   

    $this->showToastrMessage('success', __('Resource category created successfully'));
    return redirect()->route('admin.resource.category.index');
}



public function edit($id)
{
   
    $data['title'] = 'Edit Resource Category';
    $data['category'] = ResourceCategory::where('id',$id)->first();
    $category_types=explode(',', $data['category']->category_types);
    $data['category_types']=$category_types;
 
    return view('admin.resource_category.edit', $data);
}


public function update(Request $request , $id)
{
    $request->validate([
        'category_name' => ['required', 'string', 'max:100'],
    ]);
    $categoryTypes = $request->categorytype;
    if(empty($categoryTypes)){
        $this->showToastrMessage('warning', __('Please check atleast one category type'));
        return redirect()->route('admin.resource.category.edit',['id'=>$id]);
    }
    $categoryTypeString = implode(',',$request->categorytype);
  
   

    $category = ResourceCategory::where('id',$id)->first();
    $category->name = $request->category_name;
    $category->category_types = $categoryTypeString;
    $category->description = $request->category_desc;
    $category->updated_at = date('y-m-d h:i:sa' );
    $category->updated_by =  Auth::id();
  
    $category->update();

   

    $this->showToastrMessage('success', __('Resource category updated successfully'));
    return redirect()->route('admin.resource.category.index');
}



public function delete(Request $request, $id){
    $find = ResourceCategory::where('id',$id)->first();
    if($find){
        if($find->delete()){
            $this->showToastrMessage('success', __('Resource category deleted successfully'));
             return redirect()->route('admin.resource.category.index'); 
        }
        else{
            $this->showToastrMessage('warning', __('Something went wrong'));
            return redirect()->route('admin.resource.category.index'); 
        }
    }
    else{
        $this->showToastrMessage('warning', __('Something went wrong'));
        return redirect()->route('admin.resource.category.index'); 
    }
 
}
}
