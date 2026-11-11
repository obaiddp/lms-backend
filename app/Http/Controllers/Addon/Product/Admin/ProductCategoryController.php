<?php

namespace App\Http\Controllers\Addon\Product\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon\Product\ProductCategory;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    use  ImageSaveTrait, General;

    protected $model;
    public function __construct(ProductCategory $category)
    {
        $this->model = new Crud($category);
    }
    public function index()
    {
        if (!auth()->user()->can('product_module_category')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Manage Product Category');
        $data['productCategories'] = $this->model->getOrderById('DESC', 25);
        return view('addon.product.admin.category.index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('product_module_category')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Add Product Category');
        return view('addon.product.admin.category.create', $data);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('product_module_category')) {
            abort('403');
        } // end permission checking

        // $request->validate([
        //     'name' => ['required', 'unique:product_categories,name', 'max:120'],
        //     'image' => 'bail|required|mimes:png|file|dimensions:min_width=60,min_height=60,max_width=60,max_height=60'
        // ]);

        $request->validate([
            'name' => ['required', 'unique:product_categories,name', 'max:120'],
            'image' => 'bail|required|mimes:png|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300'
        ]);

        $IMAGE = $request->image ? $this->saveImage('product-category', $request->image, null, null) :   null;
        $data = [
            'name' => $request->name,
            'is_feature' => $request->is_feature ? 'yes' : 'no',
            'slug' => getSlug($request->name),
            'image' => $IMAGE,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ];

        if($request->hasFile('og_image')){
            $data['og_image'] = $this->saveImage('meta', $request->og_image, null, null);
        }
        else{
            $data['og_image'] = $IMAGE;
        }

        $this->model->create($data); // create new category

        return $this->controlRedirection($request, 'lms_product.admin.category', 'ProductCategory');
    }

    public function edit($slug)
    {
        if (!auth()->user()->can('product_module_category')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Edit Product Category');
        $data['productCategory'] = $this->model->getRecordBySlug($slug);
        return view('addon.product.admin.category.edit', $data);
    }

    public function update(Request $request, $slug)
    {
        if (!auth()->user()->can('product_module_category')) {
            abort('403');
        } // end permission checking
        
        $oldData = $this->model->getRecordBySlug($slug); // update tag

        $request->validate([
            'name' => ['required', 'unique:product_categories,name,'.$oldData->id, 'max:255'],
            'image' => 'mimes:png|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300'
        ]);

        $category = $this->model->getRecordBySlug($slug);

        if ($request->image)
        {
            $this->deleteFile($category->image); // delete file from server

            $image = $this->saveImage('product-category', $request->image, null, null); // new file upload into server

        } else {
            $image = $category->image;
        }

        $data = [
            'name' => $request->name,
            'is_feature' => $request->is_feature ? 'yes' : 'no',
            'slug' => getSlug($request->name),
            'image' => $image,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ];

        if($request->hasFile('og_image')){
            $data['og_image'] = $this->saveImage('meta', $request->og_image, null, null);
        }
        else{
            if($category->og_image){
                $data['og_image'] = $category->og_image;
            }
            else{
                $data['og_image'] = $image;
            }
        }

        $this->model->updateBySlug($data, $slug); // update category

        return $this->controlRedirection($request, 'lms_product.admin.category', 'ProductCategory');
    }

    public function delete($slug)
    {
        if (!auth()->user()->can('product_module_category')) {
            abort('403');
        } // end permission checking

        $category = $this->model->getRecordBySlug($slug);
        
        if(count($category->products)){
            $this->showToastrMessage('error', __('This category has been used'));
            return redirect()->back();
        }
        
        $this->deleteFile($category->image); // delete file from server
        $this->model->deleteBySlug($slug); // delete record

        $this->showToastrMessage('error', __('Product Category has been deleted'));
        return redirect()->back();
    }
}
