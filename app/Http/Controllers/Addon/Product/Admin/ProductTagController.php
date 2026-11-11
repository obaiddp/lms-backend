<?php

namespace App\Http\Controllers\Addon\Product\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon\Product\ProductTag;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    use General;

    protected $model;

    public function __construct(ProductTag $tag)
    {
        $this->model = new Crud($tag);
    }

    public function index()
    {
        if (!auth()->user()->can('product_module_tag')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Manage Product Tag');
        $data['productTags'] = $this->model->getOrderById('DESC', 25);
        return view('addon.product.admin.tag.index', $data);
    }

    public function create()
    {
        if (!auth()->user()->can('product_module_tag')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Add Product Tag');
        return view('addon.product.admin.tag.create', $data);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('product_module_tag')) {
            abort('403');
        } // end permission checking

        $request->validate([
            'name' => ['required', 'unique:product_tags,name', 'string', 'max:255'],
        ]);

        $data = [
            'name' => $request->name,
            'slug' => getSlug($request->name)
        ];
        $this->model->create($data); // create new category
        return $this->controlRedirection($request, 'lms_product.admin.tag', 'ProductTag');
    }

    public function edit($slug)
    {
        if (!auth()->user()->can('product_module_tag')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Edit Tag');
        $data['productTag'] = $this->model->getRecordBySlug($slug);
        return view('addon.product.admin.tag.edit', $data);
    }

    public function update(Request $request, $slug)
    {
        if (!auth()->user()->can('product_module_tag')) {
            abort('403');
        } // end permission checking
        
        $oldData = $this->model->getRecordBySlug($slug); // update tag

        $request->validate([
            'name' => ['required', 'unique:product_tags,name,'.$oldData->id, 'string', 'max:255'],
        ]);

        $data = [
            'name' => $request->name,
            'slug' => getSlug($request->name)
        ];
        $this->model->updateBySlug($data, $slug); // update tag
        return $this->controlRedirection($request, 'lms_product.admin.tag', 'ProductTag');
    }

    public function delete($slug)
    {
        if (!auth()->user()->can('product_module_tag')) {
            abort('403');
        } // end permission checking

        $tag = $this->model->getRecordBySlug($slug);

        if(count($tag->product)){
            $this->showToastrMessage('error', __('This tag has been used'));
            return redirect()->back();
        }

        $this->model->deleteBySlug($slug); // delete record

        $this->showToastrMessage('error', __('Product Tag has been deleted'));
        return redirect()->back();
    }
}
