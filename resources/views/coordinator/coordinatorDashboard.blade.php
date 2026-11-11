@extends('frontend.layouts.app')
@section('content2')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/dashboard.css') }}">
    <link href="{{ asset('frontend/assets/css/style.css') }}" rel="stylesheet">
    <style>
        iframe body{
            background: transparent !important;
        }
    </style>
</head> 
<div class="student-new-dashboard">
        <div class="student-sidebar" id="sidebar">
            <div class="stu-logo">
                <a href="{{ route('coordinator.dashboard') }}">
                    <img class="stu-logo-img" src="{{ getImageFile(get_option('app_logo')) }}" alt="">
                </a>
                <button class="stu-menu-toggle2" onClick="toggleSidebar()">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 18L20 18" stroke="white" stroke-width="2" stroke-linecap="round"></path> <path d="M4 12L20 12" stroke="white" stroke-width="2" stroke-linecap="round"></path> <path d="M4 6L20 6" stroke="white" stroke-width="2" stroke-linecap="round"></path> </g></svg>
                </button>
            </div>
            <div class="stu-info">
                <img class="stu-img" src={{asset(auth::user()->image_path)}} alt="user">
                <span class="stu-name">{{ auth::user()->name }}</span>
                <span class="stu-email">{{ auth::user()->email }}</span>
            </div>
            <div class="student-menu">
                <a href="/academic-coordinator/home" target="iframeUrl" class="stu-menu-item" id="home-menu-item">
                    <div class="icon">
                        <!-- Home Icon SVG -->
                        <svg class="path svg1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M22 22L2 22" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M2 11L10.1259 4.49931C11.2216 3.62279 12.7784 3.62279 13.8741 4.49931L22 11" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15.5 5.5V3.5C15.5 3.22386 15.7239 3 16 3H18.5C18.7761 3 19 3.22386 19 3.5V8.5" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M4 22V9.5" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M20 22V9.5" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 22V17C15 15.5858 15 14.8787 14.5607 14.4393C14.1213 14 13.4142 14 12 14C10.5858 14 9.87868 14 9.43934 14.4393C9 14.8787 9 15.5858 9 17V22" stroke="#024a6a" stroke-width="1.5"></path> <path d="M14 9.5C14 10.6046 13.1046 11.5 12 11.5C10.8954 11.5 10 10.6046 10 9.5C10 8.39543 10.8954 7.5 12 7.5C13.1046 7.5 14 8.39543 14 9.5Z" stroke="#024a6a" stroke-width="1.5"></path> </g></svg>
                    </div>
                    <span>Home</span>
                </a>
                <a href="/academic-coordinator/class-schedule" target="iframeUrl" class="stu-menu-item" id="schedule-menu-item">
                    <div class="icon">
                        <!-- Schedule Icon SVG -->
                        <svg class="path svg4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 9H21M7 3V5M17 3V5M6 12H8M11 12H13M16 12H18M6 15H8M11 15H13M16 15H18M6 18H8M11 18H13M16 18H18M6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V8.2C21 7.07989 21 6.51984 20.782 6.09202C20.5903 5.71569 20.2843 5.40973 19.908 5.21799C19.4802 5 18.9201 5 17.8 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21Z" stroke="#024a6a" stroke-width="2" stroke-linecap="round"></path> </g></svg>
                    </div>
                    <span>Classes Schedule</span>
                </a>
                <a href="/academic-coordinator/class-history" target="iframeUrl" class="stu-menu-item" id="history-menu-item">
                    <div class="icon">
                        <!-- History Icon SVG -->
                        <svg class="path-fill stroke svg5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 5.67541V3C3 2.44772 2.55228 2 2 2C1.44772 2 1 2.44772 1 3V7C1 8.10457 1.89543 9 3 9H7C7.55229 9 8 8.55229 8 8C8 7.44772 7.55229 7 7 7H4.52186C4.54218 6.97505 4.56157 6.94914 4.57995 6.92229C5.621 5.40094 7.11009 4.22911 8.85191 3.57803C10.9074 2.80968 13.173 2.8196 15.2217 3.6059C17.2704 4.3922 18.9608 5.90061 19.9745 7.8469C20.9881 9.79319 21.2549 12.043 20.7247 14.1724C20.1945 16.3018 18.9039 18.1638 17.0959 19.4075C15.288 20.6513 13.0876 21.1909 10.9094 20.9247C8.73119 20.6586 6.72551 19.605 5.27028 17.9625C4.03713 16.5706 3.27139 14.8374 3.06527 13.0055C3.00352 12.4566 2.55674 12.0079 2.00446 12.0084C1.45217 12.0088 0.995668 12.4579 1.04626 13.0078C1.25994 15.3309 2.2082 17.5356 3.76666 19.2946C5.54703 21.3041 8.00084 22.5931 10.6657 22.9188C13.3306 23.2444 16.0226 22.5842 18.2345 21.0626C20.4464 19.541 22.0254 17.263 22.6741 14.6578C23.3228 12.0526 22.9963 9.30013 21.7562 6.91897C20.5161 4.53782 18.448 2.69239 15.9415 1.73041C13.4351 0.768419 10.6633 0.756291 8.14853 1.69631C6.06062 2.47676 4.26953 3.86881 3 5.67541Z" fill="#024a6a"></path> <path d="M12 5C11.4477 5 11 5.44771 11 6V12.4667C11 12.4667 11 12.7274 11.1267 12.9235C11.2115 13.0898 11.3437 13.2344 11.5174 13.3346L16.1372 16.0019C16.6155 16.278 17.2271 16.1141 17.5032 15.6358C17.7793 15.1575 17.6155 14.546 17.1372 14.2698L13 11.8812V6C13 5.44772 12.5523 5 12 5Z" fill="#024a6a"></path> </g></svg>                    </div>
                    <span>Classes History</span>
                </a>
                <hr>
                <a href="{{ route('logout') }}" class="stu-menu-item">
                    <div class="icon">
                        <!-- Logout Icon SVG -->
                        <svg class="path-fill stroke svg10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M14.9453 1.25C13.5778 1.24998 12.4754 1.24996 11.6085 1.36652C10.7084 1.48754 9.95048 1.74643 9.34857 2.34835C8.82363 2.87328 8.55839 3.51836 8.41916 4.27635C8.28387 5.01291 8.25799 5.9143 8.25196 6.99583C8.24966 7.41003 8.58357 7.74768 8.99778 7.74999C9.41199 7.7523 9.74964 7.41838 9.75194 7.00418C9.75803 5.91068 9.78643 5.1356 9.89448 4.54735C9.99859 3.98054 10.1658 3.65246 10.4092 3.40901C10.686 3.13225 11.0746 2.9518 11.8083 2.85315C12.5637 2.75159 13.5648 2.75 15.0002 2.75H16.0002C17.4356 2.75 18.4367 2.75159 19.1921 2.85315C19.9259 2.9518 20.3144 3.13225 20.5912 3.40901C20.868 3.68577 21.0484 4.07435 21.1471 4.80812C21.2486 5.56347 21.2502 6.56459 21.2502 8V16C21.2502 17.4354 21.2486 18.4365 21.1471 19.1919C21.0484 19.9257 20.868 20.3142 20.5912 20.591C20.3144 20.8678 19.9259 21.0482 19.1921 21.1469C18.4367 21.2484 17.4356 21.25 16.0002 21.25H15.0002C13.5648 21.25 12.5637 21.2484 11.8083 21.1469C11.0746 21.0482 10.686 20.8678 10.4092 20.591C10.1658 20.3475 9.99859 20.0195 9.89448 19.4527C9.78643 18.8644 9.75803 18.0893 9.75194 16.9958C9.74964 16.5816 9.41199 16.2477 8.99778 16.25C8.58357 16.2523 8.24966 16.59 8.25196 17.0042C8.25799 18.0857 8.28387 18.9871 8.41916 19.7236C8.55839 20.4816 8.82363 21.1267 9.34857 21.6517C9.95048 22.2536 10.7084 22.5125 11.6085 22.6335C12.4754 22.75 13.5778 22.75 14.9453 22.75H16.0551C17.4227 22.75 18.525 22.75 19.392 22.6335C20.2921 22.5125 21.0499 22.2536 21.6519 21.6517C22.2538 21.0497 22.5127 20.2919 22.6337 19.3918C22.7503 18.5248 22.7502 17.4225 22.7502 16.0549V7.94513C22.7502 6.57754 22.7503 5.47522 22.6337 4.60825C22.5127 3.70814 22.2538 2.95027 21.6519 2.34835C21.0499 1.74643 20.2921 1.48754 19.392 1.36652C18.525 1.24996 17.4227 1.24998 16.0551 1.25H14.9453Z" fill="#024a6a"></path> <path d="M15 11.25C15.4142 11.25 15.75 11.5858 15.75 12C15.75 12.4142 15.4142 12.75 15 12.75H4.02744L5.98809 14.4306C6.30259 14.7001 6.33901 15.1736 6.06944 15.4881C5.79988 15.8026 5.3264 15.839 5.01191 15.5694L1.51191 12.5694C1.34567 12.427 1.25 12.2189 1.25 12C1.25 11.7811 1.34567 11.573 1.51191 11.4306L5.01191 8.43056C5.3264 8.16099 5.79988 8.19741 6.06944 8.51191C6.33901 8.8264 6.30259 9.29988 5.98809 9.56944L4.02744 11.25H15Z" fill="#024a6a"></path> </g></svg>
                    </div>
                    <span>Logout</span>
                </a>
            </div>
            <div class="stu-visit-website">
                <a href="/" class="stu-visit-website-btn">
                    <div class="icon">
                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 107.366 107.366" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path style="fill:#ffffff;" d="M107.366,53.683C107.366,24.082,83.283,0,53.683,0c-4.982,0-9.785,0.734-14.366,2.011L39.21,1.94 c-0.025,0.036-0.043,0.075-0.068,0.115C16.595,8.417,0,29.125,0,53.683c0,29.601,24.082,53.683,53.683,53.683 c4.985,0,9.785-0.734,14.366-2.011l0.107,0.075c0.025-0.036,0.047-0.075,0.068-0.115C90.77,98.948,107.366,78.237,107.366,53.683z M5.343,56.292H24.69c0.193,5.766,0.837,11.438,2.004,16.942H9.366C7.047,68.002,5.662,62.29,5.343,56.292z M102.022,51.077H82.679 c-0.197-5.769-0.837-11.442-2.008-16.946H98C100.319,39.364,101.704,45.076,102.022,51.077z M77.468,51.077h-21.18V34.131h19.061 C76.58,39.625,77.264,45.298,77.468,51.077z M56.292,28.924V5.343c2.53,0.136,5.014,0.415,7.43,0.923 c4.502,6.986,7.97,14.595,10.289,22.658H56.292z M51.077,28.924h-18.12c2.137-7.92,5.354-15.436,9.57-22.368 c2.766-0.655,5.622-1.056,8.55-1.213V28.924z M51.077,34.132v16.946H29.84c0.111-5.766,0.712-11.442,1.85-16.946H51.077z M24.633,51.077H5.34c0.319-6.002,1.704-11.717,4.023-16.946h17.046C25.335,39.654,24.741,45.319,24.633,51.077z M29.898,56.292 h21.18v16.942H32.013C30.785,67.74,30.102,62.068,29.898,56.292z M51.077,78.441v23.581c-2.53-0.136-5.018-0.415-7.43-0.923 c-4.502-6.986-7.97-14.595-10.293-22.658C33.355,78.441,51.077,78.441,51.077,78.441z M56.292,78.441h18.116 c-2.14,7.92-5.354,15.432-9.57,22.368c-2.766,0.659-5.622,1.056-8.55,1.213V78.441H56.292z M56.292,73.234V56.292h21.237 c-0.111,5.762-0.712,11.438-1.854,16.942C75.675,73.234,56.292,73.234,56.292,73.234z M82.732,56.292h19.294 c-0.319,6.002-1.704,11.714-4.023,16.942H80.957C82.031,67.712,82.625,62.047,82.732,56.292z M95.28,28.924H79.393 c-1.943-7.147-4.681-13.993-8.235-20.403C81.294,12.454,89.765,19.694,95.28,28.924z M35.219,8.883 c-3.31,6.32-5.848,13.041-7.627,20.038H12.086C17.425,19.991,25.514,12.898,35.219,8.883z M12.086,78.441h15.89 c1.94,7.147,4.678,13.993,8.231,20.403C26.072,94.911,17.601,87.671,12.086,78.441z M72.146,98.483 c3.31-6.32,5.848-13.041,7.627-20.038H95.28C89.94,87.374,81.852,94.467,72.146,98.483z"></path> </g> </g></svg>
                    </div>
                    <span>Visit Website</span>
                </a>
            </div>
        </div>
            <div class="student-dashboard-content">
            <div class="student-navbar">
                <div class="d-flex justify-content-between align-items-center">
                    <button class="stu-menu-toggle" id="stu-menu-toggle" onClick="toggleSidebar()">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 18L20 18" stroke="#024a6a" stroke-width="2" stroke-linecap="round"></path> <path d="M4 12L20 12" stroke="#024a6a" stroke-width="2" stroke-linecap="round"></path> <path d="M4 6L20 6" stroke="#024a6a" stroke-width="2" stroke-linecap="round"></path> </g></svg>
                    </button>
                    <span class="navbar-header" id="navbar-header">
                    </span>
                </div>
                <div class="d-flex align-items-center student-name-nav">
                    <div class="notification-icon">
                    </div>
                    <span>Hey, {{ auth::user()->name }}</span>
                </div>
            </div>
            <div class="stu-view-content" >
                <iframe src="/academic-coordinator/home" style="width: 100%;max-height: 200vh; min-height: 84vh; border: none !important;" id="iframeUrl" name="iframeUrl" class="iframe-content"></iframe>
            </div>
            <div class="student-footer">
                <p class="font-14">{{ __(get_option('app_copyright')) }}</p>
            </div>
        </div>
    </div>
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
            
        document.addEventListener('DOMContentLoaded', function() {
            const iframe = document.getElementById('iframeUrl');
            iframe.onload = function() {

                const sidebar = document.getElementById('sidebar');
                try {
                    const iframeDocument = iframe.contentWindow.document;
                    console.log("iframeDocument Loaded");
                    sidebar.classList.add('collapsed');
                    iframeDocument.addEventListener('click', handleClickOutside);
                    document.querySelectorAll('.stu-menu-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    const iframeUrl = iframe.contentWindow.location.href;
                    console.log('Iframe URL: ' + iframeUrl);
                    if (iframeUrl.includes('/academic-coordinator/home')) {
                        document.getElementById('home-menu-item').classList.add('active');
                        console.log('Added active class to home-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Dashboard';
                    } else if (iframeUrl.includes('/academic-coordinator/class-schedule')) {
                        document.getElementById('schedule-menu-item').classList.add('active');
                        console.log('Added active class to schedule-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Classes Schedule';
                    } else if (iframeUrl.includes('/academic-coordinator/class-history')) {
                        document.getElementById('history-menu-item').classList.add('active');
                        console.log('Added active class to history-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Classes History';
                    } 
                } catch (error) {
                    console.error('Cannot access iframe content due to cross-origin restrictions.');
                }
            };
        });    

        function toggleSidebar() {
            console.log('toggleSidebar');
            const sidebar = document.getElementById('sidebar'); 
            if (sidebar) {  
                sidebar.classList.toggle('collapsed'); 
                sidebar.classList.toggle('collapsed2'); 
                console.log('Sidebar collapsed state toggled');
            } else {
                console.error('Sidebar element not found!');
            }
        }

        function handleClickOutside(event) {
            console.log('handleClickOutside');
            const sidebar = document.getElementById('sidebar');
            if (sidebar && !sidebar.contains(event.target)) {
                sidebar.classList.add('collapsed'); 
            }
        }

        document.addEventListener('click', handleClickOutside);

        const stuMenuToggle = document.getElementById('stu-menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const iframeUrl = document.getElementById('iframeUrl');

        if (stuMenuToggle) {
            stuMenuToggle.addEventListener('click', function(event) {
                event.stopPropagation(); 
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('collapsed2');
            });
        }


    </script>
</html>
@endsection