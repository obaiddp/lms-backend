@extends('frontend.layouts.app')
@section('content2')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/dashboard.css') }}">
    <style>
        iframe body{
            background: transparent !important;
        }
    </style>
</head> 
<body>
    <div class="student-new-dashboard">
        <div class="student-sidebar collapsed collapsed2" id="sidebar">
            <div class="stu-logo">
                <a href="{{ route('student.dashboard') }}">
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
                <a href="/student/home" target="iframeUrl" class="stu-menu-item" id="home-menu-item">
                    <div class="icon">
                        <!-- Home Icon SVG -->
                        <svg class="path svg1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M22 22L2 22" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M2 11L10.1259 4.49931C11.2216 3.62279 12.7784 3.62279 13.8741 4.49931L22 11" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15.5 5.5V3.5C15.5 3.22386 15.7239 3 16 3H18.5C18.7761 3 19 3.22386 19 3.5V8.5" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M4 22V9.5" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M20 22V9.5" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 22V17C15 15.5858 15 14.8787 14.5607 14.4393C14.1213 14 13.4142 14 12 14C10.5858 14 9.87868 14 9.43934 14.4393C9 14.8787 9 15.5858 9 17V22" stroke="#024a6a" stroke-width="1.5"></path> <path d="M14 9.5C14 10.6046 13.1046 11.5 12 11.5C10.8954 11.5 10 10.6046 10 9.5C10 8.39543 10.8954 7.5 12 7.5C13.1046 7.5 14 8.39543 14 9.5Z" stroke="#024a6a" stroke-width="1.5"></path> </g></svg>
                    </div>
                    <span>Home</span>
                </a>
                <a href="/student/my-learning" target="iframeUrl" class="stu-menu-item" id="course-menu-item">
                    <div class="icon">
                        <!-- Courses Icon SVG -->
                        <svg class="path path-fill svg2" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="#024a6a" d="M15 4.7v-0.7c-1.159-1.163-2.734-1.91-4.484-1.999-0.112-0.012-0.222-0.018-0.334-0.018-0.874 0-1.657 0.394-2.179 1.013-0.556-0.617-1.357-1.007-2.249-1.007-0.090 0-0.178 0.004-0.266 0.012-1.754 0.089-3.33 0.836-4.488 1.999l-0 0.7-1 0.3v10l6.7-1.4 0.3 0.4h2l0.3-0.4 6.7 1.4v-10zM5.48 11.31c-1.275 0.037-2.467 0.358-3.526 0.902l0.046-7.792c0.885-0.835 2.066-1.365 3.369-1.42 0.806 0.054 1.534 0.303 2.159 0.701l-0.019 7.869c-0.555-0.166-1.193-0.262-1.854-0.262-0.062 0-0.124 0.001-0.185 0.003zM14 12.19c-1.013-0.522-2.205-0.843-3.468-0.88-0.056-0.001-0.108-0.002-0.161-0.002-0.66 0-1.297 0.096-1.899 0.274l0.047-7.902c0.601-0.381 1.322-0.627 2.096-0.679 1.324 0.055 2.501 0.586 3.386 1.422l-0.003 7.768z"></path> </g></svg>
                    </div>
                    <span>My Courses</span>
                </a>
                <a href="/student/purchase-list" target="iframeUrl" class="stu-menu-item" id="product-menu-item">
                    <div class="icon" style="margin-bottom: -0.4rem;">
                        <!-- Products Icon SVG -->
                        <svg class="path-fill svg3" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.05 12.082C13.05 11.6678 12.7142 11.332 12.3 11.332C11.8858 11.332 11.55 11.6678 11.55 12.082H13.05ZM12.3 19H11.55C11.55 19.249 11.6736 19.4818 11.8799 19.6213C12.0862 19.7608 12.3483 19.7888 12.5794 19.696L12.3 19ZM18.044 16.694L18.3234 17.39C18.6077 17.2759 18.794 17.0003 18.794 16.694H18.044ZM18.794 11.9C18.794 11.4858 18.4582 11.15 18.044 11.15C17.6298 11.15 17.294 11.4858 17.294 11.9H18.794ZM12.7554 11.4861C12.4263 11.2346 11.9556 11.2975 11.7041 11.6266C11.4526 11.9557 11.5155 12.4264 11.8446 12.6779L12.7554 11.4861ZM14.348 13.647L13.8926 14.2429C14.1156 14.4133 14.415 14.445 14.6687 14.325L14.348 13.647ZM18.3617 12.578C18.7361 12.4008 18.8961 11.9537 18.719 11.5793C18.5418 11.2049 18.0947 11.0449 17.7203 11.222L18.3617 12.578ZM12.0206 11.386C11.6362 11.5403 11.4497 11.977 11.604 12.3614C11.7583 12.7458 12.195 12.9323 12.5794 12.778L12.0206 11.386ZM18.3234 10.472C18.7078 10.3177 18.8943 9.88097 18.74 9.49658C18.5857 9.11219 18.149 8.92567 17.7646 9.07999L18.3234 10.472ZM17.7647 10.472C18.1491 10.6263 18.5858 10.4397 18.74 10.0553C18.8943 9.6709 18.7077 9.23421 18.3233 9.07995L17.7647 10.472ZM12.5793 6.77495C12.1949 6.62069 11.7582 6.80727 11.604 7.19168C11.4497 7.5761 11.6363 8.01279 12.0207 8.16705L12.5793 6.77495ZM18.4115 9.12322C18.0505 8.92024 17.5932 9.0484 17.3902 9.40947C17.1872 9.77054 17.3154 10.2278 17.6765 10.4308L18.4115 9.12322ZM20.095 10.93L20.4153 11.6082C20.669 11.4884 20.8346 11.237 20.8445 10.9566C20.8545 10.6762 20.7071 10.4137 20.4625 10.2762L20.095 10.93ZM17.7207 11.2218C17.3462 11.3987 17.1859 11.8457 17.3628 12.2203C17.5397 12.5948 17.9867 12.7551 18.3613 12.5782L17.7207 11.2218ZM17.4849 9.27273C17.207 9.57984 17.2306 10.0541 17.5377 10.3321C17.8448 10.61 18.3191 10.5864 18.5971 10.2793L17.4849 9.27273ZM20.5 7.059L21.0561 7.56227C21.2259 7.37459 21.2897 7.11388 21.2255 6.86901C21.1614 6.62413 20.9781 6.42812 20.738 6.34778L20.5 7.059ZM14.348 5L14.586 4.28878C14.2926 4.19056 13.9689 4.28268 13.7711 4.52071L14.348 5ZM11.7181 6.99171C11.4534 7.31031 11.4971 7.78317 11.8157 8.04787C12.1343 8.31258 12.6072 8.26889 12.8719 7.95029L11.7181 6.99171ZM6.27168 9.07995C5.88727 9.23421 5.70069 9.6709 5.85495 10.0553C6.00921 10.4397 6.4459 10.6263 6.83032 10.472L6.27168 9.07995ZM12.5743 8.16705C12.9587 8.01279 13.1453 7.5761 12.991 7.19168C12.8368 6.80727 12.4001 6.62069 12.0157 6.77495L12.5743 8.16705ZM6.91853 10.4298C7.2796 10.2268 7.40776 9.76954 7.20478 9.40847C7.00179 9.0474 6.54454 8.91924 6.18347 9.12222L6.91853 10.4298ZM4.5 10.929L4.13247 10.2752C3.88802 10.4126 3.74065 10.675 3.75046 10.9552C3.76027 11.2355 3.92561 11.4869 4.17908 11.6069L4.5 10.929ZM6.23008 12.5779C6.60445 12.7551 7.05163 12.5953 7.22887 12.2209C7.40611 11.8465 7.2463 11.3994 6.87192 11.2221L6.23008 12.5779ZM6.83042 9.07999C6.44603 8.92567 6.00931 9.11219 5.85499 9.49658C5.70067 9.88097 5.88719 10.3177 6.27158 10.472L6.83042 9.07999ZM12.0156 12.778C12.4 12.9323 12.8367 12.7458 12.991 12.3614C13.1453 11.977 12.9588 11.5403 12.5744 11.386L12.0156 12.778ZM5.9907 10.2746C6.26604 10.584 6.74011 10.6116 7.04956 10.3363C7.35901 10.061 7.38665 9.58689 7.1113 9.27744L5.9907 10.2746ZM4.5 7.471L4.20362 6.78205C3.98226 6.87727 3.82014 7.07305 3.76786 7.30828C3.71558 7.54351 3.77951 7.78954 3.9397 7.96956L4.5 7.471ZM10.244 5L10.8211 4.52099C10.6087 4.26508 10.2531 4.17962 9.94762 4.31105L10.244 5ZM11.7179 7.95001C11.9824 8.26874 12.4553 8.31265 12.774 8.0481C13.0927 7.78355 13.1367 7.31071 12.8721 6.99199L11.7179 7.95001ZM13.045 12.083C13.045 11.6688 12.7092 11.333 12.295 11.333C11.8808 11.333 11.545 11.6688 11.545 12.083H13.045ZM12.295 19L12.0156 19.696C12.2467 19.7888 12.5088 19.7608 12.7151 19.6213C12.9214 19.4818 13.045 19.249 13.045 19H12.295ZM6.551 16.694H5.801C5.801 17.0003 5.9873 17.2759 6.27158 17.39L6.551 16.694ZM7.301 11.9C7.301 11.4858 6.96521 11.15 6.551 11.15C6.13679 11.15 5.801 11.4858 5.801 11.9H7.301ZM12.75 12.6782C13.0793 12.427 13.1425 11.9563 12.8912 11.627C12.64 11.2977 12.1693 11.2345 11.84 11.4858L12.75 12.6782ZM10.244 13.647L9.92328 14.325C10.1768 14.4449 10.476 14.4134 10.699 14.2432L10.244 13.647ZM6.87172 11.222C6.49729 11.0449 6.05016 11.2049 5.87303 11.5793C5.6959 11.9537 5.85585 12.4008 6.23028 12.578L6.87172 11.222ZM11.55 12.082V19H13.05V12.082H11.55ZM12.5794 19.696L18.3234 17.39L17.7646 15.998L12.0206 18.304L12.5794 19.696ZM18.794 16.694V11.9H17.294V16.694H18.794ZM11.8446 12.6779L13.8926 14.2429L14.8034 13.0511L12.7554 11.4861L11.8446 12.6779ZM14.6687 14.325L18.3617 12.578L17.7203 11.222L14.0273 12.969L14.6687 14.325ZM12.5794 12.778L18.3234 10.472L17.7646 9.07999L12.0206 11.386L12.5794 12.778ZM18.3233 9.07995L12.5793 6.77495L12.0207 8.16705L17.7647 10.472L18.3233 9.07995ZM17.6765 10.4308L19.7275 11.5838L20.4625 10.2762L18.4115 9.12322L17.6765 10.4308ZM19.7747 10.2518L17.7207 11.2218L18.3613 12.5782L20.4153 11.6082L19.7747 10.2518ZM18.5971 10.2793L21.0561 7.56227L19.9439 6.55573L17.4849 9.27273L18.5971 10.2793ZM20.738 6.34778L14.586 4.28878L14.11 5.71122L20.262 7.77022L20.738 6.34778ZM13.7711 4.52071L11.7181 6.99171L12.8719 7.95029L14.9249 5.47929L13.7711 4.52071ZM6.83032 10.472L12.5743 8.16705L12.0157 6.77495L6.27168 9.07995L6.83032 10.472ZM6.18347 9.12222L4.13247 10.2752L4.86753 11.5828L6.91853 10.4298L6.18347 9.12222ZM4.17908 11.6069L6.23008 12.5779L6.87192 11.2221L4.82092 10.2511L4.17908 11.6069ZM6.27158 10.472L12.0156 12.778L12.5744 11.386L6.83042 9.07999L6.27158 10.472ZM7.1113 9.27744L5.0603 6.97244L3.9397 7.96956L5.9907 10.2746L7.1113 9.27744ZM4.79638 8.15995L10.5404 5.68895L9.94762 4.31105L4.20362 6.78205L4.79638 8.15995ZM9.6669 5.47901L11.7179 7.95001L12.8721 6.99199L10.8211 4.52099L9.6669 5.47901ZM11.545 12.083V19H13.045V12.083H11.545ZM12.5744 18.304L6.83042 15.998L6.27158 17.39L12.0156 19.696L12.5744 18.304ZM7.301 16.694V11.9H5.801V16.694H7.301ZM11.84 11.4858L9.78904 13.0508L10.699 14.2432L12.75 12.6782L11.84 11.4858ZM10.5647 12.969L6.87172 11.222L6.23028 12.578L9.92328 14.325L10.5647 12.969Z" fill="#024a6a"></path></svg>
                    </div>
                    <span>My Products</span>
                </a>
                <a href="/student/class-schedule" target="iframeUrl" class="stu-menu-item" id="schedule-menu-item">
                    <div class="icon">
                        <!-- Schedule Icon SVG -->
                        <svg class="path svg4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 9H21M7 3V5M17 3V5M6 12H8M11 12H13M16 12H18M6 15H8M11 15H13M16 15H18M6 18H8M11 18H13M16 18H18M6.2 21H17.8C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V8.2C21 7.07989 21 6.51984 20.782 6.09202C20.5903 5.71569 20.2843 5.40973 19.908 5.21799C19.4802 5 18.9201 5 17.8 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21Z" stroke="#024a6a" stroke-width="2" stroke-linecap="round"></path> </g></svg>
                    </div>
                    <span>Classes Schedule</span>
                </a>
                <a href="/student/class-history" target="iframeUrl" class="stu-menu-item" id="history-menu-item">
                    <div class="icon">
                        <!-- History Icon SVG -->
                        <svg class="path-fill stroke svg5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 5.67541V3C3 2.44772 2.55228 2 2 2C1.44772 2 1 2.44772 1 3V7C1 8.10457 1.89543 9 3 9H7C7.55229 9 8 8.55229 8 8C8 7.44772 7.55229 7 7 7H4.52186C4.54218 6.97505 4.56157 6.94914 4.57995 6.92229C5.621 5.40094 7.11009 4.22911 8.85191 3.57803C10.9074 2.80968 13.173 2.8196 15.2217 3.6059C17.2704 4.3922 18.9608 5.90061 19.9745 7.8469C20.9881 9.79319 21.2549 12.043 20.7247 14.1724C20.1945 16.3018 18.9039 18.1638 17.0959 19.4075C15.288 20.6513 13.0876 21.1909 10.9094 20.9247C8.73119 20.6586 6.72551 19.605 5.27028 17.9625C4.03713 16.5706 3.27139 14.8374 3.06527 13.0055C3.00352 12.4566 2.55674 12.0079 2.00446 12.0084C1.45217 12.0088 0.995668 12.4579 1.04626 13.0078C1.25994 15.3309 2.2082 17.5356 3.76666 19.2946C5.54703 21.3041 8.00084 22.5931 10.6657 22.9188C13.3306 23.2444 16.0226 22.5842 18.2345 21.0626C20.4464 19.541 22.0254 17.263 22.6741 14.6578C23.3228 12.0526 22.9963 9.30013 21.7562 6.91897C20.5161 4.53782 18.448 2.69239 15.9415 1.73041C13.4351 0.768419 10.6633 0.756291 8.14853 1.69631C6.06062 2.47676 4.26953 3.86881 3 5.67541Z" fill="#024a6a"></path> <path d="M12 5C11.4477 5 11 5.44771 11 6V12.4667C11 12.4667 11 12.7274 11.1267 12.9235C11.2115 13.0898 11.3437 13.2344 11.5174 13.3346L16.1372 16.0019C16.6155 16.278 17.2271 16.1141 17.5032 15.6358C17.7793 15.1575 17.6155 14.546 17.1372 14.2698L13 11.8812V6C13 5.44772 12.5523 5 12 5Z" fill="#024a6a"></path> </g></svg>                    </div>
                    <span>Classes History</span>
                </a>
                <a href="/student/pending-payments" target="iframeUrl" class="stu-menu-item" id="payment-menu-item">
                    <div class="icon">
                        <!-- Payments Icon SVG -->
                        <svg class="path svg6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M2 12.6101H19" stroke="#024a6a" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M19 10.28V17.43C18.97 20.28 18.19 21 15.22 21H5.78003C2.76003 21 2 20.2501 2 17.2701V10.28C2 7.58005 2.63 6.71005 5 6.57005C5.24 6.56005 5.50003 6.55005 5.78003 6.55005H15.22C18.24 6.55005 19 7.30005 19 10.28Z" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M22 6.73V13.72C22 16.42 21.37 17.29 19 17.43V10.28C19 7.3 18.24 6.55 15.22 6.55H5.78003C5.50003 6.55 5.24 6.56 5 6.57C5.03 3.72 5.81003 3 8.78003 3H18.22C21.24 3 22 3.75 22 6.73Z" stroke="#024a6a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M5.25 17.8101H6.96997" stroke="#024a6a" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M9.10986 17.8101H12.5499" stroke="#024a6a" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </div>
                    <span>Pending Payment</span>
                </a>
                <a href="/student/profile" target="iframeUrl" class="stu-menu-item" id="profile-menu-item">
                    <div class="icon">
                        <!-- Profile Icon SVG -->
                        <svg class="fill stroke g svg7" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#024a6a" stroke="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>profile [#024a6a]</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Dribbble-Light-Preview" transform="translate(-420.000000, -2159.000000)" fill="#024a6a"> <g id="icons" transform="translate(56.000000, 160.000000)"> <path d="M374,2009 C371.794,2009 370,2007.206 370,2005 C370,2002.794 371.794,2001 374,2001 C376.206,2001 378,2002.794 378,2005 C378,2007.206 376.206,2009 374,2009 M377.758,2009.673 C379.124,2008.574 380,2006.89 380,2005 C380,2001.686 377.314,1999 374,1999 C370.686,1999 368,2001.686 368,2005 C368,2006.89 368.876,2008.574 370.242,2009.673 C366.583,2011.048 364,2014.445 364,2019 L366,2019 C366,2014 369.589,2011 374,2011 C378.411,2011 382,2014 382,2019 L384,2019 C384,2014.445 381.417,2011.048 377.758,2009.673" id="profile-[#024a6a]"> </path> </g> </g> </g> </g></svg>                    </div>
                    <span>Profile</span>
                </a>
                <a href="/student/address" target="iframeUrl" class="stu-menu-item" id="address-menu-item">
                    <div class="icon">
                        <!-- Location Icon SVG -->
                        <svg class="path svg8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="#024a6a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#024a6a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>                    </div>
                    <span>Address & Location</span>
                </a>
                <a href="/student/change-password" target="iframeUrl" class="stu-menu-item" id="password-menu-item">
                    <div class="icon">
                        <!-- Settings Icon SVG -->
                        <svg class="path-fill svg9" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M10.65 3L9.93163 3.53449L9.32754 5.54812L7.47651 4.55141L6.5906 4.68143L4.68141 6.59062L4.55139 7.47652L5.5481 9.32755L3.53449 9.93163L3 10.65V13.35L3.53449 14.0684L5.54811 14.6725L4.55142 16.5235L4.68144 17.4094L6.59063 19.3186L7.47653 19.4486L9.32754 18.4519L9.93163 20.4655L10.65 21H13.35L14.0684 20.4655L14.6725 18.4519L16.5235 19.4486L17.4094 19.3185L19.3186 17.4094L19.4486 16.5235L18.4519 14.6724L20.4655 14.0684L21 13.35V10.65L20.4655 9.93163L18.4519 9.32754L19.4486 7.47654L19.3186 6.59063L17.4094 4.68144L16.5235 4.55142L14.6725 5.54812L14.0684 3.53449L13.35 3H10.65ZM10.4692 6.96284L11.208 4.5H12.792L13.5308 6.96284L13.8753 7.0946C13.9654 7.12908 14.0543 7.16597 14.142 7.2052L14.4789 7.35598L16.7433 6.13668L17.8633 7.25671L16.644 9.52111L16.7948 9.85803C16.834 9.9457 16.8709 10.0346 16.9054 10.1247L17.0372 10.4692L19.5 11.208V12.792L17.0372 13.5308L16.9054 13.8753C16.8709 13.9654 16.834 14.0543 16.7948 14.1419L16.644 14.4789L17.8633 16.7433L16.7433 17.8633L14.4789 16.644L14.142 16.7948C14.0543 16.834 13.9654 16.8709 13.8753 16.9054L13.5308 17.0372L12.792 19.5H11.208L10.4692 17.0372L10.1247 16.9054C10.0346 16.8709 9.94569 16.834 9.85803 16.7948L9.52111 16.644L7.25671 17.8633L6.13668 16.7433L7.35597 14.4789L7.2052 14.142C7.16597 14.0543 7.12908 13.9654 7.0946 13.8753L6.96284 13.5308L4.5 12.792L4.5 11.208L6.96284 10.4692L7.0946 10.1247C7.12907 10.0346 7.16596 9.94571 7.20519 9.85805L7.35596 9.52113L6.13666 7.2567L7.25668 6.13667L9.5211 7.35598L9.85803 7.2052C9.9457 7.16597 10.0346 7.12908 10.1247 7.0946L10.4692 6.96284ZM14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C10.7574 14.25 9.75 13.2426 9.75 12C9.75 10.7574 10.7574 9.75 12 9.75C13.2426 9.75 14.25 10.7574 14.25 12ZM15.75 12C15.75 14.0711 14.0711 15.75 12 15.75C9.92893 15.75 8.25 14.0711 8.25 12C8.25 9.92893 9.92893 8.25 12 8.25C14.0711 8.25 15.75 9.92893 15.75 12Z" fill="#024a6a"></path> </g></svg>
                    </div>
                    <span>Change Password</span>
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
                        <div class="notification-btn">
                            <span class="notification-count">{{count($student_notifications) }}</span>
                            <!-- Notification Icon SVG -->
                            <svg width="1.4rem" height="1.4rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C10.8954 2 10 2.89543 10 4V4.5C7.51472 5.05719 6 7.16895 6 9.75V15L4 17V18H20V17L18 15V9.75C18 7.16895 16.4853 5.05719 14 4.5V4C14 2.89543 13.1046 2 12 2ZM12 22C13.1046 22 14 21.1046 14 20H10C10 21.1046 10.8954 22 12 22Z" fill="#024A6A"/>
                            </svg>
                            <div class="notification-list">
                                <div class="custom-scrollbar menu-notification-tab-content"> 
                                    @forelse ($student_notifications as $student_notification)
                                        <a href="{{route('notification.url', [$student_notification->uuid])}}" class="message-user-item cursor d-flex align-items-center">
                                            <div class="message-user-item-left">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="user-img-wrap position-relative radius-50">
                                                            <img src="{{ asset($student_notification->sender->image_path) }}"
                                                                alt="img" class="radius-50">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="color-heading font-14">
                                                            {{$student_notification->sender->name}}
                                                        </h6>
                                                        <p class="font-13">{{$student_notification->text}}</p>
                                                        <div class="font-11 color-gray mt-1">
                                                            {{@$student_notification->created_at->diffForHumans()}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="no-notification-found-box">
                                            <p class="text-center">{{__('No Data Found')}}</p>
                                        </div>
                                    @endforelse
                                </div>
                                <div  class="message-user-item cursor d-flex align-items-center">
                                    @if (count($student_notifications)) 
                                    <div class="dropdown-divider"></div>
                                        <form action="{{ route('notification.all-read') }}" method="POST"> @csrf
                                            <input type="hidden" name="type" value="3">
                                            <button type="submit"
                                                class="dropdown-item dropdown-footer">{{ __('Mark all as read') }}</button>
                                        </form> 
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <span>Hey, {{ auth::user()->name }}</span>
                </div>
            </div>
            <div class="stu-view-content" >
                <iframe src="/student/home" style="width: 100%;max-height: 200vh; min-height: 84vh; border: none !important;" id="iframeUrl" name="iframeUrl" class="iframe-content"></iframe>
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
            const urlParams = new URLSearchParams(window.location.search);
            const courseUrl = urlParams.get('course_url');

            if (courseUrl) {
                iframe.src = decodeURIComponent(courseUrl);
                // Remove the query parameter from the URL without reloading the page
                const newUrl = window.location.origin + window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }

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
                    if (iframeUrl.includes('/student/home')) {
                        document.getElementById('home-menu-item').classList.add('active');
                        console.log('Added active class to home-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Dashboard';
                    } else if (iframeUrl.includes('/student/my-learning')) {
                        document.getElementById('course-menu-item').classList.add('active');
                        console.log('Added active class to course-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'My Courses';
                    } else if (iframeUrl.includes('/student/purchase-list')) {
                        document.getElementById('product-menu-item').classList.add('active');
                        console.log('Added active class to product-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'My Products';
                    } else if (iframeUrl.includes('/student/class-schedule')) {
                        document.getElementById('schedule-menu-item').classList.add('active');
                        console.log('Added active class to schedule-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Classes Schedule';
                    } else if (iframeUrl.includes('/student/class-history')) {
                        document.getElementById('history-menu-item').classList.add('active');
                        console.log('Added active class to history-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Classes History';
                    } else if (iframeUrl.includes('/student/pending-payments')) {
                        document.getElementById('payment-menu-item').classList.add('active');
                        console.log('Added active class to payment-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Pending Payments';
                    } else if (iframeUrl.includes('/student/profile')) {
                        document.getElementById('profile-menu-item').classList.add('active');
                        console.log('Added active class to profile-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Profile';
                    } else if (iframeUrl.includes('/student/address')) {
                        document.getElementById('address-menu-item').classList.add('active');
                        console.log('Added active class to address-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Address';
                    } else if (iframeUrl.includes('/student/change-password')) {
                        document.getElementById('password-menu-item').classList.add('active');
                        console.log('Added active class to password-menu-item');
                        document.getElementById('navbar-header').innerHTML = 'Change Password';
                    } else if (iframeUrl.includes('/student/my-course/')) {
                        // const courseName = iframeUrl.split('/').pop().replace(/-/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());
                        // document.getElementById('course-menu-item').classList.add('active');
                        // console.log('Added active class to course-menu-item');
                        // document.getElementById('navbar-header').innerHTML = courseName;
                        document.getElementById('course-menu-item').classList.add('active');
                        const coursePart = iframeUrl.split('/student/my-course/')[1];
                        console.log(coursePart)
                        const courseName = coursePart.split(/[/?]/)[0];
                        console.log(courseName)
                        document.getElementById('navbar-header').innerHTML = courseName.replace(/-/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());
                    } else if(iframeUrl.includes('/student/viewFullResource/')){
                        document.getElementById('course-menu-item').classList.add('active');
                    }else if(iframeUrl.includes('/student/viewFullResourceOther/')){
                        document.getElementById('course-menu-item').classList.add('active');
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
</body>
</html>
@endsection