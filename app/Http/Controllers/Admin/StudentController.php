<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Enrollment;
use App\Models\Instructor;
use App\Models\AffiliateHistory;
use App\Models\Order;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\Course;
use App\Models\Order_item;
use App\Models\Currency;
use App\Models\Order_billing_address;
use App\Models\CartManagement;
use Illuminate\Support\Facades\Mail;
use App\Models\AffiliateRequest;
use App\Models\State;

use App\Models\ClassDetail;
use App\Models\UserPackage;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;
use App\Traits\SendNotification;
use App\Models\TrialClass;
use Illuminate\Support\Facades\DB;
use App\Models\Student;

use App\Models\User;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
 
    use ImageSaveTrait, General, SendNotification;

    protected $studentModel;
    public function __construct( Student $student , TrialClass $free_class , CartManagement $cart_management , Order_item $order_item)
    {
        $this->studentModel = new Crud($student);
        $this->freeClassModel = new Crud($free_class);
        $this->cartManagementModel = new Crud($cart_management);
        $this->orderItemModel = new Crud($order_item);
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


    public function index(Request $request)
    {
        // $data['students'] = $this->studentModel->getOrderById('DESC', 25);
        $data['title'] = 'All Student';

        $query = Student::query(); // Assuming you have these relationships

        $data['students'] = $query->orderBy('id', 'DESC')->paginate(25);
        // Handle search
      
    if ($request->has('search')) {
        $searchTerm = $request->search;
     
        $query->where(function($q) use ($searchTerm) {
            $q->where('first_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('phone_number', 'LIKE', "%{$searchTerm}%")
            ->orwhere('address', 'LIKE', "%{$searchTerm}%")
            ->orWhereHas('user', function($subQuery) use ($searchTerm) {
                $subQuery->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('phone_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('mobile_number', 'LIKE', "%{$searchTerm}%");
            })
            ->orWhereHas('country', function($subQuery) use ($searchTerm) {
                $subQuery->where('country_name', 'LIKE', "%{$searchTerm}%");
            });  
        });
    }
        $data['students'] = $query->orderBy('id', 'DESC')->paginate(25);
      
     
        if ($request->ajax()) {
            return response()->json([
                'students' => view('admin.student.table-data', $data)->render(),
                'pagination' => $data['students']->links()->toHtml()
            ]);
        }

        return view('admin.student.list', $data);
    }
    
    public function getData(Request $request)
    {
        $start = (int) ($request->start ?? 0);  // Default to 0 if not provided
        $length = (int) ($request->length ?? 2);  // Default to 10 rows per page if not provided
        // $length = 2;
        $query = Student::query();
        // Search functionality
     
        if (!empty($request->search)) {
        
            $searchValue = $request->search;
            $query->where('first_name', 'like', '%' . $searchValue . '%')
                  ->orWhere('last_name', 'like', '%' . $searchValue . '%')
                  ->orWhere('phone_number', 'LIKE', '%' . $searchValue . '%')
                  ->orwhere('address', 'LIKE', '%' . $searchValue . '%')
                  ->orWhereHas('user', function($subQuery) use ($searchValue) {
                    $subQuery->where('name', '%' . $searchValue . '%')
                            ->orWhere('email', '%' . $searchValue . '%')
                            ->orWhere('phone_number', '%' . $searchValue . '%')
                            ->orWhere('mobile_number', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('country', function($subQuery) use ($searchValue) {
                    $subQuery->where('country_name', '%' . $searchValue . '%');
                    });  ;
        }

        $data = $query->with('user')->orderBy('id','DESC')->skip($start)->take($length)->get();

    
        $data = $data->map(function ($student) {
        
            return [
                'image_path' => getImageFile($student->user ? $student->user->image_path : ''),
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->user ? $student->user->email : null,
                'phone_number' => $student->phone_number ?? ($student->user ? $student->user->phone_number : null),
                'details_link' => route('student.view', ['uuid' => $student->uuid]),
                'uuid' => $student->uuid,
                'country' => $student->country ? $student->country->country_name : '',
            ];
        });
     
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => Student::count(),
            'recordsFiltered' => $data->count(),
            'data' => $data,
        ]);

     
    }

    public function pendingPayments(Request $request)
    {
        $data['title'] = 'Pending Payments';

        $query = CartManagement::query(); // Assuming you have these relationships
        $data['pendingPayments'] = $query->where('created_by_type',2)
        // ->where('is_monthly_cron',0)
        ->orderBy('id', 'DESC')->paginate(25);

        if ($request->has('search')) {
            $searchTerm = $request->search;
         
            $query->where(function($q) use ($searchTerm) {
                $q->orWhereHas('user', function($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('course', function($subQuery) use ($searchTerm) {
                    $subQuery->where('title', 'LIKE', "%{$searchTerm}%");
                })
                ->orwhere('price', 'LIKE', "%{$searchTerm}%")
                ->orWhere('month', 'LIKE', "%{$searchTerm}%")
                ->orWhere('year', 'LIKE', "%{$searchTerm}%");  
            });
        }
         $data['pendingPayments'] = $query->where('created_by_type',2)->orderBy('id', 'DESC')->paginate(25);
        if ($request->ajax()) {
            return response()->json([
                'pendingPayments' => view('admin.student_payment.table-data', $data)->render(),
                'pagination' => $data['pendingPayments']->links()->toHtml()
            ]);
        }
        // $data['pendingPayments'] = CartManagement::where('created_by_type',2)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.student_payment.pending', $data);
    }



    public function overduePayments(Request $request)
    {
        $data['title'] = 'Pending/Overdue Payments';

        $now = now();

        // Base query (you can add ->with(['user','course','student']) if relationships exist)
        $query = CartManagement::with('user')
            ->where('is_donation', 0)
            ->where('request_enrollment', 1)
            ->orderBy('id', 'DESC');

        // Optional: search on student name or user's email (simple example)
        if ($request->filled('search')) {
            $term = $request->search;
            $query->whereHas('user', function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%");
            });
            // Add more whereHas conditions if you want to search course title, etc.
        }

        $overdues = $query->paginate(15)->appends($request->except('page'));

        // Map/transform items into the shape your view expects.
        // Note: using getCollection()->map then setCollection preserves pagination metadata.
        $mapped = $overdues->getCollection()->map(function ($row) use ($now) {
            // avoid extra queries if possible: if you have relations, eager load them
            $getStudent = \App\Models\Student::where('user_id', $row->user_id)->first();
            $getCourse  = \App\Models\Course::find($row->course_id);

            $studentName = $getStudent ? trim(($getStudent->first_name ?? '') . ' ' . ($getStudent->last_name ?? '')) : ($row->user->name ?? '—');
            $studentUuid = $getStudent ? ($getStudent->uuid ?? null) : null;
            $courseName  = $getCourse ? ($getCourse->title ?? '—') : '—';
            $courseId    = $getCourse ? $getCourse->id : null;

            $daysOverdue = $now->diffInDays($row->created_at);

            return [
                'id'              => $row->id,
                'user_id'         => $row->user_id,
                'student_uuid'    => $studentUuid,
                'student_name'    => $studentName ?: '—',
                'course_id'       => $courseId,
                'course_name'     => $courseName,
                'email'           => $row->user->email ?? null,
                'phone'           => $row->user->mobile_number ?? null,
                'issue_date'      => $row->created_at ? $row->created_at->toDateString() : null,
                'days_overdue'    => $daysOverdue,
                'amount'          => (float) $row->main_price,
                'raw'             => $row,
            ];
        });

        // Replace paginator's collection with mapped rows
        $overdues->setCollection($mapped);

      

        $data['overdues'] = $overdues;
        $data['overdue_rows'] = $mapped;

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.student_payment._overdue_table', $data)->render(),
                'pagination' => $overdues->links()->toHtml()
            ]);
        }

        return view('admin.student_payment.overdue_index', $data);
    }





     public function donationPayments(Request $request)
    {
        $data['title'] = 'Donation Payments';

        $query = CartManagement::query(); // Assuming you have these relationships
        $data['donationPayments'] = $query->where('is_donation',1)->orderBy('id', 'DESC')->paginate(25);
        if ($request->has('search')) {
            $searchTerm = $request->search;
         
            $query->where(function($q) use ($searchTerm) {
                $q->orWhereHas('user', function($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('course', function($subQuery) use ($searchTerm) {
                    $subQuery->where('title', 'LIKE', "%{$searchTerm}%");
                })
                ->orwhere('price', 'LIKE', "%{$searchTerm}%")
                ->orWhere('month', 'LIKE', "%{$searchTerm}%")
                ->orWhere('year', 'LIKE', "%{$searchTerm}%");  
            });
        }
         $data['donationPayments'] = $query->where('is_donation',1)->orderBy('id', 'DESC')->paginate(25);
        if ($request->ajax()) {
            return response()->json([
                'donationPayments' => view('admin.student_payment.table-data-donation', $data)->render(),
                'pagination' => $data['donationPayments']->links()->toHtml()
            ]);
        }
        // $data['donationPayments'] = CartManagement::where('created_by_type',2)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.student_payment.donation', $data);
    }



       public function changePaymentStatusOverDue($payment_id)
    {
        $data['title'] = 'Change Payment Status';
        $data['paymentid'] = $payment_id;
        $data['getpayment'] = CartManagement::where('id',$payment_id)->orderBy('id', 'DESC')->first();
    
        // $data['pendingPayments'] = CartManagement::where('created_by_type',2)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.student_payment.change_payment_status_overdue', $data);
    }




    public function updatePaymentStatusOverDue(Request $request)
{
    $request->validate([
        'transaction_id' => 'required',
    ]);
    DB::beginTransaction();
    try {
        $payment_id = (int)$request->paymentid;
        $transaction_id = $request->transaction_id;
        $getPaymentData = CartManagement::where('id', $payment_id)->first();
        if ($getPaymentData == null) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Payment ID Does Not Exists.'));
            return redirect()->back();
        }

        // Ensure transaction_id is not already used by another order (excluding this cart)
        $existingOrderWithSameTransaction = Order::where(function ($query) use ($transaction_id) {
                $query->where('faysal_transaction_id', $transaction_id)
                      ->orWhere('meezan_transaction_id', $transaction_id)
                      ->orWhere('payment_id', $transaction_id);
            })
            ->where('cart_id', '!=', $payment_id) // Exclude current cart
            ->first();

        if ($existingOrderWithSameTransaction) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Transaction ID is not Unique. Already Exists with another payment.'));
            return redirect()->back();
        }

        $unpaidAmount = $getPaymentData->price;
        $subTotal = $getPaymentData->price;
        $month = $getPaymentData->month;
        $grandTotal = $subTotal;
        $year = $getPaymentData->year;
        $student_user_id = $getPaymentData->user?->id;
        $carts = CartManagement::where('id', $payment_id)->get();

        // Try to find an existing order for this cart that already references this transaction
        $existingOrder = Order::where('cart_id', $payment_id)
            ->where(function ($q) use ($transaction_id) {
                $q->where('meezan_transaction_id', $transaction_id)
                  ->orWhere('faysal_transaction_id', $transaction_id)
                  ->orWhere('payment_id', $transaction_id);
            })->first();

        if ($existingOrder) {
            // --- Update existing order (your existing logic) ---
            if ($existingOrder->meezan_transaction_id) {
                $existingOrder->payment_id = $existingOrder->meezan_transaction_id;
                if ($existingOrder->meezan_payment_date == null) {
                    $existingOrder->meezan_payment_date = date('Y-m-d H:i:s');
                }
            } else if ($existingOrder->faysal_transaction_id) {
                $existingOrder->payment_id = $existingOrder->faysal_transaction_id;
                if ($existingOrder->faysal_payment_date == null) {
                    $existingOrder->faysal_payment_date = date('Y-m-d H:i:s');
                }
            }

            $existingOrder->payment_status = 'paid';
            $existingOrder->delivery_status = 1;
            $existingOrder->user_id = $student_user_id;
            $existingOrder->updated_at = now();
            $existingOrder->payment_currency = 'PKR';
            $existingOrder->save();

            DB::commit();

            $target_url = route('student_payment.completed');
            $text = __("Student payment has been accepted and paid manually.");
            $this->send($text, 1, $target_url, null);
            $email_sent = $this->sendPaymentEmail($existingOrder->id);
            $this->showToastrMessage('success', __('Payment status updated successfully for existing transaction.'));
            $getCartData = CartManagement::where('id', $payment_id)->first();
            if ($getCartData) {
                $getCartData->delete();
            }

            return redirect()->route('student_payment.completed');
        }

        // --- NO existing Order for this cart & transaction: create a NEW order ---
        // At this point we've already checked transaction uniqueness across other orders.
        $order = new Order;
        $successMsg = "Transaction Amount was deposited successfully manually.";
        $order->user_id = $student_user_id;
        $order->order_number = rand(100000, 999999);
        $order->sub_total = $subTotal;
        $order->grand_total = $grandTotal;
        $order->current_currency = "PKR";
        $order->payment_currency = "PKR";
        $order->payment_method = "Manual";
        $order->payment_id = $transaction_id;
        $order->cart_id = $payment_id; // <-- important: link order to cart

        if ($payment_id != null) {
            $order->created_by_type = 2;
            $order->cart_type = 2;
        } else {
            $order->cart_type = 1;
            $order->created_by_type = 1;
        }
        $order->month = $month;
        $order->year = $year;
        $order->payment_status = 'paid';
        $order->delivery_status = 1;
        $payment_currency = get_option('faysal_currency');
        $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;

        $order->conversion_rate = $conversion_rate;
        if ($conversion_rate) {
            $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
        }
        $order->save();

        // Create order items / enrollments (your existing logic)
        foreach ($carts as $cart) {
            if ($cart->course_id) {
                $courseExits = Course::find($cart->course_id);
                $order_item = new Order_item();
                $order_item->order_id = $order->id;
                $order_item->user_id = $student_user_id;
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
                $userPackage = UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')
                    ->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])
                    ->where('user_packages.user_id', $order_item->owner_user_id)
                    ->where('user_packages.status', PACKAGE_STATUS_ACTIVE)
                    ->where('enroll_date', '<=', now())
                    ->where('expired_date', '>=', now())
                    ->select('user_packages.*')->first();
                $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                if ($adminCommission) {
                    $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                    $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                    $order_item->sell_commission = $adminCommission;
                } else {
                    $order_item->owner_balance = $cart->price;
                }

                $order_item->save();

                if ($order->created_by_type != 2) {
                    // $enrollment = new Enrollment();
                    // $enrollment->order_id = $order->id;
                    // $enrollment->user_id = $student_user_id;
                    // $enrollment->course_id = $cart->course_id;

                    // $enrollment->main_price = $order_item->unit_price;
                    // $enrollment->price = $order_item->unit_price;
                    // $enrollment->original_price = $order_item->original_price;
                    // $enrollment->discount = $order_item->discount;
                    // $enrollment->admission_fee = $order_item->admission_fee;
                    // $enrollment->discount_type = $order_item->discount_type;
                    // $enrollment->net_original_price = $order_item->net_original_price;
                    // $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                    // $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                    // $enrollment->net_admission_fee = $order_item->net_admission_fee;
                    // $enrollment->request_enrollment = $order_item->request_enrollment;

                    // $enrollment->owner_user_id = $courseExits->user_id ?? null;
                    // $enrollment->start_date = now();
                    // $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                    // $enrollment->save();
                }

                $this->addAffiliateHistory($cart, $order, $order_item);
            }
        }

        DB::commit();
        $target_url = route('student_payment.completed');
        $text = __("Student payment has been accepted and paid manually.");
        $this->send($text, 1, $target_url, null);

        $email_sent = $this->sendPaymentEmail($order->id);
        $this->showToastrMessage('success', "<strong>" . $successMsg . "</strong><br/>");

        $getCartData = CartManagement::where('id', $payment_id)->first();
        if ($getCartData) {
            $getCartData->delete();
        }

        return redirect()->route('student_payment.completed');
    } catch (\Throwable $th) {
        DB::rollBack();
        // better to log the error instead of dd in production
        \Log::error($th);
        $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
        return redirect()->back();
    }
}



      public function changePaymentStatusDonation($payment_id)
    {
        $data['title'] = 'Change Payment Status';
        $data['paymentid'] = $payment_id;
        $data['getpayment'] = CartManagement::where('id',$payment_id)->orderBy('id', 'DESC')->first();
    
        // $data['pendingPayments'] = CartManagement::where('created_by_type',2)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.student_payment.change_payment_status_donation', $data);
    }




    public function updatePaymentStatusDonation(Request $request)
{
    $request->validate([
        'transaction_id' => 'required',
    ]);
    DB::beginTransaction();
    try {
        $payment_id = (int)$request->paymentid;
        $transaction_id = $request->transaction_id;
        $getPaymentData = CartManagement::where('id', $payment_id)->first();
        if ($getPaymentData == null) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Payment ID Does Not Exists.'));
            return redirect()->back();
        }

        // Ensure transaction_id is not already used by another order (excluding this cart)
        $existingOrderWithSameTransaction = Order::where(function ($query) use ($transaction_id) {
                $query->where('faysal_transaction_id', $transaction_id)
                      ->orWhere('meezan_transaction_id', $transaction_id)
                      ->orWhere('payment_id', $transaction_id);
            })
            ->where('cart_id', '!=', $payment_id) // Exclude current cart
            ->first();

        if ($existingOrderWithSameTransaction) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Transaction ID is not Unique. Already Exists with another payment.'));
            return redirect()->back();
        }

        $unpaidAmount = $getPaymentData->price;
        $subTotal = $getPaymentData->price;
        $month = $getPaymentData->month;
        $grandTotal = $subTotal;
        $year = $getPaymentData->year;
        $student_user_id = $getPaymentData->user?->id;
        $carts = CartManagement::where('id', $payment_id)->get();

        // Try to find an existing order for this cart that already references this transaction
        $existingOrder = Order::where('cart_id', $payment_id)
            ->where(function ($q) use ($transaction_id) {
                $q->where('meezan_transaction_id', $transaction_id)
                  ->orWhere('faysal_transaction_id', $transaction_id)
                  ->orWhere('payment_id', $transaction_id);
            })->first();

        if ($existingOrder) {
            // --- Update existing order (your existing logic) ---
            if ($existingOrder->meezan_transaction_id) {
                $existingOrder->payment_id = $existingOrder->meezan_transaction_id;
                if ($existingOrder->meezan_payment_date == null) {
                    $existingOrder->meezan_payment_date = date('Y-m-d H:i:s');
                }
            } else if ($existingOrder->faysal_transaction_id) {
                $existingOrder->payment_id = $existingOrder->faysal_transaction_id;
                if ($existingOrder->faysal_payment_date == null) {
                    $existingOrder->faysal_payment_date = date('Y-m-d H:i:s');
                }
            }

            $existingOrder->payment_status = 'paid';
            $existingOrder->delivery_status = 1;
            $existingOrder->user_id = $student_user_id;
            $existingOrder->updated_at = now();
            $existingOrder->payment_currency = 'PKR';
            $existingOrder->save();

            DB::commit();

            $target_url = route('student_payment.completed_donation');
            $text = __("Student payment has been accepted and paid manually.");
            $this->send($text, 1, $target_url, null);
            $email_sent = $this->sendPaymentEmail($existingOrder->id);
            $this->showToastrMessage('success', __('Payment status updated successfully for existing transaction.'));
            $getCartData = CartManagement::where('id', $payment_id)->first();
            if ($getCartData) {
                $getCartData->delete();
            }

            return redirect()->route('student_payment.completed_donation');
        }

        // --- NO existing Order for this cart & transaction: create a NEW order ---
        // At this point we've already checked transaction uniqueness across other orders.
        $order = new Order;
        $successMsg = "Transaction Amount was deposited successfully manually.";
        $order->user_id = $student_user_id;
        $order->order_number = rand(100000, 999999);
        $order->sub_total = $subTotal;
        $order->grand_total = $grandTotal;
        $order->current_currency = "PKR";
        $order->payment_currency = "PKR";
        $order->payment_method = "Manual";
        $order->payment_id = $transaction_id;
        $order->cart_id = $payment_id; // <-- important: link order to cart

        if ($payment_id != null) {
            $order->created_by_type = 2;
            $order->cart_type = 2;
        } else {
            $order->cart_type = 1;
            $order->created_by_type = 1;
        }
        $order->month = $month;
        $order->year = $year;
        $order->payment_status = 'paid';
        $order->delivery_status = 1;
        $payment_currency = get_option('faysal_currency');
        $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;

        $order->conversion_rate = $conversion_rate;
        if ($conversion_rate) {
            $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
        }
        $order->save();

        // Create order items / enrollments (your existing logic)
        foreach ($carts as $cart) {
            if ($cart->course_id) {
                $courseExits = Course::find($cart->course_id);
                $order_item = new Order_item();
                $order_item->order_id = $order->id;
                $order_item->user_id = $student_user_id;
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
                $userPackage = UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')
                    ->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])
                    ->where('user_packages.user_id', $order_item->owner_user_id)
                    ->where('user_packages.status', PACKAGE_STATUS_ACTIVE)
                    ->where('enroll_date', '<=', now())
                    ->where('expired_date', '>=', now())
                    ->select('user_packages.*')->first();
                $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                if ($adminCommission) {
                    $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                    $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                    $order_item->sell_commission = $adminCommission;
                } else {
                    $order_item->owner_balance = $cart->price;
                }

                $order_item->save();

                if ($order->created_by_type != 2) {
                    // $enrollment = new Enrollment();
                    // $enrollment->order_id = $order->id;
                    // $enrollment->user_id = $student_user_id;
                    // $enrollment->course_id = $cart->course_id;

                    // $enrollment->main_price = $order_item->unit_price;
                    // $enrollment->price = $order_item->unit_price;
                    // $enrollment->original_price = $order_item->original_price;
                    // $enrollment->discount = $order_item->discount;
                    // $enrollment->admission_fee = $order_item->admission_fee;
                    // $enrollment->discount_type = $order_item->discount_type;
                    // $enrollment->net_original_price = $order_item->net_original_price;
                    // $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                    // $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                    // $enrollment->net_admission_fee = $order_item->net_admission_fee;
                    // $enrollment->request_enrollment = $order_item->request_enrollment;

                    // $enrollment->owner_user_id = $courseExits->user_id ?? null;
                    // $enrollment->start_date = now();
                    // $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                    // $enrollment->save();
                }

                $this->addAffiliateHistory($cart, $order, $order_item);
            }
        }

        DB::commit();
        $target_url = route('student_payment.completed_donation');
        $text = __("Student payment has been accepted and paid manually.");
        $this->send($text, 1, $target_url, null);

        $email_sent = $this->sendPaymentEmail($order->id);
        $this->showToastrMessage('success', "<strong>" . $successMsg . "</strong><br/>");

        $getCartData = CartManagement::where('id', $payment_id)->first();
        if ($getCartData) {
            $getCartData->delete();
        }

        return redirect()->route('student_payment.completed_donation');
    } catch (\Throwable $th) {
        DB::rollBack();
        // better to log the error instead of dd in production
        \Log::error($th);
        $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
        return redirect()->back();
    }
}




//         public function updatePaymentStatusOverdue(Request $request)
// {
//     $request->validate([
//         'transaction_id' => 'required', 
//     ]);
//     DB::beginTransaction();
//     try {
//         $payment_id = (int)$request->paymentid;
//         $transaction_id = $request->transaction_id;
//         $getPaymentData = CartManagement::where('id',$payment_id)->first();
//         if( $getPaymentData == null){
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Payment ID Does Not Exists.'));
//             return redirect()->back();
//         }
       
//             // Check if this transaction_id already exists in orders table with a different payment_id
//         $existingOrderWithSameTransaction = Order::where(function($query) use ($transaction_id) {
//                 $query->where('faysal_transaction_id', $transaction_id)
//                     ->orWhere('meezan_transaction_id', $transaction_id)
//                     ->orWhere('payment_id', $transaction_id);
//             })
//         ->where('cart_id', '!=', $payment_id) // Exclude current payment_id if it exists
//         ->first();

//         if ($existingOrderWithSameTransaction) {
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Transaction ID is not Unique. Already Exists with another payment.'));
//             return redirect()->back();
//         }

      
     


//         $unpaidAmount = $getPaymentData->price;
//         $subTotal = $getPaymentData->price;
//         $month = $getPaymentData->month;
//         $grandTotal = $subTotal;
//         $year = $getPaymentData->year;
//         $student_user_id = $getPaymentData->user?->id;
//         $carts = CartManagement::where('id',$payment_id)->get();
    

//         // $checkExistingOrder = Order::where('payment_id', $transaction_id)->first();
//         // if($checkExistingOrder == null){
//         //     DB::rollBack();
//         //     $this->showToastrMessage('warning', __('Given Transaction ID is Invalid. Does not Exists in Records'));
//         //     return redirect()->back();
//         // }

//         $checkExistingOrder = Order::where('cart_id', $payment_id)
//             ->where(function($q) use ($transaction_id) {
//                 $q->where('meezan_transaction_id', $transaction_id)
//                   ->orWhere('faysal_transaction_id', $transaction_id)
//                   ->orWhere('payment_id', $transaction_id);
//             })
//             ->first();
      
//         if (!$checkExistingOrder) {
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Transaction ID is Invalid or does not exist for this cart.'));
//             return redirect()->back();
//         }


           
      

//         // Check if there's an order with both this payment_id and transaction_id (update case)
//         // $existingOrder = Order::where('payment_id', $transaction_id)
//         //     ->where('cart_id',$payment_id)
//         //     ->first();

            
//         $existingOrder = Order::where('cart_id', $payment_id)
//             ->where(function($q) use ($transaction_id) {
//                 $q->where('meezan_transaction_id', $transaction_id)
//                   ->orWhere('faysal_transaction_id', $transaction_id)
//                   ->orWhere('payment_id', $transaction_id);
//             })
//             ->first();

//         if ($existingOrder) {
//             if($existingOrder->meezan_transaction_id){
//                     $existingOrder->payment_id = $existingOrder->meezan_transaction_id;
//                         if($existingOrder->meezan_payment_date == null){
//                             $existingOrder->meezan_payment_date = date('Y-m-d h:i:s');
//                         }
//               }
//               else if($existingOrder->faysal_transaction_id){
//                     $existingOrder->payment_id = $existingOrder->faysal_transaction_id;
//                     if($existingOrder->faysal_payment_date == null){
//                             $existingOrder->faysal_payment_date = date('Y-m-d h:i:s');
//                     }
//               }

          

//               /////////////////////////////////
//             // Update existing order
//             $existingOrder->payment_status = 'paid';
//              $existingOrder->delivery_status = 1;
//              $existingOrder->user_id = $student_user_id;
//              $existingOrder->updated_at = now();
//              $existingOrder->payment_currency = 'PKR';
//             $existingOrder->save();
        
//             DB::commit();
             
//             $target_url = route('student_payment.completed');
//             $text = __("Student payment has been accepted and paid manually.");
//             $this->send($text, 1, $target_url, null);
//             $email_sent = $this->sendPaymentEmail($existingOrder->id);
//             $this->showToastrMessage('success', __('Payment status updated successfully for existing transaction.'));
//             $getCartData = CartManagement::where('id',$payment_id)->first();
//             if($getCartData){
//                 $getCartData->delete();
//             }
        
        
//             return redirect()->route('student_payment.completed');
//         }
//         else{
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Transaction ID is Not Unique. Already Exists For Another Payment'));
//             return redirect()->back();
//         }






//         //////////////////////////

      
        
//         $order = new Order;
//         $successMsg = "Transaction Amount was deposited successfully manually.";
//         $order->user_id = $student_user_id;
//         $order->order_number = rand(100000,999999);
//         $order->sub_total = $subTotal;
//         $order->grand_total = $grandTotal;
//         $order->current_currency = "PKR";
//         $order->payment_currency = "PKR";
//         $order->payment_method = "Manual";
//         $order->payment_id = $transaction_id;
      
//         if($payment_id != null){
//             $order->created_by_type = 2;   
//             $order->cart_type = 2;
//         }
//         else{
//             $order->cart_type = 1;
//             $order->created_by_type = 1;   
//         }
//         $order->month = $month;
//         $order->year= $year;
//         $order->payment_status = 'paid';
//         $order->delivery_status = 1;
//         $payment_currency = get_option('faysal_currency');
//         $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;

//         $order->conversion_rate = $conversion_rate;
//         if ($conversion_rate) {
//             $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
//         }
//         $order->save();
    
//         foreach ($carts as $cart) {
           
//             if ($cart->course_id) {
              
//                 if ($cart->course_id) {
//                     $courseExits = Course::find($cart->course_id);
//                     $order_item = new Order_item();
//                     $order_item->order_id = $order->id;
//                     $order_item->user_id = $student_user_id;
//                     $order_item->product_id = $cart->product_id;
//                     $order_item->course_id = $cart->course_id;

                
//                     $order_item->original_price = $cart->original_price;
//                     $order_item->discount = $cart->discount;
//                     $order_item->admission_fee = $cart->admission_fee;
//                     $order_item->discount_type = $cart->discount_type;
//                     $order_item->net_original_price = $cart->net_original_price;
//                     $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
//                     $order_item->discount_admission_fee = $cart->discount_admission_fee;
//                     $order_item->net_admission_fee = $cart->net_admission_fee;

//                     $order_item->is_monthly_cron = $cart->is_monthly_cron;
//                     $order_item->is_donation = $cart->is_donation;
//                     $order_item->students_count = $cart->students_count;
//                     $order_item->cart_type = $cart->created_by_type;
//                     $order_item->created_by_type = $cart->created_by_type;  
//                     $order_item->month = $cart->month;
//                     $order_item->year = $cart->year;   
//                     $order_item->cart_id = $cart->id;


//                     $order_item->type = 1;
//                     $order_item->receiver_info = $cart->receiver_info;
//                     $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;
//                     $order_item->unit_price = $cart->price;
//                     $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
//                     $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
//                     if ($adminCommission) {
//                         $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
//                         $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
//                         $order_item->sell_commission = $adminCommission;
//                     } else {
//                         $order_item->owner_balance = $cart->price;
//                     }
                
//                     $order_item->save();

//                     if($order->created_by_type != 2){
//                         $enrollment = new Enrollment();
//                         $enrollment->order_id = $order->id;
//                         $enrollment->user_id = $student_user_id;
//                         $enrollment->course_id = $cart->course_id;

//                         $enrollment->main_price = $order_item->unit_price;
//                         $enrollment->price = $order_item->unit_price;
//                         $enrollment->original_price = $order_item->original_price;
//                         $enrollment->discount = $order_item->discount;
//                         $enrollment->admission_fee = $order_item->admission_fee;
//                         $enrollment->discount_type = $order_item->discount_type;
//                         $enrollment->net_original_price = $order_item->net_original_price;
//                         $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
//                         $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
//                         $enrollment->net_admission_fee = $order_item->net_admission_fee;
//                         $enrollment->request_enrollment = $order_item->request_enrollment;

//                         $enrollment->owner_user_id = $courseExits->user_id ?? null;
//                         $enrollment->start_date = now();
//                         $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
//                         $enrollment->save();
//                     }

//                     $this->addAffiliateHistory($cart,$order,$order_item);
//                 }
//             }
//         }

//         DB::commit();
//         $target_url = route('student_payment.completed');
//         $text = __("Student payment has been accepted and paid manually.");
//         $this->send($text, 1, $target_url, null);

//         $email_sent = $this->sendPaymentEmail($order->id);
//         $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
      
//         $getCartData = CartManagement::where('id',$payment_id)->first();
//         if($getCartData){
//             $getCartData->delete();
//         }

       
//         return redirect()->route('student_payment.completed');

     
      

      
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         dd($th);
//         $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
//         return redirect()->back();
//     }
// }







    public function changePaymentStatus($payment_id)
    {
        $data['title'] = 'Change Payment Status';
        $data['paymentid'] = $payment_id;
        $data['getpayment'] = CartManagement::where('id',$payment_id)->where('created_by_type',2)->orderBy('id', 'DESC')->first();
    
        // $data['pendingPayments'] = CartManagement::where('created_by_type',2)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.student_payment.change_payment_status', $data);
    }




public function updatePaymentStatus(Request $request)
{
    $request->validate([
        'transaction_id' => 'required',
    ]);
    DB::beginTransaction();
    try {
        $payment_id = (int)$request->paymentid;
        $transaction_id = $request->transaction_id;
        $getPaymentData = CartManagement::where('id', $payment_id)->first();
        if ($getPaymentData == null) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Payment ID Does Not Exists.'));
            return redirect()->back();
        }

        // Ensure transaction_id is not already used by another order (excluding this cart)
        $existingOrderWithSameTransaction = Order::where(function ($query) use ($transaction_id) {
                $query->where('faysal_transaction_id', $transaction_id)
                      ->orWhere('meezan_transaction_id', $transaction_id)
                      ->orWhere('payment_id', $transaction_id);
            })
            ->where('cart_id', '!=', $payment_id) // Exclude current cart
            ->first();

        if ($existingOrderWithSameTransaction) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Transaction ID is not Unique. Already Exists with another payment.'));
            return redirect()->back();
        }

        $unpaidAmount = $getPaymentData->price;
        $subTotal = $getPaymentData->price;
        $month = $getPaymentData->month;
        $grandTotal = $subTotal;
        $year = $getPaymentData->year;
        $student_user_id = $getPaymentData->user?->id;
        $carts = CartManagement::where('id', $payment_id)->get();

        // Try to find an existing order for this cart that already references this transaction
        $existingOrder = Order::where('cart_id', $payment_id)
            ->where(function ($q) use ($transaction_id) {
                $q->where('meezan_transaction_id', $transaction_id)
                  ->orWhere('faysal_transaction_id', $transaction_id)
                  ->orWhere('payment_id', $transaction_id);
            })->first();

        if ($existingOrder) {
            // --- Update existing order (your existing logic) ---
            if ($existingOrder->meezan_transaction_id) {
                $existingOrder->payment_id = $existingOrder->meezan_transaction_id;
                if ($existingOrder->meezan_payment_date == null) {
                    $existingOrder->meezan_payment_date = date('Y-m-d H:i:s');
                }
            } else if ($existingOrder->faysal_transaction_id) {
                $existingOrder->payment_id = $existingOrder->faysal_transaction_id;
                if ($existingOrder->faysal_payment_date == null) {
                    $existingOrder->faysal_payment_date = date('Y-m-d H:i:s');
                }
            }

            $existingOrder->payment_status = 'paid';
            $existingOrder->delivery_status = 1;
            $existingOrder->user_id = $student_user_id;
            $existingOrder->updated_at = now();
            $existingOrder->payment_currency = 'PKR';
            $existingOrder->save();

            DB::commit();

            $target_url = route('student_payment.completed');
            $text = __("Student payment has been accepted and paid manually.");
            $this->send($text, 1, $target_url, null);
            $email_sent = $this->sendPaymentEmail($existingOrder->id);
            $this->showToastrMessage('success', __('Payment status updated successfully for existing transaction.'));
            $getCartData = CartManagement::where('id', $payment_id)->first();
            if ($getCartData) {
                $getCartData->delete();
            }

            return redirect()->route('student_payment.completed');
        }

        // --- NO existing Order for this cart & transaction: create a NEW order ---
        // At this point we've already checked transaction uniqueness across other orders.
        $order = new Order;
        $successMsg = "Transaction Amount was deposited successfully manually.";
        $order->user_id = $student_user_id;
        $order->order_number = rand(100000, 999999);
        $order->sub_total = $subTotal;
        $order->grand_total = $grandTotal;
        $order->current_currency = "PKR";
        $order->payment_currency = "PKR";
        $order->payment_method = "Manual";
        $order->payment_id = $transaction_id;
        $order->cart_id = $payment_id; // <-- important: link order to cart

        if ($payment_id != null) {
            $order->created_by_type = 2;
            $order->cart_type = 2;
        } else {
            $order->cart_type = 1;
            $order->created_by_type = 1;
        }
        $order->month = $month;
        $order->year = $year;
        $order->payment_status = 'paid';
        $order->delivery_status = 1;
        $payment_currency = get_option('faysal_currency');
        $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;

        $order->conversion_rate = $conversion_rate;
        if ($conversion_rate) {
            $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
        }
        $order->save();

        // Create order items / enrollments (your existing logic)
        foreach ($carts as $cart) {
            if ($cart->course_id) {
                $courseExits = Course::find($cart->course_id);
                $order_item = new Order_item();
                $order_item->order_id = $order->id;
                $order_item->user_id = $student_user_id;
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
                $userPackage = UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')
                    ->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])
                    ->where('user_packages.user_id', $order_item->owner_user_id)
                    ->where('user_packages.status', PACKAGE_STATUS_ACTIVE)
                    ->where('enroll_date', '<=', now())
                    ->where('expired_date', '>=', now())
                    ->select('user_packages.*')->first();
                $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                if ($adminCommission) {
                    $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                    $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                    $order_item->sell_commission = $adminCommission;
                } else {
                    $order_item->owner_balance = $cart->price;
                }

                $order_item->save();

                if ($order->created_by_type != 2) {
                    // $enrollment = new Enrollment();
                    // $enrollment->order_id = $order->id;
                    // $enrollment->user_id = $student_user_id;
                    // $enrollment->course_id = $cart->course_id;

                    // $enrollment->main_price = $order_item->unit_price;
                    // $enrollment->price = $order_item->unit_price;
                    // $enrollment->original_price = $order_item->original_price;
                    // $enrollment->discount = $order_item->discount;
                    // $enrollment->admission_fee = $order_item->admission_fee;
                    // $enrollment->discount_type = $order_item->discount_type;
                    // $enrollment->net_original_price = $order_item->net_original_price;
                    // $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                    // $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                    // $enrollment->net_admission_fee = $order_item->net_admission_fee;
                    // $enrollment->request_enrollment = $order_item->request_enrollment;

                    // $enrollment->owner_user_id = $courseExits->user_id ?? null;
                    // $enrollment->start_date = now();
                    // $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                    // $enrollment->save();
                }

                $this->addAffiliateHistory($cart, $order, $order_item);
            }
        }

        DB::commit();
        $target_url = route('student_payment.completed');
        $text = __("Student payment has been accepted and paid manually.");
        $this->send($text, 1, $target_url, null);

        $email_sent = $this->sendPaymentEmail($order->id);
        $this->showToastrMessage('success', "<strong>" . $successMsg . "</strong><br/>");

        $getCartData = CartManagement::where('id', $payment_id)->first();
        if ($getCartData) {
            $getCartData->delete();
        }

        return redirect()->route('student_payment.completed');
    } catch (\Throwable $th) {
        DB::rollBack();
        // better to log the error instead of dd in production
        \Log::error($th);
        $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
        return redirect()->back();
    }
}






//     public function updatePaymentStatus(Request $request)
// {
//     $request->validate([
//         'transaction_id' => 'required', 
//     ]);
//     DB::beginTransaction();
//     try {
//         $payment_id = (int)$request->paymentid;
//         $transaction_id = $request->transaction_id;
//         $getPaymentData = CartManagement::where('id',$payment_id)->first();
//         if( $getPaymentData == null){
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Payment ID Does Not Exists.'));
//             return redirect()->back();
//         }
       
//             // Check if this transaction_id already exists in orders table with a different payment_id
//         $existingOrderWithSameTransaction = Order::where(function($query) use ($transaction_id) {
//                 $query->where('faysal_transaction_id', $transaction_id)
//                     ->orWhere('meezan_transaction_id', $transaction_id)
//                     ->orWhere('payment_id', $transaction_id);
//             })
//         ->where('cart_id', '!=', $payment_id) // Exclude current payment_id if it exists
//         ->first();

//         if ($existingOrderWithSameTransaction) {
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Transaction ID is not Unique. Already Exists with another payment.'));
//             return redirect()->back();
//         }

      
     


//         $unpaidAmount = $getPaymentData->price;
//         $subTotal = $getPaymentData->price;
//         $month = $getPaymentData->month;
//         $grandTotal = $subTotal;
//         $year = $getPaymentData->year;
//         $student_user_id = $getPaymentData->user?->id;
//         $carts = CartManagement::where('id',$payment_id)->get();
    

//         // $checkExistingOrder = Order::where('payment_id', $transaction_id)->first();
//         // if($checkExistingOrder == null){
//         //     DB::rollBack();
//         //     $this->showToastrMessage('warning', __('Given Transaction ID is Invalid. Does not Exists in Records'));
//         //     return redirect()->back();
//         // }

//         $checkExistingOrder = Order::where('cart_id', $payment_id)
//             ->where(function($q) use ($transaction_id) {
//                 $q->where('meezan_transaction_id', $transaction_id)
//                   ->orWhere('faysal_transaction_id', $transaction_id)
//                   ->orWhere('payment_id', $transaction_id);
//             })
//             ->first();
      
//         if (!$checkExistingOrder) {
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Transaction ID is Invalid or does not exist for this cart.'));
//             return redirect()->back();
//         }


           
      

//         // Check if there's an order with both this payment_id and transaction_id (update case)
//         // $existingOrder = Order::where('payment_id', $transaction_id)
//         //     ->where('cart_id',$payment_id)
//         //     ->first();

            
//         $existingOrder = Order::where('cart_id', $payment_id)
//             ->where(function($q) use ($transaction_id) {
//                 $q->where('meezan_transaction_id', $transaction_id)
//                   ->orWhere('faysal_transaction_id', $transaction_id)
//                   ->orWhere('payment_id', $transaction_id);
//             })
//             ->first();

//         if ($existingOrder) {
//             if($existingOrder->meezan_transaction_id){
//                     $existingOrder->payment_id = $existingOrder->meezan_transaction_id;
//                         if($existingOrder->meezan_payment_date == null){
//                             $existingOrder->meezan_payment_date = date('Y-m-d h:i:s');
//                         }
//               }
//               else if($existingOrder->faysal_transaction_id){
//                     $existingOrder->payment_id = $existingOrder->faysal_transaction_id;
//                     if($existingOrder->faysal_payment_date == null){
//                             $existingOrder->faysal_payment_date = date('Y-m-d h:i:s');
//                     }
//               }

          

//               /////////////////////////////////
//             // Update existing order
//             $existingOrder->payment_status = 'paid';
//              $existingOrder->delivery_status = 1;
//              $existingOrder->user_id = $student_user_id;
//              $existingOrder->updated_at = now();
//              $existingOrder->payment_currency = 'PKR';
//             $existingOrder->save();
        
//             DB::commit();
             
//             $target_url = route('student_payment.completed');
//             $text = __("Student payment has been accepted and paid manually.");
//             $this->send($text, 1, $target_url, null);
//             $email_sent = $this->sendPaymentEmail($existingOrder->id);
//             $this->showToastrMessage('success', __('Payment status updated successfully for existing transaction.'));
//             $getCartData = CartManagement::where('id',$payment_id)->first();
//             if($getCartData){
//                 $getCartData->delete();
//             }
        
        
//             return redirect()->route('student_payment.completed');
//         }
//         else{
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Transaction ID is Not Unique. Already Exists For Another Payment'));
//             return redirect()->back();
//         }






//         //////////////////////////

      
        
//         $order = new Order;
//         $successMsg = "Transaction Amount was deposited successfully manually.";
//         $order->user_id = $student_user_id;
//         $order->order_number = rand(100000,999999);
//         $order->sub_total = $subTotal;
//         $order->grand_total = $grandTotal;
//         $order->current_currency = "PKR";
//         $order->payment_currency = "PKR";
//         $order->payment_method = "Manual";
//         $order->payment_id = $transaction_id;
      
//         if($payment_id != null){
//             $order->created_by_type = 2;   
//             $order->cart_type = 2;
//         }
//         else{
//             $order->cart_type = 1;
//             $order->created_by_type = 1;   
//         }
//         $order->month = $month;
//         $order->year= $year;
//         $order->payment_status = 'paid';
//         $order->delivery_status = 1;
//         $payment_currency = get_option('faysal_currency');
//         $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;

//         $order->conversion_rate = $conversion_rate;
//         if ($conversion_rate) {
//             $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
//         }
//         $order->save();
    
//         foreach ($carts as $cart) {
           
//             if ($cart->course_id) {
              
//                 if ($cart->course_id) {
//                     $courseExits = Course::find($cart->course_id);
//                     $order_item = new Order_item();
//                     $order_item->order_id = $order->id;
//                     $order_item->user_id = $student_user_id;
//                     $order_item->product_id = $cart->product_id;
//                     $order_item->course_id = $cart->course_id;

                
//                     $order_item->original_price = $cart->original_price;
//                     $order_item->discount = $cart->discount;
//                     $order_item->admission_fee = $cart->admission_fee;
//                     $order_item->discount_type = $cart->discount_type;
//                     $order_item->net_original_price = $cart->net_original_price;
//                     $order_item->discount_type_admission_fee = $cart->discount_type_admission_fee;
//                     $order_item->discount_admission_fee = $cart->discount_admission_fee;
//                     $order_item->net_admission_fee = $cart->net_admission_fee;

//                     $order_item->is_monthly_cron = $cart->is_monthly_cron;
//                     $order_item->is_donation = $cart->is_donation;
//                     $order_item->students_count = $cart->students_count;
//                     $order_item->cart_type = $cart->created_by_type;
//                     $order_item->created_by_type = $cart->created_by_type;  
//                     $order_item->month = $cart->month;
//                     $order_item->year = $cart->year;   
//                     $order_item->cart_id = $cart->id;


//                     $order_item->type = 1;
//                     $order_item->receiver_info = $cart->receiver_info;
//                     $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;
//                     $order_item->unit_price = $cart->price;
//                     $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
//                     $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
//                     if ($adminCommission) {
//                         $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
//                         $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
//                         $order_item->sell_commission = $adminCommission;
//                     } else {
//                         $order_item->owner_balance = $cart->price;
//                     }
                
//                     $order_item->save();

//                     if($order->created_by_type != 2){
//                         $enrollment = new Enrollment();
//                         $enrollment->order_id = $order->id;
//                         $enrollment->user_id = $student_user_id;
//                         $enrollment->course_id = $cart->course_id;

//                         $enrollment->main_price = $order_item->unit_price;
//                         $enrollment->price = $order_item->unit_price;
//                         $enrollment->original_price = $order_item->original_price;
//                         $enrollment->discount = $order_item->discount;
//                         $enrollment->admission_fee = $order_item->admission_fee;
//                         $enrollment->discount_type = $order_item->discount_type;
//                         $enrollment->net_original_price = $order_item->net_original_price;
//                         $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
//                         $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
//                         $enrollment->net_admission_fee = $order_item->net_admission_fee;
//                         $enrollment->request_enrollment = $order_item->request_enrollment;

//                         $enrollment->owner_user_id = $courseExits->user_id ?? null;
//                         $enrollment->start_date = now();
//                         $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
//                         $enrollment->save();
//                     }

//                     $this->addAffiliateHistory($cart,$order,$order_item);
//                 }
//             }
//         }

//         DB::commit();
//         $target_url = route('student_payment.completed');
//         $text = __("Student payment has been accepted and paid manually.");
//         $this->send($text, 1, $target_url, null);

//         $email_sent = $this->sendPaymentEmail($order->id);
//         $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
      
//         $getCartData = CartManagement::where('id',$payment_id)->first();
//         if($getCartData){
//             $getCartData->delete();
//         }

       
//         return redirect()->route('student_payment.completed');

     
      

      
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         dd($th);
//         $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
//         return redirect()->back();
//     }
// }


//     public function updatePaymentStatus(Request $request)
// {
//     $request->validate([
//         'transaction_id' => 'required', 
//     ]);
//     DB::beginTransaction();
//     try {
//         $payment_id = (int)$request->paymentid;
//         $getPaymentData = CartManagement::where('id',$payment_id)->first();
//         if( $getPaymentData == null){
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Payment ID Does Not Exists.'));
//             return redirect()->back();
//         }
//         $transaction_id = $request->transaction_id;
//         $getCheck = Order::where('faysal_transaction_id',$transaction_id)
//         ->orWhere('meezan_transaction_id',$transaction_id)
//         ->orWhere('payment_id',$transaction_id)->first();
//         if($getCheck){
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Transaction ID is not Unique. Already Exists.'));
//             return redirect()->back();
//         }

      
//         $unpaidAmount = $getPaymentData->price;
//         $subTotal = $getPaymentData->price;
//         $month = $getPaymentData->month;
//         $grandTotal = $subTotal;
//         $year = $getPaymentData->year;
//         $student_user_id = $getPaymentData->user?->id;
//         $carts = CartManagement::where('id',$payment_id)->get();
    
        
//         $order = new Order;
//         $successMsg = "Transaction Amount was deposited successfully manually.";
//         $order->user_id = $student_user_id;
//         $order->order_number = rand(100000,999999);
//         $order->sub_total = $subTotal;
//         $order->grand_total = $grandTotal;
//         $order->current_currency = "PKR";
//         $order->payment_currency = "PKR";
//         $order->payment_method = "Manual";
//         $order->payment_id = $transaction_id;
      
//         if($payment_id != null){
//             $order->created_by_type = 2;   
//             $order->cart_type = 2;
//         }
//         else{
//             $order->cart_type = 1;
//             $order->created_by_type = 1;   
//         }
//         $order->month = $month;
//         $order->year= $year;
//         $order->payment_status = 'paid';
//         $order->delivery_status = 1;
//         $payment_currency = get_option('faysal_currency');
//         $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;

//         $order->conversion_rate = $conversion_rate;
//         if ($conversion_rate) {
//             $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
//         }
//         $order->save();
    
//         foreach ($carts as $cart) {
           
//             if ($cart->course_id) {
              
//                 if ($cart->course_id) {
//                     $courseExits = Course::find($cart->course_id);
//                     $order_item = new Order_item();
//                     $order_item->order_id = $order->id;
//                     $order_item->user_id = $student_user_id;
//                     $order_item->product_id = $cart->product_id;
//                     $order_item->course_id = $cart->course_id;
//                     $order_item->type = 1;
//                     $order_item->receiver_info = $cart->receiver_info;
//                     $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;
//                     $order_item->unit_price = $cart->price;
//                     $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
//                     $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
//                     if ($adminCommission) {
//                         $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
//                         $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
//                         $order_item->sell_commission = $adminCommission;
//                     } else {
//                         $order_item->owner_balance = $cart->price;
//                     }
                
//                     $order_item->save();

//                     if($order->created_by_type != 2){
//                         $enrollment = new Enrollment();
//                         $enrollment->order_id = $order->id;
//                         $enrollment->user_id = $student_user_id;
//                         $enrollment->course_id = $cart->course_id;
//                         $enrollment->owner_user_id = $courseExits->user_id ?? null;
//                         $enrollment->start_date = now();
//                         $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
//                         $enrollment->save();
//                     }

//                     $this->addAffiliateHistory($cart,$order,$order_item);
//                 }
//             }
//         }
//         DB::commit();
//         $target_url = route('student_payment.completed');
//         $text = __("Student payment has been accepted and paid manually.");
//         $this->send($text, 1, $target_url, null);

//         $email_sent = $this->sendPaymentEmail($order->id);
//         $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");
      
//         $getCartData = CartManagement::where('id',$payment_id)->first();
//         if($getCartData){
//             $getCartData->delete();
//         }

       
//         return redirect()->route('student_payment.completed');

     
      

      
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         dd($th);
//         $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
//         return redirect()->back();
//     }
// }




public function sendPaymentEmail($payment_id = null,$payment_order_id = null){
    return app(\App\Services\PaymentReceiptEmailService::class)
        ->sendByOrderId($payment_id, $payment_order_id, 'generate', auth()->id());
}



public function updateOrderItems(){
   $getAllOrders = Order::all();
   foreach ($getAllOrders as $key => $order) {
        foreach ($order->items as $key => $order_item) {
          
            $order_item->created_by_type =$order->created_by_type;
            $order_item->cart_type =$order->cart_type;
           
            $order_item->save();
        }
   }
   echo "Done";
}



public function testEmail(){
    $result = $this->sendPaymentEmail(1039);
}

    public function completedPayments(Request $request)
    {
        $data['title'] = 'Completed Payments';
        // $data['completedPayments'] = Order::where('cart_type',2)->where('payment_status','paid')->orderBy('id', 'DESC')->paginate(10);

        $query = Order_item::query(); // Assuming you have these relationships

        $data['completedPayments'] = $query
        ->whereHas('order', function ($query2) {
            $query2->where('payment_status','paid');
        })
        ->where('is_donation','<>',1)
        ->orderBy('id', 'DESC')->paginate(25);



        if ($request->has('search')) {
            $searchTerm = $request->search;
         
            $query->where(function($q) use ($searchTerm) {
                $q->orWhereHas('order', function($subQuery) use ($searchTerm) {
                    $subQuery->where('id', 'LIKE', "%{$searchTerm}%")
                    ->orwhere('grand_total', 'LIKE', "%{$searchTerm}%")
                    ->orwhere('payment_method', 'LIKE', "%{$searchTerm}%")
                    ->orwhere('payment_id', 'LIKE', "%{$searchTerm}%")
                    ->orwhere('year', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('user', function($subQuery2) use ($searchTerm) {
                        $subQuery2->where('name', 'LIKE', "%{$searchTerm}%");
                    });
                })
                ->orWhereHas('course', function($subQuery) use ($searchTerm) {
                    $subQuery->where('title', 'LIKE', "%{$searchTerm}%");
                });  
            });
        }
        $data['completedPayments'] = $query->whereHas('order', function ($query2) {
            $query2->where('payment_status','paid');
        })
         ->where('is_donation','<>',1)
        ->orderBy('id', 'DESC')->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'completedPayments' => view('admin.student_payment.table-data-complete', $data)->render(),
                'pagination' => $data['completedPayments']->links()->toHtml()
            ]);
        }


    
     
        return view('admin.student_payment.completed', $data);
    }






      public function completedDonations(Request $request)
    {
        $data['title'] = 'Completed Donations';

        $query = Order_item::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->orWhereHas('order', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('id', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('grand_total', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('payment_method', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('payment_id', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('year', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($subQuery2) use ($searchTerm) {
                            $subQuery2->where('name', 'LIKE', "%{$searchTerm}%");
                        });
                })
                ->orWhereHas('course', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('title', 'LIKE', "%{$searchTerm}%");
                });
            });
        }

        $data['completedDonations'] = $query
            ->whereHas('order', function ($query2) {
                $query2->where('payment_status', 'paid');
            })
            ->where('is_donation', 1)
            ->orderBy('id', 'DESC')
            ->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'completedDonations' => view('admin.student_payment.table-data-complete-donations', $data)->render(),
                'pagination' => $data['completedDonations']->links()->toHtml(),
            ]);
        }

        return view('admin.student_payment.completed_donations', $data);
    }




    public function addPayment()
{
    $userids = array();
    $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
    if(!$data['instructors']->isEmpty()){
        foreach ($data['instructors'] as $key => $value) {
            $userids[] = $value->user_id;
        }
    }
    $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();
 
    $data['courses'] = Course::where('status',1)->orderBy('id', 'asc')->get();
    $data['title'] = 'Add Student Payment';

     // admission fee pulled from your option store (same as approve form)
    $data['admissionFee'] = (float)(get_option('admission_fee') ?? 0);


    return view('admin.student_payment.add', $data);
}



/**
 * AJAX endpoint: check if student already has enrollment for a given course
 * Returns JSON: { enrolled: true|false }
 */
public function checkStudentEnrollment(Request $request)
{
    $request->validate([
        'student_id' => 'required|integer',
        'course_id' => 'required|integer'
    ]);

    $student = Student::find($request->student_id);
    if (!$student) {
        return response()->json(['enrolled' => false]);
    }
    $student_user_id = (int)$student->user_id;

    // Mirror the logic you use elsewhere: look for active enrollment whose end_date >= now
    $exists = Enrollment::where('course_id', $request->course_id)
                ->where('user_id', $student_user_id)
                ->where('status', ACCESS_PERIOD_ACTIVE)   // keep same constant you use
                // ->where('end_date', '>=', now())
                ->exists();

    return response()->json(['enrolled' => $exists]);
}


public function storePayment(Request $request)
{
    $request->validate([
        'student_id' => 'required|integer',
        'course_id' => 'required|integer',
        // price is per-student price
        'price' => 'required|numeric',
        'students_count' => 'nullable|integer|min:1',
        'month' => 'required',
        'year' => 'required',
        // discount inputs optional but ensure numeric if passed
        'discount_course_price' => 'nullable|numeric',
        'discount_type_course_price' => 'nullable|in:0,1',
        'discount_admission_fee' => 'nullable|numeric',
        'discount_type_admission_fee' => 'nullable|in:0,1',
        'admission_applicable' => 'nullable|in:0,1',
    ]);

    DB::beginTransaction();
    try {
        $student_id = (int)$request->student_id;
        $student = Student::where('id', $student_id)->first();
        if (!$student) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Student Does Not Exists.'));
            return redirect()->back();
        }
        $student_user_id = (int)$student->user_id;
        $studentUser = User::where('id', $student_user_id)->first();

        if ($request->course_id) {
            $check = CartManagement::whereUserId($student_user_id)
                ->whereCourseId($request->course_id)
                ->where('created_by_type', 2)
                ->where('month', (int)$request->month)
                ->where('year', (int)$request->year)
                ->first();

            if ($check) {
                DB::rollBack();
                $this->showToastrMessage('warning', __("Payment already added for current course within current month and year!"));
                return redirect()->back();
            }

            $course = Course::find($request->course_id);
            if (!$course) {
                DB::rollBack();
                $this->showToastrMessage('warning', __("Given course not found!"));
                return redirect()->back();
            }

            // students count (default 1)
            $studentsCount = (int)($request->students_count ?? 1);
            if ($studentsCount < 1) $studentsCount = 1;

            // Determine if admission fee should be applied server-side:
            $frontendAdmissionApplicable = $request->input('admission_applicable', '1');
            $alreadyEnrolled = Enrollment::where('course_id', $request->course_id)
                    ->where('user_id', $student_user_id)
                    ->where('status', ACCESS_PERIOD_ACTIVE)
                    ->exists();

            $admissionApplicable = ($frontendAdmissionApplicable == '1' && !$alreadyEnrolled);

            // === Recalculate net values on the server (do not trust client values) ===
            $courseUnitPrice = (float)$request->price; // per-student price
            // Updated (total) course price = per-student * students_count
            $coursePriceTotal = $courseUnitPrice * $studentsCount;

            $discountCourse = (float)($request->discount_course_price ?? 0.0);
            $discountTypeCourse = $request->discount_type_course_price ?? '0'; // '0' amount, '1' percentage
            $discountAmountCourse = 0.0;
            if ($discountTypeCourse === '0') {
                $discountAmountCourse = $discountCourse;
            } else {
                $discountAmountCourse = ($coursePriceTotal * $discountCourse) / 100;
            }
            if ($discountAmountCourse > $coursePriceTotal) $discountAmountCourse = $coursePriceTotal;
            $netCourse = max($coursePriceTotal - $discountAmountCourse, 0);

            // Admission fee per student (from option)
            $admissionFeePerUnit = (float)(get_option('admission_fee') ?? 0.0);
            // admission total = per-student admission * students_count
            $admissionFeeTotal = $admissionFeePerUnit * $studentsCount;

            $discountAdmission = (float)($request->discount_admission_fee ?? 0.0);
            $discountTypeAdmission = $request->discount_type_admission_fee ?? '0';
            $discountAmountAdmission = 0.0;
            if ($admissionApplicable) {
                if ($discountTypeAdmission === '0') {
                    $discountAmountAdmission = $discountAdmission;
                } else {
                    $discountAmountAdmission = ($admissionFeeTotal * $discountAdmission) / 100;
                }
                if ($discountAmountAdmission > $admissionFeeTotal) $discountAmountAdmission = $admissionFeeTotal;
                $netAdmission = max($admissionFeeTotal - $discountAmountAdmission, 0);
            } else {
                $netAdmission = 0.0;
                // ensure admission totals reflect no admission
                $admissionFeeTotal = 0.0;
                $discountAmountAdmission = 0.0;
            }

            $totalNet = $netCourse + $netAdmission;
        
            // Remove old pending order items if due etc (kept existing logic)
            $enrollment = Enrollment::where([
                'course_id' => (int)$request->course_id,
                'user_id' => $student_user_id,
                'status' => ACCESS_PERIOD_ACTIVE
            ])->where('end_date', '>=', now())->first();

            if ($enrollment) {
                $order = Order::find($enrollment->order_id);
                if ($order) {
                    if ($order->payment_status == 'due') {
                        Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                            $q->delete();
                        });
                        $order->delete();
                    }
                }
            }

            // Check existing cart item
            $cartExists = CartManagement::whereUserId($student_user_id)
                ->whereCourseId($request->course_id)
                ->where('created_by_type', 2)
                ->first();
            if ($cartExists) {
                DB::rollBack();
                $this->showToastrMessage('warning', __("Course Payment Already Added in Pending List!"));
                return redirect()->back();
            }

             
            // Build and save cart
            $cart = new CartManagement();
            $cart->user_id = $student_user_id;
            $cart->receiver_info = [];
            $cart->course_id = $request->course_id;
            $cart->month = $request->month;
            $cart->year = $request->year;
            $cart->quantity = 1; // keep as previous business logic (if you want change to studentsCount you can)
            $cart->students_count = $studentsCount; // NEW: store the students count
            $cart->created_by_type = 2;
            $cart->is_subscription_enable = $course->is_subscription_enable ?? 0;

            // set stored price values
            $cart->main_price = $totalNet; // total net amount
            $cart->price = $totalNet;
            // store original_price as the UPDATED course original price (per your request)
            $cart->original_price = $coursePriceTotal; // (course original price * students_count)
            if ($admissionApplicable) {
                $cart->admission_fee = $admissionFeeTotal; // admission total after multiplying students
                $cart->discount_type_admission_fee = (float)$request->discount_type_admission_fee;
                $cart->discount_admission_fee = $discountAmountAdmission;
                $cart->net_admission_fee = $netAdmission;
            } else {
                $cart->admission_fee = 0.00;
                $cart->discount_type_admission_fee = 0.00;
                $cart->discount_admission_fee = 0.00;
                $cart->net_admission_fee = 0.00;
            }
            $cart->discount = $discountAmountCourse;
            $cart->discount_type = $request->discount_type_course_price;
            $cart->net_original_price = $netCourse;

            // Request data to create invoice PDF/email
            $requestData = [
                'course_unit_price' => $courseUnitPrice,
                'students_count' => $studentsCount,
                'course_price_total' => $coursePriceTotal,
                'discount_course_price' => $discountAmountCourse,
                'discount_type_course_price' => (float)$request->discount_type_course_price,
                'net_course_price' => $netCourse,
                'admission_fee_per_unit' => $admissionFeePerUnit,
                'admission_fee_total' => $admissionFeeTotal,
                'discount_admission_fee' => $discountAmountAdmission,
                'discount_type_admission_fee' => (float)$request->discount_type_admission_fee,
                'net_admission_fee' => $netAdmission,
                'net_price' => $totalNet,
            ];

             

            // email and invoice creation (kept your existing logic but use updated values)
            $studentCourse = Course::where('id', $request->course_id)->first();
            $userEmail = $studentUser->email;
            $template_name = 'Payment-create-student';
            $checkoutLink = route('student.checkout', ['type' => 1, 'location' => 1]);
            $viewData = [
                '{{USER_NAME}}' => $studentUser->name,
                '{{COURSE_NAME}}' => $studentCourse->title,
                // show total amount paid (totalNet) or per-student? I keep per previous behaviour - pass total
                '{{COURSE_FEE}}' => (int)$totalNet,
                '{{CHECKOUT}}' => $checkoutLink,
              
            ];

            $emailResponse = sendEmailNotification($viewData, $userEmail, $template_name);
            // Save cart regardless of email success (your prior logic saved it on email failure too)

             

            $cart->save();

          

            // Generate PDF invoice
            $TIME = time();
            $pdfFilePath = public_path('invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf');

            $mpdf = new Mpdf([
                'tempDir' => public_path('mpdf-temp'),
                'default_font' => 'dejavusans',
            ]);

            $fullData = [];
            $fullData['year'] = $cart->year ?: date('Y', strtotime($cart->created_at));
            $fullData['id'] = $cart->id;
            $fullData['s_name'] = $studentUser->name;
            $fullData['s_gender'] = $student->gender;
            $fullData['s_parent_name'] = $student->parent_name;
            $fullData['payment_date'] = $cart->created_at;
            $fullData['month'] = $cart->month ?: date('F', strtotime($cart->created_at));
            $fullData['course_name'] = $course->title;
            $getCurrency = Currency::where('current_currency','on')->first();
            $fullData['currency'] = $getCurrency ? $getCurrency->symbol : 'Rs';

            // include students related fields
            $fullData['students_count'] = $studentsCount;
            $fullData['amount'] = $totalNet; // total paid
            $fullData['course_price_1'] = (float)$coursePriceTotal;
            $fullData['discount'] = $discountAmountCourse;
            $fullData['discount_type'] = (float)$request->discount_type_course_price;
            $fullData['net_course_price_1'] = $netCourse;
            $fullData['admission_fee'] = (float)$admissionFeeTotal;
            $fullData['discount_admission_fee'] = $discountAmountAdmission;
            $fullData['discount_type_admission_fee'] = (float)$request->discount_type_admission_fee;
            $fullData['net_admission_fee'] = $netAdmission;

          


            $html = view('emails.invoice-challan', ["data" => $fullData])->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

            $relativePath = 'invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf';
            $cart->invoice_pdf_path = $relativePath;
            $cart->save();

            // Toaster & commit
            if ($emailResponse) {
                $this->showToastrMessage('success', __('Student Payment added to pending list and email is being sent to student.'));
            } else {
                $this->showToastrMessage('success', __('Student Payment added to pending list but email is not sent to student due to email server issue.'));
            }

            DB::commit();
            return redirect()->route('student_payment.pending');
        }

    } catch (\Throwable $th) {
        DB::rollBack();
        \Log::error('storePayment error: '.$th->getMessage().' on '.$th->getFile().'#'.$th->getLine());
        $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
        return redirect()->back();
    }
}




// public function storePayment1(Request $request)
// {
//     $request->validate([
//         'student_id' => 'required|integer', 
//         'course_id' => 'required|integer', 
//         'price' => 'required|integer', 
//         'month' => 'required', 
//         'year' => 'required', 
//              // discount inputs optional but ensure numeric if passed
//         'discount_course_price' => 'nullable|numeric',
//         'discount_type_course_price' => 'nullable|in:0,1',
//         'discount_admission_fee' => 'nullable|numeric',
//         'discount_type_admission_fee' => 'nullable|in:0,1',
//         'admission_applicable' => 'nullable|in:0,1',
//     ]);
//     DB::beginTransaction();
//     try {
       
//         $student_id = (int)$request->student_id;
//         $student = Student::where('id',$student_id)->first();
//         if($student){
//             $student_user_id = (int)$student->user_id;
//         }
//         else{
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Student Does Not Exists.'));
//             return redirect()->back();
//         }
//         $studentUser = User::where('id',$student_user_id)->first();
//         if ($request->course_id) {
//             $check = CartManagement::whereUserId($student_user_id)->whereCourseId($request->course_id)
//             ->where('created_by_type',2)
//             ->where('month',(int)$request->month)
//             ->where('year',(int)$request->year)->first();

         

//              if($check){
//                 DB::rollBack();
//                 $this->showToastrMessage('warning', __("Payment already added for current course within current month and year!"));
//                 return redirect()->back();
//              }


//               // ensure course exists
//             $course = Course::find($request->course_id);
//             if (!$course) {
//                 DB::rollBack();
//                 $this->showToastrMessage('warning', __("Given course not found!"));
//                 return redirect()->back();
//             }

//             // Determine if admission fee should be applied server-side:
//             // (If admission_applicable flag sent from frontend use it; but also re-check)
//             $frontendAdmissionApplicable = $request->input('admission_applicable','1');
//             // Extra server check: if student already has active enrollment for course, admission not applicable
//             $alreadyEnrolled = Enrollment::where('course_id', $request->course_id)
//                     ->where('user_id', $student_user_id)
//                     ->where('status', ACCESS_PERIOD_ACTIVE)
//                     // ->where('end_date', '>=', now())
//                     ->exists();

          
//             $admissionApplicable = ($frontendAdmissionApplicable == '1' && !$alreadyEnrolled);


//                   // === Recalculate net values on the server (do not trust client values) ===
//             $coursePrice = (float)$request->price;
//             $discountCourse = (float)($request->discount_course_price ?? 0.0);
//             $discountTypeCourse = $request->discount_type_course_price ?? '0'; // '0' amount, '1' percentage
//             $discountAmountCourse = 0.0;
//             if ($discountTypeCourse === '0') {
//                 $discountAmountCourse = $discountCourse;
//             } else {
//                 $discountAmountCourse = ($coursePrice * $discountCourse) / 100;
//             }
//             if ($discountAmountCourse > $coursePrice) $discountAmountCourse = $coursePrice;
//             $netCourse = max($coursePrice - $discountAmountCourse, 0);


//             $admissionFee = (float)(get_option('admission_fee') ?? 0.0);
//             $discountAdmission = (float)($request->discount_admission_fee ?? 0.0);
//             $discountTypeAdmission = $request->discount_type_admission_fee ?? '0';
//             $discountAmountAdmission = 0.0;
//             if ($admissionApplicable) {
//                 if ($discountTypeAdmission === '0') {
//                     $discountAmountAdmission = $discountAdmission;
//                 } else {
//                     $discountAmountAdmission = ($admissionFee * $discountAdmission) / 100;
//                 }
//                 if ($discountAmountAdmission > $admissionFee) $discountAmountAdmission = $admissionFee;
//                 $netAdmission = max($admissionFee - $discountAmountAdmission, 0);
//             } else {
//                 $netAdmission = 0.0;
//             }

//             $totalNet = $netCourse + $netAdmission;

             

//             $enrollment = Enrollment::where(['course_id' => (int)$request->course_id , 'user_id' => $student_user_id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
//             if ($enrollment) {
//                 $order = Order::find($enrollment->order_id);
//                 if ($order) {
//                     if ($order->payment_status == 'due') {
//                         Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
//                             $q->delete();
//                         });
//                         $order->delete();
//                     } 
//                 }
//             }
//             $cartExists = null;
//               // Build and save cart



//             $cartExists = CartManagement::whereUserId($student_user_id)
//                 ->whereCourseId($request->course_id)
//                 ->where('created_by_type',2)
//                 ->first();
//             if ($cartExists) {
//                 DB::rollBack();
//                 $this->showToastrMessage('warning', __("Course Payment Already Added in Pending List!"));
//                 return redirect()->back();
//             }

           

//             //Start:: Cart Management
//             $cart = new CartManagement();
//             $cart->user_id = $student_user_id;
//             $cart->receiver_info = [];
//             $cart->course_id = $request->course_id;
//             $cart->month = $request->month;
//             $cart->year = $request->year;
//             $cart->quantity = 1;
//             $cart->created_by_type = 2;
//             $cart->is_subscription_enable = $course->is_subscription_enable ?? 0;

//               // set stored price values (main_price = total net amount)
//             $cart->main_price = $totalNet;
//             $cart->price = $totalNet;
//             $cart->original_price = (float)$request->price;
//             if ($admissionApplicable) {
//                 $cart->admission_fee = (float)$request->admission_fee;
//                 $cart->discount_type_admission_fee = (float)$request->discount_type_admission_fee;
//                 $cart->discount_admission_fee = $discountAmountAdmission;
//                 $cart->net_admission_fee = $netAdmission;
//             }
//             else{
//                 $cart->admission_fee = 0.00;
//                 $cart->discount_type_admission_fee = 0.00;
//                 $cart->discount_admission_fee = 0.00;
//                 $cart->net_admission_fee = 0.00;
//             }
//             $cart->discount = $discountAmountCourse;
//             $cart->discount_type = $request->discount_type_course_price;
//             $cart->net_original_price = $netCourse;
           

             

//             $requestData = [
//                 'course_price' => (float)$request->price,
//                 'discount_course_price' => $discountAmountCourse,
//                 'discount_type_course_price' => (float)$request->discount_type_course_price,
//                 'net_course_price' => $netCourse,
//                 'admission_fee' => (float)$request->admission_fee,
//                 'discount_admission_fee' => $discountAmountAdmission,
//                 'discount_type_admission_fee' => (float)$request->discount_type_admission_fee,
//                 'net_admission_fee' => $netAdmission,
//                 'net_price' => $totalNet,
//             ];
         

             

//             $studentCourse = Course::where('id',$request->course_id)->first();
//             $userEmail =  $studentUser->email;
//             $template_name = 'Payment-create-student';
//             $viewData = [
//                 '{{USER_NAME}}' => $studentUser->name,
//                 '{{AMOUNT}}' => $request->price,
//                 '{{COURSE_NAME}}' => $studentCourse->title,
//             ];

//             $emailResponse =sendEmailNotification($viewData,$userEmail,$template_name);
//             if($emailResponse){
//                 $cart->save();
//                 $TIME = time();
//                 $pdfFilePath = public_path('invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf');

//                 // Create a new instance of mPDF
//                 $mpdf = new Mpdf([
//                     'tempDir' => public_path('mpdf-temp'), // ensure writable dir
//                     'default_font' => 'dejavusans',
//                 ]);

//                 $fullData = [];
//                 if($cart->year){
//                     $fullData['year'] = $cart->year;
//                 }
//                 else{
//                     $fullData['year'] = date('Y',strtotime($cart->created_at));
//                 }
//                 $fullData['id'] = $cart->id;
//                 $fullData['s_name'] = $studentUser->name;
//                 $fullData['s_gender'] = $student->gender;
//                 $fullData['s_parent_name'] = $student->parent_name;
//                 $fullData['payment_date'] = $cart->created_at;
//                 if($cart->month){
//                     $fullData['month'] = $cart->month;
//                 }
//                 else{
//                     $fullData['month'] = date('F',strtotime($cart->created_at));
//                 }
//                 $fullData['course_name'] = $course->title;
//                 $getCurrency = Currency::where('current_currency','on')->first();
//                 if($getCurrency){
//                     $fullData['currency'] = $getCurrency->symbol;
//                 }
//                 else{
//                     $fullData['currency'] = 'Rs';
//                 }
                
//                 $fullData['amount'] = $totalNet;
//                 $fullData['course_price_1'] = (float)$request->price;
//                 $fullData['discount'] = $discountAmountCourse;
//                 $fullData['discount_type'] = (float)$request->discount_type_course_price;
//                 $fullData['net_course_price_1'] = $netCourse;
//                 $fullData['admission_fee'] = (float)$request->admission_fee;
//                 $fullData['discount_admission_fee'] = $discountAmountAdmission;
//                 $fullData['discount_type_admission_fee'] = (float)$request->discount_type_admission_fee;
//                 $fullData['net_admission_fee'] = $netAdmission;


//                 // Load HTML view once and generate 3 copies
//                 // $copies = ['Bank Copy', 'CEF Copy', 'Student Copy'];
//                 // $html = '';
//                 // foreach ($copies as $copyName) {
//                 //     $html .= view('emails.invoice', [
//                 //         'student' => $student,
//                 //         'course' => $course,
//                 //         'data' => $requestData,
//                 //         'copy_type' => $copyName, // Pass type to view
//                 //     ])->render();
                    
//                 //     // Page break between copies (except last one)
//                 //     if ($copyName !== end($copies)) {
//                 //         $html .= '<pagebreak />';
//                 //     }
//                 // }  
            
//                 $html = view('emails.invoice-challan', ["data" => $fullData])->render();
//                 // Write content
//                 $mpdf->WriteHTML($html);   
//                 // Save the file
//                 $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);   
                
//                 $attachments = [
//                     [
//                         'path' => $pdfFilePath,
//                         'name' => 'Invoice-manual'.$course->title.'.pdf',
//                         'mime' => 'application/pdf',
//                     ],
//                 ];
//                 $relativePath = 'invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf';
//                 $cart->invoice_pdf_path = $relativePath;
//                 $cart->save();
                
//                 $this->showToastrMessage('success', __('Student Payment added to pending list and email is being sent to student.'));
//                 DB::commit();
//                 return redirect()->route('student_payment.pending');
//             }
//             else{
//                 $cart->save();
//                 $this->showToastrMessage('success', __('Student Payment added to pending list but email is not sent to student due to email server issue.'));
//                 DB::commit();
//                 return redirect()->route('student_payment.pending');
//             }
//         }


      

      
      
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
//         return redirect()->back();
//     }
// }




// public function storePayment(Request $request)
// {
//     $request->validate([
//         'student_id' => 'required|integer', 
//         'course_id' => 'required|integer', 
//         'price' => 'required|integer', 
//         'month' => 'required', 
//         'year' => 'required', 
//              // discount inputs optional but ensure numeric if passed
//         'discount_course_price' => 'nullable|numeric',
//         'discount_type_course_price' => 'nullable|in:0,1',
//         'discount_admission_fee' => 'nullable|numeric',
//         'discount_type_admission_fee' => 'nullable|in:0,1',
//         'admission_applicable' => 'nullable|in:0,1',
//     ]);
//     DB::beginTransaction();
//     try {
//         $student_id = (int)$request->student_id;
//         $student = Student::where('id',$student_id)->first();
//         if($student){
//             $student_user_id = (int)$student->user_id;
//         }
//         else{
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Student Does Not Exists.'));
//             return redirect()->back();
//         }
//         $studentUser = User::where('id',$student_user_id)->first();
//         if ($request->course_id) {
//             $check = CartManagement::whereUserId($student_user_id)->whereCourseId($request->course_id)
//             ->where('created_by_type',2)
//             ->where('month',(int)$request->month)
//             ->where('year',(int)$request->year)->first();
//              if($check){
//                 DB::rollBack();
//                 $this->showToastrMessage('warning', __("Payment already added for current course within current month and year!"));
//                 return redirect()->back();
//              }


//               // ensure course exists
//             $course = Course::find($request->course_id);
//             if (!$course) {
//                 DB::rollBack();
//                 $this->showToastrMessage('warning', __("Given course not found!"));
//                 return redirect()->back();
//             }

//             $enrollment = Enrollment::where(['course_id' => (int)$request->course_id , 'user_id' => $student_user_id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
//             if ($enrollment) {
//                 $order = Order::find($enrollment->order_id);
//                 if ($order) {
//                     if ($order->payment_status == 'due') {
//                         Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
//                             $q->delete();
//                         });
//                         $order->delete();
//                     } 
//                     // elseif ($order->payment_status == 'pending') {
//                     //     DB::rollBack();
//                     //     $this->showToastrMessage('warning', __("Given student has already requested the given course & status is pending!"));
//                     //     return redirect()->back();
//                     // } 
//                     // elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
//                     //     DB::rollBack();
//                     //     $this->showToastrMessage('warning', __("Given student has already purchased the course!"));
//                     //     return redirect()->back();
//                     // }
//                 }
//                 // else{
//                 //     DB::rollBack();
//                 //     $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
//                 //     return redirect()->back();
//                 // }
//             }
//             $courseExits = Course::find($request->course_id);
//             if (!$courseExits) {
//                 DB::rollBack();
//                 $this->showToastrMessage('warning', __("Given course not found!"));
//                 return redirect()->back();
//             }
//         }

//         //Start:: Cart Management
//         $cartExists = null;
//         if ($request->course_id) {
//             // $cartExists = CartManagement::whereUserId($student_user_id)->whereCourseId($request->course_id)->first();
//             $cartExists = CartManagement::whereUserId($student_user_id)->whereCourseId($request->course_id)
//             ->where('created_by_type',2)->first();
//         }
//         if ($cartExists) {
//             DB::rollBack();
//             $this->showToastrMessage('warning', __("Course Payment Already Added in Pending List!"));
//             return redirect()->back();
//         }
//         $quantity = 1;
//         $cart = NULL;

//         if(is_null($cart)){
//             $cart = new CartManagement();
//         }else{
//             $quantity = ($request->quantity ?? 0) + $cart->quantity;
//         }

//         $cart->user_id = $student_user_id;
//         $cart->receiver_info = [];
//         $cart->course_id = $request->course_id;
//         $cart->month = $request->month;
//         $cart->year = $request->year;
//         $cart->quantity = $quantity ?? 0;
//         $cart->created_by_type = 2;
//         if($request->course_id){
//             $course = Course::findOrFail($request->course_id);
//             $cart->is_subscription_enable = $course->is_subscription_enable;
//         }
//         if ($request->course_id) {
//             $cart->main_price = $request->price;
//             $cart->price = $request->price;
//         }


//         $studentCourse = Course::where('id',$request->course_id)->first();
//         $userEmail =  $studentUser->email;
//         $template_name = 'Payment-create-student';
//         $viewData = [
//             '{{USER_NAME}}' => $studentUser->name,
//             '{{AMOUNT}}' => $request->price,
//             '{{COURSE_NAME}}' => $studentCourse->title,
//         ];

//         $emailResponse =sendEmailNotification($viewData,$userEmail,$template_name);
//         if($emailResponse){
//             $cart->save();
//             $this->showToastrMessage('success', __('Student Payment added to pending list and email is being sent to student.'));
//             DB::commit();
//             return redirect()->route('student_payment.pending');
//         }
//         else{
//             $cart->save();
//             $this->showToastrMessage('success', __('Student Payment added to pending list but email is not sent to student due to email server issue.'));
//             DB::commit();
//             return redirect()->route('student_payment.pending');
//         }
      
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
//         return redirect()->back();
//     }
// }


public function editPayment($id)
{
   
     // load the existing cart entry
    $cart = CartManagement::find($id);
    if (!$cart) {
        $this->showToastrMessage('warning', __('Cart not found.'));
        return redirect()->back();
    }
    
    $data['paymentDetail'] = CartManagement::where('id',$id)->orderBy('id', 'asc')->first();
    $userID = $data['paymentDetail']->user_id;
    $getStudent = Student::where('user_id',$userID)->first();
    $data['studentID'] = $getStudent->id;
    $data['studentName'] = $getStudent->name;
    $userids = array();
    $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
    if(!$data['instructors']->isEmpty()){
        foreach ($data['instructors'] as $key => $value) {
            $userids[] = $value->user_id;
        }
    }
    $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();
 
    $data['courses'] = Course::where('status',1)->orderBy('id', 'asc')->get();
    $data['title'] = 'Edit Student Payment';

     // admission fee option
    $data['admissionFee'] = (float)(get_option('admission_fee') ?? 0);

    // prefill values for view
    $data['cart'] = $cart;
    // get related student & user
    $student = Student::where('user_id', $cart->user_id)->first();
    $data['selected_student_id'] = $student ? $student->id : null;
    $data['selected_course_id'] = $cart->course_id;
    $data['students_count'] = $cart->students_count ?? 1;

    // If you want to show calculated displays in the form:
    $data['course_unit_price'] = $cart->original_price && $cart->students_count ? ($cart->original_price / max(1, $cart->students_count)) : 0;
    $data['net_course_price'] = $cart->net_original_price ?? 0;
    $data['net_admission_fee'] = $cart->net_admission_fee ?? 0;
    $data['net_price'] = $cart->price ?? 0;

    return view('admin.student_payment.edit', $data);
}




public function editPayment2($id)
{
   
     // load the existing cart entry
    $cart = CartManagement::find($id);
    if (!$cart) {
        $this->showToastrMessage('warning', __('Cart not found.'));
        return redirect()->back();
    }
    
    $data['paymentDetail'] = CartManagement::where('id',$id)->orderBy('id', 'asc')->first();
    $userID = $data['paymentDetail']->user_id;
    $getStudent = Student::where('user_id',$userID)->first();
    $data['studentID'] = $getStudent->id;
    $data['studentName'] = $getStudent->name;
    $userids = array();
    $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
    if(!$data['instructors']->isEmpty()){
        foreach ($data['instructors'] as $key => $value) {
            $userids[] = $value->user_id;
        }
    }
    $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();
 
    $data['courses'] = Course::where('status',1)->orderBy('id', 'asc')->get();
    $data['title'] = 'Edit Challan Payment';

     // admission fee option
    $data['admissionFee'] = (float)(get_option('admission_fee') ?? 0);

    // prefill values for view
    $data['cart'] = $cart;
    // get related student & user
    $student = Student::where('user_id', $cart->user_id)->first();
    $data['selected_student_id'] = $student ? $student->id : null;
    $data['selected_course_id'] = $cart->course_id;
    $data['students_count'] = $cart->students_count ?? 1;

    // If you want to show calculated displays in the form:
    $data['course_unit_price'] = $cart->original_price && $cart->students_count ? ($cart->original_price / max(1, $cart->students_count)) : 0;
    $data['net_course_price'] = $cart->net_original_price ?? 0;
    $data['net_admission_fee'] = $cart->net_admission_fee ?? 0;
    $data['net_price'] = $cart->price ?? 0;

    return view('admin.student_payment.edit_challan', $data);
}


public function editPayment3($id)
{
   
     // load the existing cart entry
    $cart = CartManagement::find($id);
    if (!$cart) {
        $this->showToastrMessage('warning', __('Cart not found.'));
        return redirect()->back();
    }
    
    $data['paymentDetail'] = CartManagement::where('id',$id)->orderBy('id', 'asc')->first();
    $userID = $data['paymentDetail']->user_id;
    $getStudent = Student::where('user_id',$userID)->first();
    $data['studentID'] = $getStudent->id;
    $data['studentName'] = $getStudent->name;
    $userids = array();
    $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
    if(!$data['instructors']->isEmpty()){
        foreach ($data['instructors'] as $key => $value) {
            $userids[] = $value->user_id;
        }
    }
    $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();
 
    $data['courses'] = Course::where('status',1)->orderBy('id', 'asc')->get();
    $data['title'] = 'Edit Challan Payment';

     // admission fee option
    $data['admissionFee'] = (float)(get_option('admission_fee') ?? 0);

    // prefill values for view
    $data['cart'] = $cart;
    // get related student & user
    $student = Student::where('user_id', $cart->user_id)->first();
    $data['selected_student_id'] = $student ? $student->id : null;
    $data['selected_course_id'] = $cart->course_id;
    $data['students_count'] = $cart->students_count ?? 1;

    // If you want to show calculated displays in the form:
    $data['course_unit_price'] = $cart->original_price && $cart->students_count ? ($cart->original_price / max(1, $cart->students_count)) : 0;
    $data['net_course_price'] = $cart->net_original_price ?? 0;
    $data['net_admission_fee'] = $cart->net_admission_fee ?? 0;
    $data['net_price'] = $cart->price ?? 0;

    return view('admin.student_payment.edit_challan_2', $data);
}




public function updatePayment(Request $request, $id)
{
    $request->validate([
        'student_id' => 'required|integer',
        'course_id' => 'required|integer',
        // price is per-student price
        'price' => 'required|numeric',
        'students_count' => 'nullable|integer|min:1',
        'month' => 'required',
        'year' => 'required',
        // discount inputs optional but ensure numeric if passed
        'discount_course_price' => 'nullable|numeric',
        'discount_type_course_price' => 'nullable|in:0,1',
        'discount_admission_fee' => 'nullable|numeric',
        'discount_type_admission_fee' => 'nullable|in:0,1',
        'admission_applicable' => 'nullable|in:0,1',
    ]);

    DB::beginTransaction();
    try {
        $cart = CartManagement::find($id);
        if (!$cart) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Cart not found.'));
            return redirect()->back();
        }

        // fetch student & user
        $student_id = (int)$request->student_id;
        $student = Student::where('id', $student_id)->first();
        if (!$student) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Student Does Not Exists.'));
            return redirect()->back();
        }
        $student_user_id = (int)$student->user_id;
        $studentUser = User::where('id', $student_user_id)->first();

        // check course exists
        $course = Course::find($request->course_id);
        if (!$course) {
            DB::rollBack();
            $this->showToastrMessage('warning', __("Given course not found!"));
            return redirect()->back();
        }

        // Prevent duplicate pending cart for same user/course/month/year but allow same current cart
        $existingConflict = CartManagement::whereUserId($student_user_id)
            ->whereCourseId($request->course_id)
            ->where('created_by_type', 2)
            ->where('month', (int)$request->month)
            ->where('year', (int)$request->year)
            ->where('id', '!=', $cart->id)
            ->first();

        if ($existingConflict) {
            DB::rollBack();
            $this->showToastrMessage('warning', __("Another Payment already exists for this course within same month and year!"));
            return redirect()->back();
        }

        // students count (default 1)
        $studentsCount = (int)($request->students_count ?? 1);
        if ($studentsCount < 1) $studentsCount = 1;

        // Determine admission applicability server-side (do not trust client)
        $frontendAdmissionApplicable = $request->input('admission_applicable', '1');
        $alreadyEnrolled = Enrollment::where('course_id', $request->course_id)
                ->where('user_id', $student_user_id)
                ->where('status', ACCESS_PERIOD_ACTIVE)
                ->exists();

        $admissionApplicable = ($frontendAdmissionApplicable == '1' && !$alreadyEnrolled);

        // === Recalculate net values on the server ===
        $courseUnitPrice = (float)$request->price; // per-student price
        $coursePriceTotal = $courseUnitPrice * $studentsCount;

        $discountCourse = (float)($request->discount_course_price ?? 0.0);
        $discountTypeCourse = $request->discount_type_course_price ?? '0'; // '0' amount, '1' percentage
        $discountAmountCourse = 0.0;
        if ($discountTypeCourse === '0') {
            $discountAmountCourse = $discountCourse;
        } else {
            $discountAmountCourse = ($coursePriceTotal * $discountCourse) / 100;
        }
        if ($discountAmountCourse > $coursePriceTotal) $discountAmountCourse = $coursePriceTotal;
        $netCourse = max($coursePriceTotal - $discountAmountCourse, 0.0);

        // Admission fee per student (from option)
        $admissionFeePerUnit = (float)(get_option('admission_fee') ?? 0.0);
        $admissionFeeTotal = $admissionFeePerUnit * $studentsCount;

        $discountAdmission = (float)($request->discount_admission_fee ?? 0.0);
        $discountTypeAdmission = $request->discount_type_admission_fee ?? '0';
        $discountAmountAdmission = 0.0;
        if ($admissionApplicable) {
            if ($discountTypeAdmission === '0') {
                $discountAmountAdmission = $discountAdmission;
            } else {
                $discountAmountAdmission = ($admissionFeeTotal * $discountAdmission) / 100;
            }
            if ($discountAmountAdmission > $admissionFeeTotal) $discountAmountAdmission = $admissionFeeTotal;
            $netAdmission = max($admissionFeeTotal - $discountAmountAdmission, 0.0);
        } else {
            $netAdmission = 0.0;
            $admissionFeeTotal = 0.0;
            $discountAmountAdmission = 0.0;
        }

        $totalNet = $netCourse + $netAdmission;

        // Remove old pending order items logic (preserve previous behavior)
        $enrollment = Enrollment::where([
            'course_id' => (int)$request->course_id,
            'user_id' => $student_user_id,
            'status' => ACCESS_PERIOD_ACTIVE
        ])->where('end_date', '>=', now())->first();

        if ($enrollment) {
            $order = Order::find($enrollment->order_id);
            if ($order) {
                if ($order->payment_status == 'due') {
                    Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                        $q->delete();
                    });
                    $order->delete();
                }
            }
        }

        // Update cart fields (preserve created_by_type etc)
        $cart->user_id = $student_user_id;
        $cart->receiver_info = $cart->receiver_info ?? [];
        $cart->course_id = $request->course_id;
        $cart->month = $request->month;
        $cart->year = $request->year;
        // keep quantity business logic as previous (1) but store students_count
        $cart->quantity = 1;
        $cart->students_count = $studentsCount;
        $cart->created_by_type = 2;
        $cart->is_subscription_enable = $course->is_subscription_enable ?? 0;

        // set stored price values
        $cart->main_price = $totalNet; // total net amount
        $cart->price = $totalNet;
        // store original_price as the UPDATED course original price
        $cart->original_price = $coursePriceTotal; // (course original price * students_count)

        if ($admissionApplicable) {
            $cart->admission_fee = $admissionFeeTotal; // admission total for students
            $cart->discount_type_admission_fee = (float)$request->discount_type_admission_fee;
            $cart->discount_admission_fee = $discountAmountAdmission;
            $cart->net_admission_fee = $netAdmission;
        } else {
            $cart->admission_fee = 0.00;
            $cart->discount_type_admission_fee = 0.00;
            $cart->discount_admission_fee = 0.00;
            $cart->net_admission_fee = 0.00;
        }

        $cart->discount = $discountAmountCourse;
        $cart->discount_type = $request->discount_type_course_price;
        $cart->net_original_price = $netCourse;

        // update requestData if you use later in emails/invoice
        $requestData = [
            'course_unit_price' => $courseUnitPrice,
            'students_count' => $studentsCount,
            'course_price_total' => $coursePriceTotal,
            'discount_course_price' => $discountAmountCourse,
            'discount_type_course_price' => (float)$request->discount_type_course_price,
            'net_course_price' => $netCourse,
            'admission_fee_per_unit' => $admissionFeePerUnit,
            'admission_fee_total' => $admissionFeeTotal,
            'discount_admission_fee' => $discountAmountAdmission,
            'discount_type_admission_fee' => (float)$request->discount_type_admission_fee,
            'net_admission_fee' => $netAdmission,
            'net_price' => $totalNet,
        ];

        // Save cart updates now (this assigns updated id/updated_at)
        $cart->save();

        // Generate PDF invoice (overwrite existing invoice file if desired)
        $TIME = time();
        $pdfFilePath = public_path('invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf');

        $mpdf = new Mpdf([
            'tempDir' => public_path('mpdf-temp'),
            'default_font' => 'dejavusans',
        ]);

        // prepare fullData like in storePayment
        $fullData = [];
        $fullData['year'] = $cart->year ?: date('Y', strtotime($cart->created_at));
        $fullData['id'] = $cart->id;
        $fullData['s_name'] = $studentUser->name;
        $fullData['s_gender'] = $student->gender;
        $fullData['s_parent_name'] = $student->parent_name;
        $fullData['payment_date'] = $cart->created_at;
        $fullData['month'] = $cart->month ?: date('F', strtotime($cart->created_at));
        $fullData['course_name'] = $course->title;
        $getCurrency = Currency::where('current_currency','on')->first();
        $fullData['currency'] = $getCurrency ? $getCurrency->symbol : 'Rs';

        $fullData['students_count'] = $studentsCount;
        $fullData['amount'] = $totalNet; // total paid
        $fullData['course_price_1'] = (float)$coursePriceTotal;
        $fullData['discount'] = $discountAmountCourse;
        $fullData['discount_type'] = (float)$request->discount_type_course_price;
        $fullData['net_course_price_1'] = $netCourse;
        $fullData['admission_fee'] = (float)$admissionFeeTotal;
        $fullData['discount_admission_fee'] = $discountAmountAdmission;
        $fullData['discount_type_admission_fee'] = (float)$request->discount_type_admission_fee;
        $fullData['net_admission_fee'] = $netAdmission;

        $html = view('emails.invoice-challan', ["data" => $fullData])->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

        $relativePath = 'invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf';
        $cart->invoice_pdf_path = $relativePath;
        $cart->save();

        // email and invoice creation (reuse existing behavior)
        $studentCourse = $course;
        $userEmail = $studentUser->email;
        $template_name = 'Payment-create-student';
        $checkoutLink = route('student.checkout', ['type' => 1, 'location' => 1]);
        $viewData = [
            '{{USER_NAME}}' => $studentUser->name,
            '{{COURSE_NAME}}' => $studentCourse->title,
            '{{COURSE_FEE}}' => (int)$totalNet,
            '{{CHECKOUT}}' => $checkoutLink,
        ];

        $emailResponse = sendEmailNotification($viewData, $userEmail, $template_name);

        if ($emailResponse) {
            $this->showToastrMessage('success', __('Student Payment updated and email is being sent to student.'));
        } else {
            $this->showToastrMessage('success', __('Student Payment updated but email is not sent to student due to email server issue.'));
        }

        DB::commit();
        return redirect()->route('student_payment.pending');

    } catch (\Throwable $th) {
        DB::rollBack();
        \Log::error('updatePayment error: '.$th->getMessage().' on '.$th->getFile().'#'.$th->getLine());
        $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
        return redirect()->back();
    }
}




public function updatePayment2(Request $request, $id)
{
    $request->validate([
        'student_id' => 'required|integer',
        'course_id' => 'required|integer',
        // price is per-student price
        'price' => 'required|numeric',
        'students_count' => 'nullable|integer|min:1',
        // 'month' => 'required',
        // 'year' => 'required',
        // discount inputs optional but ensure numeric if passed
        'discount_course_price' => 'nullable|numeric',
        'discount_type_course_price' => 'nullable|in:0,1',
        'discount_admission_fee' => 'nullable|numeric',
        'discount_type_admission_fee' => 'nullable|in:0,1',
        'admission_applicable' => 'nullable|in:0,1',
    ]);

    DB::beginTransaction();
    try {
        $cart = CartManagement::find($id);
        if (!$cart) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Cart not found.'));
            return redirect()->back();
        }

        // fetch student & user
        $student_id = (int)$request->student_id;
        $student = Student::where('id', $student_id)->first();
        if (!$student) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Student Does Not Exists.'));
            return redirect()->back();
        }
        $student_user_id = (int)$student->user_id;
        $studentUser = User::where('id', $student_user_id)->first();

        // check course exists
        $course = Course::find($request->course_id);
        if (!$course) {
            DB::rollBack();
            $this->showToastrMessage('warning', __("Given course not found!"));
            return redirect()->back();
        }

        // Prevent duplicate pending cart for same user/course/month/year but allow same current cart
        // $existingConflict = CartManagement::whereUserId($student_user_id)
        //     ->whereCourseId($request->course_id)
        //     ->where('created_by_type', 2)
        //     ->where('month', (int)$request->month)
        //     ->where('year', (int)$request->year)
        //     ->where('id', '!=', $cart->id)
        //     ->first();

        // if ($existingConflict) {
        //     DB::rollBack();
        //     $this->showToastrMessage('warning', __("Another Payment already exists for this course within same month and year!"));
        //     return redirect()->back();
        // }

        // students count (default 1)
        $studentsCount = (int)($request->students_count ?? 1);
        if ($studentsCount < 1) $studentsCount = 1;

        // Determine admission applicability server-side (do not trust client)
        $frontendAdmissionApplicable = $request->input('admission_applicable', '1');
        $alreadyEnrolled = Enrollment::where('course_id', $request->course_id)
                ->where('user_id', $student_user_id)
                ->where('status', ACCESS_PERIOD_ACTIVE)
                ->exists();

        $admissionApplicable = ($frontendAdmissionApplicable == '1' && !$alreadyEnrolled);

        // === Recalculate net values on the server ===
        $courseUnitPrice = (float)$request->price; // per-student price
        $coursePriceTotal = $courseUnitPrice * $studentsCount;

        $discountCourse = (float)($request->discount_course_price ?? 0.0);
        $discountTypeCourse = $request->discount_type_course_price ?? '0'; // '0' amount, '1' percentage
        $discountAmountCourse = 0.0;
        if ($discountTypeCourse === '0') {
            $discountAmountCourse = $discountCourse;
        } else {
            $discountAmountCourse = ($coursePriceTotal * $discountCourse) / 100;
        }
        if ($discountAmountCourse > $coursePriceTotal) $discountAmountCourse = $coursePriceTotal;
        $netCourse = max($coursePriceTotal - $discountAmountCourse, 0.0);

        // Admission fee per student (from option)
        $admissionFeePerUnit = (float)(get_option('admission_fee') ?? 0.0);
        $admissionFeeTotal = $admissionFeePerUnit * $studentsCount;

        $discountAdmission = (float)($request->discount_admission_fee ?? 0.0);
        $discountTypeAdmission = $request->discount_type_admission_fee ?? '0';
        $discountAmountAdmission = 0.0;
        if ($admissionApplicable) {
            if ($discountTypeAdmission === '0') {
                $discountAmountAdmission = $discountAdmission;
            } else {
                $discountAmountAdmission = ($admissionFeeTotal * $discountAdmission) / 100;
            }
            if ($discountAmountAdmission > $admissionFeeTotal) $discountAmountAdmission = $admissionFeeTotal;
            $netAdmission = max($admissionFeeTotal - $discountAmountAdmission, 0.0);
        } else {
            $netAdmission = 0.0;
            $admissionFeeTotal = 0.0;
            $discountAmountAdmission = 0.0;
        }

        $totalNet = $netCourse + $netAdmission;

        // Remove old pending order items logic (preserve previous behavior)
        $enrollment = Enrollment::where([
            'course_id' => (int)$request->course_id,
            'user_id' => $student_user_id,
            'status' => ACCESS_PERIOD_ACTIVE
        ])->where('end_date', '>=', now())->first();

        if ($enrollment) {
            $order = Order::find($enrollment->order_id);
            if ($order) {
                if ($order->payment_status == 'due') {
                    Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                        $q->delete();
                    });
                    $order->delete();
                }
            }
        }

        // Update cart fields (preserve created_by_type etc)
        $cart->user_id = $student_user_id;
        $cart->receiver_info = $cart->receiver_info ?? [];
        $cart->course_id = $request->course_id;
        // $cart->month = $request->month;
        // $cart->year = $request->year;
        // keep quantity business logic as previous (1) but store students_count
        $cart->quantity = 1;
        $cart->students_count = $studentsCount;
        $cart->created_by_type = 2;
        $cart->is_subscription_enable = $course->is_subscription_enable ?? 0;

        // set stored price values
        $cart->main_price = $totalNet; // total net amount
        $cart->price = $totalNet;
        // store original_price as the UPDATED course original price
        $cart->original_price = $coursePriceTotal; // (course original price * students_count)

        if ($admissionApplicable) {
            $cart->admission_fee = $admissionFeeTotal; // admission total for students
            $cart->discount_type_admission_fee = (float)$request->discount_type_admission_fee;
            $cart->discount_admission_fee = $discountAmountAdmission;
            $cart->net_admission_fee = $netAdmission;
        } else {
            $cart->admission_fee = 0.00;
            $cart->discount_type_admission_fee = 0.00;
            $cart->discount_admission_fee = 0.00;
            $cart->net_admission_fee = 0.00;
        }

        $cart->discount = $discountAmountCourse;
        $cart->discount_type = $request->discount_type_course_price;
        $cart->net_original_price = $netCourse;

        // update requestData if you use later in emails/invoice
        $requestData = [
            'course_unit_price' => $courseUnitPrice,
            'students_count' => $studentsCount,
            'course_price_total' => $coursePriceTotal,
            'discount_course_price' => $discountAmountCourse,
            'discount_type_course_price' => (float)$request->discount_type_course_price,
            'net_course_price' => $netCourse,
            'admission_fee_per_unit' => $admissionFeePerUnit,
            'admission_fee_total' => $admissionFeeTotal,
            'discount_admission_fee' => $discountAmountAdmission,
            'discount_type_admission_fee' => (float)$request->discount_type_admission_fee,
            'net_admission_fee' => $netAdmission,
            'net_price' => $totalNet,
        ];

        // Save cart updates now (this assigns updated id/updated_at)
        $cart->save();

        // Generate PDF invoice (overwrite existing invoice file if desired)
        $TIME = time();
        $pdfFilePath = public_path('invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf');

        $mpdf = new Mpdf([
            'tempDir' => public_path('mpdf-temp'),
            'default_font' => 'dejavusans',
        ]);

        // prepare fullData like in storePayment
        $fullData = [];
        $fullData['year'] = $cart->year ?: date('Y', strtotime($cart->created_at));
        $fullData['id'] = $cart->id;
        $fullData['s_name'] = $studentUser->name;
        $fullData['s_gender'] = $student->gender;
        $fullData['s_parent_name'] = $student->parent_name;
        $fullData['payment_date'] = $cart->created_at;
        $fullData['month'] = $cart->month ?: date('F', strtotime($cart->created_at));
        $fullData['course_name'] = $course->title;
        $getCurrency = Currency::where('current_currency','on')->first();
        $fullData['currency'] = $getCurrency ? $getCurrency->symbol : 'Rs';

        $fullData['students_count'] = $studentsCount;
        $fullData['amount'] = $totalNet; // total paid
        $fullData['course_price_1'] = (float)$coursePriceTotal;
        $fullData['discount'] = $discountAmountCourse;
        $fullData['discount_type'] = (float)$request->discount_type_course_price;
        $fullData['net_course_price_1'] = $netCourse;
        $fullData['admission_fee'] = (float)$admissionFeeTotal;
        $fullData['discount_admission_fee'] = $discountAmountAdmission;
        $fullData['discount_type_admission_fee'] = (float)$request->discount_type_admission_fee;
        $fullData['net_admission_fee'] = $netAdmission;

        $html = view('emails.invoice-challan', ["data" => $fullData])->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

        $relativePath = 'invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf';
        $cart->invoice_pdf_path = $relativePath;
        $cart->save();

        // email and invoice creation (reuse existing behavior)
        $studentCourse = $course;
        $userEmail = $studentUser->email;
        $template_name = 'Payment-create-student';
        $checkoutLink = route('student.checkout', ['type' => 1, 'location' => 1]);
        $viewData = [
            '{{USER_NAME}}' => $studentUser->name,
            '{{COURSE_NAME}}' => $studentCourse->title,
            '{{COURSE_FEE}}' => (int)$totalNet,
            '{{CHECKOUT}}' => $checkoutLink,
        ];

        $emailResponse = sendEmailNotification($viewData, $userEmail, $template_name);

        if ($emailResponse) {
            $this->showToastrMessage('success', __('Challan Payment updated and email is being sent to student.'));
        } else {
            $this->showToastrMessage('success', __('Challan Payment updated but email is not sent to student due to email server issue.'));
        }

        DB::commit();
        return redirect()->route('admin.dashboard');

    } catch (\Throwable $th) {
        DB::rollBack();
        \Log::error('updatePayment error: '.$th->getMessage().' on '.$th->getFile().'#'.$th->getLine());
        $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
        return redirect()->back();
    }
}




public function updatePayment3(Request $request, $id)
{
    $request->validate([
        'student_id' => 'required|integer',
        'course_id' => 'required|integer',
        // price is per-student price
        'price' => 'required|numeric',
        'students_count' => 'nullable|integer|min:1',
        // 'month' => 'required',
        // 'year' => 'required',
        // discount inputs optional but ensure numeric if passed
        'discount_course_price' => 'nullable|numeric',
        'discount_type_course_price' => 'nullable|in:0,1',
        'discount_admission_fee' => 'nullable|numeric',
        'discount_type_admission_fee' => 'nullable|in:0,1',
        'admission_applicable' => 'nullable|in:0,1',
    ]);

    DB::beginTransaction();
    try {
        $cart = CartManagement::find($id);
        if (!$cart) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Cart not found.'));
            return redirect()->back();
        }

        // fetch student & user
        $student_id = (int)$request->student_id;
        $student = Student::where('id', $student_id)->first();
        if (!$student) {
            DB::rollBack();
            $this->showToastrMessage('warning', __('Given Student Does Not Exists.'));
            return redirect()->back();
        }
        $student_user_id = (int)$student->user_id;
        $studentUser = User::where('id', $student_user_id)->first();

        // check course exists
        $course = Course::find($request->course_id);
        if (!$course) {
            DB::rollBack();
            $this->showToastrMessage('warning', __("Given course not found!"));
            return redirect()->back();
        }

        // Prevent duplicate pending cart for same user/course/month/year but allow same current cart
        // $existingConflict = CartManagement::whereUserId($student_user_id)
        //     ->whereCourseId($request->course_id)
        //     ->where('created_by_type', 2)
        //     ->where('month', (int)$request->month)
        //     ->where('year', (int)$request->year)
        //     ->where('id', '!=', $cart->id)
        //     ->first();

        // if ($existingConflict) {
        //     DB::rollBack();
        //     $this->showToastrMessage('warning', __("Another Payment already exists for this course within same month and year!"));
        //     return redirect()->back();
        // }

        // students count (default 1)
        $studentsCount = (int)($request->students_count ?? 1);
        if ($studentsCount < 1) $studentsCount = 1;

        // Determine admission applicability server-side (do not trust client)
        $frontendAdmissionApplicable = $request->input('admission_applicable', '1');
        $alreadyEnrolled = Enrollment::where('course_id', $request->course_id)
                ->where('user_id', $student_user_id)
                ->where('status', ACCESS_PERIOD_ACTIVE)
                ->exists();

        $admissionApplicable = ($frontendAdmissionApplicable == '1' && !$alreadyEnrolled);

        // === Recalculate net values on the server ===
        $courseUnitPrice = (float)$request->price; // per-student price
        $coursePriceTotal = $courseUnitPrice * $studentsCount;

        $discountCourse = (float)($request->discount_course_price ?? 0.0);
        $discountTypeCourse = $request->discount_type_course_price ?? '0'; // '0' amount, '1' percentage
        $discountAmountCourse = 0.0;
        if ($discountTypeCourse === '0') {
            $discountAmountCourse = $discountCourse;
        } else {
            $discountAmountCourse = ($coursePriceTotal * $discountCourse) / 100;
        }
        if ($discountAmountCourse > $coursePriceTotal) $discountAmountCourse = $coursePriceTotal;
        $netCourse = max($coursePriceTotal - $discountAmountCourse, 0.0);

        // Admission fee per student (from option)
        $admissionFeePerUnit = (float)(get_option('admission_fee') ?? 0.0);
        $admissionFeeTotal = $admissionFeePerUnit * $studentsCount;

        $discountAdmission = (float)($request->discount_admission_fee ?? 0.0);
        $discountTypeAdmission = $request->discount_type_admission_fee ?? '0';
        $discountAmountAdmission = 0.0;
        if ($admissionApplicable) {
            if ($discountTypeAdmission === '0') {
                $discountAmountAdmission = $discountAdmission;
            } else {
                $discountAmountAdmission = ($admissionFeeTotal * $discountAdmission) / 100;
            }
            if ($discountAmountAdmission > $admissionFeeTotal) $discountAmountAdmission = $admissionFeeTotal;
            $netAdmission = max($admissionFeeTotal - $discountAmountAdmission, 0.0);
        } else {
            $netAdmission = 0.0;
            $admissionFeeTotal = 0.0;
            $discountAmountAdmission = 0.0;
        }

        $totalNet = $netCourse + $netAdmission;

        // Remove old pending order items logic (preserve previous behavior)
        $enrollment = Enrollment::where([
            'course_id' => (int)$request->course_id,
            'user_id' => $student_user_id,
            'status' => ACCESS_PERIOD_ACTIVE
        ])->where('end_date', '>=', now())->first();

        if ($enrollment) {
            $order = Order::find($enrollment->order_id);
            if ($order) {
                if ($order->payment_status == 'due') {
                    Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                        $q->delete();
                    });
                    $order->delete();
                }
            }
        }

        // Update cart fields (preserve created_by_type etc)
        $cart->user_id = $student_user_id;
        $cart->receiver_info = $cart->receiver_info ?? [];
        $cart->course_id = $request->course_id;
        // $cart->month = $request->month;
        // $cart->year = $request->year;
        // keep quantity business logic as previous (1) but store students_count
        $cart->quantity = 1;
        $cart->students_count = $studentsCount;
        $cart->created_by_type = 2;
        $cart->is_subscription_enable = $course->is_subscription_enable ?? 0;

        // set stored price values
        $cart->main_price = $totalNet; // total net amount
        $cart->price = $totalNet;
        // store original_price as the UPDATED course original price
        $cart->original_price = $coursePriceTotal; // (course original price * students_count)

        if ($admissionApplicable) {
            $cart->admission_fee = $admissionFeeTotal; // admission total for students
            $cart->discount_type_admission_fee = (float)$request->discount_type_admission_fee;
            $cart->discount_admission_fee = $discountAmountAdmission;
            $cart->net_admission_fee = $netAdmission;
        } else {
            $cart->admission_fee = 0.00;
            $cart->discount_type_admission_fee = 0.00;
            $cart->discount_admission_fee = 0.00;
            $cart->net_admission_fee = 0.00;
        }

        $cart->discount = $discountAmountCourse;
        $cart->discount_type = $request->discount_type_course_price;
        $cart->net_original_price = $netCourse;

        // update requestData if you use later in emails/invoice
        $requestData = [
            'course_unit_price' => $courseUnitPrice,
            'students_count' => $studentsCount,
            'course_price_total' => $coursePriceTotal,
            'discount_course_price' => $discountAmountCourse,
            'discount_type_course_price' => (float)$request->discount_type_course_price,
            'net_course_price' => $netCourse,
            'admission_fee_per_unit' => $admissionFeePerUnit,
            'admission_fee_total' => $admissionFeeTotal,
            'discount_admission_fee' => $discountAmountAdmission,
            'discount_type_admission_fee' => (float)$request->discount_type_admission_fee,
            'net_admission_fee' => $netAdmission,
            'net_price' => $totalNet,
        ];

        // Save cart updates now (this assigns updated id/updated_at)
        $cart->save();

        // Generate PDF invoice (overwrite existing invoice file if desired)
        $TIME = time();
        $pdfFilePath = public_path('invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf');

        $mpdf = new Mpdf([
            'tempDir' => public_path('mpdf-temp'),
            'default_font' => 'dejavusans',
        ]);

        // prepare fullData like in storePayment
        $fullData = [];
        $fullData['year'] = $cart->year ?: date('Y', strtotime($cart->created_at));
        $fullData['id'] = $cart->id;
        $fullData['s_name'] = $studentUser->name;
        $fullData['s_gender'] = $student->gender;
        $fullData['s_parent_name'] = $student->parent_name;
        $fullData['payment_date'] = $cart->created_at;
        $fullData['month'] = $cart->month ?: date('F', strtotime($cart->created_at));
        $fullData['course_name'] = $course->title;
        $getCurrency = Currency::where('current_currency','on')->first();
        $fullData['currency'] = $getCurrency ? $getCurrency->symbol : 'Rs';

        $fullData['students_count'] = $studentsCount;
        $fullData['amount'] = $totalNet; // total paid
        $fullData['course_price_1'] = (float)$coursePriceTotal;
        $fullData['discount'] = $discountAmountCourse;
        $fullData['discount_type'] = (float)$request->discount_type_course_price;
        $fullData['net_course_price_1'] = $netCourse;
        $fullData['admission_fee'] = (float)$admissionFeeTotal;
        $fullData['discount_admission_fee'] = $discountAmountAdmission;
        $fullData['discount_type_admission_fee'] = (float)$request->discount_type_admission_fee;
        $fullData['net_admission_fee'] = $netAdmission;

        $html = view('emails.invoice-challan', ["data" => $fullData])->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

        $relativePath = 'invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf';
        $cart->invoice_pdf_path = $relativePath;
        $cart->save();

        // email and invoice creation (reuse existing behavior)
        $studentCourse = $course;
        $userEmail = $studentUser->email;
        $template_name = 'Payment-create-student';
        $checkoutLink = route('student.checkout', ['type' => 1, 'location' => 1]);
        $viewData = [
            '{{USER_NAME}}' => $studentUser->name,
            '{{COURSE_NAME}}' => $studentCourse->title,
            '{{COURSE_FEE}}' => (int)$totalNet,
            '{{CHECKOUT}}' => $checkoutLink,
        ];

        $emailResponse = sendEmailNotification($viewData, $userEmail, $template_name);

        if ($emailResponse) {
            $this->showToastrMessage('success', __('Challan Payment updated and email is being sent to student.'));
        } else {
            $this->showToastrMessage('success', __('Challan Payment updated but email is not sent to student due to email server issue.'));
        }

        DB::commit();
        return redirect()->route('student.view',['uuid' => $student->uuid]);

    } catch (\Throwable $th) {
        DB::rollBack();
        \Log::error('updatePayment error: '.$th->getMessage().' on '.$th->getFile().'#'.$th->getLine());
        $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
        return redirect()->back();
    }
}



// public function updatePayment(Request $request , $id)
// {
//     $request->validate([
//         'student_id' => 'required|integer', 
//         'course_id' => 'required|integer', 
//         'price' => 'required|integer', 
//         'month' => 'required', 
//         'year' => 'required', 
//     ]);
  
//     DB::beginTransaction();
//     try {
//         $student_id = (int)$request->student_id;
//         $student = Student::where('id',$student_id)->first();
//         if($student){
//             $student_user_id = (int)$student->user_id;
//         }
//         else{
//             DB::rollBack();
//             $this->showToastrMessage('warning', __('Given Student Does Not Exists.'));
//             return redirect()->back();
//         }
//         if ($request->course_id) {
      
//             $check = CartManagement::whereUserId($student_user_id)->whereCourseId($request->course_id)
//             ->where('created_by_type',2)
//             ->where('month',(int)$request->month)
//             ->where('year',(int)$request->year)
//             ->where('id','<>',$id)->first();
//              if($check){
//                 DB::rollBack();
//                 $this->showToastrMessage('warning', __("Payment already added for current course within current month and year!"));
//                 return redirect()->back();
//              }
        
//             $courseExits = Course::find($request->course_id);
//             if (!$courseExits) {
//                 DB::rollBack();
//                 $this->showToastrMessage('warning', __("Given course not found!"));
//                 return redirect()->back();
//             }
//         }

//         //Start:: Cart Management
//         $cartExists = null;
//         if ($request->course_id) {
          
//             // $cartExists = CartManagement::whereUserId($student_user_id)->whereCourseId($request->course_id)->first();
//             $cartExists = CartManagement::whereUserId($student_user_id)->whereCourseId($request->course_id)
//             ->where('created_by_type',2)
//             ->where('id','<>',$id)->first();
//         }
//         if ($cartExists) {
//             DB::rollBack();
//             $this->showToastrMessage('warning', __("Course Payment Already Added in Pending List!"));
//             return redirect()->back();
//         }
      
//         $quantity = 1;
//         $cart = CartManagement::where('id',$id)->first();

//         if(is_null($cart)){
//             $cart = new CartManagement();
//         }else{
//             $quantity = ($request->quantity ?? 0) + $cart->quantity;
//         }
     
//         $cart->user_id = $student_user_id;
//         $cart->receiver_info = [];
//         $cart->course_id = $request->course_id;
//         $cart->month = $request->month;
//         $cart->year = $request->year;
//         $cart->quantity = $quantity ?? 0;
//         $cart->created_by_type = 2;
//         if($request->course_id){
//             $course = Course::findOrFail($request->course_id);
//             $cart->is_subscription_enable = $course->is_subscription_enable;
//         }
//         if ($request->course_id) {
//             $cart->main_price = $request->price;
//             $cart->price = $request->price;
//         }
//         $cart->update();
//         $this->showToastrMessage('success', __('Student Payment updated successfully.'));
//         DB::commit();
//         return redirect()->route('student_payment.pending');
//         dd($quantity);
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         $this->showToastrMessage('warning', __('Something is wrong! Try again.'));
//         return redirect()->back();
//     }
// }


public function deletePayment($id)
{
    $cart = CartManagement::where('id',$id)->first();
    if ($cart){
        CartManagement::find($id)->delete();
        $this->showToastrMessage('success', __('Deleted Successfully'));
        return redirect()->back();
    }
    else{
        $this->showToastrMessage('error', __('Given Payment Not Found.'));
        return redirect()->back();
    }  
}




public function deletePaymentEnrollment($id)
{
    $enrollment = Enrollment::where('id',$id)->first();
    if ($enrollment){
        Enrollment::find($id)->delete();
        $this->showToastrMessage('success', __('Deleted Successfully'));
        return redirect()->back();
    }
    else{
        $this->showToastrMessage('error', __('Given Enrollment Not Found.'));
        return redirect()->back();
    }  
}





public function deleteDonationPayment($id)
{
    $cart = CartManagement::where('id',$id)->first();
    if ($cart){
        if(!$cart->is_donation){
            $this->showToastrMessage('error', __('Given Payment is not a donation.'));
            return redirect()->back();
        }
        CartManagement::find($id)->delete();
        $this->showToastrMessage('success', __('Donation Payment Deleted Successfully'));
        return redirect()->back();
    }
    else{
        $this->showToastrMessage('error', __('Given Donation Payment Not Found.'));
        return redirect()->back();
    }  
}






public function deleteCompletedPayment($order_id)
{
    $affiliate_history = AffiliateHistory::where('order_id',$order_id)->get();
    if(!$affiliate_history->isEmpty()){
        foreach ($affiliate_history as $key => $value) {
            $value->delete();
        }
    }
    $orderBillingAddresses = Order_billing_address::where('order_id',$order_id)->get();
    if(!$orderBillingAddresses->isEmpty()){
        foreach ($orderBillingAddresses as $key => $value) {
            $value->delete();
        }
    }
    $orderItems = Order_item::where('order_id',$order_id)->get();
    if(!$orderItems->isEmpty()){
        foreach ($orderItems as $key => $value) {
            $value->delete();
        }
    }
    $payment = Order::where('id',$order_id)->first();
    if ($payment){
        Order::find($order_id)->delete();
        $this->showToastrMessage('success', __('Paid Payment And Its Related Records Deleted Successfully'));
        return redirect()->back();
    }
    else{
        $this->showToastrMessage('error', __('Given Paid Payment Not Found.'));
        return redirect()->back();
    }  
}




     public function freeClassList()
    {
        $data['title'] = 'All New Enrollments';
        $data['isFree'] = 1;
        // $data['freeClasses'] = TrialClass::whereHas('course')->whereHas('student')->orderBy('id','DESC')->paginate(25);

        $data['freeClasses'] = TrialClass::whereHas('course')
        ->whereHas('student')
        ->whereDoesntHave('student.user.enrollments', function ($q) {
            $q->whereColumn('enrollments.course_id', 'trial_classes.course_id');
        })
        ->with(['student.user', 'course'])
        ->orderBy('id', 'DESC')
        ->paginate(25);

        return view('admin.student.freeClassList', $data);
    }




    public function downloadVoucher($id)
{
    $trial = TrialClass::findOrFail($id);
   
    if (!$trial->invoice_pdf_path || !file_exists(public_path($trial->invoice_pdf_path))) {
        return redirect()->back()->with('error', 'Invoice file not found.');
    }

    $filePath = public_path($trial->invoice_pdf_path);
    $fileName = basename($filePath);
    
    return response()->download($filePath, $fileName, [
        'Content-Type' => 'application/pdf',
    ]);
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
    // 1) Show approve form
    public function showApproveForm($id)
    {
        $data['title'] = 'Approve Enrollment Request';
        $trial = TrialClass::with('course', 'student')->findOrFail($id);
        $data['trial'] = $trial;
        $course = $trial->course;
        $data['course'] = $course;
        $student = $trial->student;
        $data['student'] = $student;

        // Use course->admission_fee if exists, otherwise default to 0
        $admissionFee = get_option('admission_fee') ?? 0;
        $data['admissionFee'] = $admissionFee;

        return view('admin.trial.approve-form',$data);
    }


     // 2) Submit approve form
    public function submitApproveForm(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',

            'students_count' => 'nullable|integer|min:1',

            // Course discount validation
            'discount_course_price' => 'nullable|numeric|min:0',
            'discount_type_course_price' => 'required|in:0,1',
            'net_course_price' => 'required|numeric|min:0',

            // Admission discount validation
            'discount_admission_fee' => 'nullable|numeric|min:0',
            'discount_type_admission_fee' => 'required|in:0,1',
            'net_admission_fee' => 'required|numeric|min:0',

            // Total
            'net_price' => 'required|numeric|min:0',
        ]);
     
        DB::beginTransaction();
        try {
            $trial = TrialClass::with('course', 'student')->findOrFail($id);
         
            $course = $trial->course;
            $student = $trial->student;
             
            $studentsCount = (int)($request->students_count ?? 1);
            if ($studentsCount < 1) $studentsCount = 1;

            $requestData = [
                'course_price' => $request->course_price,
                'discount_course_price' => $request->discount_course_price,
                'discount_type_course_price' => $request->discount_type_course_price,
                'net_course_price' => $request->net_course_price,
                'admission_fee' => $request->admission_fee,
                'discount_admission_fee' => $request->discount_admission_fee,
                'discount_type_admission_fee' => $request->discount_type_admission_fee,
                'net_admission_fee' => $request->net_admission_fee,
                'net_price' => $request->net_price,
                'students_count' => $studentsCount,
            ];
               
            if (!$student) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Student not found.');
            }

            $user = User::find($student->user_id);
            if (!$user) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Student user not found.');
            }

           

            

            $result = live_course_checkout($course,$student, $trial->id,$requestData);
          
            if(isset($result['is_true'])){
               $checkoutLink  = route('student.checkout',['type'=>1,'location' => 1]);
            }
            else{
               $checkoutLink  = null;
            }
            
            if($checkoutLink){
                $getStudent = Student::where('id',$trial->student_id)->first();
                if($getStudent){
                    $userID = $getStudent->user_id;
                }
                else{
                    $userID = 0;
                }
                $getUser = User::where('id',$userID)->first();
                if($getUser){
                    $TIME = time();
                    $pdfFilePath = public_path('invoices/invoice_'.$trial->id.'_'.$TIME.'.pdf');

                    // Create a new instance of mPDF
                    $mpdf = new Mpdf([
                        'tempDir' => public_path('mpdf-temp'), // ensure writable dir
                        'default_font' => 'dejavusans',
                    ]);

                    $fullData = [];
                    $cart = \App\Models\CartManagement::where('trial_id', $trial->id)->first();
                    if($cart->year){
                        $fullData['year'] = $cart->year;
                    }
                    else{
                        $fullData['year'] = date('Y',strtotime($cart->created_at));
                    }
                    $fullData['id'] = $cart->id;
                    $fullData['s_name'] = $getUser->name;
                    $fullData['s_gender'] = $getStudent->gender;
                    $fullData['s_parent_name'] = $getStudent->parent_name;
                    $fullData['payment_date'] = $cart->created_at;
                    if($cart->month){
                        $fullData['month'] = $cart->month;
                    }
                    else{
                        $fullData['month'] = date('F',strtotime($cart->created_at));
                    }
                    $fullData['course_name'] = $course->title;
                    $getCurrency = Currency::where('current_currency','on')->first();
                    if($getCurrency){
                        $fullData['currency'] = $getCurrency->symbol;
                    }
                    else{
                        $fullData['currency'] = 'Rs';
                    }
                    $fullData['students_count'] = $studentsCount;
                    $fullData['amount'] = $request->net_price;
                    $fullData['course_price_1'] = $request->course_price;
                    $fullData['discount'] = $request->discount_course_price;
                    $fullData['discount_type'] = $request->discount_type_course_price;
                    $fullData['net_course_price_1'] = $request->net_course_price;
                    $fullData['admission_fee'] = $request->admission_fee;
                    $fullData['discount_admission_fee'] = $request->discount_admission_fee;
                    $fullData['discount_type_admission_fee'] = $request->discount_type_admission_fee;
                    $fullData['net_admission_fee'] = $request->net_admission_fee;

                    
                    // Load HTML view once and generate 3 copies
                    // $copies = ['Bank Copy', 'CEF Copy', 'Student Copy'];
                    // $html = '';

                    // foreach ($copies as $copyName) {
                    //     $html .= view('emails.invoice', [
                    //         'student' => $student,
                    //         'course' => $course,
                    //         'data' => $requestData,
                    //         'copy_type' => $copyName, // Pass type to view
                    //     ])->render();
                        
                    //     // Page break between copies (except last one)
                    //     if ($copyName !== end($copies)) {
                    //         $html .= '<pagebreak />';
                    //     }
                    // }

                
                    $html = view('emails.invoice-challan', ["data" => $fullData])->render();
                    // Write content
                    $mpdf->WriteHTML($html);

                    // Save the file
                    $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

                  
                    $attachments = [
                        [
                            'path' => $pdfFilePath,
                            'name' => 'Invoice-'.$course->title.'.pdf',
                            'mime' => 'application/pdf',
                        ],
                    ];
                    $relativePath = 'invoices/invoice_'.$trial->id.'_'.$TIME.'.pdf';

                    // $template_name2 =  'enrollment-request-approved'; 
                    // sendEmailNotification([
                    //     '{{USER}}' => $getUser->name,
                    //     '{{COURSE}}' => $trial->course?->title,
                    //     '{{CHECKOUT}}' => $checkoutLink,
                    // ], $getUser->email, $template_name2,$attachments);  
                     // ✅ Save invoice path in DB
                   
                    $template_name2 = 'Payment-create-student';
                    $viewData = [
                        '{{USER_NAME}}' => $getUser->name,
                        '{{COURSE_NAME}}' => $trial->course?->title,
                        // show total amount paid (totalNet) or per-student? I keep per previous behaviour - pass total
                        '{{COURSE_FEE}}' => (int)$request->net_price,
                        '{{CHECKOUT}}' => $checkoutLink,
                    
                    ];
                
                    $emailResponse = sendEmailNotification($viewData, $getUser->email, $template_name2);

                    $trial->invoice_pdf_path = $relativePath;
                    $trial->approved = 1;
                    $trial->save();
                   
                    // ✅ Also save in cart table if exists
                    $cart = \App\Models\CartManagement::where('trial_id', $trial->id)->first();
                    if ($cart) {
                        $cart->invoice_pdf_path = $relativePath;
                        $cart->save();
                    }

                    DB::commit(); 
                    return redirect()->route('student.free_classes')
                             ->with('success', 'Request Enrollment approved and email with challan and checkout link sent.');
                }
                else{
                    DB::rollBack(); 
                    return redirect()->route('admin.trial.approve.form', $trial->id)
                             ->with('error', 'Student Not Found.');
                }
            }
            else{    
                DB::rollBack(); 
                return redirect()->route('admin.trial.approve.form', $trial->id)
                             ->with('error', 'Something Went Wrong While Generating Checkout Url Link. Please Try Again Later');
            
            }
    

        } catch (\Exception $e) {
            DB::rollBack();
            // log error
            // dd($e);
            \Log::error('Approve submit error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }









   public function generateInvoice(Request $request)
{
      $enrollment_id = $request->enrollment_id;
 
      

        // Use Enrollment records (course_type = 3), active and not from license, with end_date in future
        $payment = Enrollment::with('course')
            ->where('id',$enrollment_id)
            ->where('from_license', 0)
            // ->whereNotNull('trial_id')
            ->where('status', 1)
            ->where('end_date', '>', Carbon::now())
            ->whereHas('course', function ($q) {
                $q->where('course_type', 3);
            })
            ->whereHas('user', function ($q) {
            })
            ->orderBy('id', 'ASC')
            ->first();

        if($payment){
               // Wrap processing for each enrollment in its own transaction
            DB::beginTransaction();
            try {
               
                // Fetch related models
                $trial = TrialClass::find($payment->trial_id);
                $course = Course::find($payment->course_id);
                $userId = $payment->user_id;
                $student = Student::where('user_id', $userId)->first();
                 
        
                  

                if (!$course || !$student) {
                    Log::warning("Generate Invoice Enrollment #{$payment->id} missing related models (trial/course/student). Skipped.");
                    DB::rollBack();
                    return response()->json([
                        'data' => 'error',
                    ]);
                }
              
                // Prepare requestData (admission_fee = 0 for cron run)
                $originalPrice = $payment->original_price ?? 0;
                $discount = $payment->discount ?? 0;
                $discountType = $payment->discount_type ?? 0; // 0 = Amount, 1 = Percentage
                // Calculate net course price based on discount type
                if ($discountType == 1) {
                    // Percentage discount
                    $netCoursePrice = $originalPrice - (($discount / 100) * $originalPrice);
                } else {
                    // Fixed amount discount
                    $netCoursePrice = $originalPrice - $discount;
                }
                
                // Ensure it doesn’t go negative
                if ($netCoursePrice < 0) {
                    $netCoursePrice = 0;
                }
             
                $requestData = [
                    'course_price' => $originalPrice,
                    'discount_course_price' => $discount,
                    'discount_type_course_price' => $discountType,
                    'net_course_price' => $netCoursePrice,
                    'admission_fee' => 0.00,
                    'discount_admission_fee' => 0.00,
                    'discount_type_admission_fee' => 0.00,
                    'net_admission_fee' => 0.00,
                    'net_price' => $netCoursePrice,
                ];

                 
                
          
                $checkPayment = Enrollment::with('course')
                ->where('id',$enrollment_id)
                ->where('from_license', 0)
                // ->whereNotNull('trial_id')
                ->where('status', 1)
                ->where('end_date', '>', Carbon::now())
                ->whereHas('course', function ($q) {
                    $q->where('course_type', 3);
                })
                ->whereHas('user', function ($q) {
                })
                ->where('course_id',$course->id)
                ->where('user_id',$userId)
                ->first();
                
                //   dd($checkPayment);
                if($checkPayment) {
                            $billingService = app(\App\Services\MonthlyFeeBillingService::class);
                            $evaluation = $billingService->evaluate($payment);
                            if (!$evaluation->shouldCreate()) {
                                DB::rollBack();
                                $message = match ($evaluation->reason) {
                                    \App\Services\MonthlyFeeEvaluation::REASON_PAID_ORDER_EXISTS =>
                                        __('A paid monthly payment for this student and course already exists for the current month.'),
                                    \App\Services\MonthlyFeeEvaluation::REASON_ALREADY_INVOICED_THIS_PERIOD =>
                                        __('A monthly invoice was already generated for this student and course this month.'),
                                    default =>
                                        __('A pending monthly payment for this student and course already exists for the current month.'),
                                };

                                return response()->json([
                                    'data' => 'already_exists',
                                    'cart_id' => $evaluation->existingCartId,
                                    'order_id' => $evaluation->existingOrderId,
                                    'reason' => $evaluation->reason,
                                    'message' => $message,
                                ], 200);
                            }

                            // ✅ Ensure unique prefer_date + prefer_time combination
                            if ($trial) {
                                // use trial's date/time
                                $preferDate = Carbon::parse($trial->prefer_date)->startOfDay();
                                $preferTime = $trial->prefer_time;
                            } else {
                                // fallback defaults when no trial exists
                                $preferDate = Carbon::today()->addDay()->startOfDay(); // start from tomorrow
                                $preferTime = '09:00 AM'; // or any default slot you want
                            }


                            // Get today's date (start of day for safe comparison)
                            $today = Carbon::today();

                            // Loop until we find a unique AND future date/time slot
                            while (
                                $preferDate->lessThanOrEqualTo($today) || // ensure date > today
                                TrialClass::where('prefer_date', $preferDate->toDateString())
                                          ->where('prefer_time', $preferTime)
                                          ->exists()
                            ) {
                                $preferDate->addDay(); // Increment date until unique + future
                            }


                           
                            $newTrial = new TrialClass();
                            $newTrial->student_id = $student->id;
                            $newTrial->prefer_date = $preferDate->toDateString();
                            $newTrial->prefer_time = $preferTime;
                            if($trial){
                                $newTrial->course_id = $trial->course_id;
                            }
                            else{
                                $newTrial->course_id = $course->id;
                            }
                            $newTrial->save();
                          
                            $cartExists = CartManagement::whereUserId($userId)->whereCourseId($course->id)
                                            ->where('request_enrollment',1)
                                            ->where('trial_id',$newTrial->id)->first();
                           
                            if (!$cartExists) {
                                $result = live_course_checkout_cron($course, $student, $newTrial->id, $requestData);
                                $checkoutLink = isset($result['is_true']) ? route('student.checkout', ['type' => 1, 'location' => 1]) : null;

                                if (!$checkoutLink) {
                                    Log::warning("Generate Invoice live_course_checkout for Enrollment #{$payment->id} did not return checkout link. Skipped.");
                                    DB::rollBack();
                                    return response()->json([
                                        'data' => 'error',
                                    ]);
                                }

                                  // Find user to send email
                                $getUser = User::find($student->user_id);
                                if (!$getUser) {
                                    Log::warning("Generate Invoice User not found for student (enrollment #{$payment->id}). Skipped.");
                                    DB::rollBack();
                                    
                                    return response()->json([
                                        'data' => 'error',
                                    ]);
                                }

                             
                                if (!file_exists(public_path('invoices'))) {
                                    mkdir(public_path('invoices'), 0755, true);
                                }
                                if (!file_exists(public_path('mpdf-temp'))) {
                                    mkdir(public_path('mpdf-temp'), 0755, true);
                                }
                            
                                $TIME = time();
                                $pdfFilePath = public_path('invoices/invoice_' . $newTrial->id .'_'.$TIME.'.pdf');
                            
                                $mpdf = new Mpdf([
                                    'tempDir' => public_path('mpdf-temp'),
                                    'default_font' => 'dejavusans',
                                ]);

                                $fullData = [];
                                $cart = \App\Models\CartManagement::where('trial_id', $newTrial->id)->first();
                                if($cart->year){
                                    $fullData['year'] = $cart->year;
                                }
                                else{
                                    $fullData['year'] = date('Y',strtotime($cart->created_at));
                                }
                                $fullData['id'] = $cart->id;
                                $fullData['s_name'] = $getUser->name;
                                $fullData['s_gender'] = $student->gender;
                                $fullData['s_parent_name'] = $student->parent_name;
                                $fullData['payment_date'] = $cart->created_at;
                                if($cart->month){
                                    $fullData['month'] = $cart->month;
                                }
                                else{
                                    $fullData['month'] = date('F',strtotime($cart->created_at));
                                }
                                $fullData['course_name'] = $course->title;
                                $getCurrency = Currency::where('current_currency','on')->first();
                                if($getCurrency){
                                    $fullData['currency'] = $getCurrency->symbol;
                                }
                                else{
                                    $fullData['currency'] = 'Rs';
                                }
                               
                                $fullData['amount'] = $netCoursePrice;
                                $fullData['course_price_1'] = $originalPrice;
                                $fullData['discount'] = $discount;
                                $fullData['discount_type'] = $discountType;
                                $fullData['net_course_price_1'] = $netCoursePrice;
                                $fullData['admission_fee'] = 0.00;
                                $fullData['discount_admission_fee'] = 0.00;
                                $fullData['discount_type_admission_fee'] = 0.00;
                                $fullData['net_admission_fee'] = 0.00;


                               
                                $html = view('emails.invoice-challan', ["data" => $fullData])->render();
                                $mpdf->WriteHTML($html);
                                $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);
                            
                                // Save relative path and mark approved on trial
                                $relativePath = 'invoices/invoice_' . $newTrial->id .'_'.$TIME.'.pdf';
                                $newTrial->invoice_pdf_path = $relativePath;
                                $newTrial->approved = 1;
                                $newTrial->save();

                                // Update cart record created by live_course_checkout (if any)
                                $cart = CartManagement::where('trial_id', $newTrial->id)->first();
                                if ($cart) {
                                    $cart->invoice_pdf_path = $relativePath;
                                    $cart->save();
                                }

                                if ($cart) {
                                    app(\App\Services\MonthlyFeeInvoiceEmailService::class)
                                        ->send($cart, null, 'generate', auth()->id());
                                }

                                $payment->invoice_generated_at = Carbon::now();
                                $payment->save();
                                DB::commit();
                                
                                Log::info("Generate Invoice Enrollment #{$payment->id} processed successfully. Invoice saved: {$relativePath}");
                                return response()->json([
                                        'data' => 'success',
                                ]);
                            }
                            else{
                                Log::warning("Generate Invoice Enrollment #{$payment->id} next cart entry already craeted (trial/course/student). Skipped.");
                                return response()->json([
                                    'data' => 'error',
                                ]);
                            }
                }
                else{
                    Log::warning("[ScheduleJob] No Paid Existing Payment Found");
                }
            } catch (\Throwable $ex) {
                DB::rollBack();
                Log::error("[ScheduleJob] Error processing enrollment #{$payment->id}: " . $ex->getMessage());
                // continue with next enrollment
            }
        }
        else{
            return response()->json([
                'data' => 'error',
            ]);
        }
                 dd($payment);

    
        Log::info('[ScheduleJob] processTrialEnrollments finished at '.now());
}

  
    // Resend approval email
    public function resendApprovalEmail(Request $request)
    {
        $trial = TrialClass::findOrFail($request->id);
        if($trial){
            $course = $trial->course;
            $student = $trial->student;
            $cart = \App\Models\CartManagement::where('trial_id', $trial->id)->first();
            if ($cart){
                $requestData = [
                    'course_price' => $cart->original_price,
                    'discount_course_price' => $cart->discount,
                    'discount_type_course_price' => $cart->discount_type,
                    'net_course_price' => $cart->net_original_price,
                    'admission_fee' => $cart->admission_fee,
                    'discount_admission_fee' => $cart->discount_admission_fee,
                    'discount_type_admission_fee' => $cart->discount_type_admission_fee,
                    'net_admission_fee' => $cart->net_admission_fee,
                    'net_price' => $cart->main_price,
                    'students_count' => $cart->students_count,
                ];
              
            }
            else{
                return redirect()->route('student.free_classes')
                             ->with('error', 'Cart Entry Related To Current Request Enrollment Deleted In DB.');
            }
            $result = live_course_checkout($trial->course,$trial->student, $trial->id, $requestData);
            if(isset($result['is_true'])){
                $checkoutLink  = route('student.checkout',['type'=>1,'location' => 1]);
            }
            else{
               $checkoutLink  = null;
            }
            if($checkoutLink){
                    $getStudent = Student::where('id',$trial->student_id)->first();
                    if($getStudent){
                        $userID = $getStudent->user_id;
                    }
                    else{
                        $userID = 0;
                    }
                    $getUser = User::where('id',$userID)->first();
                    if($getUser){
                        $TIME = time();
                        $pdfFilePath = public_path('invoices/invoice_'.$trial->id.'_'.$TIME.'.pdf');

                        // Create a new instance of mPDF
                        $mpdf = new Mpdf([
                            'tempDir' => public_path('mpdf-temp'), // ensure writable dir
                            'default_font' => 'dejavusans',
                        ]);

                        // Load HTML view once and generate 3 copies
                        $copies = ['Bank Copy', 'CEF Copy', 'Student Copy'];
                        $html = '';

                 
                        $fullData = [];
                        if($cart->year){
                            $fullData['year'] = $cart->year;
                        }
                        else{
                            $fullData['year'] = date('Y',strtotime($cart->created_at));
                        }
                        $fullData['id'] = $cart->id;
                        $fullData['s_name'] = $getUser->name;
                        $fullData['s_gender'] = $getStudent->gender;
                        $fullData['s_parent_name'] = $getStudent->parent_name;
                        $fullData['payment_date'] = $cart->created_at;
                        if($cart->month){
                            $fullData['month'] = $cart->month;
                        }
                        else{
                            $fullData['month'] = date('F',strtotime($cart->created_at));
                        }
                        $fullData['course_name'] = $course->title;
                        $getCurrency = Currency::where('current_currency','on')->first();
                        if($getCurrency){
                            $fullData['currency'] = $getCurrency->symbol;
                        }
                        else{
                            $fullData['currency'] = 'Rs';
                        }
                        $fullData['students_count'] = $cart->students_count;
                        $fullData['amount'] = $cart->main_price;
                        $fullData['course_price_1'] = $cart->original_price;
                        $fullData['discount'] = $cart->discount;
                        $fullData['discount_type'] = $cart->discount_type;
                        $fullData['net_course_price_1'] = $cart->net_original_price;
                        $fullData['admission_fee'] = $cart->admission_fee;
                        $fullData['discount_admission_fee'] = $cart->discount_admission_fee;
                        $fullData['discount_type_admission_fee'] = $cart->discount_type_admission_fee;
                        $fullData['net_admission_fee'] = $cart->net_admission_fee;
                     
                       
                     
                        // foreach ($copies as $copyName) {
                        //     $html .= view('emails.invoice', [
                        //         'student' => $student,
                        //         'course' => $course,
                        //         'data' => $requestData,
                        //         'copy_type' => $copyName, // Pass type to view
                        //     ])->render();
                            
                        //     // Page break between copies (except last one)
                        //     if ($copyName !== end($copies)) {
                        //         $html .= '<pagebreak />';
                        //     }
                        // }

                        $html = view('emails.invoice-challan', ["data" => $fullData])->render();
                        // Write content
                        $mpdf->WriteHTML($html);

                        // Save the file
                        $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

                    
                        $attachments = [
                            [
                                'path' => $pdfFilePath,
                                'name' => 'Invoice-'.$course->title.'.pdf',
                                'mime' => 'application/pdf',
                            ],
                        ];
                        // $template_name2 =  'enrollment-request-approved'; 
                        // sendEmailNotification([
                        //     '{{USER}}' => $getUser->name,
                        //     '{{COURSE}}' => $trial->course?->title,
                        //     '{{CHECKOUT}}' => $checkoutLink,
                        // ], $getUser->email, $template_name2,$attachments);  

                        $template_name2 = 'Payment-create-student';
                        $viewData = [
                            '{{USER_NAME}}' => $getUser->name,
                            '{{COURSE_NAME}}' => $trial->course?->title,
                            // show total amount paid (totalNet) or per-student? I keep per previous behaviour - pass total
                            '{{COURSE_FEE}}' => (int)$cart->main_price,
                            '{{CHECKOUT}}' => $checkoutLink,
                        
                        ];
                
                    $emailResponse = sendEmailNotification($viewData, $getUser->email, $template_name2);
                        $relativePath = 'invoices/invoice_'.$trial->id.'_'.$TIME.'.pdf';
                        $trial->invoice_pdf_path = $relativePath;
                        $trial->approved = 1;
                        $trial->save();
                        
                        // ✅ Also save in cart table if exists
                        $cart = \App\Models\CartManagement::where('trial_id', $trial->id)->first();
                        if ($cart) {
                            $cart->invoice_pdf_path = $relativePath;
                            $cart->save();
                        }
                        // $trial->approved = 1;
                        // $trial->save();
                      
                        return response()->json(['success' => true, 'message' => 'Request checkout email resend successfully.']);
                    }
                    else{
                        return response()->json(['success' => false, 'message' => 'Student Not Found.']);
                    }
            }
            else{
                return response()->json(['success' => false, 'message' => 'Something Went Wrong While Resending Checkout Url Link. Please Try Again Later']);
            }
        }
        else{
            return response()->json(['success' => false, 'message' => 'Given Request Enrollment Not Found.']);
        }
        if (!$trial->approved) {
            return response()->json(['success' => false, 'message' => 'Cannot resend: enrollment not approved yet.'], 400);
        }
    }



      // Approve the trial enrollment
    public function approve(Request $request)
    {
        DB::beginTransaction();  // Start transaction to ensure atomicity
        $trial = TrialClass::findOrFail($request->id);
        if($trial){
            $result = live_course_checkout($trial->course,$trial->student, $trial->id);
             if(isset($result['is_true'])){
                $checkoutLink  = route('student.checkout',['type'=>1,'location' => 1]);
             }
             else{
                $checkoutLink  = null;
             }
             if($checkoutLink){
                    $getStudent = Student::where('id',$trial->student_id)->first();
                    if($getStudent){
                        $userID = $getStudent->user_id;
                    }
                    else{
                        $userID = 0;
                    }
                    $getUser = User::where('id',$userID)->first();
                    if($getUser){
                        $template_name2 =  'enrollment-request-approved'; 
                        sendEmailNotification([
                            '{{USER}}' => $getUser->name,
                            '{{COURSE}}' => $trial->course?->title,
                            '{{CHECKOUT}}' => $checkoutLink,
                        ], $getUser->email, $template_name2);  
                        $trial->approved = 1;
                        $trial->save();
                        DB::commit(); 
                        return response()->json(['success' => true, 'message' => 'Request Approved and checkout email sent.']);
                    }
                    else{
                        DB::rollBack(); 
                        return response()->json(['success' => false, 'message' => 'Student Not Found.']);
                    }
             }
             else{
                DB::rollBack(); 
                return response()->json(['success' => false, 'message' => 'Something Went Wrong While Generating Checkout Url Link. Please Try Again Later']);
             }
        }
        else{
            DB::rollBack(); 
            return response()->json(['success' => false, 'message' => 'Given Request Enrollment Not Found.']);
        }

       

       
    
    }



    public function pending_list()
    {
        
        $data['title'] = 'Pending Student';
        $data['students'] = Student::where('status', STATUS_PENDING)->orderBy('id', 'DESC')->paginate(25);
        return view('admin.student.pending_list', $data);

    }

    public function create()
    {
        $data['title'] = 'Add Student';
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id')) {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id')) {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }
        $data['allCourses'] = Course::where('course_type',3)->where('status',1)->get();
        return view('admin.student.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:2'],
            'area_code' => 'required',
            'phone_number' => 'bail|numeric|unique:users,mobile_number',
            'address' => 'required',
            'gender' => 'required',
            'is_featured' => 'required',
            'about_me' => 'required',
            // 'image' => 'mimes:jpeg,png,jpg|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300|max:1024'
            'image' => 'required|mimes:jpeg,png,jpg|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300|max:1024'
        ]);

       
       

        $user = new User();
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->mobile_number = $request->phone_number;
        $user->phone_number = $request->phone_number;
        $user->email_verified_at = now();
        $user->password = Hash::make($request->password);
        $user->role = 3;
        $user->image =  $request->image ? $this->saveImage('user', $request->image, null, null) :   null;
        $user->og_image = $user->image;
        $user->save();
     
        $update_course_ids_array = array();
        $courses = $request->course;
        if(!empty($courses)){
            $netCount = 0;
            $totalCount = count($courses);
            foreach ($courses as $key => $course_id) {
                $checkExists = Enrollment::where('user_id',$user->id)->where('course_id',$course_id)->first();
                if($checkExists){
                    $netCount++;
                }
                else{
                    $update_course_ids_array[] = $course_id;
                }
            }
            if($netCount == $totalCount){
                $this->showToastrMessage('warning', __('Selected Courses Are Already Enrolled'));
                return redirect()->back();
            }
        }
        $update_course_ids_string = implode(',',$update_course_ids_array);

      
        $student_data = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'phone_number' => $user->phone_number,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'gender' => $request->gender,
            'is_featured' => $request->is_featured,
            'about_me' => $request->about_me,
            'postal_code' => $request->postal_code,
        ];

        $this->studentModel->create($student_data);

  
        if(!empty($update_course_ids_array)){
            foreach ($update_course_ids_array as $key => $courseID) {
                $enrollment = Enrollment::where('user_id',$user->id)->where('course_id',$courseID)->orderBy('id','DESC')->first();
                if($enrollment){
                    $enrollment->order_id = 0;
                    $enrollment->user_id = $user->id;
                    $enrollment->course_type = 2;
                    $enrollment->course_id = $courseID;
                    $enrollment->start_date = date("Y-m-d H:i:s");
                    $enrollment->save();
                }   
                else{
                    $enrollment = new Enrollment();
                    $enrollment->order_id = 0;
                    $enrollment->user_id = $user->id;
                    $enrollment->course_type = 2;
                    $enrollment->course_id = $courseID;
                    $enrollment->start_date = date("Y-m-d H:i:s");
                    $enrollment->save();
                    Log::info("NEW ENROLLMENT BEING CREATED IN (store) IN ADMIN/STUDENTCONTROLLER AT ".Carbon::now());
                    Log::info("NEW ENROLLMENT DETAILS IN (store) IN ADMIN/STUDENTCONTROLLER:- ".$enrollment);
                }
             
            }
        }
     
    

        $getStudent = Student::where('user_id',$user->id)->first();
        // $getStudent->enrollment_id = $enrollment->id;
        $getStudent->enrollments =    $update_course_ids_string;
        $getStudent->update();

        
        $userEmail = $user->email;
        $template_name = 'student-sign-up';
        $viewData = [
            '{{USER_NAME}}' => $user->name,
            '{{EMAIL}}' => $userEmail,
            '{{PASSWORD}}' => $request->password,
        ];
       
        sendEmailNotification($viewData,$userEmail,$template_name);

        $this->showToastrMessage('success', __('Student created successfully'));
        return redirect()->route('student.index');
    }

    public function view($uuid , $trial_id = null)
    {
     
        if($trial_id){
            $getTrialClass = TrialClass::where('id',$trial_id)->first();
            $data['getTrialClass'] = $getTrialClass;
        }
        else{
            $data['getTrialClass'] = null; 
        }

        $data['title'] = 'Student Profile';
        $data['student'] = $this->studentModel->getRecordByUuid($uuid);
        // $data['enrollments'] = Enrollment::where('user_id', $data['student']->user_id)->whereNotNull('course_id')->latest()->paginate();


          $dataEnrollments = Enrollment::where('user_id', $data['student']->user_id)
        ->where('from_license',0)
        ->whereNotNull('course_id')
        ->whereHas('course', function ($q) {
                $q->where('course_type', 3);
        })
        ->latest()->get();

        // dd($dataEnrollments);
        $enrollmentCourseIDs = [];
        foreach ($dataEnrollments as $key0 => $value) {
            $enrollmentCourseIDs[] = $value->course_id;
        }
        $enrollmentCourseIDs = array_unique($enrollmentCourseIDs);
       
        foreach ($enrollmentCourseIDs as $key1 => $courseID) {
            $getCourseEnrollmentInfo = Enrollment::where('user_id', $data['student']->user_id)->where('course_id',$courseID)->orderBy('id','ASC')->get();
            $getCourseEnrollmentCount = $getCourseEnrollmentInfo->count();
           
         
            if($getCourseEnrollmentCount > 1){
                foreach ($getCourseEnrollmentInfo as $keyInfo => $courseEnrollmentInfo) {
                    // if($keyInfo+1 != $getCourseEnrollmentCount){
                        if($courseEnrollmentInfo->main_price != null){
                            if($courseEnrollmentInfo->original_price == null){
                                $courseEnrollmentInfo->delete();
                            }
                        }
                        else if($courseEnrollmentInfo->main_price == null && $courseEnrollmentInfo->price == null && $courseEnrollmentInfo->original_price == null){
                            
                            $courseEnrollmentInfo->delete();
                        }
                    // }
                  
                }
            }
        }
      


        $check = Enrollment::where('user_id', $data['student']->user_id)
        ->where('from_license',0)
        ->whereNotNull('course_id')
        ->get();

        if(!$check->isEmpty()){
            foreach ($check as $key => $studentEnrollment) {
             
                if(checkIfExpired($studentEnrollment)){
                    if(checkIfLifetime($studentEnrollment->end_date)){

                    }
                }
                else{    //Expired
                    $studentEnrollment->status = 0;
                    $studentEnrollment->save(); 
                }
              
            }
        }


        $data['enrollments'] = Enrollment::where('user_id', $data['student']->user_id)
        ->where('from_license',0)
        ->whereNotNull('course_id')
        ->latest()->paginate();



      
        // Example: list of payments/invoices for the student (replace with your real model)
       $query = CartManagement::query(); // Assuming you have these relationships
        $data['pendingPayments'] = $query
        ->whereIn('is_monthly_cron',[1])
        ->whereIn('is_donation',[0])
        ->whereNotNull('trial_id')
        ->where('user_id', $data['student']->user_id)
        ->orderBy('id', 'DESC')->latest()->paginate();
      

        $paidQuery = Order_item::query(); // Assuming you have these relationships
        $data['paidPayments'] = $paidQuery
        ->whereHas('order', function ($paidQuery2) {
            $paidQuery2->where('payment_status','paid');
        })
        ->whereNotNull('trial_id')
        ->where('user_id', $data['student']->user_id)
        ->whereIn('is_monthly_cron',[1])
        ->latest()->paginate();

        return view('admin.student.view', $data);
    }




     public function view2($uuid , $trial_id = null)
    {
     
        if($trial_id){
            $getTrialClass = TrialClass::where('id',$trial_id)->first();
            $data['getTrialClass'] = $getTrialClass;
        }
        else{
            $data['getTrialClass'] = null; 
        }

        $data['title'] = 'Student Profile';
        $data['student'] = $this->studentModel->getRecordByUuid($uuid);
        // $data['enrollments'] = Enrollment::where('user_id', $data['student']->user_id)->whereNotNull('course_id')->latest()->paginate();

        $dataEnrollments = Enrollment::where('user_id', $data['student']->user_id)
        ->where('from_license',0)
        ->whereNotNull('course_id')
        ->whereHas('course', function ($q) {
                $q->where('course_type', 3);
        })
        ->latest()->get();

        // dd($dataEnrollments);
        $enrollmentCourseIDs = [];
        foreach ($dataEnrollments as $key0 => $value) {
            $enrollmentCourseIDs[] = $value->course_id;
        }
        $enrollmentCourseIDs = array_unique($enrollmentCourseIDs);
       
        foreach ($enrollmentCourseIDs as $key1 => $courseID) {
            $getCourseEnrollmentInfo = Enrollment::where('user_id', $data['student']->user_id)->where('course_id',$courseID)->orderBy('id','ASC')->get();
            $getCourseEnrollmentCount = $getCourseEnrollmentInfo->count();
           
         
            if($getCourseEnrollmentCount > 1){
                foreach ($getCourseEnrollmentInfo as $keyInfo => $courseEnrollmentInfo) {
                    // if($keyInfo+1 != $getCourseEnrollmentCount){
                        if($courseEnrollmentInfo->main_price != null){
                            if($courseEnrollmentInfo->original_price == null){
                                $courseEnrollmentInfo->delete();
                            }
                        }
                        else if($courseEnrollmentInfo->main_price == null && $courseEnrollmentInfo->price == null && $courseEnrollmentInfo->original_price == null){
                            
                            $courseEnrollmentInfo->delete();
                        }
                    // }
                  
                }
            }
        }
      


        $check = Enrollment::where('user_id', $data['student']->user_id)
        ->where('from_license',0)
        ->whereNotNull('course_id')
        ->get();

        if(!$check->isEmpty()){
            foreach ($check as $key => $studentEnrollment) {
             
                if(checkIfExpired($studentEnrollment)){
                    if(checkIfLifetime($studentEnrollment->end_date)){

                    }
                }
                else{    //Expired
                    $studentEnrollment->status = 0;
                    $studentEnrollment->save(); 
                }
              
            }
        }


        $data['enrollments'] = Enrollment::where('user_id', $data['student']->user_id)
        ->where('from_license',0)
        ->whereNotNull('course_id')
        ->latest()->paginate();



      
        // Example: list of payments/invoices for the student (replace with your real model)
       $query = CartManagement::query(); // Assuming you have these relationships
        $data['pendingPayments'] = $query
        ->whereIn('is_monthly_cron',[1])
        ->whereIn('is_donation',[0])
        ->whereNotNull('trial_id')
        ->where('user_id', $data['student']->user_id)
        ->orderBy('id', 'DESC')->latest()->paginate();
      

        $paidQuery = Order_item::query(); // Assuming you have these relationships
        $data['paidPayments'] = $paidQuery
        ->whereHas('order', function ($paidQuery2) {
            $paidQuery2->where('payment_status','paid');
        })
        ->whereNotNull('trial_id')
        ->where('user_id', $data['student']->user_id)
        ->whereIn('is_monthly_cron',[1])
        ->latest()->paginate();

        return view('admin.student.view', $data);
    }



    public function edit($uuid)
    {
        $data['title'] = 'Edit Student';
        $getStudent = $this->studentModel->getRecordByUuid($uuid);
        $data['student'] = $this->studentModel->getRecordByUuid($uuid);
        $enrollmentIDsString = $getStudent->enrollments;
        $enrollmentIDs = array();
        // $getEnrollments =  Enrollment::where('user_id', $data['student']->user_id)
        // ->where('course_type',2)->get();

        $getEnrollments =  Enrollment::where('user_id', $data['student']->user_id)
        ->whereHas('course', function ($q) {
                $q->where('course_type', 3);
        })
        ->where('status', 1)
        ->where('from_license', 0)
        ->get();

        if(!$getEnrollments->isEmpty()){
            foreach ($getEnrollments as $key => $value) {
                $enrollmentIDs[] = $value->course_id;
            }
        }
        else{
        if($enrollmentIDsString && $enrollmentIDsString != ''){
            $enrollmentIDs = explode(',',$enrollmentIDsString);
        }
        else{
            $enrollmentIDs = array();
        }
        }
     
     
        $data['enrollmentIDs'] = $enrollmentIDs;
      
        $data['user'] = User::findOrfail($data['student']->user_id);

        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id'))
        {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id'))
        {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }
      $data['allCourses'] = Course::where('course_type',3)->where('status',1)->get();
      
      $timezones_array = timezones_array();
      $data['timeZonesArray'] = $timezones_array;

       
        $activeEnrollments = Enrollment::with('course')
            ->where('user_id', $getStudent->user_id)
            ->where('from_license', 0)
            ->where('status', 1)
            ->where('end_date', '>', Carbon::now())
            ->whereHas('course', function ($q) {
                $q->where('course_type', 3);
            })
            ->orderBy('id','DESC')
            ->get();
     



        $data['activeEnrollments'] = $activeEnrollments;


    return view('admin.student.edit', $data);
    }

    public function update(Request $request, $uuid)
    {
        $student = $this->studentModel->getRecordByUuid($uuid);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$student->user_id],
            'area_code' => 'required',
            'is_featured' => 'required',
            'time_zone' => 'required',
            // 'phone_number' => 'bail|numeric|unique:users,mobile_number,'.$student->user_id,
            'phone_number' => 'bail|string|unique:users,mobile_number,'.$student->user_id,
            'address' => 'required',
            'gender' => 'required',
            'about_me' => 'required',
            'image' => 'nullable|image',
        ]);


        $user = User::findOrfail($student->user_id);
        if (User::where('id', '!=', $student->user_id)->where('email', $request->email)->count() > 0) {
            $this->showToastrMessage('warning', __('Email already exist'));
            return redirect()->back();
        }

        $update_course_ids_string = null;
        $courses = $request->course;
        if(!empty($courses)){
            $update_course_ids_string = implode(',',$courses); 
        }
      
      
    
  
   
  
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        if ($request->password){
            $request->validate([
                'password' => 'required|string|min:6'
            ]);
            $user->password = Hash::make($request->password);
        }
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->time_zone =  $request->time_zone;
        $user->mobile_number = $request->phone_number;
        $user->phone_number = $request->phone_number;
        $user->image =  $request->image ? $this->saveImage('user', $request->image, null, null) :   $user->image;
        if($user->og_image == null){
            $user->og_image = $user->image;
        }
        else{  
        }
        $user->save();

        $student_data = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'phone_number' => $user->phone_number,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'is_featured' => $request->is_featured,
            'gender' => $request->gender,
            'about_me' => $request->about_me,
            'postal_code' => $request->postal_code,
        ];

        $this->studentModel->updateByUuid($student_data, $uuid);

     
        // if(!empty($courses)){
        //     $checkExisting = Enrollment::where('user_id',$user->id)->where('course_type',2)->get();
        //     if(!$checkExisting->isEmpty()){
        //     $deleteExisting = Enrollment::where('user_id',$user->id)->where('course_type',2)->delete();
        //     }
        //     foreach ($courses as $key => $courseID) {
        //         $enrollment = new Enrollment();
        //         $enrollment->order_id = 0;
        //         $enrollment->user_id = $user->id;
        //         $enrollment->course_type = 2;
        //         $enrollment->course_id = $courseID;
        //         $enrollment->start_date = date("Y-m-d H:i:s");
        //         $enrollment->save();
        //     }
        // }
    
     
        $getStudent = Student::where('user_id',$user->id)->first();
        // $getStudent->enrollment_id = $enrollment->id;
        $getStudent->enrollments = $update_course_ids_string;
        $getStudent->update();

    


        // === NOW: handle discount updates for active enrollments ===
        // Expect arrays with keys = enrollment IDs, ex: discount_course_price[123] => '10', discount_type_course_price[123] => '1', etc.
        $discountsCourse = $request->input('discount_course_price', []); // raw entered (amount or percent)
        $discountTypesCourse = $request->input('discount_type_course_price', []);
        $netCoursePrices = $request->input('net_course_price', []); // sent by JS hidden (but we recalc server-side too)
        $coursePrices = $request->input('course_price', []); // original course price per enrollment id
        
        $studentsCount = $request->input('students_count', []); // raw entered (amount or percent)
        $discountsAdmission = $request->input('discount_admission_fee', []);
        $discountTypesAdmission = $request->input('discount_type_admission_fee', []);
        $netAdmissionFees = $request->input('net_admission_fee', []);
        $admissionFees = $request->input('admission_fee', []);

        // dd($studentsCount);
        // Defensive: If the inputs were submitted as arrays keyed by enrollment id, process each key
        $enrollmentIds = array_unique(array_merge(
            array_keys($discountsCourse ?? []),
            array_keys($discountsAdmission ?? []),
            array_keys($netCoursePrices ?? []),
            array_keys($netAdmissionFees ?? [])
        ));

        foreach ($enrollmentIds as $enrollId) {
            $STUDENT_COUNT = isset($studentsCount[$enrollId]) ? (int)$studentsCount[$enrollId] : (int)($enrollment->students_count ?? 1);
          
            $enrollment = Enrollment::find($enrollId);
            if (!$enrollment) continue;
       
             // Only update enrollments that match your conditions (extra safety)
            if ($enrollment->from_license != 0) continue;
            if ($enrollment->status != 1) continue;
            if ($enrollment->end_date && Carbon::parse($enrollment->end_date)->lte(Carbon::now())) continue;
            if (!$enrollment->course || $enrollment->course->course_type != 3) continue;

             // Server-side calculation for course discount
            $origPrice = isset($coursePrices[$enrollId]) ? (float)$coursePrices[$enrollId] : (float)($enrollment->original_price ?? 0);
            $rawDiscountCourse = isset($discountsCourse[$enrollId]) ? (float)$discountsCourse[$enrollId] : 0;
            $discountTypeCourse = isset($discountTypesCourse[$enrollId]) ? (int)$discountTypesCourse[$enrollId] : 0;
            $origPrice = $origPrice * $STUDENT_COUNT;
            if ($discountTypeCourse === 1) {
                $discountAmountCourse = ($origPrice * $rawDiscountCourse) / 100;
            } else {
                $discountAmountCourse = $rawDiscountCourse;
            }
            if ($discountAmountCourse > $origPrice) $discountAmountCourse = $origPrice;
            $netCourse = max($origPrice - $discountAmountCourse, 0);

            // Server-side calculation for admission discount
            $origAdmission = isset($admissionFees[$enrollId]) ? (float)$admissionFees[$enrollId] : (float)($enrollment->admission_fee ?? 0);
            $rawDiscountAdmission = isset($discountsAdmission[$enrollId]) ? (float)$discountsAdmission[$enrollId] : 0;
            $discountTypeAdmission = isset($discountTypesAdmission[$enrollId]) ? (int)$discountTypesAdmission[$enrollId] : 0;

            $origAdmission = $origAdmission * $STUDENT_COUNT;
            if ($discountTypeAdmission === 1) {
                $discountAmountAdmission = ($origAdmission * $rawDiscountAdmission) / 100;
            } else {
                $discountAmountAdmission = $rawDiscountAdmission;
            }
            if ($discountAmountAdmission > $origAdmission) $discountAmountAdmission = $origAdmission;
            $netAdmission = max($origAdmission - $discountAmountAdmission, 0);
            $totalNet = $netCourse + $netAdmission;

            // Save into enrollment fields (only fields that exist per your table)
            // Keep a defensive approach: don't overwrite unrelated fields
            $enrollment->original_price = $origPrice; // optional: store original reference
            $enrollment->discount = $rawDiscountCourse;
            $enrollment->discount_type = $discountTypeCourse;
            $enrollment->net_original_price = $netCourse;

            $enrollment->admission_fee = $origAdmission;
            $enrollment->discount_admission_fee = $rawDiscountAdmission;
            $enrollment->discount_type_admission_fee = $discountTypeAdmission;
            $enrollment->net_admission_fee = $netAdmission;
            $enrollment->main_price = $totalNet;
            $enrollment->students_count = $STUDENT_COUNT;
            $enrollment->price = $totalNet;

            // dd($enrollment);
            // optionally set main_price to netCourse if that matches your business rule:
            // $enrollment->main_price = $netCourse;

            $enrollment->save();
           
        }
      
        $this->showToastrMessage('success', __('Updated Successfully'));
        return redirect()->route('student.index');
    }

    public function delete($uuid)
    {
        $student = $this->studentModel->getRecordByUuid($uuid);
        $instructor = Instructor::whereUserId($student->user_id)->first();
        if ($instructor){
            $this->showToastrMessage('error', __('You can`t delete it. Because this user already an instructor. If you want to delete, at first you delete from instructor.'));
            return redirect()->back();
        }
        if ($student){
            $this->deleteFile(@$student->user->image);
        }
        TrialClass::where('student_id',$student->id)->delete();
        $student_class_details = ClassDetail::where('student_id',$student->id)->get();
        if (count($student_class_details) > 0)
        {
            $classIDs = array();
            foreach ($student_class_details as $key => $class_detail) {
               $classIDs[] = $class_detail->class_id;
               $class_detail->delete();
            }
            $classIDs = array_unique($classIDs);
            if(!empty($classIDs)){
                foreach ($classIDs as $key => $classID) {
                    $getClass = Lesson::where('id',$classID)->first();
                    if($getClass){
                        $getClass->delete();
                    }
                }
              
            }
        }
        User::find($student->user_id)->delete();
        $this->studentModel->deleteByUuid($uuid);

        $this->showToastrMessage('success', __('Deleted Successfully'));
        return redirect()->back();
    }

    public function changeStudentStatus(Request $request)
    {
        $student = Student::findOrFail($request->id);
        $student->status = $request->status;
        $student->save();

        return response()->json([
            'data' => 'success',
        ]);
    }
   
    public function changeEnrollmentStatus(Request $request)
    {
        $enrollment = Enrollment::findOrFail($request->id);
        $checkCourse = Course::findorFail($enrollment->course_id);
        $checkUser = User::findorFail($enrollment->user_id);
     
     
        if($request->status == 1){
            $enrollment->start_date = now();
            $enrollment->end_date = ($checkCourse->access_period) ? Carbon::now()->addDays($checkCourse->access_period) : MAX_EXPIRED_DATE;
            $getStudent = Student::where('user_id',$checkUser->id)->first();
            if($getStudent){
                $getStudent->status = 1;
                $getStudent->save();
            }
         
            
        }
        else{
            // $enrollment->start_date = Carbon::createFromFormat(
            //     'Y-m-d H:i:s',
            //     '2024-10-26 13:44:34'
            // );
            // $enrollment->end_date = Carbon::createFromFormat(
            //     'Y-m-d H:i:s',
            //     '2025-10-26 13:42:51'
            // );
        }
        $enrollment->status = $request->status;

        // dd($enrollment);
        $enrollment->save();

        return response()->json([
            'data' => 'success',
        ]);
    }
}
