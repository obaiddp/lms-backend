<?php

namespace App\Http\Controllers\Addon\Product\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Services\EmailSendService;
use App\Models\Addon\Product\Product;
use App\Models\Addon\Product\ProductCategory;
use App\Models\Addon\Product\ProductTag;
use App\Models\Addon\Product\TagProduct;
use App\Models\Order_item;
use App\Traits\ApiStatusTrait;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use General , ImageSaveTrait, SendNotification, ApiStatusTrait;

    public function index()
    {
        $data['title'] = __('My Products');
        $data['subNavMyProductActiveClass'] = 'active';
        $data['navProductActiveClass'] = 'has-open';

        $data['products'] =  Product::where('user_id', auth()->id())->where('status', STATUS_SUCCESS)->paginate(10);
        return view('addon.product.instructor.my-product', $data);
    }

    public function upload($step=1)
    {
        $data['subNavUploadProductActiveClass'] = 'active';
        $data['navProductActiveClass'] = 'has-open';
        if($step == 'step-1'){
            $data['title'] = __('Upload Product');
            return view('addon.product.instructor.upload', $data);
        }elseif($step == 'step-2'){
            $data['title'] = __('Upload Product');
            $data['type'] = request()->get('type');
            $data['categories'] = ProductCategory::all();
            $data['tags'] = ProductTag::all();
            return view('addon.product.instructor.upload-product', $data);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'nullable',
            "title" => 'bail|required|string|max:155',
            "slug" => 'bail|required|unique:products',
            "product_category_id" => 'bail|required',
            "old_price" => 'bail|required|min:0',
            "discount_percentage" => 'bail|required|min:0|max:100',
            "current_price" => 'bail|required|min:0',
            "quantity" => 'bail|required|min:0',
            "shipping_charge" => 'bail|nullable|min:0',
            "description" => 'bail|required',
            "shipping_return" => 'bail|nullable',
            "additional_information" => 'bail|nullable',
            "meta_title" => 'bail|nullable',
            "meta_description" => 'bail|nullable',
            "meta_keywords" => 'bail|nullable',
            "og_image" => 'bail|nullable',
            'thumbnail' => 'bail|required|mimes:jpeg,png,jpg|file',
            'image_1' => 'bail|required|mimes:jpeg,png,jpg|file',
            'image_2' => 'bail|required|mimes:jpeg,png,jpg|file',
            'image_3' => 'bail|required|mimes:jpeg,png,jpg|file',
            'image_4' => 'bail|required|mimes:jpeg,png,jpg|file',
        ];

        if($request->type == DIGITAL_PRODUCT){
            $rules['file'] = 'bail|required|mimes:zip|file';
        }

        $data = $request->validate($rules);

        try{
            DB::beginTransaction();


            if($request->hasFile('og_image')){
                $data['og_image'] = $this->saveImage('meta', $request->og_image, null, null);
            }
            if($request->hasFile('thumbnail')){
                $data['thumbnail'] = $this->saveImage('product', $request->thumbnail, null, null);
            }
            if($request->hasFile('image_1')){
                $data['image_1'] = $this->saveImage('product', $request->image_1, null, null);
            }
            if($request->hasFile('image_2')){
                $data['image_2'] = $this->saveImage('product', $request->image_2, null, null);
            }
            if($request->hasFile('image_3')){
                $data['image_3'] = $this->saveImage('product', $request->image_3, null, null);
            }
            if($request->hasFile('image_4')){
                $data['image_4'] = $this->saveImage('product', $request->image_4, null, null);
            }
            if($request->hasFile('file')){
                $data['main_file'] = $this->uploadFile('product', $request->file, null, null);
            }

            $product = Product::create($data);

            if ($request->tag_ids){
                TagProduct::where('product_id', $product->id)->delete();
                foreach ($request->tag_ids as $tag_id){
                    $productTag = new TagProduct();
                    $productTag->product_id = $product->id;
                    $productTag->product_tag_id = $tag_id;
                    $productTag->save();
                }
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return $this->error([],  $e->getMessage());
        }

        return $this->success([],  __("Product upload successfully,  Wait for admin approval"));

    }

    public function edit($slug)
    {
        $data['subNavUploadProductActiveClass'] = 'active';
        $data['navProductActiveClass'] = 'has-open';
        $data['title'] = __('Update Product');
        $data['categories'] = ProductCategory::all();
        $data['tags'] = ProductTag::all();
        $data['product'] = Product::where('slug', $slug)->where('user_id', auth()->id())->first();
        if(is_null($data['product'])){
            $this->showToastrMessage('error', __('No product found!'));
            return back();
        }
        return view('addon.product.instructor.edit-product', $data);
    }

    public function update(Request $request, $slug)
    {
        $product = Product::where('user_id', auth()->id())->whereSlug($slug)->firstOrFail();
        $rules = [
            'type' => 'nullable',
            "title" => 'bail|required|string|max:155',
            "slug" => 'bail|required|unique:products,slug,'.$product->id,
            "product_category_id" => 'bail|required',
            "old_price" => 'bail|required|min:0',
            "discount_percentage" => 'bail|required|min:0|max:100',
            "current_price" => 'bail|required|min:0',
            "quantity" => 'bail|required|min:0',
            "shipping_charge" => 'bail|nullable|min:0',
            "description" => 'bail|required',
            "shipping_return" => 'bail|nullable',
            "additional_information" => 'bail|nullable',
            "meta_title" => 'bail|nullable',
            "meta_description" => 'bail|nullable',
            "meta_keywords" => 'bail|nullable',
            "og_image" => 'bail|nullable',
            'thumbnail' => 'bail|nullable|mimes:jpeg,png,jpg|file',
            'image_1' => 'bail|nullable|mimes:jpeg,png,jpg|file',
            'image_2' => 'bail|nullable|mimes:jpeg,png,jpg|file',
            'image_3' => 'bail|nullable|mimes:jpeg,png,jpg|file',
            'image_4' => 'bail|nullable|mimes:jpeg,png,jpg|file',
        ];

        if($request->type == DIGITAL_PRODUCT){
            $rules['file'] = 'bail|nullable|mimes:zip|file';
        }

        $data = $request->validate($rules);

        try{
            DB::beginTransaction();


            if($request->hasFile('og_image')){
                $data['og_image'] = $this->saveImage('meta', $request->og_image, null, null);
            }
            if($request->hasFile('thumbnail')){
                $data['thumbnail'] = $this->saveImage('product', $request->thumbnail, null, null);
            }
            if($request->hasFile('image_1')){
                $data['image_1'] = $this->saveImage('product', $request->image_1, null, null);
            }
            if($request->hasFile('image_2')){
                $data['image_2'] = $this->saveImage('product', $request->image_2, null, null);
            }
            if($request->hasFile('image_3')){
                $data['image_3'] = $this->saveImage('product', $request->image_3, null, null);
            }
            if($request->hasFile('image_4')){
                $data['image_4'] = $this->saveImage('product', $request->image_4, null, null);
            }
            if($request->hasFile('file')){
                $data['main_file'] = $this->uploadFile('product', $request->file, null, null);
            }

            $product->update($data);

            if ($request->tag_ids){
                TagProduct::where('product_id', $product->id)->delete();
                foreach ($request->tag_ids as $tag_id){
                    $productTag = new TagProduct();
                    $productTag->product_id = $product->id;
                    $productTag->product_tag_id = $tag_id;
                    $productTag->save();
                }
            }
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return $this->error([],  $e->getMessage());
        }

        return $this->success([],  __("Product update successfully"));

    }

    public function delete($slug)
    {
        $product = Product::where('user_id', auth()->id())->whereSlug($slug)->firstOrFail();
        $order_item = Order_item::whereProductId($product->id)->first();

        if ($order_item)
        {
            $this->showToastrMessage('error', __('You can not deleted. Because already sold it!'));
            return redirect()->back();
        }

        $product->delete();
        $this->showToastrMessage('success', __('Product has been deleted.'));
        return redirect()->back();
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'quantity' => 'bail|required|min:1'
        ]);

        try{
            DB::beginTransaction();
            $product = Product::where('user_id', auth()->id())->whereSlug($request->slug)->firstOrFail();
            $product->increment('quantity', (int)$request->quantity);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            return $this->error([],  $e->getMessage());
        }

        return $this->success([],  __("Stock update successfully"));
    }

    public function orders(Request $request)
    {
        $data['title'] = __('Orders');
        $data['subNavOrderProductActiveClass'] = 'active';
        $data['navProductActiveClass'] = 'has-open';

        $data['orderItems'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')->orderBy('id', 'DESC')->paginate(10);

        return view('addon.product.instructor.orders', $data);
    }

    public function statusChange(Request $request)
    {
        try{
            DB::beginTransaction();
            $orderItem = Order_item::where(['id'=> $request->id, 'owner_user_id' => auth()->id()])->firstOrFail();
            $orderItem->delivery_status = $request->delivery_status;
            $orderItem->save();

            $text = __("Your product order status has been changed.");
            $target_url = route('lms_product.student.purchase_list');
            $this->send($text, 3, $target_url, $orderItem->user_id);

            $sendEmail = new EmailSendService();
            $sendEmail->sendCommonUserAndLink($orderItem->user, $target_url, 'product-order-status-change-student');

            if($request->delivery_status == 4){
                //refund process
                createTransaction($orderItem->user_id, $orderItem->owner_balance + $orderItem->sell_commission, TRANSACTION_REFUND, 'Product Refund', 'Order_item (' . $orderItem->id . ')', $orderItem->id);
                if ($orderItem->user_id) {
                    $orderItem->user->increment('balance', decimal_to_int($orderItem->owner_balance + $orderItem->sell_commission));
                }

                createTransaction($orderItem->owner_user_id, $orderItem->owner_balance, TRANSACTION_SELL_REFUND, 'Product Refund Reversed', 'Order_item (' . $orderItem->id . ')');
                if ($orderItem->owner_user_id) {
                    $orderItem->owner_user->decrement('balance', decimal_to_int($orderItem->owner_balance));
                }

                Product::where('id', $orderItem->product_id)->increment('quantity', $orderItem->unit);

            }

            DB::commit();

            return $this->success([],  __('Status Change Successfully'));

         }catch(Exception $e){
            DB::rollBack();
            return $this->error([],  $e->getMessage());
        }
    }
}
