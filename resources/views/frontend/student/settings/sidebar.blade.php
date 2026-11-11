<div class="col-lg-3 p-0">
    <div class="student-profile-left-part">
        <h6>{{ @Auth::user()->name }}</h6>
        <ul>
            <li><a href="{{ route('student.profile') }}" class="font-medium font-15 {{active_if_full_match('student/profile')}}">{{__('Profile')}}</a></li>
            <li><a href="{{ route('student.address') }}" class="font-medium font-15 {{active_if_full_match('student/address')}}">{{__('Address & Location')}}</a></li>
            <li><a href="{{ route('student.change-password') }}" class="font-medium font-15 {{active_if_full_match('student/change-password')}}">{{__('Change Password')}}</a></li>
            <li><a href="{{ route('student.my-learning') }}" class="font-medium font-15 {{active_if_full_match('student/my-learning')}}">{{__('My Courses')}}</a></li>
            <li><a href="{{ route('lms_product.student.purchase_list') }}" class="font-medium font-15 {{active_if_full_match('student/purchase-list')}}">{{__('My Products')}}</a></li>
            <li><a href="{{ route('student.class-schedule.index') }}" class="font-medium font-15 {{active_if_full_match('student/class-schedule')}}">{{__('Class Schedule')}}</a></li>
            <li><a href="{{ route('student.class-history.index') }}" class="font-medium font-15 {{active_if_full_match('student/class-history')}}">{{__('Class History')}}</a></li>
            <li><a href="{{ route('student.pending-payments.index') }}" class="font-medium font-15 {{active_if_full_match('student/pending-payments')}}">{{__('Pending Payments')}}</a></li>
        </ul>
    </div>
</div>
