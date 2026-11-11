@props([
    'title' => '',
    'subtitle' => '',
    'breadcrumbs' => [],
    'additionalClasses' => '',
])

<div class="modal fade VideoTypeModal" id="newVideoPlayerModal" tabindex="-1"
    aria-labelledby="newVideoPlayerModal" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <span
                class="iconify" data-icon="akar-icons:cross">
            </span>
        </button>
    </div>

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="video-player-area">
                    <div class="plyr__video-embed" id="youtubePlayer2">
                        <iframe src="https://www.youtube.com/embed/dummyvideo"
                            allowfullscreen allowtransparency allow="autoplay" style="width: 100% !important">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<link rel="stylesheet"
    href="{{ asset('frontend/assets/vendor/video-player/plyr.css') }}">
@endpush

<!-- Video Player js -->
<script src="{{ asset('frontend/assets/vendor/video-player/plyr.js') }}"></script>

<script>
    const zai_player1 = new Plyr('#playerVideoYoutube');
    const aysha_player = new Plyr('#player');
    const zai_player1student = new Plyr('#playerVideoYoutubeStudent');
    const aysha_player_student = new Plyr('#playerStudent');
    const aysha_player2 = new Plyr('#youtubePlayer');
</script>
<!-- Video Player js -->