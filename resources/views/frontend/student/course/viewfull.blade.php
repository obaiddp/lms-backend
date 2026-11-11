@extends('frontend.layouts.app')

@section('content2')
@section('navbar-header')
@if ($lectureID)
<span><a href={{route('student.my-course.show',['slug'=>$courseSlug])}}>{{__($courseName)}}</a></span>
<span style="margin-left: 50px">{{__($lectureTitle)}}</span>
@else
{{__('Learning Resource')}}
@endif
@endsection

<style>

body, html {
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
}


.pdf-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  overflow: auto;
}


.pdf-container object,
.pdf-container embed {
  width: 100%;
  height: 100%;
}


    .main-content iframe body, .main-content iframe html{
        background-color: transparent !important;
    } 
</style>


<body>
    <div class="main-content" style="min-height: 88vh; width: 98%; padding: 0; margin: 0 auto; padding-top: 1%;">
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
                <div class="card">
                    <div class="card-body p-0 d-flex justify-content-center">
                        <?php if(($file_ext =="jpg" || $file_ext=="jpeg"||$file_ext=="gif"||$file_ext=="png"||$file_ext=="webp")){?>
                            <div align="center">
                                {{-- <img style="" src="<?php echo base_url();?>/public/assets/uploads/<?php echo $file_name ;?>" alt="Picture"> --}}
                                <img style="width: 50%;" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}" alt="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                            </div>
                        <?php } else if(($file_ext =="mp3" ||$file_ext =="wav"||$file_ext =="m4a")){ ?>
                        <div align="center">
                            <audio style="" controls>
                                <source src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                            </audio>
                        </div>
                        <?php } else if(($file_ext =="pdf")){ ?>
                        {{-- <div align="center">
                            <object type="application/pdf" style="height: 94vh; width: 44vw; margin-top: -4vw;"  data="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}#zoom=130&toolbar=0" width="100%" height="100%">
                                <embed type="application/pdf" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                                <p>This pdf is not opened here. Please click button to view it on browser instead: <a href="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">View PDF</a>.</p>
                                </embed>
                            </object>
                        </div> --}}
                        <div class="pdf-container">
                            <object type="application/pdf" data="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}#zoom=130&toolbar=0">
                                <embed type="application/pdf" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                                <p>This pdf is not opened here. Please click button to view it on browser instead: <a href="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">View PDF</a>.</p>
                              </embed>
                              </object>
                        </div>
                        <?php } else if(($file_ext =="doc" || $file_ext =="docx")){ ?>
                            <div>
                                <a class="btn btn-primary" href="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">Download</a>   
                            </div>     
                            <iframe width="100%" style="height: 84vh; width: 90%; margin: 0 auto;" type="application/docx" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}"></iframe>
                        <?php } else if(($file_ext =="zip")){ ?>
                            <iframe width="100%" style="height: 84vh; width: 90%; margin: 0 auto;" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}{{'/index.html'}}"></iframe>
                            <?php }  
                            else if($file_ext =="pptx"){ ?>
                        <div align="center" id="warper">
                            <div id="container">
                                <br>
                                <div id="result"></div>
                                <br><br><br><br><br><br><br>
                            </div>
                        </div>
                        <?php } else if(($file_ext =="mp4" ||$file_ext =="webm"||$file_ext =="mkv")){ ?>
                        <div align="center">
                            <video style="height: 84vh; width: 100%;" width="100%" height="100%" controls>
                                {{-- {{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}} --}}
                                <source src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                            </video>
                        </div>
                        <?php } else{echo "";} ?>
                    </div>       
                </div>
            </div>
        </div>
    </div>
</body>
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

$("#result").pptxToHtml({

pptxFileUrl: "<?php echo $baseURL ;?>/uploads/course_resource/<?php echo $file_name ;?>",
// fileInputId: "uploadFileInput",
slideMode: true,
keyBoardShortCut: false,
// slidesScale: "50%", 
mediaProcess: true,
themeProcess: false,
// slideType: "revealjs",
slideModeConfig: {  //on slide mode (slideMode: true)
first: 1, 
nav: true, /** true,false : show or not nav buttons*/
navTxtColor: "black", /** color */
navNextTxt:"&#8250;", //">"
animations:true,
navPrevTxt: "&#8249;", //"<"
showPlayPauseBtn: true,/** true,false */
keyBoardShortCut: true, /** true,false */
showSlideNum: true, /** true,false */
showTotalSlideNum: true, /** true,false */
autoSlide: true, /** false or seconds (the pause time between slides) , F8 to active(keyBoardShortCut: true) */
randomAutoSlide: false, /** true,false ,autoSlide:true */ 
loop: true,  /** true,false */
background: false, /** false or color*/
transition: "slid", /** transition type: "slid","fade","default","random" , to show transition efects :transitionTime > 0.5 */
transitionTime: 1 /** transition time in seconds */
},
revealjsConfig: { /* for 'revealjs' settings (https://revealjs.com) */
    transition: 'zoom',
    backgroundTransition: 'zoom', 
    autoSlide: 5000,
    loop: true,
    slideNumber: true,
    }
});
</script>

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

@endsection



