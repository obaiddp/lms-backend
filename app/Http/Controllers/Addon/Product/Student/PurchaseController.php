<?php

namespace App\Http\Controllers\Addon\Product\Student;

use App\Http\Controllers\Controller;
use App\Models\Addon\Product\Product;
use App\Models\Addon\Product\ProductReview;
use App\Models\Order;
use App\Models\Order_item;
use App\Traits\ApiStatusTrait;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use App\Models\Notification;

class PurchaseController extends Controller
{
    use General , ImageSaveTrait, SendNotification, ApiStatusTrait;
    public function list(Request $request)
    {
        $data['pageTitle'] = __('My Purchase Product');
    
        $allUserOrder = Order::where('user_id', auth()->id());
     
        // $OrderIds = $allUserOrder->where('payment_status', 'paid')->pluck('id')->toArray();
        // $OrderIds = $allUserOrder->whereIn('payment_status', ['paid','free'])->pluck('id')->toArray();
        $OrderIds = $allUserOrder->whereIn('payment_status', ['paid','free','due'])->pluck('id')->toArray();
        $data['orderItems'] = Order_item::whereIn('order_id', $OrderIds)->whereNotNull('product_id');
        // dd( $data['orderItems']);
        if ($request->ajax()) {
            $sortByID = $request->sortByID; // 1=newest, 2=Oldest
            if ($sortByID) {
                $data['orderItems'] = Order_item::whereIn('order_id', $OrderIds)->whereNotNull('product_id');
                if ($sortByID == 1) {

                    $data['orderItems'] = $data['orderItems']->latest()->paginate();
                }

                if ($sortByID == 2) {
                    $data['orderItems'] = $data['orderItems']->paginate();
                }
            }

            return view('addon.product.partials.purchase-list', $data);
        }

        $data['orderItems'] = $data['orderItems']->latest()->paginate();
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();
        return view('addon.product.student.purchase-list', $data);
    }   


    public function reviewCreate(Request $request)
    {
        $review_exists_user = ProductReview::whereUserId(auth()->id())->whereProductId($request->product_id)->first();
        if ($review_exists_user) {
            return response()->json([
                'status' => 302,
                'msg' => 'Already you have reviewed. Thank you.'
            ]);
        }

        $request->validate([
            'product_id' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ]);

        $review = new ProductReview();
        $review->user_id = auth()->user()->id;
        $review->product_id = $request->product_id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();

        // Review Calculation and Update

        $data['five_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(5)->count();
        $data['four_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(4)->count();
        $data['three_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(3)->count();
        $data['two_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(2)->count();
        $data['first_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(1)->count();

        $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
            (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
        $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];

        if ($data['total_user_review'] > 0) {
            $average_rating = $data['total_reviews'] / $data['total_user_review'];
        } else {
            $average_rating = 0;
        }

        $product = Product::findOrFail($request->product_id);
        $product->average_review = number_format($average_rating, 1);
        $product->update();

        // End:: Review Calculation and Update

        return response()->json([
            'status' => 200,
            'msg' => 'Review Created Successfully.'
        ]);
    }
}