<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Logger;
use App\Http\Services\Payment\BasePaymentService;
use App\Models\AffiliateHistory;
use App\Models\AffiliateRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Bank;
use App\Models\BookingHistory;
use Illuminate\Support\Facades\Mail;
use App\Models\Bundle;
use App\Models\BundleCourse;
use Illuminate\Support\Facades\Log;
use App\Models\DonationUser;
use App\Models\CartManagement;
use App\Models\CartManagementWebsite;
use App\Models\City;
use App\Models\TrialClass;
use App\Models\ConsultationSlot;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\CouponCourse;
use App\Models\CouponInstructor;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\DonationOrder;
use App\Models\DonationOrder_item;
use App\Models\Addon\Product\Product;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\Withdraw;
use App\Jobs\SendDonationReceiptEmailJob;
use App\Services\DonationWebhookService;
use App\Traits\ApiStatusTrait;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class CartManagementController extends Controller
{
    use ImageSaveTrait, General, SendNotification, ApiStatusTrait;

    private $_api_context;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    private function resolveReturnBaseUrl(?string $url): ?string
    {
        if (!$url || !is_string($url)) return null;
        try {
            $parsed = parse_url(trim($url));
            $host = strtolower($parsed['host'] ?? '');
        } catch (\Throwable $e) {
            return null;
        }
        $allowed = [
            'cef.org.pk', 'www.cef.org.pk',
            'cefonlineacademy.com', 'www.cefonlineacademy.com',
            'localhost',
            '127.0.0.1',
        ];
        if (!in_array($host, $allowed, true)) return null;
        $scheme = $parsed['scheme'] ?? 'https';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        return rtrim("{$scheme}://{$host}{$port}", '/');
    }

    public function cartList()
    {
        $result_array = array();
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }

        $carts = CartManagement::whereUserId($user->id)
            ->with(['course' => function ($query) {
                $query->select('id', 'uuid', 'title', 'price', 'course_type', 'image');
            }, 'course.reviews' => function ($query) {
                $query->select('id', 'rating', 'user_id', 'course_id');
            }])
            ->get();
    
        $cartItems = [];
        $totalPrice = 0;
    
        foreach ($carts as $cart) {
            if ($cart->course) {
                $course = $cart->course;
    
                $cartItems[] = [
                    'payment_id' => $cart->id, 
                    'payment_type' => $cart->created_by_type,
                    'cart_id' => $cart->id, // Adding the cart ID
                    'course_id' => $course->id,
                    'uuid' => $course->uuid,
                    'title' => $course->title,
                    // 'price' => $course->price,
                    'price' => $cart->price,
                    'course_type' => $course->course_type,
                    'image' => asset($course->image),
                    'reviews' => $course->reviews->map(function ($review) {
                        return [
                            'review_id' => $review->id,
                            'rating' => $review->rating,
                            'user_id' => $review->user_id,
                        ];
                    }),
                ];
    
                // $totalPrice += $course->price;
                $totalPrice += $cart->price;
            }
        }
    
        $platformCharge = get_platform_charge($totalPrice);
    
        $orderSummary = [
            'total_items' => count($cartItems),
            'total_price' => $totalPrice,
            'platform_charge' => $platformCharge,
            'grand_total' => $totalPrice + $platformCharge,
        ];
    
        return $this->success([
            'courses' => $cartItems,
            'order_summary' => $orderSummary,
        ]);
    }
    

    public function applyCoupon(Request $request)
    {
        if (!Auth::check()) {
            $msg = __("You need to login first!");
            return $this->error([], $msg);
        }

        if (!$request->coupon_code) {
            $msg = __("Enter coupon code!");
            return $this->error([], $msg);
        }


        if ($request->id) {
            $cart = CartManagement::find($request->id);
            if (!$cart) {
                $msg = __("Cart item not found!");
                return $this->error([], $msg);
            }

            $coupon = Coupon::where('coupon_code_name', $request->coupon_code)->where('start_date', '<=', Carbon::now()->format('Y-m-d'))->where('end_date', '>=', Carbon::now()->format('Y-m-d'))->first();

            if ($coupon) {
                if ($cart->price < $coupon->minimum_amount) {
                    $msg = "Minimum " . get_currency_code() . $coupon->minimum_amount . " need to buy for use this coupon!";
                    return $this->error([], $msg);
                }
            }
            if (!$coupon) {
                $msg = __("Invalid coupon code!");
                return $this->error([], $msg);
            }


            if (CartManagement::whereUserId(@Auth::id())->whereCouponId($coupon->id)->count() > 0) {
                $msg = __("You've already used this coupon!");
                return $this->error([], $msg);
            }

            // Round discount to 2dp so it matches DB decimal(8,2); apply/remove stay exact inverses.
            $discount_price = round(((float) $cart->price * (float) $coupon->percentage) / 100, 2);

            if ($coupon->coupon_type == 1) {
                $cart->price = round((float) $cart->price - $discount_price, 2);
                $cart->discount = $discount_price;
                $cart->coupon_id = $coupon->id;
                $cart->save();

                $carts = CartManagement::whereUserId(@Auth::id())->get();
                $msg = __("Coupon Applied");
                $responseData['price'] = $cart->price;
                $responseData['discount'] = $cart->discount;
                $responseData['total'] = get_number_format($carts->sum('price'));
                $responseData['platform_charge'] = get_platform_charge($carts->sum('price'));
                $responseData['grand_total'] = get_number_format($carts->sum('price') + get_platform_charge($carts->sum('price')));
                return $this->success($responseData, $msg);
            } 
            elseif ($coupon->coupon_type == 2) {
                if ($cart->course) {
                    $user_id = $cart->course->user_id;
                } else if ($cart->product_id) {
                    $user_id = $cart->product->user_id;
                } else {
                    $user_id = NULL;
                }

                $couponInstructor = CouponInstructor::where('coupon_id', $coupon->id)->where('user_id', $user_id)->orderBy('id', 'desc')->first();
                if ($couponInstructor) {

                    $cart->price = round((float) $cart->price - $discount_price, 2);
                    $cart->discount = $discount_price;
                    $cart->coupon_id = $coupon->id;
                    $cart->save();

                    $carts = CartManagement::whereUserId(@Auth::id())->get();
                    $msg = __("Coupon Applied");
                    $responseData['price'] = $cart->price;
                    $responseData['discount'] = $cart->discount;
                    $responseData['total'] = get_number_format($carts->sum('price'));
                    $responseData['platform_charge'] = get_platform_charge($carts->sum('price'));
                    $responseData['grand_total'] = get_number_format($carts->sum('price') + get_platform_charge($carts->sum('price')));
                    return $this->success($responseData, $msg);
                } else {
                    $msg = __("Invalid coupon code!");
                    return $this->error([], $msg);
                }
            } 
            elseif ($coupon->coupon_type == 3) {
                $couponCourse = CouponCourse::where('coupon_id', $coupon->id)->where('course_id', $cart->course_id)->orderBy('id', 'desc')->first();
                if ($couponCourse) {

                    $cart->price = round((float) $cart->price - $discount_price, 2);
                    $cart->discount = $discount_price;
                    $cart->coupon_id = $coupon->id;
                    $cart->save();

                    $carts = CartManagement::whereUserId(@Auth::id())->get();
                    $msg = __("Coupon Applied");
                    $responseData['price'] = $cart->price;
                    $responseData['discount'] = $cart->discount;
                    $responseData['total'] = get_number_format($carts->sum('price'));
                    $responseData['platform_charge'] = get_platform_charge($carts->sum('price'));
                    $responseData['grand_total'] = get_number_format($carts->sum('price') + get_platform_charge($carts->sum('price')));
                    return $this->success($responseData, $msg);
                } else {
                    $msg = __("Invalid coupon code!");
                    return $this->error([], $msg);
                }
            } else {
                $msg = __("Invalid coupon code!");
                return $this->error([], $msg);
            }
        } else {
            $msg = __("Cart item not found!");
            return $this->error([], $msg);
        }
    }

    /**
     * Remove applied coupon from a cart row (restore price, clear coupon_id / discount).
     */
    public function removeCoupon(Request $request)
    {
        if (!Auth::check()) {
            return $this->error([], __("You need to login first!"));
        }

        if (!$request->id) {
            return $this->error([], __("Cart item not found!"));
        }

        $cart = CartManagement::find($request->id);
        if (!$cart || (int) $cart->user_id !== (int) Auth::id()) {
            return $this->error([], __("Cart item not found!"));
        }

        if (!$cart->coupon_id) {
            return $this->error([], __("No coupon applied to this item."));
        }

        $discount = round((float) ($cart->discount ?? 0), 2);
        $cart->price = round((float) $cart->price + $discount, 2);
        $cart->discount = 0;
        $cart->coupon_id = null;
        $cart->save();

        $carts = CartManagement::whereUserId(Auth::id())->get();
        $responseData = [
            'price' => $cart->price,
            'discount' => 0,
            'total' => get_number_format($carts->sum('price')),
            'platform_charge' => get_platform_charge($carts->sum('price')),
            'grand_total' => get_number_format($carts->sum('price') + get_platform_charge($carts->sum('price'))),
        ];

        return $this->success($responseData, __("Coupon removed"));
    }

    public function addToCart(Request $request)
    {
        DB::beginTransaction();
        try {

            if (!$request->course_id && !$request->product_id && !$request->bundle_id) {
                return $this->error([], __("Please provide a valid course, product, or bundle ID"), 400);
            }

            if ($request->course_id) {
                $course = Course::find($request->course_id);

                // If course doesn't exist
                if (!$course) {
                    return $this->error([], __("Course not found!"), 404);
                }
            }
            if ($request->course_id) {
                $enrollment = Enrollment::where(['course_id' => $request->course_id, 'user_id' => Auth::user()->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();

                if ($enrollment) {
                    $order = Order::find($enrollment->order_id);
                    if ($order) {
                        if ($order->payment_status == 'due') {
                            Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                                $q->delete();
                            });
                            $order->delete();
                        } elseif ($order->payment_status == 'pending') {
                            $msg = __("You've already request the course & status is pending!");
                            DB::rollBack();
                            return $this->success([], $msg);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $msg = __("You've already purchased the course!");
                            DB::rollBack();
                            return $this->success([], $msg);
                        }
                    } else {
                        $msg = __("Something is wrong! Try again.");
                        DB::rollBack();
                        return $this->failed([], $msg);
                    }
                }

                $ownCourseCheck = CourseInstructor::where('course_id', $request->course_id)->where('instructor_id', $request->user_id)->delete();

                if ($ownCourseCheck) {
                    $msg = __("This is your course. No need to add to cart.");
                    DB::rollBack();
                    return $this->success([], $msg);
                }

                $courseExits = Course::find($request->course_id);
                if (!$courseExits) {
                    $msg = __("Course not found!");
                    DB::rollBack();
                    return $this->error([], $msg, 404);
                }
            }

            if ($request->product_id) {
                $productExits = Product::find($request->product_id);
                if (!$productExits) {
                    $msg = __("Product not found!");
                    DB::rollBack();
                    return $this->error([], $msg, 404);
                }
            }

            if ($request->bundle_id) {
                $bundleExits = Bundle::find($request->bundle_id);
                if (!$bundleExits) {
                    $msg = __("Bundle not found!");
                    DB::rollBack();
                    return $this->error([], $msg, 404);
                }

                $ownBundleCheck = Bundle::whereUserId(Auth::user()->id)->where('id', $request->bundle_id)->first();
                if ($ownBundleCheck) {
                    $msg = __("This is your bundle offer. No need to add to cart.");
                    DB::rollBack();
                    return $this->success([], $msg);
                }

                $enrollment = Enrollment::where(['user_id' => Auth::user()->id, 'bundle_id' => $request->bundle_id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                if ($enrollment) {
                    $order = Order::find($enrollment->order_id);
                    if ($order) {
                        if ($order->payment_status == 'due') {
                            Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                                $q->delete();
                            });
                            $order->delete();
                        } elseif ($order->payment_status == 'pending') {
                            $msg = __("You've already request this bundle & status is pending!");
                            DB::rollBack();
                            return $this->success([], $msg);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $msg = __("You've already purchased this bundle!");
                            DB::rollBack();
                            return $this->success([], $msg);
                        }
                    } else {
                        $msg = __("Something is wrong! Try again.");
                        DB::rollBack();
                        return $this->failed([], $msg);
                    }
                }
            }

            //Start:: Cart Management
            if ($request->course_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereCourseId($request->course_id)->first();
            } elseif ($request->product_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereProductId($request->product_id)->first();
            } elseif ($request->bundle_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereBundleId($request->bundle_id)->first();
            }

            if ($cartExists) {
                $msg = __("Already added to cart!");
                DB::rollBack();
                return $this->success([], $msg);
            }

            if ($request->course_id) {
                if ($courseExits->learner_accessibility == 'free') {
                    $order = new Order();
                    $order->user_id = Auth::user()->id;
                    $order->order_number = rand(100000, 999999);
                    $order->payment_status = 'free';
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
                    $order_item->save();

                    $enrollment = Enrollment::where('user_id',auth()->id())->where('course_id',$request->course_id)->orderBy('id','DESC')->first();
                    if($enrollment){
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = auth()->id();
                        $enrollment->course_id = $request->course_id;
                        $enrollment->owner_user_id = $courseExits->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;

                        $enrollment->main_price = $order_item->unit_price;
                        $enrollment->price = $order_item->unit_price;
                        $enrollment->original_price = $order_item->original_price;
                        $enrollment->discount = $order_item->discount;
                        $enrollment->admission_fee = $order_item->admission_fee;
                        $enrollment->discount_type = $order_item->discount_type;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;
                        $enrollment->request_enrollment = $order_item->request_enrollment;

                        $enrollment->save();
                    }
                    else{
                        $enrollment = new Enrollment();
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = auth()->id();
                        $enrollment->course_id = $request->course_id;
                        $enrollment->owner_user_id = $courseExits->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;

                        $enrollment->main_price = $order_item->unit_price;
                        $enrollment->price = $order_item->unit_price;
                        $enrollment->original_price = $order_item->original_price;
                        $enrollment->discount = $order_item->discount;
                        $enrollment->admission_fee = $order_item->admission_fee;
                        $enrollment->discount_type = $order_item->discount_type;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;
                        $enrollment->request_enrollment = $order_item->request_enrollment;

                        $enrollment->save();
                         Log::info("NEW ENROLLMENT BEING CREATED IN (addToCart) IN API/STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                         Log::info("NEW ENROLLMENT DETAILS IN (addToCart) IN API/STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                    }
                    
                    $msg = __("Free Course added to your my learning list!");
                    DB::commit();
                    return $this->success([], $msg);
                }
            }

            $cart = new CartManagement();
            $cart->user_id = Auth::user()->id;
            $cart->course_id = $request->course_id;
            $cart->product_id = $request->product_id;
            $cart->bundle_id = $request->bundle_id;
            if ($request->course_id) {
                $course = Course::findOrFail($request->course_id);
                $cart->is_subscription_enable = $course->is_subscription_enable;
                $cart->main_price = $courseExits->price;
                $cart->price = $courseExits->price;
                $cart->month = date('m');
                $cart->year = date('Y');
            } else if ($request->bundle_id) {
                $bundle = Bundle::findOrFail($request->bundle_id);
                $cart->is_subscription_enable = $bundle->is_subscription_enable;
                $bundleCourses = BundleCourse::where('bundle_id', $request->bundle_id)->pluck('course_id')->toArray();
                $cart->bundle_course_ids = $bundleCourses;
                $cart->main_price = $bundleExits->price;
                $cart->price = $bundleExits->price;
            } else {
                $message = __("Please select the item to add in wishlist");
                return $this->error([], $message, 404);
            }

            $refData = $request->get('ref', '');
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

            $response['quantity'] = CartManagement::whereUserId(Auth::user()->id)->count();
            $msg = __("Added to cart");
            DB::commit();
            return $this->success([], $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = __("Something is wrong! Try again.");
            return $this->failed([], $msg);
        }
    }

    public function updateCartQuantity(Request $request)
    {
        if (!Auth::check()) {
            $response['msg'] = __("You need to login first!");
            $response['status'] = 401;
            return response()->json($response);
        }

        if ($request->quantity < 2 && $request->type == 2) {
            $response['msg'] = __("Minimum Quantity is 1");
            $response['status'] = 422;
            return response()->json($response);
        }

        $cart = CartManagement::find($request->id);


        if ($request->type == 1) {
            $product = $cart->product;

            if ($product && $product->quantity < 1) {
                $cart->delete();
                $response['msg'] = __("Product is out of stock");
                $response['status'] = 422;
                return response()->json($response);
            } elseif ($product && $product->quantity < ($cart->quantity + 1)) {
                $response['msg'] = __("Quantity is out of stock");
                $response['status'] = 422;
                return response()->json($response);
            }

            $cart->increment('quantity', 1);
        } else {
            $cart->decrement('quantity', 1);
        }

        $response['data'] = [
            'quantity' => $cart->quantity
        ];

        $response['msg'] = __("Cart Update Successfully");
        $response['status'] = 200;
        return response()->json($response);
    }

    public function cartCount(Request $request)
    {
        $totalCart = CartManagement::whereUserId(@Auth::id())->count();

        return $this->success(['total' => $totalCart]);
    }



      public function donationAddToCart(Request $request)
    {
        try {
            $messages = [
                'cart_session_id.required' => __('The Cart Session ID Field Is Required'),
                'item_name.required' => __('The Item Name Field Is Required'),
                'item_name.string' => __('The Item Name Field Must Be A String'),
                'item_price.required' => __('The Item Price Field Is Required'),
                'item_price.numeric' => __('The Item Price Field Must Be Numeric'),
                'cycle.required' => __('The Cart Cycle Field Is Required'),
                'cycle.in' => __('The Cart Cycle Field Must Be either "one_time" or "monthly" or "annually" or "daily" or "full_sponsor" or "partial_sponsor".'),
                'type.required' => __('The Cart Type Field Is Required'),
                'type.in' => __('The Cart Type Field Must Be either "general_donation" or "zakat".'),
                'sponsor_type.in' => __('The Cart Sponsor Type Field Must Be either "full_sponsor" or "partial_sponsor".'),
            ];

            $validator = Validator::make($request->all(), [
                'cart_session_id' => 'required',
                'item_name' => 'required|string',
                'item_price' => 'required|numeric',
                'cycle' => 'required|in:one_time,monthly,annually,daily,full_sponsor,partial_sponsor',
                'type' => 'required|in:general_donation,zakat',
                'sponsor_type' => 'nullable|in:full_sponsor,partial_sponsor',
            
            ],$messages);
        
            if($validator->fails()){
            
                return response(['success'=>false,'message' => $validator->errors()],302);
            }

            DB::beginTransaction();
          
            $cartSessionID = $request->cart_session_id;
            $itemName = $request->item_name;
            $itemID = $request->item_id ?? null;
            $itemPrice = $request->item_price;
            $cycle = $request->cycle;
            $type = $request->type;

            $checkExistingItem = CartManagementWebsite::where('cart_session_id',$cartSessionID)->where('item_name',$itemName)->where('main_price',$itemPrice)
            ->where('cycle',$cycle)->where('type',$type)->first();

            if($checkExistingItem){
                $checkExistingItem->quantity += 1;
                $checkExistingItem->price = $checkExistingItem->quantity * $checkExistingItem->main_price;
                $checkExistingItem->save();
                //  return response()->json($result_array);
                DB::commit();
                return response()->json(['success'=>true,'message' => 'Given Item With Other Details Already Existed In Cart. Quantity Added Successfully'], 200);
            }

            $newItem =  new CartManagementWebsite;
            $newItem->cart_session_id = $cartSessionID;
            $newItem->item_name = $itemName;
            $newItem->item_id = $itemID;
            $newItem->main_price = $itemPrice;
            $newItem->price = $itemPrice;
            $newItem->cycle = $cycle;
            $newItem->type = $type;
            $newItem->sponsor_type = $request->sponsor_type ?? null;
            $newItem->month = date('m');
            $newItem->year = date('Y');
            $newItem->save();
             DB::commit();
            return response()->json(['success'=>true,'message' => 'New Item Added To Cart Successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response(['success' => 0,'message'=>__($th->getMessage())],500);    
        }
       
    }









        public function donationDeleteCartItem(Request $request)
    {
        try {
            $messages = [
                'cart_session_id.required' => __('The Cart Session ID Field Is Required'),
                'item_name.required' => __('The Item Name Field Is Required'),
                'item_name.string' => __('The Item Name Field Must Be A String'),
                'item_price.required' => __('The Item Price Field Is Required'),
                'item_price.numeric' => __('The Item Price Field Must Be Numeric'),
                'cycle.required' => __('The Cart Cycle Field Is Required'),
                'cycle.in' => __('The Cart Cycle Field Must Be either "one_time" or "monthly" or "annually" or "daily" or "full_sponsor" or "partial_sponsor".'),
                'type.required' => __('The Cart Type Field Is Required'),
                'type.in' => __('The Cart Type Field Must Be either "general_donation" or "zakat".'),
            ];

            $validator = Validator::make($request->all(), [
                 'cart_session_id' => 'required',
                'item_name' => 'required|string',
                'item_price' => 'required|numeric',
                'cycle' => 'required|in:one_time,monthly,annually,daily,full_sponsor,partial_sponsor',
                'type' => 'required|in:general_donation,zakat',
            
            ],$messages);
        
            if($validator->fails()){
            
                return response(['success'=>false,'message' => $validator->errors()],302);
            }

            DB::beginTransaction();
          
    
            $itemName = $request->item_name;
            $itemPrice = $request->item_price;
            $cycle = $request->cycle;
            $type = $request->type;


            $checkSession =   CartManagementWebsite::where('cart_session_id',$request->cart_session_id)->first();
            if($checkSession == null){
                DB::rollBack();
                return response()->json(['success'=>false,'message' => 'Invalid Cart Session ID Does not exists'], 302);
            }



           $checkExistingItem = CartManagementWebsite::where('cart_session_id',$request->cart_session_id)->where('item_name',$itemName)->where('main_price',$itemPrice)
            ->where('cycle',$cycle)->where('type',$type)->first();

            if($checkExistingItem){
                $checkExistingItem->delete();
                DB::commit();
                return response()->json(['success'=>true,'message' => 'Cart Item Deleted Successfully'], 200);
            }
            else{
                 DB::commit();
                return response()->json(['success'=>true,'message' => 'Given Cart Item Either Does Not Exists or Already Deleted'], 200);
            }
    
        } catch (\Throwable $th) {
            DB::rollBack();
            return response(['success' => false,'message'=>__($th->getMessage())],500);    
        }
       
    }





          public function donationUpdateCart(Request $request)
    {
        try {
            $messages = [
                'cart_session_id.required' => __('The Cart Session ID Field Is Required'),
                'item_name.required' => __('The Item Name Field Is Required'),
                'item_name.string' => __('The Item Name Field Must Be A String'),
                'item_price.required' => __('The Item Price Field Is Required'),
                'item_price.numeric' => __('The Item Price Field Must Be Numeric'),
                'cycle.required' => __('The Cart Cycle Field Is Required'),
                'cycle.in' => __('The Cart Cycle Field Must Be either "one_time" or "monthly" or "annually" or "daily" or "full_sponsor" or "partial_sponsor".'),
                'type.required' => __('The Cart Type Field Is Required'),
                'type.in' => __('The Cart Type Field Must Be either "general_donation" or "zakat".'),
                 'is_increment.required' => __('The Is Increment Field Is Required'),
                'is_increment.in' => __('The Is Increment Field Must Be either "0" or "1".'),
            ];

            $validator = Validator::make($request->all(), [
                'cart_session_id' => 'required',
                'item_name' => 'required|string',
                'item_price' => 'required|numeric',
                'cycle' => 'required|in:one_time,monthly,annually,daily,full_sponsor,partial_sponsor',
                'type' => 'required|in:general_donation,zakat',
                'is_increment' => 'required|in:0,1',
            
            ],$messages);
        
            if($validator->fails()){
            
                return response(['success'=>false,'message' => $validator->errors()],302);
            }

            DB::beginTransaction();
          
    
            $itemName = $request->item_name;
            $itemPrice = $request->item_price;
            $cycle = $request->cycle;
            $type = $request->type;
            $is_increment = $request->is_increment;
            $custom_message = $request->custom_message ?? null;

            $checkSession =   CartManagementWebsite::where('cart_session_id',$request->cart_session_id)->first();
            if($checkSession == null){
                DB::rollBack();
                return response()->json(['success'=>false,'message' => 'Invalid Cart Session ID Does not exists'], 302);
            }

            $checkExistingItem = CartManagementWebsite::where('cart_session_id',$request->cart_session_id)->where('item_name',$itemName)->where('main_price',$itemPrice)
            ->where('cycle',$cycle)->where('type',$type)->first();

            if($checkExistingItem){
                $cartSessionID =  $checkExistingItem->cart_session_id;
                if($is_increment == 1){
                    $checkExistingItem->quantity += 1;
                    $checkExistingItem->price = $checkExistingItem->quantity * $checkExistingItem->main_price;
                    $checkExistingItem->save();
                }
                else{
                    if($checkExistingItem->quantity > 1){
                        $checkExistingItem->quantity -= 1;
                        $checkExistingItem->price = $checkExistingItem->quantity * $checkExistingItem->main_price;
                        $checkExistingItem->save();
                    }
                    else{
                        DB::rollBack();
                        return response()->json(['success'=>false,'message' => 'Cart Can Not Be Updated. Given Item Already Has Base Quantity. Can Not be decremented further'], 302);
                    }
                }

                if($custom_message){
                    $getRelated = CartManagementWebsite::where('cart_session_id',$cartSessionID)->get();
                    if(!$getRelated->isEmpty()){
                        foreach ($getRelated as $key => $related) {
                            $related->custom_message = $custom_message;
                            $related->save();
                        }
                    }
                }
                //  return response()->json($result_array);
                DB::commit();
                return response()->json(['success'=>true,'message' => 'Cart Updated Successfully'], 200);
            }
            else{
                 DB::rollBack();
                return response()->json(['success'=>false,'message' => 'Cart Can Not Be Updated.'], 302);
            }
    
        } catch (\Throwable $th) {
            DB::rollBack();
            return response(['success' => false,'message'=>__($th->getMessage())],500);    
        }
       
    }



         public function donationViewCart(Request $request, $cart_session_id = null)
    {
        try {
           
            $netTotal = 0;
            $customMessage = null;
            $result_array = array();
            $check = CartManagementWebsite::where('cart_session_id',$cart_session_id)->first();
            if($check == null){
                return response(['success' => false,'message'=>__('Given Cart Session ID Does not Exists. It May Be Either Deleted')]); 
            }
            $getData = CartManagementWebsite::where('cart_session_id',$cart_session_id)->get();
            if(!$getData->isEmpty()){
                foreach ($getData as $key => $data) {
                    $netTotal += $data->price;
                    $customMessage = $data->custom_message;
                    $result_array[] = [
                        'sr_no' => $key+1,
                        'cart_session_id' => $data->cart_session_id,
                        'item_name' => $data->item_name,
                        'cycle' => $data->cycle,
                        'type' => $data->type,
                        'sponsor_type' => $data->sponsor_type,
                        'main_price' => $data->main_price,
                        'quantity' => $data->quantity,
                        'price' => $data->price,
                    ];
                }
            }
         

             return response(['success' => true,'message'=>__('Cart List Retreived Successfully'),'total'=>$netTotal,'custom_message' => $customMessage ,'cart_list'=>$result_array],200);    
        } catch (\Throwable $th) {
            DB::rollBack();
            return response(['success' => false,'message'=>__($th->getMessage())],500);    
        }
       
    }





          public function donationDeleteCart(Request $request)
    {
        try {
            $messages = [
                'cart_session_id.required' => __('The Cart Session ID Field Is Required'),
                'cart_session_id.string' => __('The Cart Session ID Field Must Be A String'),
            
            ];

            $validator = Validator::make($request->all(), [
                'cart_session_id' => 'required|string',
            
            
            ],$messages);
        
            if($validator->fails()){
            
                return response(['success'=>false,'message' => $validator->errors()],302);
            }

            DB::beginTransaction();
          
    
            $cartSessionID = $request->cart_session_id;

            $checkExistingItem = CartManagementWebsite::where('cart_session_id',$cartSessionID)->first();
            if($checkExistingItem == null){
                DB::commit();
                return response()->json(['success'=>true,'message' => 'Given Cart With Given Session Either Does Not Exists or Already Deleted'], 200);
            }

            CartManagementWebsite::where('cart_session_id',$cartSessionID)->delete();
                DB::commit();
                return response()->json(['success'=>true,'message' => 'Cart Deleted Successfully'], 200);
    
        } catch (\Throwable $th) {
            DB::rollBack();
            return response(['success' => false,'message'=>__($th->getMessage())],500);    
        }
       
    }







        public function donationPayCheckoutAll(Request $request)
{

        try {
           
        
            $fromApi = 1;
            $result_array = array();
         
            $messages = [
                'cart_session_id.required' => __('The Cart Session ID Field Is Required'),
                'payment_method.required' => __('The Payment Method Is Required'),
                'payment_method.in' => __('The Payment Method Must Have Either of Following Values: "faysal_bank" or "meezan_bank"'),
                'first_name.required' =>  __('The First Name Field Is Required.'),
                'last_name.required' =>  __('The Last Name Field Is Required.'),
                'email.required' =>  __('The Email Field Is Required.'),
                'email.email' => __('Please Provide A Valid Email Address.'),
                'phone.required' =>  __('The Phone Field Is Required.'),
            ];
        
            $validator = Validator::make($request->all(), [
                'cart_session_id' => 'required',
                'payment_method' => 'required|in:faysal_bank,meezan_bank',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                
            ],$messages);
         
            if($validator->fails()){
             
                return response(['success'=>false,'message' => $validator->errors()],302);
            }
          
        $cart_session_id = $request->cart_session_id;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $phone = $request->phone;
        $company_name = $request->company_name ?? null;
        $country = $request->country ?? null;
        $address = $request->address ?? null;
        $area = $request->area ?? null;
        $city = $request->city ?? null;
        $postal_code = $request->postal_code ?? null;

        $returnBaseUrl = $this->resolveReturnBaseUrl($request->return_base_url);
        $sourceSite = $returnBaseUrl ? strtolower(parse_url($returnBaseUrl, PHP_URL_HOST)) : null;

        $checkExistingCart = CartManagementWebsite::where('cart_session_id',$cart_session_id)->first();
        if($checkExistingCart == null){
           
            return response()->json(['success'=>false,'message' => 'Given Cart With Given Session Either Does Not Exists or Already Deleted'], 302);
        }

        // Recurring/regular donations must be processed via Meezan only (plan: Meezan-only recurring flow)
        if ($request->payment_method == 'faysal_bank') {
            $cartHasRecurring = CartManagementWebsite::where('cart_session_id', $cart_session_id)
                ->whereIn('cycle', ['monthly', 'annually', 'daily'])
                ->exists();
            if ($cartHasRecurring) {
                return response()->json([
                    'success' => false,
                    'message' => __('Recurring donations must be paid via Meezan Bank. Please select Meezan Bank for monthly, annual or daily donations.'),
                ], 302);
            }
        }

        $checkDonationUser = DonationUser::where('email',$email)->first();
        if($checkDonationUser){
            $checkDonationUser->name = $first_name.' '.$last_name;
            // $checkDonationUser->email = $email;
            $checkDonationUser->phone_number = $phone;
            $checkDonationUser->company_name = $company_name;
            $checkDonationUser->country = $country;
            $checkDonationUser->address = $address;
            $checkDonationUser->area = $area;
            $checkDonationUser->city = $city;
            $checkDonationUser->postal_code = $postal_code;
            $checkDonationUser->updated_at = Carbon::now();
            $checkDonationUser->save();
        }
        else{
            $checkDonationUser = new DonationUser;
            $checkDonationUser->name = $first_name.' '.$last_name;
            $checkDonationUser->email = $email;
            $checkDonationUser->phone_number = $phone;
            $checkDonationUser->company_name = $company_name;
            $checkDonationUser->country = $country;
            $checkDonationUser->address = $address;
            $checkDonationUser->area = $area;
            $checkDonationUser->city = $city;
            $checkDonationUser->postal_code = $postal_code;
            $checkDonationUser->created_at = Carbon::now();
            $checkDonationUser->save();
        }
      
     
        $cartID = null;
        $Location = null;
        $trialID = null;
        $LiveEnrollment = null;

      
       
        // if ($request->payment_method == 'paypal') {
        //     if (empty(env('PAYPAL_CLIENT_ID')) || empty(env('PAYPAL_SECRET')) || empty(env('PAYPAL_MODE'))) {
        //         return response()->json(['message' => __('Paypal payment gateway is off!')], 302); 
        //     }
        // }
     
        // if ($request->payment_method == 'faysal_bank') {
        //     if (empty(get_option('faysal_merchant_id')) || empty(get_option('faysal_secure_key')) || empty(get_option('faysal_base_url'))
        //     || empty(get_option('faysal_access_token_url'))) {
        //         return response()->json(['message' => __('Faysal bank payment gateway is off!')], 302); 
        //     }
        // }

        // if ($request->payment_method == 'meezan_bank') {
        //     if (empty(get_option('meezan_user_name')) || empty(get_option('meezan_user_password')) || empty(get_option('meezan_port_address'))) {
        //         return response()->json(['message' => __('Meezan bank payment gateway is off!')], 302); 
        //     }
        // }
   
      
         

        if ($request->payment_method != 'faysal_bank')  {
            // $is_order_already_placed = $this->checkDonationOrderExists($request->payment_method , $cart_session_id);
            // if($is_order_already_placed){
            //     $checkORDER = DonationOrder::where('cart_session_id',$cart_session_id)->first();
            //     if($checkORDER->payment_status == 'paid'){
            //          return response()->json(['message' => __('Donation Order For Given Payment ID Already Being Paid!')], 302); 
            //     }
            //     else{
            //          $order_data = [
            //              'status' => true,
            //              'data' => $checkORDER
            //          ];
            //     }
            // }
            // else{
            //     $order_data = $this->placeDonationOrderAll($request->payment_method,$cart_session_id); 
            // } 
            $cartsData = CartManagementWebsite::where('cart_session_id',$cart_session_id)->get();
            if(!$cartsData->isEmpty()){
                foreach ($cartsData as $key => $cartData) {
                    $cartData->donation_user_id = $checkDonationUser->id;
                    $cartData->update();
                }
            }
             $order_data = $this->placeDonationOrderAll($request->payment_method,$cart_session_id, null, $sourceSite); 
            if($order_data['status']){
                $order = $order_data['data'];
            }else{
                return response()->json(['message' =>  __('Something went wrong while creating order for payment!')], 302);
            }
        }
      
     
      
        if ($request->payment_method == 'faysal_bank')  {
        
            $data['faysalBankUrl'] = 'https://ipg1.apps.net.pk/Ecommerce/api/Transaction/PostTransaction';
            $data['faysalBankMerchantID'] = 19915;
            $faysal_bank_secure_key = 'DnoMH-0Eisf2Tx-xea8sleSWx';

                $cart = CartManagementWebsite::where('cart_session_id',$cart_session_id)->first();

                $carts = CartManagementWebsite::where('cart_session_id',$cart_session_id)->get();
                $tranAmount = (int)$carts->sum('price');
               
                $basketID = 'NEW-CEF-P-'. time();
                $cartsData = CartManagementWebsite::where('cart_session_id',$cart_session_id)->get();
                if(!$cartsData->isEmpty()){
                    foreach ($cartsData as $key => $cartData) {
                        $cartData->faysal_order_id = $basketID;
                        $cartData->donation_user_id = $checkDonationUser->id;
                        $cartData->source_site = $sourceSite;
                        $cartData->update();
                    }
                }

                $courseName = 'Donation Payment';
                $userInfo = null;
           
           
                
            $Currency = get_option('faysal_currency')?get_option('faysal_currency'):'PKR';

            $faysal_bank_token = $this->getDonationFaysalBankAccessToken($data['faysalBankMerchantID'], $faysal_bank_secure_key,$tranAmount,$basketID,$Currency); 
            $data['randSignature'] =  uniqid();
          
    
            $formParametersArray = [
                'CURRENCY_CODE' => $Currency,
                'MERCHANT_ID' => $data['faysalBankMerchantID'],
                'TOKEN' => $faysal_bank_token,
            
                // 'SUCCESS_URL' => route('donation-successful-faysal-api-all',['session_id' => $cart_session_id]),
                // 'FAILURE_URL' => route('donation-failure-faysal-api-all',['session_id' => $cart_session_id]),
                // 'CANCEL_URL' => route('donation-failure-faysal-api-all',['session_id' => $cart_session_id]),

                // 'SUCCESS_URL' => "http://localhost:5173/payment-status?status=success&session_id=$cart_session_id",
                // 'FAILURE_URL' => "http://localhost:5173/payment-status?status=fail&session_id=$cart_session_id",
                // 'CANCEL_URL' => "http://localhost:5173/payment-status?status=fail&session_id=$cart_session_id",


                // 'SUCCESS_URL' => 'http://localhost:5173/payment-status/success/'.$cart_session_id,
                // 'FAILURE_URL' => 'http://localhost:5173/payment-status/fail/'.$cart_session_id,
                // 'CANCEL_URL' => 'http://localhost:5173/payment-status/fail/'.$cart_session_id,


                'SUCCESS_URL' => $returnBaseUrl
                    ? "{$returnBaseUrl}/payment-status/success/{$cart_session_id}"
                    : str_replace('{session_id}', $cart_session_id, config('services.donation_faysal.success_url')),
                'FAILURE_URL' => $returnBaseUrl
                    ? "{$returnBaseUrl}/payment-status/fail/{$cart_session_id}"
                    : str_replace('{session_id}', $cart_session_id, config('services.donation_faysal.failure_url')),
                'CANCEL_URL' => $returnBaseUrl
                    ? "{$returnBaseUrl}/payment-status/fail/{$cart_session_id}"
                    : str_replace('{session_id}', $cart_session_id, config('services.donation_faysal.failure_url')),



                // 'CHECKOUT_URL' => route('checkoutFaysalIPN',['user_id' => $user->id, 'payment_type' => $request->payment_type, 'cart_id'=>$cartID ]),
                'CHECKOUT_URL' => route('donation-checkout-faysal-ipn-api',['session_id' =>$cart_session_id ,'user_id' => $checkDonationUser->id, 'payment_type' => $request->payment_type, 'cart_id'=>$cartID, 'live_enrollment'=> 0 ]),
                'BASKET_ID' => $basketID,
                'SIGNATURE' => $data['randSignature'],
                'VERSION' => "MERCHANT-CART 0.1",
                'PROCCODE' => '00',
                'TRAN_TYPE' => 'ECOMM_PURCHASE',
                'STORE_ID' => '',
                'RECURRING_TXN' => false,
                'CUSTOMER_NAME' => $checkDonationUser ? $checkDonationUser->name : '',
                'MERCHANT_CUSTOMER_ID' => $checkDonationUser? $checkDonationUser->id : '',
                'CUSTOMER_EMAIL_ADDRESS' => $checkDonationUser? $checkDonationUser->email : '',
                'CUSTOMER_MOBILE_NO' => $checkDonationUser? $checkDonationUser->phone_number : '',
                'TXNAMT' => $tranAmount,
                'ORDER_DATE' => date('Y-m-d H:i:s', time()),
                'TXNDESC' => $courseName,
                
            ];
          
            $result_array = [
                'user_id' => $checkDonationUser->id,
                // 'cart_id' => $cartID ? (int)$cartID : null,
                'frontend_form-action_url' => $data['faysalBankUrl'],
                'form_required_parameters_values'=> $formParametersArray,
            ];
            return response()->json($result_array);
        }
        else if ($request->payment_method == 'meezan_bank')  {
            $total = $order->grand_total * (get_option('meezan_conversion_rate') ? get_option('meezan_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');

            $location = null;
            $responseData = $this->makeDonationMeezanPayment($cart_session_id,$order->donation_user_id,$total ,null ,$order->id , $cartID,$fromApi,$location,$returnBaseUrl);

           
            if(isset($responseData['errorMessage'])){
                $getOrderItemsData2 = DonationOrder_item::where('order_id',$order->id)->get();
                if(!$getOrderItemsData2->isEmpty()){
                    foreach ($getOrderItemsData2 as $key => $item) {
                        $item->delete();
                    }
                }
                $getOrderData2 = DonationOrder::where('id',$order->id)->first();
                $getOrderData2->delete();
                return response()->json(['message' =>  __($responseData['errorMessage'])], 302);
            }
            else{
                $result_array[] = [
                    // 'user_id' => $user->id,
                    // 'cart_id' => $cartID ? (int)$cartID : null,
                    'cart_session_id' => $cart_session_id,
                    'order_id' => $order->id,
                    'meezan_payment_flow_redirect_url' => $responseData['redirectURL'],
                ];
                return response()->json($result_array);
            }
            // $responseData = $getWay->makePayment($total ,null ,$order->id , $cartID);       
        }
        } catch (\Throwable $th) {
            Log::error('Donation checkout error', [
                'exception' => $th->getMessage(),
                'exception_class' => get_class($th),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'cart_session_id' => $request->input('cart_session_id'),
                'payment_method' => $request->input('payment_method'),
                'trace' => $th->getTraceAsString(),
            ]);
            $payload = [
                'success' => false,
                'message' => __('An error occurred while processing your donation. Please try again or contact support.'),
            ];
            if (config('app.debug')) {
                $payload['debug_message'] = $th->getMessage();
                $payload['debug_class'] = get_class($th);
            }
            return response($payload, 500);
        }
}






    private function makeDonationMeezanPayment($cart_session_id,$user_id,$amount,$post_data = null,$order_id = null , $cart_id = null, $fromApi = 0, $location = null, $returnBaseUrl = null)
{
        try {
         
        $meezan_port_address = 'https://acquiring.meezanbank.com/payment/rest/';
        $meezan_user_name = 'Character_api';
        $meezan_user_password = 'C987658';

        // $meezan_port_address = 'https://test-securepayment.meezanbank.com:9716/payment/rest/';
        // $meezan_user_name = 'ibft_merchant';
        // $meezan_user_password = 'mbl@1234';

        $meezan_pkr_currency_code = 586;
        $meezan_usd_currency_code = 840;

            $data['success'] = false;
            $data['redirect_url'] = '';
            $data['payment_id'] = '';
            $data['message'] = '';
            $amount         =(int)$amount; 
            $amountUpdated         =$amount * 100; 
            $orderIDupdated = rand(1,99999).$order_id;

            if ($cart_session_id) {
                $cart = CartManagementWebsite::where('cart_session_id',$cart_session_id)->first();
                $cartss = CartManagementWebsite::where('cart_session_id',$cart_session_id)->get();
                if(!$cartss->isEmpty()){
                    foreach ($cartss as $key => $cart_data) {
                        $cart_data->meezan_order_id = $orderIDupdated;
                        $cart_data->save();
                    }
                 
                }
            }

            $Order = DonationOrder::where('id',$order_id)->first();
            if($Order){
                $Order->meezan_order_id = $orderIDupdated;
                $Order->save();
            }
          
            $returnURLUpdated = '';
            if ($fromApi == 1) {
                    // $returnURLUpdated = 'https://portal.cefonlineacademy.com/billing/payment-confirmation/'.$cart_id.'/'.$order_id;
              
                    // $returnURLUpdated = 'http://localhost:5173/meezan-payment-confirmation/'.$order_id .'/'.$cart_session_id;
                    $cfgFrontend = config('services.donation_frontend_url');
                    $fallbackBase = (is_string($cfgFrontend) && $cfgFrontend !== '')
                        ? $cfgFrontend
                        : 'https://cef.org.pk';
                    $base = $returnBaseUrl ?: rtrim($fallbackBase, '/');
                    $returnURLUpdated = $base . '/meezan-payment-confirmation/'.$order_id .'/'.$cart_session_id;

            
               
            } 
            // else { 
            //     if($location == 1){
            //         if($cart_id){
            //           // Use named route with only order_id → cleaner & safer
            //             $returnURLUpdated = route('student.successfulMeezanWithCheckOut', ['order_id' => $order_id, 'cart_id' => $cart_id]);
            //         }
            //         else{
            //             // Use named route with only order_id → cleaner & safer
            //            $returnURLUpdated = route('student.successfulMeezanWithCheckOut', ['order_id' => $order_id]);
            //         }
            //     }
            //     else{
            //         if($cart_id){
            //           // Use named route with only order_id → cleaner & safer
            //             $returnURLUpdated = route('student.successfulMeezan', ['order_id' => $order_id, 'cart_id' => $cart_id]);
            //         }
            //         else{
            //             // Use named route with only order_id → cleaner & safer
            //            $returnURLUpdated = route('student.successfulMeezan', ['order_id' => $order_id]);
            //         }
            //     }
            // }

        
        
            // When order has recurring items (monthly/annually/daily), register with recurrenceType=MANUAL so Meezan returns recurrenceId for cron (recurringPayment.do)
            if ($returnURLUpdated === '') {
                return [
                    'errorMessage' => '<strong>' . __('Meezan return URL could not be built. Check donation checkout configuration.') . '</strong><br/>',
                ];
            }

            $hasRecurringItems = DonationOrder_item::where('order_id', $order_id)->whereIn('cycle', ['monthly', 'annually', 'daily'])->exists();
            $urlPostParams = array(
                'userName' => $meezan_user_name,
                'password' => $meezan_user_password,
                'orderNumber' => $orderIDupdated,
                'amount' => $amountUpdated,
                'currency' => $meezan_pkr_currency_code,
                'returnUrl' => $returnURLUpdated,
            );
            if ($hasRecurringItems) {
                $urlPostParams['recurrenceType'] = 'MANUAL';
            }

            $tokenApiUrl = $meezan_port_address.'register.do';
            $query = urldecode(http_build_query($urlPostParams));
            $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Meezan API Example'); 
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch); 
        $arrayResult = json_decode($response, true);
   
        curl_close($ch); 
    
        if (!empty($arrayResult) && is_array($arrayResult)) {
            $generatedErrorCode = array_key_exists('errorCode', $arrayResult)
                ? (int) $arrayResult['errorCode']
                : null;
            if ($generatedErrorCode !== null && $generatedErrorCode !== 0) {
                $generatedErrorMessage = $arrayResult['errorMessage'] ?? __('Unknown Meezan error');
                $data = [
                    'errorMessage' => '<strong>' . $generatedErrorMessage . '</strong><br/>',
                ];
                return $data;
            }

            $generatedOrderID = $arrayResult['orderId'] ?? null;
            $generatedFormUrl = $arrayResult['formUrl'] ?? null;
            $looksSuccessful = ($generatedErrorCode === 0)
                || ($generatedOrderID !== null && $generatedOrderID !== '' && $generatedFormUrl !== null && $generatedFormUrl !== '');
            if ($looksSuccessful) {
                if ($generatedOrderID === null || $generatedOrderID === '' || $generatedFormUrl === null || $generatedFormUrl === '') {
                    return [
                        'errorMessage' => '<strong>' . __('Meezan response missing orderId or formUrl.') . '</strong><br/>',
                    ];
                }
                $getOrderData = DonationOrder::where('id', $order_id)->first();
                if ($getOrderData === null) {
                    Log::error('makeDonationMeezanPayment: order row missing after register.do', ['order_id' => $order_id]);

                    return [
                        'errorMessage' => '<strong>' . __('Donation order not found after Meezan registration.') . '</strong><br/>',
                    ];
                }
                $getOrderData->meezan_transaction_id = $generatedOrderID;
                // Recurring: register.do may return recurrenceId or recurrentPaymentId
                if (!empty($arrayResult['recurrenceId']) || !empty($arrayResult['recurrentPaymentId'])) {
                    $getOrderData->meezan_recurrence_id = $arrayResult['recurrenceId'] ?? $arrayResult['recurrentPaymentId'];
                }
                $getOrderData->update();

                $data = [
                    'redirectURL' => $generatedFormUrl,
                ];
                return $data;

                // return $data['redirectURL'] = $generatedFormUrl;
                // header("Location: $generatedFormUrl");
                // exit();
            }
        }

        $data = [
            'errorMessage' => '<strong>' . __('Something went wrong while using Meezan credentials or network.') . '</strong><br/>',
        ];
        return $data;
       
          
    } catch (\Throwable $ex) {
            Log::warning('makeDonationMeezanPayment exception', [
                'message' => $ex->getMessage(),
                'class' => get_class($ex),
                'order_id' => $order_id,
            ]);
            $data = [
                'errorMessage' => '<strong>' . $ex->getMessage() . '</strong><br/>',
            ];
            return $data;
          
    }
}





    private function checkDonationOrderExists($payment_method , $cart_session_id = null)
    {
        $check = DonationOrder::where('cart_session_id',$cart_session_id)->first();
        if($check){
            return true;
        }
        else{
            return false;
        }
    }



    public function donationSuccessfulFaysalApiAll(Request $request,$cart_session_id){
    
        $cart_id = null;
        $result_array = array();
      

        // $checkUser = User::where('id',$user_id)->first();
        // if($checkUser == null){
        //     $result_array = [
        //                 'status' => false,
        //                 'message' => 'Given User ID Does not exists in records',
        //             ];
        //     return response()->json($result_array,302);
        // }
    
        $errCode = $request->err_code;
		$errMsg = $request->err_msg;
		$paymentName = $request->PaymentName;
		$trans_id = $request->transaction_id;
		$order_date = $request->order_date;
		$response_key = $request->Response_Key; 
		$secretword = '';
		$original_basket_id = $request->basket_id; 
		$txt_amount = $request->transaction_amount; 
		$cart_id = $cart_id;
   
        $checkExistingCart = CartManagementWebsite::where('cart_session_id',$cart_session_id)->first();
        if($checkExistingCart == null){
           
            return response()->json(['success'=>false,'message' => 'Given Cart With Given Session Either Does Not Exists or Already Deleted'], 302);
        }

        if ($errCode == '000' || $errCode == '00') { 
                $getCartData = CartManagementWebsite::where('cart_session_id',$cart_session_id)->get();    
                $subTotal = $getCartData->sum('price');
                $month = null;
                $year = null;
                $getCartData = CartManagementWebsite::where('cart_session_id',$cart_session_id)->first();
                $student_user_id = $getCartData->donation_user_id;  
                $faysalOrderID =  $getCartData->faysal_order_id;
       
            $getCheck = DonationOrder::where('faysal_transaction_id',$trans_id)->first();
            $grandTotal = $subTotal;
            $getStudentData = DonationUser::where('id', $student_user_id)->first();
            if($getCheck == null){
                    $carts = CartManagementWebsite::where('cart_session_id',$cart_session_id)->get();
                
                $getPaymentData = new DonationOrder;
                $successMsg = "Donation Amount was deposited successfully at pay fast.";
                $getPaymentData->donation_user_id = $student_user_id;
                $getPaymentData->cart_session_id = $cart_session_id;
                // $getPaymentData->cart_id = $cart_id;
                $getPaymentData->order_number = rand(100000,999999);
                $getPaymentData->sub_total = $subTotal;
                $getPaymentData->grand_total = $grandTotal;
                // $getPaymentData->current_currency = "PKR";
                // $getPaymentData->payment_currency = "PKR";
                $getPaymentData->payment_method = "faysal_bank";
                $getPaymentData->source_site = $carts->first()?->source_site;
                $getPaymentData->faysal_transaction_id = $trans_id;
                $getPaymentData->faysal_order_id = $faysalOrderID;
                $getPaymentData->payment_id = $trans_id;
                $getPaymentData->faysal_payment_source = $paymentName;
                $getPaymentData->faysal_payment_date = date('y-m-d h:i:sa');
                    // $getPaymentData->cart_type = 1;
                    // $getPaymentData->created_by_type = 1;   
                // $getPaymentData->month = $month;
                // $getPaymentData->year= $year;
                $getPaymentData->payment_status = 'paid';
                $getPaymentData->delivery_status = 1;
                $getPaymentData->created_by = $student_user_id;
                $payment_currency = get_option('faysal_currency');
                $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;
    
                // $getPaymentData->conversion_rate = $conversion_rate;
                if ($conversion_rate) {
                    // $getPaymentData->grand_total_with_conversation_rate = ($getPaymentData->sub_total + $getPaymentData->platform_charge + $getPaymentData->shipping_cost) * $conversion_rate;
                }
                $getPaymentData->save();
    
                
                foreach ($carts as $cart) {
                        $order_item = new DonationOrder_item();
                        $order_item->order_id = $getPaymentData->id;
                        $order_item->cart_session_id = $cart->cart_session_id;
                        $order_item->donation_user_id = $cart->donation_user_id;
                        $order_item->item_id = $cart->item_id;
                        $order_item->item_name = $cart->item_name;
                        $order_item->month = $cart->month;
                        $order_item->year = $cart->year;
                        $order_item->cart_id = $cart->id;
                        $order_item->unit_price = $cart->price;

                        $order_item->original_price = $cart->main_price;
                        $order_item->quantity = $cart->quantity;
                        $order_item->cycle = $cart->cycle;
                        $order_item->type = $cart->type;
                        $order_item->sponsor_type = $cart->sponsor_type;
                        $order_item->custom_message = $cart->custom_message;
                        $order_item->error_trans_response = $cart->error_trans_response;

                        // $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                        // $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                        // if ($adminCommission) {
                        //     $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                        //     $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                        //     $order_item->sell_commission = $adminCommission;
                        // } else {
                        //     $order_item->owner_balance = $cart->price;
                        // }
                    
                        $order_item->save();
                }
                  
                        // $CART =CartManagementWebsite::where('cart_session_id',$cart_session_id)->first();
                        // if($CART){
                        //     $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                        // }
                        // else{
                        //     // $email_sent = $this->sendPaymentEmail($getPaymentData->id);
                        //     $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                        // }            
                 
                    // $target_url = route('student_payment.completed');
                    // $text = __("Student payment has been accepted by faysal bank.");
                    // $this->send($text, 1, $target_url, null);

                // $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");

                // Persist Faysal callback params for resend/preview webhooks; bank_response uses same snapshot.
                $faysalGatewayPayload = $request->all();
                $getPaymentData->faysal_ipn_payload = json_encode($faysalGatewayPayload);
                $getPaymentData->save();

                // Donation webhook: one-time (Faysal does not create recurring schedules in this flow)
                try {
                    $getPaymentData->load(['donation_user', 'donation_items']);
                    app(DonationWebhookService::class)->sendDonationCreatedSingle($getPaymentData, [
                        'payment_status' => 'Paid',
                        'bank_response' => $faysalGatewayPayload,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('[donationSuccessfulFaysalApiAll] Webhook send failed', ['order_id' => $getPaymentData->id, 'error' => $e->getMessage()]);
                }

                if ($getPaymentData->donation_user && !empty(trim($getPaymentData->donation_user->email ?? ''))) {
                    try {
                        SendDonationReceiptEmailJob::dispatch($getPaymentData->id, 'automatic');
                    } catch (\Throwable $e) {
                        Log::warning('[donationSuccessfulFaysalApiAll] Receipt email job dispatch failed', ['order_id' => $getPaymentData->id, 'error' => $e->getMessage()]);
                    }
                }

                CartManagementWebsite::where('cart_session_id',$cart_session_id)->delete();

                  $successMsg = $successMsg;
                 $result_array = [
                        'status' => true,
                        'message' => $successMsg,
                        'data' => $getPaymentData
                    ];
                return response()->json($result_array);
              
            }
        }
        else{
          
                $errorMsg = "Transaction Failed. Message: " . $errMsg;
                 $result_array = [
                        'status' => false,
                        'message' => $errorMsg,
                        'data' => []
                    ];
            return response()->json($result_array);
		}

    }




    public function donationFailureFaysalApiAll(Request $request,$cart_session_id){
        $result_array = array();
        $errCode = $request->err_code;
		$errMsg = $request->err_msg;
        // if(isset($errCode) && isset($errMsg)){
		if(isset($errCode)){
			if ($errCode == '001') { 
                $errMsg2 = "Transaction Failed. Message: " . $errMsg;
                $result_array = [
                        'status' => false,
                        'errCode' => $errCode,
                        'errMsg' => $errMsg2,
                ];
                return response()->json($result_array,500);
			}
            else if($errCode == '90'){
                $errMsg2 = "Transaction Failed. Message: " . $errMsg;
                $result_array = [
                        'status' => false,
                        'errCode' => $errCode,
                        'errMsg' => $errMsg2,
                ];
                return response()->json($result_array,500);
            }
            else{
                 $result_array = [
                        'status' => false,
                        'message' => __('Invalid Error Code'),
                ];
                return response()->json($result_array,500);
            }
	    }
        else{
                $result_array = [
                        'status' => false,
                        'message' => __('No Error Code in Url. ErrCode'),
                ];
                return response()->json($result_array,302);
        }
    }







         private function placeDonationOrderAll($payment_method ,$cart_session_id = null, $cartID = null, $sourceSite = null)
    {
     
        DB::beginTransaction();
        try{
            $carts = CartManagementWebsite::where('cart_session_id',$cart_session_id)->get();
            $cart = null;
            $getCartData = CartManagementWebsite::where('cart_session_id',$cart_session_id)->first();
            if ($getCartData === null) {
                DB::rollBack();
                Log::warning('placeDonationOrderAll: no cart rows for session', ['cart_session_id' => $cart_session_id]);
                return ['status' => false, 'data' => null];
            }
            $student_user_id = $getCartData->donation_user_id;
         
          
            $order = new DonationOrder();
            $order->donation_user_id = $student_user_id;
            $order->cart_session_id = $cart_session_id;
            $order->order_number = rand(100000, 999999);
            $order->sub_total = $carts->sum('price');
            $order->grand_total = $carts->sum('price');
            $order->payment_method = $payment_method;
            $order->source_site = $sourceSite;
            $order->created_by = $student_user_id;
    
            $order->save();
     
            
            foreach ($carts as $cart) {
                $order_item = new DonationOrder_item();
                $order_item->order_id = $order->id;
                $order_item->cart_session_id = $cart->cart_session_id;
                $order_item->donation_user_id = $cart->donation_user_id;
                $order_item->item_id = $cart->item_id;
                $order_item->item_name = $cart->item_name;
                $order_item->month = $cart->month;
                $order_item->year = $cart->year;
                $order_item->cart_id = $cart->id;
                $order_item->unit_price = $cart->price;

                $order_item->original_price = $cart->main_price;
                $order_item->quantity = $cart->quantity;
                $order_item->cycle = $cart->cycle;
                $order_item->type = $cart->type;
                $order_item->sponsor_type = $cart->sponsor_type;
                $order_item->custom_message = $cart->custom_message;
                $order_item->error_trans_response = $cart->error_trans_response;
                $order_item->save();
            }
         
            DB::commit();
            return ['status' => true,'data' => $order];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('placeDonationOrderAll failed', [
                'cart_session_id' => $cart_session_id,
                'message' => $e->getMessage(),
                'class' => get_class($e),
            ]);
            return ['status' => false, 'data' => null];
        }

    }



    public function getGroupedDonations()
    {
        try {
            // Fetch orders with relations, ordered by session and email to match your sequence
            $orders = DonationOrder::with(['donation_user', 'donation_items'])
                ->join('donation_users', 'donation_orders.donation_user_id', '=', 'donation_users.id')
                ->orderBy('donation_orders.cart_session_id', 'ASC')
                ->orderBy('donation_users.email', 'ASC')
                ->select('donation_orders.*') // Ensure we get order columns
                ->get();

            $formattedOrders = $orders->map(function ($order) {
                if($order->payment_method == 'meezan_bank'){
                    $orderPaymentMethod = 'Meezan Bank';
                    if($order->meezan_payment_date != null){
                        $orderPaymentDate = date('d-m-Y h:i A',strtotime($order->meezan_payment_date));
                    }
                    else{
                        $orderPaymentDate = date('d-m-Y h:i A',strtotime($order->updated_at));
                    }
                }
                else if($order->payment_method == 'faysal_bank'){
                    $orderPaymentMethod = 'PayFast';
                    if($order->faysal_payment_date != null){
                        $orderPaymentDate = date('d-m-Y h:i A',strtotime($order->faysal_payment_date));
                    }
                    else{
                        $orderPaymentDate = date('d-m-Y h:i A',strtotime($order->updated_at));
                    }
                }
                else if($order->payment_method == 'Manual'){
                    $orderPaymentMethod = 'Manual';
                    $orderPaymentDate = date('d-m-Y h:i A',strtotime($order->updated_at));
                }
                else {
                    $orderPaymentMethod = 'N/A';
                    $orderPaymentDate = date('d-m-Y h:i A',strtotime($order->updated_at));
                }



                if($order->payment_status == 'paid'){
                    $orderPaymentStatus = 'Paid';
                }
                else if($order->payment_status == 'due'){
                    $orderPaymentStatus = 'Due';
                }
                else {
                    $orderPaymentStatus = 'N/A';
                }


                return [
                    // donation_orders selective columns
                    'id'              => $order->id,
                    'sr_no'           => $order->id,
                    'cart_session_id' => $order->cart_session_id,
                    'user_name'       => $order->donation_user?->name ?? 'N/A',
                    'user_email'      => $order->donation_user?->email ?? 'N/A',
                    'grand_total'     => number_format($order->grand_total, 2, '.', ''),
                    'currency'        => $order->payment_currency ?: 'PKR',
                    'payment_method'  => $orderPaymentMethod,
                    'payment_status'  => $orderPaymentStatus,
                    'payment_date'    => $orderPaymentDate,
                    'source_site'     => $order->source_site ?? 'cef.org.pk',

                    // donation_order_items nested inside
                    'donation_order_items' => $order->donation_items->map(function ($item, $index) {
                        // if($item->month){
                        //     $itemMonth = $item->month;
                        // }
                        // else{
                        //     $itemMonth = 'N/A';
                        // }

                        // if($item->year){
                        //     $itemYear = $item->year;
                        // }
                        // else{
                        //     $itemYear = 'N/A';
                        // }

                        if($item->cycle == 'one_time'){
                            $itemCycle = 'One Time';
                        }
                        else if($item->cycle == 'daily'){
                            $itemCycle = 'Daily';
                        }
                        else if($item->cycle == 'monthly'){
                            $itemCycle = 'Monthly';
                        }
                        else if($item->cycle == 'annually'){
                            $itemCycle = 'Annually';
                        }
                        else if($item->cycle == 'full_sponsor'){
                            $itemCycle = 'Full Sponsor';
                        }
                        else if($item->cycle == 'partial_sponsor'){
                            $itemCycle = 'Partial Sponsor';
                        }
                        else{
                            $itemCycle = 'N/A';
                        }



                        if($item->type == 'general_donation'){
                            $itemType = 'General Donation';
                        }
                        else if($item->type == 'zakat'){
                            $itemType = 'Zakat';
                        }
                        else{
                            $itemType = 'N/A';
                        }


                        if($item->sponsor_type == 'full_sponsor'){
                            $itemSponsorType = 'Full Sponsor';
                        }
                        else if($item->sponsor_type == 'partial_sponsor'){
                            $itemSponsorType = 'Partial Sponsor';
                        }
                        else{
                            $itemSponsorType = 'N/A';
                        }

                        return [
                            'sr_no'          => $index + 1,
                            'item_id'        => $item->item_id,
                            'item_name'      => $item->item_name,
                            'original_price' => number_format($item->original_price, 2, '.', ''),
                            'quantity'       => $item->quantity,
                            'final_price'    => number_format($item->unit_price, 2, '.', ''),
                            // 'month'          => $itemMonth,
                            // 'year'           => $itemYear,
                            'cycle'          => $itemCycle,
                            'type'           => $itemType,
                            'sponsor_type'           => $itemSponsorType,
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => 'true',
                'message' => 'Donations Data Retrieved Successfully',
                'data' => [
                    'donation_orders' => $formattedOrders
                ]
            ], 200);

            // return response()->json([
            //     'status' => 'true',
            //     'message' => 'Donations Data Retrieved Successfully',
            //     'data' => $groupedData
            // ], 200);

         
        } 
        catch (\Exception $e) {
            return response()->json([
                'success' => 'false',
                'message' => $e->getMessage()
            ], 500);
        }
       
    }







      public function getPendingCartsData()
    {
        try {
            // 1. Fetch all records from the table
            $cartItems = CartManagementWebsite::all();

            // 2. Group by cart_session_id and transform the structure
            $groupedData = $cartItems->groupBy('cart_session_id')
                ->values() // Reset keys after grouping to ensure an indexed array
                ->map(function ($items, $index) {
                    
                    // The first item in the group provides the session-level info
                    $firstItem = $items->first();

                    return [
                        // Outer Array Columns
                        'sr_no'                  => $index + 1,
                        'cart_session_id'        => $firstItem->cart_session_id,
                        'total_items'            => $items->count(),
                        // 'total_main_price'       => number_format($items->sum('main_price'), 2, '.', ''),
                        'total_quantities'       => $items->sum('quantity'),
                        'total_price'      => number_format($items->sum('price'), 2, '.', ''),
                        'cart_update_date_time'  => date('d-m-Y h:i A' ,strtotime($firstItem->updated_at)),

                        // Inner Array (The items within this session)
                        'cart_items' => $items->map(function ($item, $innerIndex) {
                            if($item->cycle == 'one_time'){
                                $itemCycle = 'One Time';
                            }
                            else if($item->cycle == 'monthly'){
                                $itemCycle = 'Monthly';
                            }
                            else if($item->cycle == 'annually'){
                                $itemCycle = 'Annually';
                            }
                            else if($item->cycle == 'full_sponsor'){
                                $itemCycle = 'Full Sponsor';
                            }
                            else if($item->cycle == 'partial_sponsor'){
                                $itemCycle = 'Partial Sponsor';
                            }
                            else{
                                $itemCycle = 'N/A';
                            }



                            if($item->type == 'general_donation'){
                                $itemType = 'General Donation';
                            }
                            else if($item->type == 'zakat'){
                                $itemType = 'Zakat';
                            }
                            else{
                                $itemType = 'N/A';
                            }


                            if($item->sponsor_type == 'full_sponsor'){
                                $itemSponsorType = 'Full Sponsor';
                            }
                            else if($item->sponsor_type == 'partial_sponsor'){
                                $itemSponsorType = 'Partial Sponsor';
                            }
                            else{
                                $itemSponsorType = 'N/A';
                            }

                            return [
                                'sr_no'       => $innerIndex + 1,
                                'item_id'     => $item->item_id,
                                'item_name'   => $item->item_name,
                                'main_price'  => number_format($item->main_price, 2, '.', ''),
                                'quantity'    => $item->quantity,
                                'final_price' => number_format($item->price, 2, '.', ''),
                                'cycle'       => $itemCycle,
                                'type'        => $itemType,
                                'sponsor_type'        => $itemSponsorType,
                            ];
                        })->values()
                    ];
                });

            return response()->json([
                'success' => 'true',
                'message' => 'Pending Carts Data Retrieved Successfully',
                'data'   => $groupedData
            ], 200);

            // return response()->json([
            //     'status' => 'true',
            //     'message' => 'Donations Data Retrieved Successfully',
            //     'data' => $groupedData
            // ], 200);

         
        } 
        catch (\Exception $e) {
            return response()->json([
                'success' => 'false',
                'message' => $e->getMessage()
            ], 500);
        }
       
    }


    
    public function addToCartConsultation(Request $request)
    {

        if ($request->consultation_slot_id) {
            DB::beginTransaction();
            try {
                $consultationExit = ConsultationSlot::whereId($request->consultation_slot_id)->whereUserId($request->booking_instructor_user_id)->first();

                if (!$consultationExit) {
                    DB::rollBack();
                    $msg = __("Time slot not found!");
                    return $this->error([], $msg, 404);
                }

                $ownConsultationSlotCheck = ConsultationSlot::whereId($request->consultation_slot_id)->whereUserId(Auth::id())->first();
                if ($ownConsultationSlotCheck) {
                    DB::rollBack();
                    $msg = __("This is your consultation slot. No need to add.");
                    return $this->success([], $msg);
                }

                $consultationOrderBooked = Order_item::whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
                if ($consultationOrderBooked) {
                    $order = Order::find($consultationOrderBooked->order_id);
                    if ($order) {
                        if ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            DB::rollBack();
                            $msg = __("This slot already purchased. Please try another slot.");
                            return $this->success([], $msg);
                        }
                    } else {
                        DB::rollBack();
                        $msg = __("Something is wrong! Try again.");
                        return $this->failed([], $msg);
                    }
                }

                $consultationOrderExit = Order_item::whereUserId(Auth::user()->id)->whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
                if ($consultationOrderExit) {
                    $order = Order::find($consultationOrderExit->order_id);
                    if ($order) {
                        if ($order->payment_status == 'due') {
                            Order_item::whereOrderId($consultationOrderExit->order_id)->get()->map(function ($q) {
                                $q->delete();
                            });
                            $order->delete();
                        } elseif ($order->payment_status == 'pending') {
                            DB::rollBack();
                            $msg = __("You've already request this slot & status is pending!");
                            return $this->success([], $msg);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            DB::rollBack();
                            $msg = __("You've already purchased this slot!");
                            return $this->success([], $msg);
                        }
                    } else {
                        DB::rollBack();
                        $msg = __("Something is wrong! Try again.");
                        return $this->success([], $msg);
                    }
                }

                $consultationOrderExit = Order_item::whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
                if ($consultationOrderExit) {
                    $order = Order::find($consultationOrderExit->order_id);
                    if ($order) {
                        if ($order->payment_status == 'pending') {
                            DB::rollBack();
                            $msg = __("Another User already request this slot!");
                            return $this->failed([], $msg);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            DB::rollBack();
                            $msg = __("Another User already purchased this slot!");
                            return $this->failed([], $msg);
                        }
                    } else {
                        DB::rollBack();
                        $msg = __("Something is wrong! Try again.");
                        return $this->failed([], $msg);
                    }
                }


                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
                if ($cartExists) {
                    DB::rollBack();
                    $msg = __("Already added to cart!");
                    return $this->failed([], $msg);
                }

                $cart = new CartManagement();
                $cart->user_id = Auth::user()->id;
                $cart->consultation_slot_id = $request->consultation_slot_id;
                $consultationArray = array();
                $newConsultationDataArray = [
                    'instructor_user_id' => $consultationExit->user_id,
                    'student_user_id' => Auth::id(),
                    'consultation_slot_id' => $consultationExit->id,
                    'date' => $request->bookingDate,
                    'day' => $consultationExit->day,
                    'time' => $consultationExit->time,
                    'duration' => $consultationExit->duration,
                    'status' => 0,
                ];

                $consultationArray[] = $newConsultationDataArray;

                $cart->consultation_details = $consultationArray;
                $cart->consultation_date = $request->bookingDate;
                $cart->consultation_available_type = $request->available_type;

                $consultationUser = User::whereId($request->booking_instructor_user_id)->first();
                $relation = getUserRoleRelation($consultationUser);
                $cart->is_subscription_enable = $consultationUser->$relation->is_subscription_enable;

                /*
                * Price Calculation
                */
                $hour_duration = $consultationExit->hour_duration;
                $minute_duration = $consultationExit->minute_duration;
                // User role check
                if ($consultationExit->user->role == USER_ROLE_INSTRUCTOR) {
                    $relation = 'instructor';
                } elseif ($consultationExit->user->role == USER_ROLE_ORGANIZATION) {
                    $relation = 'organization';
                }
                $hourly_rate = @$consultationExit->user->$relation->hourly_rate;
                $minuteCost = 0;
                if ($minute_duration > 0) {
                    $minuteCost = ($hourly_rate / (60 / $minute_duration));
                }
                $totalCost = ($hour_duration * $hourly_rate) + $minuteCost;

                $cart->main_price = $totalCost;
                $cart->price = $totalCost;
                $cart->month = date('m');
                $cart->year = date('Y');
                $cart->save();

                DB::commit();
                $msg = __("Consultation added to cart");
                return $this->success([], $msg);
            } catch (\Exception $e) {
                DB::rollBack();
                $msg = __("Something is wrong! Try again.");
                return $this->failed([], $msg);
            }
        } else {
            $msg = __("Time slot not found!");
            return $this->failed([], $msg);
        }
    }

    public function cartDelete($id)
    {
        $cart = CartManagement::find($id);
        if (is_null($cart)) {
            return $this->error([], __('Data Not found'));
        }
        $cart->delete();
        return $this->success([], __('Removed from cart list!'));
    }

    public function fetchBank(Request $request)
    {
        $bank_id = Bank::find($request->bank_id);
        if ($bank_id) {
            return response()->json([
                'account_number' => $bank_id->account_number,
            ]);
        }
    }

    // public function checkout($payment_type = null, $payment_id = null)
    // {
    //     $result_array = array();
    //     $user = Auth::user();
    //     if($user){
    //         $role = $user->role;
    //         if($role != 3){ // Student
    //             return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
    //         }
    //     }
    //     else{
    //         return response()->json(['message' => 'User Is Not Logged In'], 200);
    //     }


    //     $isPaymentExists = CartManagement::where('id',$payment_id)->first();
    //     if($isPaymentExists == null){
    //         return response()->json(['message' => 'Given Payment ID Does Not Exists'], 302); 
    //     }
    //     if($isPaymentExists->created_by_type != $payment_type){
    //         return response()->json(['message' => 'Invalid Payment Type For Current Given Payment'], 302); 
    //     }



    //     $cartID = $payment_id;
    //     $type = $payment_type;


    //     $data['createdByType'] = $type;
    //     $data['cartID'] = $cartID;
    //     $data['pageTitle'] = "Checkout";
    //     $cart = CartManagement::where('id',$cartID)->first();
    //     $courseName = $cart->course->title;
    //     $data['courseName'] = $courseName;
    //     $data['userInfo'] =  $cart->user;
    


    //     if($data['cartID'] == null){
    //             $data['carts'] = CartManagement::whereUserId($user->id)
    //             ->where('created_by_type',$type)->get();
    //     }
    //     else{
    //         $data['carts'] = CartManagement::whereUserId($user->id)
    //         ->where('id',$data['cartID'])
    //         ->where('created_by_type',$type)->get();
    //     }



    //     $data['student'] = Auth::user()->student;
    //     $data['countries'] = Country::orderBy('country_name', 'asc')->get();
    //     $data['banks'] = Bank::orderBy('name', 'asc')->where('status', 1)->get();

    //     if($data['cartID'] == null){
    //         $cart = CartManagement::whereUserId($user->id)
    //         ->where('created_by_type',$type)->first();
    //         $data['paymentCartID'] = $cart->id;
    //     }
    //     else{
    //         $cart = CartManagement::where('id',$data['cartID'])
    //         ->where('created_by_type',$type)->first();
    //         $data['paymentCartID'] = $data['cartID']; 
    //     }


    //     if (old('country_id')) {
    //             $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
    //     }

    //     if (old('state_id')) {
    //         $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
    //     }
    


    //       $razorpay_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('razorpay_conversion_rate') ? get_option('razorpay_conversion_rate') : 0);
    //         $data['razorpay_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($razorpay_grand_total_with_conversion_rate, 2));
    
    //         $paystack_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0);
    //         $data['paystack_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($paystack_grand_total_with_conversion_rate, 2));
    
    //         $sslcommerz_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0);
    //         $data['sslcommerz_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($sslcommerz_grand_total_with_conversion_rate, 2));
         
    //         if(empty(get_option('faysal_base_url'))){
    //             $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
    //         }
    //         else{
    //             // $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
    //             $data['faysalBankUrl']= get_option('faysal_base_url');
    //         }
    
    //         if(empty(get_option('faysal_merchant_id'))){
    //             $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
    //         }
    //         else{
    //             // $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
    //             $data['faysalBankMerchantID']= get_option('faysal_merchant_id'); 
    //         }
    
    //         if(empty(get_option('faysal_secure_key'))){
    //             $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
    //         }
    //         else{
    //             // $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
    //             $faysal_bank_secure_key= get_option('faysal_secure_key'); 
    //         }
    
    //         $basketID = (int)$cart->id.time();
    //         $tranAmount = (int)$cart->price;
    //         $Currency = get_option('faysal_currency')?get_option('faysal_currency'):'PKR';

    //         $faysal_bank_token = $this->getFaysalBankAccessToken($data['faysalBankMerchantID'], $faysal_bank_secure_key,$tranAmount,$basketID,$Currency); 
         
    //         $data['faysalBankToken'] = $faysal_bank_token;
    //         $data['randSignature'] =  uniqid();
    //         $data['generalUrl']= route('student.pay');
          
    //         $billing_address_section = [
    //             'student_first_name' => $data['student']->first_name,
    //             'student_last_name' => $data['student']->last_name,
    //             'student_email' => $data['student']->user?->email,
    //             'student_address' => $data['student']->address,
    //             'student_country_id' => $data['student']->country_id,
    //             'student_country_name' => $data['student']->country?->country_name,
    //             // 'student_state_id' => $data['student']->state_id,
    //             // 'student_state_name' => $data['student']->state?->name,
    //             // 'student_city_id' => $data['student']->city_id,
    //             // 'student_city_name' => $data['student']->city?->name,
    //             'student_zip_code' => $data['student']->postal_code,
    //             'student_phone_number' => $data['student']->phone_number,
    //         ];



    //         $item_details_array = array();
    //         foreach ($data['carts'] as $key => $cart) {
    //             if (get_currency_placement() == 'after'){
    //                 $price = get_number_format($cart->price, 2).' '.get_currency_symbol();
    //             }
    //             else{
    //                 $price = get_currency_symbol().' '.get_number_format($cart->price, 2);
    //             }

    //             $item_details_array[] = [
    //                 'course_id' => $cart->course?->id,
    //                 'course_image' => getImageFile($cart->course?->image_path),
    //                 'course_title' => $cart->course?->title,
    //                 'course_detail_web_route' => route('course-details', $cart->course?->slug),
    //                 'course_detail_api_route' => route('my-course-show-student', $cart->course?->slug),
    //                 'course_price' => $price
    //             ];
    //         }


    //     $order_review_section = [
    //             'total_items_in_cart' => $data['carts']->count(),
    //             'items_details' => $item_details_array,
    //         ];
    //         if (get_currency_placement() == 'after'){
    //             $subTotal = get_number_format($data['carts']->sum('price')).' '.get_currency_symbol();
    //         }
    //         else{
    //             $subTotal = get_currency_symbol().' '.get_number_format($data['carts']->sum('price'));
    //         }
        
    //         if (get_currency_placement() == 'after'){
    //             $discount = '-'.get_number_format($data['carts']->sum('discount')).' '.get_currency_symbol();
    //         }
    //         else{
    //             $discount = '-'.get_currency_symbol().' '.get_number_format($data['carts']->sum('discount'));
    //         }

    //         if (get_currency_placement() == 'after'){
    //             $grandTotal = get_number_format($data['carts']->sum('price')).' '.get_currency_symbol();
    //         }
    //         else{
    //             $grandTotal = get_currency_symbol().' '.get_number_format($data['carts']->sum('price'));
    //         }

    //         $billing_summary_section = [
    //             'sub_total' => $subTotal,
    //             'discount' => $discount,
    //             'grand_total' => $grandTotal,
    //             'conversion_rate' => '1 ' .get_currency_code(). ' = ?',
    //         ];
    //         if (get_option('cash_on_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('cash_on'),
    //                 'conversion_rate'=> null,
    //                 'currency'=> null,
    //             ];
    //         }
           
    //         if (get_option('faysal_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('faysal_bank'),
    //                 'conversion_rate'=> get_option('faysal_conversion_rate'),
    //                 'currency'=> get_option('faysal_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('faysal_conversion_rate'),
    //             ];
    //         }
    //         if (get_option('meezan_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('meezan_bank'),
    //                 'conversion_rate'=> get_option('meezan_conversion_rate'),
    //                 'currency'=> get_option('meezan_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('meezan_conversion_rate'),
    //             ];
    //         }
    //         if (get_option('paypal_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('paypal'),
    //                 'conversion_rate'=> get_option('paypal_conversion_rate'),
    //                 'currency'=> get_option('paypal_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('paypal_conversion_rate'),
    //             ];
    //         }
    //         if (get_option('stripe_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('stripe'),
    //                 'conversion_rate'=> get_option('stripe_conversion_rate'),
    //                 'currency'=> get_option('stripe_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('stripe_conversion_rate'),
    //             ];
    //         }
    //         if (get_option('mollie_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('mollie'),
    //                 'conversion_rate'=> get_option('mollie_conversion_rate'),
    //                 'currency'=> get_option('mollie_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('mollie_conversion_rate'),
    //             ];
    //         }
    //         if (get_option('im_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('instamojo'),
    //                 'conversion_rate'=> get_option('im_conversion_rate'),
    //                 'currency'=> get_option('im_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('im_conversion_rate'),
    //             ];
    //         }
    //         if (get_option('razorpay_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('razorpay'),
    //                 'conversion_rate'=> get_option('razorpay_conversion_rate'),
    //                 'currency'=> get_option('razorpay_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('razorpay_conversion_rate'),
    //             ];
    //         }
    //         if (get_option('paystack_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('paystack'),
    //                 'conversion_rate'=> get_option('paystack_conversion_rate'),
    //                 'currency'=> get_option('paystack_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('paystack_conversion_rate'),
    //             ];
    //         }
    //         if (get_option('sslcommerz_status') == 1){
    //             $availablePaymentMethods[] = [
    //                 'method'=>__('sslcommerz'),
    //                 'conversion_rate'=> get_option('sslcommerz_conversion_rate'),
    //                 'currency'=> get_option('sslcommerz_currency'),
    //                 'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('sslcommerz_conversion_rate'),
    //             ];
    //         }
    //         $result_array = [
    //             'billingAddressSectionData' => $billing_address_section,
    //             'availablePaymentMethods' => $availablePaymentMethods,
    //             'orderReviewSectionData' => $order_review_section,
    //             'billingSummarySectionData' => $billing_summary_section,
    //         ];
    //     return response()->json($result_array);

    //     // return $this->success($data);
    // }




       public function getDonationFaysalBankAccessToken($merchant_id,$secured_key,$trans_amount = null, $basket_id = null, $Currency = null){
      
        $tokenApiUrl = 'https://ipg1.apps.net.pk/Ecommerce/api/Transaction/GetAccessToken';
    
        $urlPostParams = sprintf( 
        'MERCHANT_ID=%s&SECURED_KEY=%s&TXNAMT=%s&BASKET_ID=%s',
        $merchant_id, 
        $secured_key,
        $trans_amount, 
        $basket_id,
        $Currency
        ); 
        $array = array();
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlPostParams); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP PayFast Example'); 
        $response = curl_exec($ch); 
        curl_close($ch); 
        $payload = json_decode($response); 
        $token = isset($payload->ACCESS_TOKEN) ? $payload->ACCESS_TOKEN: ''; 
        return $token;
    }



      public function getFaysalBankAccessToken($merchant_id,$secured_key,$trans_amount = null, $basket_id = null, $Currency = null){
        if(empty(get_option('faysal_access_token_url'))){
            $accessTokenURL= env('FAYSAL_BANK_ACCESS_TOKEN_URL');
        }
        else{
            $accessTokenURL= get_option('faysal_access_token_url'); 
        }
        $tokenApiUrl = $accessTokenURL;
    
        $urlPostParams = sprintf( 
        'MERCHANT_ID=%s&SECURED_KEY=%s&TXNAMT=%s&BASKET_ID=%s',
        $merchant_id, 
        $secured_key,
        $trans_amount, 
        $basket_id,
        $Currency
        ); 
        $array = array();
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlPostParams); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP PayFast Example'); 
        $response = curl_exec($ch); 
        curl_close($ch); 
        $payload = json_decode($response); 
        $token = isset($payload->ACCESS_TOKEN) ? $payload->ACCESS_TOKEN: ''; 
        return $token;
    }




    //   public function checkout($type = null, $payment_id = null)
    // {
    //     $result_array = array();
    //     $user = Auth::user();
    //     if($user){
    //         $role = $user->role;
    //         if($role != 3){ // Student
    //             return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
    //         }
    //     }
    //     else{
    //         return response()->json(['message' => 'User Is Not Logged In'], 200);
    //     }

    //     $data['createdByType'] = $type;
    //     $data['cartID'] = null;
    //     $data['userInfo'] = null;
    //     $data['pageTitle'] = "Checkout";

    //     // Fetch user's cart items
    //     $carts = CartManagement::whereUserId($user->id)
    //         ->where('created_by_type', $type)
    //         ->with(['course' => function ($query) {
    //             $query->select('id', 'uuid', 'title', 'price', 'image');
    //         }])
    //         ->get();

    //     // Calculate total amount
    //     $tranAmount = (int) $carts->sum('price');

    //     // Generate Order ID and Update Cart with Payment ID
    //     if ($carts->isNotEmpty()) {
    //         $data['paymentCartID'] = 'LMS-P-' . time();

    //         foreach ($carts as $cart) {
    //             $cart->faysal_order_id = $data['paymentCartID'];
    //             $cart->update();
    //         }
    //     }

    //     // Fetch User Information and split name
    //     $user = Auth::user();
    //     $nameParts = explode(' ', $user->name, 2);
    //     $firstName = $nameParts[0] ?? '';
    //     $lastName = $nameParts[1] ?? '';

    //     $data['userInfo'] = [
    //         'first_name' => $firstName,
    //         'last_name' => $lastName,
    //         'email' => $user->email,
    //         'address' => $user->address,
    //         'mobile_number' => $user->mobile_number,
    //     ];

    //     // Fetch Course Details
    //     $courseNames = [];
    //     foreach ($carts as $cart) {
    //         if ($cart->course) {
    //             $courseNames[] = [
    //                 'id' => $cart->course->id,
    //                 'uuid' => $cart->course->uuid,
    //                 'title' => $cart->course->title,
    //                 'price' => $cart->course->price,
    //                 'image' => asset($cart->course->image),
    //             ];
    //         }
    //     }

    //     // Order Review (Courses + Cart Count)
    //     $data['order_review'] = [
    //         'courses' => $courseNames,
    //         'total_items' => count($carts),
    //     ];

    //     // Order Summary
    //     $subtotal = $carts->sum('price');
    //     $discount = $carts->sum('discount');
    //     $platformCharge = get_platform_charge($subtotal);
    //     $grandTotal = $subtotal - $discount + $platformCharge;

    //     $razorpayRate = get_option('razorpay_conversion_rate') ?? 1;
    //     $paystackRate = get_option('paystack_conversion_rate') ?? 1;
    //     $sslcommerzRate = get_option('sslcommerz_conversion_rate') ?? 1;

    //     if ($type == 'razorpay') {
    //         $grandTotal = $grandTotal * $razorpayRate;
    //     } elseif ($type == 'paystack') {
    //         $grandTotal = $grandTotal * $paystackRate;
    //     } elseif ($type == 'sslcommerz') {
    //         $grandTotal = $grandTotal * $sslcommerzRate;
    //     }
    //     $conversionRate = get_option('conversion_rate') ?? "?";


    //     $data['billingSummary'] = [
    //         'subtotal' => $subtotal,
    //         'discount' => $discount,
    //         'platform_charge' => $platformCharge,
    //         'grand_total' => $grandTotal,
    //         'conversion_rate' => "1 PKR = $conversionRate",
    //     ];

    //     // Fetch Available Banks
    //     $data['banks'] = Bank::where('status', 1)->orderBy('name', 'asc')->get(['id', 'name']);

    //     // Fetch Available Countries
    //     $data['countries'] = Country::orderBy('country_name', 'asc')->get(['id', 'country_name']);

    //     // Payment Data
    //     $data['LocationRedirect'] = 1;
    //     $data['txnAmount'] = $tranAmount;
    //     $data['randSignature'] = uniqid();
    //     $data['generalUrl'] = route('student.pay');
    //     $data['paymentType'] = $type;

    //     return $this->success($data);
    // }



    public function checkout($type = 1, $payment_id = null)
{
    $result_array = array();
    $user = Auth::user();
    if ($user) {
        $role = $user->role;
        if ($role != 3) { // Student
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
        }
    } else {
        return response()->json(['message' => 'User Is Not Logged In'], 200);
    }

    
    $data['cartID'] = null;
    $data['userInfo'] = null;
    $data['pageTitle'] = "Checkout";

    // Fetch ALL user carts with created_by_type = 1 (user-created carts)
    $carts = CartManagement::whereUserId($user->id)
        ->where('created_by_type', $type) // Critical fix: Always use 1 for student carts
        ->with(['course' => function ($query) {
            $query->select('id', 'uuid', 'title', 'price', 'image');
        }])
        ->get();

    if ($carts->isEmpty()) {
        return response()->json(['message' => 'Your cart is empty'], 302);
    }

    // Calculate total amount
    $tranAmount = (int) $carts->sum('price');

    // Generate Order ID and Update Cart with Payment ID
    $data['paymentCartID'] = 'LMS-P-' . time();
    foreach ($carts as $cart) {
        $cart->faysal_order_id = $data['paymentCartID'];
        $cart->save(); // Use save() instead of update() for mass assignment safety
    }

    // Fetch User Information
    $nameParts = explode(' ', $user->name, 2);
    $firstName = $nameParts[0] ?? '';
    $lastName = $nameParts[1] ?? '';

    $data['userInfo'] = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $user->email,
        'address' => $user->address,
        'mobile_number' => $user->mobile_number,
    ];

    // Fetch Course Details
    $courseNames = [];
    foreach ($carts as $cart) {
        if ($cart->course) {
            $courseNames[] = [
                'id' => $cart->course->id,
                'uuid' => $cart->course->uuid,
                'title' => $cart->course->title,
                'price' => $cart->course->price,
                'image' => asset($cart->course->image),
            ];
        }
    }

    // Order Review
    $data['order_review'] = [
        'courses' => $courseNames,
        'total_items' => count($carts),
    ];

    // Order Summary
    $subtotal = $carts->sum('price');
    $discount = $carts->sum('discount');
    $platformCharge = get_platform_charge($subtotal);
    $grandTotal = $subtotal - $discount + $platformCharge;

    // Apply payment gateway conversion rates
    $conversionRate = get_option('conversion_rate') ?? 1;
    $data['billingSummary'] = [
        'subtotal' => $subtotal,
        'discount' => $discount,
        'platform_charge' => $platformCharge,
        'grand_total' => $grandTotal,
        'conversion_rate' => "1 PKR = $conversionRate",
    ];

    // Fetch Available Data
    $data['banks'] = Bank::where('status', 1)->orderBy('name', 'asc')->get(['id', 'name']);
    $data['countries'] = Country::orderBy('country_name', 'asc')->get(['id', 'country_name']);

    // Payment Data
    $data['createdByType'] = $type;
    $data['LocationRedirect'] = 1;
    $data['txnAmount'] = $tranAmount;
    $data['randSignature'] = uniqid();
    $data['generalUrl'] = route('student.pay');
    // $data['paymentType'] = $payment_gateway; // Updated to use payment_gateway

    return $this->success($data);
}


















       public function getAddressLocation($type = null, $payment_id = null)
    {
        $result_array = array();
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }

        $data['userInfo'] = null;
        $data['pageTitle'] = "Checkout";

     
     

        // Fetch User Information and split name
        $user = Auth::user();
        $nameParts = explode(' ', $user->name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        $data['userInfo'] = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->email,
            'address' => $user->address,
            'mobile_number' => $user->mobile_number,
        ];

  
    

     
     
        // Fetch Available Banks
        $data['banks'] = Bank::where('status', 1)->orderBy('name', 'asc')->get(['id', 'name']);

        // Fetch Available Countries
        $data['countries'] = Country::orderBy('country_name', 'asc')->get(['id', 'country_name']);

        $data['randSignature'] = uniqid();
        $data['generalUrl'] = route('student.pay');
      

        return $this->success($data);
    }

    public function pay(Request $request)
    {
        // Validate if the cart is empty
        if (CartManagement::whereUserId(@Auth::id())->count() == 0) {
            return $this->error([], __('Your cart is empty'));
        }

        // Validate payment method
        if (is_null($request->payment_method)) {
            return $this->error([], __('Please Select Payment Method'));
        }

        $order_data = $this->placeOrder($request->payment_method);
        if (!$order_data['status']) {
            return $this->error([], __('Something went wrong!'));
        }

        $order = $order_data['data'];

        // Update student billing address if applicable
        if (auth::user()->student) {
            $student = Student::find(auth::user()->student->id);
            $student->fill($request->all());
            $student->save();
        }

        // Handle bank payment (if applicable)
        if ($request->payment_method == 'bank') {
            if (empty($request->deposit_by) || is_null($request->deposit_slip)) {
                return $this->error([], __('Please provide deposit details'));
            }

            $deposit_slip = $this->uploadFileWithDetails('bank', $request->deposit_slip);
            if (!$deposit_slip['is_uploaded']) {
                return $this->error([], __('Failed to upload deposit slip'));
            }

            $order->payment_status = 'pending';
            $order->deposit_by = $request->deposit_by;
            $order->deposit_slip = $deposit_slip['path'];
            $order->payment_method = 'bank';
            $order->bank_id = $request->bank_id;
            $order->save();

            CartManagement::whereUserId(@Auth::id())->delete();

            // Send notification
            $text = __("New course enrollment pending request");
            $target_url = route('report.order-pending');
            $this->send($text, 1, $target_url, null);

            return $this->success([], __('Request has been placed! Please wait for approval'));
        }

        // For other payment methods, just store the payment ID and update status
        if ($request->has('payment_id')) {
            $order->payment_id = $request->payment_id;
            $order->payment_status = 'completed'; // Assuming payment is confirmed by the frontend
            $order->save();

            CartManagement::whereUserId(@Auth::id())->delete();

            return $this->success([], __('Payment successful! Order has been placed'));
        }

        return $this->error([], __('Payment details are missing'));
    }

    private function placeOrder($payment_method)
    {
        DB::beginTransaction();
        try {
            $carts = CartManagement::whereUserId(@Auth::id())->get();
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->order_number = rand(100000, 999999);
            $order->sub_total = $carts->sum('price');
            $order->discount = $carts->sum('discount');
            $order->platform_charge = get_platform_charge($carts->sum('price'));
            $order->current_currency = get_currency_code();
            $order->grand_total = $order->sub_total + $order->platform_charge;
            $order->payment_method = $payment_method;

            $payment_currency = '';
            $conversion_rate = '';

            if ($payment_method == 'paypal') {
                $payment_currency = get_option('paypal_currency');
                $conversion_rate = get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 0;
            } elseif ($payment_method == 'mercadopago') {
                $payment_currency = get_option('mercado_currency');
                $conversion_rate = get_option('mercado_conversion_rate') ? get_option('mercado_conversion_rate') : 0;
            }

            $order->payment_currency = $payment_currency;
            $order->conversion_rate = $conversion_rate;
            if ($conversion_rate) {
                $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge) * $conversion_rate;
            }

            $order->save();

            foreach ($carts as $cart) {
                if ($cart->course_id) {
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::id();
                    $order_item->course_id = $cart->course_id;
                    $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;

                    $order_item->original_price = $cart->original_price;
                    $order_item->discount = $cart->discount;
                    $order_item->admission_fee = $cart->admission_fee;
                    $order_item->discount_type = $cart->discount_type;
                    $order_item->net_original_price = $cart->net_original_price;
                    $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
                    $order_item->discount_admission_fee = $cart->discount_admission_fee;
                    $order_item->net_admission_fee = $cart->net_admission_fee;
                    $order_item->request_enrollment = $cart->request_enrollment;
                    $order_item->trial_id = $cart->trial_id;
                    $order_item->is_monthly_cron = $cart->is_monthly_cron;
                    $order_item->is_donation = $cart->is_donation;
                    $order_item->students_count = $cart->students_count;
                    $order_item->cart_type = $cart->created_by_type;
                    $order_item->created_by_type = $cart->created_by_type;

                    $order_item->month = $cart->month;
                    $order_item->year = $cart->year;
                    $order_item->cart_id = $cart->id;
                    
                    


                    $order_item->unit_price = $cart->price;
                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission) ? $userPackage->admin_commission : get_option('sell_commission');
                    if ($adminCommission) {
                        $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->sell_commission = $adminCommission;
                    } else {
                        $order_item->owner_balance = $cart->price;
                    }

                    $order_item->save();
                    $this->addAffiliateHistory($cart, $order, $order_item);
                } elseif ($cart->bundle_id) {
                    // $bundleIds = Enrollment::where('user_id', auth()->id())->whereNotIn('course_id', $cart->bundle_course_ids)->where('end_date', '<', now())->select('course_id')->get()->toArray();
                    $courses = Course::whereIn('id', $cart->bundle_course_ids)->get();
                    $bundleUserId = $cart->bundle->user_id;
                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $bundleUserId)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission) ? $userPackage->admin_commission : get_option('sell_commission');
                    if ($adminCommission) {
                        $adminCommissionAmount = admin_commission_by_percentage($cart->price, $adminCommission);
                        $totalDistributeAmount = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                    } else {
                        $adminCommissionAmount = admin_commission_by_percentage($cart->price, $adminCommission);
                        $totalDistributeAmount = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                    }

                    $totalCourseAmount = $courses->sum('price');
                    $finalPriceRatio = $totalDistributeAmount / $totalCourseAmount;
                    $adminCommissionRatio = ($totalCourseAmount) ? $adminCommissionAmount / $totalCourseAmount : 0;
                    $sellCommissionRatio = ($totalCourseAmount) ? $adminCommission / $totalCourseAmount : 0;

                    /*
                     * All bundle course added but not calculate balance, commission etc
                     */
                    foreach ($courses as $course) {
                        /*
                        need to add bundle course in order list
                        Old paid course check with bundle course
                        */

                        $order_item = new Order_item();
                        $order_item->order_id = $order->id;
                        $order_item->user_id = Auth::user()->id;
                        $order_item->bundle_id = $cart->bundle_id;
                     
                     
                        $order_item->owner_user_id = $bundleUserId;
                        $order_item->unit_price = $course->price;
                        $order_item->owner_balance = $finalPriceRatio * $course->price;
                        $order_item->sell_commission = $sellCommissionRatio * $course->price;
                        $order_item->admin_commission = $adminCommissionRatio * $course->price;
                        $order_item->course_id = $course->id;
                        $order_item->type = 3; //bundle course
                        $order_item->request_enrollment = $cart->request_enrollment;
                        $order_item->trial_id = $cart->trial_id;
                        $order_item->is_monthly_cron = $cart->is_monthly_cron;
                        $order_item->is_donation = $cart->is_donation;
                        $order_item->students_count = $cart->students_count;
                        $order_item->cart_type = $cart->created_by_type;
                        $order_item->created_by_type = $cart->created_by_type;

                        $order_item->month = $cart->month;
                        $order_item->year = $cart->year;
                        $order_item->cart_id = $cart->id;
                        $order_item->save();
                        $this->addAffiliateHistory($cart, $order, $order_item);
                    }
                } elseif ($cart->consultation_slot_id) {
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::id();
                    $order_item->owner_user_id = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->instructor_user_id : null;
                    $order_item->consultation_slot_id = $cart->consultation_slot_id;
                    $order_item->consultation_date = $cart->consultation_date;
                    $order_item->unit_price = $cart->price;
                    $order_item->request_enrollment = $cart->request_enrollment;
                        $order_item->trial_id = $cart->trial_id;
                        $order_item->is_monthly_cron = $cart->is_monthly_cron;
                        $order_item->is_donation = $cart->is_donation;
                        $order_item->students_count = $cart->students_count;
                        $order_item->cart_type = $cart->created_by_type;
                        $order_item->created_by_type = $cart->created_by_type;

                        $order_item->month = $cart->month;
                        $order_item->year = $cart->year;
                        $order_item->cart_id = $cart->id;

                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission) ? $userPackage->admin_commission : get_option('sell_commission');
                    if ($adminCommission) {
                        $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->sell_commission = $adminCommission;
                    } else {
                        $order_item->owner_balance = $cart->price;
                    }

                    $order_item->type = 4;
                    $order_item->save();

                    //Start:: Need to add Booking History
                    $booking = new BookingHistory();
                    $booking->order_id = $order->id;
                    $booking->order_item_id = $order_item->id;
                    $booking->instructor_user_id = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->instructor_user_id : null;
                    $booking->student_user_id = Auth::id();
                    $booking->consultation_slot_id = $cart->consultation_slot_id;
                    $booking->date = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->date : null;
                    $booking->day = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->day : null;
                    $booking->time = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->time : null;
                    $booking->duration = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->duration : null;
                    $booking->type = $cart->consultation_available_type;
                    $booking->status = 0; //Pending
                    $booking->save();

                    //End:: Add Booking History
                }
            }

            DB::commit();
            return ['status' => true, 'data' => $order];
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logger->log('Cannot Create Order', $e->getMessage());
            return ['status' => false, 'data' => null];
        }
    }

    private function addAffiliateHistory($cart, $order, $order_item)
    {
        if (get_option('referral_status')) {
            $refRequest = AffiliateRequest::where(['affiliate_code' => $cart->reference, 'status' => STATUS_APPROVED])->first();
            if (!is_null($refRequest)) {
                $refUser = User::where(['id' => $refRequest->user_id])->first();
                $alreadyAffiliated = AffiliateHistory::where(['user_id' => $refUser->id, 'course_id' => $cart->course_id])->first();
                if ($refUser->is_affiliator == AFFILIATOR && is_null($alreadyAffiliated)) {
                    $commission = referral_sell_commission($cart->price - $cart->discount);
                    $affiliate = new AffiliateHistory();
                    $affiliate->hash = Str::uuid()->getHex();
                    $affiliate->user_id = $refUser->id;
                    $affiliate->buyer_id = Auth::id();
                    $affiliate->order_id = $order->id;
                    $affiliate->order_item_id = $order_item->id;
                    if ($cart->course_id) {
                        $affiliate->course_id = $cart->course_id;
                    } elseif ($cart->bundle_id) {
                        $affiliate->bundle_id = $cart->bundle_id;
                    }
                    $affiliate->actual_price = $cart->price;
                    $affiliate->discount = $cart->discount;
                    $affiliate->commission = $commission;
                    $affiliate->commission_percentage = get_option('referral_commission_percentage');
                    $affiliate->save();
                }
            }
        }
    }









 public function successfulFaysalApi(Request $request ,$user_id = null, $cart_id = null){
    
        $result_array = array();
        if($user_id == null){
            $result_array = [
                        'status' => false,
                        'message' => 'User ID is required',
                    ];
            return response()->json($result_array,302);
        }
        if($cart_id == null){
            $result_array = [
                        'status' => false,
                        'message' => 'Cart ID is required',
                    ];
            return response()->json($result_array,302);
        }

        $checkUser = User::where('id',$user_id)->first();
        if($checkUser == null){
            $result_array = [
                        'status' => false,
                        'message' => 'Given User ID Does not exists in records',
                    ];
            return response()->json($result_array,302);
        }
        $checkCart = CartManagement::where('id',$cart_id)->first();
        if($checkCart == null){
            $result_array = [
                        'status' => false,
                        'message' => 'Given Cart ID Does not exists in records',
                    ];
            return response()->json($result_array,302);
        }
    
        if($checkCart->user_id != $user_id){
             $result_array = [
                        'status' => false,
                        'message' => 'Invalid Cart ID For Given User',
                    ];
            return response()->json($result_array,302);
        }
        $errCode = $request->err_code;
		$errMsg = $request->err_msg;
		$paymentName = $request->PaymentName;
		$trans_id = $request->transaction_id;
		$order_date = $request->order_date;
		$response_key = $request->Response_Key; 
		$secretword = '';
		$original_basket_id = $request->basket_id; 
		$txt_amount = $request->transaction_amount; 
		$cart_id = $cart_id;

   
        if ($errCode == '000' || $errCode == '00') { 
            if($cart_id != null){
                $getCartData = CartManagement::where('id',$cart_id)->first();
                $subTotal = $getCartData->price;
                $month = $getCartData->month;
                $year = $getCartData->year;
                $student_user_id = $getCartData->user->id;
                $faysalOrderID =  $getCartData->faysal_order_id;
            }
            else{
                $getCartData = CartManagement::whereUserId($user_id)
                    ->where('created_by_type',1)->get();    //1 Means Created By Student     2 Means Created BY Admin
                $subTotal = $getCartData->sum('price');
                $month = null;
                $year = null;
                $getCartData = CartManagement::whereUserId($user_id)
                    ->where('created_by_type',1)->first();
                $student_user_id = $getCartData->user->id;  
                $faysalOrderID =  $getCartData->faysal_order_id;
            }

            $getCheck = Order::where('faysal_transaction_id',$trans_id)->first();
            $grandTotal = $subTotal;
            $getStudentData = User::where('id', $student_user_id)->first();
            if($getCheck == null){
                if($cart_id != null){
                    $carts = CartManagement::whereUserId($user_id)->where('id',$cart_id)->get();
                }
                else{
                    $carts = CartManagement::whereUserId($user_id)
                    ->where('created_by_type',1)->get();
                }
                $getPaymentData = new Order;
                $successMsg = "Transaction Amount was deposited successfully at faysal bank.";
                $getPaymentData->user_id = $student_user_id;
                $getPaymentData->cart_id = $cart_id;
                $getPaymentData->order_number = rand(100000,999999);
                $getPaymentData->sub_total = $subTotal;
                $getPaymentData->grand_total = $grandTotal;
                $getPaymentData->current_currency = "PKR";
                $getPaymentData->payment_currency = "PKR";
                $getPaymentData->payment_method = "faysal_bank";
                $getPaymentData->faysal_transaction_id = $trans_id;
                $getPaymentData->faysal_order_id = $faysalOrderID;
                $getPaymentData->payment_id = $trans_id;
                $getPaymentData->faysal_payment_source = $paymentName;
                $getPaymentData->faysal_payment_date = date('y-m-d h:i:sa');
                if($cart_id != null){
                    $getPaymentData->created_by_type = 2;   
                $getPaymentData->cart_type = 2;
                }
                else{
                    $getPaymentData->cart_type = 1;
                    $getPaymentData->created_by_type = 1;   
                }
                $getPaymentData->month = $month;
                $getPaymentData->year= $year;
                $getPaymentData->payment_status = 'paid';
                $getPaymentData->delivery_status = 1;
                $payment_currency = get_option('faysal_currency');
                $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;
    
                $getPaymentData->conversion_rate = $conversion_rate;
                if ($conversion_rate) {
                    $getPaymentData->grand_total_with_conversation_rate = ($getPaymentData->sub_total + $getPaymentData->platform_charge + $getPaymentData->shipping_cost) * $conversion_rate;
                }
                $getPaymentData->save();
    
                
                foreach ($carts as $cart) {
                    if ($cart->course_id) {
                        $courseExits = Course::find($cart->course_id);
                        $order_item = new Order_item();
                        $order_item->order_id = $getPaymentData->id;
                        $order_item->user_id = $user_id;
                        $order_item->product_id = $cart->product_id;
                        $order_item->course_id = $cart->course_id;

                        $order_item->original_price = $cart->original_price;
                        $order_item->discount = $cart->discount;
                        $order_item->admission_fee = $cart->admission_fee;
                        $order_item->discount_type = $cart->discount_type;
                        $order_item->net_original_price = $cart->net_original_price;
                        $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
                        $order_item->discount_admission_fee = $cart->discount_admission_fee;
                        $order_item->net_admission_fee = $cart->net_admission_fee;
                        $order_item->request_enrollment = $cart->request_enrollment;
                    $order_item->trial_id = $cart->trial_id;
                    $order_item->is_monthly_cron = $cart->is_monthly_cron;
                    $order_item->is_donation = $cart->is_donation;
                    $order_item->students_count = $cart->students_count;
                    $order_item->cart_type = $cart->created_by_type;
                    $order_item->created_by_type = $cart->created_by_type;

                    $order_item->month = $cart->month;
                    $order_item->year = $cart->year;
                    $order_item->cart_id = $cart->id;


                        $order_item->type = 1;
                        $order_item->receiver_info = $cart->receiver_info;
                        $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;
                        $order_item->unit_price = $cart->price;
                        $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                        $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                        if ($adminCommission) {
                            $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                            $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                            $order_item->sell_commission = $adminCommission;
                        } else {
                            $order_item->owner_balance = $cart->price;
                        }
                    
                        $order_item->save();

                        if($getPaymentData->created_by_type != 2){
                            $enrollment = Enrollment::where('user_id',$user->id)->where('course_id',$cart->course_id)->orderBy('id','DESC')->first();
                            if($enrollment){
                                $enrollment->order_id = $getPaymentData->id;
                                $enrollment->user_id = $user_id;
                                $enrollment->course_id = $cart->course_id;
    
                                $enrollment->main_price = $order_item->unit_price;
                                $enrollment->price = $order_item->unit_price;
                                $enrollment->original_price = $order_item->original_price;
                                $enrollment->discount = $order_item->discount;
                                $enrollment->admission_fee = $order_item->admission_fee;
                                $enrollment->discount_type = $order_item->discount_type;
                                $enrollment->net_original_price = $order_item->net_original_price;
                                $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                                $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                $enrollment->net_admission_fee = $order_item->net_admission_fee;
                                $enrollment->request_enrollment = $order_item->request_enrollment;
    
                                $enrollment->owner_user_id = $courseExits->user_id ?? null;
                                $enrollment->start_date = now();
                                $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                                $enrollment->save();
                            }
                            else{
                                $enrollment = new Enrollment();
                                $enrollment->order_id = $getPaymentData->id;
                                $enrollment->user_id = $user_id;
                                $enrollment->course_id = $cart->course_id;

                                $enrollment->main_price = $order_item->unit_price;
                                $enrollment->price = $order_item->unit_price;
                                $enrollment->original_price = $order_item->original_price;
                                $enrollment->discount = $order_item->discount;
                                $enrollment->admission_fee = $order_item->admission_fee;
                                $enrollment->discount_type = $order_item->discount_type;
                                $enrollment->net_original_price = $order_item->net_original_price;
                                $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                                $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                $enrollment->net_admission_fee = $order_item->net_admission_fee;
                                $enrollment->request_enrollment = $order_item->request_enrollment;

                                $enrollment->owner_user_id = $courseExits->user_id ?? null;
                                $enrollment->start_date = now();
                                $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                                $enrollment->save();
                                Log::info("NEW ENROLLMENT BEING CREATED IN (successfulFaysalApi) IN API/STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                Log::info("NEW ENROLLMENT DETAILS IN (successfulFaysalApi) IN API/STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                            }
                          
                        }
                     

                        // $this->addAffiliateHistory($cart,$getPaymentData,$order_item);
                    }
                    elseif ($cart->product_id) {
                        $order_item = new Order_item();
                        $order_item->order_id = $getPaymentData->id;
                        $order_item->user_id = $user_id;
                        $order_item->type = 2;
                        $order_item->product_id = $cart->product_id;
                        $order_item->receiver_info = $cart->receiver_info;
                        $order_item->owner_user_id = $cart->product ? $cart->product->user_id : null;
                        $order_item->unit_price = $cart->price;
                        $order_item->unit = $cart->quantity;

                        $order_item->original_price = $cart->original_price;
                        $order_item->discount = $cart->discount;
                        $order_item->admission_fee = $cart->admission_fee;
                        $order_item->discount_type = $cart->discount_type;
                        $order_item->net_original_price = $cart->net_original_price;
                        $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
                        $order_item->discount_admission_fee = $cart->discount_admission_fee;
                        $order_item->net_admission_fee = $cart->net_admission_fee;
                   $order_item->request_enrollment = $cart->request_enrollment;
                        $order_item->trial_id = $cart->trial_id;
                        $order_item->is_monthly_cron = $cart->is_monthly_cron;
                        $order_item->is_donation = $cart->is_donation;
                        $order_item->students_count = $cart->students_count;
                        $order_item->cart_type = $cart->created_by_type;
                        $order_item->created_by_type = $cart->created_by_type;

                        $order_item->month = $cart->month;
                        $order_item->year = $cart->year;
                        $order_item->cart_id = $cart->id;

                        $adminCommission = get_option('sell_commission');
                        if ($adminCommission) {
                            $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                            $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                            $order_item->sell_commission = $adminCommission;
                        } else {
                            $order_item->owner_balance = $cart->price;
                        }
    
                        $order_item->save();
    
                    }
                }

               

                                  //Email Send Code
                    if($cart_id != null){
                        // $email_sent = $this->sendPaymentEmail($getPaymentData->id,$cart_id);
                        $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                    }
                    else{
                        $CART =CartManagement::whereUserId($user_id)
                        ->where('created_by_type',1)->first();
                        if($CART){
                            $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                        }
                        else{
                            // $email_sent = $this->sendPaymentEmail($getPaymentData->id);
                            $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                        }            
                    }


                    $target_url = route('student_payment.completed');
                    $text = __("Student payment has been accepted by faysal bank.");
                    $this->send($text, 1, $target_url, null);

      
                $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");

                if($cart_id != null){
                    $getCartData = CartManagement::where('id',$cart_id)->first();
                    if($getCartData){
                        $getCartData->delete();
                    }
                }
                else{
                      CartManagement::whereUserId($user_id)
                        ->where('created_by_type',1)->delete();
                }

                  $successMsg = $successMsg;
                 $result_array = [
                        'status' => true,
                        'message' => $successMsg,
                        'data' => $getPaymentData
                    ];
                return response()->json($result_array);
              
            }
        }
        else{
          
                $errorMsg = "Transaction Failed. Message: " . $errMsg;
                 $result_array = [
                        'status' => false,
                        'message' => $errorMsg,
                        'data' => []
                    ];
            return response()->json($result_array);
		}

}





public function successfulFaysalApiAll(Request $request){
    
     
        $cart_id = null;
        $result_array = array();
        $user = Auth::user();
    
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }

        // if($user_id == null){
        //     $result_array = [
        //                 'status' => false,
        //                 'message' => 'User ID is required',
        //             ];
        //     return response()->json($result_array,302);
        // }
        $user_id = $user->id;
        
        $checkUser = User::where('id',$user_id)->first();
        if($checkUser == null){
            $result_array = [
                        'status' => false,
                        'message' => 'Given User ID Does not exists in records',
                    ];
            return response()->json($result_array,302);
        }
     
    
      
        $errCode = $request->err_code;
		$errMsg = $request->err_msg;
		$paymentName = $request->PaymentName;
		$trans_id = $request->transaction_id;
		$order_date = $request->order_date;
		$response_key = $request->Response_Key; 
		$secretword = '';
		$original_basket_id = $request->basket_id; 
		$txt_amount = $request->transaction_amount; 
		$cart_id = $cart_id;

   
        if ($errCode == '000' || $errCode == '00') { 
            if($cart_id != null){
                $getCartData = CartManagement::where('id',$cart_id)->first();
                $subTotal = $getCartData->price;
                $month = $getCartData->month;
                $year = $getCartData->year;
                $student_user_id = $getCartData->user->id;
                $faysalOrderID =  $getCartData->faysal_order_id;
            }
            else{
                // $getCartData = CartManagement::whereUserId($user_id)
                //     ->where('created_by_type',1)->get();    //1 Means Created By Student     2 Means Created BY Admin
                $getCartData = CartManagement::whereUserId($user_id)
                    ->whereIn('created_by_type',[1,2])->get();    //1 Means Created By Student     2 Means Created BY Admin
                $subTotal = $getCartData->sum('price');
                $month = null;
                $year = null;
                $getCartData = CartManagement::whereUserId($user_id)
                    ->whereIn('created_by_type',[1,2])->first();
                $student_user_id = $getCartData->user->id;  
                $faysalOrderID =  $getCartData->faysal_order_id;
            }

            $getCheck = Order::where('faysal_transaction_id',$trans_id)->first();
            $grandTotal = $subTotal;
            $getStudentData = User::where('id', $student_user_id)->first();
            if($getCheck == null){
                if($cart_id != null){
                    $carts = CartManagement::whereUserId($user_id)->where('id',$cart_id)->get();
                }
                else{
                    $carts = CartManagement::whereUserId($user_id)
                    ->whereIn('created_by_type',[1,2])->get();
                }
                $getPaymentData = new Order;
                $successMsg = "Transaction Amount was deposited successfully at faysal bank.";
                $getPaymentData->user_id = $student_user_id;
                $getPaymentData->cart_id = $cart_id;
                $getPaymentData->order_number = rand(100000,999999);
                $getPaymentData->sub_total = $subTotal;
                $getPaymentData->grand_total = $grandTotal;
                $getPaymentData->current_currency = "PKR";
                $getPaymentData->payment_currency = "PKR";
                $getPaymentData->payment_method = "faysal_bank";
                $getPaymentData->faysal_transaction_id = $trans_id;
                $getPaymentData->faysal_order_id = $faysalOrderID;
                $getPaymentData->payment_id = $trans_id;
                $getPaymentData->faysal_payment_source = $paymentName;
                $getPaymentData->faysal_payment_date = date('y-m-d h:i:sa');
                if($cart_id != null){
                    $getPaymentData->created_by_type = 2;   
                $getPaymentData->cart_type = 2;
                }
                else{
                    $getPaymentData->cart_type = 1;
                    $getPaymentData->created_by_type = 1;   
                }
                $getPaymentData->month = $month;
                $getPaymentData->year= $year;
                $getPaymentData->payment_status = 'paid';
                $getPaymentData->delivery_status = 1;
                $payment_currency = get_option('faysal_currency');
                $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;
    
                $getPaymentData->conversion_rate = $conversion_rate;
                if ($conversion_rate) {
                    $getPaymentData->grand_total_with_conversation_rate = ($getPaymentData->sub_total + $getPaymentData->platform_charge + $getPaymentData->shipping_cost) * $conversion_rate;
                }
                $getPaymentData->save();
    
                
                foreach ($carts as $cart) {
                    if ($cart->course_id) {
                        $courseExits = Course::find($cart->course_id);
                        $order_item = new Order_item();
                        $order_item->order_id = $getPaymentData->id;
                        $order_item->user_id = $user_id;
                        $order_item->product_id = $cart->product_id;
                        $order_item->course_id = $cart->course_id;

                        $order_item->original_price = $cart->original_price;
                        $order_item->discount = $cart->discount;
                        $order_item->admission_fee = $cart->admission_fee;
                        $order_item->discount_type = $cart->discount_type;
                        $order_item->net_original_price = $cart->net_original_price;
                        $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
                        $order_item->discount_admission_fee = $cart->discount_admission_fee;
                        $order_item->net_admission_fee = $cart->net_admission_fee;
                        $order_item->request_enrollment = $cart->request_enrollment;
                    $order_item->trial_id = $cart->trial_id;
                    $order_item->is_monthly_cron = $cart->is_monthly_cron;
                    $order_item->is_donation = $cart->is_donation;
                    $order_item->students_count = $cart->students_count;
                    $order_item->cart_type = $cart->created_by_type;
                    $order_item->created_by_type = $cart->created_by_type;

                    $order_item->month = $cart->month;
                    $order_item->year = $cart->year;
                    $order_item->cart_id = $cart->id;


                        $order_item->type = 1;
                        $order_item->receiver_info = $cart->receiver_info;
                        $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;
                        $order_item->unit_price = $cart->price;
                        $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                        $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                        if ($adminCommission) {
                            $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                            $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                            $order_item->sell_commission = $adminCommission;
                        } else {
                            $order_item->owner_balance = $cart->price;
                        }
                    
                        $order_item->save();

                        if(auth::user()->student->status == 0){
                                $courseExits = Course::find($order_item->course_id);
                                $enrollment = Enrollment::where('user_id',auth()->id())->where('course_id',$order_item->course_id)->orderBy('id','DESC')->first();
                                if($enrollment){
                                    $enrollment->order_id = $order_item->order_id;
                                    $enrollment->user_id = auth()->id();
                                    $enrollment->course_id = $order_item->course_id;
                                    $enrollment->owner_user_id = $courseExits->user_id ?? null;
                                    $enrollment->start_date = now();
                                    $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;

                                     if($order_item->request_enrollment && $order_item->trial_id != null){
                                         $enrollment->trial_id = $order_item->trial_id;
                                         $getTrialInfo = TrialClass::where('id',$order_item->trial_id)->first();
                                         if($getTrialInfo){
                                             $getTrialInfo->student_enrolled = 1;
                                             $getTrialInfo->save();
                                         }
                                     }


                                     $enrollment->main_price = $order_item->unit_price;
                                     $enrollment->price = $order_item->unit_price;
                                     $enrollment->original_price = $order_item->original_price;
                                     $enrollment->discount = $order_item->discount;
                                     $enrollment->admission_fee = $order_item->admission_fee;
                                     $enrollment->discount_type = $order_item->discount_type;
                                     $enrollment->net_original_price = $order_item->net_original_price;
                                     $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                                     $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                     $enrollment->net_admission_fee = $order_item->net_admission_fee;

                                    $enrollment->save();     
                                }
                                else{  
                                    $enrollment = new Enrollment();
                                    $enrollment->order_id = $order_item->order_id;
                                    $enrollment->user_id = auth()->id();
                                    $enrollment->course_id = $order_item->course_id;
                                    $enrollment->owner_user_id = $courseExits->user_id ?? null;
                                    $enrollment->start_date = now();
                                    $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;

                                     if($order_item->request_enrollment && $order_item->trial_id != null){
                                         $enrollment->trial_id = $order_item->trial_id;
                                         $getTrialInfo = TrialClass::where('id',$order_item->trial_id)->first();
                                         if($getTrialInfo){
                                             $getTrialInfo->student_enrolled = 1;
                                             $getTrialInfo->save();
                                         }
                                     }


                                     $enrollment->main_price = $order_item->unit_price;
                                     $enrollment->price = $order_item->unit_price;
                                     $enrollment->original_price = $order_item->original_price;
                                     $enrollment->discount = $order_item->discount;
                                     $enrollment->admission_fee = $order_item->admission_fee;
                                     $enrollment->discount_type = $order_item->discount_type;
                                     $enrollment->net_original_price = $order_item->net_original_price;
                                     $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                                     $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                     $enrollment->net_admission_fee = $order_item->net_admission_fee;

                                    $enrollment->save(); 
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (successfulFaysalApiAll) IN API/STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (successfulFaysalApiAll) IN API/STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                                }
                              

                                $getStudent = Student::where('id',auth::user()->student->id)->first();
                                if($getStudent){
                                    $getStudent->status = 1;
                                    $getStudent->save();
                                }

                 
                                $template_name =  'student-sign-up'; 
                                
                                
                                sendEmailNotification([
                                '{{USER_NAME}}' => auth::user()->name,
                                '{{EMAIL}}' => auth::user()->email,
                                '{{PASSWORD}}' => auth::user()->random_password,
                                ], auth::user()->email, $template_name);
                        }
                        else{
                            if($getPaymentData->created_by_type != 2){
                                $enrollment = Enrollment::where('user_id',auth()->id())->where('course_id',$order_item->course_id)->orderBy('id','DESC')->first();
                                if($enrollment){
                                    $enrollment->order_id = $getPaymentData->id;
                                    $enrollment->user_id = $user_id;
                                    $enrollment->course_id = $cart->course_id;

                                    if($order_item->request_enrollment && $order_item->trial_id != null){
                                        $enrollment->trial_id = $order_item->trial_id;
                                        $getTrialInfo = TrialClass::where('id',$order_item->trial_id)->first();
                                        if($getTrialInfo){
                                            $getTrialInfo->student_enrolled = 1;
                                            $getTrialInfo->save();
                                        }
                                    }

                                    $enrollment->main_price = $order_item->unit_price;
                                    $enrollment->price = $order_item->unit_price;
                                    $enrollment->original_price = $order_item->original_price;
                                    $enrollment->discount = $order_item->discount;
                                    $enrollment->admission_fee = $order_item->admission_fee;
                                    $enrollment->discount_type = $order_item->discount_type;
                                    $enrollment->net_original_price = $order_item->net_original_price;
                                    $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                                    $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                    $enrollment->net_admission_fee = $order_item->net_admission_fee;
                                    $enrollment->request_enrollment = $order_item->request_enrollment;

                                    $enrollment->owner_user_id = $courseExits->user_id ?? null;
                                    $enrollment->start_date = now();
                                    $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                                    $enrollment->save();
                                }
                                else{
                                    $enrollment = new Enrollment();
                                    $enrollment->order_id = $getPaymentData->id;
                                    $enrollment->user_id = $user_id;
                                    $enrollment->course_id = $cart->course_id;

                                    if($order_item->request_enrollment && $order_item->trial_id != null){
                                        $enrollment->trial_id = $order_item->trial_id;
                                        $getTrialInfo = TrialClass::where('id',$order_item->trial_id)->first();
                                        if($getTrialInfo){
                                            $getTrialInfo->student_enrolled = 1;
                                            $getTrialInfo->save();
                                        }
                                    }

                                    $enrollment->main_price = $order_item->unit_price;
                                    $enrollment->price = $order_item->unit_price;
                                    $enrollment->original_price = $order_item->original_price;
                                    $enrollment->discount = $order_item->discount;
                                    $enrollment->admission_fee = $order_item->admission_fee;
                                    $enrollment->discount_type = $order_item->discount_type;
                                    $enrollment->net_original_price = $order_item->net_original_price;
                                    $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                                    $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                    $enrollment->net_admission_fee = $order_item->net_admission_fee;
                                    $enrollment->request_enrollment = $order_item->request_enrollment;

                                    $enrollment->owner_user_id = $courseExits->user_id ?? null;
                                    $enrollment->start_date = now();
                                    $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                                    $enrollment->save();
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (successfulFaysalApiAll(2)) IN API/STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (successfulFaysalApiAll(2)) IN API/STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                                }
                             
                            }
                        }

                      
                     

                        // $this->addAffiliateHistory($cart,$getPaymentData,$order_item);
                    }
                    elseif ($cart->product_id) {
                        $order_item = new Order_item();
                        $order_item->order_id = $getPaymentData->id;
                        $order_item->user_id = $user_id;
                        $order_item->type = 2;
                        $order_item->product_id = $cart->product_id;
                        $order_item->receiver_info = $cart->receiver_info;
                        $order_item->owner_user_id = $cart->product ? $cart->product->user_id : null;
                        $order_item->unit_price = $cart->price;
                        $order_item->unit = $cart->quantity;

                        $order_item->original_price = $cart->original_price;
                        $order_item->discount = $cart->discount;
                        $order_item->admission_fee = $cart->admission_fee;
                        $order_item->discount_type = $cart->discount_type;
                        $order_item->net_original_price = $cart->net_original_price;
                        $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
                        $order_item->discount_admission_fee = $cart->discount_admission_fee;
                        $order_item->net_admission_fee = $cart->net_admission_fee;
                     $order_item->request_enrollment = $cart->request_enrollment;
                        $order_item->trial_id = $cart->trial_id;
                        $order_item->is_monthly_cron = $cart->is_monthly_cron;
                        $order_item->is_donation = $cart->is_donation;
                        $order_item->students_count = $cart->students_count;
                        $order_item->cart_type = $cart->created_by_type;
                        $order_item->created_by_type = $cart->created_by_type;

                        $order_item->month = $cart->month;
                        $order_item->year = $cart->year;
                        $order_item->cart_id = $cart->id;

                        $adminCommission = get_option('sell_commission');
                        if ($adminCommission) {
                            $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                            $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                            $order_item->sell_commission = $adminCommission;
                        } else {
                            $order_item->owner_balance = $cart->price;
                        }
    
                        $order_item->save();
    
                    }
                }

               

                                  //Email Send Code
                    if($cart_id != null){
                        // $email_sent = $this->sendPaymentEmail($getPaymentData->id,$cart_id);
                        $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                    }
                    else{
                        $CART =CartManagement::whereUserId($user_id)
                        ->whereIn('created_by_type',[1,2])->first();
                        if($CART){
                            $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                        }
                        else{
                            // $email_sent = $this->sendPaymentEmail($getPaymentData->id);
                            $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                        }            
                    }


                    $target_url = route('student_payment.completed');
                    $text = __("Student payment has been accepted by faysal bank.");
                    $this->send($text, 1, $target_url, null);

      
                $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");

                if($cart_id != null){
                    $getCartData = CartManagement::where('id',$cart_id)->first();
                    if($getCartData){
                        $getCartData->delete();
                    }
                }
                else{
                      CartManagement::whereUserId($user_id)
                        ->whereIn('created_by_type',[1,2])->delete();
                }

                  $successMsg = $successMsg;
                 $result_array = [
                        'status' => true,
                        'message' => $successMsg,
                        'data' => $getPaymentData
                    ];
                return response()->json($result_array);
              
            }
        }
        else{
          
                $errorMsg = "Transaction Failed. Message: " . $errMsg;
                 $result_array = [
                        'status' => false,
                        'message' => $errorMsg,
                        'data' => []
                    ];
            return response()->json($result_array);
		}

}





public function checkoutFaysalApi(Request $request ,$user_id = null, $cart_id = null){
    $result_array = array();
    $errCode = $request->err_code;
    $errMsg = $request->err_msg;
    $paymentName = $request->PaymentName;
    $trans_id = $request->transaction_id;
    $order_date = $request->order_date;
    $response_key = $request->Response_Key; 
    $secretword = '';
    $original_basket_id = $request->basket_id; 
    $txt_amount = $request->transaction_amount; 
    $cart_id = $cart_id;

    if ($errCode == '000' || $errCode == '00') { 
        
        if($cart_id != null){
            $getCartData = CartManagement::where('id',$cart_id)->first();
            $subTotal = $getCartData->price;
            $month = $getCartData->month;
            $year = $getCartData->year;
            $student_user_id = $getCartData->user->id;
            $faysalOrderID =  $getCartData->faysal_order_id;
        }
        else{
            $getCartData = CartManagement::whereUserId($user_id)
                ->where('created_by_type',1)->get();
            $subTotal = $getCartData->sum('price');
            $month = null;
            $year = null;
            $getCartData = CartManagement::whereUserId($user_id)
                ->where('created_by_type',1)->first();
            $student_user_id = $getCartData->user->id;   
            $faysalOrderID =  $getCartData->faysal_order_id;
        }
      
        $getCheck = Order::where('faysal_transaction_id',$trans_id)->first();
     
        $grandTotal = $subTotal;
        $getStudentData = User::where('id', $student_user_id)->first();
        if($getCheck == null){
            
            if($cart_id != null){
            $carts = CartManagement::whereUserId($user_id)->where('id',$cart_id)->get();
            }
            else{
                $carts = CartManagement::whereUserId($user_id)
                ->where('created_by_type',1)->get();
            }
            $getPaymentData = new Order;
            $successMsg = "Transaction Amount was deposited successfully at faysal bank.";
            $getPaymentData->user_id = $student_user_id;
            $getPaymentData->order_number = rand(100000,999999);
            $getPaymentData->sub_total = $subTotal;
            $getPaymentData->grand_total = $grandTotal;
            $getPaymentData->current_currency = "PKR";
            $getPaymentData->payment_currency = "PKR";
            $getPaymentData->payment_method = "faysal_bank";
            $getPaymentData->faysal_transaction_id = $trans_id;
            $getPaymentData->faysal_order_id = $faysalOrderID;
            $getPaymentData->payment_id = $trans_id;
            $getPaymentData->faysal_payment_source = $paymentName;
            $getPaymentData->faysal_payment_date = date('y-m-d h:i:sa');
            if($cart_id != null){
                $getPaymentData->created_by_type = 2;   
            $getPaymentData->cart_type = 2;
            }
            else{
                $getPaymentData->cart_type = 1;
                $getPaymentData->created_by_type = 1;   
            }
            $getPaymentData->month = $month;
            $getPaymentData->year= $year;
            $getPaymentData->payment_status = 'paid';
            $getPaymentData->delivery_status = 1;
            

            $payment_currency = get_option('faysal_currency');
            $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;

            $getPaymentData->conversion_rate = $conversion_rate;
            if ($conversion_rate) {
                $getPaymentData->grand_total_with_conversation_rate = ($getPaymentData->sub_total + $getPaymentData->platform_charge + $getPaymentData->shipping_cost) * $conversion_rate;
            }
            $getPaymentData->save();

            
            foreach ($carts as $cart) {
                if ($cart->course_id) {
                    $courseExits = Course::find($cart->course_id);
                    $order_item = new Order_item();
                    $order_item->order_id = $getPaymentData->id;
                    $order_item->user_id = $user_id;
                    $order_item->product_id = $cart->product_id;
                    $order_item->course_id = $cart->course_id;
                    $order_item->receiver_info = $cart->receiver_info;
                    $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;
                    $order_item->unit_price = $cart->price;

                    $order_item->original_price = $cart->original_price;
                    $order_item->discount = $cart->discount;
                    $order_item->admission_fee = $cart->admission_fee;
                    $order_item->discount_type = $cart->discount_type;
                    $order_item->net_original_price = $cart->net_original_price;
                    $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
                    $order_item->discount_admission_fee = $cart->discount_admission_fee;
                    $order_item->net_admission_fee = $cart->net_admission_fee;
                     $order_item->request_enrollment = $cart->request_enrollment;
                        $order_item->trial_id = $cart->trial_id;
                        $order_item->is_monthly_cron = $cart->is_monthly_cron;
                        $order_item->is_donation = $cart->is_donation;
                        $order_item->students_count = $cart->students_count;
                        $order_item->cart_type = $cart->created_by_type;
                        $order_item->created_by_type = $cart->created_by_type;

                        $order_item->month = $cart->month;
                        $order_item->year = $cart->year;
                        $order_item->cart_id = $cart->id;

                    $order_item->type = 1;
                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                    if ($adminCommission) {
                        $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->sell_commission = $adminCommission;
                    } else {
                        $order_item->owner_balance = $cart->price;
                    }
                
                    $order_item->save();

                       if($getPaymentData->created_by_type != 2){
                            $enrollment = Enrollment::where('user_id',$user_id)->where('course_id',$cart->course_id)->orderBy('id','DESC')->first();
                            if($enrollment){
                                $enrollment->order_id = $getPaymentData->id;
                                $enrollment->user_id = $user_id;
                                $enrollment->course_id = $cart->course_id;

                                $enrollment->main_price = $order_item->unit_price;
                                $enrollment->price = $order_item->unit_price;
                                $enrollment->original_price = $order_item->original_price;
                                $enrollment->discount = $order_item->discount;
                                $enrollment->admission_fee = $order_item->admission_fee;
                                $enrollment->discount_type = $order_item->discount_type;
                                $enrollment->net_original_price = $order_item->net_original_price;
                                $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                                $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                $enrollment->net_admission_fee = $order_item->net_admission_fee;
                                $enrollment->request_enrollment = $order_item->request_enrollment;


                                $enrollment->owner_user_id = $courseExits->user_id ?? null;
                                $enrollment->start_date = now();
                                $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                                $enrollment->save();
                            }
                            else{
                                $enrollment = new Enrollment();
                                $enrollment->order_id = $getPaymentData->id;
                                $enrollment->user_id = $user_id;
                                $enrollment->course_id = $cart->course_id;

                                $enrollment->main_price = $order_item->unit_price;
                                $enrollment->price = $order_item->unit_price;
                                $enrollment->original_price = $order_item->original_price;
                                $enrollment->discount = $order_item->discount;
                                $enrollment->admission_fee = $order_item->admission_fee;
                                $enrollment->discount_type = $order_item->discount_type;
                                $enrollment->net_original_price = $order_item->net_original_price;
                                $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                                $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                $enrollment->net_admission_fee = $order_item->net_admission_fee;
                                $enrollment->request_enrollment = $order_item->request_enrollment;


                                $enrollment->owner_user_id = $courseExits->user_id ?? null;
                                $enrollment->start_date = now();
                                $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                                $enrollment->save();
                                Log::info("NEW ENROLLMENT BEING CREATED IN (checkoutFaysalApi) IN API/STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                Log::info("NEW ENROLLMENT DETAILS IN (checkoutFaysalApi) IN API/STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                            }
                            
                       }

                    // $this->addAffiliateHistory($cart,$getPaymentData,$order_item);
                }
                elseif ($cart->product_id) {
                    $order_item = new Order_item();
                    $order_item->order_id = $getPaymentData->id;
                    $order_item->user_id = $user_id;
                    $order_item->type = 2;
                    $order_item->product_id = $cart->product_id;
                    $order_item->receiver_info = $cart->receiver_info;
                    $order_item->owner_user_id = $cart->product ? $cart->product->user_id : null;
                    $order_item->unit_price = $cart->price;
                    $order_item->unit = $cart->quantity;

                    $order_item->original_price = $cart->original_price;
                    $order_item->discount = $cart->discount;
                    $order_item->admission_fee = $cart->admission_fee;
                    $order_item->discount_type = $cart->discount_type;
                    $order_item->net_original_price = $cart->net_original_price;
                    $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
                    $order_item->discount_admission_fee = $cart->discount_admission_fee;
                    $order_item->net_admission_fee = $cart->net_admission_fee;
                     $order_item->request_enrollment = $cart->request_enrollment;
                        $order_item->trial_id = $cart->trial_id;
                        $order_item->is_monthly_cron = $cart->is_monthly_cron;
                        $order_item->is_donation = $cart->is_donation;
                        $order_item->students_count = $cart->students_count;
                        $order_item->cart_type = $cart->created_by_type;
                        $order_item->created_by_type = $cart->created_by_type;

                        $order_item->month = $cart->month;
                        $order_item->year = $cart->year;
                        $order_item->cart_id = $cart->id;

                    $adminCommission = get_option('sell_commission');
                    if ($adminCommission) {
                        $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->sell_commission = $adminCommission;
                    } else {
                        $order_item->owner_balance = $cart->price;
                    }

                    $order_item->save();

                }
            }

            // if($cart_id != null){
            //     $getCartData = CartManagement::where('id',$cart_id)->first();
            //     if($getCartData){
            //         $getCartData->delete();
            //     }
            // }
            // else{
            //       CartManagement::whereUserId(@Auth::id())
            //         ->where('created_by_type',1)->delete();
            // }

                $target_url = route('student_payment.completed');
                $text = __("Student payment has been accepted by faysal bank.");
                $this->send($text, 1, $target_url, null);

                //Email Send Code

                if($cart_id != null){
                    // $email_sent = $this->sendPaymentEmail($getPaymentData->id,$cart_id);
                    $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                }
                else{
                    $CART =CartManagement::whereUserId($user_id)
                    ->where('created_by_type',1)->first();
                    if($CART){
                        // $email_sent = $this->sendPaymentEmail($getPaymentData->id,$CART->id);
                        $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                    }
                    else{
                        // $email_sent = $this->sendPaymentEmail($getPaymentData->id);
                        $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                    }            
                }

                // $email_sent = $this->sendPaymentEmail($getPaymentData->id);

      
                 $successMsg = $successMsg;
                 $result_array = [
                        'status' => true,
                        'message' => $successMsg,
                        'data' => $getPaymentData
                    ];
                return response()->json($result_array);
          
        }
    }
}




public function failureFaysalApi(Request $request){
        $result_array = array();
        $errCode = $request->err_code;
		$errMsg = $request->err_msg;
        // if(isset($errCode) && isset($errMsg)){
		if(isset($errCode)){
			if ($errCode == '001') { 
                $errMsg2 = "Transaction Failed. Message: " . $errMsg;
                $result_array = [
                        'status' => false,
                        'errCode' => $errCode,
                        'errMsg' => $errMsg2,
                ];
                return response()->json($result_array,500);
			}
            else if($errCode == '90'){
                $errMsg2 = "Transaction Failed. Message: " . $errMsg;
                $result_array = [
                        'status' => false,
                        'errCode' => $errCode,
                        'errMsg' => $errMsg2,
                ];
                return response()->json($result_array,500);
            }
            else{
                 $result_array = [
                        'status' => false,
                        'message' => __('Invalid Error Code'),
                ];
                return response()->json($result_array,500);
            }
	    }
        else{
                $result_array = [
                        'status' => false,
                        'message' => __('Undefined Error Code'),
                ];
                return response()->json($result_array,500);
        }
}



public function failureFaysalApiAll(Request $request){
        $result_array = array();
        $errCode = $request->err_code;
		$errMsg = $request->err_msg;
        // if(isset($errCode) && isset($errMsg)){
		if(isset($errCode)){
			if ($errCode == '001') { 
                $errMsg2 = "Transaction Failed. Message: " . $errMsg;
                $result_array = [
                        'status' => false,
                        'errCode' => $errCode,
                        'errMsg' => $errMsg2,
                ];
                return response()->json($result_array,500);
			}
            else if($errCode == '90'){
                $errMsg2 = "Transaction Failed. Message: " . $errMsg;
                $result_array = [
                        'status' => false,
                        'errCode' => $errCode,
                        'errMsg' => $errMsg2,
                ];
                return response()->json($result_array,500);
            }
            else{
                 $result_array = [
                        'status' => false,
                        'message' => __('Invalid Error Code'),
                ];
                return response()->json($result_array,500);
            }
	    }
        else{
                $result_array = [
                        'status' => false,
                        'message' => __('Undefined Error Code'),
                ];
                return response()->json($result_array,500);
        }
}


  public function sendPaymentEmail($payment_id = null,$payment_order_id = null){
     
        $receipt_number = rand(0,9999999);
        $getPaymentData = Order_item::where('order_id',$payment_id)->first();
        $getItems =Order_item::where('order_id',$payment_id)->get();
        if(!$getItems->isEmpty()){
            foreach ($getItems as $key => $item) {
                if($item->course){
                    $name = $item->course?->title;
                }
                else if($item->product){
                    $name = $item->product?->title;
                }
                else{
                    $name = 'Item';
                }


                if($item->month && $item->year){
                    $month = $item->month;
                    $Updated_month = date('F', strtotime("2024-$month-01"));
                    $year = $item->year;
                }
                else if($item->cart){
                    $month = $item->cart?->month;
                    $Updated_month = date('F', strtotime("2024-$month-01"));
                    $year = $item->cart?->year;
                }
                else{
                    $month = date('M',strtotime($item->created_at));
                    $Updated_month = date('F', strtotime("2024-$month-01"));
                    $year = date('Y',strtotime($item->created_at));
                }  

                $monthYear = $Updated_month.'/'.$year;

                $symbol='RS ';
                $amount1 =(int)$item->unit_price;
               
                $Amount2 = $symbol.$amount1;
                $itemsArray[] = [
                    'name' => $name,
                    'monthYear' => $monthYear,
                    'amount' => $Amount2,
                ];
            }
        }

        if($getPaymentData->order->payment_method == 'meezan_bank'){
            $data2['order_payment_date'] = $getPaymentData->order->meezan_payment_date;
            $data2['payment_source'] = $getPaymentData->order->meezan_payment_source;
            $data2['payment_method'] = $getPaymentData->order->payment_method;
        }
        else if($getPaymentData->order->payment_method == 'faysal_bank'){
            $data2['order_payment_date'] = $getPaymentData->order->faysal_payment_date;
            $data2['payment_source'] = $getPaymentData->order->faysal_payment_source;
            $data2['payment_method'] = $getPaymentData->order->payment_method;
        }
        else{
            $data2['order_payment_date'] = date('Y-m-d h:i:s');
            $data2['payment_source'] = 'Card';
            $data2['payment_method'] = $getPaymentData->order?->payment_method;
        }
        if($payment_order_id != null){
            $ID = $payment_order_id;
        }
        else{
            // $ID = $getPaymentData->order_id; 
            $ID = 'LMS-P-'.$getPaymentData->order_id; 
        }

        $data2['result'] = $getPaymentData;
        $data2['receipt_number_new']  = $receipt_number;
        $toEmail = $getPaymentData->user?->email;
        if(get_option('app_email')){
            $data2['app_email']  = get_option('app_email');
        }
        else{
            $data2['app_email']  = env('MAIL_FROM_ADDRESS');
        }
        if(get_option('app_contact_number')){
            $data2['app_contact_number']  = get_option('app_contact_number');
        }
        else{
            $data2['app_contact_number']  = env('APP_CONTACT_NUMBER');
        }
        if(get_option('facebook_url') && get_option('facebook_url') != '#'){
            $data2['facebook_url']  = get_option('facebook_url');
        }
        else{
            $data2['facebook_url']  = env('FACEBOOK_URL');
        }
        if(get_option('twitter_url') && get_option('twitter_url') != '#'){
            $data2['twitter_url']  = get_option('twitter_url');
        }
        else{
            $data2['twitter_url']  = env('TWITTER_URL');
        }
        if(get_option('instagram_url') && get_option('instagram_url') != '#'){
            $data2['instagram_url']  = get_option('instagram_url');
        }
        else{
            $data2['instagram_url']  = env('INSTAGRAM_URL');
        }
        if(get_option('youtube_url') && get_option('youtube_url') != '#'){
            $data2['youtube_url']  = get_option('youtube_url');
        }
        else{
            $data2['youtube_url']  = env('YOUTUBE_URL');
        }
        if(get_option('app_location')){
            $data2['app_location']  = get_option('app_location');
        }
        else{
            $data2['app_location']  = env('APP_LOCATION');
        }
        $mainArray = array();
        $mainArray = [
            'data2' => $data2,
            'Items' => $itemsArray,
            'ID' => $ID,
        ];
        // $view = view('frontend.student.emails.completed_payment',compact('data2'));
     
        $adminEmail = get_option('admin_email');
        $FinancialEmail = get_option('financial_email');
      
      
     


        // multi-select JSON emails
        $otherEmails = json_decode(get_option('other_emails'), true) ?? [];
    
        // build CC list safely
        $ccEmails = array_filter(array_unique(array_merge(
            [$adminEmail, $FinancialEmail],
            $otherEmails
        )));

        Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$ccEmails) {
                $message->to($toEmail)
                ->subject('Payment Received | CEF Online');

                 // add CC only if exists
                if (!empty($ccEmails)) {
                    $message->cc($ccEmails);
                }
        });

        // if($adminEmail && $FinancialEmail){
        //     Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$adminEmail,$FinancialEmail) {
        //         $message->to($toEmail)
        //                 ->cc([
        //                     $adminEmail,
        //                     $FinancialEmail,
        //                     'quranedu.online@cef.org.pk',
                            
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else if($adminEmail){
        //      Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$adminEmail) {
        //         $message->to($toEmail)
        //                 ->cc([
        //                     $adminEmail,
        //                     'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else if($FinancialEmail){
        //      Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$FinancialEmail) {
        //         $message->to($toEmail)
        //                 ->cc([
        //                     $FinancialEmail,
        //                     'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else{
        //     Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail) {
        //         $message->to($toEmail)
        //                   ->cc([
                         
        //                     'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        
        return true;
}






   // 'bankalerts@cef.org.pk',
    // 'quranedu.online@cef.org.pk',

}


