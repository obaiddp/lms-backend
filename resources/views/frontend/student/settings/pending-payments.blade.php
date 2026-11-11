@extends('frontend.layouts.app')

@section('navbar-header')
{{__('Pending Payments')}}
@endsection

@section('content2')
<div>
<section class="student-profile-page">
    <div>
        <div class="student-profile-page-content">
            <div class="row">
                <div class="col-12">
                    <div class="row bg-white">
    <div class="col-lg-12 p-0">
        <div class="student-profile-right-part">
        
        <input type="hidden" name="base_url" id="base_url" value="{{$base_url}}">
        {{-- <input type="hidden" id="base_url" value="{{url('/')}}"> --}}
            <div class="row">
                <div class="col-12">
                    @if(!$getAllPendingPayments->isEmpty())
                        <div class="table-responsive table-responsive-xl courses-table">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">{{ __('ID') }}</th>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Course') }}</th>
                                    <th scope="col">{{ __('Amount') }}</th>
                                    <th scope="col">{{ __('Issue Date') }}</th>
                                    <th scope="col">{{ __('Issue Month') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0;
                                    @endphp
                                @foreach($getAllPendingPayments as $payment)
                                    <tr>
                                        <td>{{ ++$i}}</td>
                                        <td>
                                          {{
                                            $payment->user?->name
                                          }}
                                        </td>
                                        <td>
                                        {{
                                            $payment->course?->title
                                          }}
                                        </td>
                                        <td>
                                            {{get_currency_symbol()}}

                                            {{
                                                $payment->price
                                              }}
                                        </td>
                                     
                                              <td>
                                                {{
                                                    date('d-m-Y',strtotime($payment->created_at))
                                                  }}
                                            </td>
                                            <td>
                                                @php
                                                $monthName = \Carbon\Carbon::create()->month($payment->month)->format('F');
                                                $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                                            @endphp
                                                {{
                                                   $shortMonthName
                                                  }}
                                            </td>
                                            <td>
                                                {{-- class="btn-action mr-30" --}}
                                                <a target="_blank" href="{{route('student.checkout_payment', ['type' => $payment->created_by_type , 'cartid' => $payment->id])}}" class="theme-btn theme-button1 w-100 justify-content-center mt-3"  title="Edit Details">
                                                    {{-- <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit"> --}}
                                                    {{ __("Checkout") }}
                                                </a>
                                                {{-- <input type="submit" class="theme-btn theme-button1 w-100 justify-content-center mt-3"
                                                value="{{ __("Proceed to Checkout") }}" name="proceed_to_checkout"> --}}
                                            </td>
                                        {{-- <td>{{$individual->status }}</td> --}}
                                  
                                     
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="courses-cards">
                            @php
                                $i = 0;
                            @endphp
                            @foreach($getAllPendingPayments as $payment)
                                <div class="course-card">
                                    
                                    <div class="font-15 color-heading">
                                        <strong>Name:</strong>
                                        {{
                                        $payment->user?->name
                                        }}
                                    </div>
                                    <div class="font-15 color-heading">
                                        <strong>Course:</strong>
                                    {{
                                        $payment->course?->title
                                        }}
                                    </div>
                                    <div class="font-15 color-heading"  >
                                        <strong>Amount:</strong>
                                        {{get_currency_symbol()}}

                                        {{
                                            $payment->price
                                            }}
                                    </div>
                                    <div class="font-15 color-heading">
                                        <strong>Issue Date:</strong>
                                            {{
                                                date('d-m-Y',strtotime($payment->created_at))
                                                }}
                                    </div>
                                    <div class="font-15 color-heading">
                                        <strong>Issue Month:</strong>
                                            @php
                                            $monthName = \Carbon\Carbon::create()->month($payment->month)->format('F');
                                            $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                                        @endphp
                                            {{
                                                $shortMonthName
                                                }}
                                    </div>
                                    <div class="font-15 color-heading">
                                            {{-- class="btn-action mr-30" --}}
                                            <a target="_blank" href="{{route('student.checkout_payment', ['type' => $payment->created_by_type , 'cartid' => $payment->id])}}" class="theme-btn theme-button1 w-100 justify-content-center mt-3"  title="Edit Details">
                                                {{-- <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit"> --}}
                                                {{ __("Checkout") }}
                                            </a>
                                            {{-- <input type="submit" class="theme-btn theme-button1 w-100 justify-content-center mt-3"
                                            value="{{ __("Proceed to Checkout") }}" name="proceed_to_checkout"> --}}
                                    </div>
                                    {{-- <td>{{$individual->status }}</td> --}}
                                </div>
                            @endforeach
                        </div>
                        <!-- Pagination Start -->
                        @if(@$getAllPendingPayments->hasPages())
                            {{ @$getAllPendingPayments->links('frontend.paginate.paginate') }}
                        @endif
                        <!-- Pagination End -->
                    @else
                        <!-- If there is no data Show Empty Design Start -->
                        <div class="empty-data">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                            <h5 class="my-3">{{ __('No Pending Payments Available') }}</h5>
                        </div>
                        <!-- If there is no data Show Empty Design End -->
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
</div>
</div>
</div>
</div>
</section>
  </div>
@endsection
  <script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
  <script>

var base_url = $('#base_url').val();
$(document).on("click", ".create_zoom_meeting_link", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 userId = $(this).attr("data-user_id");
 lessonId = $(this).attr("data-lesson_id");

 if(userId == null){
    userId = 'NewUser_'+ (new Date()).getTime();
 }
 userName = $(this).attr("data-user_name");
 if(userName == null){
    userName = 'New_Student';
 }
 var res = userName.replace(/\s+/g, "");
//  alert(res)
 userType = $(this).attr("data-user_type");
 var murl = base_url+'/'+'student/NewZoomMeeting/'+meetingId+'/'+userType+'/'+res+'/'+userId+'/'+lessonId;
 var currentHref = murl;
 window.open(currentHref, '_blank');
});






        function ajaxCallfirst1() 
    {
      var base_url = window.location.origin;
    	$.ajax({
            url: base_url+"/student/checkLessonStartTime",
            dataType: "json",
            success: (function (result) {
                if(result.l_id.length > 0)
                {
                	var lesson_id = JSON.parse(result.l_id);
                  $('#lesson_id').val(lesson_id);                       
              
                    if(result.learning_tool == 'Zoom'){
                    $('#lec_link1_'+lesson_id).attr('href', result.url); 
                  $('#lec_link1_'+lesson_id).attr('target', '_blank'); 
                  $('#lec_link1_'+lesson_id).html("Start Class"); 
                  $('#lec_link1_'+lesson_id).css("background-color", "#499619"); 
                  $('#lec_link1_'+lesson_id).css("color", "#fff"); 
                  $('#lec_link1_'+lesson_id).attr('data-lesson_id', lesson_id); 
                  $('#lec_link1_'+lesson_id).attr('data-meeting_id', result.meeting_id); 
                  $('#lec_link1_'+lesson_id).attr('data-user_name', result.user_name); 
                  $('#lec_link1_'+lesson_id).attr('data-user_id', result.user_id); 
                  $('#lec_link1_'+lesson_id).attr('data-user_type', result.user_type); 
                  $('#lec_link1_'+lesson_id).addClass('create_zoom_meeting_link');
                    }
                 
                    IsmeetingCreated = $('#lec_link1_'+lesson_id).attr('data-meeting_created'); 
                    if(IsmeetingCreated != 'Yes'){
                        var audioElement = document.createElement('audio');
                         audioElement.setAttribute('src', base_url+'/public/assets/notification_sound/Cool_Notification_2.mp3');
                            // audioElement.play();
                    }
                    $('#lec_link1_'+lesson_id).attr('data-meeting_created', 'Yes'); 

                
            }
            })
        })
    }

ajaxCallfirst1(); // To output when the page loads
setInterval(ajaxCallfirst1, (50 * 1000)); // x * 1000 to get it in seconds
</script>



@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
    <script src="{{asset('frontend/assets/js/custom/student-profile.js')}}"></script>
@endpush

