@foreach($reviews as $review)
    <!-- Customer Review Item Start-->
<div class="customer-review-item d-flex">
    <div class="review-container">
        <ul class="rating-list d-flex align-items-center me-2">
            <li class="{{ $review->rating >= 1 ? 'star-full': '' }}"><span class="iconify" data-icon="bi:star-fill"></span></li>
            <li class="{{ $review->rating >= 2 ? 'star-full': '' }}"><span class="iconify" data-icon="bi:star-fill"></span></li>
            <li class="{{ $review->rating >= 3 ? 'star-full': '' }}"><span class="iconify" data-icon="bi:star-fill"></span></li>
            <li class="{{ $review->rating >= 4 ? 'star-full': '' }}"><span class="iconify" data-icon="bi:star-fill"></span></li>
            <li class="{{ $review->rating >= 5 ? 'star-full': '' }}"><span class="iconify" data-icon="bi:star-fill"></span></li>
        </ul>

        <p class="review-text">
            {{ $review->comment }}
        </p>
        
        <p class="review-date">{{ @$review->created_at->diffForHumans() }}</p>
        
        <div class="d-flex justify-content-between align-items-center">
            <div class="reviewer-info">
                <div class="reviewer-avatar">
                    <img src="{{ getImageFile(@$review->user->image_path) }}" alt="{{ @$review->user->name }}">
                </div>
                <div class="reviewer-name-role">
                    <p class="reviewer-name">{{ @$review->user->name }} - Student</p>
                </div>
            </div>
            
            {{-- <div class="review-actions">
                <button class="action-button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.39014 18.49V8.32998C8.39014 7.92998 8.51014 7.53998 8.73014 7.20998L11.4601 3.14998C11.8901 2.49998 12.9601 2.03998 13.8701 2.37998C14.8501 2.70998 15.5001 3.80997 15.2901 4.78997L14.7701 8.05998C14.7301 8.35998 14.8101 8.62998 14.9801 8.83998C15.1501 9.02998 15.4001 9.14997 15.6701 9.14997H19.7801C20.5701 9.14997 21.2501 9.46997 21.6501 10.03C22.0301 10.57 22.1001 11.27 21.8501 11.98L19.3901 19.47C19.0801 20.71 17.7301 21.72 16.3901 21.72H12.4901C11.8201 21.72 10.8801 21.49 10.4501 21.06L9.17014 20.07C8.68014 19.7 8.39014 19.11 8.39014 18.49Z" fill="#141414"/>
                    <path d="M5.21 6.37988H4.18C2.63 6.37988 2 6.97988 2 8.45988V18.5199C2 19.9999 2.63 20.5999 4.18 20.5999H5.21C6.76 20.5999 7.39 19.9999 7.39 18.5199V8.45988C7.39 6.97988 6.76 6.37988 5.21 6.37988Z" fill="#141414"/>
                    </svg>
                    <span class="like-count">128</span>
                </button>
                <button class="action-button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.47998 5.65003L10.58 3.25003C10.98 2.85003 11.88 2.65003 12.48 2.65003H16.28C17.48 2.65003 18.78 3.55003 19.08 4.75003L21.48 12.05C21.98 13.45 21.08 14.65 19.58 14.65H15.58C14.98 14.65 14.48 15.15 14.58 15.85L15.08 19.05C15.28 19.95 14.68 20.95 13.78 21.25C12.98 21.55 11.98 21.15 11.58 20.55L7.47998 14.45" stroke="#0B0F0E" stroke-width="1.5" stroke-miterlimit="10"/>
                    <path d="M2.37988 5.6501V15.4501C2.37988 16.8501 2.97988 17.3501 4.37988 17.3501H5.37988C6.77988 17.3501 7.37988 16.8501 7.37988 15.4501V5.6501C7.37988 4.2501 6.77988 3.7501 5.37988 3.7501H4.37988C2.97988 3.7501 2.37988 4.2501 2.37988 5.6501Z" stroke="#0B0F0E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div> --}}

        </div>
    </div>
</div>
    <!-- Customer Review Item End-->
@endforeach

