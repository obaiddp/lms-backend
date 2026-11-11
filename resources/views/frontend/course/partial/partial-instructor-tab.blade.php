@if (!$isInstructorHidden)
<div class="instructor-container my-4">
    <h2 class="section-title">Instructors</h2>
    @if (isset($course->course_instructors))
        @foreach ($course->course_instructors->where('status', STATUS_APPROVED) as $course_instructor)
            @php
            if ($course_instructor->user?->is_public === 0 || $course_instructor->user?->is_public === null){   
                continue;
            }
    
            @endphp
             
            @if($course_instructor->user->role != USER_ROLE_ADMIN)
                @php
                    if($course_instructor->user->role == USER_ROLE_INSTRUCTOR){
                        $userType = 'instructor';
                    }
                    else if($course_instructor->user->role == USER_ROLE_ORGANIZATION){
                        $userType = 'organization';
                    }
                    
                    else{
                        $userType = null;
                    }
                @endphp
                @php
                    if($userType){
                    $total_instructor_course =  count($course_instructor->user->$userType->courses);
                    $total_instructor_students = $course_instructor->user->$userType->enrollments->count();
                    }
                    else{
                        $total_instructor_course =  count($course_instructor->user->courses);
                        $total_instructor_students = $course_instructor->user->enrollments->count();   
                    }
                @endphp
                <div class="row">
                    <div class="col-md-3">
                        <a class="instructor-card d-block" href="{{ route('userProfile',$course_instructor->user->id) }}">
                            <div class="instructor-image">
                                <img src="{{ getImageFile(@$course_instructor->user->image_path) }}" alt="{{ @$course_instructor->user->name }}">
                            </div>
                        </a>
                    </div>
                    <div class="col-md-9">
                        <div class="instructor-details">
                            <h3 class="instructor-name2">{{ @$course_instructor->user->name }}</h3>
                            <p class="instructor-title2">{{__('Instructor') }}</p>
                        
                            <div class="instructor-rating">
                                <i class="fas fa-star"></i>
                                <span>{{ number_format(getUserAverageRating($course_instructor->instructor_id), 1) }} {{__('Rating') }}</span>
                            </div>
                            
                            <div class="instructor-stats">
                                <div class="stat">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_125_1165)">
                                        <path d="M15.0625 0.78125H0.9375C0.420625 0.78125 0 1.20187 0 1.71875V11.0312C0 11.5481 0.420625 11.9688 0.9375 11.9688H15.0625C15.5794 11.9688 16 11.5481 16 11.0312V1.71875C16 1.20187 15.5794 0.78125 15.0625 0.78125ZM13 14.2812H10.4688V12.9062H5.53125V14.2812H3C2.74125 14.2812 2.53125 14.4913 2.53125 14.75C2.53125 15.0087 2.74125 15.2188 3 15.2188H13C13.2587 15.2188 13.4688 15.0087 13.4688 14.75C13.4688 14.4913 13.2587 14.2812 13 14.2812Z" fill="#818B9C"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_125_1165">
                                        <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                        </defs>
                                    </svg>
                                    <span>{{ $total_instructor_course }} {{ __('Courses') }}</span>
                                </div>
                                <div class="stat">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.7145 8.00004C6.27968 8.00004 6.83217 7.83243 7.30209 7.51842C7.77202 7.20442 8.13827 6.75811 8.35453 6.23594C8.5708 5.71377 8.62736 5.1392 8.51706 4.58488C8.40676 4.03057 8.13456 3.52141 7.73488 3.1218C7.3352 2.72219 6.826 2.45008 6.27166 2.33988C5.71733 2.22968 5.14277 2.28634 4.62064 2.50269C4.0985 2.71905 3.65226 3.08538 3.33833 3.55536C3.02441 4.02534 2.8569 4.57785 2.857 5.14304C2.85779 5.9006 3.15912 6.6269 3.69485 7.16254C4.23058 7.69817 4.95693 7.99937 5.7145 8.00004ZM9.4525 10.3605C8.72841 9.61783 7.79936 9.10783 6.78402 8.89568C5.76869 8.68353 4.71316 8.77886 3.75226 9.1695C2.79136 9.56013 1.96873 10.2283 1.38943 11.0888C0.81013 11.9492 0.500469 12.9628 0.5 14C0.5 14.1326 0.552678 14.2598 0.646447 14.3536C0.740215 14.4474 0.867392 14.5 1 14.5H10.43C10.5626 14.5 10.6898 14.4474 10.7836 14.3536C10.8773 14.2598 10.93 14.1326 10.93 14C10.9319 13.4698 10.8501 12.9427 10.6875 12.438C10.4454 11.6578 10.0222 10.946 9.4525 10.3605Z" fill="#818B9C"/>
                                        <path d="M11.75 8.25C12.9236 8.25 13.875 7.2986 13.875 6.125C13.875 4.95139 12.9236 4 11.75 4C10.5764 4 9.625 4.95139 9.625 6.125C9.625 7.2986 10.5764 8.25 11.75 8.25Z" fill="#818B9C"/>
                                        <path d="M11.7497 8.83508C11.0795 8.8376 10.4222 9.02013 9.84668 9.36358C9.95518 9.46058 10.0647 9.55608 10.1672 9.66108C10.848 10.3609 11.3537 11.2117 11.6432 12.1441C11.7423 12.4511 11.8154 12.7658 11.8617 13.0851H14.9997C15.1323 13.0851 15.2595 13.0324 15.3532 12.9386C15.447 12.8449 15.4997 12.7177 15.4997 12.5851C15.4986 11.5908 15.1032 10.6376 14.4002 9.9346C13.6971 9.23157 12.7439 8.83614 11.7497 8.83508Z" fill="#818B9C"/>
                                    </svg>
                                    <span>{{ $total_instructor_students }} {{ __('Students') }}</span>
                                </div>
                            </div>
                            
                            <div class="social-links">
                                <a href="#" class="social-icon facebook">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_125_1180)">
                                        <path d="M9.23896 16V8.70218H11.6875L12.0549 5.85725H9.23896V4.04118C9.23896 3.21776 9.46668 2.65661 10.6488 2.65661L12.154 2.65599V0.111384C11.8937 0.0775563 11.0002 0 9.96017 0C7.78849 0 6.30172 1.32557 6.30172 3.75942V5.85725H3.8457V8.70218H6.30172V16H9.23896Z" fill="#013370"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_125_1180">
                                        <rect width="16" height="16" fill="white"/>
                                        </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                                <a href="#" class="social-icon linkedin">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.3996 13.4V8.71198C13.3996 6.40798 12.9036 4.64798 10.2156 4.64798C8.91961 4.64798 8.05561 5.35198 7.70361 6.02398H7.67161V4.85598H5.12761V13.4H7.78361V9.15998C7.78361 8.03998 7.99161 6.96798 9.36761 6.96798C10.7276 6.96798 10.7436 8.23198 10.7436 9.22398V13.384H13.3996V13.4ZM0.807609 4.85598H3.46361V13.4H0.807609V4.85598ZM2.13561 0.599976C1.28761 0.599976 0.599609 1.28798 0.599609 2.13598C0.599609 2.98398 1.28761 3.68798 2.13561 3.68798C2.98361 3.68798 3.67161 2.98398 3.67161 2.13598C3.67161 1.28798 2.98361 0.599976 2.13561 0.599976Z" fill="#0785DF"/>
                                    </svg>
                                </a>
                                <a href="#" class="social-icon twitter">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.4893 6.77491L15.3176 0H13.9365L8.87577 5.88256L4.8338 0H0.171875L6.28412 8.89547L0.171875 16H1.55307L6.8973 9.78782L11.1659 16H15.8278L9.48896 6.77491H9.4893ZM7.59756 8.97384L6.97826 8.08805L2.05073 1.03974H4.17217L8.14874 6.72795L8.76804 7.61374L13.9371 15.0075H11.8157L7.59756 8.97418V8.97384Z" fill="black"/>
                                    </svg>
                                </a>
                                <a href="#" class="social-icon instagram">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.2335 0H4.76649C2.13823 0 0 2.13823 0 4.76649V11.2335C0 13.8618 2.13823 16 4.76649 16H11.2335C13.8618 16 16 13.8618 16 11.2335V4.76649C16 2.13823 13.8617 0 11.2335 0ZM14.3904 11.2335C14.3904 12.977 12.977 14.3904 11.2335 14.3904H4.76649C3.023 14.3904 1.6096 12.977 1.6096 11.2335V4.76649C1.6096 3.02297 3.023 1.6096 4.76649 1.6096H11.2335C12.977 1.6096 14.3904 3.02297 14.3904 4.76649V11.2335Z" fill="#8A02C9"/>
                                    <path d="M8.00047 3.86182C5.71868 3.86182 3.8623 5.7182 3.8623 7.99996C3.8623 10.2817 5.71868 12.1381 8.00047 12.1381C10.2823 12.1381 12.1386 10.2817 12.1386 7.99996C12.1386 5.71817 10.2823 3.86182 8.00047 3.86182ZM8.00047 10.5286C6.60397 10.5286 5.4719 9.39649 5.4719 7.99999C5.4719 6.60348 6.604 5.47142 8.00047 5.47142C9.39698 5.47142 10.529 6.60348 10.529 7.99999C10.529 9.39646 9.39695 10.5286 8.00047 10.5286Z" fill="#8A02C9"/>
                                    <path d="M12.1459 4.88455C12.6935 4.88455 13.1375 4.4406 13.1375 3.89296C13.1375 3.34532 12.6935 2.90137 12.1459 2.90137C11.5982 2.90137 11.1543 3.34532 11.1543 3.89296C11.1543 4.4406 11.5982 4.88455 12.1459 4.88455Z" fill="#8A02C9"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="instructor-bio">
                    <p>{{ @$course_instructor->user->$userType->about_me }}</p>
                </div>

                <br><br>
            @endif
        @endforeach
    @endif
</div>
@endif
