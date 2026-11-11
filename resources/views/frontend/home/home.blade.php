@extends('frontend.layouts.app')
@include('frontend.home.components.meta')

@section('content')
@include('frontend.home.components.header')
@include('frontend.home.components.courses')
@include('frontend.home.components.featured-courses')
@include('frontend.home.components.featured-students')
@include('frontend.home.components.instructors')
@include('frontend.home.components.registration')
@include('frontend.home.components.about')
@include('frontend.home.components.why-cef')
@include('frontend.home.components.products')
@include('frontend.home.components.product-categories')
@include('frontend.home.components.blog')




<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/js/utils.js"></script>

@endsection

@push('style')
<style>
    .error {
        border-color: red;
    }

    .hide {
        display: none;
    }

    .is-invalid {
        border-color: red;
    }

    .is-valid {
        border-color: green;
    }







</style>
@endpush

@push('script')
<!--Hero text effect-->
<script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/addToWishlist.js') }}"></script>
<script src="{{ asset('frontend/assets/js/custom/booking.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>

     $(document).ready(function () {
    
        // Use event delegation: listen on the slider wrapper, act on .swiper-slide
        $('.our-tutors-slider .swiper-wrapper').on('click', '.swiper-slide', function (e) {
        
            const $slide = $(this);
            const profileUrl = $slide.data('profile-url');

            // Exit if no profile URL
            if (!profileUrl) return;

            // Check if the click was inside or on the play button
            if ($(e.target).closest('.play-btn').length > 0) {
                // User clicked play button → do nothing, let modal open
                return;
            }

            // Otherwise, navigate to the profile
            window.location.href = profileUrl;
        });

        // Optional: Set cursor style for visual feedback
        $('.our-tutors-slider').on('mouseenter', '.swiper-slide', function () {
            $(this).css('cursor', 'pointer');
        });
    });


    document.addEventListener('DOMContentLoaded', function() {
        // Select all anchor (<a>) tags in the document
        var links = document.querySelectorAll('a');

        // Add an event listener to each link
        links.forEach(function(link) {
            link.addEventListener('contextmenu', function(event) {
                // Prevent the context menu from appearing
                event.preventDefault();
            });
        });
    });

    $('.addToCartProduct').on('click', function() {
        var course_id = $(this).data('course_id');
        var course_type = $(this).data('coursetype');
        var product_id = $(this).data('product_id');
        var bundle_id = $(this).data('bundle_id');
        var quantity = $(this).data('quantity');
        var route = $(this).data('route');
        var login_route = $(this).data('login_route');
        var ref = localStorage.getItem('ref')

        $.ajax({
            type: "POST",
            url: route,
            data: {
                'course_id': course_id,
                'product_id': product_id,
                'bundle_id': bundle_id,
                'ref': ref,
                'quantity': quantity,
                '_token': $('meta[name="csrf-token"]').attr('content')
            },
            datatype: "json",
            success: function(response) {
                toastr.options.positionClass = 'toast-bottom-right';
                if (response.status == 402) {
                    toastr.error(response.msg)
                }
                if (response.status == 401 || response.status == 404 || response.status == 409) {
                    toastr.error(response.msg)
                } else if (response.status == 200) {
                    $('.cartQuantity').text(response.quantity)
                    toastr.success(response.msg)
                    $('.msgInfoChange').html(response.msgInfoChange)
                } else if (response.status == 422) {
                    toastr.error(response.msg)
                }
            },
            error: function(error) {
                toastr.options.positionClass = 'toast-bottom-right';
                if (error.status == 401) {
                    // $('#loginModal').modal({
                    //     show: true,
                    //     backdrop: 'static',
                    //     keyboard: false
                    // });
                    // $('#coursetype').val(course_type)
                    // $('#loginModal').modal('show')
                    // window.location.href = login_route;
                    toastr.error("You need to login first!")
                }
                if (error.status == 403) {
                    toastr.error("You don't have permission to add course or product!")
                }

            },
        });
    })
</script>

<script>
    $(document).ready(function() {
        console.log('Document ready');
        if ($.fn.select2) {
            console.log('Select2 plugin found');
            $('#country_id').select2({
                placeholder: "Select a Country",
                allowClear: true,
                width: '100%'
            });

            $('#course_id').select2({
                placeholder: "Select Course (Live)",
                allowClear: true,
                width: '100%'
            });

            console.log('Select2 initialized on #country_id');
        } else {
            console.log('Select2 plugin not found');
        }
    });
</script>
@endpush