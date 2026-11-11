<div class="sidebar__area">
    <div class="sidebar__close">
        <button class="close-btn">
            <i class="fa fa-close"></i>
        </button>
    </div>

    <div class="sidebar__brand">
        @canany(['manage_dashboard','all_dashboard'])
        <a href="{{ route('admin.dashboard') }}">
            @if(get_option('app_logo') != '')
                <img src="{{getImageFile(get_option('app_logo'))}}" alt="">
            @else
                <img src="" alt="">
            @endif
        </a>
        @endcanany
    </div>
    <ul id="sidebar-menu" class="sidebar__menu">

        @canany(['manage_dashboard','all_dashboard'])
        <li class=" {{ active_if_full_match('admin/dashboard') }} ">
            <a href="{{route('admin.dashboard')}}">
                <span class="iconify" data-icon="bxs:dashboard"></span>
                <span>{{__('Dashboard')}}</span>
            </a>
        </li>
         @endcanany
        @canany(['manage_course','add_course', 'pending_course', 'hold_course', 'approved_course', 'all_course','manage_course_reference', 'manage_course_category', 'manage_course_subcategory', 'manage_course_tag', 'manage_course_language', 'manage_course_difficulty_level'])
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="dashicons:welcome-learn-more"></span>
                    <span>{{__('Courses')}}</span>
                </a>
                <ul>
                @can('all_course')
                    <li class="{{ active_if_full_match('admin/course') }}">
                        <a href="{{route('admin.course.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('All')}}</span>
                        </a>
                    </li>
                @endcan
                @can('add_course')
                    <li class="{{ active_if_match('admin/course/add-course') }}">
                        <a target="_blank" href="{{route('admin.course.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Add Self Learning')}}</span>
                        </a>
                    </li>
                @endcan
                @can('add_course')
                    <li class="{{ active_if_match('admin/course/add-course-standard') }}">
                        <a  href="{{route('admin.course.create_standard')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Add Live')}}</span>
                        </a>
                    </li>
                @endcan
                @can('manage_course')
                <li>
                    <a href="{{ route('admin.course.orders') }}" class="{{ @$subNavOrderProductActiveClass }}">
                        <i class="fa fa-circle"></i>
                        <span>{{ __('Orders') }}</span>
                    </a>
                </li>
                @endcan
                @can('pending_course')
                    <li class="{{ active_if_match('admin/course/review-pending') }}">
                        <a href="{{route('admin.course.review_pending')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Review Pending')}}</span>
                        </a>
                    </li>
                @endcan
                @can('hold_course')
                    <li class="{{ active_if_match('admin/course/hold') }}">
                        <a href="{{route('admin.course.hold')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Hold')}}</span>
                        </a>
                    </li>
                @endcan
                @can('approved_course')
                    <li class="{{ active_if_match('admin/course/approved') }}">
                        <a href="{{route('admin.course.approved')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Approved')}}</span>
                        </a>
                    </li>
                @endcan
                @can('pending_course')
                    <li class="{{ active_if_match('admin/course/review-upcoming') }}">
                        <a href="{{route('admin.course.review_upcoming')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Upcoming')}}</span>
                        </a>
                    </li>
                @endcan
                <li class="{{ active_if_full_match('admin/course/enroll') }}">
                    <a href="{{route('admin.course.enroll')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{ __('Enroll') }}</span>
                    </a>
                </li>
                @can('manage_course_category')
                    <li class="{{ active_if_match('admin/category') }}">
                        <a href="{{route('category.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Categories')}}</span>
                        </a>
                    </li>
                @endcan
                @can('manage_course_subcategory')
                    <li class="{{ active_if_match('admin/subcategory') }}">
                        <a href="{{route('subcategory.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Subcategory')}}</span>
                        </a>
                    </li>
                @endcan
                @can('manage_course_tag')
                    <li class="{{ active_if_match('admin/tag') }}">
                        <a href="{{route('tag.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Tags')}}</span>
                        </a>
                    </li>
                @endcan
                @can('manage_course_language')
                    <li class="{{ active_if_match('admin/course-language') }}">
                        <a href="{{route('course-language.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Languages')}}</span>
                        </a>
                    </li>
                @endcan
                @can('manage_course_difficulty_level')
                    <li class="{{ active_if_match('admin/difficulty-level') }}">
                        <a href="{{route('difficulty-level.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Difficulty Levels')}}</span>
                        </a>
                    </li>
                @endcan
                <li class="{{ @$subNavSpecialPromotionIndexActiveClass }}">
                    <a href="{{route('special_promotional_tag.index')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{ __('Promotional Tag') }}</span>
                    </a>
                </li>
                <li class="{{ active_if_match('admin/course-upload-rules') }}">
                    <a href="{{route('course-rules.index')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{ __('Rules & Benefits') }}</span>
                    </a>
                </li>
                </ul>
            </li>
        @endcanany
    
        @can(['product_module_product', 'product_module_tag', 'product_module_category'])
        @if(isAddonInstalled('LMSZAIPRODUCT'))
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="gridicons:product"></span>
                    <span>{{__('Products')}}
                        @if(env('LOGIN_HELP') == 'active')
                        <span class="badge bg-warning">(Addon)</span>
                        @endif
                    </span>
                </a>
                <ul>
                    @can('product_module_product')
                    <li class="{{ @$subNavProductActiveClass }}">
                        <a href="{{ route('lms_product.admin.product.index') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('All')}}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_match('admin/product/pending') }}">
                        <a href="{{ route('lms_product.admin.product.pending') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Pending')}}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_match('admin/product/approved') }}">
                        <a href="{{ route('lms_product.admin.product.approved') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Approved')}}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_match('admin/product/hold') }}">
                        <a href="{{ route('lms_product.admin.product.hold') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Hold')}}</span>
                        </a>
                    </li>

                    @if(isAddonInstalled('LMSZAIPRODUCT'))
                    <li>
                        <a href="{{ route('lms_product.admin.product.dashboard') }}" class="{{@$subNavProductDashboardActiveClass}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                  
                    <li>
                        <a href="{{ route('lms_product.admin.product.orders') }}" class="{{ @$subNavOrderProductActiveClass }}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Orders') }}</span>
                        </a>
                    </li>

                    @endif
                    <li class="{{ active_if_match('admin/product/add') }}">
                        <a href="{{ route('lms_product.admin.product.create',['step'=>"step-1"]) }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Add New')}}</span>
                        </a>
                    </li>
                    {{-- <li class="{{ active_if_match('admin/product/upcoming') }}">
                        <a href="{{route('lms_product.admin.product.upcoming')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Upcoming')}}</span>
                        </a>
                    </li> --}}
                    @endcan
                    @can('product_module_category')
                    <li class="{{ active_if_match('admin/product-category') }}">
                        <a href="{{ route('lms_product.admin.category.index') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Category')}}</span>
                        </a>
                    </li>
                    @endcan
                    @can('product_module_tag')
                    <li class="{{ active_if_match('admin/product-tag') }}">
                        <a href="{{ route('lms_product.admin.tag.index') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Tags')}}</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endif
        @endcanany

        @canany(['manage_instructor', 'pending_instructor', 'approved_instructor', 'all_instructor'])
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                    <span>{{__('Instructors')}}</span>
                </a>
                <ul>
                    @can('all_instructor')
                        <li class="
                        {{ active_if_full_match('admin/instructor') }}
                        {{ active_if_match('admin/instructor/view/*') }}
                    ">
                            <a href="{{route('instructor.index')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('All')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('add_instructor')
                    <li class="{{ active_if_match('admin/instructor/create') }}">
                        <a href="{{route('instructor.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Add New') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('pending_instructor')
                        <li class="{{ active_if_match('admin/instructor/pending') }}">
                            <a href="{{route('instructor.pending')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Pending')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('approved_instructor')
                        <li class="{{ active_if_match('admin/instructor/approved') }}">
                            <a href="{{route('instructor.approved')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Approved')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('approved_instructor')
                        <li class="{{ active_if_match('admin/instructor/blocked') }}">
                            <a href="{{route('instructor.blocked')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Blocked')}}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany


        @canany(['manage_coordinator','add_coordinator', 'blocked_coordinator', 'all_coordinator'])
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                    <span>{{__('Coordinators')}}</span>
                </a>
                <ul>
                @can('all_coordinator')
                        <li class="
                        {{ active_if_full_match('admin/coordinator') }}
                        {{ active_if_match('admin/coordinator/view/*') }}
                    ">
                            <a href="{{route('coordinator.index')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('All')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('add_coordinator')
                    <li class="{{ active_if_match('admin/coordinator/create') }}">
                        <a href="{{route('coordinator.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Add New') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('blocked_coordinator')
                        <li class="{{ active_if_match('admin/coordinator/blocked') }}">
                            <a href="{{route('coordinator.blocked')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Blocked')}}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['manage_resources','manage_resource_categories'])
                <li class=" {{ active_if_full_match('admin/resource') }} ">
                    <a class="has-arrow" href="#">
                        <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                        <span>{{__('Resources')}}</span>
                    </a>
                    <ul>
                        @can('manage_resource_categories')
                        <li class="{{ active_if_match('admin/resource/category/view') }}">
                            <a href="{{route('admin.resource.category.index')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{ __('View Categories') }}</span>
                            </a>
                        </li>
                        {{-- <li class="{{ active_if_match('admin/resource/category/add') }}">
                            <a href="{{route('admin.resource.category.add')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{ __('Add Category') }}</span>
                            </a>
                        </li> --}}
                        @endcan

                        @can('manage_resources')
                        <li class="{{ active_if_match('admin/resource/view') }}">
                            <a href="{{route('admin.resource.index')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{ __('View All') }}</span>
                            </a>
                        </li>
                        <li class="{{ active_if_match('admin/resource/add') }}">
                            <a href="{{route('admin.resource.add')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{ __('Add New') }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
        @endcanany

        @canany(['manage_classes','add_classes', 'all_classes','all_individual','all_group'])
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                    <span>{{__('Classes')}}</span>
                </a>
                <ul>
                    @can('all_classes')

                        <li class="
                        {{ active_if_full_match('admin/classes') }}
                        {{ active_if_match('admin/classes/view_detail/*') }}
                    ">
                            <a href="{{route('classes.index')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('All')}}</span>
                            </a>
                        </li>
                    @endcan

                    @can('all_individual')
                        <li class="
                                {{ active_if_full_match('admin/classes/view_individual_classes') }}
                                {{ active_if_match('admin/classes/view_detail/*') }}
                            ">
                            <a href="{{route('classes.view_individual_classes')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Individual')}}</span>
                            </a>
                        </li>
                    @endcan

                    @can('all_group')

                            <li class="
                            {{ active_if_full_match('admin/classes/view_group_classes') }}
                            {{ active_if_match('admin/classes/view_detail/*') }}
                        ">
                                <a href="{{route('classes.view_group_classes')}}">
                                    <i class="fa fa-circle"></i>
                                    <span>{{__('Group')}}</span>
                                </a>
                        </li>
                    @endcan

                    @can('add_classes')
                        <li class="{{ active_if_match('admin/classes/create_individual') }}">
                            <a href="{{route('classes.create_individual')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{ __('Add Individual') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('add_classes')
                        <li class="{{ active_if_match('admin/classes/create_group') }}">
                            <a href="{{route('classes.create_group')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{ __('Add Group') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('all_individual')
                        <li class="
                                {{ active_if_match('admin/classes/view_individual_classes_history') }}
                            ">
                            <a href="{{route('classes.view_individual_classes_history')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Individual Classes History')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('all_group')
                    <li class="
                            {{ active_if_match('admin/classes/view_group_classes_history') }}
                        ">
                        <a href="{{route('classes.view_group_classes_history')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Group Classes History')}}</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endcanany



    @canany(['manage_time_slot','add_time_slot','all_time_slots'])
        <li>
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                <span>{{__('TimeSlots')}}</span>
            </a>
            <ul>
            @can('all_time_slots')
                    <li class="
                    {{ active_if_full_match('admin/time_slots') }}
                    {{ active_if_match('admin/time_slots/view/*') }}
                ">
                        <a href="{{route('time_slots.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('All')}}</span>
                        </a>
                    </li>
                @endcan
                @can('add_time_slot')
                <li class="{{ active_if_match('admin/time_slots/create') }}">
                    <a href="{{route('time_slots.create')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{ __('Add New') }}</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
    @endcanany




        @canany(['manage_organization', 'pending_organization', 'approved_organization', 'all_organization'])
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                    <span>{{__('Organizations')}}</span>
                </a>
                <ul>
                    @can('all_organization')

                        <li class="
                        {{ active_if_full_match('admin/organizations') }}
                        {{ active_if_match('admin/organizations/view/*') }}
                    ">
                            <a href="{{route('organizations.index')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('All')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('add_organization')
                    <li class="{{ active_if_match('admin/organizations/create') }}">
                        <a href="{{route('organizations.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Add New') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('pending_organization')
                        <li class="{{ active_if_match('admin/organizations/pending') }}">
                            <a href="{{route('organizations.pending')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Pending')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('approved_organization')
                        <li class="{{ active_if_match('admin/organizations/approved') }}">
                            <a href="{{route('organizations.approved')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Approved')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('approved_organization')
                        <li class="{{ active_if_match('admin/organizations/blocked') }}">
                            <a href="{{route('organizations.blocked')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Blocked')}}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('manage_student')
            <li class=" {{ active_if_full_match('admin/student') }} ">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span>{{__('Students')}}</span>
                </a>
                <ul>
                    <li class="{{ active_if_match('admin/student') }}">
                        <a href="{{route('student.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('All') }}</span>
                        </a>
                    </li>
                    @if(get_option('private_mode'))
                    <li class="{{ active_if_match('admin/student/pending') }}">
                        <a href="{{route('student.pending_list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Pending') }}</span>
                        </a>
                    </li>
                    @endif
                    <li class="{{ active_if_match('admin/student/create') }}">
                        <a href="{{route('student.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Add New') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_match('admin/student/free_classes') }}">
                        <a href="{{route('student.free_classes')}}">
                            <i class="fa fa-circle"></i>
                            {{-- <span>{{ __('Free') }}</span> --}}
                            <span>{{ __('New Enrollments') }}</span>
                        </a>
                    </li>
                    @canany(['resend_monthly_fee_invoice', 'manage_student'])
                    <li class="{{ active_if_match('admin/monthly-fee-invoices') }}">
                        <a href="{{ route('admin.monthly-fee-invoices.index') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Fee Invoice Emails') }}</span>
                        </a>
                    </li>
                    @endcanany
                    @canany(['resend_payment_receipt', 'manage_student'])
                    <li class="{{ active_if_match('admin/payment-receipt-emails') }}">
                        <a href="{{ route('admin.payment-receipt-emails.index') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Payment Receipt Emails') }}</span>
                        </a>
                    </li>
                    @endcanany
                    <li class="{{ active_if_match('admin/student_payment/donation') }}">
                        <a href="{{route('student_payment.donation')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Pending Donations') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_match('admin/student_payment/pending') }}">
                        <a href="{{route('student_payment.pending')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Pending Payments') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_match('admin/student_payment/completed_donation') }}">
                        <a href="{{route('student_payment.completed_donation')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Completed Donations') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_match('admin/student_payment/completed') }}">
                        <a href="{{route('student_payment.completed')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Completed Payments') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_match('admin/student_payment/add_payment') }}">
                        <a href="{{route('student_payment.add_payment')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Add Payment') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan




        @canany(['manage_licenses','add_license','all_licenses','delete_license','update_license'])
            <li class=" {{ active_if_full_match('admin/license') }} ">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span>{{__('Licenses')}}</span>
                </a>
                <ul>
                    @can('all_licenses')
                        <li class="{{ active_if_match('admin/license') }}">
                            <a href="{{route('admin.license.index')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{ __('All') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('add_license')
                        <li class="{{ active_if_match('admin/license/create') }}">
                            <a href="{{route('admin.license.create')}}">
                                <i class="fa fa-circle"></i>
                                <span>{{ __('Add New') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany



        @if(get_option('subscription_mode'))
        @can('manage_subscriptions')
            <li class="{{ @$navSubscriptionParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span>{{__('Subscriptions')}}</span>
                </a>
                <ul class="{{ @$navSubscriptionParentShowClass }}">
                    <li class="{{ @$subNavSubscriptionActiveClass }}">
                        <a href="{{route('admin.subscriptions.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('All') }}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->route()->getName() == "admin.subscriptions.create") ? 'mm-active' : '' }}">
                        <a href="{{route('admin.subscriptions.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Add New') }}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->route()->getName() == "admin.subscriptions.purchase_pending_list") ? 'mm-active' : '' }}">
                        <a href="{{route('admin.subscriptions.purchase_pending_list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Sale Pending') }}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->route()->getName() == "admin.subscriptions.purchase_list") ? 'mm-active' : '' }}">
                        <a href="{{route('admin.subscriptions.purchase_list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Sale') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan
        @endif

        @if(get_option('wallet_recharge_system', 0))
        @can('manage_wallet_recharge')
            <li class="{{ @$navWalletParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span>{{__('Wallets')}}</span>
                </a>
                <ul class="{{ @$navWalletParentShowClass }}">
                    <li class="{{ (request()->route()->getName() == "admin.wallet_recharge.pending_list") ? 'mm-active' : '' }}">
                        <a href="{{route('admin.wallet_recharge.pending_list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Recharge Pending') }}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->route()->getName() == "admin.wallet_recharge.list") ? 'mm-active' : '' }}">
                        <a href="{{route('admin.wallet_recharge.list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Recharge List') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan
        @endif

        @if(get_option('saas_mode'))
        @can('manage_saas')
            <li class=" {{ @$navSaasParentActiveClass }} ">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span>{{__('SaaS')}}</span>
                </a>
                <ul class="{{ @$navSaasParentShowClass }}">
                    <li class="{{ @$subNavSaasActiveClass }}">
                        <a href="{{route('admin.saas.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('All ') }}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->route()->getName() == "admin.saas.create") ? 'mm-active' : '' }}">
                        <a href="{{route('admin.saas.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Add New') }}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->route()->getName() == "admin.saas.purchase_pending_list") ? 'mm-active' : '' }}">
                        <a href="{{route('admin.saas.purchase_pending_list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Sale Pending') }}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->route()->getName() == "admin.saas.purchase_list") ? 'mm-active' : '' }}">
                        <a href="{{route('admin.saas.purchase_list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Sale') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan
        @endif

        @can('manage_coupon')
            <li class="{{ @$navCouponActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ri:coupon-3-fill"></span>
                    <span>{{__('Coupons')}}</span>
                </a>
                <ul>
                    <li class="{{ @$subNavCouponIndexActiveClass }}">
                        <a href="{{ route('coupon.index') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('List')}}</span>
                        </a>
                    </li>
                    <li class="{{ @$navCouponAddActiveClass }}">
                        <a href="{{ route('coupon.create') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Add New')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('manage_promotion')
        <li class="{{ @$navPromotionParentActiveClass }}">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="ic:round-discount"></span>
                <span>{{ __('Promotions') }}</span>
            </a>
            <ul>
                <li class="{{ @$subNavPromotionIndexActiveClass }}">
                    <a href="{{ route('promotion.index') }}">
                        <i class="fa fa-circle"></i>
                        <span>{{ __('List') }}</span>
                    </a>
                </li>
                <li class="{{@$subNavAddPromotionActiveClass}}">
                    <a href="{{ route('promotion.create') }}">
                        <i class="fa fa-circle"></i>
                        <span>{{ __('Add New') }}</span>
                    </a>
                </li>
            </ul>
        </li>
        @endcan

        @can('payout')
            <li class="{{ @$navFinanceParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="clarity:dollar-bill-solid"></span>
                    <span>{{__('Payouts')}}</span>
                </a>
                <ul class="{{ @$navFinanceShowClass }}">
                    <li class="{{@$subNavFinanceNewWithdrawActiveClass}}">
                        <a href="{{route('payout.new-withdraw')}}">
                            <i class="fa fa-circle"></i>
                            <span> {{ __('Request') }}</span>
                        </a>
                    </li>

                    <li class="{{@$subNavFinanceCompleteWithdrawActiveClass}}">
                        <a href="{{route('payout.complete-withdraw')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Complete')}}</span>
                        </a>
                    </li>
                    <li class="{{@$subNavFinancerejectedWithdrawActiveClass}}">
                        <a href="{{route('payout.rejected-withdraw')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Rejected')}}</span>
                        </a>
                    </li>
                    <li class="{{@$subNavFinanceDistributeSubscriptionActiveClass}}">
                        <a href="{{route('payout.distribute.subscriptions')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Subscription')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan


        @can('finance')
            <li class="">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="map:finance"></span>
                    <span>{{ __('Financial Reports') }}</span>
                </a>
                <ul>
                    <li class="{{ active_if_full_match('admin/report/course-revenue-report') }}{{ active_if_full_match('admin/report/bundle-revenue-report') }}{{ active_if_full_match('admin/report/consultation-revenue-report') }}">
                        <a href="{{route('course-report.revenue-report')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Revenue') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_full_match('admin/report/order-report') }}">
                        <a href="{{route('report.order-report')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Order') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_full_match('admin/report/order-pending') }}">
                        <a href="{{route('report.order-pending')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Order Pending') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_full_match('admin/report/order-cancelled') }}">
                        <a href="{{route('report.order-cancelled')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Order Cancelled') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_full_match('admin/report/cancel-consultation-list') }}">
                        <a href="{{route('report.cancel-consultation-list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Consultation Cancel') }}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_full_match('admin/report/student-fee-ledger') }}">
                        <a href="{{ route('report.student-fee-ledger') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Student Fee Ledger') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('manage_certificate')
            <li class="{{ @$navCertificateActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="fluent:certificate-20-filled"></span>
                    <span>{{__('Certificates')}}</span>
                </a>
                <ul>
                    <li class="{{ @$subNavAllCertificateActiveClass }}">
                        <a href="{{route('certificate.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('All')}}</span>
                        </a>
                    </li>
                    <li class="{{ @$subNavAddCertificateActiveClass }}">
                        <a href="{{route('certificate.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Add New')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan


        @can('open_ai_setting')
        @if(isAddonInstalled('LMSZAIAI'))
            <li class="{{ @$navWriteAISettingParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="eos-icons:ai"></span>
                    <span>{{__('Write AI Settings')}}
                        @if(env('LOGIN_HELP') == 'active')
                        <span class="badge bg-warning">(Addon)</span>
                        @endif
                    </span>
                </a>
                <ul class="">
                    <li class="{{ @$subNavOpenAISettingsActiveClass }}">
                        <a href="{{ route('ai.open_ai_setting') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Write AI Setting')}}</span>
                        </a>
                    </li>

                    <li class="{{ @$subNavOpenAIContentActiveClass }}">
                        <a href="{{ route('ai.generated_content_list') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Generated Content')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcanany
        @endif

        @can('ranking_level')
            <li class="{{ @$navRankingActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="fa6-solid:ranking-star"></span>
                    <span>{{__('Badges')}}</span>
                </a>
                <ul>
                    <li class="{{ @$subNavRankingActiveClass }}">
                        <a href="{{route('ranking.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Badges')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan
        @can('skill')
            <li class="{{ @$subNavSkillActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="carbon:skill-level-intermediate"></span>
                    <span>{{__('Skills')}}</span>
                </a>
                <ul>
                    <li class="{{ @$subNavSkillActiveClass }}">
                        <a href="{{route('skill.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Skills')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan
        @can('manage_language')
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="fa6-solid:language"></span>
                    <span>{{__('Languages')}}</span>
                </a>
                <ul>
                    <li class="{{ active_if_match('admin/language') }}">
                        <a href="{{route('language.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Settings')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('support_ticket')
            <li class="{{ @$navSupportTicketParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ic:twotone-support-agent"></span>
                    <span>{{__('Support Tickets')}}</span>
                </a>
                <ul class="">
                    <li class="{{ @$subNavSupportTicketIndexActiveClass }}">
                        <a href="{{ route('support-ticket.admin.index') }}">
                            <i class="fa fa-circle"></i>
                            <span> {{__('All')}} </span>
                        </a>
                    </li>
                    <li class="{{ @$subNavSupportTicketOpenActiveClass }}">
                        <a href="{{ route('support-ticket.admin.open') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Open')}}</span>
                        </a>
                    </li>

                </ul>
            </li>
        @endcan

        @can('user_management')
            <li class="{{ @$navUserParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="bxs:user-account"></span>
                    <span>{{__('Admin Management')}}</span>
                </a>
                <ul class="{{ @$navUserParentShowClass }}">
                    <li class="{{ @$subNavUserCreateActiveClass }}">
                        <a href="{{route('user.create')}}">
                            <i class="fa fa-circle"></i>
                            <span> {{__('Add User')}} </span>
                        </a>
                    </li>
                    <li class="{{ @$subNavUserActiveClass }}">
                        <a href="{{route('user.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('All Users')}}</span>
                        </a>
                    </li>
                    <li class="{{ @$subNavUserRoleActiveClass }}">
                        <a href="{{route('role.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Roles')}}</span>
                        </a>
                    </li>


                </ul>
            </li>
        @endcan

        @can('user_management')
            <li class="{{ @$navEmailActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="dashicons:email"></span>
                    <span>{{__('Email Management')}}</span>
                </a>
                <ul class="{{ @$navEmailParentShowClass }}">
                    <li class="{{ @$subNavEmailTemplateActiveClass }}">
                        <a href="{{route('email-template.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Template')}}</span>
                        </a>
                    </li>

                    <li class="{{ @$subNavSendMailActiveClass }}">
                        <a href="{{route('email-template.send-email')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Send')}}</span>
                        </a>
                    </li>

                </ul>
            </li>
        @endcan

        @can('email_notification_template')
            <li class="{{$navEmailNotificationActiveClass ?? ''}}">
                <a href="{{route('email-notification.index')}}">
                    <span class="iconify" data-icon="dashicons:email"></span>
                    <span>{{__('Email Notification')}}</span>
                </a>
            </li>
        @endcan

        @can('page_management')
        <li class="{{ @$navPageParentActiveClass }}">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="dashicons:edit-page"></span>
                <span>{{__('Pages')}}</span>
            </a>
            <ul class="">
                <li class="{{ @$subNavPageIndexActiveClass }}">
                    <a href="{{route('page.index')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{__('All')}}</span>
                    </a>
                </li>
                <li class="{{ @$subNavPageAddActiveClass }}">
                    <a href="{{route('page.create')}}">
                        <i class="fa fa-circle"></i>
                        <span> {{__('Add New')}} </span>
                    </a>
                </li>
            </ul>
        </li>
        @endcan
        @can('menu_management')
        <li class="{{ @$navMenuParentActiveClass }}">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="bi:menu-up"></span>
                <span>{{__('Menus')}}</span>
            </a>
            <ul class="">
                <li class="{{ @$subNavStaticMenuIndexActiveClass }}">
                    <a href="{{route('menu.static')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{__('Nav')}}</span>
                    </a>
                </li>
                <li class="{{ @$subNavDynamicMenuIndexActiveClass }}">
                    <a href="{{route('menu.dynamic')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{__('Nav Dynamic')}}</span>
                    </a>
                </li>
                <li class="{{ @$subNavFooterCompanyMenuIndexActiveClass }}">
                    <a href="{{route('menu.footer-company')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{__('Footer Left')}}</span>
                    </a>
                </li>
                <li class="{{ @$subNavFooterSupportMenuIndexActiveClass }}">
                    <a href="{{route('menu.footer-support')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{__('Footer Right')}}</span>
                    </a>
                </li>

            </ul>
        </li>
        @endcan


        @canany(['application_setting', 'global_setting', 'home_setting', 'mail_configuration', 'payment_option', 'content_setting'])
            <li class="{{ @$navApplicationSettingParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="mdi:application-cog-outline"></span>
                    <span>{{__('Application Settings')}}</span>
                </a>
                <ul class="">
                    @can('global_setting')
                        <li class="{{ @$subNavGlobalSettingsActiveClass }}">
                            <a href="{{ route('settings.general_setting') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Global')}}</span>
                            </a>
                        </li>
                    @endcan


                    <li class="{{ @$subNavLocationSettingsActiveClass }}">
                        <a href="{{ route('settings.location.country.index') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Location')}}</span>
                        </a>
                    </li>

                    @can('home_setting')
                        <li class="{{ @$subNavHomeSettingsActiveClass }}">
                            <a href="{{ route('settings.theme-setting') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Home')}}</span>
                            </a>
                        </li>
                    @endcan

                    @can('mail_configuration')

                        <li class="{{ @$subNavMailConfigSettingsActiveClass }}">
                            <a href="{{ route('settings.mail-configuration') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Mail Configuration')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('payment_option')
                        <li class="{{ @$subNavPaymentOptionsSettingsActiveClass }}">
                            <a href="{{ route('settings.payment_method_settings') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Payment Options')}}</span>
                            </a>
                        </li>
                    @endcan
                    @can('content_setting')
                        <li class="{{ @$subNavInstructorSettingsActiveClass }}">
                            <a href="{{ route('settings.instructor-feature') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Become Instructor')}}</span>
                            </a>
                        </li>

                        <li class="{{ @$subNavFAQSettingsActiveClass }}">
                            <a href="{{ route('settings.faq.cms') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('FAQ')}}</span>
                            </a>
                        </li>
                        <li class="{{ @$subNavSupportSettingsActiveClass }}">
                            <a href="{{ route('settings.support-ticket.cms') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Support Ticket')}}</span>
                            </a>
                        </li>

                        <li class="{{ @$subNavAboutUsSettingsActiveClass }}">
                            <a href="{{ route('settings.about.gallery-area') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('About Us')}}</span>
                            </a>
                        </li>

                        <li class="{{ @$subNavContactUsSettingsActiveClass }}">
                            <a href="{{ route('settings.contact.cms') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Contact Us')}}</span>
                            </a>
                        </li>
                        <li class="{{ @$subNavMaintenanceModeActiveClass }}">
                            <a href="{{ route('settings.maintenance') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Maintenance Mode')}}</span>
                            </a>
                        </li>
                        <li class="{{ @$subNavComingSoonModeSettingsActiveClass }}">
                            <a href="{{ route('settings.coming-soon') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Coming Soon Mode')}}</span>
                            </a>
                        </li>
                        <li class="{{ @$subNavCacheActiveClass }}">
                            <a href="{{ route('settings.cache-settings') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Cache')}}</span>
                            </a>
                        </li>
                        <li class="{{ @$subNavMigrateActiveClass }}">
                            <a href="{{ route('settings.migrate-settings') }}">
                                <i class="fa fa-circle"></i>
                                <span>{{__('Migrate')}}</span>
                            </a>
                        </li>

                    @endcan
                </ul>
            </li>
        @endcanany

        @can('policy_management')
        <li class="{{ @$navPolicyActiveClass }}">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="dashicons:privacy"></span>
                <span>{{__('Policy Settings')}}</span>
            </a>
            <ul>
                <li class="{{ @$subNavTermConditionsActiveClass }}">
                    <a href="{{ route('admin.terms-conditions') }}">
                        <i class="fa fa-circle"></i>
                        <span> {{__('Terms Conditions')}} </span>
                    </a>
                </li>
                <li class="{{ @$subNavPrivacyPolicyActiveClass }}">
                    <a href="{{ route('admin.privacy-policy') }}">
                        <i class="fa fa-circle"></i>
                        <span> {{__('Privacy Policy')}} </span>
                    </a>
                </li>
                <li class="{{ @$subNavRefundPolicyActiveClass }}">
                    <a href="{{ route('admin.refund-policy') }}">
                        <i class="fa fa-circle"></i>
                        <span> {{__('Refund Policy')}} </span>
                    </a>
                </li>
                <li class="{{ @$subNavCookiePolicyActiveClass }}">
                    <a href="{{ route('admin.cookie-policy') }}">
                        <i class="fa fa-circle"></i>
                        <span> {{__('Cookie Policy')}} </span>
                    </a>
                </li>
            </ul>
        </li>
        @endcan

        @can('content_setting')
            <li class="{{ @$navContactUsParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="fluent:contact-card-32-regular"></span>
                    <span>{{__('Contact Us')}}</span>
                </a>
                <ul class="{{ @$navContactUsParentShowClass }}">
                    <li class="{{ @$subNavContactUsIndexActiveClass }}">
                        <a href="{{ route('contact.index') }}">
                            <i class="fa fa-circle"></i>
                            <span> {{__('All')}} </span>
                        </a>
                    </li>
                    <li class="{{ @$subNavContactUsIssueIndexActiveClass }}">
                        <a href="{{ route('contact.issue.index') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('All Issues')}}</span>
                        </a>
                    </li>
                    <li class="{{ @$subNavContactUsIssueAddActiveClass }}">
                        <a href="{{ route('contact.issue.create') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Add Issue')}}</span>
                        </a>
                    </li>

                </ul>
            </li>
        @endcan

        @can('manage_blog')
            <li class="{{ @$navBlogParentActiveClass }}">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="jam:blogger-square"></span>
                    <span>{{__('Blogs')}} </span>
                </a>
                <ul>
                    <li class="{{ active_if_full_match('admin/blog') }} {{ active_if_full_match('admin/blog/edit/*') }}">
                        <a href="{{route('blog.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('All')}}</span>
                        </a>
                    </li>
                    <li class="{{ active_if_full_match('admin/blog/create') }}">
                        <a href="{{route('blog.create')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Add')}}</span>
                        </a>
                    </li>
                    <li class="{{ @$subNavBlogCommentListActiveClass }}">
                        <a href="{{route('blog.blog-comment-list')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{ __('Comment List') }}</span>
                        </a>
                    </li>
                    <li class="{{ @$subNavBlogCategoryIndexActiveClass }}">
                        <a href="{{route('blog.blog-category.index')}}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Category')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('forum_management')
        <li class="{{ @$navForumParentActiveClass }}">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="carbon:forum"></span>
                <span>{{ __('Forums') }}</span>
            </a>
            <ul>
                <li class="{{ @$subNavForumCategoryIndexActiveClass }}">
                    <a href="{{route('admin.forum.category.index')}}">
                        <i class="fa fa-circle"></i>
                        <span>{{ __('Category') }}</span>
                    </a>
                </li>
            </ul>
        </li>
        @endcan

        @can('account_setting')
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="bytesize:settings"></span>
                    <span>{{__('Account Settings')}}</span>
                </a>
                <ul class="{{ @$navUserParentShowClass }}">
                    <li class="{{ active_if_full_match('admin/profile') }}">
                        <a href="{{route('admin.profile')}}">
                            <i class="fa fa-circle"></i>
                            <span> {{__('Profile')}} </span>
                        </a>
                    </li>
                    <li class="{{ active_if_full_match('admin/profile/change-password') }}">
                        <a href="{{ route('admin.change-password') }}">
                            <i class="fa fa-circle"></i>
                            <span>{{__('Change Password')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('manage_affiliate')
        <li class="">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="tabler:affiliate"></span>
                <span>{{__('Affiliates')}}</span>
            </a>
            <ul class="">
                <li class="{{ @$subNavAffiliateManageListActiveClass }}">
                    <a href="{{route('affiliate.affiliate-request-list')}}">
                        <i class="fa fa-circle"></i>
                        <span> {{__('Request List')}} </span>
                    </a>
                </li>
                <li class="{{ active_if_full_match('admin/affiliate/affiliation-settings') }}">
                    <a href="{{ route('affiliate.affiliation-settings') }}">
                        <i class="fa fa-circle"></i>
                        <span>{{__('Settings')}}</span>
                    </a>
                </li>
                <li class="{{ @$subNavAffiliateHistoryActiveClass }}">
                    <a href="{{ route('affiliate.affiliate-history') }}">
                        <i class="fa fa-circle"></i>
                        <span>{{__('History')}}</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif
        @can('manage_version_update')
            <li class="{{ @$subNavVersionUpdateActiveClass }}">
                <a href="{{ route('settings.file-version-update') }}">
                    <i class="fa fa-circle"></i>
                    <span>{{__('Version Update')}}</span>
                </a>
            </li>
        @endif

        <li class="mb-5 text-center">
            <a href="#">
                <span>
                    <h3>{{ __('Software Version') }} {{ get_option('current_version', 2.4) }}</h3>
                </span>
            </a>
        </li>
    </ul>
</div>
