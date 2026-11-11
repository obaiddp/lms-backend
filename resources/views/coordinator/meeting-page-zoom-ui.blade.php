<!DOCTYPE html>
<html lang="en" dir="">
<head>

{{-- <link rel="stylesheet" href="{{$baseURL}}/assets/zoom-ui/@zoom/videosdk-ui-toolkit/dist/videosdk-ui-toolkit.css"> --}}
<link rel="stylesheet" href="{{asset('assets/zoom-ui/@zoom/videosdk-ui-toolkit/dist/videosdk-ui-toolkit.css')}}">
<style type="text/css">
    .animated-class {
        background-color: black;
    transition: background-color 3s ease;
    animation: fadeIn 3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
            body {
                padding-top: 50px;
            }
            .leaveend {
                visibility: hidden;
            }
            .navbar-inverse {
                background-color: #313131;
                border-color: #404142;
            }
            .navbar-header h4 {
                margin: 0;
                padding: 15px 15px;
                color: #c4c2c2;
            }
            .navbar-right h5 {
                margin: 0;
                padding: 9px 5px;
                color: #c4c2c2;
            }
            .navbar-inverse .navbar-collapse, .navbar-inverse .navbar-form{
                border-color: transparent;
            }
</style>
</head>
<body>
<div class="main-content">
    <input type="hidden" name="baseURL" id="baseURL" value="{{$baseURL}}">
                <div class="separator-breadcrumb border-top"></div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <?php if($group_id == null){ ?>
                        <div hidden style="font-size: 12px; margin-top:-40px" align="center" id="timer">
                        <span> <b>Class Time Left: </b></span><span id="minutes"><b>00</b></span>:<span id="seconds"><b>00</b></span>
                        </div>
                        <?php } else{ ?>
                        <div hidden style="font-size: 12px;word-wrap:break-word;margin-top:-40px" align="center" id="timer">
                            <span><b>Class Time Left: </b></span><span id="minutes"><b>00</b></span>:<span id="seconds"><b>00</b></span>
                            </div>
                        <?php } ?>
                        <div align="center" id="loader-container" class="text-center">
                                <div class="spinner-border" role="status">
                                     <span class="sr-only">Loading...</span>
                                </div>
                        </div>

                            <div hidden align="center" id="end-message" class="text-center">
                                     <span id="end-message-text" class="sr-only"><b>Class Time Is Ended...</b></span>
                            </div>
                            <div hidden id="exit-buttons-tab" align="center">
                                    <button class="form-control" style="font-size: 12px; background-color: blue;      color: white;
                   padding: 8px 16px;
                   text-align: center;
                   text-decoration: none;
                   display: inline-block;
                   font-size: 16px;
                   margin: 4px 2px;
                   cursor: pointer;
                   border-radius: 10px;"  id="leave_btn">Leave</button>
                                    {{-- <button  style="font-size: 12px; background-color: red;      color: white;
                   padding: 8px 16px;
                   text-align: center;
                   text-decoration: none;
                   display: inline-block;
                   font-size: 16px;
                   margin: 4px 2px;
                   cursor: pointer;
                   border-radius: 10px;" id="end_btn">End</button> --}}
                            </div>
                    </div>
                </div>
                <!-- end of row-->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-body">
                               <!-- <div style="width:100%;height:100vh;" id="jitsi_meet_container"></div> -->
                               <!-- <div id='previewContainer'></div> -->
                               <div id='sessionContainer'></div>
                            </div>
                        </div>
                    <!-- end of col-->
                    
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <?php if($group_id == null){ ?>
                        <div align="center">
                        <span> <h4>Quran Education Online Class - <?php echo $group_id; ?> - <?php echo $tutor_name; ?> & <?php echo $student_name; ?></h4>
                        </div>
                        <?php } else{ ?>
                        <div style="word-wrap:break-word" align="center">
                            <span><h4>Quran Education Online Class - <?php echo $group_id; ?> - <?php echo $tutor_name; ?> & [<?php echo $student_names_json; ?>] </h4>
                            </div>
                        <?php } ?>

                    </div>
                </div>
        </div>

        </body>   
        </html>   

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/10.5.24/jsrsasign-all-min.js"></script>
        <script src="{{asset('assets/zoom-ui/@zoom/videosdk-ui-toolkit/index.js')}}" type="module"></script>
        <script src="{{asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js')}}"></script>
        <script src="{{asset('frontend/assets/vendor/jquery/popper.min.js')}}"></script>
        <script src="{{asset('frontend/assets/vendor/bootstrap/js/bootstrap.min.js')}}"></script>

<script type="text/javascript">
 var base_url = $('#baseURL').val();

   $(document).ready(function () {
});
</script>
<script type="module">
 
import uitoolkit from "{{asset('assets/zoom-ui/@zoom/videosdk-ui-toolkit/index.js')}}";
import previewkit from "{{asset('assets/zoom-ui/@zoom/videosdk-ui-toolkit/index.js')}}";
   
    var meeting_id = '<?php echo $meeting_id;?>';
    var meeting_password = '<?php echo $meeting_password;?>';
    var meeting_signature = '<?php echo $meeting_signature;?>';
    var session_name = '<?php echo $session_name;?>';
var group_id = <?php echo $group_id;?>;
var user_name = '<?php echo $user_name;?>';
var user_type = '<?php echo $user_type;?>';
var user_id = '<?php echo $user_id;?>';
var base_url = '<?php echo $baseURL;?>';
var meeting_end_url = '<?php echo $baseURL;?>/academic-coordinator/class-schedule';
var startTime = <?php echo $start_time;?>;
var endTime = <?php echo $end_time;?>;
if(group_id === 0){
    group_id = null;
}
let hostId = null;     


function generateSignature(apiKey, sessionName, sessionKey, role,apiSecret) {
 
    const iat = Math.floor(Date.now() / 1000)
    const exp = iat + 80 * 80 * 2

    const oHeader = { alg: 'HS256', typ: 'JWT' }

    const oPayload = {
        app_key: apiKey,
        role_type: role,
        tpc: sessionName,
        version : 1,
        iat: iat,
        exp: exp,
        // session_key: sessionKey
    };

    const sHeader = JSON.stringify(oHeader);
    const sPayload = JSON.stringify(oPayload);

    const sJWT = KJUR.jws.JWS.sign('HS256', sHeader, sPayload, apiSecret);

    return sJWT;

}

            $(document).ready(function () {

            var meetConfig = {
                apiKey: "<?php echo $sdkKey;?>",
                apiSecret: "<?php echo $sdkSecret;?>",
                meetingNumber: "<?php echo $meeting_id;?>",
                userName: "<?php echo $user_name;?>",
                passWord: "<?php echo $meeting_password;?>",
                leaveUrl: "<?php echo $baseURL;?>/academic-coordinator/class-schedule",
                role: 1,
            };
        
      
         
        
            var sessionContainer = document.getElementById('sessionContainer')

var config = {
    videoSDKJWT: '',
    sessionName: session_name,
    userName: user_name,
    // sessionPasscode: meeting_password,
    features: ['video', 'audio', 'settings', 'users', 'chat', 'share']
};
var role = 1;


if(meeting_signature != ''){
        // var signature1 = meeting_signature;
        var signature1 = generateSignature('<?php echo $sdkKey;?>', '<?php echo $session_name;?>',  '<?php echo $meeting_password;?>', 1, '<?php echo $sdkSecret;?>');
        if(group_id == null){
    $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/academic-coordinator/UpdateMeetingSignature/<?php echo $meeting_id;?>",
    data: {
        'meetingSignature':signature1,
    },
            success: (function (result) {
            })
        })
    }
    else{
        $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/academic-coordinator/UpdateMeetingSignature/<?php echo $meeting_id;?>/<?php echo $group_id;?>",
    data: {
        'meetingSignature':signature1,
    },
            success: (function (result) {
            })
        })  
    }
    }
    else{
        var signature1 = generateSignature('<?php echo $sdkKey;?>', '<?php echo $session_name;?>',  '<?php echo $meeting_password;?>', 1, '<?php echo $sdkSecret;?>');
        if(group_id == null){
    $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/academic-coordinator/UpdateMeetingSignature/<?php echo $meeting_id;?>",
    data: {
        'meetingSignature':signature1,
    },
            success: (function (result) {
            })
        })
    }
    else{
        $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/academic-coordinator/UpdateMeetingSignature/<?php echo $meeting_id;?>/<?php echo $group_id;?>",
    data: {
        'meetingSignature':signature1,
    },
            success: (function (result) {
            })
        })  
    }

    }

    if(signature1){
        config.videoSDKJWT = signature1;
        console.log(signature1)
        joinZoomSession();
    }

    function joinZoomSession() {
        console.log("started joined");
        console.log(config);
    // uitoolkit.joinSession(sessionContainer, config)
    // uitoolkit.joinSession(sessionContainer, config,meeting_id,group_id,endTime,base_url,'Coordinator',meeting_end_url)
    uitoolkit.joinSession(sessionContainer, config,meeting_id,group_id,base_url,endTime,meeting_end_url,'Coordinator',0)
    // startTimer(meeting_id,group_id);
    uitoolkit.onSessionJoined(sessionJoined)
    uitoolkit.onSessionClosed(sessionClosed)
}


var sessionClosed = (() => {
  
    console.log('session closed')
    uitoolkit.closeSession(sessionContainer)
    document.getElementById('join-flow').style.display = 'block'
})


var sessionJoined = (() => {   
    console.log('session joined')
  

})



        });


        $('#leave_btn').on('click', function () {
            uitoolkit.closeSession(sessionContainer)
            window.location.href = meeting_end_url;
 
        });




</script>   

              
          





