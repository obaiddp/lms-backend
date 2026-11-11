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
   
   if(($resourceType =="Text")){?>
       <div align="center">
            <div class="card-text">
                <div class="card-body-text">
                    <p class="formatted-text">
                            {{$text_description}}
                    </p>
                </div>
            </div>
        </div>
         <?php } 
                   
                     else if(($resourceType =="Youtube")){ ?>
                     <div align="center">
                        <iframe width="100%" style="height: 90%" type="application/youtube" src="https://www.youtube.com/embed/{{ $youtube_url_path }}">

                    
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

</script>



