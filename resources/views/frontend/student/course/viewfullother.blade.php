@extends('frontend.layouts.app')
@section('content3')
    @section('navbar-header')
        @if ($lectureID)
        <span><a href={{route('student.my-course.show',['slug'=>$courseSlug])}}>{{__($courseName)}}</a></span>
        <span style="margin-left: 50px">{{__($lectureTitle)}}</span>
        @else
        {{__('Learning Resource')}}
        @endif
    @endsection

<head>
    <style>
        .card-text {
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        .card-body-text {
            display: flex;
            flex-direction: column;
        }

        .formatted-text {
            line-height: 1.6; /* Controls the line spacing */
            text-align: justify; /* Aligns the text evenly */
            font-size: 16px;
            font-family: Arial, sans-serif;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            white-space: normal;
            word-wrap: break-word;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="main-content" style="min-height: 80vh; width: 98%; padding: 0; margin: 0 auto; padding-top: 1%;">
        <!-- <div class="breadcrumb">
            <h1>Layouts</h1>
            <ul>
                <li><a href="href">Forms</a></li>
                <li>Form Layouts</li>
            </ul>
        </div> -->
        @if ($lectureID)
        <div class="row">
            <div class="col-md-2 mb-3">
                <a href="{{route('student.my-course.show', ['slug' => $courseSlug, 'tab' => 'content'])}}" class="quiz-answer-btn theme-btn theme-button3 quiz-back-btn default-hover-btn">Back</a>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-5">
                    <div class="card-body">
                    <?php if(($resourceType =="Text")){?>
                    <div align="center">
                        <div>
                            <div class="card-body-text">
                                <p class="formatted-text">
                                    {{$text_description}}
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php } else if(($resourceType =="Youtube")){ ?>
                    <div align="center">
                        <iframe id="contentIframe" style="height: 84vh; width: 100%;" width="100%" type="application/youtube" src="https://www.youtube.com/embed/{{ $youtube_url_path }}">
                        </iframe>
                    </div>
                    <?php } else{echo "";} ?>
                    </div>       
                </div>
            </div>
        </div>
    </div>
</body>


<script>
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
</script>

    <!-- Footer Start -->
<script src="{{asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('frontend/assets/js/pptxjs/jszip.min.js')}}"></script>
<script src="{{asset('frontend/assets/js/pptxjs/filereader.js')}}"></script>
<script src="{{asset('frontend/assets/js/pptxjs/d3.min.js')}}"></script>
<script src="{{asset('frontend/assets/js/pptxjs/nv.d3.min.js')}}"></script>
<script src="{{asset('frontend/assets/js/pptxjs/dingbat.js')}}"></script>
<script src="{{asset('frontend/assets/js/pptxjs/divs2slides.js')}}"></script>
<script src="{{asset('frontend/assets/js/pptxjs/pptxjs.js')}}"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const iframe = document.getElementById("contentIframe");
    const preloader = document.getElementById("preloader");

    if (iframe) {
        iframe.addEventListener("load", function () {
            if (preloader) {
                preloader.style.display = "none"; 
            }
        });
    }
});
</script>

@endsection

