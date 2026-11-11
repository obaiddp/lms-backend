@extends('layouts.admin')

@section('content')
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{__('Student Profile')}}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item"><a href="{{route('instructor.index')}}">{{__('All Instructors')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('Student Profile')}}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-5">
                    <div class="profile__item bg-style">
                        <div class="profile__item__top">
                            <div class="user-img">
                                <img src="{{asset($student->user ? $student->user->image_path  : '')}}" alt="img">
                            </div>
                            <div class="user-text">
                                <h2>{{$student->name}}</h2>
                            </div>
                        </div>
                        <div class="profile__item__content">
                            <h2>{{__('Personal Information')}}</h2>
                            <p>
                                {{$student->about_me}}
                            </p>
                        </div>
                        <ul class="profile__item__list">
                            <li>
                                <div class="list-item">
                                    <h2>{{__('Name')}}:</h2>
                                    <p>{{$student->name}}</p>
                                </div>
                            </li>
                            <li>
                                <div class="list-item">
                                    <h2>{{__('Phone')}}:</h2>
                                    <p>{{$student->phone_number}}</p>
                                </div>
                            </li>
                            <li>
                                <div class="list-item">
                                    <h2>{{__('Email')}}:</h2>
                                    <p>{{$student->user ? $student->user->email : '' }}</p>
                                </div>
                            </li>
                            {{-- <li>
                                <div class="list-item">
                                    <h2>{{ __('Address') }}:</h2>
                                    <p>{{$student->address}} </p>
                                </div>
                            </li> --}}
                            <li>
                                <div class="list-item">
                                    <h2>{{ __('Location') }}:</h2>
                                    {{-- <p>{{$student->city ?  $student->city->name.', ' : ''}}  {{$student->state ?  $student->state->name.', ' : ''}} {{$student->country ? $student->country->country_name : ''}} </p> --}}
                                    <p>{{$student->city ?  $student->city.', ' : ''}}  {{$student->country ? $student->country->country_name : ''}} </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>



                @if ($getTrialClass != null)
                <div class="col-lg-8 col-md-7">
                    <div class="profile__timeline__area bg-style">
                        <div class="item-title">
                            <h2>{{__('Free Class Details')}}

                                @if ($getTrialClass->approved)
                                    @if (!$getTrialClass->status)
                                    @can('add_classes')
                                        <a style="margin-left:5px" class="btn-btn primary mr-30" href="{{route('classes.create_free_class_individual',['id'=>$getTrialClass->id])}}" title="Schedule Free Class">
                                            <span style="color:white; font-size:12px;padding:14px" class="badge bg-success">{{__('Schedule Class')}}</span>
                                        </a>
                                    
                                    @endcan
                                @endcan
                
                            @endif
                            </h2>
                          
                        </div>

                        <div class="profile__item bg-style">
                            <ul class="profile__item__list">
                                <li>
                                    <div class="list-item">
                                        <h2 style="margin-right:5px">{{__('Preferred Date')}}: </h2>
                                        <p> {{date('d-m-Y',strtotime($getTrialClass->prefer_date))}}<br></p>
                                    </div>
                                </li>

                                <li>
                                    <div class="list-item">
                                        <h2 style="margin-right:5px">{{__('Preferred Time')}}: </h2>
                                        <p> {{date('h:i A',strtotime($getTrialClass->prefer_time))}}<br></p>
                                    </div>
                                </li>

                                @if ($getTrialClass->course_id)
                                    <li>
                                        <div class="list-item">
                                            <h2 style="margin-right:5px">{{__('Course')}}: </h2>
                                            <p> {{$getTrialClass->course?->title}}<br></p>
                                        </div>
                                    </li>
                                @endif
                            

                                <li>
                                    <div class="list-item">
                                        <h2 style="margin-right:5px">{{__('No. Of Joined Students')}}: </h2>
                                        <p> {{$student->no_of_joined_students}}<br></p>
                                    </div>
                                </li>


                                <li>
                                    <div class="list-item">
                                        <h2 style="margin-right:5px">{{__('Preferred Teacher')}}: </h2>
                                        <p> {{$student->preferred_teacher}}<br></p>
                                    </div>
                                </li>

                                <li>
                                    <div class="list-item">
                                        <h2 style="margin-right:5px">{{__('Learn About CEF Online')}}: </h2>
                                        <p> {{$student->learn_about_lms}}<br></p>
                                    </div>
                                </li>

                                {{-- <li>
                                    <div class="list-item">
                                        <h2 style="margin-right:5px">{{__('CEF Online Uses')}}: </h2>
                                        @php
                                            if($student->lms_uses){
                                             $uses = json_decode(  $student->lms_uses);
                                             $usesString = implode(',',$uses);
                                            }
                                            else{
                                                $usesString = ''; 
                                            }
                                        @endphp
                                        <p> {{ 
                                            $usesString
                                        }}<br></p>
                                    </div>
                                </li> --}}



                            
                            </ul>
                        </div>

           
                    </div>
                </div>
                @endif

                
                <div class="col-lg-8 col-md-7">
                    <div class="profile__status__area">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="status__item bg-style">
                                    <div class="status-img">
                                        <img src="{{asset('admin/images/status-icon/done.png')}}" alt="icon">
                                    </div>
                                    <div class="status-text">
                                        <h2>{{ studentCoursesCount($student->user_id) }}</h2>
                                        <p>{{ __('Total Enrolled Courses') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profile__timeline__area bg-style">
                        <div class="item-title">
                            <h2>{{__('Enrolled Courses')}}</h2>
                        </div>
                        <div class="profile__table">
                            <table class="table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Enrollment ID')}}</th>
                                    <th>{{__('Course ID')}}</th>
                                    <th>{{__('Image')}}</th>
                                    <th>{{__('Title')}}</th>
                                    <th>{{__('Validity')}}</th>
                                    <th>{{__('Fee')}}</th>
                                    <th>{{__('Admission Fee')}}</th>
                                    <th>{{__('Course Type')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Invoice Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            {{ $enrollment->id }}
                                        </td>
                                        <td>
                                            {{ $enrollment->course_id }}
                                        </td>
                                        <td>
                                            @if(isset($enrollment->course->uuid))
                                                <a target="_blank" href="{{route('admin.course.view', [$enrollment->course->uuid])}}"><img src="{{ getImageFile(@$enrollment->course->image_path) }}" alt="course" class="img-fluid" width="80"></a>
                                            @else
                                                <img src="{{ getImageFile(@$enrollment->course->image_path) }}" alt="course" class="img-fluid" width="80">
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($enrollment->course->uuid))
                                                <span class="data-text"><a target="_blank" href="{{route('admin.course.view', [$enrollment->course->uuid])}}">{{ @$enrollment->course->title }}</a></span>
                                            @else
                                                <span class="data-text">{{ @$enrollment->course->title }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $enrollment->end_date }}
                                        </td>
                                        <td>
                                            @php
                                                if($enrollment->discount_type == 0){
                                                    $discountValue = $enrollment->discount;
                                                }
                                                else if($enrollment->discount_type == 1){
                                                    $temp1 = $enrollment->discount/100;
                                                    $discountValue = $enrollment->original_price * $temp1;
                                                    
                                                }
                                            @endphp
                                            @if ($enrollment->original_price && $enrollment->original_price != 0.00)
                                                Rs. {{number_format($enrollment->net_original_price)}}
                                                @if($discountValue > 0)  (-{{$discountValue}})   @endif
                                            @endif
                                            
                                        </td>
                                        <td>
                                            @php
                                                if($enrollment->discount_type_admission_fee == 0){
                                                    $discountValueAdmission = $enrollment->discount_admission_fee;
                                                }
                                                else if($enrollment->discount_type_admission_fee == 1){
                                                    $temp2 = $enrollment->discount_admission_fee/100;
                                                    $discountValueAdmission = $enrollment->admission_fee * $temp2;
                                                }
                                            @endphp
                                            @if ($enrollment->admission_fee && $enrollment->admission_fee != 0.00)
                                                {{$enrollment->admission_fee}} (-{{$discountValueAdmission}}) ({{$enrollment->net_admission_fee}})
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                if($enrollment->course?->course_type == 3){
                                                    $courseType = 'Live';
                                                }
                                                else{
                                                    $courseType = 'Self Learning';
                                                }
                                            @endphp
                                            {{ $courseType }}
                                        </td>
                                        <td>
                                            {{$enrollment->created_at ? $enrollment->created_at->toDateTimeString() : null}}
                                        </td>

                                        <td>
                                            {{$enrollment->invoice_generated_at ? date('Y-m-d h:i A',strtotime($enrollment->invoice_generated_at)) : null}}
                                        </td>

                                        <td>
                                            <span id="hidden_id" style="display: none">{{$enrollment->id}}</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="{{ ACCESS_PERIOD_ACTIVE }}" @if($enrollment->status == ACCESS_PERIOD_ACTIVE) selected @endif>{{ __('Active') }}</option>
                                                <option value="{{ ACCESS_PERIOD_DEACTIVATE }}" @if($enrollment->status == ACCESS_PERIOD_DEACTIVATE) selected @endif>{{ __('Revoke') }}</option>
                                                @if($enrollment->course?->course_type == 3)
                                                <option value="generate_invoice">{{ __('Generate Invoice') }}</option>
                                                @endif
                                            </select>

                                            @if ($enrollment->course?->course_type == 3  )
                                                @if ($enrollment->main_price != null)
                                                    @if($enrollment->original_price == null)
                                                        <div class="action__buttons">
                                                        <button class="ms-3">
                                                            <span data-formid="delete_row_form_{{$enrollment->id}}" class="deleteItem">
                                                                <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                            </span>
                                                        </button>
                                                    
                                                        <form action="{{route('student_payment.delete_payment_enrollment', $enrollment->id)}}" method="post" id="delete_row_form_{{ $enrollment->id }}">
                                                            {{ method_field('DELETE') }}
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        </form>
                                                        </div>
                                                    @elseif ($enrollment->main_price == 0.00)
                                                        <div class="action__buttons">
                                                        <button class="ms-3">
                                                            <span data-formid="delete_row_form_{{$enrollment->id}}" class="deleteItem">
                                                                <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                            </span>
                                                        </button>
                                                    
                                                        <form action="{{route('student_payment.delete_payment_enrollment', $enrollment->id)}}" method="post" id="delete_row_form_{{ $enrollment->id }}">
                                                            {{ method_field('DELETE') }}
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        </form>
                                                        </div>
                                                    @endif
                                                @elseif($enrollment->main_price == null && $enrollment->price == null && $enrollment->original_price == null)
                                                    <div class="action__buttons">
                                                    <button class="ms-3">
                                                        <span data-formid="delete_row_form_{{$enrollment->id}}" class="deleteItem">
                                                            <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                        </span>
                                                    </button>
                                                
                                                    <form action="{{route('student_payment.delete_payment_enrollment', $enrollment->id)}}" method="post" id="delete_row_form_{{ $enrollment->id }}">
                                                        {{ method_field('DELETE') }}
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    </form>
                                                    </div>
                                                @endif

                                             
                                            @endif
                                           

                                        </td>
                                      
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{@$enrollments->links()}}
                            </div>
                        </div>
                    </div>









                


                </div>
            </div>


            <hr>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                        <!-- NEW: Other Records (Payments / Invoices / Purchases) -->
                        <div class="profile__timeline__area bg-style mt-4">
                            <div class="item-title">
                                <h2>{{ __('Monthly Fee History/ Paid') }}</h2>
                            </div>
                        
                            <div class="profile__table">
                                <table class="table-style">
                                    <thead>
                                        <tr>
                                            <th style="width:100px">{{__('Payment ID')}}</th>
                                            <th style="width:30px">{{__('Course')}}</th>
                                            <th style="width:100px">{{__('Fee')}}</th>
                                            <th style="width:100px">{{__('Admission Fee')}}</th>
                                            <th style="width:100px">{{__('Method')}}</th>
                                            <th style="width:100px">{{__('Status')}}</th>
                                            <th style="width:100px">{{__('Trans ID')}}</th>
                                            {{-- <th style="width:100px">{{__('Order No')}}</th> --}}
                                            <th style="width:100px">{{__('Month')}}</th>
                                            <th style="width:100px">{{__('Year')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paidPayments as $payment)
                                            <tr class="removable-item">
                                                <td style="width:100px">
                                                    {{ "LMS-P-".$payment->order?->id}}
                                                </td>
                                                <td style="width:100px">
                                                    @if ($payment->course)
                                                        <a href="{{route('admin.course.view', [$payment->course->uuid])}}" class="btn-action mr-30" title="View Details">
                                                        {{ $payment->course->title}}
                                                        </a>
                                                    @endif 
                                                </td>
                                                <td>
                                                    @php
                                                        if($payment->discount_type == 0){
                                                            $discountValue = $payment->discount;
                                                        }
                                                        else if($payment->discount_type == 1){
                                                            $temp1 = $payment->discount/100;
                                                            $discountValue = $payment->original_price * $temp1;

                                                        }
                                                    @endphp
                                                    @if ($payment->original_price)
                                                        {{get_currency_symbol()}} {{$payment->original_price}} (-{{$discountValue}}) ({{$payment->net_original_price}})  
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        if($payment->discount_type_admission_fee == 0){
                                                            $discountValueAdmission = $payment->discount_admission_fee;
                                                        }
                                                        else if($payment->discount_type_admission_fee == 1){
                                                            $temp2 = $payment->discount_admission_fee/100;
                                                            $discountValueAdmission = $payment->admission_fee * $temp2;
                                                        }
                                                    @endphp
                                                    @if ($payment->admission_fee)
                                                        {{get_currency_symbol()}} {{$payment->admission_fee}} (-{{$discountValueAdmission}}) ({{$payment->net_admission_fee}})
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        if($payment->order?->payment_method == 'meezan_bank'){
                                                            // $paymentMethod = 'Meezan Bank';
                                                            $paymentMethod = 'meezan_bank';
                                                        }
                                                        else if($payment->order?->payment_method == 'faysal_bank'){
                                                            // $paymentMethod = 'Faysal Bank';
                                                            $paymentMethod = 'faysal_bank';
                                                        }
                                                        else{
                                                            $paymentMethod = $payment->order?->payment_method;
                                                        }
                                                    @endphp
                                                    {{ $paymentMethod}}
                                                </td>
                                                <td>
                                                    <button class="btn btn-success btn-sm" title="Paid">Paid</button>
                                                </td>
                                            
                                                <td style="width:100px">
                                                    {{ $payment->order?->payment_id}}
                                                </td>
                                            
                                            
                                                {{-- <td style="width:100px">
                                                    @php
                                                        if($payment->order?->payment_method == 'meezan_bank'){
                                                            $paymentOrderNo = $payment->order?->meezan_order_id;
                                                        }
                                                        else if($payment->order?->payment_method == 'faysal_bank'){
                                                            $paymentOrderNo = $payment->order?->faysal_order_id;
                                                        }
                                                        else{
                                                            $paymentOrderNo = null;
                                                        }
                                                    @endphp
                                                    {{ $paymentOrderNo}}
                                                </td> --}}
                                            
                                            
                                    
                                                <td>
                                                    @php
                                                        if ($payment->month){
                                                             $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                                                        } 
                                                        else{
                                                            $shortMonthName = date('M',strtotime($payment->created_at));
                                                        }
                                                    @endphp
                                                        {{$shortMonthName}}
                                                </td>

                                                <td>
                                                    @if ($payment->year)
                                                        {{ $payment->year}}
                                                    @else
                                                         {{ date('Y',strtotime($payment->created_at))}}
                                                    @endif
                                                   
                                                </td>

                                                {{-- <td>
                                                    @if ($payment->order)
                                                        <div class="action__buttons">
                                                            <button class="ms-3">
                                                                <span data-formid="delete_row_form_{{$payment->order?->id}}" class="deletePaidPayment">
                                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                                </span>
                                                            </button>
                                                        
                                                            <form action="{{route('student_payment.delete_completed_payment', [$payment->order?->id])}}" method="post" id="delete_row_form_{{ $payment->order?->id }}">
                                                                {{ method_field('DELETE') }}
                                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            </form>
                                                        </div>
                                                    @endif
                                                    
                                                </td> --}}
                                            </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">{{ __('No records found') }}</td>
                                                    </tr>
                                                @endforelse
                                    </tbody>
                                </table>
                            
                                <div class="mt-3">
                                    {{ $paidPayments->links() }}
                                </div>
                            </div>
                        </div>
                        <!-- END NEW -->
                </div>
            </div>


            <hr>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                        <!-- NEW: Other Records (Payments / Invoices / Purchases) -->
                        <div class="profile__timeline__area bg-style mt-4">
                            <div class="item-title">
                                <h2>{{ __('Monthly Fee History/ Pending') }}</h2>
                            </div>
                        
                            <div class="profile__table">
                                <table class="table-style">
                                    <thead>
                                        <tr>
                                            <th>{{__('ID')}}</th>
                                            <th>{{__('Course')}}</th>
                                            <th>{{__('Fee')}}</th>
                                            <th>{{__('Admission Fee')}}</th>
                                            <th>{{__('Status')}}</th>
                                            <th>{{__('Month')}}</th>
                                            <th>{{__('Year')}}</th>
                                            <th>{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @php
                                            $i = 0;
                                        @endphp
                                        @forelse($pendingPayments as $payment)
                                            <tr class="removable-item">
                                                    <td>
                                                        {{ ++$i}}
                                                    </td>
                                              
                                                    <td>
                                                        @if ($payment->course)
                                                            <a href="{{route('admin.course.view', [$payment->course?->uuid])}}" class="btn-action mr-30" title="View Details">
                                                            {{$payment->course->title}}
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            if($payment->discount_type == 0){
                                                                $discountValue = $payment->discount;
                                                            }
                                                            else if($payment->discount_type == 1){
                                                                $temp1 = $payment->discount/100;
                                                                $discountValue = $payment->original_price * $temp1;

                                                            }
                                                        @endphp
                                                        @if ($payment->original_price && $payment->original_price != 0.00)
                                                            {{get_currency_symbol()}} {{$payment->original_price}} (-{{$discountValue}}) ({{$payment->net_original_price}})  
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            if($payment->discount_type_admission_fee == 0){
                                                                $discountValueAdmission = $payment->discount_admission_fee;
                                                            }
                                                            else if($payment->discount_type_admission_fee == 1){
                                                                $temp2 = $payment->discount_admission_fee/100;
                                                                $discountValueAdmission = $payment->admission_fee * $temp2;
                                                            }
                                                        @endphp
                                                        @if ($payment->admission_fee && $payment->admission_fee != 0.00)
                                                            {{get_currency_symbol()}} {{$payment->admission_fee}} (-{{$discountValueAdmission}}) ({{$payment->net_admission_fee}})
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm" title="Unpaid">UnPaid</button>
                                                        {{-- <a style="margin-top: 10px;" href="{{route('student_payment.change_status', [$payment->id])}}" class="btn btn-info btn-sm" title="Change Status" >{{__('Change Status')}} </a> --}}
                                                    </td>
                                                    <td>
                                                        @php
                                                            if ($payment->month){
                                                                 $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                                                            } 
                                                            else{
                                                                $shortMonthName = date('M',strtotime($payment->created_at));
                                                            }
                                                        @endphp
                                                            {{$shortMonthName}}
                                                    </td>
                                                
                                                    <td>
                                                        @if ($payment->year)
                                                            {{ $payment->year}}
                                                        @else
                                                             {{ date('Y',strtotime($payment->created_at))}}
                                                        @endif
                                                    
                                                    </td>
                                                    <td>
                                                        {{-- <div class="action__buttons">
                                                            <a href="{{route('student_payment.edit_payment', [$payment->id])}}" class="btn-action mr-30" title="Edit Details">
                                                                <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                            </a>
                                                            <button class="ms-3">
                                                                <span data-formid="delete_row_form_{{$payment->id}}" class="deleteItem">
                                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                                </span>
                                                            </button>
                                                        
                                                            <form action="{{route('student_payment.delete_payment', [$payment->id])}}" method="post" id="delete_row_form_{{ $payment->id }}">
                                                                {{ method_field('DELETE') }}
                                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            </form>
                                                        
                                                        
                                                        
                                                        
                                                        </div> --}}
                                                        <div class="action__buttons">
                                                               @if($payment->invoice_pdf_path)
                                                                <a href="{{ route('admin.cart.download.voucher', $payment->id) }}"
                                                                   class="btn btn-sm btn-success mt-1" target="_blank">
                                                                    {{ __('Download PDF') }}
                                                                </a>
                                                            @endif


                                                        <a href="{{route('student_payment.edit_payment_3', $payment->id)}}" class="btn-action mr-30" title="Edit Details">
                                                            <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                        </a>
                                                        <button class="ms-3">
                                                            <span data-formid="delete_row_form_{{$payment->id}}" class="deleteItem">
                                                                <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                            </span>
                                                        </button>
                                                    
                                                        <form action="{{route('student_payment.delete_payment', $payment->id)}}" method="post" id="delete_row_form_{{ $payment->id }}">
                                                            {{ method_field('DELETE') }}
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        </form>

                                                        </div>
                                                    </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">{{ __('No records found') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            
                                <div class="mt-3">
                                    {{ $pendingPayments->links() }}
                                </div>
                            </div>
                        </div>
                        <!-- END NEW -->
                </div>
            </div>


         


        </div>
    </div>
    <!-- Page content area end -->
@endsection

@push('script')
<script>
'use strict';

$(".status").change(function () {
    var id = $(this).closest('tr').find('#hidden_id').html();
    var selectedValue = $(this).val();

        console.log(selectedValue);
    // if (selectedValue.startsWith('change_status_')) {
    //     var status_value = selectedValue.replace('change_status_', '');
    //     Swal.fire({
    //         title: "{{ __('Are you sure to change status?') }}",
    //         text: "{{ __('You won`t be able to revert this!') }}",
    //         icon: "warning",
    //         showCancelButton: true,
    //         confirmButtonText: "{{__('Yes, Change it!')}}",
    //         cancelButtonText: "{{__('No, cancel!')}}",
    //         reverseButtons: true
    //     }).then(function (result) {
    //         if (result.value) {
    //             $.ajax({
    //                 type: "POST",
    //                 url: "{{ route('admin.student.changeEnrollmentStatus') }}",
    //                 data: {"status": status_value, "id": id, "_token": "{{ csrf_token() }}"},
    //                 success: function () {
    //                     toastr.options.positionClass = 'toast-bottom-right';
    //                     toastr.success('', '{{ __("Enrollment status has been updated") }}');
    //                 },
    //                 error: function () {
    //                     alert("Error!");
    //                 },
    //             });
    //         }
    //     });
    // }
    if (selectedValue == 1 || selectedValue == 0) {
        Swal.fire({
            title: "{{ __('Are you sure to change status?') }}",
            text: "{{ __('You won`t be able to revert this!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "{{__('Yes, Change it!')}}",
            cancelButtonText: "{{__('No, cancel!')}}",
            reverseButtons: true
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.student.changeEnrollmentStatus') }}",
                    data: {"status": selectedValue, "id": id, "_token": "{{ csrf_token() }}"},
                    success: function () {
                        toastr.options.positionClass = 'toast-bottom-right';
                        toastr.success('', '{{ __("Enrollment status has been updated") }}');
                    },
                    error: function () {
                        alert("Error!");
                    },
                });
            }
        });
    }  
    else if (selectedValue === 'generate_invoice') {
        Swal.fire({
            title: "{{ __('Generate Invoice?') }}",
            text: "{{ __('An invoice will be generated for this enrollment.') }}",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "{{__('Yes, Generate')}}",
            cancelButtonText: "{{__('Cancel')}}",
            reverseButtons: true
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.student.generateInvoice') }}", // 👈 you will define this route later
                    data: {"enrollment_id": id, "_token": "{{ csrf_token() }}"},
                    success: function (response) {
                        toastr.options.positionClass = 'toast-bottom-right';
                        toastr.success('', '{{ __("Invoice generated successfully") }}');
                    },
                    error: function () {
                        toastr.error('', '{{ __("Failed to generate invoice") }}');
                    },
                });
            }
        });
    }
});
</script>
@endpush
