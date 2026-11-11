<?php

namespace App\Http\Controllers\Addon\Product\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Addon\Product\Product;
use App\Models\Addon\Product\ProductCategory;
use App\Models\Addon\Product\ProductTag;
use App\Traits\ApiStatusTrait;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    use General , ImageSaveTrait, SendNotification, ApiStatusTrait;
    public function list(Request $request)
    {
        
     
        $data['pageTitle'] = "All Products";
        $data['categories'] = ProductCategory::get();
        $data['tags'] = ProductTag::get();

        if($request->ajax()){
            
            $products = Product::where('status', STATUS_SUCCESS);
            if($request->min_price >= 0){
                $products->where('current_price', '>=' , $request->min_price ?? 0);
            }
            if($request->max_price >= 0){
                $products->where('current_price', '<=' , $request->max_price ?? 0);
            }
            if($request->category != NULL && is_array($request->category)){
                $products->whereIn('product_category_id', $request->category);
            }
            if($request->category != NULL){
                $products->where('product_category_id', $request->category);
            }
            if($request->rating != NULL){
                $products->where('average_review', '>=', $request->rating);
            }
            if($request->discount != NULL){
                $products->where('discount_percentage', '>=', $request->discount);
            }
            if($request->search_key != NULL && $request->search_key != ''){
                $products->where('title', 'LIKE',"%{$request->search_key}%");
            }
           
            if($request->tag != NULL){
                $products->join('tag_products', 'tag_products.product_id', '=', 'product.id')
                ->where('tag_products.product_tag_id', $request->tag);
             }

            // if($request->tag != NULL && is_array($request->tags)){
            //    $products->join('tag_products', 'tag_products.product_id', '=', 'product.id')
            //    ->whereIn('tag_products.product_tag_id', $request->tag);
            // }
           
            if($request->sort_by == 1){
                $products->orderBy('id', 'DESC');
            }else{
                $products->orderBy('id', 'ASC');
            }

            $data['products'] = $products->paginate(9);

            $data['html'] = View::make('addon.product.partials.product-list-render', $data)->render();
            $data['status'] = true;
            return response()->json($data);
        }
        else{
            if($request->category != NULL){
             
                  $data['products'] = Product::where('status', STATUS_SUCCESS)->where('product_category_id', $request->category)->paginate(9);
                 
            }
            else{
                  $data['products'] = Product::where('status', STATUS_SUCCESS)->paginate(9);
            }
            
        }

         if($request->category != NULL){
            $data['total_products'] = Product::where('status', STATUS_SUCCESS)->where('product_category_id', $request->category)->count();
         }
         else{
              $data['total_products'] = Product::where('status', STATUS_SUCCESS)->count();
         }

        $data['highest_price'] = Product::where('status', STATUS_SUCCESS)->max('current_price');

        return view('addon.product.frontend.list', $data);
    }
    
    public function view($slug)
    {
        $data['pageTitle'] = __("Product Details");
        $data['product'] = Product::whereSlug($slug)->with('user')->first();
        // dd($data['product']);
        if ((auth()->user() && auth()->user()->role != USER_ROLE_ADMIN) && $data['product']->status != STATUS_APPROVED && $data['product']->status != STATUS_UPCOMING_APPROVED){
            $this->showToastrMessage('error', __('This product is not active'));
            return redirect()->back();
        }

        return view('addon.product.frontend.details', $data);
    }

    public function view2($slug)
    {
        $data['pageTitle'] = __("Product Details");
        $data['product'] = Product::whereSlug($slug)->with('user')->first();
        // dd($data['product']);
        if ((auth()->user() && auth()->user()->role != USER_ROLE_ADMIN) && $data['product']->status != STATUS_APPROVED && $data['product']->status != STATUS_UPCOMING_APPROVED){
            $this->showToastrMessage('error', __('This product is not active'));
            return redirect()->back();
        }

        return view('addon.product.frontend.details2', $data);
    }
   
}
