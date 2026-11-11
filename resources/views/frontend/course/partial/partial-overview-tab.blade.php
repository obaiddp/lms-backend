    <div class="overview-content">
        <p>{{ __($course->subtitle) }}</p>
        @if(count($course->keyPoints) > 0)
        <div class="what-you-will-learn">
            <h4>{{ __('What you will learn') }}</h4>

            <div class="what-you-learn-list-wrap d-flex justify-content-between">
                <ul>
                    @foreach(@$course->keyPoints as $key => $point)
                        @if($key % 2 == 0)
                            <li>
                                <div class="check-wrap flex-shrink-0"><span class="iconify" data-icon="akar-icons:check"></span></div>
                                <p class="flex-grow-1">{{ $point->name }}</p>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <ul>
                    @foreach(@$course->keyPoints as $key=> $point)
                        @if($key % 2 == 1)
                            <li>
                                <div class="check-wrap flex-shrink-0"><span class="iconify" data-icon="akar-icons:check"></span></div>
                                <p class="flex-grow-1">{{ $point->name }}</p>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
      
        {{-- <p>{!! nl2br($course->description) !!}</p> --}}
        {!! html_entity_decode($course->description) !!}
        {{-- {!! $course->description !!} --}}
        {{-- {!! html_entity_decode($course->description, ENT_QUOTES, 'UTF-8') !!} --}}
    </div>
