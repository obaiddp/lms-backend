<body>
    <div class="main-content">
        <!-- <div class="breadcrumb">
            <h1>Layouts</h1>
            <ul>
                <li><a href="href">Forms</a></li>
                <li>Form Layouts</li>
            </ul>
        </div> -->
        <div class="row">
            <div class="col-md-12">
                <h4 align="center" style="color:red; margin-top:5px;margin-bottom:-5px">Learning Resource</h4><br/>
            
                <div class="card mb-5">
                    <div class="card-body">
                    <?php
   
   if(($file_ext =="jpg" || $file_ext=="jpeg"||$file_ext=="gif"||$file_ext=="png"||$file_ext=="webp")){?>
       <div align="center">
         {{-- <img style="" src="<?php echo base_url();?>/public/assets/uploads/<?php echo $file_name ;?>" alt="Picture"> --}}
         <img style="" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}" alt="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
         </div>
         <?php } 
                   
                     else if(($file_ext =="mp3" ||$file_ext =="wav"||$file_ext =="m4a")){ ?>
                     <div align="center">
                 <audio style="" controls>
                <source src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                    </audio>
                    </div>
                                      <?php }
                   else if(($file_ext =="pdf")){ ?>

                    <div align="center">
                        <object  type="application/pdf" style=""  data="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}#toolbar=0" width="100%" height="100%">
                             <embed type="application/pdf" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                            <p>This pdf is not opened here. Please click button to view it on browser instead: <a href="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">View PDF</a>.</p>
                            </embed>
                        </object>
                    </div>
                  
                                      <?php }

                       else if(($file_ext =="doc" || $file_ext =="docx")){ ?>
                       <div>
                         <a class="btn btn-primary" href="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">Download</a>   
                        </div>     
                             <iframe width="100%" style="height: 90%" type="application/docx" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                       
                       </iframe>
                   
                  
                                      <?php }                  
                     else if(($file_ext =="zip")){ ?>
                  
                   <iframe width="100%" style="height: 90%" src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}{{'/index.html'}}">
                
                       </iframe>
                        <?php }  
                        else if($file_ext =="pptx"){ ?>
                          
                    <div align="center" id="warper">
                            <div id="container">
                            
                            <br>
                        <div  id="result"></div>
                        <br><br><br><br><br><br><br>
                        </div>
                        </div>
                      <?php  }                 
                  else if(($file_ext =="mp4" ||$file_ext =="webm"||$file_ext =="mkv")){ ?>
                                    <div align="center">
                                    <video  width="100%" height="100%" controls>
                                        {{-- {{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}} --}}
                                    <source src="{{ asset('uploads/course_resource')}}{{'/'}}{{$file_name}}">
                                        </video>
                                    </div>
                                      <?php }
                      else{echo "";} ?>
                    </div>       
                </div>
            </div>
        </div>
        <!-- end of main-content -->


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

pptxFileUrl: "<?php echo $baseURL ;?>uploads/course_resource/<?php echo $file_name ;?>",
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



