<?php

namespace App\Http\Controllers\Addon\Product\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\EmailSendService;
use App\Models\Addon\Product\Product;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Student;
use App\Models\Addon\Product\ProductCategory;
use App\Models\Addon\Product\ProductTag;
use App\Models\Addon\Product\TagProduct;
use App\Models\User;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiStatusTrait;
use Carbon\Carbon;

class ProductController extends Controller
{
    use General, ImageSaveTrait, SendNotification;
    protected $model, $lectureModel, $lessonModel;

    public function __construct(Product $product)
    {
        $this->model = new Crud($product);
    }

    public function dashboard()
    {
     
        $data['title'] = __('Dashboard');
        $data['navProductActiveClass'] = 'has-open';
        $data['subNavProductDashboardActiveClass'] = 'active';

        $data['pendingOrder'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
              ->orWhere('payment_status', 'free')
                ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->where('delivery_status', 0)
        ->count();
        
        $data['processingOrder'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereIn('delivery_status', [1,2])
        ->count();
        
        $data['deliveredOrder'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->where('delivery_status', 3)
        ->count();
       
        $data['cancelledOrder'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->where('delivery_status', 4)
        ->count();

        $dayExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%d', created_at)"
            : 'DATE_FORMAT(created_at, "%d")';
       
        $data['thisMonthSale'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->count();
        
        $data['thisMonthEarning'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->sum('owner_balance');

        $data['thisYearSale'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->count();
        
        $data['thisYearEarning'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->sum('owner_balance');

        $data['products'] =  Product::where('user_id', auth()->id())->where('status', STATUS_SUCCESS)->paginate(10);


        $top5Product = Product::where('products.user_id', auth()->id())
                ->join('order_items', 'order_items.product_id', 'products.id')
                ->join('orders', 'orders.id', 'order_items.order_id')
                ->where('orders.payment_status', 'paid')
                ->selectRaw('products.title, COALESCE(sum(order_items.unit),0) total')
                ->groupBy('products.id')
                ->orderBy('total','desc')
                ->take(5)
                ->get();

        $data['top5ProductLabels'] = $top5Product->pluck('title')->toArray();
        $data['top5ProductSeries'] = $top5Product->pluck('total')->toArray();

        $thisMonthSale = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })
        ->whereNotNull('product_id')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->selectRaw("{$dayExpression} as day, COALESCE(sum(order_items.unit),0) total")
        ->groupBy('day')
        ->get();
       
        $thisMonthEarning = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
            ->orWhere('payment_status', 'free')
            ->orWhere('payment_status', 'due');
            $q->where('owner_user_id', auth()->id());
        })
        ->whereNotNull('product_id')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->selectRaw("{$dayExpression} as day, COALESCE(sum(order_items.owner_balance),0) total")
        ->groupBy('day')
        ->get();

        $chartThisMonthSaleData = [];
        $chartThisMonthSaleCategories = [];
        $chartThisMonthEarningData = [];
        $chartThisMonthEarningCategories = [];

        for ($x = 1; $x <= Carbon::now()->month()->daysInMonth; $x++) {
            array_push($chartThisMonthSaleCategories, $x);
            array_push($chartThisMonthSaleData, $thisMonthSale->where('day', $x)->sum('total', 0));
            array_push($chartThisMonthEarningCategories, $x);
            array_push($chartThisMonthEarningData, $thisMonthEarning->where('day', $x)->sum('total', 0));
        }


        $data['chartThisMonthSaleData'] = $chartThisMonthSaleData;
        $data['chartThisMonthSaleCategories'] = $chartThisMonthSaleCategories;
        $data['chartThisMonthEarningData'] = $chartThisMonthEarningData;
        $data['chartThisMonthEarningCategories'] = $chartThisMonthEarningCategories;
       

        return view('addon.product.admin.product.dashboard', $data);
    }



    public function orders(Request $request)
    {
        $data['title'] = __('Orders');
        $data['subNavOrderProductActiveClass'] = 'active';
        $data['navProductActiveClass'] = 'has-open';

        $data['orderItems'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
                ->orWhere('payment_status', 'free')
                ->orWhere('payment_status', 'due');
            // $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')->orderBy('id', 'DESC')->paginate(10);

        return view('addon.product.admin.product.orders', $data);
    }



    public function statusChangeOrder(Request $request)
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

            return response()->json([
                'message' => __('Status Change Successfully'),
            ]);
            // return $this->success([],  __('Status Change Successfully'));

         }catch(Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ]);
            // return $this->error([],  $e->getMessage());
        }
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
            return response()->json([
                'message' => $e->getMessage(),
            ]);
            // return $this->error([],  $e->getMessage());
        }

        $this->showToastrMessage('success', __("Stock updated successfully"));
        return redirect()->route('lms_product.admin.product.dashboard');

        // return response()->json([
        //     'message' => __("Stock updated successfully"),
        // ]);
        // return $this->success([],  __("Stock update successfully"));
    }


    public function index()
    {
        if (!auth()->user()->can('product_module_product')) {
            abort('403');
        } // end permission checking

        $data['subNavProductActiveClass'] = 'mm-active';
        $data['title'] = __('All Product');
        // $data['products'] = $this->model->getOrderById('DESC', 25);
        $data['products'] = Product::where('title' ,'<>','Donation')->orderBy('id','DESC')->paginate(25);
        return view('addon.product.admin.product.index', $data);
    }

    public function create($step=1)
    {
     
        $data['subNavUploadProductActiveClass'] = 'active';
        $data['navProductActiveClass'] = 'has-open';
        if($step == 'step-1'){
            $data['title'] = __('Upload Product');
            return view('addon.product.admin.product.upload', $data);
        }elseif($step == 'step-2'){
            $data['title'] = __('Upload Product');
            $data['type'] = request()->get('type');
            $data['categories'] = ProductCategory::all();
            $data['tags'] = ProductTag::all();
            return view('addon.product.admin.product.upload-product', $data);
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

  
        if($request->title == 'Donation'){
            $this->showToastrMessage('error', 'Given Title "Donation" is Already In Use');
            return redirect()->back();
        }
              dd($request->all());
        if($request->type == DIGITAL_PRODUCT){
            $rules['file'] = 'bail|required|mimes:zip|file';
        }

        $data = $request->validate($rules);
      
        try{
            DB::beginTransaction();


          
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
            if($request->hasFile('og_image')){
                $data['og_image'] = $this->saveImage('meta', $request->og_image, null, null);
            }
            else{
                $data['og_image'] = $data['thumbnail'];
            }
            
            if($request->type == PHYSICAL_PRODUCT){
                if($request->shipping_charge == null){
                    $data['shipping_charge'] = 0.00;
                }
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
            $getProduct = Product::where('id',$product->id)->first();
            $getProduct->status = 1;
            $getProduct->update();
         
        }catch(Exception $e){
            DB::rollBack();
            $this->showToastrMessage('error', $e->getMessage());
            return redirect()->back();
            // return $this->error([],  $e->getMessage());
        }

        $this->showToastrMessage('success', __("Product created successfully"));
        return redirect()->route('lms_product.admin.product.index');
        // return $this->success([],  __("Product upload successfully,  Wait for admin approval"));
        // return $this->success([],  __("Product created successfully"));

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
            return redirect()->back();
            // return back();
        }
        return view('addon.product.admin.product.edit-product', $data);
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

        if($request->title == 'Donation'){
            $this->showToastrMessage('error', 'Given Title "Donation" is Already In Use');
            return redirect()->back();
        }

        if($request->type == DIGITAL_PRODUCT){
            $rules['file'] = 'bail|nullable|mimes:zip|file';
        }

        $data = $request->validate($rules);

        try{
            DB::beginTransaction();


         
            if($request->hasFile('thumbnail')){
                $data['thumbnail'] = $this->saveImage('product', $request->thumbnail, null, null);
            }
            else{
                $data['thumbnail'] = $product->thumbnail;
            }
            if($request->hasFile('image_1')){
                $data['image_1'] = $this->saveImage('product', $request->image_1, null, null);
            }
            else{
                $data['image_1'] = $product->image_1;
            }
            if($request->hasFile('image_2')){
                $data['image_2'] = $this->saveImage('product', $request->image_2, null, null);
            }
            else{
                $data['image_2'] = $product->image_2;
            }
            if($request->hasFile('image_3')){
                $data['image_3'] = $this->saveImage('product', $request->image_3, null, null);
            }
            else{
                $data['image_3'] = $product->image_3;
            }
            if($request->hasFile('image_4')){
                $data['image_4'] = $this->saveImage('product', $request->image_4, null, null);
            }
            else{
                $data['image_4'] = $product->image_4;
            }
            if($request->hasFile('file')){
                $data['main_file'] = $this->uploadFile('product', $request->file, null, null);
            }
            if($request->hasFile('og_image')){
                $data['og_image'] = $this->saveImage('meta', $request->og_image, null, null);
            }
            else{
                if($product->og_image){
                    $data['og_image'] = $product->og_image;
                }
                 else{
                     $data['og_image'] = $data['thumbnail'];
                 }
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
            $this->showToastrMessage('error', $e->getMessage());
            return redirect()->back();
            // return $this->error([],  $e->getMessage());
        }

        $this->showToastrMessage('success', __("Product updated successfully"));
        return redirect()->route('lms_product.admin.product.index');
        // return $this->success([],  __("Product update successfully"));

    }


    public function approved()
    {
        if (!auth()->user()->can('product_module_product')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Approved Product');
        // $data['products'] = Product::where('status', STATUS_APPROVED)->paginate(25);
        $data['products'] = Product::where('title' ,'<>','Donation')->where('status', STATUS_APPROVED)->orderBy('id','DESC')->paginate(25);
        return view('addon.product.admin.product.approved', $data);
    }

    public function pending()
    {
        if (!auth()->user()->can('product_module_product')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Review Pending Product');
        // $data['products'] = Product::where('status', STATUS_PENDING)->paginate(25);
        $data['products'] = Product::where('title' ,'<>','Donation')->where('status', STATUS_PENDING)->orderBy('id','DESC')->paginate(25);

        return view('addon.product.admin.product.pending', $data);
    }

    public function upcoming()
    {
        if (!auth()->user()->can('product_module_product')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Upcoming Product');
        // $data['products'] = Product::where('status', STATUS_UPCOMING_REQUEST)->orWhere('status', STATUS_UPCOMING_APPROVED)->paginate(25);
        $data['products'] = Product::where('title' ,'<>','Donation')->where('status', STATUS_UPCOMING_REQUEST)->orWhere('status', STATUS_UPCOMING_APPROVED)->orderBy('id','DESC')->paginate(25);

        return view('addon.product.admin.product.upcoming', $data);
    }

    public function hold()
    {
        if (!auth()->user()->can('product_module_product')) {
            abort('403');
        } // end permission checking

        $data['title'] = __('Hold Product');
        $data['products'] = Product::where('title' ,'<>','Donation')->where('status', 3)->paginate(25);

        return view('addon.product.admin.product.hold', $data);
    }

    public function statusChange($slug, $status)
    {
        $product = $this->model->getRecordBySlug($slug);
        $product->status = $status;
        $product->save();

        $sendEmail = new EmailSendService();
        if ($status == 1) {
            $text = __("Product has been approved");
            $target_url = route('lms_product.instructor.product.my-product');
            $this->send($text, 2, $target_url, $product->user_id);
            $sendEmail->sendCommonUserAndLink($product->user, $target_url, 'product-approved-instructor');
        }

        if ($status == 3) {
            $text = __("Product has been hold");
            $target_url = route('lms_product.instructor.product.my-product');
            $this->send($text, 2, $target_url, $product->user_id);
            $sendEmail->sendCommonUserAndLink($product->user, $target_url, 'product-approved-instructor');
        }


        $this->showToastrMessage('success', __('Status has been changed'));
        return redirect()->back();

    }

    public function featureChange(Request $request)
    {
        $product = $this->model->getRecordById($request->id);
        $product->is_feature = $request->status;
        $product->save();
    }

    public function delete($slug)
    {
        $product = $this->model->getRecordBySlug($slug);
        $order_item = Order_item::whereProductId($product->id)->first();

        if ($order_item)
        {
            $this->showToastrMessage('error', __('You can not deleted. Because already student purchased this product!'));
            return redirect()->back();
        }

        $product->delete();
        $this->showToastrMessage('success', __('Product has been deleted.'));
        return redirect()->back();
    }
}
