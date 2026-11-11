@extends('frontend.layouts.app')
@section('content2')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/dashboard.css') }}">
</head> 

{{-- <div class="stu-banner">
    <img class="stu-banner-img" src="https://static.vecteezy.com/system/resources/thumbnails/002/294/862/small/digital-courses-web-banner-design-student-watching-online-courses-online-education-digital-classroom-e-learning-concept-header-or-footer-banner-free-vector.jpg" alt="">
</div> --}}


<div class="stu-grid">
    <input type="hidden" name="base_url" id="base_url" value="{{$base_url}}">
    <div class="stu-grid-enrolled-courses">
        <h4>Enrolled Courses</h4>
        <div class="stu-grid-course-card">
            <div class="stu-grid-course-card-item">
                <div class="stu-grid-course-card-item-progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                        <span class="progress-bar-text">75%</span>
                    </div>
                </div>
                <div class="stu-grid-course-card-item-details">
                    <h6 class="stu-grid-course-card-item-title">
                        Networking
                    </h6>
                    <div class="stu-grid-course-card-item-info">
                        <div class="stu-grid-course-card-item-duration">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                <path d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3.5 2.1a.5.5 0 0 1-.5.85l-4-2.4A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z"/>
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                            </svg>
                        </span>
                        <span class="text">5 Hours</span>
                        </div>
                        <div class="stu-grid-course-card-item-lessons">
                        <span>
                            <svg width="0.8rem" height="0.8rem" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" d="M15 4.7v-0.7c-1.159-1.163-2.734-1.91-4.484-1.999-0.112-0.012-0.222-0.018-0.334-0.018-0.874 0-1.657 0.394-2.179 1.013-0.556-0.617-1.357-1.007-2.249-1.007-0.090 0-0.178 0.004-0.266 0.012-1.754 0.089-3.33 0.836-4.488 1.999l-0 0.7-1 0.3v10l6.7-1.4 0.3 0.4h2l0.3-0.4 6.7 1.4v-10zM5.48 11.31c-1.275 0.037-2.467 0.358-3.526 0.902l0.046-7.792c0.885-0.835 2.066-1.365 3.369-1.42 0.806 0.054 1.534 0.303 2.159 0.701l-0.019 7.869c-0.555-0.166-1.193-0.262-1.854-0.262-0.062 0-0.124 0.001-0.185 0.003zM14 12.19c-1.013-0.522-2.205-0.843-3.468-0.88-0.056-0.001-0.108-0.002-0.161-0.002-0.66 0-1.297 0.096-1.899 0.274l0.047-7.902c0.601-0.381 1.322-0.627 2.096-0.679 1.324 0.055 2.501 0.586 3.386 1.422l-0.003 7.768z"></path> </g></svg>
                        </span>
                        <span class="text">10 Lessons</span>
                        </div>
                    </div>
                    <a class="stu-grid-course-card-item-continue-btn" href="http://127.0.0.1:8000/student/my-course/networking">Continue</a>
                </div>
            </div>
            <div class="stu-grid-course-card-item">
                <div class="stu-grid-course-card-item-progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                        <span class="progress-bar-text">75%</span>
                    </div>
                </div>
                <div class="stu-grid-course-card-item-details">
                    <h6 class="stu-grid-course-card-item-title">
                        Networking
                    </h6>
                    <div class="stu-grid-course-card-item-info">
                        <div class="stu-grid-course-card-item-duration">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                <path d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3.5 2.1a.5.5 0 0 1-.5.85l-4-2.4A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z"/>
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                            </svg>
                        </span>
                        <span class="text">5 Hours</span>
                        </div>
                        <div class="stu-grid-course-card-item-lessons">
                        <span>
                            <svg width="0.8rem" height="0.8rem" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" d="M15 4.7v-0.7c-1.159-1.163-2.734-1.91-4.484-1.999-0.112-0.012-0.222-0.018-0.334-0.018-0.874 0-1.657 0.394-2.179 1.013-0.556-0.617-1.357-1.007-2.249-1.007-0.090 0-0.178 0.004-0.266 0.012-1.754 0.089-3.33 0.836-4.488 1.999l-0 0.7-1 0.3v10l6.7-1.4 0.3 0.4h2l0.3-0.4 6.7 1.4v-10zM5.48 11.31c-1.275 0.037-2.467 0.358-3.526 0.902l0.046-7.792c0.885-0.835 2.066-1.365 3.369-1.42 0.806 0.054 1.534 0.303 2.159 0.701l-0.019 7.869c-0.555-0.166-1.193-0.262-1.854-0.262-0.062 0-0.124 0.001-0.185 0.003zM14 12.19c-1.013-0.522-2.205-0.843-3.468-0.88-0.056-0.001-0.108-0.002-0.161-0.002-0.66 0-1.297 0.096-1.899 0.274l0.047-7.902c0.601-0.381 1.322-0.627 2.096-0.679 1.324 0.055 2.501 0.586 3.386 1.422l-0.003 7.768z"></path> </g></svg>
                        </span>
                        <span class="text">10 Lessons</span>
                        </div>
                    </div>
                    <a class="stu-grid-course-card-item-continue-btn" href="http://127.0.0.1:8000/student/my-course/networking">Continue</a>
                </div>
            </div>
            <div class="stu-grid-course-card-item">
                <div class="stu-grid-course-card-item-progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                        <span class="progress-bar-text">75%</span>
                    </div>
                </div>
                <div class="stu-grid-course-card-item-details">
                    <h6 class="stu-grid-course-card-item-title">
                        Networking
                    </h6>
                    <div class="stu-grid-course-card-item-info">
                        <div class="stu-grid-course-card-item-duration">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                <path d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3.5 2.1a.5.5 0 0 1-.5.85l-4-2.4A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z"/>
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                            </svg>
                        </span>
                        <span class="text">5 Hours</span>
                        </div>
                        <div class="stu-grid-course-card-item-lessons">
                        <span>
                            <svg width="0.8rem" height="0.8rem" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" d="M15 4.7v-0.7c-1.159-1.163-2.734-1.91-4.484-1.999-0.112-0.012-0.222-0.018-0.334-0.018-0.874 0-1.657 0.394-2.179 1.013-0.556-0.617-1.357-1.007-2.249-1.007-0.090 0-0.178 0.004-0.266 0.012-1.754 0.089-3.33 0.836-4.488 1.999l-0 0.7-1 0.3v10l6.7-1.4 0.3 0.4h2l0.3-0.4 6.7 1.4v-10zM5.48 11.31c-1.275 0.037-2.467 0.358-3.526 0.902l0.046-7.792c0.885-0.835 2.066-1.365 3.369-1.42 0.806 0.054 1.534 0.303 2.159 0.701l-0.019 7.869c-0.555-0.166-1.193-0.262-1.854-0.262-0.062 0-0.124 0.001-0.185 0.003zM14 12.19c-1.013-0.522-2.205-0.843-3.468-0.88-0.056-0.001-0.108-0.002-0.161-0.002-0.66 0-1.297 0.096-1.899 0.274l0.047-7.902c0.601-0.381 1.322-0.627 2.096-0.679 1.324 0.055 2.501 0.586 3.386 1.422l-0.003 7.768z"></path> </g></svg>
                        </span>
                        <span class="text">10 Lessons</span>
                        </div>
                    </div>
                    <a class="stu-grid-course-card-item-continue-btn" href="http://127.0.0.1:8000/student/my-course/networking">Continue</a>
                </div>
            </div>
            <div class="stu-grid-course-card-item">
                <div class="stu-grid-course-card-item-progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                        <span class="progress-bar-text">75%</span>
                    </div>
                </div>
                <div class="stu-grid-course-card-item-details">
                    <h6 class="stu-grid-course-card-item-title">
                        Networking
                    </h6>
                    <div class="stu-grid-course-card-item-info">
                        <div class="stu-grid-course-card-item-duration">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                <path d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3.5 2.1a.5.5 0 0 1-.5.85l-4-2.4A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z"/>
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                            </svg>
                        </span>
                        <span class="text">5 Hours</span>
                        </div>
                        <div class="stu-grid-course-card-item-lessons">
                        <span>
                            <svg width="0.8rem" height="0.8rem" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" d="M15 4.7v-0.7c-1.159-1.163-2.734-1.91-4.484-1.999-0.112-0.012-0.222-0.018-0.334-0.018-0.874 0-1.657 0.394-2.179 1.013-0.556-0.617-1.357-1.007-2.249-1.007-0.090 0-0.178 0.004-0.266 0.012-1.754 0.089-3.33 0.836-4.488 1.999l-0 0.7-1 0.3v10l6.7-1.4 0.3 0.4h2l0.3-0.4 6.7 1.4v-10zM5.48 11.31c-1.275 0.037-2.467 0.358-3.526 0.902l0.046-7.792c0.885-0.835 2.066-1.365 3.369-1.42 0.806 0.054 1.534 0.303 2.159 0.701l-0.019 7.869c-0.555-0.166-1.193-0.262-1.854-0.262-0.062 0-0.124 0.001-0.185 0.003zM14 12.19c-1.013-0.522-2.205-0.843-3.468-0.88-0.056-0.001-0.108-0.002-0.161-0.002-0.66 0-1.297 0.096-1.899 0.274l0.047-7.902c0.601-0.381 1.322-0.627 2.096-0.679 1.324 0.055 2.501 0.586 3.386 1.422l-0.003 7.768z"></path> </g></svg>
                        </span>
                        <span class="text">10 Lessons</span>
                        </div>
                    </div>
                    <a class="stu-grid-course-card-item-continue-btn" href="http://127.0.0.1:8000/student/my-course/networking">Continue</a>
                </div>
            </div>
            <div class="stu-grid-course-card-item">
                <div class="stu-grid-course-card-item-progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                        <span class="progress-bar-text">75%</span>
                    </div>
                </div>
                <div class="stu-grid-course-card-item-details">
                    <h6 class="stu-grid-course-card-item-title">
                        Networking
                    </h6>
                    <div class="stu-grid-course-card-item-info">
                        <div class="stu-grid-course-card-item-duration">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                <path d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3.5 2.1a.5.5 0 0 1-.5.85l-4-2.4A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z"/>
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                            </svg>
                        </span>
                        <span class="text">5 Hours</span>
                        </div>
                        <div class="stu-grid-course-card-item-lessons">
                        <span>
                            <svg width="0.8rem" height="0.8rem" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" d="M15 4.7v-0.7c-1.159-1.163-2.734-1.91-4.484-1.999-0.112-0.012-0.222-0.018-0.334-0.018-0.874 0-1.657 0.394-2.179 1.013-0.556-0.617-1.357-1.007-2.249-1.007-0.090 0-0.178 0.004-0.266 0.012-1.754 0.089-3.33 0.836-4.488 1.999l-0 0.7-1 0.3v10l6.7-1.4 0.3 0.4h2l0.3-0.4 6.7 1.4v-10zM5.48 11.31c-1.275 0.037-2.467 0.358-3.526 0.902l0.046-7.792c0.885-0.835 2.066-1.365 3.369-1.42 0.806 0.054 1.534 0.303 2.159 0.701l-0.019 7.869c-0.555-0.166-1.193-0.262-1.854-0.262-0.062 0-0.124 0.001-0.185 0.003zM14 12.19c-1.013-0.522-2.205-0.843-3.468-0.88-0.056-0.001-0.108-0.002-0.161-0.002-0.66 0-1.297 0.096-1.899 0.274l0.047-7.902c0.601-0.381 1.322-0.627 2.096-0.679 1.324 0.055 2.501 0.586 3.386 1.422l-0.003 7.768z"></path> </g></svg>
                        </span>
                        <span class="text">10 Lessons</span>
                        </div>
                    </div>
                    <a class="stu-grid-course-card-item-continue-btn" href="http://127.0.0.1:8000/student/my-course/networking">Continue</a>
                </div>
            </div>
        </div>
    </div>

    <div class="stu-grid-today-classes">
        <h4>Today's Live Class</h4>
        <div class="stu-grid-today-classes-card">
            <div class="stu-grid-today-classes-card-item">
                <div class="stu-grid-today-classes-card-heading">
                    Course Name
                </div>
                <div class="stu-grid-today-classes-card-text">
                    Course
                </div>
            </div>
            <div class="stu-grid-today-classes-card-item">
                <div class="stu-grid-today-classes-card-heading">
                    Instructor Name
                </div>
                <div class="stu-grid-today-classes-card-text">
                    Instructor
                </div>
            </div>
            <div class="stu-grid-today-classes-card-item">
                <div class="stu-grid-today-classes-card-heading">
                    Date / Time
                </div>
                <div class="stu-grid-today-classes-card-text">
                    01-11-2024 12:33 PM
                </div>
            </div>
            <a class="stu-grid-today-classes-card-join-btn">Join</a>
        </div>
    </div>

    <div class="stu-grid-chart-classes">
        <h4>Monthly Classes</h4>
        <div class="stu-grid-chart-classes-card">
            <div id="container"></div>
        </div>
    </div>
    <div class="stu-grid-leaderboard">
        <h4>Leaderboard</h4>
        <div class="stu-grid-leaderboard-card">
            <div class="stu-grid-leaderboard-card-item">
                <div class="stu-grid-leaderboard-card-item-rank">
                    1
                </div>
                <div class="d-flex align-items-center justify-content-between">
                <div class="stu-grid-leaderboard-card-item-img">
                    <img src="http://127.0.0.1:8000/uploads/course/1729259354-6LsoRB2BGr.png" alt="student">
                </div>
                <div class="stu-grid-leaderboard-card-item-course">
                    Networking
                </div>
                </div>
                <div class="stu-grid-leaderboard-card-item-rating">
                    ★★★★★
                </div>
            </div>
            <div class="stu-grid-leaderboard-card-item">
                <div class="stu-grid-leaderboard-card-item-rank">
                    2
                </div>
                <div class="d-flex align-items-center justify-content-between">
                <div class="stu-grid-leaderboard-card-item-img">
                    <img src="http://127.0.0.1:8000/uploads/course/1729259354-6LsoRB2BGr.png" alt="student">
                </div>
                <div class="stu-grid-leaderboard-card-item-course">
                    Networking
                </div>
                </div>
                <div class="stu-grid-leaderboard-card-item-rating">
                    ★★★★★
                </div>
            </div>
            <div class="stu-grid-leaderboard-card-item">
                <div class="stu-grid-leaderboard-card-item-rank">
                    3
                </div>
                <div class="d-flex align-items-center justify-content-between">
                <div class="stu-grid-leaderboard-card-item-img">
                    <img src="http://127.0.0.1:8000/uploads/course/1729259354-6LsoRB2BGr.png" alt="student">
                </div>
                <div class="stu-grid-leaderboard-card-item-course">
                    Networking
                </div>
                </div>
                <div class="stu-grid-leaderboard-card-item-rating">
                    ★★★★★
                </div>
            </div>
        </div>
    </div>
    
    <div class="stu-grid-blogs">
        <h4>Latest Blogs</h4>
        <div class="stu-grid-blogs-card">

            <div class="stu-grid-blogs-card-item">
                <div class="stu-grid-blogs-card-item-img">
                    <img src="http://127.0.0.1:8000/uploads/course/1729259354-6LsoRB2BGr.png" alt="student">
                </div>
                <div class="stu-grid-blogs-card-item-data">
                    <div class="stu-grid-blogs-card-item-date">
                        01-11-2024
                    </div>
                    <div class="stu-grid-blogs-card-item-title">
                        Blog Title
                    </div>
                    <div class="stu-grid-blogs-card-item-desc">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                    </div>
                </div>
            </div>

            <div class="stu-grid-blogs-card-item">
                <div class="stu-grid-blogs-card-item-img">
                    <img src="http://127.0.0.1:8000/uploads/course/1729259354-6LsoRB2BGr.png" alt="student">
                </div>
                <div class="stu-grid-blogs-card-item-data">
                    <div class="stu-grid-blogs-card-item-date">
                        01-11-2024
                    </div>
                    <div class="stu-grid-blogs-card-item-title">
                        Blog Title
                    </div>
                    <div class="stu-grid-blogs-card-item-desc">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                    </div>
                </div>
            </div>

            <div class="stu-grid-blogs-card-item">
                <div class="stu-grid-blogs-card-item-img">
                    <img src="http://127.0.0.1:8000/uploads/course/1729259354-6LsoRB2BGr.png" alt="student">
                </div>
                <div class="stu-grid-blogs-card-item-data">
                    <div class="stu-grid-blogs-card-item-date">
                        01-11-2024
                    </div>
                    <div class="stu-grid-blogs-card-item-title">
                        Blog Title
                    </div>
                    <div class="stu-grid-blogs-card-item-desc">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                    </div>
                </div>
            </div>

            <div class="stu-grid-blogs-card-item">
                <div class="stu-grid-blogs-card-item-img">
                    <img src="http://127.0.0.1:8000/uploads/course/1729259354-6LsoRB2BGr.png" alt="student">
                </div>
                <div class="stu-grid-blogs-card-item-data">
                    <div class="stu-grid-blogs-card-item-date">
                        01-11-2024
                    </div>
                    <div class="stu-grid-blogs-card-item-title">
                        Blog Title
                    </div>
                    <div class="stu-grid-blogs-card-item-desc">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                    </div>
                </div>
            </div>

        </div>  
    </div>
    <div class="stu-grid-resources">
        <h4>Latest Resources</h4>
        <div class="stu-grid-resources-card">
            <!-- Youtube -->
            <div class="stu-grid-resources-card-item">
                <div class="stu-grid-resources-card-item-img">
                    <svg width="3rem" height="3rem" viewBox="0 -3 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>youtube [#024a6a]</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Dribbble-Light-Preview" transform="translate(-300.000000, -7442.000000)" fill="#024a6a"> <g id="icons" transform="translate(56.000000, 160.000000)"> <path d="M251.988432,7291.58588 L251.988432,7285.97425 C253.980638,7286.91168 255.523602,7287.8172 257.348463,7288.79353 C255.843351,7289.62824 253.980638,7290.56468 251.988432,7291.58588 M263.090998,7283.18289 C262.747343,7282.73013 262.161634,7282.37809 261.538073,7282.26141 C259.705243,7281.91336 248.270974,7281.91237 246.439141,7282.26141 C245.939097,7282.35515 245.493839,7282.58153 245.111335,7282.93357 C243.49964,7284.42947 244.004664,7292.45151 244.393145,7293.75096 C244.556505,7294.31342 244.767679,7294.71931 245.033639,7294.98558 C245.376298,7295.33761 245.845463,7295.57995 246.384355,7295.68865 C247.893451,7296.0008 255.668037,7296.17532 261.506198,7295.73552 C262.044094,7295.64178 262.520231,7295.39147 262.895762,7295.02447 C264.385932,7293.53455 264.28433,7285.06174 263.090998,7283.18289" id="youtube-[#024a6a]"> </path> </g> </g> </g> </g></svg>
                </div>
                <div class="stu-grid-resources-card-item-title">
                    Youtube Resource
                </div>
                <a href="#" class="stu-grid-resources-card-item-view-btn">View</a>
            </div>
            <!-- PPT -->
            <div class="stu-grid-resources-card-item">
                <div class="stu-grid-resources-card-item-img">
                    <svg width="2.6rem" height="2.6rem" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 8H3.5C3.77614 8 4 7.77614 4 7.5C4 7.22386 3.77614 7 3.5 7H3V8Z" fill="#024a6a"></path> <path d="M7 8H7.5C7.77614 8 8 7.77614 8 7.5C8 7.22386 7.77614 7 7.5 7H7V8Z" fill="#024a6a"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M1 1.5C1 0.671573 1.67157 0 2.5 0H10.7071L14 3.29289V13.5C14 14.3284 13.3284 15 12.5 15H2.5C1.67157 15 1 14.3284 1 13.5V1.5ZM2 6H3.5C4.32843 6 5 6.67157 5 7.5C5 8.32843 4.32843 9 3.5 9H3V11H2V6ZM6 6H7.5C8.32843 6 9 6.67157 9 7.5C9 8.32843 8.32843 9 7.5 9H7V11H6V6ZM11 11H12V7H13V6H10V7H11V11Z" fill="#024a6a"></path> </g></svg>
                </div>
                <div class="stu-grid-resources-card-item-title">
                        PPT Resource
                </div>
                <a href="#" class="stu-grid-resources-card-item-view-btn">View</a>
            </div>
            <!-- Text -->
            <div class="stu-grid-resources-card-item">
                <div class="stu-grid-resources-card-item-img">
                    <svg width="2.8rem" height="2.8rem" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M15 1H1V15H15V1ZM3 3V7H5V5H7V11H5V13H11V11H9V5H11V7H13V3H3Z" fill="#024a6a"></path> </g></svg>
                </div>
                <div class="stu-grid-resources-card-item-title">
                    Text Resource
                </div>
                <a href="#" class="stu-grid-resources-card-item-view-btn">View</a>
            </div>
            <!-- Audio -->
            <div class="stu-grid-resources-card-item">
                <div class="stu-grid-resources-card-item-img">
                    <svg width="3rem" height="3rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9.66984 13.9219C8.92984 13.9219 8.33984 14.5219 8.33984 15.2619C8.33984 16.0019 8.93984 16.5919 9.66984 16.5919C10.4098 16.5919 11.0098 15.9919 11.0098 15.2619C11.0098 14.5219 10.4098 13.9219 9.66984 13.9219Z" fill="#024a6a"></path> <path d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2ZM17.12 9.8C17.12 10.41 16.86 10.95 16.42 11.27C16.14 11.47 15.8 11.58 15.44 11.58C15.23 11.58 15.02 11.54 14.8 11.47L12.51 10.71C12.5 10.71 12.48 10.7 12.47 10.69V15.25C12.47 16.79 11.21 18.05 9.67 18.05C8.13 18.05 6.87 16.79 6.87 15.25C6.87 13.71 8.13 12.45 9.67 12.45C10.16 12.45 10.61 12.59 11.01 12.8V8.63V8.02C11.01 7.41 11.27 6.87 11.71 6.55C12.16 6.23 12.75 6.15 13.33 6.35L15.62 7.11C16.48 7.4 17.13 8.3 17.13 9.2V9.8H17.12Z" fill="#024a6a"></path> </g></svg>
                </div>
                <div class="stu-grid-resources-card-item-title">
                    Audio Resource
                </div>
                <a href="#" class="stu-grid-resources-card-item-view-btn">View</a>
            </div>
            <!-- Video -->
            <div class="stu-grid-resources-card-item">
                <div class="stu-grid-resources-card-item-img">
                    <svg width="3rem" height="3rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8.50989 2.00001H15.49C15.7225 1.99995 15.9007 1.99991 16.0565 2.01515C17.1643 2.12352 18.0711 2.78958 18.4556 3.68678H5.54428C5.92879 2.78958 6.83555 2.12352 7.94337 2.01515C8.09917 1.99991 8.27741 1.99995 8.50989 2.00001Z" fill="#024a6a"></path> <path d="M6.31052 4.72312C4.91989 4.72312 3.77963 5.56287 3.3991 6.67691C3.39117 6.70013 3.38356 6.72348 3.37629 6.74693C3.77444 6.62636 4.18881 6.54759 4.60827 6.49382C5.68865 6.35531 7.05399 6.35538 8.64002 6.35547H15.5321C17.1181 6.35538 18.4835 6.35531 19.5639 6.49382C19.9833 6.54759 20.3977 6.62636 20.7958 6.74693C20.7886 6.72348 20.781 6.70013 20.773 6.67691C20.3925 5.56287 19.2522 4.72312 17.8616 4.72312H6.31052Z" fill="#024a6a"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3276 7.54204H8.67239C5.29758 7.54204 3.61017 7.54204 2.66232 8.52887C1.71447 9.5157 1.93748 11.0403 2.38351 14.0896L2.80648 16.9811C3.15626 19.3724 3.33115 20.568 4.22834 21.284C5.12553 22 6.4488 22 9.09534 22H14.9046C17.5512 22 18.8745 22 19.7717 21.284C20.6689 20.568 20.8437 19.3724 21.1935 16.9811L21.6165 14.0896C22.0625 11.0404 22.2855 9.51569 21.3377 8.52887C20.3898 7.54204 18.7024 7.54204 15.3276 7.54204ZM14.5812 15.7942C15.1396 15.4481 15.1396 14.5519 14.5812 14.2058L11.2096 12.1156C10.6669 11.7792 10 12.2171 10 12.9099V17.0901C10 17.7829 10.6669 18.2208 11.2096 17.8844L14.5812 15.7942Z" fill="#024a6a"></path> </g></svg>
                </div>
                <div class="stu-grid-resources-card-item-title">
                    Video Resource
                </div>
                <a href="#" class="stu-grid-resources-card-item-view-btn">View</a>
            </div>
            <!-- Pdf -->
            <div class="stu-grid-resources-card-item">
                <div class="stu-grid-resources-card-item-img">
                    <svg width="2.8rem" height="2.8rem" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3.5 8H3V7H3.5C3.77614 7 4 7.22386 4 7.5C4 7.77614 3.77614 8 3.5 8Z" fill="#024a6a"></path> <path d="M7 10V7H7.5C7.77614 7 8 7.22386 8 7.5V9.5C8 9.77614 7.77614 10 7.5 10H7Z" fill="#024a6a"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M1 1.5C1 0.671573 1.67157 0 2.5 0H10.7071L14 3.29289V13.5C14 14.3284 13.3284 15 12.5 15H2.5C1.67157 15 1 14.3284 1 13.5V1.5ZM3.5 6H2V11H3V9H3.5C4.32843 9 5 8.32843 5 7.5C5 6.67157 4.32843 6 3.5 6ZM7.5 6H6V11H7.5C8.32843 11 9 10.3284 9 9.5V7.5C9 6.67157 8.32843 6 7.5 6ZM10 11V6H13V7H11V8H12V9H11V11H10Z" fill="#024a6a"></path> </g></svg>
                </div>
                <div class="stu-grid-resources-card-item-title">
                    Pdf Resource
                </div>
                <a href="#" class="stu-grid-resources-card-item-view-btn">View</a>
            </div>
            <!-- Image -->
            <div class="stu-grid-resources-card-item">
                <div class="stu-grid-resources-card-item-img">
                    <svg width="2.8rem" height="2.8rem" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>image-picture</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-362.000000, -101.000000)" fill="#024a6a"> <path d="M392,129 C392,130.104 391.104,131 390,131 L384.832,131 L377.464,123.535 L386,114.999 L392,120.999 L392,129 L392,129 Z M366,131 C364.896,131 364,130.104 364,129 L364,128.061 L371.945,120.945 L382.001,131 L366,131 L366,131 Z M370,105 C372.209,105 374,106.791 374,109 C374,111.209 372.209,113 370,113 C367.791,113 366,111.209 366,109 C366,106.791 367.791,105 370,105 L370,105 Z M390,101 L366,101 C363.791,101 362,102.791 362,105 L362,129 C362,131.209 363.791,133 366,133 L390,133 C392.209,133 394,131.209 394,129 L394,105 C394,102.791 392.209,101 390,101 L390,101 Z M370,111 C371.104,111 372,110.104 372,109 C372,107.896 371.104,107 370,107 C368.896,107 368,107.896 368,109 C368,110.104 368.896,111 370,111 L370,111 Z" id="image-picture" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
                </div>
                <div class="stu-grid-resources-card-item-title">
                    Image Resource
                </div>
                <a href="#" class="stu-grid-resources-card-item-view-btn">View</a>
            </div>
        </div>  
    </div>
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
<script>
    var base_url = $('#base_url').val();

$(document).on("click", ".create_zoom_meeting_link", function(event){
    event.preventDefault();
    meetingId = $(this).attr("data-meeting_id");
    userId = $(this).attr("data-user_id");
    lessonId = $(this).attr("data-lesson_id");
    if(userId == null){
        userId = 'NewUser_'+ (new Date()).getTime();
    }
    userName = $(this).attr("data-user_name");
    if(userName == null){
        userName = 'New_Student';
    }
    var res = userName.replace(/\s+/g, "");
    userType = $(this).attr("data-user_type");
    var murl = '/'+'student/NewZoomMeeting/'+meetingId+'/'+userType+'/'+res+'/'+userId+'/'+lessonId;
    var currentHref = murl;
    window.open(currentHref, '_self');
    // window.location.href = currentHref;
 
});


$(document).on("click", ".start_bbb_individual", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 studentURI = $(this).attr("data-student_uri");
 var murl = studentURI;
 var currentHref = murl;
 setTimeout(() => {
    window.open(currentHref, '_blank');
 }, 2000);
});


$(document).on("click", ".start_meet_individual", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 studentURI = $(this).attr("data-student_uri");
 var murl = studentURI;
 var currentHref = murl;
 setTimeout(() => {
    window.open(currentHref, '_blank');
 }, 2000);
});



       $('.stu-grid-blogs-card').empty();
       var resource_container = $('.stu-grid-resources .stu-grid-resources-card');
       resource_container.empty();
       $('.stu-grid-course-card').empty();
       $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn").css('display','none')
    //    var resource_container = $('.stu-grid-resources');
    //     resource_container.find('.stu-grid-resources-card-item').remove();
        
    $(document).ready(function () {



        ajaxCallfirst1(); 
        setInterval(ajaxCallfirst1, (50 * 1000)); 

        var base_url = window.location.origin;
     
        $.ajax({
            type: "GET",
            url: base_url+"/student/latestResources",
            // data: "data",
            datatype: "json",
            success: function (data) {
                var resource_container = $('.stu-grid-resources .stu-grid-resources-card');
                resource_container.empty();

                $.each(data, function(index, resource) {
                    let icon;
            switch (resource.lecture_type) {
                case 'Slide Document':
                    icon = '<svg width="2.6rem" height="2.6rem" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 8H3.5C3.77614 8 4 7.77614 4 7.5C4 7.22386 3.77614 7 3.5 7H3V8Z" fill="#024a6a"></path> <path d="M7 8H7.5C7.77614 8 8 7.77614 8 7.5C8 7.22386 7.77614 7 7.5 7H7V8Z" fill="#024a6a"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M1 1.5C1 0.671573 1.67157 0 2.5 0H10.7071L14 3.29289V13.5C14 14.3284 13.3284 15 12.5 15H2.5C1.67157 15 1 14.3284 1 13.5V1.5ZM2 6H3.5C4.32843 6 5 6.67157 5 7.5C5 8.32843 4.32843 9 3.5 9H3V11H2V6ZM6 6H7.5C8.32843 6 9 6.67157 9 7.5C9 8.32843 8.32843 9 7.5 9H7V11H6V6ZM11 11H12V7H13V6H10V7H11V11Z" fill="#024a6a"></path> </g></svg>';  // Your Slide Document SVG icon
                    break;
                case 'Image':
                    icon = '<svg width="2.8rem" height="2.8rem" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>image-picture</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-362.000000, -101.000000)" fill="#024a6a"> <path d="M392,129 C392,130.104 391.104,131 390,131 L384.832,131 L377.464,123.535 L386,114.999 L392,120.999 L392,129 L392,129 Z M366,131 C364.896,131 364,130.104 364,129 L364,128.061 L371.945,120.945 L382.001,131 L366,131 L366,131 Z M370,105 C372.209,105 374,106.791 374,109 C374,111.209 372.209,113 370,113 C367.791,113 366,111.209 366,109 C366,106.791 367.791,105 370,105 L370,105 Z M390,101 L366,101 C363.791,101 362,102.791 362,105 L362,129 C362,131.209 363.791,133 366,133 L390,133 C392.209,133 394,131.209 394,129 L394,105 C394,102.791 392.209,101 390,101 L390,101 Z M370,111 C371.104,111 372,110.104 372,109 C372,107.896 371.104,107 370,107 C368.896,107 368,107.896 368,109 C368,110.104 368.896,111 370,111 L370,111 Z" id="image-picture" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>';  // Your Image SVG icon
                    break;
                case 'Youtube':
                    icon = '<svg width="3rem" height="3rem" viewBox="0 -3 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#024a6a"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>youtube [#024a6a]</title> <desc>Created with Sketch.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Dribbble-Light-Preview" transform="translate(-300.000000, -7442.000000)" fill="#024a6a"> <g id="icons" transform="translate(56.000000, 160.000000)"> <path d="M251.988432,7291.58588 L251.988432,7285.97425 C253.980638,7286.91168 255.523602,7287.8172 257.348463,7288.79353 C255.843351,7289.62824 253.980638,7290.56468 251.988432,7291.58588 M263.090998,7283.18289 C262.747343,7282.73013 262.161634,7282.37809 261.538073,7282.26141 C259.705243,7281.91336 248.270974,7281.91237 246.439141,7282.26141 C245.939097,7282.35515 245.493839,7282.58153 245.111335,7282.93357 C243.49964,7284.42947 244.004664,7292.45151 244.393145,7293.75096 C244.556505,7294.31342 244.767679,7294.71931 245.033639,7294.98558 C245.376298,7295.33761 245.845463,7295.57995 246.384355,7295.68865 C247.893451,7296.0008 255.668037,7296.17532 261.506198,7295.73552 C262.044094,7295.64178 262.520231,7295.39147 262.895762,7295.02447 C264.385932,7293.53455 264.28433,7285.06174 263.090998,7283.18289" id="youtube-[#024a6a]"> </path> </g> </g> </g> </g></svg>';  // Your YouTube SVG icon
                    break;
                case 'PDF':
                    icon = '<svg width="2.8rem" height="2.8rem" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3.5 8H3V7H3.5C3.77614 7 4 7.22386 4 7.5C4 7.77614 3.77614 8 3.5 8Z" fill="#024a6a"></path> <path d="M7 10V7H7.5C7.77614 7 8 7.22386 8 7.5V9.5C8 9.77614 7.77614 10 7.5 10H7Z" fill="#024a6a"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M1 1.5C1 0.671573 1.67157 0 2.5 0H10.7071L14 3.29289V13.5C14 14.3284 13.3284 15 12.5 15H2.5C1.67157 15 1 14.3284 1 13.5V1.5ZM3.5 6H2V11H3V9H3.5C4.32843 9 5 8.32843 5 7.5C5 6.67157 4.32843 6 3.5 6ZM7.5 6H6V11H7.5C8.32843 11 9 10.3284 9 9.5V7.5C9 6.67157 8.32843 6 7.5 6ZM10 11V6H13V7H11V8H12V9H11V11H10Z" fill="#024a6a"></path> </g></svg>';  // Your PDF SVG icon
                    break;
                case 'Audio':
                    icon = '<svg width="3rem" height="3rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9.66984 13.9219C8.92984 13.9219 8.33984 14.5219 8.33984 15.2619C8.33984 16.0019 8.93984 16.5919 9.66984 16.5919C10.4098 16.5919 11.0098 15.9919 11.0098 15.2619C11.0098 14.5219 10.4098 13.9219 9.66984 13.9219Z" fill="#024a6a"></path> <path d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2ZM17.12 9.8C17.12 10.41 16.86 10.95 16.42 11.27C16.14 11.47 15.8 11.58 15.44 11.58C15.23 11.58 15.02 11.54 14.8 11.47L12.51 10.71C12.5 10.71 12.48 10.7 12.47 10.69V15.25C12.47 16.79 11.21 18.05 9.67 18.05C8.13 18.05 6.87 16.79 6.87 15.25C6.87 13.71 8.13 12.45 9.67 12.45C10.16 12.45 10.61 12.59 11.01 12.8V8.63V8.02C11.01 7.41 11.27 6.87 11.71 6.55C12.16 6.23 12.75 6.15 13.33 6.35L15.62 7.11C16.48 7.4 17.13 8.3 17.13 9.2V9.8H17.12Z" fill="#024a6a"></path> </g></svg>';  // Your Audio SVG icon
                    break;
                case 'Video':
                    icon = '<svg width="3rem" height="3rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8.50989 2.00001H15.49C15.7225 1.99995 15.9007 1.99991 16.0565 2.01515C17.1643 2.12352 18.0711 2.78958 18.4556 3.68678H5.54428C5.92879 2.78958 6.83555 2.12352 7.94337 2.01515C8.09917 1.99991 8.27741 1.99995 8.50989 2.00001Z" fill="#024a6a"></path> <path d="M6.31052 4.72312C4.91989 4.72312 3.77963 5.56287 3.3991 6.67691C3.39117 6.70013 3.38356 6.72348 3.37629 6.74693C3.77444 6.62636 4.18881 6.54759 4.60827 6.49382C5.68865 6.35531 7.05399 6.35538 8.64002 6.35547H15.5321C17.1181 6.35538 18.4835 6.35531 19.5639 6.49382C19.9833 6.54759 20.3977 6.62636 20.7958 6.74693C20.7886 6.72348 20.781 6.70013 20.773 6.67691C20.3925 5.56287 19.2522 4.72312 17.8616 4.72312H6.31052Z" fill="#024a6a"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3276 7.54204H8.67239C5.29758 7.54204 3.61017 7.54204 2.66232 8.52887C1.71447 9.5157 1.93748 11.0403 2.38351 14.0896L2.80648 16.9811C3.15626 19.3724 3.33115 20.568 4.22834 21.284C5.12553 22 6.4488 22 9.09534 22H14.9046C17.5512 22 18.8745 22 19.7717 21.284C20.6689 20.568 20.8437 19.3724 21.1935 16.9811L21.6165 14.0896C22.0625 11.0404 22.2855 9.51569 21.3377 8.52887C20.3898 7.54204 18.7024 7.54204 15.3276 7.54204ZM14.5812 15.7942C15.1396 15.4481 15.1396 14.5519 14.5812 14.2058L11.2096 12.1156C10.6669 11.7792 10 12.2171 10 12.9099V17.0901C10 17.7829 10.6669 18.2208 11.2096 17.8844L14.5812 15.7942Z" fill="#024a6a"></path> </g></svg>';  // Your Video SVG icon
                    break;
                case 'Text':
                    icon = '<svg width="2.8rem" height="2.8rem" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M15 1H1V15H15V1ZM3 3V7H5V5H7V11H5V13H11V11H9V5H11V7H13V3H3Z" fill="#024a6a"></path> </g></svg>';  // Your Text SVG icon
                    break;
                // Add more cases as needed
                default:
                    icon = '';  // Default icon or leave blank
            }

            var resourceCard = `
                <div class="stu-grid-resources-card-item">
                    <div class="stu-grid-resources-card-item-img">
                        ${icon}
                    </div>
                    <div class="stu-grid-resources-card-item-title">
                        ${resource.lecture_title}
                    </div>
                    <a  href="${resource.lecture_view}" class="stu-grid-resources-card-item-view-btn">View</a>
                </div>
            `;

            resource_container.append(resourceCard);
                });  
            }
        });


$.ajax({
                    type: "GET",
                    url: base_url+"/student/latestBlogs",
                    // data: {"country_id": countryID,},
                    datatype: "json",
                    success: function (data) {
                        console.log(data)
                        $('.stu-grid-blogs-card').empty();
                        $.each(data, function(index, blog) {
                            // console.log(index)
                            // console.log(blog)
        var blogHtml = `
                <div class="stu-grid-blogs-card-item">
                    <div class="stu-grid-blogs-card-item-img">
                        <img src="${blog.blog_image}" alt="${blog.blog_title}">
                    </div>
                    <div class="stu-grid-blogs-card-item-data">
                        <div class="stu-grid-blogs-card-item-date">
                            ${blog.blog_date}
                        </div>
                        <div class="stu-grid-blogs-card-item-title">
                            ${blog.blog_title}
                        </div>
                        <div class="stu-grid-blogs-card-item-desc">
                            ${blog.blog_details.substring(0, 200)}... <!-- Truncate for preview -->
                        </div>
                    </div>
                </div>
        `;

        // Append the blog HTML to the container
        $('.stu-grid-blogs-card').append(blogHtml);
                        });
                    },
                    error: function () {
                        // alert('Failed to retrieve provinces');
                        console.log('Failed to retrieve provinces');
                    },
});








        $.ajax({
                    type: "GET",
                    url: base_url+"/student/latestEnrolledCourses",
                    // data: {"country_id": countryID,},
                    datatype: "json",
                    success: function (response) {
                        console.log(response)
                        $('.stu-grid-course-card').empty();
                        response.forEach(course => {
            // Generate course card HTML
            const courseCard = `
                <div class="stu-grid-course-card-item">
                    <div class="stu-grid-course-card-item-progress">
                        <div class="progress-bar" role="progressbar" 
                     aria-valuenow="${course.courseProgress}" 
                     aria-valuemin="0" 
                     aria-valuemax="100" 
                     style="--progress: ${course.courseProgress}%;">
                    <span class="progress-bar-text">${course.courseProgress}%</span>
                        </div>
                    </div>
                    <div class="stu-grid-course-card-item-details">
                        <h6 class="stu-grid-course-card-item-title">${course.courseName}</h6>
                        <div class="stu-grid-course-card-item-info">
                            <div class="stu-grid-course-card-item-duration">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                        <path d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3.5 2.1a.5.5 0 0 1-.5.85l-4-2.4A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z"/>
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                                    </svg>
                                </span>
                                <span class="text">${course.TotalLessons} Lessons</span>
                            </div>
                            <div class="stu-grid-course-card-item-lessons">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="0.8rem" height="0.8rem" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
                                        <path d="M14 1H5a2 2 0 0 0-2 2v12a1 1 0 0 0 1.5.866l.5-.286.5.286A1 1 0 0 0 6 15V3h7a1 1 0 0 1 1 1v10.07a4.001 4.001 0 0 0-1-.07H5a2 2 0 0 0-2 2v1a1 1 0 0 0 2 0v-1h7a1 1 0 0 1 1 1v-9.5A1.5 1.5 0 0 1 12 4.5H5v10a.5.5 0 0 0 .8.4l.7-.4.7.4a.5.5 0 0 0 .8-.4V3a1 1 0 0 0-1-1H5z"/>
                                    </svg>
                                </span>
                                <span class="text">${course.TotalLessonsLectures} Lectures</span>
                            </div>
                        </div>
                        <a class="stu-grid-course-card-item-continue-btn" href="${course.course_DetailPage_Link}">Continue</a>
                    </div>
                </div>
            `;

            // Append to container
            $('.stu-grid-course-card').append(courseCard);
        });


  // After appending the course cards
$('.progress-bar').each(function () {
    const progress = $(this).attr('aria-valuenow'); // Get the progress value
    // Set the rotation of the conic gradient based on progress (0 to 360 degrees)
    $(this).css('background', `conic-gradient(#4caf50 ${progress}% , #e0e0e0 ${progress}% 100%)`);
});

                    },
                    error: function () {
                        // alert('Failed to retrieve provinces');
                        console.log('Failed to retrieve provinces');
                    },
        });

        





        $.ajax({
                    type: "GET",
                    url: base_url+"/student/latestLeaderBoard",
                    // data: {"country_id": countryID,},
                    datatype: "json",
                    success: function (response) {
                        console.log(response)
                        $('.stu-grid-leaderboard-card').empty();
                // Loop through the responseData array
                $.each(response, function(index, data) {
            // Only populate cards for students with valid data
            if (data.courseName && data.obtainedPercentage !== null) {
                var rank = index + 1;
                var studentImage = data.studentImage;
                var courseName = data.courseName;
                var obtainedPercentage = data.obtainedPercentage;
                var status = data.status;

                // Create the HTML for the card
                var cardHtml = `
                    <div class="stu-grid-leaderboard-card-item">
                        <div class="stu-grid-leaderboard-card-item-rank">
                            ${rank}
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="stu-grid-leaderboard-card-item-img">
                                <img src="${studentImage}" alt="student">
                            </div>
                            <div class="stu-grid-leaderboard-card-item-course">
                                ${courseName}
                            </div>
                        </div>
                        <div class="stu-grid-leaderboard-card-item-rating">
                            ${getStars(obtainedPercentage)}
                        </div>
                    </div>
                `;
                // Append the generated HTML to the container
                $('.stu-grid-leaderboard-card').append(cardHtml);
            }
        });
                    },
                    error: function () {
                        // alert('Failed to retrieve provinces');
                        console.log('Failed to retrieve provinces');
                    },
    });


        // Function to get stars based on obtained percentage
function getStars(percentage) {
    var stars = '★★★★★';
    if (percentage < 20) stars = '★☆☆☆☆';
    else if (percentage < 40) stars = '★★☆☆☆';
    else if (percentage < 60) stars = '★★★☆☆';
    else if (percentage < 80) stars = '★★★★☆';
    return stars;
}
     





$.ajax({
                    type: "GET",
                    url: base_url+"/student/monthlyClasses",
                    // data: {"country_id": countryID,},
                    datatype: "json",
                    success: function (responseData) {
                        console.log(responseData)
                        // $('.stu-grid-leaderboard-card').empty();
                        Highcharts.chart('container', {
            chart: {
                type: 'column'
            },
            exporting: {
                enabled: false
            },
            legend: {
                enabled: true
            },
            credits: false,
            title: {
                text: '',
                align: 'left'
            },
            xAxis: {
                categories: responseData.map(function(data) { return data.month_Name; }), // Use the month names from response
                crosshair: true,
                accessibility: {
                    description: 'Classes'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total Classes'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    colorByPoint: true
                }
            },
            series: [
                {
                    name: 'Completed Classes',
                    data: responseData.map(function(data) {
                        const totalCompleted = data.total_Completed_Classes; // No conversion needed, directly use the count
                        return { 
                            y: Math.round(totalCompleted), 
                            color: 'rgb(0, 200, 0)' // Green for completed classes
                        };
                    })
                },
                {
                    name: 'Missed Classes',
                    data: responseData.map(function(data) {
                        const totalMissed = data.total_Missed_Classes; // No conversion needed, directly use the count
                        return { 
                            y: Math.round(totalMissed), 
                            color: 'rgb(220, 0, 0)' // Red for missed classes
                        };
                    })
                }
            ]
        });

                    },
                    error: function () {
                        // alert('Failed to retrieve provinces');
                        console.log('Failed to retrieve provinces');
                    },
        });








        $.ajax({
                    type: "GET",
                    url: base_url+"/student/TodayLiveClass",
                    // data: {"country_id": countryID,},
                    datatype: "json",
                    success: function (response) {
                        console.log(response)
                        // $('.stu-grid-today-classes-card').empty();
                          // Populate course name
    if (response && response.courseName && response.instructorName && response["Date/Time"]) {
        $(".stu-grid-today-classes-card .stu-grid-today-classes-card-item:nth-child(1) .stu-grid-today-classes-card-text")
            .text(response.courseName);

        // Populate instructor name
        $(".stu-grid-today-classes-card .stu-grid-today-classes-card-item:nth-child(2) .stu-grid-today-classes-card-text")
            .text(response.instructorName);

        // Populate date/time
        $(".stu-grid-today-classes-card .stu-grid-today-classes-card-item:nth-child(3) .stu-grid-today-classes-card-text")
            .text(response["Date/Time"]);

    
        if(response.type == 'Zoom'){
            // Optional: Change the 'Join' button text or functionality based on Btn_Status
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
            .attr('id',response.Btn_ID);
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .text(response.Btn_Status);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .addClass(response.Btn_Css_Class);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .css('background-color',response.Btn_BackGround);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .css('color',response.Btn_Color);
            if(response.Btn_Status == 'Start Class'){
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .attr('data-lesson_id', response.Meeting_Details.lesson_id);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .attr('data-meeting_id', response.Meeting_Details.meeting_id);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .attr('data-user_name', response.Meeting_Details.user_name);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .attr('data-user_id', response.Meeting_Details.user_id);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .attr('data-user_tyoe', response.Meeting_Details.user_type);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .addClass(response.Meeting_Details.btnEventClass);
            }
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn").css('display','block')
        }     
        else if(response.type == 'Google_Meet'){
              // Optional: Change the 'Join' button text or functionality based on Btn_Status
              $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .text(response.Btn_Status);
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .attr('id',response.Btn_ID);
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .addClass(response.Btn_Css_Class);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .css('background-color',response.Btn_BackGround);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .css('color',response.Btn_Color);

            if(response.Btn_Status == 'Start Class'){
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('href', 'javascript:void(0);');
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('target', '_blank');
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('data-meeting_id', response.Meeting_Details.meeting_id);
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('data-group_id', response.Meeting_Details.group_id);
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('data-lesson_id', response.Meeting_Details.lesson_id);
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('data-student_uri', response.Meeting_Details.student_uri);
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .addClass(response.Meeting_Details.btnEventClass);     
            }
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn").css('display','block')
        } 
        else if(response.type == 'BBB'){
             // Optional: Change the 'Join' button text or functionality based on Btn_Status
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .text(response.Btn_Status);
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .attr('id',response.Btn_ID);
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .addClass(response.Btn_Css_Class);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .css('background-color',response.Btn_BackGround);
                $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                .css('color',response.Btn_Color);

            if(response.Btn_Status == 'Start Class'){
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('href', 'javascript:void(0);');
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('target', '_blank');
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('data-meeting_id', response.Meeting_Details.meeting_id);
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('data-group_id', response.Meeting_Details.group_id);
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('data-lesson_id', response.Meeting_Details.lesson_id);
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .attr('data-student_uri', response.Meeting_Details.student_uri);
                    $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn")
                    .addClass(response.Meeting_Details.btnEventClass);            
            }
            $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn").css('display','block')
        }
        else{
            $(".stu-grid-today-classes-card").html('<div class="stu-grid-today-classes-card-text">No Classes For Today</div>');
        }
        // $(".stu-grid-today-classes-card .stu-grid-today-classes-card-join-btn").css('display','block')
    }
    else{
        $(".stu-grid-today-classes-card").html('<div class="stu-grid-today-classes-card-text">No Classes For Today</div>');
    }
      
                    },
                    error: function () {
                        // alert('Failed to retrieve provinces');
                        console.log('Failed to retrieve provinces');
                    },
        });
    });



 function ajaxCallfirst1() {
        var base_url = window.location.origin;
        $.ajax({
        type: "POST",
        url: base_url+"/student/checkLessonStartTime",
        data: {"_token": "{{ csrf_token() }}"},
        dataType: "JSON",
        success: (function (result) {
            console.log("STARTED");
                    console.log(result);
                    console.log(result.l_id);
                    console.log(result.is_group);
                    console.log(result.learning_tool);
                    console.log(result.group_id);
                    console.log(result.meeting_id);
                    console.log(result.user_name);
                    console.log(result.user_type);
                    console.log("ENTERED INSIDE INDIVIDUAL");
            $('#lesson_id').val(result.l_id);                       
            if(result.learning_tool == 'Zoom'){
                $('#lec_link1_'+result.l_id).attr('href', result.url); 
                $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                $('#lec_link1_'+result.l_id).html("Start Class"); 
                $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                $('#lec_link1_'+result.l_id).attr('data-user_name', result.user_name); 
                $('#lec_link1_'+result.l_id).attr('data-user_id', result.user_id); 
                $('#lec_link1_'+result.l_id).attr('data-user_type', result.user_type); 
                $('#lec_link1_'+result.l_id).addClass('create_zoom_meeting_link');
            }
            else if(result.learning_tool == 'BBB'){
                console.log("ENTERED INSIDE INDIVIDUAL BBB");
                        // $('#lec_link1_'+result.l_id).attr('href', result.url); 
                        // $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        // $('#lec_link1_'+result.l_id).html("Start Class"); 
                        // $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        // $('#lec_link1_'+result.l_id).css("color", "#fff"); 

                        $('#lec_link1_'+result.l_id).attr('href', 'javascript:void(0);'); 
                        $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                        $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                        $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                        $('#lec_link1_'+result.l_id).attr('data-student_uri', result.url);
                        $('#lec_link1_'+result.l_id).addClass('start_bbb_individual'); 
            }
            else if(result.learning_tool == 'Google_Meet'){
                console.log("ENTERED INSIDE INDIVIDUAL Google_Meet");
                        // $('#lec_link1_'+result.l_id).attr('href', result.url); 
                        // $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        // $('#lec_link1_'+result.l_id).html("Start Class"); 
                        // $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        // $('#lec_link1_'+result.l_id).css("color", "#fff"); 

                        $('#lec_link1_'+result.l_id).attr('href', 'javascript:void(0);'); 
                        $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                        $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                        $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                        $('#lec_link1_'+result.l_id).attr('data-student_uri', result.url);
                        $('#lec_link1_'+result.l_id).addClass('start_meet_individual'); 
            }
            IsmeetingCreated = $('#lec_link1_'+result.l_id).attr('data-meeting_created'); 
            if(IsmeetingCreated != 'Yes'){
                var audioElement = document.createElement('audio');
                // audioElement.setAttribute('src', base_url+'/public/assets/notification_sound/Cool_Notification_2.mp3');
            }
            $('#lec_link1_'+result.l_id).attr('data-meeting_created', 'Yes'); 
        }),
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            console.log("Status: " + textStatus)
            console.log("Error: " + errorThrown)
        }    
        })
}







    // document.addEventListener('DOMContentLoaded', function() {
    //     var progressBar = document.querySelector('.progress-bar');
    //     var progressValue = 75; 
    //     progressBar.setAttribute('aria-valuenow', progressValue);
    //     progressBar.style.background = `radial-gradient(closest-side, white 79%, transparent 80% 100%), conic-gradient(#89bd40 ${progressValue}%, rgba(207, 238, 165, 0.8) 0% 0)`;
    // });

    // Highcharts.chart('container', {
    //     chart: {
    //         type: 'column'
    //     },
    //     exporting: {
    //         enabled: false
    //     },
    //     legend: {
    //         enabled: false
    //     },
    //     credits: false,
    //     title: {
    //         text: '',
    //         align: 'left'
    //     },
    //     xAxis: {
    //         categories: ['C1', 'C2', 'C3'],
    //         crosshair: true,
    //         accessibility: {
    //             description: 'Classes'
    //         }
    //     },
    //     yAxis: {
    //         min: 0,
    //         title: {
    //             text: 'Time'
    //         }
    //     },
    //     plotOptions: {
    //         column: {
    //             pointPadding: 0.2,
    //             borderWidth: 0,
    //             colorByPoint: true
    //         }
    //     },
    //     series: [
    //         {
    //             name: 'C1',
    //             data: [387749, 280000, 129000, 64300, 54000, 34300].map(seconds => {
    //                 const hours = seconds / 3600; // Convert seconds to hours
    //                 let color;
    //                 if (hours <= 30) {
    //                     color = 'rgb(220, 0, 0)'; // Darker Red
    //                 } else if (hours <= 60) {
    //                     color = 'rgb(220, 220, 0)'; // Darker Yellow
    //                 } else {
    //                     color = 'rgb(0, 200, 0)'; // Darker Green
    //                 }
    //                 return { y: hours, color: color };
    //             })
    //         },
    //         {
    //             name: 'C2',
    //             data: [45321, 140000, 10000, 140500, 19500, 113500].map(seconds => {
    //                 const hours = seconds / 3600; // Convert seconds to hours
    //                 let color;
    //                 if (hours <= 30) {
    //                     color = 'rgb(220, 0, 0)'; // Darker Red
    //                 } else if (hours <= 60) {
    //                     color = 'rgb(220, 220, 0)'; // Darker Yellow
    //                 } else {
    //                     color = 'rgb(0, 200, 0)'; // Darker Green
    //                 }
    //                 return { y: hours, color: color };
    //             })
    //         }
    //     ]
    // });

</script>

@endsection

