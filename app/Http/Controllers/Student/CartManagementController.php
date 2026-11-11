<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Logger;
use Illuminate\Support\Facades\Log;
use App\Mail\EmailNotify;
use App\Http\Services\EmailSendService;
use App\Http\Services\Payment\BasePaymentService;
use App\Models\Addon\Product\Product;
use App\Models\AffiliateHistory;
use App\Models\AffiliateRequest;
use App\Models\Bank;
use App\Models\BookingHistory;
use App\Models\Bundle;
use App\Models\TrialClass;
use App\Models\BundleCourse;
use App\Models\CartManagement;
use Illuminate\Support\Facades\Mail;
use App\Models\City;
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
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\Withdraw;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Session;
use Redirect;
use Razorpay\Api\Api;
use Exception;
use Mollie\Laravel\Facades\Mollie;

class CartManagementController extends Controller
{
    use ImageSaveTrait, General, SendNotification;

    private $_api_context;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $logger;

    public function __construct()
    {
       
        /** Mollie api key **/
        if (env('MOLLIE_KEY')) {
            Mollie::api()->setApiKey(env('MOLLIE_KEY'));
        }

        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret']
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);

        $this->logger = new Logger();
    }



    public function downloadCartVoucher($id){
        $cart = CartManagement::findOrFail($id);
    
        if (!$cart->invoice_pdf_path || !file_exists(public_path($cart->invoice_pdf_path))) {
            return redirect()->back()->with('error', 'Invoice file not found.');
        }
    
        $filePath = public_path($cart->invoice_pdf_path);
        $fileName = basename($filePath);
        
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }


    public function cartList()
    {
      
        $data['pageTitle'] = 'Cart';
      
        // Start:: Check course, bundle, consultation exists or not. If not exists, Delete it.
        // $carts = CartManagement::whereUserId(@Auth::id())->with('course.promotionCourse.promotion', 'bundle', 'consultationSlot.user.instructor')->get();
        $carts = CartManagement::whereUserId(auth()->id())->with('course.promotionCourse.promotion', 'bundle', 'consultationSlot.user.instructor')
        ->where('created_by_type',1)
        // ->where('request_enrollment',0)
        ->get();

        // dd($carts);
        if(get_option('subscription_mode')){
            $subscriptionPurchaseEnable = true;
        }
        else{
            $subscriptionPurchaseEnable = false;
        }
        $cartFinalPrice  = 0;
        foreach ($carts as $cart) {
            $cartFinalPrice += $cart->price;
            if ($cart->course_id && !$cart->course) {
                $cart->delete();
            } 
            elseif ($cart->bundle_id && !$cart->bundle) {
                $cart->delete();
            } 
            elseif ($cart->consultation_slot_id && !$cart->consultationSlot) {
                $cart->delete();
            } 
            elseif ($cart->product_id && !$cart->product) {
                $cart->delete();
            }
            else{
                if(!$cart->is_subscription_enable){
                    $subscriptionPurchaseEnable = false;
                }

                // Start:: Course & Promotion Course Check or not
                // if ($cart->course) {
                //     $course = $cart->course;
                //     $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
                //     $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
                //     $percentage = @$course->promotionCourse->promotion->percentage;
                //     $promotion_discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);

                //     if (now()->gt($startDate) && now()->lt($endDate)) {
                //         $cart->promotion_id = @$course->promotionCourse->promotion->id;
                //         $cart->price = $promotion_discount_price;
                //     } else {
                //         $cart->main_price = $course->price;
                //         $cart->price = $course->price;
                //     }

                // }
                // End:: Course & Promotion Course Check or not

                //Start:: Consultation Check
                elseif ($cart->consultationSlot) {
                    $consultationSlot = $cart->consultationSlot;
                    // User role check
                    if($consultationSlot->user->role == USER_ROLE_INSTRUCTOR){
                        $relation = 'instructor';
                    }elseif($consultationSlot->user->role == USER_ROLE_ORGANIZATION){
                        $relation = 'organization';
                    }
                    if ($consultationSlot) {
                        $consultationArray = array();
                        $newConsultationDataArray = [
                            'instructor_user_id' => $consultationSlot->user_id,
                            'student_user_id' => Auth::id(),
                            'consultation_slot_id' => $consultationSlot->id,
                            'date' => $cart->consultation_date,
                            'day' => $consultationSlot->day,
                            'time' => $consultationSlot->time,
                            'duration' => $consultationSlot->duration,
                            'status' => 0,
                        ];

                        $consultationArray[] = $newConsultationDataArray;
                        $cart->consultation_details = $consultationArray;

                        $hour_duration = $consultationSlot->hour_duration;
                        $minute_duration = $consultationSlot->minute_duration;

                        $hourly_rate = @$consultationSlot->user->$relation->hourly_rate;
                        $minuteCost = 0;
                        if ($minute_duration > 0) {
                            $minuteCost = ($hourly_rate / (60 / $minute_duration));
                        }
                        $totalCost = ($hour_duration * $hourly_rate) + $minuteCost;

                        $cart->main_price = $totalCost;
                        $cart->price = $totalCost;
                    }
                }
                //End:: Consultation Check

                //Start:: Bundle Offer Check
                if ($cart->bundle) {
                    $cart->main_price = $cart->bundle->price;
                    $cart->price = $cart->bundle->price;
                }
                //End:: Bundle Offer Check

                if ($cart->product_id) {
                    $cart->main_price = $cart->product->current_price * $cart->quantity;
                    $cart->price = $cart->product->current_price * $cart->quantity;
                }


                 $cart->month = date('m');
                $cart->year = date('Y');
                // $cart->coupon_id = null;
                // $cart->discount = 0;
                $cart->save();
            }
        }
        $data['cartFinalPrice'] = (int)$cartFinalPrice;
 
        if($subscriptionPurchaseEnable){
            $subscriptionPurchaseEnable = hasLimit(PACKAGE_RULE_COURSE, count($carts));
        }
        $data['subscriptionPurchaseEnable'] = $subscriptionPurchaseEnable;
        // $data['carts'] = CartManagement::whereUserId(@Auth::id())->get();
        $data['carts'] = CartManagement::whereUserId(auth()->id())
        ->where('created_by_type',1)
        // ->where('request_enrollment',0)
        ->get();

        return view('frontend.student.cart.cart-list', $data);
    }

    public function applyCoupon(Request $request)
    {
        DB::beginTransaction();
        if (!Auth::check()) {
            $response['msg'] = __("You need to login first!");
            $response['status'] = 401;
            return response()->json($response);
        }

        if (!$request->coupon_code) {
            $response['msg'] = __("Enter coupon code!");
            $response['status'] = 404;
            return response()->json($response);
        }


        if ($request->id) {
           
            $cart = CartManagement::where('id',$request->id)->first();
            if (!$cart) {
                $response['msg'] = __("Cart item not found!");
                $response['status'] = 404;
                return response()->json($response);
            }

            $coupon = Coupon::where('coupon_code_name', $request->coupon_code)->where('start_date', '<=', Carbon::now()->format('Y-m-d'))->where('end_date', '>=', Carbon::now()->format('Y-m-d'))->first();

            if ($coupon) {
                if ($cart->price < $coupon->minimum_amount) {
                    $response['msg'] = "Minimum " . get_currency_code() . $coupon->minimum_amount . " need to buy for use this coupon!";
                    $response['status'] = 402;
                    return response()->json($response);
                }
            }
            if (!$coupon) {
                $response['msg'] = __("Invalid coupon code entered. Please enter correct one!");
                $response['status'] = 404;
                return response()->json($response);
            }



          
            if (CartManagement::where('user_id',@Auth::id())->where('coupon_id',$coupon->id)->count() > 0) {
                $response['msg'] = __("You've already used this coupon!");
                $response['status'] = 402;
                return response()->json($response);
            }

            
          
            

            $discount_price = round(((float) $cart->price * (float) $coupon->percentage) / 100, 2);

            if ($coupon->coupon_type == 1) {
                $cart->price = round((float) $cart->price - $discount_price, 2);
                $cart->discount = $discount_price;
                $cart->coupon_id = $coupon->id;
                $cart->save();

                $carts = CartManagement::whereUserId(@Auth::id())->get();
                $response['msg'] = __("Coupon Applied");
                $response['price'] = $cart->price;
                $response['discount'] = $cart->discount;
                $response['total'] = get_number_format($carts->sum('price'));
                $response['platform_charge'] = get_platform_charge($carts->sum('price'));
                $response['grand_total'] = get_number_format($carts->sum('price') + $carts->sum('shipping_charge') + get_platform_charge($carts->sum('price')));
                $response['status'] = 200;
                return response()->json($response);
            } 
            elseif ($coupon->coupon_type == 2) {
                if ($cart->course) {
                    $user_id = $cart->course->user_id;
                } else if($cart->product_id){
                    $user_id = $cart->product->user_id;
                }else{
                    $user_id = NULL;
                }

                $couponInstructor = CouponInstructor::where('coupon_id', $coupon->id)->where('user_id', $user_id)->orderBy('id', 'desc')->first();
                if ($couponInstructor) {

                    $cart->price = round((float) $cart->price - $discount_price, 2);
                    $cart->discount = $discount_price;
                    $cart->coupon_id = $coupon->id;
                    $cart->save();

                    $carts = CartManagement::whereUserId(@Auth::id())->get();
                    $response['msg'] = __("Coupon Applied");
                    $response['price'] = $cart->price;
                    $response['discount'] = $cart->discount;
                    $response['total'] = get_number_format($carts->sum('price'));
                    $response['platform_charge'] = get_platform_charge($carts->sum('price'));
                    $response['grand_total'] = get_number_format($carts->sum('price') + $carts->sum('shipping_charge') + get_platform_charge($carts->sum('price')));
                    $response['status'] = 200;
                    return response()->json($response);
                } else {
                    $response['msg'] = __("Invalid coupon code!");
                    $response['status'] = 404;
                    return response()->json($response);
                }
            } 
            elseif ($coupon->coupon_type == 3) {
                $couponCourse = CouponCourse::where('coupon_id', $coupon->id)->where('course_id', $cart->course_id)->orderBy('id', 'desc')->first();
                if ($couponCourse) {
                    $cart = CartManagement::where('id',$request->id)->first();
                    $cart->price = round((float) $cart->price - $discount_price, 2);
                    $cart->discount = $discount_price;
                    $cart->coupon_id = $coupon->id;
                 
                    $cart->save();



                 

                 


                    $carts = CartManagement::whereUserId(@Auth::id())->get();
                    $response['msg'] = __("Coupon Applied");
                    $response['price'] = $cart->price;
                    $response['discount'] = $cart->discount;
                    $response['total'] = get_number_format($carts->sum('price'));
                    $response['platform_charge'] = get_platform_charge($carts->sum('price'));
                    $response['grand_total'] = get_number_format($carts->sum('price') + $carts->sum('shipping_charge') + get_platform_charge($carts->sum('price')));
                    $response['status'] = 200;


                    
                    
                    if($cart->price == 0.00  || $cart->price == 0){
                      
                        try {
                            $courseExits2 = Course::where('id',$cart->course_id)->first();
                            $order = new Order();
                            $order->user_id = @Auth::id();
                            $order->order_number = rand(100000, 999999);
                            $order->payment_status = 'free';
                            $order->delivery_status = 1;
                            $order->save();

                            $order_item = new Order_item();
                            $order_item->order_id = $order->id;
                            $order_item->user_id = @Auth::id();
                            $order_item->course_id = $cart->course_id;
                            $order_item->owner_user_id = $courseExits2->user_id ?? null;
                            $order_item->unit_price = 0;
                            $order_item->admin_commission = 0;
                            $order_item->owner_balance = 0;
                            $order_item->sell_commission = 0;
                            $order_item->delivery_status = 3;
                            $order_item->month = date('m');
                            $order_item->year = date('Y');
                            $order_item->save();

                            $enrollment = Enrollment::where('user_id',@Auth::id())->where('course_id',$cart->course_id)->orderBy('id','DESC')->first();
                            if($enrollment){
                                $enrollment->order_id = $order->id;
                                $enrollment->user_id = @Auth::id();
                                $enrollment->course_id = $cart->course_id;
                                $enrollment->owner_user_id = $courseExits2->user_id ?? null;
                                $enrollment->start_date = now();
                                $enrollment->end_date = ($courseExits2->access_period) ? Carbon::now()->addDays($courseExits2->access_period) : MAX_EXPIRED_DATE;

                                $enrollment->main_price = $order_item->unit_price;
                                $enrollment->price = $order_item->unit_price;
                                $enrollment->original_price = $order_item->original_price;
                                $enrollment->discount = $order_item->discount;
                                $enrollment->admission_fee = $order_item->admission_fee;
                                $enrollment->discount_type = $order_item->discount_type ?? 0;
                                $enrollment->net_original_price = $order_item->net_original_price;
                                $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee ?? 0;
                                $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                $enrollment->net_admission_fee = $order_item->net_admission_fee;


                                $enrollment->save();
                            }
                            else{
                                $enrollment = new Enrollment();
                                $enrollment->order_id = $order->id;
                                $enrollment->user_id = @Auth::id();
                                $enrollment->course_id = $cart->course_id;
                                $enrollment->owner_user_id = $courseExits2->user_id ?? null;
                                $enrollment->start_date = now();
                                $enrollment->end_date = ($courseExits2->access_period) ? Carbon::now()->addDays($courseExits2->access_period) : MAX_EXPIRED_DATE;

                                $enrollment->main_price = $order_item->unit_price;
                                $enrollment->price = $order_item->unit_price;
                                $enrollment->original_price = $order_item->original_price;
                                $enrollment->discount = $order_item->discount;
                                $enrollment->admission_fee = $order_item->admission_fee;
                                $enrollment->discount_type = $order_item->discount_type ?? 0;
                                $enrollment->net_original_price = $order_item->net_original_price;
                                $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee ?? 0;
                                $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                                $enrollment->net_admission_fee = $order_item->net_admission_fee;


                                $enrollment->save();
                                Log::info("NEW ENROLLMENT BEING CREATED IN (applyCoupon) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                Log::info("NEW ENROLLMENT DETAILS IN (applyCoupon) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                            }
                            DB::commit();

                            CartManagement::where('id',$request->id)->delete();
        

                           
                            $response['msg'] = __("Coupon Applied And Course Added To Learning List.");
                         
                            // return ['status' => true,'data' => $order];


                        }
                        catch (\Exception $e) {
                            DB::rollBack();
                            $response['msg'] = __('Cannot Apply Coupon and Create Order', $e->getMessage());
                            $response['status'] = 404;
                            return response()->json($response);
                        }
                    }

                     

                    
                       DB::commit();
                    return response()->json($response);
                } else {
                     DB::rollBack();
                    $response['msg'] = __("Invalid coupon code for selected course!");
                    $response['status'] = 404;
                    return response()->json($response);
                }
            } 
            else {
                 DB::rollBack();
                $response['msg'] = __("Invalid coupon code!");
                $response['status'] = 404;
                return response()->json($response);
            }
        } else {
             DB::rollBack();
            $response['msg'] = __("Cart item not found!");
            $response['status'] = 404;
            return response()->json($response);
        }
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            $response['msg'] = __("You need to login first!");
            $response['status'] = 401;
            Session::put('type', '2');
            return response()->json($response);
        }
    
        if ($request->course_id) {
            if($request->is_gift){
                $rules = [
                    'receiver_info.receiver_name' => ['required', 'string','min:2', 'max:100'],
                    'receiver_info.receiver_email' => ['required', 'string', 'email', 'max:255'],
                ];

                $request->validate($rules,[
                    'receiver_info.receiver_name.required' => __('Name field is required'),
                    'receiver_info.receiver_name.min:2' => __('Name field should be at least 2'),
                    'receiver_info.receiver_name.max:100' => __('Name field should be max 100'),
                    'receiver_info.receiver_email.required' =>  __('Email field is required'),
                    'receiver_info.receiver_email.email' =>  __('Email must be valid'),
                ]);
            }
        }
        DB::beginTransaction();
        try {
            if ($request->course_id) {
                $enrollment = Enrollment::where(['course_id' => $request->course_id , 'user_id' => Auth::user()->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                if($request->is_gift){
                    $giftUser = User::where('email', $request->receiver_info['receiver_email'])->first();

                    if(!is_null($giftUser)){
                        $giftEnrollment = Enrollment::where(['course_id' => $request->course_id , 'user_id' => $giftUser->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                        if(!is_null($giftEnrollment) && $giftEnrollment->user_id == $giftUser->id){
                            $response['msg'] = __("This user already have this course!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    }
                    else{
                        $response['msg'] = __("This user doesn't exist. Please try another");
                        $response['status'] = 404;
                        DB::rollBack();
                        return response()->json($response);
                    }
                }

                if ($enrollment && !$request->is_gift) {
                    $order = Order::find($enrollment->order_id);
                    if ($order) {
                        if ($order->payment_status == 'due') {
                            Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                                $q->delete();
                            });
                            $order->delete();
                        } elseif ($order->payment_status == 'pending') {
                            $response['msg'] = __("You've already request the course & status is pending!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $response['msg'] = __("You've already purchased the course!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    } else {
                        $response['msg'] = __("Something is wrong! Try again.");
                        $response['status'] = 402;
                        DB::rollBack();
                        return response()->json($response);
                    }
                }

                $ownCourseCheck = CourseInstructor::where('course_id', $request->course_id)->where('instructor_id', auth()->id())->first();

                if ($ownCourseCheck && $request->is_gift) {
                    $courseExits = Course::find($request->course_id);
                    $order = new Order();
                    $order->user_id = Auth::user()->id;
                    $order->order_number = rand(100000, 999999);
                    $order->payment_status = 'free';
                    $order->save();

                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::user()->id;
                    $order_item->receiver_info = $request->receiver_info;
                    $order_item->course_id = $courseExits->id;
                    $order_item->owner_user_id = $courseExits->user_id ?? null;
                    $order_item->unit_price = 0;
                    $order_item->admin_commission = 0;
                    $order_item->owner_balance = 0;
                    $order_item->sell_commission = 0;
                    $order_item->save();

                    setEnrollment($order_item);

                    $response['msg'] = __("Your course has been gifted for free");
                    $response['status'] = 200;
                    DB::commit();
                    return response()->json($response);
                }
                elseif ($ownCourseCheck) {
                    $response['msg'] = __("This is your course. No need to add to cart.");
                    $response['status'] = 402;
                    DB::rollBack();
                    return response()->json($response);
                }

                $courseExits = Course::find($request->course_id);
                if (!$courseExits) {
                    $response['msg'] = __("Course not found!");
                    $response['status'] = 404;
                    DB::rollBack();
                    return response()->json($response);
                }
            }

            if ($request->product_id) {
                $productExits = Product::find($request->product_id);
                if (!$productExits) {
                    $response['msg'] = __("Product not found!");
                    $response['status'] = 404;
                    DB::rollBack();
                    return response()->json($response);
                }
            }

            if ($request->bundle_id) {
                $bundleExits = Bundle::find($request->bundle_id);
                if (!$bundleExits) {
                    $response['msg'] = __("Bundle not found!");
                    $response['status'] = 404;
                    DB::rollBack();
                    return response()->json($response);
                }

                $ownBundleCheck = Bundle::whereUserId(Auth::user()->id)->where('id', $request->bundle_id)->first();
                if ($ownBundleCheck) {
                    $response['msg'] = __("This is your bundle offer. No need to add to cart.");
                    $response['status'] = 402;
                    DB::rollBack();
                    return response()->json($response);
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
                            $response['msg'] = __("You've already request this bundle & status is pending!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $response['msg'] = __("You've already purchased this bundle!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    } else {
                        $response['msg'] = __("Something is wrong! Try again.");
                        $response['status'] = 402;
                        DB::rollBack();
                        return response()->json($response);
                    }
                }
            }

            //Start:: Cart Management
            if ($request->course_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereCourseId($request->course_id)->first();
            } 
            elseif ($request->product_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereProductId($request->product_id)->first();
            } 
            elseif ($request->bundle_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereBundleId($request->bundle_id)->first();
            }

            
            if ($cartExists && is_null($request->product_id)) {
                $response['msg'] = __("Already added to cart!");
                $response['status'] = 409;
                DB::rollBack();
                return response()->json($response);
            }

            if ($request->course_id) {
                if ($courseExits->learner_accessibility == 'free') {
                    $order = new Order();
                    $order->user_id = Auth::user()->id;
                    $order->order_number = rand(100000, 999999);
                    $order->payment_status = 'free';
                    $order->delivery_status = 1;
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
                    $order_item->delivery_status = 3;
                    $order_item->month = date('m');
                    $order_item->year = date('Y');
                    // $order_item->request_enrollment = $cart->request_enrollment;
                    // $order_item->trial_id = $cart->trial_id;
                    // $order_item->is_monthly_cron = $cart->is_monthly_cron;
                    // $order_item->cart_type = $cart->created_by_type;
                    // $order_item->created_by_type = $cart->created_by_type;
                    // $order_item->month = $cart->month;
                    // $order_item->year = $cart->year;
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
                        $enrollment->discount_type = $order_item->discount_type ?? 0;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee ?? 0;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;


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
                        $enrollment->discount_type = $order_item->discount_type ?? 0;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee ?? 0;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;


                        $enrollment->save();
                        Log::info("NEW ENROLLMENT BEING CREATED IN (addToCart) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                        Log::info("NEW ENROLLMENT DETAILS IN (addToCart) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                    }
                   

                    $response['msg'] = __("Free Course added to your dashboard!");
                    $response['status'] = 200;
                    DB::commit();
                    return response()->json($response);
                }
            }

            $quantity = $request->quantity;
            $cart = NULL;
            if($request->product_id){
                $cart = CartManagement::where('product_id', $request->product_id)->where('user_id', auth()->id())->first();
            }

            if(is_null($cart)){
                $cart = new CartManagement();
            }else{
                $quantity = ($request->quantity ?? 0) + $cart->quantity;
            }

              
            if ($request->product_id) {
                if($productExits && $productExits->quantity < 1){
                    CartManagement::where('product_id', $request->product_id)->delete();
                    $response['msg'] = __("Product is out of stock");
                    $response['status'] = 422;
                    return response()->json($response);
                }
                elseif($productExits && $productExits->quantity < $quantity){
                    $response['msg'] = __("Quantity is out of stock");
                    $response['status'] = 422;
                    return response()->json($response);
                }
            }

            $cart->user_id = Auth::user()->id;
            $cart->receiver_info = $request->receiver_info ?? [];
            $cart->course_id = $request->course_id;
            $cart->product_id = $request->product_id;
            $cart->quantity = $quantity ?? 0;
            $cart->bundle_id = $request->bundle_id;
            $cart->month = date('m');
            $cart->year = date('Y');

            if($request->course_id){
                $course = Course::findOrFail($request->course_id);
                $cart->is_subscription_enable = $course->is_subscription_enable;
            }else if($request->bundle_id){
                $bundle = Bundle::findOrFail($request->bundle_id);
                $cart->is_subscription_enable = $bundle->is_subscription_enable;
            }

            if ($request->course_id) {
                $cart->main_price = $courseExits->price;
                $cart->price = $courseExits->price;
            }

            if ($request->bundle_id) {
                $bundleCourses = BundleCourse::where('bundle_id', $request->bundle_id)->pluck('course_id')->toArray();
                $cart->bundle_course_ids = $bundleCourses;
                $cart->main_price = $bundleExits->price;
                $cart->price = $bundleExits->price;
            }
            if ($request->product_id) {
                $cart->main_price = $productExits->current_price * $quantity;
                $cart->price = $productExits->current_price * $quantity;
                if($productExits->type == PHYSICAL_PRODUCT){
                    $cart->shipping_charge = $productExits->shipping_charge;
                }
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
            $response['msg'] = __("Added to cart");
            $response['msgInfoChange'] = __("Added to cart");
            $response['status'] = 200;
            //End:: Cart Management
            DB::commit();
            return response()->json($response);
        } catch (\Exception $e) {
            Log::info("Error In Student AddtoCart Function");
            Log::info($e);
            DB::rollBack();
            $response['msg'] = __("Something is wrong! Try again.");
            $response['status'] = 402;
            return response()->json($response);
        }
    }



       public function addToCartWithCheckout(Request $request)
    {
        if (!Auth::check()) {
            $response['msg'] = __("You need to login first!");
            $response['status'] = 401;
            Session::put('type', '2');
            return response()->json($response);
        }
    
        $location = $request->location;
        if ($request->course_id) {
            if($request->is_gift){
                $rules = [
                    'receiver_info.receiver_name' => ['required', 'string','min:2', 'max:100'],
                    'receiver_info.receiver_email' => ['required', 'string', 'email', 'max:255'],
                ];

                $request->validate($rules,[
                    'receiver_info.receiver_name.required' => __('Name field is required'),
                    'receiver_info.receiver_name.min:2' => __('Name field should be at least 2'),
                    'receiver_info.receiver_name.max:100' => __('Name field should be max 100'),
                    'receiver_info.receiver_email.required' =>  __('Email field is required'),
                    'receiver_info.receiver_email.email' =>  __('Email must be valid'),
                ]);
            }
        }
        DB::beginTransaction();
        try {
            if ($request->course_id) {
                $enrollment = Enrollment::where(['course_id' => $request->course_id , 'user_id' => Auth::user()->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                if($request->is_gift){
                    $giftUser = User::where('email', $request->receiver_info['receiver_email'])->first();

                    if(!is_null($giftUser)){
                        $giftEnrollment = Enrollment::where(['course_id' => $request->course_id , 'user_id' => $giftUser->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                        if(!is_null($giftEnrollment) && $giftEnrollment->user_id == $giftUser->id){
                            $response['msg'] = __("This user already have this course!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    }
                    else{
                        $response['msg'] = __("This user doesn't exist. Please try another");
                        $response['status'] = 404;
                        DB::rollBack();
                        return response()->json($response);
                    }
                }

                if ($enrollment && !$request->is_gift) {
                    $order = Order::find($enrollment->order_id);
                    if ($order) {
                        if ($order->payment_status == 'due') {
                            Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                                $q->delete();
                            });
                            $order->delete();
                        } elseif ($order->payment_status == 'pending') {
                            $response['msg'] = __("You've already request the course & status is pending!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $response['msg'] = __("You've already purchased the course!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    } else {
                        $response['msg'] = __("Something is wrong! Try again.");
                        $response['status'] = 402;
                        DB::rollBack();
                        return response()->json($response);
                    }
                }

                $ownCourseCheck = CourseInstructor::where('course_id', $request->course_id)->where('instructor_id', auth()->id())->first();

                if ($ownCourseCheck && $request->is_gift) {
                    $courseExits = Course::find($request->course_id);
                    $order = new Order();
                    $order->user_id = Auth::user()->id;
                    $order->order_number = rand(100000, 999999);
                    $order->payment_status = 'free';
                    $order->save();

                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::user()->id;
                    $order_item->receiver_info = $request->receiver_info;
                    $order_item->course_id = $courseExits->id;
                    $order_item->owner_user_id = $courseExits->user_id ?? null;
                    $order_item->unit_price = 0;
                    $order_item->admin_commission = 0;
                    $order_item->owner_balance = 0;
                    $order_item->sell_commission = 0;
                    $order_item->month = date('m');
                    $order_item->year = date('Y');
                    $order_item->save();

                    setEnrollment($order_item);

                    $response['msg'] = __("Your course has been gifted for free");
                    $response['status'] = 200;
                    DB::commit();
                    return response()->json($response);
                }
                elseif ($ownCourseCheck) {
                    $response['msg'] = __("This is your course. No need to add to cart.");
                    $response['status'] = 402;
                    DB::rollBack();
                    return response()->json($response);
                }

                $courseExits = Course::find($request->course_id);
                if (!$courseExits) {
                    $response['msg'] = __("Course not found!");
                    $response['status'] = 404;
                    DB::rollBack();
                    return response()->json($response);
                }
            }

            if ($request->product_id) {
                $productExits = Product::find($request->product_id);
                if (!$productExits) {
                    $response['msg'] = __("Product not found!");
                    $response['status'] = 404;
                    DB::rollBack();
                    return response()->json($response);
                }
            }

            if ($request->bundle_id) {
                $bundleExits = Bundle::find($request->bundle_id);
                if (!$bundleExits) {
                    $response['msg'] = __("Bundle not found!");
                    $response['status'] = 404;
                    DB::rollBack();
                    return response()->json($response);
                }

                $ownBundleCheck = Bundle::whereUserId(Auth::user()->id)->where('id', $request->bundle_id)->first();
                if ($ownBundleCheck) {
                    $response['msg'] = __("This is your bundle offer. No need to add to cart.");
                    $response['status'] = 402;
                    DB::rollBack();
                    return response()->json($response);
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
                            $response['msg'] = __("You've already request this bundle & status is pending!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $response['msg'] = __("You've already purchased this bundle!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    } else {
                        $response['msg'] = __("Something is wrong! Try again.");
                        $response['status'] = 402;
                        DB::rollBack();
                        return response()->json($response);
                    }
                }
            }

            //Start:: Cart Management
            if ($request->course_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereCourseId($request->course_id)->first();
            } 
            elseif ($request->product_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereProductId($request->product_id)->first();
            } 
            elseif ($request->bundle_id) {
                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereBundleId($request->bundle_id)->first();
            }

            
            if ($cartExists && is_null($request->product_id)) {
                $response['msg'] = __("Already added to cart!");
                $response['status'] = 409;
                DB::rollBack();
                return response()->json($response);
            }

            if ($request->course_id) {
                if ($courseExits->learner_accessibility == 'free') {
                    $order = new Order();
                    $order->user_id = Auth::user()->id;
                    $order->order_number = rand(100000, 999999);
                    $order->payment_status = 'free';
                    $order->delivery_status = 1;
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
                    $order_item->delivery_status = 3;
                    // $order_item->request_enrollment = $cart->request_enrollment;
                    // $order_item->trial_id = $cart->trial_id;
                    // $order_item->is_monthly_cron = $cart->is_monthly_cron;
                    // $order_item->cart_type = $cart->created_by_type;
                    // $order_item->created_by_type = $cart->created_by_type;
                    $order_item->month = date('m');
                    $order_item->year = date('Y');
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



                        $enrollment->save();
                        Log::info("NEW ENROLLMENT BEING CREATED IN (addToCartWithCheckout) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                        Log::info("NEW ENROLLMENT DETAILS IN (addToCartWithCheckout) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                    }
                

                    $response['msg'] = __("Free Course added to your dashboard!");
                    $response['status'] = 200;
                    DB::commit();
                    return response()->json($response);
                }
            }

            $quantity = $request->quantity;
            $cart = NULL;
            if($request->product_id){
                $cart = CartManagement::where('product_id', $request->product_id)->where('user_id', auth()->id())->first();
            }

            if(is_null($cart)){
                $cart = new CartManagement();
            }else{
                $quantity = ($request->quantity ?? 0) + $cart->quantity;
            }

              
            if ($request->product_id) {
                if($productExits && $productExits->quantity < 1){
                    CartManagement::where('product_id', $request->product_id)->delete();
                    $response['msg'] = __("Product is out of stock");
                    $response['status'] = 422;
                    return response()->json($response);
                }
                elseif($productExits && $productExits->quantity < $quantity){
                    $response['msg'] = __("Quantity is out of stock");
                    $response['status'] = 422;
                    return response()->json($response);
                }
            }

            $cart->user_id = Auth::user()->id;
            $cart->receiver_info = $request->receiver_info ?? [];
            $cart->course_id = $request->course_id;
            $cart->product_id = $request->product_id;
            $cart->quantity = $quantity ?? 0;
            $cart->bundle_id = $request->bundle_id;

            if($request->course_id){
                $course = Course::findOrFail($request->course_id);
                $cart->is_subscription_enable = $course->is_subscription_enable;
            }else if($request->bundle_id){
                $bundle = Bundle::findOrFail($request->bundle_id);
                $cart->is_subscription_enable = $bundle->is_subscription_enable;
            }

            if ($request->course_id) {
                $cart->main_price = $courseExits->price;
                $cart->price = $courseExits->price;
            }

            if ($request->bundle_id) {
                $bundleCourses = BundleCourse::where('bundle_id', $request->bundle_id)->pluck('course_id')->toArray();
                $cart->bundle_course_ids = $bundleCourses;
                $cart->main_price = $bundleExits->price;
                $cart->price = $bundleExits->price;
            }
            if ($request->product_id) {
                $cart->main_price = $productExits->current_price * $quantity;
                $cart->price = $productExits->current_price * $quantity;
                if($productExits->type == PHYSICAL_PRODUCT){
                    $cart->shipping_charge = $productExits->shipping_charge;
                }
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

         
            $cart->month = date('m');
            $cart->year = date('Y');
            $cart->save();

            $response['Location'] = $location;
            $response['quantity'] = CartManagement::whereUserId(Auth::user()->id)->count();
            $response['msg'] = __("Added to cart");
            $response['msgInfoChange'] = __("Added to cart");
            $response['status'] = 200;
             // DETERMINE TYPE AND ADD REDIRECT URL
            $type = null;
            if ($request->course_id) $type = 'course';
            elseif ($request->product_id) $type = 'product';
            elseif ($request->bundle_id) $type = 'bundle';
        
        $response['redirect_url'] = route('student.checkout', [1, $location]) ;

          
            //End:: Cart Management
            DB::commit();
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            $response['msg'] = __("Something is wrong! Try again.");
            $response['status'] = 402;
            return response()->json($response);
        }
    }

    public function updateCartQuantity(Request $request)
    {
        if (!Auth::check()) {
            $response['msg'] = __("You need to login first!");
            $response['status'] = 401;
            return response()->json($response);
        }

        if($request->quantity < 2 && $request->type == 2){
            $response['msg'] = __("Minimum Quantity is 1");
            $response['status'] = 422;
            return response()->json($response);
        }

        $cart = CartManagement::find($request->id);


        if($request->type == 1){
            $product = $cart->product;

            if($product && $product->quantity < 1){
                $cart->delete();
                $response['msg'] = __("Product is out of stock");
                $response['status'] = 422;
                return response()->json($response);
            }
            elseif($product && $product->quantity < ($cart->quantity + 1)){
                $response['msg'] = __("Quantity is out of stock");
                $response['status'] = 422;
                return response()->json($response);
            }

            $cart->increment('quantity', 1);
        }else{
            $cart->decrement('quantity', 1);
        }

        $response['data'] = [
            'quantity' => $cart->quantity
        ];

        $response['msg'] = __("Cart Update Successfully");
        $response['status'] = 200;
        return response()->json($response);
    }

    public function courseGift($uuid)
    {
        if (!Auth::check()) {
            $response['msg'] = __("You need to login first!");
            $response['status'] = 401;
            return response()->json($response);
        }
        $data['pageTitle'] = __("Send this course to your friend");
        $data['course'] = Course::where('uuid', $uuid)->first();
        $data['countries'] = Country::all();

        return view('frontend.student.cart.course-gift-checkout', $data);
    }

    public function addToCartConsultation(Request $request)
    {

        if (!Auth::check()) {
            $response['msg'] = __("You need to login first!");
            $response['status'] = 401;
            return response()->json($response);
        }

        if ($request->consultation_slot_id) {
            DB::beginTransaction();
            try {
                $consultationExit = ConsultationSlot::whereId($request->consultation_slot_id)->whereUserId($request->booking_instructor_user_id)->first();

                if (!$consultationExit) {
                    $response['msg'] = __("Time slot not found!");
                    $response['status'] = 404;
                    DB::rollBack();
                    return response()->json($response);
                }

                $ownConsultationSlotCheck = ConsultationSlot::whereId($request->consultation_slot_id)->whereUserId(Auth::id())->first();
                if ($ownConsultationSlotCheck) {
                    $response['msg'] = __("This is your consultation slot. No need to add.");
                    $response['status'] = 402;
                    DB::rollBack();
                    return response()->json($response);
                }

                $consultationOrderBooked = Order_item::whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
                if ($consultationOrderBooked) {
                    $order = Order::find($consultationOrderBooked->order_id);
                    if ($order) {
                        if ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $response['msg'] = __("This slot already purchased. Please try another slot.");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    } else {
                        $response['msg'] = __("Something is wrong! Try again.");
                        $response['status'] = 402;
                        DB::rollBack();
                        return response()->json($response);
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
                            $response['msg'] = __("You've already request this slot & status is pending!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $response['msg'] = __("You've already purchased this slot!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    } else {
                        $response['msg'] = __("Something is wrong! Try again.");
                        $response['status'] = 402;
                        DB::rollBack();
                        return response()->json($response);
                    }
                }

                $consultationOrderExit = Order_item::whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
                if ($consultationOrderExit) {
                    $order = Order::find($consultationOrderExit->order_id);
                    if ($order) {
                        if ($order->payment_status == 'pending') {
                            $response['msg'] = __("Another User already request this slot!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $response['msg'] = __("Another User already purchased this slot!");
                            $response['status'] = 402;
                            DB::rollBack();
                            return response()->json($response);
                        }
                    } else {
                        $response['msg'] = __("Something is wrong! Try again.");
                        $response['status'] = 402;
                        DB::rollBack();
                        return response()->json($response);
                    }
                }


                $cartExists = CartManagement::whereUserId(Auth::user()->id)->whereConsultationSlotId($request->consultation_slot_id)->where('consultation_date', $request->bookingDate)->first();
                if ($cartExists) {
                    $response['msg'] = __("Already added to cart!");
                    $response['status'] = 409;
                    DB::rollBack();
                    return response()->json($response);
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
                if($consultationExit->user->role == USER_ROLE_INSTRUCTOR){
                    $relation = 'instructor';
                }elseif($consultationExit->user->role == USER_ROLE_ORGANIZATION){
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

                $response['status'] = 200;
                $response['msg'] = __("Consultation added to cart");
                $response['redirect_route'] = route('student.cartList');
                DB::commit();
                return response()->json($response);
            } catch (\Exception $e) {
                DB::rollBack();
                $response['msg'] = __("Something is wrong! Try again.");
                $response['status'] = 402;
                return response()->json($response);
            }
        } else {
            $response['msg'] = __("Time slot not found!");
            $response['status'] = 404;
            return response()->json($response);
        }
    }

    public function goToCheckout(Request $request)
    {
        $createdByType = (int)$request->createdByType;
        if ($request->has('proceed_to_checkout')) {
            return redirect(route('student.checkout',['type'=>$createdByType,'location'=> 1]));
        }
        else if ($request->has('proceed_to_checkout_free')) {
            return redirect(route('student.checkoutFree',['type'=>$createdByType]));
        } 
        elseif ($request->has('pay_from_lmszai_wallet'))  {
            if($createdByType == 1){
                $carts = CartManagement::whereUserId(@Auth::id())
                ->where('created_by_type',$createdByType)->get();
                if (!count($carts)){
                    $this->showToastrMessage('error', __('Your cart is empty!'));
                    return redirect()->back();
            }
            }
            if ($carts->sum('price') > int_to_decimal(Auth::user()->balance)) {
                $this->showToastrMessage('warning', __('Insufficient balance'));
                return redirect()->back();
            } else {
                DB::beginTransaction();
                try {
                    $order_data = $this->placeOrder('buy');
                    if($order_data['status']){
                        $order = $order_data['data'];
                    }else{
                        $this->showToastrMessage('error', __('Something went wrong!'));
                        return redirect()->back();
                    }

                    $order->payment_status = 'paid';
                    $order->save();

                    if($createdByType == 1){
                        CartManagement::whereUserId(@Auth::id())
                        ->where('created_by_type',$createdByType)->delete();
                    }

                    distributeCommission($order);

                    /** ====== Send notification =========*/
                    $text = __("New student enrolled");
                    $target_url = route('instructor.all-student');

                    $sendMail = new EmailSendService();
                    foreach ($order->items as $item) {
                        if ($item->course) {
                            $this->send($text, 2, $target_url, $item->course->user_id);
                            //to instructor
                            $sendMail->sendNewEnrollEmailToInstructor($item->course->user_id, $item->course, $target_url);

                            //to admin
                            $sendMail->sendNewEnrollEmailToAdmin($item->course);

                            //to student
                            $sendMail->sendNewEnrollEmailToStudent(auth()->user(), $item->course, route('student.my-learning'));
                        }
                    }

                    $text = __("Course has been sold");
                    $this->send($text, 1, null, null);

                    /** ====== Send notification =========*/

                    $withdrow = new Withdraw();
                    $withdrow->transection_id = Str::uuid()->getHex();
                    $withdrow->amount = $carts->sum('price');
                    $withdrow->payment_method = 'buy';
                    $withdrow->status = WITHDRAWAL_STATUS_COMPLETE;
                    $withdrow->save();
                    Auth::user()->decrement('balance', decimal_to_int($carts->sum('price')));
                    createTransaction(Auth::id(), $carts->sum('price'), TRANSACTION_BUY, 'Transaction for Purchase');
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->showToastrMessage('warning', __('Something Went Wrong'));
                    return redirect()->back();
                }



                $this->showToastrMessage('success', __('Payment has been completed'));
                return redirect()->route('student.thank-you');
            }
        } elseif ($request->has('cancel_order')) {
            if($createdByType == 1){
                $getData = CartManagement::whereUserId(@Auth::id())
                ->where('created_by_type',$createdByType)->get();
                if(!$getData->isEmpty()){
                    foreach ($getData as $key => $cart) {
                        if($cart->course_id){
                            $getStudent = Student::where('user_id',@Auth::id())->first();
                            $checkTrial = TrialClass::where('student_id',$getStudent->id)
                            ->where('course_id',$cart->course_id)->first();
                            if($checkTrial){
                                $checkTrial->approved = 0;
                                $checkTrial->student_enrolled = 0;
                                $checkTrial->save();
                            }
                        }
                    }
                }
                CartManagement::whereUserId(@Auth::id())
                ->where('created_by_type',$createdByType)->delete();
                if(auth::user()->student->status == 0){
                    $getStudent = Student::where('id',auth::user()->student->id)->first();
                    if($getStudent){
                        $getStudent->status = 1;
                        $getStudent->pending_from_enrollment = 0;
                        $getStudent->save();
                    }
                }
            }
            $this->showToastrMessage('warning', __('Order has been cancelled'));
            return redirect(url('/'));
        } 
        elseif ($request->has('pay_from_subscription')) {
            if($createdByType == 1){
                $carts = CartManagement::whereUserId(@Auth::id())
                ->where('created_by_type',$createdByType)->get();
            }
            if (!count($carts)) {
                $this->showToastrMessage('error', 'Your cart is empty!');
                return redirect()->back();
            }

            if(get_option('subscription_mode')){
                $subscriptionPurchaseEnable = true;
            }
            else{
                $subscriptionPurchaseEnable = false;
            }

            foreach ($carts as $cart) {
                if(!$cart->is_subscription_enable){
                    $subscriptionPurchaseEnable = false;
                }
            }

            if (!$subscriptionPurchaseEnable || !hasLimit(PACKAGE_RULE_COURSE, count($carts))) {
                $this->showToastrMessage('warning', 'Subscription Package Limit over');
                return redirect()->back();
            } else {
                DB::beginTransaction();
                try {
                    $order_data = $this->placeOrder('subscription');
                    if($order_data['status']){
                        $order = $order_data['data'];
                    }else{
                        $this->showToastrMessage('error', __('Something went wrong!'));
                        return redirect()->back();
                    }

                    $order->payment_status = 'paid';
                    $order->save();

                    if($createdByType == 1){
                        CartManagement::whereUserId(@Auth::id())
                        ->where('created_by_type',$createdByType)->delete();
                    }
               

                    $userPackage = UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->where('packages.package_type', PACKAGE_TYPE_SUBSCRIPTION)->where('user_packages.user_id', auth()->id())->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->with('enrollments')->first();

                    foreach ($order->items as $item) {
                        $enrollment = setEnrollment($item);
                        $enrollment->user_package_id = $userPackage->id;
                        $enrollment->save();
                    }

                    /** ====== Send notification =========*/
                    $text = __("New student enrolled");
                    $target_url = route('instructor.all-student');
                    $sendMail = new EmailSendService();
;                    foreach ($order->items as $item) {
                        if ($item->course) {
                            $this->send($text, 2, $target_url, $item->course->user_id);

                            //to instructor
                            $sendMail->sendNewEnrollEmailToInstructor($item->course->user_id, $item->course, $target_url);

                            //to admin
                            $sendMail->sendNewEnrollEmailToAdmin($item->course);

                            //to student
                            $sendMail->sendNewEnrollEmailToStudent(auth()->user(), $item->course, route('student.my-learning'));
                        }
                    }

                    $text = __("Course has been sold");
                    $this->send($text, 1, null, null);

                    /** ====== Send notification =========*/

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->showToastrMessage('warning', 'Something Went Wrong');
                    return redirect()->back();
                }

                $this->showToastrMessage('success', 'Payment has been completed');
                return redirect()->route('student.thank-you');
            }
        } else {
            abort(404);
        }
    }

    public function cartDelete($id)
    {
        
        $cart = CartManagement::findOrFail($id);
        if($cart->course_id){
            $getStudent = Student::where('user_id',@Auth::id())->first();
            $checkTrial = TrialClass::where('student_id',$getStudent->id)
            ->where('course_id',$cart->course_id)->first();
            if($checkTrial){
                $checkTrial->approved = 0;
                $checkTrial->student_enrolled = 0;
                $checkTrial->save();
            }
            if($getStudent->pending_from_enrollment == 1){
                $getStudent->status = 1;
                $getStudent->pending_from_enrollment = 0;
                $getStudent->save();
            }
        }
        $cart->delete();
        $getData = CartManagement::whereUserId(@Auth::id())
                ->whereNotNull('course_id')
                ->where('created_by_type',1)->get();
        if($getData->empty()){
            if(auth::user()->student->status == 0){
                $getStudent = Student::where('id',auth::user()->student->id)->first();
                if($getStudent){
                    $getStudent->status = 1;
                    $getStudent->save();
                }
            }
        }
        $this->showToastrMessage('success', __('Removed from cart list!'));
        return redirect()->back();
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

    private function placeOrder($payment_method , $cartID = null, $not_admin = 0)
    {
     
        DB::beginTransaction();
        try{
         
            if($cartID != null){
                $carts = CartManagement::whereUserId(auth()->id())->where('id',$cartID)->get();
                $cart = CartManagement::whereUserId(auth()->id())->where('id',$cartID)->first();
            }
            else{
                if($not_admin){
                    $carts = CartManagement::whereUserId(auth()->id())->where('created_by_type',1)->get();
                    $cart = null;
                }
                else{
                    $carts = CartManagement::whereUserId(auth()->id())->get();
                    $cart = null;
                }
               
            }
           
            $order = new Order();
            $order->user_id = auth()->id();
            $order->cart_id = $cartID;
            $order->order_number = rand(100000, 999999);
            $order->sub_total = $carts->sum('price');
            $order->discount = $carts->sum('discount');
            // $order->platform_charge = get_platform_charge($carts->sum('price'));
            $order->platform_charge = 0.0;
            if($payment_method == 'cash_on'){
                $order->shipping_cost = $carts->sum('shipping_charge');
            }
            else{
                $order->shipping_cost = 0.0;
            }
         
            $order->current_currency = get_currency_code();
            $order->grand_total = $order->sub_total + $order->shipping_cost + $order->platform_charge;
            $order->payment_method = $payment_method;
            if($cartID == null){
                $order->created_by_type = 1;
                $order->cart_type = 1;
            }
            else{
                $order->created_by_type = 2; 
                $order->cart_type = 2;
            }
            if($cart != null){
                $order->month = $cart->month;
                $order->year = $cart->year;
            }
            else{
                $order->month = date('m');
                $order->year = date('Y');
            }
            $payment_currency = '';
            $conversion_rate = '';
  
            if ($payment_method == 'paypal') {
                $payment_currency = get_option('paypal_currency');
                $conversion_rate = get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 0;
            }elseif ($payment_method == 'faysal_bank') {
                $payment_currency = get_option('faysal_currency');
                $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;
            }elseif ($payment_method == 'meezan_bank') {
                $payment_currency = get_option('meezan_currency');
                $conversion_rate = get_option('meezan_conversion_rate') ? get_option('meezan_conversion_rate') : 0;
            } elseif ($payment_method == 'stripe') {
                $payment_currency = get_option('stripe_currency');
                $conversion_rate = get_option('stripe_conversion_rate') ? get_option('stripe_conversion_rate') : 0;
            } elseif ($payment_method == 'bank') {
                $payment_currency = get_option('bank_currency');
                $conversion_rate = get_option('bank_conversion_rate') ? get_option('bank_conversion_rate') : 0;
            } elseif ($payment_method == 'mollie') {
                $payment_currency = get_option('mollie_currency');
                $conversion_rate = get_option('mollie_conversion_rate') ? get_option('mollie_conversion_rate') : 0;
            } elseif ($payment_method == 'instamojo') {
                $payment_currency = get_option('im_currency');
                $conversion_rate = get_option('im_conversion_rate') ? get_option('im_conversion_rate') : 0;
            } elseif ($payment_method == 'paystack') {
                $payment_currency = get_option('paystack_currency');
                $conversion_rate = get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0;
            } elseif ($payment_method == 'sslcommerz') {
                $payment_currency = get_option('sslcommerz_currency');
                $conversion_rate = get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0;
            } elseif ($payment_method == 'mercadopago') {
                $payment_currency = get_option('mercado_currency');
                $conversion_rate = get_option('mercado_conversion_rate') ? get_option('mercado_conversion_rate') : 0;
            } elseif ($payment_method == 'flutterwave') {
                $payment_currency = get_option('flutterwave_currency');
                $conversion_rate = get_option('flutterwave_conversion_rate') ? get_option('flutterwave_conversion_rate') : 0;
            } elseif ($payment_method == 'coinbase') {
                $payment_currency = get_option('coinbase_currency');
                $conversion_rate = get_option('coinbase_conversion_rate') ? get_option('coinbase_conversion_rate') : 0;
            } elseif ($payment_method == 'zitopay') {
                $payment_currency = get_option('zitopay_currency');
                $conversion_rate = get_option('zitopay_conversion_rate') ? get_option('zitopay_conversion_rate') : 0;
            } elseif ($payment_method == IYZIPAY) {
                $payment_currency = get_option('iyzipay_currency');
                $conversion_rate = get_option('iyzipay_conversion_rate') ? get_option('iyzipay_conversion_rate') : 0;
            } elseif ($payment_method == BITPAY) {
                $payment_currency = get_option('bitpay_currency');
                $conversion_rate = get_option('bitpay_conversion_rate') ? get_option('bitpay_conversion_rate') : 0;
            } elseif ($payment_method == BRAINTREE) {
                $payment_currency = get_option('braintree_currency');
                $conversion_rate = get_option('braintree_conversion_rate') ? get_option('braintree_conversion_rate') : 0;
            }

            $order->payment_currency = $payment_currency;
            $order->conversion_rate = $conversion_rate;
            if ($conversion_rate) {
                $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
            }
        
            $order->save();
     
            foreach ($carts as $cart) {
             
                if ($cart->course_id) {
                    $courseExits = Course::find($cart->course_id);
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = auth()->id();
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



                    $order_item->created_by_type = $cart->created_by_type;
                    $order_item->cart_type = $cart->created_by_type;
                    $order_item->cart_id = $cart->id;
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
                    if($cart->created_by_type == 1){        // Student Made Purchase Himself Of Course
                        if(auth::user()->student->status == 1){
                            // $enrollment = new Enrollment();
                            // $enrollment->order_id = $order->id;
                            // $enrollment->user_id = auth()->id();
                            // $enrollment->course_id = $cart->course_id;
                            // $enrollment->owner_user_id = $courseExits->user_id ?? null;
                            // $enrollment->start_date = now();
                            // $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                            // $enrollment->save();
                        }
                      
                    }
                    else if($cart->created_by_type == 2){  // Admin made manual course payment for student 
                    }
                 

                    $this->addAffiliateHistory($cart,$order,$order_item);

                } 
                elseif ($cart->bundle_id) {
                   
                    // $bundleIds = Enrollment::where('user_id', auth()->id())->whereNotIn('course_id', $cart->bundle_course_ids)->where('end_date', '<', now())->select('course_id')->get()->toArray();
                    $courses = Course::whereIn('id', $cart->bundle_course_ids)->get();
                    $bundleUserId = $cart->bundle->user_id;
                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $bundleUserId)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                    if($adminCommission){
                        $adminCommissionAmount = admin_commission_by_percentage($cart->price, $adminCommission);
                        $totalDistributeAmount = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                    }
                    else{
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

                        $order_item->owner_user_id = $bundleUserId;
                        $order_item->unit_price = $course->price;
                        $order_item->owner_balance = $finalPriceRatio * $course->price;
                        $order_item->sell_commission = $sellCommissionRatio * $course->price;
                        $order_item->admin_commission = $adminCommissionRatio * $course->price;
                        $order_item->course_id = $course->id;
                        $order_item->type = 3; //bundle course
                        $order_item->save();
                        $this->addAffiliateHistory($cart,$order,$order_item);
                    }

                } 
                elseif ($cart->consultation_slot_id) {
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::id();
                    $order_item->owner_user_id = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->instructor_user_id : null;
                    $order_item->consultation_slot_id = $cart->consultation_slot_id;
                    $order_item->consultation_date = $cart->consultation_date;
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

                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
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
                elseif ($cart->product_id) {
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = auth()->id();
                    $order_item->type = 2;
                    $order_item->product_id = $cart->product_id;
                    $order_item->receiver_info = $cart->receiver_info;
                    $order_item->owner_user_id = $cart->product ? $cart->product->user_id : null;
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

                    // $order_item->quantity = $cart->quantity;
                    $order_item->unit = $cart->quantity;
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
            DB::commit();
            return ['status' => true,'data' => $order];
        }catch (\Exception $e){
            DB::rollBack();
            $this->logger->log('Cannot Create Order', $e->getMessage());
            return ['status' => false,'data' => null];
        }

    }
    public function checkoutFree($type = null,$location = null)
    {
     
        $data['createdByType'] = $type;
        $data['cartID'] = null;
        $data['Location'] = $location;
        $cartID = null;
        $data['userInfo'] =  null;
        $data['courseName'] =  null;
        $data['pageTitle'] = "CheckoutFree";
        // $data['carts'] = CartManagement::whereUserId(@Auth::id())->get();
        // $cart = CartManagement::whereUserId(@Auth::id())
        // ->where('created_by_type',$type)->first();

        $carts = CartManagement::whereUserId(@Auth::id())->where('created_by_type',$type)->get();
        $cart = null;

        DB::beginTransaction();
        try {
        
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->order_number = rand(100000, 999999);
            $order->sub_total = $carts->sum('price');
            $order->discount = $carts->sum('discount');
            // $order->platform_charge = get_platform_charge($carts->sum('price'));
            $order->platform_charge = 0.0;
            // $order->shipping_cost = $carts->sum('shipping_charge');
            $order->shipping_cost = 0.0;
            $order->payment_status = 'free';
            $order->delivery_status = 1;
            $order->current_currency = get_currency_code();
            $order->grand_total = $order->sub_total + $order->shipping_cost + $order->platform_charge;
            $order->payment_method = null;
            if($cart != null){
                $order->month = $cart->month;
                $order->year = $cart->year;
            }
            $payment_currency = '';
            $conversion_rate = '';

            $order->payment_currency = $payment_currency;
            $order->conversion_rate = $conversion_rate;
            $order->created_by = Auth::user()->id;
            if ($conversion_rate) {
                $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
            }
            $order->save();

            foreach ($carts as $cart) {
                if ($cart->course_id) {
                    $courseExits2 = Course::where('id',$cart->course_id)->first();

                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::id();
                    $order_item->type = 1;
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

                    $enrollment = Enrollment::where('user_id',Auth::id())->where('course_id',$cart->course_id)->orderBy('id','DESC')->first();
                    if($enrollment){
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = Auth::id();
                        $enrollment->course_id = $cart->course_id;
                        $enrollment->owner_user_id = $courseExits2->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($courseExits2->access_period) ? Carbon::now()->addDays($courseExits2->access_period) : MAX_EXPIRED_DATE;

                        $enrollment->main_price = $order_item->unit_price;
                        $enrollment->price = $order_item->unit_price;
                        $enrollment->original_price = $order_item->original_price;
                        $enrollment->discount = $order_item->discount;
                        $enrollment->admission_fee = $order_item->admission_fee;
                        $enrollment->discount_type = $order_item->discount_type ?? 0;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee ?? 0;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;
                        $enrollment->save();
                    }
                    else{
                        $enrollment = new Enrollment();
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = Auth::id();
                        $enrollment->course_id = $cart->course_id;
                        $enrollment->owner_user_id = $courseExits2->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($courseExits2->access_period) ? Carbon::now()->addDays($courseExits2->access_period) : MAX_EXPIRED_DATE;

                        $enrollment->main_price = $order_item->unit_price;
                        $enrollment->price = $order_item->unit_price;
                        $enrollment->original_price = $order_item->original_price;
                        $enrollment->discount = $order_item->discount;
                        $enrollment->admission_fee = $order_item->admission_fee;
                        $enrollment->discount_type = $order_item->discount_type ?? 0;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee ?? 0;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;


                        $enrollment->save();
                        Log::info("NEW ENROLLMENT BEING CREATED IN (checkoutFree) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                        Log::info("NEW ENROLLMENT DETAILS IN (checkoutFree) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                    }
                    $this->addAffiliateHistory($cart,$order,$order_item);

                } 
                elseif ($cart->product_id) {
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::id();
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

                    $order_item->type = 2;
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
            DB::commit();
            
            if($type == 1){
                CartManagement::whereUserId(@Auth::id())
                ->where('created_by_type',$type)->delete();
            }
            else if($type == 2){
                if($cartID != null){
                    CartManagement::whereUserId(@Auth::id())
                    ->where('id',$cartID)
                    ->where('created_by_type',$type)->delete();
                }
                else{
                    CartManagement::whereUserId(@Auth::id())
                    ->where('created_by_type',$type)->delete();
                }
            }

            $this->showToastrMessage('success', __('Order Created Successfully'));
            return redirect()->route('student.cartList');
            // return ['status' => true,'data' => $order];


        } 
        catch (\Exception $e) {
            DB::rollBack();
            $this->logger->log('Cannot Create Order', $e->getMessage());
            $this->showToastrMessage('error', __('Cannot Create Order', $e->getMessage()));
            return redirect()->route('student.cartList');
        }
     
    }


    public function checkout($type = null,$location = null)
    {
        $data['createdByType'] = $type;
        $data['cartID'] = null;
        $data['userInfo'] =  null;
        $data['courseName'] =  null;
        $data['courseSlug'] =  null;
        $data['pageTitle'] = "Checkout";
        // $data['carts'] = CartManagement::whereUserId(@Auth::id())->get();

        if($data['cartID'] == null){
            $data['carts'] = CartManagement::whereUserId(@Auth::id())
            ->where('created_by_type',$type)->get();
            $tranAmount = (int)$data['carts']->sum('price');
        }
        else{
            $data['carts'] = CartManagement::whereUserId(@Auth::id())
            ->where('id',$data['cartID'])
            ->where('created_by_type',$type)->get();
            $tranAmount = (int)$data['carts']->sum('price');
        }

   
     
        if($data['cartID'] == null){
            $cart = CartManagement::whereUserId(@Auth::id())
            ->where('created_by_type',$type)->first();
            // $data['paymentCartID'] = (int)$cart->id. time();
           
            // $data['cartID'] = (int)$cart->id;

            $data['paymentCartID'] = 'LMS-P-'. time();
            $cartsData = CartManagement::whereUserId(@Auth::id())
            ->where('created_by_type',$type)->get();
            if(!$cartsData->isEmpty()){
                foreach ($cartsData as $key => $cartData) {
                    $cartData->faysal_order_id = $data['paymentCartID'];
                    $cartData->update();
                }
            }

        }
        else{
            $cart = CartManagement::where('id',$data['cartID'])
            ->where('created_by_type',$type)->first();
            // $data['paymentCartID'] = (int)$data['cartID']. time(); 
            $data['paymentCartID'] = 'LMS-P-'. time();
            $data['cartID'] = (int)$data['cartID'];

            if($cart){
                $cart->faysal_order_id = $data['paymentCartID'];
                $cart->update();
            }
        }

        $data['student'] = auth::user()->student;
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();
        $data['banks'] = Bank::orderBy('name', 'asc')->where('status', 1)->get();
        if (old('country_id')) {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id')) {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        $razorpay_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('razorpay_conversion_rate') ? get_option('razorpay_conversion_rate') : 0);
        $data['razorpay_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($razorpay_grand_total_with_conversion_rate, 2));

        $paystack_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0);
        $data['paystack_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($paystack_grand_total_with_conversion_rate, 2));

        $sslcommerz_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0);
        $data['sslcommerz_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($sslcommerz_grand_total_with_conversion_rate, 2));
        // dd($data);
        if(empty(get_option('faysal_base_url'))){
            $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
        }
        else{
            // $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
            $data['faysalBankUrl']= get_option('faysal_base_url');
        }

        if(empty(get_option('faysal_merchant_id'))){
            $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
        }
        else{
            // $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
            $data['faysalBankMerchantID']= get_option('faysal_merchant_id'); 
        }

        if(empty(get_option('faysal_secure_key'))){
            $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
        }
        else{
            // $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
            $faysal_bank_secure_key= get_option('faysal_secure_key'); 
        }
        // $basketID = $cart->id;
        $basketID = $data['paymentCartID'];
        $tranAmount = (int)$data['carts']->sum('price');
        $data['txnAmount'] = $tranAmount;
        // $tranAmount = (int)$cart->sum('price');
        $Currency = get_option('faysal_currency')?get_option('faysal_currency'):'PKR';
    
        $faysal_bank_token = $this->getFaysalBankAccessToken($data['faysalBankMerchantID'], $faysal_bank_secure_key,$tranAmount,$basketID,$Currency); 
    
        $data['faysalBankToken'] = $faysal_bank_token;
        $data['Location'] = $location;
        // $data['LiveEnrollment'] = 1;
        $data['LiveEnrollment'] = 0;
        $data['trialID'] = null;
        $data['randSignature'] =  uniqid();
        $data['generalUrl']= route('student.pay');
        $data['paymentType'] = $type;
        $data['userID'] = auth::user()->id;
        $data['courseName'] = $data['carts']->first()->course?->title ?? 'Pending Cart Payment';
        $data['courseSlug'] = $data['carts']->first()->course?->slug ?? null;

        return view('frontend.student.cart.checkout', $data);
    }



    // public function checkoutSecure($type = null)
    // {
    //     $user = Auth::user();
     
    //     if (!$user && !Session::has('secure_checkout')) {
    //         return response("<h3 style='text-align:center; font-family:Arial;'>Authentication failed. Please try again.</h3>", 401)
    //         ->header('Content-Type', 'text/html');
    //     }

    //     // Get secure session data
    //     $secureData = Session::get('secure_checkout');
    //     $cartId = $secureData['cart_id'] ?? null;
     
          
    //     $data['createdByType'] = $type;
    //     $data['cartID'] = $cartId;
    //     $data['paymentType'] = $type;
    //     $data['userID'] = $user->id;
    //     $data['carts'] = CartManagement::where('id', $cartId)
    //         ->where('created_by_type', $type)
    //         ->get();

    //     if ($data['carts']->isEmpty()) {
    //          return response("<h3 style='text-align:center; font-family:Arial;'>Your cart is empty. Please go back and try again.</h3>", 200)
    //         ->header('Content-Type', 'text/html');
    //     }

           
    //     $data['paymentCartID'] = 'LMS-P-' . time();
    //     $data['txnAmount'] = (int)$data['carts']->sum('price');

    //     foreach ($data['carts'] as $cart) {
    //         $cart->faysal_order_id = $data['paymentCartID'];
    //         $cart->save();
    //     }

    //     $data['student'] = $user->student;
    //     $data['countries'] = Country::orderBy('country_name', 'asc')->get();
    //     $data['banks'] = Bank::orderBy('name', 'asc')->where('status', 1)->get();

    //     $data['faysalBankUrl'] = get_option('faysal_base_url') ?: env('FAYSAL_BANK_BASE_URL');
    //     $data['faysalBankMerchantID'] = get_option('faysal_merchant_id') ?: env('FAYSAL_BANK_MERCHANT_ID');
    //     $faysalSecureKey = get_option('faysal_secure_key') ?: env('FAYSAL_BANK_SECURE_KEY');

    //     $currency = get_option('faysal_currency') ?: 'PKR';
    //     $data['faysalBankToken'] = $this->getFaysalBankAccessToken(
    //         $data['faysalBankMerchantID'],
    //         $faysalSecureKey,
    //         $data['txnAmount'],
    //         $data['paymentCartID'],
    //         $currency
    //     );

    //     $data['randSignature'] = uniqid();
    //     $data['generalUrl'] = route('student.pay');
    //     $data['pageTitle'] = "Checkout";
    //     $data['userInfo'] = $user;
    //     $data['courseName'] = $data['carts']->first()->course?->title ?? 'Course Payment';
    //     $data['courseSlug'] = $data['carts']->first()->course?->slug ?? null;
        
    //     $sessionPaymentMethod = Session::get('secure_checkout.payment_method');
    //     $data['sessionPaymentMethod'] = $sessionPaymentMethod;
     
    //     return view('frontend.student.cart.checkout_secure', $data);
    // }





    public function checkoutSecure($type = null)
{
  
    $user = Auth::user();

    // === 1. Authentication & Session Validation ===
    if (!$user || !Session::has('secure_checkout')) {
        return response("<h3 style='text-align:center; font-family:Arial;'>Authentication failed or session expired. Please try again.</h3>", 401)
            ->header('Content-Type', 'text/html');
    }

    $secureData = Session::get('secure_checkout');

    // Validate type matches token
    if ($secureData['created_by_type'] != $type) {
        Log::warning("Type mismatch in secure checkout", [
            'session_type' => $secureData['created_by_type'],
            'route_type' => $type,
            'user_id' => $user->id
        ]);
        return response("<h3 style='text-align:center; font-family:Arial;'>Invalid request type. Please go back and try again.</h3>", 400)
            ->header('Content-Type', 'text/html');
    }

    // === 2. Get Cart IDs from Secure Session ===
    $cartIds = $secureData['cart_ids'] ?? [];
    if (empty($cartIds)) {
        return response("<h3 style='text-align:center; font-family:Arial;'>No cart items found. Please go back and try again.</h3>", 200)
            ->header('Content-Type', 'text/html');
    }

    // === 3. Fetch Carts Safely (with ownership + type check) ===
    $carts = CartManagement::whereIn('id', $cartIds)
        ->where('user_id', $user->id)
        ->where('created_by_type', $type)
        ->with(['course:id,title,price,image,slug', 'product:id,title,current_price,thumbnail'])
        ->get();

    if ($carts->isEmpty()) {
        return response("<h3 style='text-align:center; font-family:Arial;'>No valid cart items found. Please go back and try again.</h3>", 200)
            ->header('Content-Type', 'text/html');
    }

    // === 4. Calculate Totals ===
    $subtotal = $carts->sum('price');
    $platformCharge = get_platform_charge($subtotal);
    $grandTotal = $subtotal + $platformCharge;

    // Generate unique order ID
    $paymentCartID = 'LMS-P-' . time();

    // Attach faysal_order_id to all carts
    foreach ($carts as $cart) {
        $cart->faysal_order_id = $paymentCartID;
        $cart->save();
    }

    // === 5. Prepare View Data ===
    $data = [
        'createdByType' => $type,
        'cartIDs' => $cartIds,                    // Pass all cart IDs
        'paymentCartID' => $paymentCartID,
        'txnAmount' => (int)$grandTotal,          // Use grand total
        'carts' => $carts,
        'student' => $user->student,
        'countries' => Country::orderBy('country_name', 'asc')->get(),
        'banks' => Bank::where('status', 1)->orderBy('name', 'asc')->get(),

        // Faysal Bank Config
        'faysalBankUrl' => get_option('faysal_base_url') ?: env('FAYSAL_BANK_BASE_URL'),
        'faysalBankMerchantID' => get_option('faysal_merchant_id') ?: env('FAYSAL_BANK_MERCHANT_ID'),
        'faysalCurrency' => get_option('faysal_currency') ?: 'PKR',

        // Generate Token for Payment Request
        'faysalBankToken' => $this->getFaysalBankAccessToken(
            get_option('faysal_merchant_id') ?: env('FAYSAL_BANK_MERCHANT_ID'),
            get_option('faysal_secure_key') ?: env('FAYSAL_BANK_SECURE_KEY'),
            (int)$grandTotal,
            $paymentCartID,
            get_option('faysal_currency') ?: 'PKR'
        ),

        'randSignature' => uniqid(),
        'generalUrl' => route('student.pay'),
        'pageTitle' => "Secure Checkout",
        'userInfo' => $user,

        // For UI display
        'courseName' => $carts->first()->course?->title ?? 'Multiple Courses',
        'courseSlug' => $carts->first()->course?->slug ?? null,

        // Payment method selected during token generation
        'sessionPaymentMethod' => $secureData['payment_method'] ?? null,
    ];
  
    return view('frontend.student.cart.checkout_secure', $data);
}





//     public function redirectToMeezan($cart_id)
// {
  
//     $user = Auth::user();
//     $cart = CartManagement::where('id', $cart_id)->where('user_id', $user->id)->first();
//     $isPaymentExists = CartManagement::where('id',$cart_id)->first();
//     if($isPaymentExists == null){
//         return response("<h3 style='text-align:center; font-family:Arial;'>Given Payment/Cart ID Does Not Exists.</h3>", 200)
//         ->header('Content-Type', 'text/html');
//     }

//      if($cart_id != null){
//             $is_order_already_placed = $this->checkOrderExists('meezan_bank' , $cart_id);
//             if($is_order_already_placed){
//                    $checkORDER = Order::where('cart_id',$cart_id)->first();
//                    if($checkORDER->payment_status == 'paid'){
//                          return response("<h3 style='text-align:center; font-family:Arial;'>Order For Given Payment ID Already Being Paid!</h3>", 200)
//                         ->header('Content-Type', 'text/html');
//                    }
//                    else{
//                         $order_data = [
//                             'status' => true,
//                             'data' => $checkORDER
//                         ];
//                    }
//             }
//             else{
//                    $order_data = $this->placeOrder('meezan_bank' , $cart_id);
//             }

//             if($order_data['status']){
//                 $order = $order_data['data'];
//             }
//             else{
//                 return response("<h3 style='text-align:center; font-family:Arial;'>Something went wrong while creating order for payment!</h3>", 200)
//                     ->header('Content-Type', 'text/html');
//             }
       
//             $total = $order->grand_total * (get_option('meezan_conversion_rate') ? get_option('meezan_conversion_rate') : 0);
//             $total = number_format($total, 2,'.','');

//              $fromApi = 0;
//             $responseData = $this->makeMeezanPayment($order->user_id,$total ,null ,$order->id , $cart_id,$fromApi);
//             if(isset($responseData['errorMessage'])){
//                 $getOrderItemsData2 = Order_item::where('order_id',$order->id)->get();
//                 if(!$getOrderItemsData2->isEmpty()){
//                     foreach ($getOrderItemsData2 as $key => $item) {
//                         $item->delete();
//                     }
//                 }
//                 $getOrderData2 = Order::where('id',$order->id)->first();
//                 $getOrderData2->delete();
//                 return response()->json(['message' =>  __($responseData['errorMessage'])], 302);
//                 return response("<h3 style='text-align:center; font-family:Arial;'>".__($responseData['errorMessage'])."</h3>", 200)
//                     ->header('Content-Type', 'text/html');
//             }
//             else{
                 
//                 session(['meezan_pending_cart' => (int)$cart_id]);
//                 return redirect($responseData['redirectURL']);
               
//                 // $result_array[] = [
//                 //     // 'user_id' => $user->id,
//                 //     'cart_id' => (int)$cartID,
//                 //     'order_id' => $order->id,
//                 //     'meezan_payment_flow_redirect_url' => $responseData['redirectURL'],
//                 // ];
//                 // return response()->json($result_array);
//             }
//     }
//     else{
//          return response("<h3 style='text-align:center; font-family:Arial;'>Invalid Cart ID.</h3>", 200)
//         ->header('Content-Type', 'text/html');
//     }
    
  
// }


    public function redirectToMeezan(Request $request)
{
  
    $user = Auth::user();
    if (!$user) {
        return response("<h3 style='text-align:center; font-family:Arial;'>User not logged in.</h3>", 401)
            ->header('Content-Type', 'text/html');
    }
   
    // Get secure checkout context from session
    $secureData = Session::get('secure_checkout');
   
    if (!$secureData) {
        return response("<h3 style='text-align:center; font-family:Arial;'>Invalid or expired session. Please start checkout again.</h3>", 302)
            ->header('Content-Type', 'text/html');
    }
    $createdByType = $secureData['created_by_type'] ?? null;
    $cartIds = $secureData['cart_ids'] ?? [];
    if (!$createdByType || !in_array($createdByType, [1, 2])) {
        return response("<h3 style='text-align:center; font-family:Arial;'>Invalid payment type.</h3>", 302)
            ->header('Content-Type', 'text/html');
    }

    $carts = collect();
    $order = null;
  
     if ($createdByType == 2) {
        // Admin-assigned: only one cart allowed
        if (count($cartIds) !== 1) {
            return response("<h3 style='text-align:center; font-family:Arial;'>Invalid cart selection for admin-assigned payment.</h3>", 302)
                ->header('Content-Type', 'text/html');
        }

        $cartId = $cartIds[0];
        $cart = CartManagement::where('id', $cartId)
            ->where('user_id', $user->id)
            ->where('created_by_type', 2)
            ->first();

        if (!$cart) {
            return response("<h3 style='text-align:center; font-family:Arial;'>Cart not found or access denied.</h3>", 302)
                ->header('Content-Type', 'text/html');
        }

        // Check if order already exists
        $existingOrder = Order::where('cart_id', $cartId)
            ->where('payment_method', 'meezan_bank')
            ->first();

        if ($existingOrder && $existingOrder->payment_status === 'paid') {
            return response("<h3 style='text-align:center; font-family:Arial;'>Order already paid.</h3>", 200)
                ->header('Content-Type', 'text/html');
        }

        if ($existingOrder) {
            $order = $existingOrder;
        } else {
            $order = $this->placeOrder('meezan_bank', $cartId);
            if (!$order['status']) {
                Log::error("Failed to create order for Meezan", ['cart_id' => $cartId]);
                return response("<h3 style='text-align:center; font-family:Arial;'>Failed to create order.</h3>", 500)
                    ->header('Content-Type', 'text/html');
            }
            $order = $order['data'];
        }

        $totalAmount = $order->grand_total;
        $associatedCartId = $cartId;
    }
    elseif ($createdByType == 1) {
        // User's own carts: multiple items
        $carts = CartManagement::whereIn('id', $cartIds)
            ->where('user_id', $user->id)
            ->where('created_by_type', 1)
            ->get();

               
        if ($carts->isEmpty()) {
            return response("<h3 style='text-align:center; font-family:Arial;'>No valid cart items found.</h3>", 404)
                ->header('Content-Type', 'text/html');
        }

        // Generate shared meezan_order_id-like ID for tracking
        $sharedOrderId = 'LMS-MZ-' . time();

        // Check if an unpaid order already exists for this batch
        $existingOrder = Order::where('meezan_order_id', $sharedOrderId)
            ->where('payment_method', 'meezan_bank')
            ->where('payment_status', '!=', 'paid')
            ->first();

            
        if ($existingOrder) {
            $order = $existingOrder;
        } else {
            $order = $this->placeOrder('meezan_bank');
            if (!$order['status']) {
                Log::error("Failed to create order for Meezan", ['cart_id' => $cartId]);
                return response("<h3 style='text-align:center; font-family:Arial;'>Failed to create order.</h3>", 500)
                    ->header('Content-Type', 'text/html');
            }
            $order = $order['data'];
          
            // // Create new order
            // $subtotal = $carts->sum('price');
            // $platformCharge = get_platform_charge($subtotal);
            // $grandTotal = $subtotal + $platformCharge;
           
            // $order = new Order();
            // $order->user_id = $user->id;
            // $order->order_number = 'ORD-' . strtoupper(uniqid());
            // $order->sub_total = $subtotal;
            // $order->platform_charge = $platformCharge;
            // $order->grand_total = $grandTotal;
            // $order->current_currency = 'PKR';
            // $order->payment_currency = 'PKR';
            // $order->conversion_rate = get_option('meezan_conversion_rate') ?: 1;
            // $order->grand_total_with_conversation_rate = $grandTotal * $order->conversion_rate;
            // $order->payment_method = 'meezan_bank';
            // $order->faysal_order_id = $sharedOrderId; // Use as reference
            // $order->cart_type = 1;
            // $order->created_by_type = 1;
            // $order->delivery_status = 0;
            // $order->payment_status = 'pending';
            // $order->save();
        }

        $totalAmount = $order->grand_total;
        $associatedCartId = null; // Not tied to one cart
    }
    // Make Meezan Payment Request
    $fromApi = 0;
    $responseData = $this->makeMeezanPayment(
        $user->id,
        $totalAmount,
        null,
        $order->id,
        $associatedCartId,
        $fromApi
    );

    if (isset($responseData['errorMessage'])) {
        // Clean up failed order if newly created
        if (!isset($existingOrder)) {
            Order_item::where('order_id', $order->id)->delete();
            $order->delete();
        }

        return response("<h3 style='text-align:center; font-family:Arial;'>{$responseData['errorMessage']}</h3>", 200)
            ->header('Content-Type', 'text/html');
    }

    // Store pending cart context in session
    if ($createdByType == 1) {
        session(['meezan_pending_cart_type' => 1, 'meezan_pending_faysal_order_id' => $order->faysal_order_id]);
    } else {
        session(['meezan_pending_cart' => (int)$associatedCartId]);
    }

    // Redirect to Meezan
    return redirect($responseData['redirectURL']);

   
}




//     private function makeMeezanPayment($user_id,$amount,$post_data = null,$order_id = null , $cart_id = null, $fromApi = 0)
// {
//         try {
         
//         $meezan_port_address = get_option('meezan_port_address');
//         $meezan_user_name = get_option('meezan_user_name');
//         $meezan_user_password = get_option('meezan_user_password');
//         $meezan_pkr_currency_code = 586;
//         $meezan_usd_currency_code = 840;

//             $data['success'] = false;
//             $data['redirect_url'] = '';
//             $data['payment_id'] = '';
//             $data['message'] = '';
//             $amount         =(int)$amount; 
//             $amountUpdated         =$amount * 100; 
//             $orderIDupdated = rand(1,99999).$order_id;

//             $cart = CartManagement::where('id',$cart_id)->first();
//             if($cart){
//                 $cart->meezan_order_id = $orderIDupdated;
//                 $cart->save();
//             }

//              $Order = Order::where('id',$order_id)->first();
//              if($Order){
//                 $Order->meezan_order_id = $orderIDupdated;
//                 $Order->save();
//             }
//             if($fromApi == 1){
//                 $returnURLUpdated = 'https://portal.cefonlineacademy.com/billing/payment-confirmation/'.$cart_id.'/'.$order_id;
//             }
//             else{
//                 $returnURLUpdated = route('student.successfulMeezan') .'/'.$order_id .'/'. $cart_id;
//             }
        
        
//             $tokenApiUrl =  $meezan_port_address.'register.do';
//             $urlPostParams = array( 
//                 'userName'=>$meezan_user_name, 
//                 'password'=>$meezan_user_password, 
//                 'orderNumber'=>$orderIDupdated, 
//                 'amount'=>$amountUpdated, 
//                 'currency'=>$meezan_pkr_currency_code, 
//                 'returnUrl'=>$returnURLUpdated, 
//             ); 
        
//             $query=urldecode(http_build_query($urlPostParams));
//             $ch = curl_init(); 
//         curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
//         curl_setopt($ch, CURLOPT_POST, 1); 
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $query); 
//         curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Meezan API Example'); 
//         $headers = array(
//             'Content-Type: application/x-www-form-urlencoded',
//         );
//         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//         $response = curl_exec($ch); 
//         $arrayResult = json_decode($response, true);
//         curl_close($ch); 
    
//         $generatedErrorCode = $arrayResult['errorCode'];
//         if($generatedErrorCode != 0){
//             $generatedErrorMessage = $arrayResult['errorMessage'];
//             $data = [
//                 'errorMessage' => "<strong>". $generatedErrorMessage."</strong><br/>",
//             ];
//             return $data;
//         }
//         else{
//             $generatedOrderID = $arrayResult['orderId'];
//             $generatedFormUrl = $arrayResult['formUrl'];
//             // $this->OrderId = $generatedOrderID;
        
//             $getOrderData = Order::where('id',$order_id)->first();
//             $getOrderData->meezan_transaction_id = $generatedOrderID;
//             $getOrderData->update();
         
//             $data = [
//                 'redirectURL' => $generatedFormUrl,
//             ];
//             return $data;

//             // return $data['redirectURL'] = $generatedFormUrl;
//             // header("Location: $generatedFormUrl");
//             // exit();
//         }
          
//     } catch (\Exception $ex) {
//             Log::info($ex);
//             $data = [
//                 'errorMessage' => "<strong>". $ex->getMessage()."</strong><br/>",
//             ];
//             return $data;
          
//     }
// }


private function makeMeezanPayment($user_id, $amount, $post_data = null, $order_id = null, $cart_id = null, $fromApi = 0)
{
    try {
        // Load configuration
        $meezan_port_address = get_option('meezan_port_address');
        $meezan_user_name = get_option('meezan_user_name');
        $meezan_user_password = get_option('meezan_user_password');
        $meezan_pkr_currency_code = 586; // PKR

        if (!$meezan_port_address || !$meezan_user_name || !$meezan_user_password) {
            throw new \Exception("Meezan Bank API credentials are missing or incomplete.");
        }

        // Validate required inputs
        if (!$order_id) {
            throw new \Exception("Order ID is required for Meezan payment.");
        }

        $order = Order::findOrFail($order_id);

        // Convert amount to smallest unit (cents/paisa)
        $amountInCents = (int)round($amount * 100);
        $merchantOrderId = 'MZ-' . rand(10000, 99999) . '-' . $order_id;

        // === Save Meezan Order ID on Order ===
        $order->meezan_order_id = $merchantOrderId;
        $order->save();

        // === Optionally save on Cart (only if cart_id provided) ===
        if ($cart_id) {
            $cart = CartManagement::where('id', $cart_id)->first();
            if ($cart) {
                $cart->meezan_order_id = $merchantOrderId;
                $cart->save();
            }
        }

        // === Build Return URL (DO NOT include cart_id if not needed) ===
        if ($fromApi == 1) {
            if($cart_id){
                $returnUrl = 'https://portal.cefonlineacademy.com/billing/payment-confirmation/'.$cart_id.'/'.$order_id;
            }
            else{
                $returnUrl = 'https://portal.cefonlineacademy.com/billing/payment-confirmation/' . $order_id;
            }  
        } 
        else {
             if($cart_id){
                  // Use named route with only order_id → cleaner & safer
                    $returnUrl = route('student.successfulMeezan', ['order_id' => $order_id, 'cart_id' => $cart_id]);
             }
             else{
                 // Use named route with only order_id → cleaner & safer
                $returnUrl = route('student.successfulMeezan', ['order_id' => $order_id]);
             }
           
        }

        // === Prepare API Request ===
        $tokenApiUrl = rtrim($meezan_port_address, '/') . '/register.do';

        $urlPostParams = [
            'userName' => $meezan_user_name,
            'password' => $meezan_user_password,
            'orderNumber' => $merchantOrderId,
            'amount' => $amountInCents,
            'currency' => $meezan_pkr_currency_code,
            'returnUrl' => $returnUrl,
        ];

        $query = http_build_query($urlPostParams); // No need to urldecode()

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $tokenApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_USERAGENT => 'LMS-Meezan-Integration/1.0',
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('cURL Error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        Log::info('Meezan Bank API Request', [
            'url' => $tokenApiUrl,
            'payload' => $urlPostParams,
            'response' => $result,
            'http_code' => $httpCode,
        ]);

        // === Handle Response ===
        if (!isset($result['errorCode']) || $result['errorCode'] != 0) {
            $errorMessage = $result['errorMessage'] ?? 'Unknown error from Meezan Bank.';
            return [
                'errorMessage' => "<strong>Meezan Payment Failed:</strong> {$errorMessage}",
            ];
        }

        $generatedFormUrl = $result['formUrl'] ?? null;
        $meezanTransactionId = $result['orderId'] ?? null; // Their internal ID

        if (!$generatedFormUrl) {
            return [
                'errorMessage' => '<strong>Error:</strong> Missing redirect URL from Meezan.',
            ];
        }

        // Save Meezan transaction ID
        $order->meezan_transaction_id = $meezanTransactionId;
        $order->save();

        return [
            'redirectURL' => $generatedFormUrl,
        ];

    } catch (\Exception $ex) {
        Log::error('Exception in makeMeezanPayment', [
            'user_id' => $user_id,
            'order_id' => $order_id,
            'cart_id' => $cart_id,
            'message' => $ex->getMessage(),
            'trace' => $ex->getTraceAsString(),
        ]);

        return [
            'errorMessage' => '<strong>System error:</strong> ' . e($ex->getMessage()),
        ];
    }
}

     private function checkOrderExists($payment_method , $cartID = null)
    {
        $check = Order::where('cart_id',$cartID)->first();
        if($check){
            return true;
        }
        else{
            return false;
        }
    }


    

    public function checkoutPayment($type = null , $cartID = null)
    {
       
        $data['createdByType'] = $type;
        $data['cartID'] = $cartID;
        $data['pageTitle'] = "Checkout";
        $cart = CartManagement::where('id',$cartID)->first();
        $courseName = $cart->course->title;
        $data['courseName'] = $courseName;
        $data['userInfo'] =  $cart->user;
   
       
        // $data['carts'] = CartManagement::whereUserId(@Auth::id())->get();

        if($data['cartID'] == null){
            $data['carts'] = CartManagement::whereUserId(@Auth::id())
            ->where('created_by_type',$type)->get();
            $tranAmount = (int)$data['carts']->sum('price');
        }
        else{
            $data['carts'] = CartManagement::whereUserId(@Auth::id())
            ->where('id',$data['cartID'])
            ->where('created_by_type',$type)->get();
            $tranAmount = (int)$data['carts']->sum('price');
        }

      

        $data['student'] = auth::user()->student;
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();
        $data['banks'] = Bank::orderBy('name', 'asc')->where('status', 1)->get();
      

        if($data['cartID'] == null){
            $cart = CartManagement::whereUserId(@Auth::id())
            ->where('created_by_type',$type)->first();
            $data['cartID'] = (int)$cart->id;

            // $data['paymentCartID'] = (int)$cart->id. time();
            $data['paymentCartID'] = 'LMS-P-'. time();
            $cartsData = CartManagement::whereUserId(@Auth::id())
            ->where('created_by_type',$type)->get();
            if(!$cartsData->isEmpty()){
                foreach ($cartsData as $key => $cartData) {
                    $cartData->faysal_order_id = $data['paymentCartID'];
                    $cartData->update();
                }
            }
        }
        else{
            $cart = CartManagement::where('id',$data['cartID'])
            ->where('created_by_type',$type)->first();
            // $data['paymentCartID'] = (int)$data['cartID']. time(); 
            $data['paymentCartID'] = 'LMS-P-'. time();
            $data['cartID'] = (int)$data['cartID'];

            if($cart){
                $cart->faysal_order_id = $data['paymentCartID'];
                $cart->update();
            }
        }


        if (old('country_id')) {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id')) {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        $razorpay_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('razorpay_conversion_rate') ? get_option('razorpay_conversion_rate') : 0);
        $data['razorpay_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($razorpay_grand_total_with_conversion_rate, 2));

        $paystack_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0);
        $data['paystack_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($paystack_grand_total_with_conversion_rate, 2));

        $sslcommerz_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0);
        $data['sslcommerz_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($sslcommerz_grand_total_with_conversion_rate, 2));
        // dd($data);
     
        if(empty(get_option('faysal_base_url'))){
            $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
        }
        else{
            // $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
            $data['faysalBankUrl']= get_option('faysal_base_url');
        }

        if(empty(get_option('faysal_merchant_id'))){
            $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
        }
        else{
            // $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
            $data['faysalBankMerchantID']= get_option('faysal_merchant_id'); 
        }

        if(empty(get_option('faysal_secure_key'))){
            $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
        }
        else{
            // $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
            $faysal_bank_secure_key= get_option('faysal_secure_key'); 
        }
         // $basketID = $cart->id;
         $basketID = $data['paymentCartID'];
         $tranAmount = (int)$data['carts']->sum('price');
         $data['txnAmount'] = $tranAmount;
         // $tranAmount = (int)$cart->sum('price');

        $Currency = get_option('faysal_currency')?get_option('faysal_currency'):'PKR';


        $faysal_bank_token = $this->getFaysalBankAccessToken($data['faysalBankMerchantID'], $faysal_bank_secure_key,$tranAmount,$basketID,$Currency); 
    
        $data['faysalBankToken'] = $faysal_bank_token;
        $data['randSignature'] =  uniqid();
        $data['generalUrl']= route('student.pay');
        $data['paymentType'] = $type;
        if($cart->course){
            $data['isCourse'] = 1;
        }
        else{
            $data['isCourse'] = 0; 
        }
        // dd($data['faysalBankToken']);
        return view('frontend.student.cart.checkout', $data);
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
	'MERCHANT_ID=%s&SECURED_KEY=%s', 
	$merchant_id, 
	$secured_key,
    $trans_amount, 
    $basket_id,
    $Currency
	); 

    // $urlPostParams = sprintf( 
    //     'MERCHANT_ID=%s&SECURED_KEY=%s&TXNAMT=%s&BASKET_ID=%s&CURRENCY_CODE=%s',
    //     $merchant_id, 
    //     $secured_key,
    //     $trans_amount, 
    //     $basket_id,
    //     $Currency
    // ); 

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
    public function razorpay_payment(Request $request)
    {
        $input = $request->all();
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        if (empty(env('RAZORPAY_KEY')) && empty(env('RAZORPAY_SECRET'))) {
            $this->showToastrMessage('error', __('Razorpay payment gateway off!'));
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
            $payment = $api->payment->fetch($input['razorpay_payment_id']);
            $this->logger->log('transaction razorpay ', json_encode($payment));

            if (count($input) && !empty($input['razorpay_payment_id'])) {
                try {
                    $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                } catch (Exception $e) {
                    DB::rollBack();
                    Session::put('error', $e->getMessage());
                    return redirect()->back();
                }
            }

            $order_data = $this->placeOrder($request->payment_method);
            if($order_data['status']){
                $order = $order_data['data'];
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }

            $order->payment_status = 'paid';
            $order->payment_method = 'razorpay';

            CartManagement::whereUserId(@Auth::id())->delete();

            distributeCommission($order);

            $payment_currency = get_option('razorpay_currency');
            $conversion_rate = get_option('razorpay_conversion_rate') ? get_option('razorpay_conversion_rate') : 0;

            $order->payment_currency = $payment_currency;
            $order->conversion_rate = $conversion_rate;
            $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge) * $conversion_rate;
            $order->save();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logger->log('transaction failed razorpay', $exception->getMessage());
            $this->showToastrMessage('error', __('Something went wrong!'));
            return redirect()->back();
        }


        /** ====== Send notification =========*/
        $text = __("New student enrolled");
        $target_url = route('instructor.all-student');
        $sendMail = new EmailSendService();
        foreach ($order->items as $item) {
            if ($item->course) {
                $this->send($text, 2, $target_url, $item->course->user_id);
                //to instructor
                $sendMail->sendNewEnrollEmailToInstructor($item->course->user_id, $item->course, $target_url);

                //to admin
                $sendMail->sendNewEnrollEmailToAdmin($item->course);

                //to student
                $sendMail->sendNewEnrollEmailToStudent(auth()->user(), $item->course, route('student.my-learning'));
            }
        }

        $text = __("Course has been sold");
        $this->send($text, 1, null, null);

        /** ====== Send notification =========*/

        $this->showToastrMessage('success', __('Payment has been completed'));
        return redirect()->route('student.thank-you');
    }

    public function pay(Request $request)
    {

       
        $createdByType = $request->createdByType;
        $cartID = $request->cartID;
        $Location = $request->Location;
        $trialID = $request->trialID;
        $LiveEnrollment = $request->LiveEnrollment;
        if (is_null($request->payment_method)) {
            $this->showToastrMessage('warning', __('Please Select Payment Method'));
            return redirect()->back();
        }
        if ($request->payment_method == 'bank') {
            if (empty($request->deposit_by) || is_null($request->deposit_slip)) {
                $this->showToastrMessage('error', __('Bank Information Not Valid!'));
                return redirect()->back();
            }
        }
     
       
        if ($request->payment_method == 'paypal') {
            if (empty(env('PAYPAL_CLIENT_ID')) || empty(env('PAYPAL_SECRET')) || empty(env('PAYPAL_MODE'))) {
                $this->showToastrMessage('error', __('Paypal payment gateway is off!'));
                return redirect()->back();
            }
        }

        if ($request->payment_method == 'mollie') {
            if (empty(env('MOLLIE_KEY'))) {
                $this->showToastrMessage('error', __('Mollie payment gateway is off!'));
                return redirect()->back();
            }
        }

        if ($request->payment_method == 'instamojo') {
            if (empty(env('IM_API_KEY')) || empty(env('IM_AUTH_TOKEN')) || empty(env('IM_URL'))) {
                $this->showToastrMessage('error', __('Instamojo payment gateway is off!'));
                return redirect()->back();
            }
        }
        if ($request->payment_method == 'paystack') {
            if (empty(env('PAYSTACK_PUBLIC_KEY')) || empty(env('PAYSTACK_SECRET_KEY'))) {
                $this->showToastrMessage('error', __('Paystack payment gateway is off!'));
                return redirect()->back();
            }
        }
        if ($request->payment_method == 'coinbase') {
            if (empty(get_option('coinbase_key'))) {
                $this->showToastrMessage('error', __('Coinbase payment gateway is off!'));
                return redirect()->back();
            }
        }
        if ($request->payment_method == 'zitopay') {
            if (empty(get_option('zitopay_username'))) {
                $this->showToastrMessage('error', __('Zitopay payment gateway is off!'));
                return redirect()->back();
            }
        }
        if ($request->payment_method == 'iyzipay') {
            if (empty(get_option('iyzipay_key'))) {
                $this->showToastrMessage('error', __('Iyzipay payment gateway is off!'));
                return redirect()->back();
            }
        }
        if ($request->payment_method == 'bitpay') {
            if (empty(get_option('bitpay_key'))) {
                $this->showToastrMessage('error', __('Bitpay payment gateway is off!'));
                return redirect()->back();
            }
        }
        if ($request->payment_method == 'braintree') {
            if (empty(get_option('braintree_key'))) {
                $this->showToastrMessage('error', __('Braintree payment gateway is off!'));
                return redirect()->back();
            }
        }
        if ($request->payment_method == 'faysal_bank') {
            if (empty(get_option('faysal_merchant_id')) || empty(get_option('faysal_secure_key')) || empty(get_option('faysal_base_url'))
            || empty(get_option('faysal_access_token_url'))) {
                $this->showToastrMessage('error', __('Faysal bank payment gateway is off!'));
                return redirect()->back();
            }
        }
        if ($request->payment_method == 'meezan_bank') {
            if (empty(get_option('meezan_user_name')) || empty(get_option('meezan_user_password')) || empty(get_option('meezan_port_address'))) {
                $this->showToastrMessage('error', __('Meezan bank payment gateway is off!'));
                return redirect()->back();
            }
        }
   
      
        if($cartID != null){
            $order_data = $this->placeOrder($request->payment_method , $cartID,1);
        }
        else{
            $order_data = $this->placeOrder($request->payment_method,null,1); 
        }
     
           
        if($order_data['status']){
            $order = $order_data['data'];
        }else{
            $this->showToastrMessage('error', __('Something went wrong!'));
            return redirect()->back();
        }
        /** order billing address */
     
        if (auth::user()->student) {
            $student = Student::find(auth::user()->student->id);
            $student->fill($request->all());
            $student->save();
        }
        // dd(FAYSAL_BANK);
        if ($request->payment_method == PAYPAL) {
            $total = $order->grand_total * (get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => PAYPAL,
                'currency' => get_option('paypal_currency')
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);
            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }

        }
       else if ($request->payment_method == MOLLIE) {
            $object = [
                'id' => $order->uuid,
                'payment_method' => MOLLIE,
                'currency' => get_option('mollie_currency')
            ];
            $total = $order->grand_total * (get_option('mollie_conversion_rate') ? get_option('mollie_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', $responseData['message']);
                return redirect()->back();
            }
    } else if ($request->payment_method == MERCADOPAGO) {
            $object = [
                'id' => $order->uuid,
                'payment_method' => MERCADOPAGO,
                'currency' => get_option('mercado_currency')
            ];
            $total = $order->grand_total * (get_option('mercado_conversion_rate') ? get_option('mercado_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', $responseData['message']);
                return redirect()->back();
            }
    } else if ($request->payment_method == FLUTTERWAVE) {
            $object = [
                'id' => $order->uuid,
                'payment_method' => FLUTTERWAVE,
                'currency' => get_option('flutterwave_currency')
            ];
            $total = $order->grand_total * (get_option('flutterwave_conversion_rate') ? get_option('flutterwave_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', $responseData['message']);
                return redirect()->back();
            }
    } else if ($request->payment_method == INSTAMOJO) {
            $total = $order->grand_total * (get_option('im_conversion_rate') ? get_option('im_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => INSTAMOJO,
                'currency' => get_option('im_currency')
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }

    } else if ($request->payment_method == PAYSTAC) {
            $total = $order->grand_total * (get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => PAYSTAC,
                'currency' => get_option('paystack_currency'),
                'reference' => $request->reference
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }

    } else if ($request->payment_method == COINBASE) {
            $total = $order->grand_total * (get_option('coinbase_conversion_rate') ? get_option('coinbase_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => COINBASE,
                'currency' => get_option('coinbase_currency'),
                'reference' => $request->reference
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }
    } else if ($request->payment_method == ZITOPAY) {
            $total = $order->grand_total * (get_option('zitopay_conversion_rate') ? get_option('zitopay_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => ZITOPAY,
                'currency' => get_option('zitopay_currency'),
                'reference' => $request->reference
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }
    } else if ($request->payment_method == IYZIPAY) {
            $total = $order->grand_total * (get_option('iyzipay_conversion_rate') ? get_option('iyzipay_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => IYZIPAY,
                'currency' => get_option('iyzipay_currency'),
                'reference' => $request->reference
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }
    } else if ($request->payment_method == BITPAY) {
            $total = $order->grand_total * (get_option('bitpay_conversion_rate') ? get_option('bitpay_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => BITPAY,
                'currency' => get_option('bitpay_currency'),
                'reference' => $request->reference
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }
    } else if ($request->payment_method == BRAINTREE) {
            $total = $order->grand_total * (get_option('braintree_conversion_rate') ? get_option('braintree_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => BRAINTREE,
                'currency' => get_option('braintree_currency'),
                'reference' => $request->reference
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }

    } else if ($request->payment_method == BANK) {
            $deposit_by = $request->deposit_by;
            $deposit_slip = $this->uploadFileWithDetails('bank', $request->deposit_slip);
            if (!$deposit_slip['is_uploaded']) {
                $this->showToastrMessage('error', __('Something went wrong! Failed to upload file'));
                return redirect()->back();
            }

            $order->payment_status = 'pending';
            $order->deposit_by = $deposit_by;
            $order->deposit_slip = $deposit_slip['path'];
            $order->payment_method = 'bank';
            $order->bank_id = $request->bank_id;
            $order->save();

            if($createdByType == 1){
                CartManagement::whereUserId(@Auth::id())
                ->where('created_by_type',$createdByType)->delete();
            }
            else if($createdByType == 2){
                if($cartID != null){
                    CartManagement::whereUserId(@Auth::id())
                    ->where('id',$cartID)
                    ->where('created_by_type',$createdByType)->delete();
                }
                else{
                    CartManagement::whereUserId(@Auth::id())
                    ->where('created_by_type',$createdByType)->delete();
                }
            }
         

            /** ====== Send notification =========*/
            $text = __("New course enrolled pending request");
            $target_url = route('report.order-pending');
            $this->send($text, 1, $target_url, null);
            $sendMail = new EmailSendService();
            //to admin
            $sendMail->sendBankPendingOrderToAdmin($order, $target_url);

            //to student
            $sendMail->sendBankPendingOrderToStudent(auth()->user(), $order);
            /** ====== Send notification =========*/
            $this->showToastrMessage('success', __('Request has been Placed! Please Wait for Approve'));
            return redirect()->route('student.thank-you');
    } 
    else if ($request->payment_method == SSLCOMMERZ)  {

            $total = $order->grand_total * (get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            # CUSTOMER INFORMATION
            $post_data = array();
            $post_data['tran_id'] = $order->uuid; // tran_id must be unique
            $post_data['product_category'] = "Payment for purchase";
            $phone = '0170000';
            $student = $order->user->student;

            $post_data['cus_name'] = Auth::user()->name;
            $post_data['cus_phone'] = $request->input('phone_number',$student->address);
            $post_data['cus_email'] = $request->input('email',$order->user->email);
            $post_data['cus_add1'] = $request->input('address',$student->address);
            $post_data['cus_add2'] = "";
            $post_data['cus_city'] = "";
            $post_data['cus_state'] = "";
            $post_data['cus_postcode'] = $request->input('postal_code','017');
            $post_data['cus_country'] = @$student->country->country_name ?? 'BD';
            $post_data['cus_phone'] = $phone;
            $post_data['cus_fax'] = "";

            # SHIPMENT INFORMATION
            $post_data['ship_name'] = get_option('app_name') ?? 'LMS Store';
            $post_data['ship_add1'] = $request->input('phone_number',$student->address);
            $post_data['ship_add2'] =  '';
            $post_data['ship_city'] =  '';
            $post_data['ship_state'] =  '';
            $post_data['ship_postcode'] = '';
            $post_data['ship_phone'] = $request->input('phone_number',$student->address);
            $post_data['ship_country'] = @$student->country->country_name ?? 'BD';

            $post_data['shipping_method'] = "NO";
            $post_data['product_name'] = "Course Buy";

            $post_data['product_profile'] = "digital-goods";

            # OPTIONAL PARAMETERS
            $post_data['value_a'] = "ref001";
            $post_data['value_b'] = "ref002";
            $post_data['value_c'] = "ref003";
            $post_data['value_d'] = "ref004";

            $object = [
                'id' => $order->uuid,
                'payment_method' => SSLCOMMERZ,
                'currency' => get_option('sslcommerz_currency')
            ];

            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total,$post_data);
            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }
    } else if ($request->payment_method == STRIPE)  {

            $total = $order->grand_total * (get_option('stripe_conversion_rate') ? get_option('stripe_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');

            $object = [
                'id' => $order->uuid,
                'payment_method' => STRIPE,
                'currency' => get_option('stripe_currency'),
            ];

            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);

            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }
    }
        else if ($request->payment_method == FAYSAL_BANK)  {

            $total = $order->grand_total * (get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');

            $object = [
                'id' => $order->uuid,
                'payment_method' => FAYSAL_BANK,
                'currency' => get_option('faysal_currency'),
            ];
          
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);
       
            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                return Redirect::away($responseData['redirect_url']);
            }else{
                $this->showToastrMessage('error', __('Something went wrong!'));
                return redirect()->back();
            }
    }
        else if ($request->payment_method == MEEZAN_BANK)  {
         
            $total = $order->grand_total * (get_option('meezan_conversion_rate') ? get_option('meezan_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');

            $object = [
                'id' => $order->uuid,
                'payment_method' => MEEZAN_BANK,
                'currency' => get_option('meezan_currency'),
            ];
          
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total ,null ,$order->id , $cartID, $Location,$trialID,$LiveEnrollment);
         
        }
        else if($request->payment_method == CASH_ON){

            // if($createdByType == 1){
            //     CartManagement::whereUserId(@Auth::id())
            //     ->where('created_by_type',$createdByType)->delete();
            // }
            // else if($createdByType == 2){
            //     if($cartID != null){
            //         CartManagement::whereUserId(@Auth::id())
            //         ->where('id',$cartID)
            //         ->where('created_by_type',$createdByType)->delete();
            //     }
            //     else{
            //         CartManagement::whereUserId(@Auth::id())
            //         ->where('created_by_type',$createdByType)->delete();
            //     }
            // }
            $this->showToastrMessage('success', __('Order Via Cash On Delivery has been Placed! Please Wait for Admin Approval'));
            return redirect()->route('lms_product.student.purchase_list');
        }
    }


    public function successfulMeezan($order_id = null , $cart_id = null){
        $getOrderData = Order::where('id',$order_id)->first();
        $transactionID = $getOrderData->meezan_transaction_id;
        $meezanTransactionID = $getOrderData->meezan_transaction_id;
        $portAddress= get_option('meezan_port_address');
		$apiUserName= get_option('meezan_user_name');
		$apiUserPassword= get_option('meezan_user_password');
		$tokenApiUrl = $portAddress.'getOrderStatusExtended.do';
		$urlPostParams = array( 
			'userName'=>$apiUserName, 
			'password'=>$apiUserPassword, 
			'orderId'=>$meezanTransactionID, 
			); 
        $query=urldecode(http_build_query($urlPostParams));
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
        // dd($arrayResult);
        $errorCode = $arrayResult['errorCode'];
        $errorMsg = $arrayResult['errorMessage'];
        if($cart_id != null){
            $carts = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->get();
             $cart = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->first();
        }
        else{
            $carts = CartManagement::whereUserId(auth()->id())->get();
            $cart = null;
        }
        if($cart){
            $courseID = $cart->course_id;
        }
        else{
            foreach ($carts as $key => $cart) {
                if($cart->course_id){
                     $courseID = $cart->course_id;
                }
            }
        }
        $courseExits = Course::find($courseID);
      
        if($errorCode == 0){
            $orderStatus = $arrayResult['orderStatus'];
            $actionCode = $arrayResult['actionCode'];
            $actionDescription = $arrayResult['actionCodeDescription'];
            $amount = $arrayResult['amount'];
            $date = $arrayResult['date'];
            $formatted_date = date('y-m-d h:i:sa', $date);
            
            $orderNumber = $arrayResult['orderNumber'];
           
            if($orderStatus == 2){  //SUCCESSFULL
                $getPaymentData = Order::where('id',$order_id)->first();
                $successMsg = $errorMsg."<br>Amount was deposited successfully.";
                $getPaymentData->meezan_transaction_id = $transactionID;
                $getPaymentData->payment_id = $transactionID;
                $getPaymentData->meezan_payment_source = 'Card';
                $getPaymentData->meezan_order_number = $orderNumber;
                $getPaymentData->meezan_payment_date = date('y-m-d h:i:sa');
                $getPaymentData->meezan_trans_response = $response;
                if($cart_id != null){
                $getPaymentData->cart_type = 2;
                $getPaymentData->created_by_type = 2;
                }
                else{
                $getPaymentData->cart_type = 1;
                $getPaymentData->created_by_type = 1;
                }
                $getPaymentData->payment_status = 'paid';
                $getPaymentData->delivery_status = 1;
                $getPaymentData->update();
                $getPaymentData = Order::where('id',$order_id)->first();

                  
                foreach ($getPaymentData->items as $key => $order_item) {
                    if($cart_id != null){
                        $order_item->cart_type = 2;
                        $order_item->created_by_type = 2;
                    }
                    else{
                        $order_item->cart_type = 1;
                        $order_item->created_by_type = 1;
                    }
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
    
                    if ($order_item->course_id) {
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
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (successfulMeezan) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (successfulMeezan) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);

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
                            if($order_item->created_by_type == 1){
                                $courseExits = Course::find($order_item->course_id);
                                $enrollment = Enrollment::where('user_id',auth()->id())->where('course_id',$courseExits->id)->orderBy('id','DESC')->first();
                                if($enrollment){
                                    $enrollment->order_id = $order_item->order_id;
                                    $enrollment->user_id = auth()->id();
                                    $enrollment->course_id = $courseExits->id;
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
                                    $enrollment->course_id = $courseExits->id;
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
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (successfulMeezan(2)) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (successfulMeezan(2)) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);

                                }
                               
                            }
                        }
                    }
                }

                $target_url = route('student_payment.completed');
                $text = __("Student payment has been accepted by meezan bank.");
                $this->send($text, 1, $target_url, null);
                //Email Send Code
                $email_sent = $this->sendPaymentEmail($order_id);
                $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");

                if($cart_id != null){
                    $getCartData = CartManagement::where('id',$cart_id)->first();
                    if($getCartData){
                        $getCartData->delete();
                    }
                }
                else{
                    // CartManagement::whereUserId(@Auth::id())
                    //     ->where('created_by_type',1)->delete();
                    CartManagement::whereUserId(@Auth::id())
                        ->whereIn('created_by_type',[1,2])->delete();
                }

                return redirect()->route('student.pending-payments.index');
            }
            else{
                if($orderStatus == 6){
                    $errorMsg = "Authorization is declined.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 0){
                    $errorMsg = "Order registered, but not paid.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 1){
                    $errorMsg = "Transaction has been approved.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 3){
                    $errorMsg = "Authorization has been reversed.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 4){
                    $errorMsg = "Transaction has been refunded.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }

                if($cart_id != null){
                $getCartData = CartManagement::where('id',$cart_id)->first();
                if($getCartData){
                    $getCartData->error_trans_response = $response;
                    $getCartData->update();
                }
                }
                else{
                //  $getCartData = CartManagement::whereUserId(@Auth::id())
                //         ->where('created_by_type',1)->get();
                        
                $getCartData = CartManagement::whereUserId($user_id)
                            ->whereIn('created_by_type',[1.2])->get();
                    if(!$getCartData->isEmpty()){
                        foreach ($getCartData as $key => $value) {
                            $value->error_trans_response = $response;
                            $value->update();
                        }
                    }
                }
                $getOrderItemsData2 = Order_item::where('order_id',$order_id)->get();
                if(!$getOrderItemsData2->isEmpty()){
                    foreach ($getOrderItemsData2 as $key => $item) {
                        $item->delete();
                    }
                }
                $getOrderData2 = Order::where('id',$order_id)->first();
                $getOrderData2->delete();

                $getEnrollmentData = Enrollment::where('order_id',$order_id)->first();
                if($getEnrollmentData){
                    $getEnrollmentData->delete();
                }
                $this->showToastrMessage('warning',"<strong>". $errorMsg."</strong><br/>");
                return redirect()->route('student.pending-payments.index');
                // return redirect()->back();
            }
        }
        else{
            if($cart_id != null){
                $getCartData = CartManagement::where('id',$cart_id)->first();
                if($getCartData){
                    $getCartData->error_trans_response = $response;
                    $getCartData->update();
                }
                }
                else{
                 $getCartData = CartManagement::whereUserId(@Auth::id())
                        ->where('created_by_type',1)->get();
                        
                    if(!$getCartData->isEmpty()){
                        foreach ($getCartData as $key => $value) {
                            $value->error_trans_response = $response;
                            $value->update();
                        }
                    }
                }

            $getOrderItemsData2 = Order_item::where('order_id',$order_id)->get();
            if(!$getOrderItemsData2->isEmpty()){
                foreach ($getOrderItemsData2 as $key => $item) {
                    $item->delete();
                }
            }
            $getOrderData2 = Order::where('id',$order_id)->first();
            $getOrderData2->delete();
    
            $this->showToastrMessage('warning',"<strong>". $errorMsg."</strong><br/>");
            return redirect()->route('student.pending-payments.index');
        }
    }





     public function successfulMeezanWithCheckOut($order_id = null , $cart_id = null){
      
        $getOrderData = Order::where('id',$order_id)->first();
        
        $transactionID = $getOrderData->meezan_transaction_id;
        $meezanTransactionID = $getOrderData->meezan_transaction_id;
        $portAddress= get_option('meezan_port_address');
		$apiUserName= get_option('meezan_user_name');
		$apiUserPassword= get_option('meezan_user_password');
		$tokenApiUrl = $portAddress.'getOrderStatusExtended.do';
		$urlPostParams = array( 
			'userName'=>$apiUserName, 
			'password'=>$apiUserPassword, 
			'orderId'=>$meezanTransactionID, 
			); 
        $query=urldecode(http_build_query($urlPostParams));
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
        // dd($arrayResult);
        $errorCode = $arrayResult['errorCode'];
        $errorMsg = $arrayResult['errorMessage'];
        if($cart_id != null){
            $carts = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->get();
             $cart = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->first();
         }
         else{
             $carts = CartManagement::whereUserId(auth()->id())->get();
             $cart = null;
         }
         if($cart){
             $courseID = $cart->course_id;
         }
         else{
             foreach ($carts as $key => $cart) {
                 if($cart->course_id){
                      $courseID = $cart->course_id;
                 }
             }
         }
         
        $courseExits = Course::find($courseID);
         
        if($errorCode == 0){
            $orderStatus = $arrayResult['orderStatus'];
            $actionCode = $arrayResult['actionCode'];
            $actionDescription = $arrayResult['actionCodeDescription'];
            $amount = $arrayResult['amount'];
            $date = $arrayResult['date'];
            $formatted_date = date('y-m-d h:i:sa', $date);
            
            $orderNumber = $arrayResult['orderNumber'];
         
            if($orderStatus == 2){  //SUCCESSFULL
                $getPaymentData = Order::where('id',$order_id)->first();
                $successMsg = $errorMsg."<br>Amount was deposited successfully.";
                $getPaymentData->meezan_transaction_id = $transactionID;
                $getPaymentData->payment_id = $transactionID;
                $getPaymentData->meezan_payment_source = 'Card';
                $getPaymentData->meezan_order_number = $orderNumber;
                $getPaymentData->meezan_payment_date = date('y-m-d h:i:sa');
                $getPaymentData->meezan_trans_response = $response;

             
                if($cart_id != null){
                    $getPaymentData->cart_type = 2;
                    $getPaymentData->created_by_type = 2;
                }
                else{
                    $getPaymentData->cart_type = 1;
                    $getPaymentData->created_by_type = 1;
                }
                $getPaymentData->payment_status = 'paid';
                $getPaymentData->delivery_status = 1;
               
                $getPaymentData->update();
                $getPaymentData = Order::where('id',$order_id)->first();

                foreach ($getPaymentData->items as $key => $order_item) {
                    if($cart_id != null){
                        $order_item->cart_type = 2;
                        $order_item->created_by_type = 2;
                    }
                    else{
                        $order_item->cart_type = 1;
                        $order_item->created_by_type = 1;
                    }
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
    
                    if ($order_item->course_id) {
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
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (successfulMeezanWithCheckout) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (successfulMeezanWithCheckout) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
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
                            if($order_item->created_by_type == 1){
                                $courseExits = Course::find($order_item->course_id);
                                $enrollment = Enrollment::where('user_id',auth()->id())->where('course_id',$courseExits->id)->orderBy('id','DESC')->first();
                                if($enrollment){
                                    $enrollment->order_id = $order_item->order_id;
                                    $enrollment->user_id = auth()->id();
                                    $enrollment->course_id = $courseExits->id;
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
                                    $enrollment->course_id = $courseExits->id;
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
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (successfulMeezanWithCheckout(2)) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (successfulMeezanWithCheckout(2)) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);

                                }
                             
                            }
                        }
                    }
                }

                   
        
                $target_url = route('student_payment.completed');
                $text = __("Student payment has been accepted by meezan bank.");
                $this->send($text, 1, $target_url, null);

                //Email Send Code
                $email_sent = $this->sendPaymentEmail($order_id);
                $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");

                if($cart_id != null){
                    $getCartData = CartManagement::where('id',$cart_id)->first();
                    if($getCartData){
                        $getCartData->delete();
                    }
                }
                else{
                      CartManagement::whereUserId(@Auth::id())
                        ->where('created_by_type',1)->delete();
                }
                // return redirect()->route('course-details',['slug'=>$courseExits->slug]);
                return redirect()->route('main.index');
            }
            else{
                if($orderStatus == 6){
                    $errorMsg = "Authorization is declined.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 0){
                    $errorMsg = "Order registered, but not paid.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 1){
                    $errorMsg = "Transaction has been approved.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 3){
                    $errorMsg = "Authorization has been reversed.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 4){
                    $errorMsg = "Transaction has been refunded.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }

                if($cart_id != null){
                $getCartData = CartManagement::where('id',$cart_id)->first();
                if($getCartData){
                    $getCartData->error_trans_response = $response;
                    $getCartData->update();
                }
                }
                else{
                 $getCartData = CartManagement::whereUserId(@Auth::id())
                        ->where('created_by_type',1)->get();
                        
                    if(!$getCartData->isEmpty()){
                        foreach ($getCartData as $key => $value) {
                            $value->error_trans_response = $response;
                            $value->update();
                        }
                    }
                }
                $getOrderItemsData2 = Order_item::where('order_id',$order_id)->get();
                if(!$getOrderItemsData2->isEmpty()){
                    foreach ($getOrderItemsData2 as $key => $item) {
                        $item->delete();
                    }
                }
                $getOrderData2 = Order::where('id',$order_id)->first();
                $getOrderData2->delete();

                $getEnrollmentData = Enrollment::where('order_id',$order_id)->first();
                if($getEnrollmentData){
                    $getEnrollmentData->delete();
                }
                $this->showToastrMessage('warning',"<strong>". $errorMsg."</strong><br/>");
              
                return redirect()->route('main.index');
                // return redirect()->back();
            }
        }
        else{
            if($cart_id != null){
                $getCartData = CartManagement::where('id',$cart_id)->first();
                if($getCartData){
                    $getCartData->error_trans_response = $response;
                    $getCartData->update();
                }
                }
                else{
                 $getCartData = CartManagement::whereUserId(@Auth::id())
                        ->where('created_by_type',1)->get();
                        
                    if(!$getCartData->isEmpty()){
                        foreach ($getCartData as $key => $value) {
                            $value->error_trans_response = $response;
                            $value->update();
                        }
                    }
                }

            $getOrderItemsData2 = Order_item::where('order_id',$order_id)->get();
            if(!$getOrderItemsData2->isEmpty()){
                foreach ($getOrderItemsData2 as $key => $item) {
                    $item->delete();
                }
            }
            $getOrderData2 = Order::where('id',$order_id)->first();
            $getOrderData2->delete();
    
            $this->showToastrMessage('warning',"<strong>". $errorMsg."</strong><br/>");
            return redirect()->route('main.index');
        }
    }













    //    public function successfulMeezanWithCheckOut($order_id = null , $cart_id = null, $trial_id = null, $live_enrollment = 0){
      
    //     $getOrderData = Order::where('id',$order_id)->first();
        
    //     $transactionID = $getOrderData->meezan_transaction_id;
    //     $meezanTransactionID = $getOrderData->meezan_transaction_id;
    //     $portAddress= get_option('meezan_port_address');
	// 	$apiUserName= get_option('meezan_user_name');
	// 	$apiUserPassword= get_option('meezan_user_password');
	// 	$tokenApiUrl = $portAddress.'getOrderStatusExtended.do';
	// 	$urlPostParams = array( 
	// 		'userName'=>$apiUserName, 
	// 		'password'=>$apiUserPassword, 
	// 		'orderId'=>$meezanTransactionID, 
	// 		); 
    //     $query=urldecode(http_build_query($urlPostParams));
    //     $ch = curl_init(); 
    //     curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    //     curl_setopt($ch, CURLOPT_POST, 1); 
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $query); 
    //     curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Meezan API Example'); 
    //     $headers = array(
    //         'Content-Type: application/x-www-form-urlencoded',
    //     );
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     $response = curl_exec($ch);
    //     $arrayResult = json_decode($response, true);
    //     curl_close($ch);  
    //     // dd($arrayResult);
    //     $errorCode = $arrayResult['errorCode'];
    //     $errorMsg = $arrayResult['errorMessage'];
    //     if($cart_id != null){
    //         $carts = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->get();
    //          $cart = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->first();
    //      }
    //      else{
    //          $carts = CartManagement::whereUserId(auth()->id())->get();
    //          $cart = null;
    //      }
    //      if($cart){
    //          $courseID = $cart->course_id;
    //      }
    //      else{
    //          foreach ($carts as $key => $cart) {
    //              if($cart->course_id){
    //                   $courseID = $cart->course_id;
    //              }
    //          }
    //      }
         
    //     $courseExits = Course::find($courseID);
         
    //     if($errorCode == 0){
    //         $orderStatus = $arrayResult['orderStatus'];
    //         $actionCode = $arrayResult['actionCode'];
    //         $actionDescription = $arrayResult['actionCodeDescription'];
    //         $amount = $arrayResult['amount'];
    //         $date = $arrayResult['date'];
    //         $formatted_date = date('y-m-d h:i:sa', $date);
            
    //         $orderNumber = $arrayResult['orderNumber'];
         
    //         if($orderStatus == 2){  //SUCCESSFULL
    //             $getPaymentData = Order::where('id',$order_id)->first();
    //             $successMsg = $errorMsg."<br>Amount was deposited successfully.";
    //             $getPaymentData->meezan_transaction_id = $transactionID;
    //             $getPaymentData->payment_id = $transactionID;
    //             $getPaymentData->meezan_payment_source = 'Card';
    //             $getPaymentData->meezan_order_number = $orderNumber;
    //             $getPaymentData->meezan_payment_date = date('y-m-d h:i:sa');
    //             $getPaymentData->meezan_trans_response = $response;

             
    //             if($cart_id != null){
    //                 $getPaymentData->cart_type = 2;
    //                 $getPaymentData->created_by_type = 2;
    //             }
    //             else{
    //                 $getPaymentData->cart_type = 1;
    //                 $getPaymentData->created_by_type = 1;
    //             }
    //             $getPaymentData->payment_status = 'paid';
    //             $getPaymentData->delivery_status = 1;
               
    //             $getPaymentData->update();


                   
            
                  
    //                 if(auth::user()->student->status == 0){
    //                             if($cart_id != null){
    //                                 $carts = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->get();
    //                                 $cart = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->first();
    //                             }
    //                             else{
    //                                 $carts = CartManagement::whereUserId(auth()->id())->get();
    //                                 $cart = null;
    //                             }

    //                             if($cart){
    //                                 $courseID = $cart->course_id;
    //                             }
    //                             else{
    //                                 foreach ($carts as $key => $cart) {
    //                                     if($cart->course_id){
    //                                          $courseID = $cart->course_id;
    //                                     }
    //                                 }
    //                             }
    //                            $courseExits = Course::find($courseID);
    //                            $enrollment = new Enrollment();
    //                            $enrollment->order_id = $getPaymentData->id;
    //                            $enrollment->user_id = auth()->id();
    //                            $enrollment->course_id = $courseID;
    //                            $enrollment->owner_user_id = $courseExits->user_id ?? null;
    //                            $enrollment->start_date = now();
    //                            $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                               
    //                             if($live_enrollment){
    //                                 $enrollment->trial_id = $trial_id;
    //                                 $getTrialInfo = TrialClass::where('id',$trial_id)->first();
    //                                 if($getTrialInfo){
    //                                     $getTrialInfo->student_enrolled = 1;
    //                                     $getTrialInfo->save();
    //                                 }
    //                             }

    //                            $enrollment->save();

    //                             $getStudent = Student::where('id',auth::user()->student->id)->first();
    //                             if($getStudent){
    //                                 $getStudent->status = 1;
    //                                 $getStudent->save();
    //                             }

                 
    //                             $template_name =  'student-sign-up'; 
                                
                                
    //                             sendEmailNotification([
    //                             '{{USER_NAME}}' => auth::user()->name,
    //                             '{{EMAIL}}' => auth::user()->email,
    //                             '{{PASSWORD}}' => auth::user()->random_password,
    //                             ], auth::user()->email, $template_name);



    //                 }
    //                 else{
    //                     if($getPaymentData->created_by_type == 1){
    //                         if($cart_id != null){
    //                             $carts = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->get();
    //                             $cart = CartManagement::whereUserId(auth()->id())->where('id',$cart_id)->first();
    //                         }
    //                         else{
    //                             $carts = CartManagement::whereUserId(auth()->id())->get();
    //                             $cart = null;
    //                         }
    //                         if($cart){
    //                             $courseID = $cart->course_id;
    //                         }
    //                         else{
    //                             foreach ($carts as $key => $cart) {
    //                                 if($cart->course_id){
    //                                      $courseID = $cart->course_id;
    //                                 }
    //                             }
    //                         }
    //                         $courseExits = Course::find($courseID);
    //                         $enrollment = new Enrollment();
    //                         $enrollment->order_id = $getPaymentData->id;
    //                         $enrollment->user_id = auth()->id();
    //                         $enrollment->course_id = $courseExits->id;
    //                         $enrollment->owner_user_id = $courseExits->user_id ?? null;
    //                         $enrollment->start_date = now();
    //                         $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                            
    //                         if($live_enrollment){
    //                             $enrollment->trial_id = $trial_id;
    //                             $getTrialInfo = TrialClass::where('id',$trial_id)->first();
    //                             if($getTrialInfo){
    //                                 $getTrialInfo->student_enrolled = 1;
    //                                 $getTrialInfo->save();
    //                             }
    //                         }

                            
    //                         $enrollment->save();
    //                     }
    //                 }

    //                 $target_url = route('student_payment.completed');
    //                 $text = __("Student payment has been accepted by meezan bank.");
    //                 $this->send($text, 1, $target_url, null);

    //                 //Email Send Code
    //                 $email_sent = $this->sendPaymentEmail($order_id);
    //                 $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");

    //             if($cart_id != null){
    //                 $getCartData = CartManagement::where('id',$cart_id)->first();
    //                 if($getCartData){
    //                     $getCartData->delete();
    //                 }
    //             }
    //             else{
    //                   CartManagement::whereUserId(@Auth::id())
    //                     ->where('created_by_type',1)->delete();
    //             }

    //             // return redirect()->route('course-details',['slug'=>$courseExits->slug]);
    //             return redirect()->route('main.index');
    //         }
    //         else{
    //             if($orderStatus == 6){
    //                 $errorMsg = "Authorization is declined.<br>";
    //                 if($actionCode != 0){
    //                     $errorMsg .= $actionDescription;	
    //                 }
    //             }
    //             else if($orderStatus == 0){
    //                 $errorMsg = "Order registered, but not paid.<br>";
    //                 if($actionCode != 0){
    //                     $errorMsg .= $actionDescription;	
    //                 }
    //             }
    //             else if($orderStatus == 1){
    //                 $errorMsg = "Transaction has been approved.<br>";
    //                 if($actionCode != 0){
    //                     $errorMsg .= $actionDescription;	
    //                 }
    //             }
    //             else if($orderStatus == 3){
    //                 $errorMsg = "Authorization has been reversed.<br>";
    //                 if($actionCode != 0){
    //                     $errorMsg .= $actionDescription;	
    //                 }
    //             }
    //             else if($orderStatus == 4){
    //                 $errorMsg = "Transaction has been refunded.<br>";
    //                 if($actionCode != 0){
    //                     $errorMsg .= $actionDescription;	
    //                 }
    //             }

    //             if($cart_id != null){
    //             $getCartData = CartManagement::where('id',$cart_id)->first();
    //             if($getCartData){
    //                 $getCartData->error_trans_response = $response;
    //                 $getCartData->update();
    //             }
    //             }
    //             else{
    //              $getCartData = CartManagement::whereUserId(@Auth::id())
    //                     ->where('created_by_type',1)->get();
                        
    //                 if(!$getCartData->isEmpty()){
    //                     foreach ($getCartData as $key => $value) {
    //                         $value->error_trans_response = $response;
    //                         $value->update();
    //                     }
    //                 }
    //             }
    //             $getOrderItemsData2 = Order_item::where('order_id',$order_id)->get();
    //             if(!$getOrderItemsData2->isEmpty()){
    //                 foreach ($getOrderItemsData2 as $key => $item) {
    //                     $item->delete();
    //                 }
    //             }
    //             $getOrderData2 = Order::where('id',$order_id)->first();
    //             $getOrderData2->delete();

    //             $getEnrollmentData = Enrollment::where('order_id',$order_id)->first();
    //             if($getEnrollmentData){
    //                 $getEnrollmentData->delete();
    //             }
    //             $this->showToastrMessage('warning',"<strong>". $errorMsg."</strong><br/>");
    //             // return redirect()->route('student.pending-payments.index');
    //             // return redirect()->route('course-details',['slug'=>$courseExits->slug]);
    //             return redirect()->route('main.index');
    //             // return redirect()->back();
    //         }
    //     }
    //     else{
    //         if($cart_id != null){
    //             $getCartData = CartManagement::where('id',$cart_id)->first();
    //             if($getCartData){
    //                 $getCartData->error_trans_response = $response;
    //                 $getCartData->update();
    //             }
    //             }
    //             else{
    //              $getCartData = CartManagement::whereUserId(@Auth::id())
    //                     ->where('created_by_type',1)->get();
                        
    //                 if(!$getCartData->isEmpty()){
    //                     foreach ($getCartData as $key => $value) {
    //                         $value->error_trans_response = $response;
    //                         $value->update();
    //                     }
    //                 }
    //             }

    //         $getOrderItemsData2 = Order_item::where('order_id',$order_id)->get();
    //         if(!$getOrderItemsData2->isEmpty()){
    //             foreach ($getOrderItemsData2 as $key => $item) {
    //                 $item->delete();
    //             }
    //         }
    //         $getOrderData2 = Order::where('id',$order_id)->first();
    //         $getOrderData2->delete();
    
    //         $this->showToastrMessage('warning',"<strong>". $errorMsg."</strong><br/>");
    //         // return redirect()->route('student.pending-payments.index');
    //         // return redirect()->route('course-details',['slug'=>$courseExits->slug]);
    //         return redirect()->route('main.index');
    //     }
    // }


    public function checkoutFaysal(Request $request , $cart_id = null){
    $ipnPayload = $request->all();

    if(empty(get_option('faysal_merchant_id'))){
        $merchantId= env('FAYSAL_BANK_MERCHANT_ID');
    }
    else{
       $merchantId= get_option('faysal_merchant_id'); 
    }
    if(empty(get_option('faysal_secure_key'))){
        $merchantSecretKey= env('FAYSAL_BANK_SECURE_KEY');
    }
    else{
        $merchantSecretKey= get_option('faysal_secure_key'); 
    }


    $errCode = $request->err_code;
    $errMsg = $request->err_msg;
    $paymentName = $request->PaymentName;
    $trans_id = $request->transaction_id;
    $order_date = $request->order_date;
    $response_key = $request->Response_Key; 
    $secretword = '';
    $original_basket_id = $request->basket_id; 
    $receivedHash = $request->validation_hash; 
    $txt_amount = $request->transaction_amount; 

    // === Step 1: Create the string in required order ===
    $stringToHash = "{$original_basket_id}|{$merchantSecretKey}|{$merchantId}|{$errCode}";
    // === Step 2: Generate SHA256 hash ===
    $calculatedHash = hash('sha256', $stringToHash);

    //=== Step 3: Compare hashes securely ===
    if (hash_equals($calculatedHash, $receivedHash)) {
          // Log the payload in CartManagement (if cart exists)
        if ($cart_id) {
            $getCartData = CartManagement::where('id', $cart_id)->first();
            if ($getCartData) {
                $getCartData->faysal_hash_original = $receivedHash;
                $getCartData->faysal_hash_calculated = $calculatedHash;
                $getCartData->faysal_ipn_payload = json_encode($ipnPayload, JSON_PRETTY_PRINT);
                $getCartData->save();
            }
        } else {
            exit;
            // CartManagement::whereUserId(auth()->id())
            //     ->where('created_by_type', 1)
            //     ->update([
            //         'faysal_ipn_payload' => json_encode($ipnPayload, JSON_PRETTY_PRINT),
            //         'faysal_hash_original' => $receivedHash,
            //         'faysal_hash_calculated' => $calculatedHash,
            //     ]);
        }


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
                exit;
                // $getCartData = CartManagement::whereUserId(auth::user()->id)
                //     ->where('created_by_type',1)->get();
                // $subTotal = $getCartData->sum('price');
                // $month = null;
                // $year = null;
                // $getCartData = CartManagement::whereUserId(auth::user()->id)
                //     ->where('created_by_type',1)->first();
                // $student_user_id = $getCartData->user->id;   
                // $faysalOrderID =  $getCartData->faysal_order_id;
            }
        
            $getCheck = Order::where('faysal_transaction_id',$trans_id)->first();
        
            $grandTotal = $subTotal;
            $getStudentData = User::where('id', $student_user_id)->first();
            if($getCheck == null){

                if($cart_id != null){
                $carts = CartManagement::whereUserId($student_user_id)->where('id',$cart_id)->get();
                }
                else{
                    $carts = CartManagement::whereUserId($student_user_id)
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

                // Save raw IPN payload in Order
                $getPaymentData->faysal_hash_original = $receivedHash;
                $getPaymentData->faysal_hash_calculated = $calculatedHash;
                $getPaymentData->faysal_ipn_payload = json_encode($ipnPayload, JSON_PRETTY_PRINT);

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
                        $order_item->user_id = $student_user_id;
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
                                $enrollment = Enrollment::where('user_id',$student_user_id)->where('course_id',$cart->course_id)->orderBy('id','DESC')->first();
                                if($enrollment){
                                    $enrollment->order_id = $getPaymentData->id;
                                    $enrollment->user_id = $student_user_id;
                                    $enrollment->course_id = $cart->course_id;
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
                                    $enrollment->order_id = $getPaymentData->id;
                                    $enrollment->user_id = $student_user_id;
                                    $enrollment->course_id = $cart->course_id;
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
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (checkoutFaysal) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (checkoutFaysal) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                                }
                             
                        }

                        $this->addAffiliateHistory($cart,$getPaymentData,$order_item);
                    }
                    elseif ($cart->product_id) {
                        $order_item = new Order_item();
                        $order_item->order_id = $getPaymentData->id;
                        $order_item->user_id = $student_user_id;
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
                    $order_item->cart_id = $cart->id; $order_item->request_enrollment = $cart->request_enrollment;
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

            


                    if(auth::user()->student->status == 0){
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

                    $target_url = route('student_payment.completed');
                    $text = __("Student payment has been accepted by faysal bank.");
                    $this->send($text, 1, $target_url, null);

                    //Email Send Code

                    if($cart_id != null){
                        // $email_sent = $this->sendPaymentEmail($getPaymentData->id,$cart_id);
                        $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                    }
                    else{
                        // $CART =CartManagement::whereUserId(@Auth::id())
                        // ->where('created_by_type',1)->first();
                        // if($CART){
                        //     // $email_sent = $this->sendPaymentEmail($getPaymentData->id,$CART->id);
                        //     $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                        // }
                        // else{
                        //     // $email_sent = $this->sendPaymentEmail($getPaymentData->id);
                        //     $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                        // }            
                    }

                $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
                if($cart_id != null){
                    $getCartData = CartManagement::where('id',$cart_id)->first();
                    if($getCartData){
                        $getCartData->delete();
                    }
                }
                else{
                    //   CartManagement::whereUserId(auth::user()->id)
                    //     ->where('created_by_type',1)->delete();
                }

                // return redirect()->route('student.pending-payments.index');
            }
        }
    } 
    else {
        if ($cart_id) {
            $getCartData = CartManagement::where('id', $cart_id)->first();
            if ($getCartData) {
                $getCartData->faysal_hash_original = $receivedHash;
                $getCartData->faysal_hash_calculated = $calculatedHash;
                $getCartData->faysal_ipn_payload = json_encode($ipnPayload, JSON_PRETTY_PRINT);
                $getCartData->save();
            }
        } else {
            CartManagement::whereUserId(auth()->id())
                ->where('created_by_type', 1)
                ->update([
                    'faysal_ipn_payload' => json_encode($ipnPayload, JSON_PRETTY_PRINT),
                    'faysal_hash_original' => $receivedHash,
                    'faysal_hash_calculated' => $calculatedHash,
                ]);
        }
    }
    }




    public function checkoutFaysal2(Request $request , $cart_id = null){
    $ipnPayload = $request->all();

    if(empty(get_option('faysal_merchant_id'))){
        $merchantId= env('FAYSAL_BANK_MERCHANT_ID');
    }
    else{
       $merchantId= get_option('faysal_merchant_id'); 
    }
    if(empty(get_option('faysal_secure_key'))){
        $merchantSecretKey= env('FAYSAL_BANK_SECURE_KEY');
    }
    else{
        $merchantSecretKey= get_option('faysal_secure_key'); 
    }


    $errCode = $request->err_code;
    $errMsg = $request->err_msg;
    $paymentName = $request->PaymentName;
    $trans_id = $request->transaction_id;
    $order_date = $request->order_date;
    $response_key = $request->Response_Key; 
    $secretword = '';
    $original_basket_id = $request->basket_id; 
    $receivedHash = $request->validation_hash; 
    $txt_amount = $request->transaction_amount; 

    // === Step 1: Create the string in required order ===
    $stringToHash = "{$original_basket_id}|{$merchantSecretKey}|{$merchantId}|{$errCode}";
    // === Step 2: Generate SHA256 hash ===
    $calculatedHash = hash('sha256', $stringToHash);

    //=== Step 3: Compare hashes securely ===
    if (hash_equals($calculatedHash, $receivedHash)) {
          // Log the payload in CartManagement (if cart exists)
        if ($cart_id) {
            $getCartData = CartManagement::where('id', $cart_id)->first();
            if ($getCartData) {
                $getCartData->faysal_hash_original = $receivedHash;
                $getCartData->faysal_hash_calculated = $calculatedHash;
                $getCartData->faysal_ipn_payload = json_encode($ipnPayload, JSON_PRETTY_PRINT);
                $getCartData->save();
            }
        } else {
            exit;
            // CartManagement::whereUserId(auth()->id())
            //     ->where('created_by_type', 1)
            //     ->update([
            //         'faysal_ipn_payload' => json_encode($ipnPayload, JSON_PRETTY_PRINT),
            //         'faysal_hash_original' => $receivedHash,
            //         'faysal_hash_calculated' => $calculatedHash,
            //     ]);
        }


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
                exit;
                // $getCartData = CartManagement::whereUserId(auth::user()->id)
                //     ->where('created_by_type',1)->get();
                // $subTotal = $getCartData->sum('price');
                // $month = null;
                // $year = null;
                // $getCartData = CartManagement::whereUserId(auth::user()->id)
                //     ->where('created_by_type',1)->first();
                // $student_user_id = $getCartData->user->id;   
                // $faysalOrderID =  $getCartData->faysal_order_id;
            }
        
            $getCheck = Order::where('faysal_transaction_id',$trans_id)->first();
        
            $grandTotal = $subTotal;
            $getStudentData = User::where('id', $student_user_id)->first();
            if($getCheck == null){

                if($cart_id != null){
                $carts = CartManagement::whereUserId(auth::user()->id)->where('id',$cart_id)->get();
                }
                else{
                    $carts = CartManagement::whereUserId(auth::user()->id)
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

                // Save raw IPN payload in Order
                $getPaymentData->faysal_hash_original = $receivedHash;
                $getPaymentData->faysal_hash_calculated = $calculatedHash;
                $getPaymentData->faysal_ipn_payload = json_encode($ipnPayload, JSON_PRETTY_PRINT);

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
                        $order_item->user_id = auth::user()->id;
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
                                $enrollment = Enrollment::where('user_id',auth::user()->id)->where('course_id',$cart->course_id)->orderBy('id','DESC')->first();
                                if($enrollment){
                                    $enrollment->order_id = $getPaymentData->id;
                                    $enrollment->user_id = auth::user()->id;
                                    $enrollment->course_id = $cart->course_id;
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
                                    $enrollment->order_id = $getPaymentData->id;
                                    $enrollment->user_id = auth::user()->id;
                                    $enrollment->course_id = $cart->course_id;
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
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (checkoutFaysal2) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (checkoutFaysal2) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                                }
                              
                        }

                        $this->addAffiliateHistory($cart,$getPaymentData,$order_item);
                    }
                    elseif ($cart->product_id) {
                        $order_item = new Order_item();
                        $order_item->order_id = $getPaymentData->id;
                        $order_item->user_id = auth::user()->id;
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

            


                    if(auth::user()->student->status == 0){
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

                    $target_url = route('student_payment.completed');
                    $text = __("Student payment has been accepted by faysal bank.");
                    $this->send($text, 1, $target_url, null);

                    //Email Send Code

                    if($cart_id != null){
                        // $email_sent = $this->sendPaymentEmail($getPaymentData->id,$cart_id);
                        $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                    }
                    else{
                        $CART =CartManagement::whereUserId(@Auth::id())
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

                $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
                if($cart_id != null){
                    $getCartData = CartManagement::where('id',$cart_id)->first();
                    if($getCartData){
                        $getCartData->delete();
                    }
                }
                else{
                      CartManagement::whereUserId(auth::user()->id)
                        ->where('created_by_type',1)->delete();
                }

                return redirect()->route('student.pending-payments.index');
            }
        }
    } 
    else {
        if ($cart_id) {
            $getCartData = CartManagement::where('id', $cart_id)->first();
            if ($getCartData) {
                $getCartData->faysal_hash_original = $receivedHash;
                $getCartData->faysal_hash_calculated = $calculatedHash;
                $getCartData->faysal_ipn_payload = json_encode($ipnPayload, JSON_PRETTY_PRINT);
                $getCartData->save();
            }
        } else {
            CartManagement::whereUserId(auth()->id())
                ->where('created_by_type', 1)
                ->update([
                    'faysal_ipn_payload' => json_encode($ipnPayload, JSON_PRETTY_PRINT),
                    'faysal_hash_original' => $receivedHash,
                    'faysal_hash_calculated' => $calculatedHash,
                ]);
        }
    }
    }

    public function successfulFaysal2(Request $request , $cart_id = null){
    
        $ipnPayload = $request->all();
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
                    $getCartData = CartManagement::whereUserId(auth::user()->id)
                        ->where('created_by_type',1)->get();
                    $subTotal = $getCartData->sum('price');
                    $month = null;
                    $year = null;
                    $getCartData = CartManagement::whereUserId(auth::user()->id)
                        ->where('created_by_type',1)->first();
                    $student_user_id = $getCartData->user->id;   
                    $faysalOrderID =  $getCartData->faysal_order_id;
                }
            
                $getCheck = Order::where('faysal_transaction_id',$trans_id)->first();
            
                $grandTotal = $subTotal;
                $getStudentData = User::where('id', $student_user_id)->first();
            if($getCheck == null){
            
                if($cart_id != null){
                $carts = CartManagement::whereUserId(auth::user()->id)->where('id',$cart_id)->get();
                }
                else{
                    $carts = CartManagement::whereUserId(auth::user()->id)
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

                // Save raw IPN payload in Order
                $getPaymentData->faysal_ipn_payload = json_encode($ipnPayload, JSON_PRETTY_PRINT);

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
                        $order_item->user_id = auth::user()->id;
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
                                $enrollment = Enrollment::where('user_id',auth::user()->id)->where('course_id',$cart->course_id)->orderBy('id','DESC')->first();
                                if($enrollment){
                                    $enrollment->order_id = $getPaymentData->id;
                                    $enrollment->user_id = auth::user()->id;
                                    $enrollment->course_id = $cart->course_id;
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
                                    $enrollment->save();
                                }
                                else{
                                    $enrollment = new Enrollment();
                                    $enrollment->order_id = $getPaymentData->id;
                                    $enrollment->user_id = auth::user()->id;
                                    $enrollment->course_id = $cart->course_id;
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
                                    $enrollment->save();
                                    Log::info("NEW ENROLLMENT BEING CREATED IN (successfulFaysal2) IN STUDENT/CARTMANAGEMENTCONTROLLER AT ".Carbon::now());
                                    Log::info("NEW ENROLLMENT DETAILS IN (successfulFaysal2) IN STUDENT/CARTMANAGEMENTCONTROLLER:- ".$enrollment);
                                }
                             
                        }

                        $this->addAffiliateHistory($cart,$getPaymentData,$order_item);
                    }
                    elseif ($cart->product_id) {
                        $order_item = new Order_item();
                        $order_item->order_id = $getPaymentData->id;
                        $order_item->user_id = auth::user()->id;
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

            


                    if(auth::user()->student->status == 0){
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

                    $target_url = route('student_payment.completed');
                    $text = __("Student payment has been accepted by faysal bank.");
                    $this->send($text, 1, $target_url, null);

                    //Email Send Code

                    if($cart_id != null){
                        // $email_sent = $this->sendPaymentEmail($getPaymentData->id,$cart_id);
                        $email_sent = $this->sendPaymentEmail($getPaymentData->id,$getPaymentData->faysal_order_id);
                    }
                    else{
                        $CART =CartManagement::whereUserId(@Auth::id())
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

                    $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
                    if($cart_id != null){
                        $getCartData = CartManagement::where('id',$cart_id)->first();
                        if($getCartData){
                            $getCartData->delete();
                        }
                    }
                    else{
                          CartManagement::whereUserId(auth::user()->id)
                            ->where('created_by_type',1)->delete();
                    }
                
                    return redirect()->route('student.pending-payments.index');

                }
        }
        else{
            $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
            return redirect()->route('student.pending-payments.index');
		}

    }


     public function successfulFaysalApp(Request $request , $cart_id = null){
    
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
        
                $successMsg = "Thank You. Your Transaction Amount was deposited successfully at faysal bank.";
                $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");

                return redirect()->route('student.pending-payments.index');
        }
        else{
            $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
            return redirect()->route('student.pending-payments.index');
		}

    }

    public function successfulFaysal(Request $request , $cart_id = null, $location = null){
    
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
        
            if($location == 1){
                    $slug = null;
                    $getCart = CartManagement::where('id',$cart_id)->first();
                    if($getCart){
                        $course_id = $getCart->course_id;
                    }
                    else{
                        $course_id = null;
                    }
                    if($course_id){
                        $getCourse = Course::where('id',$course_id)->first();
                        if($getCourse){
                            $slug = $getCourse->slug;
                        }
                        else{
                            $slug = null;
                        }
                    }
                    if($slug){
                        $successMsg = "Thank You. Your Transaction Amount was deposited successfully at faysal bank.";
                        $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
                        return redirect()->route('course-details',['slug'=>$slug]);
                    }
                    else{
                        $successMsg = "Thank You. Your Transaction Amount was deposited successfully at faysal bank.";
                        $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
                        return redirect()->route('student.pending-payments.index');
                    }
                }
            else{
                    $successMsg = "Thank You. Your Transaction Amount was deposited successfully at faysal bank.";
                    $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
                    return redirect()->route('student.pending-payments.index');
            }
              
        }
        else{
            if($location == 1){
                    $slug = null;
                    $getCart = CartManagement::where('id',$cart_id)->first();
                    if($getCart){
                        $course_id = $getCart->course_id;
                    }
                    else{
                        $course_id = null;
                    }
                    if($course_id){
                        $getCourse = Course::where('id',$course_id)->first();
                        if($getCourse){
                            $slug = $getCourse->slug;
                        }
                        else{
                            $slug = null;
                        }
                    }
                    if($slug){
                        $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                        return redirect()->route('course-details',['slug'=>$slug]);
                    }
                    else{
                        $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                        return redirect()->route('student.pending-payments.index');
                    }
            }
            else{
                    $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                    return redirect()->route('student.pending-payments.index');
            }
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
        //                      'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else if($adminEmail){
        //      Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$adminEmail) {
        //         $message->to($toEmail)
        //                 ->cc([
                         
        //                     $adminEmail,
        //                      'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else if($FinancialEmail){
        //      Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$FinancialEmail) {
        //         $message->to($toEmail)
        //                 ->cc([
                         
        //                     $FinancialEmail,
        //                      'quranedu.online@cef.org.pk',
        //                 ])
        //                 ->subject('Payment Received | CEF Online');
        //     });
        // }
        // else{
        //     Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail) {
        //     $message->to($toEmail)
        //       ->cc([
                         
                      
        //                      'quranedu.online@cef.org.pk',
        //                 ])
        //             ->subject('Payment Received | CEF Online');
        //     });
        // }
        
        return true;
    }

    public function testFunction(){
        $email_sent = $this->sendPaymentEmail(145);
        // $target_url = route('student_payment.completed');
        //     $text = __("Student payment has been accepted by meezan bank.");
        //     $this->send($text, 1, $target_url, null);
        // return $email_sent;
        dd($email_sent);
        // $data = array('name'=>'CEF Online Shah' , 'message_text'=>"HELLOOOOOOOOOOOOOOOOOOOOOOOOOOOOO");
        // return view('frontend.student.cart.cart-list', $data);
            // Mail::send('frontend.student.emails.completed_payment', $data2, function($message)  {
            //     $message->to('alihassan.750hz@gmail.com', 'CEF Online Shah')
            //     ->subject('Payment Received');
            // });    
            dd("SUCCESS EXIT!!");
    }
  

    private function addAffiliateHistory($cart, $order, $order_item){
        if(get_option('referral_status')){
            $refRequest = AffiliateRequest::where(['affiliate_code' => $cart->reference,'status' => STATUS_APPROVED])->first();
            if(!is_null($refRequest)) {
                $refUser = User::where(['id' => $refRequest->user_id])->first();
                $alreadyAffiliated = AffiliateHistory::where(['user_id'=>$refUser->id,'course_id' =>$cart->course_id])->first();
                if($refUser->is_affiliator == AFFILIATOR && is_null($alreadyAffiliated)) {
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
}
