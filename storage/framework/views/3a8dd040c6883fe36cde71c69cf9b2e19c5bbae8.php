<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="<?php echo e(get_option('app_copyright')); ?>">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="msapplication-TileImage" content="<?php echo e(getImageFile(get_option('app_logo'))); ?>">
    <meta name="msapplication-TileColor" content="rgba(103, 20, 222,.55)">
    <meta name="theme-color" content="#754FFE">
    <?php $defaultMeta = getMeta('default'); ?>
    <meta name="description" content="<?php echo e($defaultMeta['meta_description']); ?>">
    <meta name="keywords" content="<?php echo e($defaultMeta['meta_keyword']); ?>">
    <meta property="og:type" content="Learning">
    <meta property="og:title" content="<?php echo e($defaultMeta['meta_title']); ?>">
    <meta property="og:description" content="<?php echo e($defaultMeta['meta_description']); ?>">
    <meta property="og:image" content="<?php echo e($defaultMeta['og_image']); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:site_name" content="<?php echo e(get_option('app_name')); ?>">
    <meta name="twitter:card" content="Learning">
    <meta name="twitter:title" content="<?php echo e($defaultMeta['meta_title']); ?>">
    <meta name="twitter:description" content="<?php echo e($defaultMeta['meta_description']); ?>">
    <meta name="twitter:image" content="<?php echo e($defaultMeta['og_image']); ?>">
    <title><?php echo e(get_option('app_name')); ?> - <?php echo e(__(@$title)); ?></title>
    <style>
        .resources-container {
            display: flex;
            flex-wrap: wrap;
            gap: 50px;
            padding: 5px;
            border: 1px solid black;
            border-radius: 10px;
        }
        #resourcesContainer {
            display: flex;
            flex-wrap: wrap;
            gap: 50px;
            padding: 5px;
            border: none;
            border-radius: 10px;
        }
        .resource-box {
            cursor: pointer;
            width: 14vw;
            height: 10vw;
            font-size: 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 5px;
        }

        .resource-box .resource-link .folder svg {
            width: 4vw;
            height: 4vw;
        }
        .resource-box:hover {
            background-color: #f0f0f0;
        }

        .resource-box .resource-link {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 1rem;
            text-align: center;
        }

        .course-box {
            margin-right: 20px;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
        }
    
        .back-btn {
            background: #89BD40;
            width: 3vw;
            height: 3vw;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .back-btn:hover {
            cursor: pointer;
        }
    </style>
    <link href="<?php echo e(asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('frontend/assets/css/jquery-ui.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('frontend/assets/vendor/fontawesome-free/css/all.min.css')); ?>" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php if(get_option('app_font_design_type') == 2): ?>
        <?php if(empty(get_option('app_font_link'))): ?>
            <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <?php else: ?>
            <?php echo get_option('app_font_link'); ?>

        <?php endif; ?>
    <?php else: ?>
        <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/fonts/feather/feather.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/animate.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/owl.carousel.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/owl.theme.default.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/venobox.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/sweetalert2/sweetalert2.css')); ?>">
    <link href="<?php echo e(asset('frontend/assets/css/style.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/navbar-styles.css')); ?>">
    <link href="<?php echo e(asset('frontend/assets/css/extra.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/responsive.css')); ?>">
    <link rel="icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" type="image/x-icon">
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" sizes="72x72">
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" sizes="114x114">
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" sizes="144x144">
    <?php if(isEnableOpenAI()): ?>
        <link rel="stylesheet" href="<?php echo e(asset('addon/AI/css/main.css')); ?>">
    <?php endif; ?>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php echo $__env->yieldPushContent('style'); ?>
    <?php if(!empty(get_option('certificate_regular')) && get_option('certificate_regular') != ''): ?>
        <style>
            .certificateFont {
                @font-face {
                    font-family: "certificateFont";
                    url: <?php echo e(asset(get_option('certificate_font'))); ?>,
                }
            }
        </style>
    <?php endif; ?>
    <?php echo toastr_css(); ?>
    <?php echo $__env->make('frontend.layouts.dynamic-style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        function getLanguage() {
            return {
                "sEmptyTable": "<?php echo e(__('No data available in table')); ?>",
                "sInfo": "<?php echo e(__('Showing _START_ To _END_ Of _TOTAL_ Entries')); ?>",
                "sInfoEmpty": "<?php echo e(__('Showing 0 to 0 of 0 entries')); ?>",
                "sInfoFiltered": "<?php echo e(__('(filtered from _MAX_ total entries)')); ?>",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "<?php echo e(__('Show _MENU_ entries')); ?>",
                "sLoadingRecords": "<?php echo e(__('Loading...')); ?>",
                "sProcessing": "<?php echo e(__('Processing...')); ?>",
                "sSearch": "<?php echo e(__('Search:')); ?>",
                "sZeroRecords": "<?php echo e(__('No matching records found')); ?>",
                "oPaginate": {
                    "sFirst": "<?php echo e(__('First')); ?>",
                    "sLast": "<?php echo e(__('Last')); ?>",
                    "sNext": "<?php echo e(__('Next')); ?>",
                    "sPrevious": "<?php echo e(__('Previous')); ?>"
                },
                "oAria": {
                    "sSortAscending": ": <?php echo e(__('activate to sort column ascending')); ?>",
                    "sSortDescending": ": <?php echo e(__('activate to sort column descending')); ?>"
                }
            };
        }
    </script>
</head>
<?php $selectedLanguage = selectedLanguage(); ?>
<body class="<?php echo e($selectedLanguage->rtl == 1 ? 'direction-rtl' : 'direction-ltr'); ?>">
<?php if(get_option('allow_preloader') == 1): ?>
    <div id="preloader">
        <div id="preloader_status"><img src="<?php echo e(getImageFile(get_option('app_preloader'))); ?>" alt="img" /></div>
    </div>
<?php endif; ?>

<?php if(View::hasSection('content3')): ?>
    <div class="col-lg-12 mt-20 instructor-profile-page">
        <?php echo $__env->yieldContent('content3'); ?>
    </div>
<?php else: ?>

<?php echo $__env->make(getThemePath().'.layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<header style="padding-top: 6rem; font-size: 2rem !important; margin-left: 4rem; margin-right: 4rem;">
    <?php echo $__env->yieldContent('header-new'); ?>
</header>

<section class="instructor-profile-page">
    <div class="container">
        <div class="instructor-dashboard-page-content">
            <div class="row">
                <div class="col-12">
                    <div class="row mt-20 instructor-dashboard-two-part-join bg-white radius-8">
                        <?php if(View::hasSection('content2')): ?>
                        <div class="col-lg-12 mt-20">
                            <?php echo $__env->yieldContent('content2'); ?>
                        </div>
                        <?php else: ?>
                        <div class="col-lg-3 instructor-profile-left-part-wrap p-0">
                            <?php echo $__env->make('instructor.common.left-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                        <div class="col-lg-9 p-0">
                            <?php echo $__env->yieldContent('content'); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php echo $__env->make('frontend.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php endif; ?>

<?php echo $__env->yieldContent('modal'); ?>

<script>
    var deleteTitle = '<?php echo e(__("Sure! You want to delete?")); ?>';
    var deleteText = '<?php echo e(__("You wont be able to revert this!")); ?>';
    var deleteConfirmButton = '<?php echo e(__("Yes, Delete It!")); ?>';
    var deleteSuccessText = '<?php echo e(__("Item has been deleted")); ?>';
</script>
<script src="<?php echo e(asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/jquery/popper.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/bootstrap/js/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/jquery-easing/jquery.easing.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/jquery-ui.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/waypoints.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/jquery.counterup.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/owl.carousel.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/price_range_script.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/feather.min.js')); ?>"></script>
<script src="<?php echo e(asset('common/js/iconify.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/venobox.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/menu.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/js/frontend-custom.js')); ?>"></script>
<script src="<?php echo e(asset('admin/sweetalert2/sweetalert2.all.js')); ?>"></script>
<input type="hidden" id="base_url" value="<?php echo e(url('/')); ?>">
<input type="hidden" class="search_route" value="<?php echo e(route('search-course.list')); ?>">
<script src="<?php echo e(asset('frontend/assets/js/custom/search-course.js')); ?>"></script>
<script src="https://cdn.datatables.net/2.1.4/js/dataTables.min.js"></script>
<?php if(isEnableOpenAI()): ?>
    <script src="<?php echo e(asset('addon/AI/js/main.js')); ?>"></script>
<?php endif; ?>
<script>
    const backBtn = document.getElementById("back-btn");
    const resContainer = document.getElementById("resourcesContainer");
    const catContainer = document.getElementById("resourcesCategoriesContainer");
    $(document).on("click", ".total_students", function(event) {
        event.preventDefault();
        classId = $(this).attr("data-class_id");
        $.ajax({
            type: "POST",
            url: "<?php echo e(route('live-class.get-student-names')); ?>",
            data: {"_token": "<?php echo e(csrf_token()); ?>", class_id: classId},
            dataType: "json",
            success: function(result) {
                $('#student_names_box').empty().html(result);
                $('#showstudentnames').modal('show');
            }
        });
    });
    var base_url = $('#base_url').val();

    $(document).on("click", ".start_bbb_individual", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 tutorURI = $(this).attr("data-tutor_uri");
 var murl = tutorURI;
 var currentHref = murl;

 var murl_2 = base_url+'/'+ 'instructor/live-class/live-class/UpdateTutorMeetingStatusIndividual/' +meetingId+'/'+lessonId;
  $.ajax({
    type: "GET",
    url: murl_2,
            dataType: "json",
            success: (function (data) {
                window.open(currentHref, '_blank');
            })
});

});



$(document).on("click", ".start_meet_individual", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 tutorURI = $(this).attr("data-tutor_uri");
 var murl = tutorURI;
 var currentHref = murl;

 var murl_2 = base_url+'/'+ 'instructor/live-class/live-class/UpdateTutorMeetingStatusIndividual/' +meetingId+'/'+lessonId;
  $.ajax({
    type: "GET",
    url: murl_2,
            dataType: "json",
            success: (function (data) {
                window.open(currentHref, '_blank');
            })
});

});



    $(document).on("click", ".start_bbb_group", function(event){
 event.preventDefault();
 groupId = $(this).attr("data-group_id");
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 tutorURI = $(this).attr("data-tutor_uri");
 var murl = tutorURI;
 var currentHref = murl;
//  console.log(murl)

 var murl_2 = base_url+'/'+ 'instructor/live-class/live-class/UpdateTutorMeetingStatusGroup/' +groupId+'/'+meetingId+'/'+lessonId;
  $.ajax({
    type: "GET",
    url: murl_2,
            dataType: "json",
            success: (function (data) {
         
                window.open(currentHref, '_blank');
            })
});

});


$(document).on("click", ".start_meet_group", function(event){
 event.preventDefault();
 groupId = $(this).attr("data-group_id");
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 tutorURI = $(this).attr("data-tutor_uri");
 var murl = tutorURI;
 var currentHref = murl;
//  console.log(murl)

 var murl_2 = base_url+'/'+ 'instructor/live-class/live-class/UpdateTutorMeetingStatusGroup/' +groupId+'/'+meetingId+'/'+lessonId;
  $.ajax({
    type: "GET",
    url: murl_2,
            dataType: "json",
            success: (function (data) {
         
                window.open(currentHref, '_blank');
            })
});

});


    $(document).on("click", ".create_zoom_meeting_link", function(event) {
        event.preventDefault();
        meetingId = $(this).attr("data-meeting_id");
        userId = $(this).attr("data-user_id") || 'NewUser_' + (new Date()).getTime();
        userName = $(this).attr("data-user_name") || 'New_Tutor';
        var res = userName.replace(/\s+/g, "");
        userType = $(this).attr("data-user_type");
        var murl = base_url + '/instructor/live-class/live-class/NewZoomMeeting/' + meetingId + '/' + userType + '/' + res + '/' + userId;
        // window.open(murl, '_blank');
        window.open(murl, '_self');
    });
    $(document).on("click", ".create_zoom_meeting_link_2", function(event) {
        event.preventDefault();
        meetingId = $(this).attr("data-meeting_id");
        groupId = $(this).attr("data-group_id");
        userId = $(this).attr("data-user_id") || 'NewUser_' + (new Date()).getTime();
        userName = $(this).attr("data-user_name") || 'New_Tutor';
        var res = userName.replace(/\s+/g, "");
        userType = $(this).attr("data-user_type");
        var murl = base_url + '/instructor/live-class/live-class/NewZoomGroupMeeting/' + meetingId + '/' + userType + '/' + res + '/' + userId + '/' + groupId;
        // window.open(murl, '_blank');
        window.open(murl, '_self');
    });

    backBtn.addEventListener('click', function() {
        if (resContainer.style.display === 'flex') {
            catContainer.style.display = 'none';
            catContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease 0s';
            catContainer.style.opacity = '0.1';
            catContainer.style.display = 'block';
            resContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
            resContainer.style.transform = 'scale(0.8)';
            resContainer.style.opacity = '0';
            resContainer.style.display = 'none';
            setTimeout(() => {
                catContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease 0s';
                backBtn.style.display = 'none';
                catContainer.style.opacity = '1';
                catContainer.style.transform = 'scale(1)';
            }, 200);
        }
    });

    function toggleIcon(courseBox) {
        backBtn.style.display = 'flex';
        catContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
        catContainer.style.transform = 'scale(1.1)';
        catContainer.style.opacity = '0';
        resContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
        resContainer.style.transform = 'scale(0.8)';
        resContainer.style.opacity = '0.1';
        setTimeout(() => {
            resContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
            resContainer.style.transform = 'scale(1)';
            resContainer.style.opacity = '1';
            catContainer.style.display = 'none';
            resContainer.style.display = 'flex';
        }, 300);
    }
    document.querySelectorAll('.course-box').forEach(courseBox => {
        courseBox.addEventListener('click', function() {
            toggleIcon(courseBox);
        });
    });
    function showResources(categoryID, courseID) {
        var resourcesContainer = document.getElementById('resourcesContainer');
        resourcesContainer.innerHTML = '';
        $.ajax({
            type: "POST",
            url: "<?php echo e(route('resource.get_by_course_category')); ?>",
            data: {"_token": "<?php echo e(csrf_token()); ?>", category_id: categoryID, course_id: courseID},
            dataType: "JSON",
            success: function(result) {
                if (!$.isEmptyObject(result)) {
                    resourcesContainer.innerHTML = '';
                    result.forEach(function(resource) {
                        var resourceID = resource.resource_id;
                        var resource_name = resource.resource_name;
                        var resource_ext = resource.resource_ext;
                        var resource_path = resource.resource_path;
                        var resourceType = resource.resource_type;
                        var url = (resourceType == 'Text' || resourceType == 'Youtube') ? "<?php echo e(route('resource.view_full_other')); ?>/" + resourceID : "<?php echo e(route('resource.view_full')); ?>/" + resource_ext + '/' + resource_path;
                        var resourceBox = document.createElement('div');
                        resourceBox.className = 'resource-box';
                        // resourceBox.innerHTML = '<a class="resource-link" href ="' + url + '" ><span class="folder"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="#89bd40" d="M13.586 2a2 2 0 0 1 1.284.467l.13.119L19.414 7a2 2 0 0 1 .578 1.238l.008.176V20a2 2 0 0 1-1.85 1.995L18 22H6a2 2 0 0 1-1.995-1.85L4 20V4a2 2 0 0 1 1.85-1.995L6 2zM12 4H6v16h12V10h-4.5A1.5 1.5 0 0 1 12 8.5zm2 .414V8h3.586z"/></g></svg></span>' + resource_name + '</a>';
                        resourceBox.innerHTML = '<a class="resource-link" href ="' + url + '" ><span class="folder"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="#89bd40" d="M13.586 2a2 2 0 0 1 1.284.467l.13.119L19.414 7a2 2 0 0 1 .578 1.238l.008.176V20a2 2 0 0 1-1.85 1.995L18 22H6a2 2 0 0 1-1.995-1.85L4 20V4a2 2 0 0 1 1.85-1.995L6 2zM12 4H6v16h12V10h-4.5A1.5 1.5 0 0 1 12 8.5zm2 .414V8h3.586z"/></g></svg></span>' + resource_name + '</a>';
                        resourcesContainer.appendChild(resourceBox);
                    });
                    setTimeout(() => {
                        $('#resourcesContainer').attr('hidden', null).show();
                    }, 300);
                } else {
                    resourcesContainer.innerHTML = '';
                    var resourceBox = document.createElement('div');
                    resourceBox.className = 'resource-box';
                    resourceBox.innerHTML = 'This Course Category Has No Resources Allocated Yet!!!';
                    resourcesContainer.appendChild(resourceBox);
                    $('#resourcesContainer').attr('hidden', null).show();
                }
            }
        });
    }
</script>
<?php echo $__env->yieldPushContent('script'); ?>
<?php echo toastr_js(); ?>
<?php echo app('toastr')->render(); ?>
<?php if(@$errors->any()): ?>
    <script>
        "use strict";
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.error("<?php echo e($error); ?>");
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </script>
<?php endif; ?>
</body>
</html>
<?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/layouts/instructor.blade.php ENDPATH**/ ?>