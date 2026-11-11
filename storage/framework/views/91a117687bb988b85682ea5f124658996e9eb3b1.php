<?php $authUser = @auth()->user(); // echo $authUser;
?>

<?php
$currentRoute = Route::currentRouteName();
$isHomePage = $currentRoute == 'main.index' || $currentRoute == 'home';
$navbarClass = $isHomePage ? 'home-navbar' : 'inner-page-navbar';
?>

<section class="menu-section-area <?php if(auth()->guard()->check()): ?> isLoginMenu <?php endif; ?> <?php echo e($navbarClass); ?>">
    <nav class="navbar sticky-header navbar-expand-lg" id="mainNav">
        <div class="container">
            <a href="<?php echo e(url('/')); ?>" class="navbar-img">
                <img src="<?php echo e(getImageFile(get_option('app_logo'))); ?>" alt="Logo">
            </a>
            <div class="d-flex align-items-center">
            <div class="panel-mobile">

                        <?php if(@$authUser->role == USER_ROLE_STUDENT ): ?> 
                        <?php if(@$authUser->instructor || @$authUser->organization): ?>
                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Request Pending')); ?></span>
                        </li>

                        <?php else: ?>
                        <li class="">
                            


                            


                             <?php if(@$authUser->role == USER_ROLE_STUDENT): ?>
                                <?php if(@$authUser->student?->status == 1): ?>
                                    <li class="nav-item nav-panel-btn">
                                        <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                            <?php echo e(__('Student Panel')); ?>

                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="nav-item nav-panel-btn" >
                                        <?php if(checkPaymentPending($authUser)): ?>
                                            <a href="https://portal.cefonlineacademy.com" id="signInBtn" aria-current="page">
                                                <?php echo e(__('Student Panel')); ?>

                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('student.cartList')); ?>" id="signInBtn" aria-current="page">
                                                <?php echo e(__('Payment Pending')); ?>

                                            </a>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                        </li>

                        <?php endif; ?>

                        <?php elseif(@$authUser->role == 5): ?>

                        <?php if(@$authUser->coordinator->status == 1): ?>
                        <li class="nav-item nav-panel-btn">
                            
                             <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Coordinator Panel')); ?>

                            </a>
                            
                        </li>

                        <?php elseif(@$authUser->coordinator->status == 0): ?>
                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Blocked From Coordinator Panel')); ?></span>
                        </li>

                        <?php endif; ?>

                        <?php elseif(@$authUser->role == USER_ROLE_INSTRUCTOR || @$authUser->role == USER_ROLE_ORGANIZATION): ?>
                        <!-- Status 1 = Approved,  Status 2 = Blocked,  Status 0 = Pending -->

                        <?php if(@$authUser->instructor->status == STATUS_APPROVED): ?>
                        <?php if(\Illuminate\Support\Str::contains(url()->current(), 'instructor')): ?>

                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Instructor Panel')); ?>

                            </a>
                        </li>

                        <?php else: ?>

                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Instructor Panel')); ?>

                            </a>
                        </li>

                        <?php endif; ?>

                        <?php elseif(@$authUser->organization->status == STATUS_APPROVED): ?>

                        <?php if(\Illuminate\Support\Str::contains(url()->current(), 'organization')): ?>

                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Organization Panel')); ?>

                            </a>
                        </li>

                        <?php else: ?>

                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Organization Panel')); ?>

                            </a>
                        </li>

                        <?php endif; ?>

                        <?php elseif(@$authUser->instructor->status == STATUS_REJECTED): ?>

                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Blocked From Instructor Panel')); ?></span>
                        </li>

                        <?php elseif(@$authUser->organization->status == STATUS_REJECTED): ?>

                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Blocked From Organization Panel')); ?></span>
                        </li>

                        <?php else: ?>

                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Request Pending')); ?></span>
                        </li>

                        <?php endif; ?>

                        <?php endif; ?>
                        </div>
            <button class="navbar-toggler" type="button" >
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_649_1115)">
                    <path d="M0 4.66667C0 3.56 0.893333 2.66667 2 2.66667H23.3333C24.44 2.66667 25.3333 3.56 25.3333 4.66667C25.3333 5.77333 24.44 6.66667 23.3333 6.66667H2C0.893333 6.66667 0 5.77333 0 4.66667ZM23.3333 24H2C0.893333 24 0 24.8933 0 26C0 27.1067 0.893333 28 2 28H23.3333C24.44 28 25.3333 27.1067 25.3333 26C25.3333 24.8933 24.44 24 23.3333 24ZM30 13.3333H8.66667C7.56 13.3333 6.66667 14.2267 6.66667 15.3333C6.66667 16.44 7.56 17.3333 8.66667 17.3333H30C31.1067 17.3333 32 16.44 32 15.3333C32 14.2267 31.1067 13.3333 30 13.3333Z" fill="white"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_649_1115">
                    <rect width="32" height="32" fill="white"/>
                    </clipPath>
                    </defs>
                </svg>
            </button>
            </div>
            <div class="main-menu-collapse navbar-collapse d-none" id="navbarSupportedContent">
                <div class="justify-content-between align-items-center mobile-view">
                    <a href="<?php echo e(url('/')); ?>" class="navbar-img-mobile">
                        <img src="<?php echo e(getImageFile(get_option('app_logo'))); ?>" alt="Logo">
                    </a>
                    <svg class="nav-close-mobile" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g opacity="0.3" clip-path="url(#clip0_651_1092)">
                        <path d="M21.3334 10.6667C21.0834 10.4167 20.7443 10.2763 20.3908 10.2763C20.0372 10.2763 19.6981 10.4167 19.4481 10.6667L16.0001 14.1147L12.5521 10.6667C12.3006 10.4238 11.9638 10.2894 11.6142 10.2924C11.2646 10.2955 10.9302 10.4357 10.683 10.6829C10.4358 10.9301 10.2956 11.2645 10.2925 11.6141C10.2895 11.9637 10.4239 12.3005 10.6668 12.552L14.1148 16L10.6668 19.448C10.4239 19.6995 10.2895 20.0363 10.2925 20.3859C10.2956 20.7355 10.4358 21.0699 10.683 21.3171C10.9302 21.5643 11.2646 21.7045 11.6142 21.7076C11.9638 21.7106 12.3006 21.5762 12.5521 21.3333L16.0001 17.8853L19.4481 21.3333C19.6996 21.5762 20.0364 21.7106 20.386 21.7076C20.7356 21.7045 21.07 21.5643 21.3172 21.3171C21.5644 21.0699 21.7046 20.7355 21.7077 20.3859C21.7107 20.0363 21.5763 19.6995 21.3334 19.448L17.8854 16L21.3334 12.552C21.5834 12.302 21.7238 11.9629 21.7238 11.6093C21.7238 11.2558 21.5834 10.9167 21.3334 10.6667Z" fill="black"/>
                        <path d="M16 0C12.8355 0 9.74207 0.938384 7.11088 2.69649C4.4797 4.45459 2.42894 6.95345 1.21793 9.87707C0.00693258 12.8007 -0.309921 16.0177 0.307443 19.1214C0.924806 22.2251 2.44866 25.0761 4.6863 27.3137C6.92394 29.5514 9.77487 31.0752 12.8786 31.6926C15.9823 32.3099 19.1993 31.9931 22.1229 30.7821C25.0466 29.5711 27.5454 27.5203 29.3035 24.8891C31.0616 22.2579 32 19.1645 32 16C31.9954 11.7579 30.3082 7.69095 27.3087 4.69136C24.3091 1.69177 20.2421 0.00458811 16 0ZM16 29.3333C13.3629 29.3333 10.7851 28.5513 8.5924 27.0863C6.39975 25.6212 4.69078 23.5388 3.68161 21.1024C2.67245 18.6661 2.4084 15.9852 2.92287 13.3988C3.43734 10.8124 4.70722 8.43661 6.57192 6.57191C8.43662 4.70721 10.8124 3.43733 13.3988 2.92286C15.9852 2.40839 18.6661 2.67244 21.1025 3.68161C23.5388 4.69078 25.6212 6.39974 27.0863 8.5924C28.5514 10.7851 29.3333 13.3629 29.3333 16C29.3295 19.535 27.9235 22.9242 25.4238 25.4238C22.9242 27.9235 19.535 29.3295 16 29.3333Z" fill="black"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_651_1092">
                        <rect width="32" height="32" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
                </div>
                <div>
                    <form action="#" class="form-search-mobile">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.3" clip-path="url(#clip0_651_1078)">
                                <path d="M17.6268 13.8499C20.7909 10.6858 20.7909 5.53727 17.6268 2.37311C14.4627 -0.791056 9.31424 -0.791056 6.15014 2.37311C3.46578 5.05747 3.05923 9.0498 4.92941 12.1674C4.92941 12.1674 5.06374 12.3928 4.88231 12.5741L0.741918 16.7145C-0.0821909 17.5385 -0.278377 18.6908 0.45244 19.4218L0.578269 19.5475C1.30909 20.2784 2.46146 20.0823 3.2855 19.2582L7.4172 15.1265C7.60733 14.9363 7.83263 15.0707 7.83263 15.0707C10.9502 16.9408 14.9425 16.5343 17.6268 13.8499ZM7.64837 12.3517C5.31037 10.0137 5.31044 6.20952 7.64843 3.87153C9.98643 1.5336 13.7906 1.53354 16.1285 3.87153C18.4665 6.20946 18.4665 10.0137 16.1285 12.3517C13.7905 14.6895 9.98643 14.6895 7.64837 12.3517Z" fill="black"/>
                                <path d="M8.1663 7.62992C8.05748 7.62992 7.94686 7.60853 7.84023 7.56349C7.41429 7.38322 7.21501 6.89176 7.39528 6.46576C8.52833 3.78816 11.6285 2.5316 14.306 3.66465C14.7319 3.84492 14.9312 4.33638 14.7509 4.76239C14.5706 5.18839 14.0793 5.38754 13.6531 5.20733C11.8263 4.43431 9.71099 5.29167 8.93803 7.11848C8.8028 7.43799 8.49264 7.62992 8.1663 7.62992Z" fill="black"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_651_1078">
                                <rect width="20" height="20" fill="white"/>
                                </clipPath>
                                </defs>
                                </svg>
                            </span>
                            <input type="text" id="searchCourse" type="search" name="keyword" value="<?php echo e(request('keyword')); ?>" placeholder="<?php echo e(__('Search Course')); ?>..." aria-label="Search">
                        </div>
                        <div class="search-bar-suggestion-box-mobile searchBox custom-scrollbar d-block">
                            <ul class="appendCourseSearchList">
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="header-nav-left-side d-flex">

                    <!----Courses Menu-->
                    <ul class="navbar-nav mb-2 mb-lg-0">   
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="librariesDropdown" data-bs-toggle="dropdown">
                                <?php echo e(__('Courses')); ?>

                            </a>
                            <ul class="dropdown-menu <?php echo e($selectedLanguage->rtl == 1 ? 'dropdown-menu-end' : ''); ?>">
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a href="<?php echo e(route('category-courses', $category->slug)); ?>" class="dropdown-item <?php if(count($category->subcategories) > 0): ?> dropdown-toggle <?php endif; ?>">
                                        <?php echo e($category->name); ?>

                                    </a>
                                    <?php if(count($category->subcategories) > 0): ?>
                                    <ul class="submenu dropdown-menu">
                                        <?php $__currentLoopData = $category->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo e(route('subcategory-courses', $subcategory->slug)); ?>">
                                                <?php echo e($subcategory->name); ?>

                                            </a>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a href="<?php echo e(route('courses')); ?>">
                                        <?php echo e(__('All Courses')); ?>

                                    </a>
                                </li>
                                <li>
                                    <a href="javascript::void(0)" class="dropdown-item <?php if(count(free_self_learning_courses()) > 0): ?> dropdown-toggle <?php endif; ?>">
                                        <?php echo e(__('Free Self-Learning Courses')); ?>

                                    </a>
                                    <?php if(count(free_self_learning_courses()) > 0): ?>
                                    <ul class="submenu dropdown-menu">
                                        <?php $__currentLoopData = free_self_learning_courses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $free): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo e(route('course-details', $free->slug)); ?>">
                                                <?php echo e($free->title); ?>

                                            </a>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <a href="javascript::void(0)" class="dropdown-item <?php if(count(paid_self_learning_courses()) > 0): ?> dropdown-toggle <?php endif; ?>">
                                        <?php echo e(__('Paid Self-Learning Courses')); ?>

                                    </a>
                                    <?php if(count(paid_self_learning_courses()) > 0): ?>
                                    <ul class="submenu dropdown-menu">
                                        <?php $__currentLoopData = paid_self_learning_courses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo e(route('course-details', $paid->slug)); ?>">
                                                <?php echo e($paid->title); ?>

                                            </a>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                             
                                <li>
                                    <a href="javascript::void(0)" class="dropdown-item <?php if(count(live_courses()) > 0): ?> dropdown-toggle <?php endif; ?>">
                                        <?php echo e(__('Live Tutoring Courses')); ?>

                                    </a>
                                    <?php if(count(live_courses()) > 0): ?>
                                    <ul class="submenu dropdown-menu">
                                        <?php $__currentLoopData = live_courses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $live): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo e(route('course-details', $live->slug)); ?>">
                                                <?php echo e($live->title); ?>

                                            </a>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>

                               
                            </ul>
                        </li>
                    
                        <li class="nav-item">
                            <a class="nav-link" href="/product-list">
                                <?php echo e(__('Products')); ?>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('blogs')); ?>">
                                <?php echo e(__('Blogs')); ?>

                            </a>
                        </li>
                        
                    </ul>
                    <!----Courses Menu-->

                    
                </div>
                <div class="header-right-nav">
                    <ul>
                        
                        <button class="search-hover">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_55_617)">
                                    <path d="M17.6268 13.8499C20.7909 10.6858 20.7909 5.53727 17.6268 2.37311C14.4627 -0.791056 9.31424 -0.791056 6.15014 2.37311C3.46578 5.05747 3.05923 9.0498 4.92941 12.1674C4.92941 12.1674 5.06374 12.3928 4.88231 12.5741L0.741918 16.7145C-0.0821909 17.5385 -0.278377 18.6908 0.45244 19.4218L0.578269 19.5475C1.30909 20.2784 2.46146 20.0823 3.2855 19.2582L7.4172 15.1265C7.60733 14.9363 7.83263 15.0707 7.83263 15.0707C10.9502 16.9408 14.9425 16.5343 17.6268 13.8499ZM7.64837 12.3517C5.31037 10.0137 5.31044 6.20952 7.64843 3.87153C9.98643 1.5336 13.7906 1.53354 16.1285 3.87153C18.4665 6.20946 18.4665 10.0137 16.1285 12.3517C13.7905 14.6895 9.98643 14.6895 7.64837 12.3517Z" fill="white" />
                                    <path d="M8.16655 7.62992C8.05773 7.62992 7.9471 7.60853 7.84047 7.56349C7.41454 7.38322 7.21526 6.89176 7.39553 6.46576C8.52857 3.78816 11.6287 2.5316 14.3062 3.66465C14.7322 3.84492 14.9314 4.33638 14.7512 4.76239C14.5708 5.18839 14.0795 5.38754 13.6534 5.20733C11.8266 4.43431 9.71123 5.29167 8.93828 7.11848C8.80304 7.43799 8.49288 7.62992 8.16655 7.62992Z" fill="white" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_55_617">
                                        <rect width="20" height="20" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </button>
                        <form action="#" class="form-search">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_55_617)">
                                            <path d="M17.6268 13.8499C20.7909 10.6858 20.7909 5.53727 17.6268 2.37311C14.4627 -0.791056 9.31424 -0.791056 6.15014 2.37311C3.46578 5.05747 3.05923 9.0498 4.92941 12.1674C4.92941 12.1674 5.06374 12.3928 4.88231 12.5741L0.741918 16.7145C-0.0821909 17.5385 -0.278377 18.6908 0.45244 19.4218L0.578269 19.5475C1.30909 20.2784 2.46146 20.0823 3.2855 19.2582L7.4172 15.1265C7.60733 14.9363 7.83263 15.0707 7.83263 15.0707C10.9502 16.9408 14.9425 16.5343 17.6268 13.8499ZM7.64837 12.3517C5.31037 10.0137 5.31044 6.20952 7.64843 3.87153C9.98643 1.5336 13.7906 1.53354 16.1285 3.87153C18.4665 6.20946 18.4665 10.0137 16.1285 12.3517C13.7905 14.6895 9.98643 14.6895 7.64837 12.3517Z" fill="white" />
                                            <path d="M8.16655 7.62992C8.05773 7.62992 7.9471 7.60853 7.84047 7.56349C7.41454 7.38322 7.21526 6.89176 7.39553 6.46576C8.52857 3.78816 11.6287 2.5316 14.3062 3.66465C14.7322 3.84492 14.9314 4.33638 14.7512 4.76239C14.5708 5.18839 14.0795 5.38754 13.6534 5.20733C11.8266 4.43431 9.71123 5.29167 8.93828 7.11848C8.80304 7.43799 8.49288 7.62992 8.16655 7.62992Z" fill="white" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_55_617">
                                                <rect width="20" height="20" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </span>
                                <input type="text" id="searchCourse" type="search" name="keyword" value="<?php echo e(request('keyword')); ?>" placeholder="<?php echo e(__('Search Course')); ?>..." aria-label="Search">
                            </div>
                            <div class="search-bar-suggestion-box searchBox custom-scrollbar d-block">
                                <ul class="appendCourseSearchList">
                                </ul>
                            </div>
                        </form>

                        <div class="panel-desktop">

                        <?php if(@$authUser->role == USER_ROLE_STUDENT ): ?> <?php if(@$authUser->instructor || @$authUser->organization): ?>
                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Request Pending')); ?></span>
                        </li>

                        <?php else: ?>

                            
                           

                            <?php if(@$authUser->role == USER_ROLE_STUDENT): ?>
                                <?php if(@$authUser->student?->status == 1): ?>
                                    <li class="nav-item nav-panel-btn">
                                        <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                            <?php echo e(__('Student Panel')); ?>

                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="nav-item nav-panel-btn" >
                                        <?php if(checkPaymentPending($authUser)): ?>
                                            <a href="https://portal.cefonlineacademy.com" id="signInBtn" aria-current="page">
                                                <?php echo e(__('Student Panel')); ?>

                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('student.cartList')); ?>" id="signInBtn" aria-current="page">
                                                <?php echo e(__('Payment Pending')); ?>

                                            </a>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>

                             
                            <?php endif; ?>



                       

                        <?php endif; ?>

                        <?php elseif(@$authUser->role == 5): ?>

                        <?php if(@$authUser->coordinator->status == 1): ?>
                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Coordinator Panel')); ?>

                            </a>
                        </li>

                        <?php elseif(@$authUser->coordinator->status == 0): ?>
                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Blocked From Coordinator Panel')); ?></span>
                        </li>

                        <?php endif; ?>

                        <?php elseif(@$authUser->role == USER_ROLE_INSTRUCTOR || @$authUser->role == USER_ROLE_ORGANIZATION): ?>
                        <!-- Status 1 = Approved,  Status 2 = Blocked,  Status 0 = Pending -->

                        <?php if(@$authUser->instructor->status == STATUS_APPROVED): ?>
                        <?php if(\Illuminate\Support\Str::contains(url()->current(), 'instructor')): ?>

                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Instructor Panel')); ?>

                            </a>
                        </li>

                        <?php else: ?>

                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Instructor Panel')); ?>

                            </a>
                        </li>

                        <?php endif; ?>

                        <?php elseif(@$authUser->organization->status == STATUS_APPROVED): ?>

                        <?php if(\Illuminate\Support\Str::contains(url()->current(), 'organization')): ?>

                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Organization Panel')); ?>

                            </a>
                        </li>

                        <?php else: ?>

                        <li class="nav-item nav-panel-btn">
                            <a class="nav-link" href="https://portal.cefonlineacademy.com">
                                <?php echo e(__('Organization Panel')); ?>

                            </a>
                        </li>

                        <?php endif; ?>

                        <?php elseif(@$authUser->instructor->status == STATUS_REJECTED): ?>

                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Blocked From Instructor Panel')); ?></span>
                        </li>

                        <?php elseif(@$authUser->organization->status == STATUS_REJECTED): ?>

                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Blocked From Organization Panel')); ?></span>
                        </li>

                        <?php else: ?>

                        <li class="nav-item">
                            <span class="nav-link"><?php echo e(__('Request Pending')); ?></span>
                        </li>

                        <?php endif; ?>

                        <?php endif; ?>
                        </div>

                           
                                

                        <?php if(auth::user()): ?>
                        <li class="nav-item dropdown menu-round-btn menu-notification-btn dropdown-top-space">
                            <a class="nav-link menu-cart-btn me-0" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-icon lucide-bell"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo e(count($student_notifications) + count($instructor_notifications)); ?>

                                </span>
                            </a>
                            <div class="dropdown-menu <?php echo e($selectedLanguage->rtl == 1 ? 'dropdown-menu-start' : 'dropdown-menu-end'); ?>" data-bs-popper="none">
                                <ul class="nav nav-pills menu-notification-tab-list" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="pills-student-tab" data-bs-toggle="pill" data-bs-target="#pills-student" type="button" role="tab" aria-controls="pills-student" aria-selected="false"><?php echo e(__('Student')); ?></button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="pills-instructor-tab" data-bs-toggle="pill" data-bs-target="#pills-instructor" type="button" role="tab" aria-controls="pills-instructor" aria-selected="true"><?php echo e(__('Instructor')); ?></button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-student" role="tabpanel" aria-labelledby="pills-student-tab">
                                        <div class="custom-scrollbar menu-notification-tab-content">
                                            <?php $__empty_1 = true; $__currentLoopData = $student_notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student_notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <a href="<?php echo e(route('notification.url', [$student_notification->uuid])); ?>" class="message-user-item cursor d-flex align-items-center">
                                                <div class="message-user-item-left">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="user-img-wrap position-relative radius-50">
                                                                <img src="<?php echo e(asset($student_notification->sender->image_path)); ?>" alt="img" class="radius-50">
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 <?php echo e($selectedLanguage->rtl == 1 ? 'me-2' : 'ms-2'); ?>">
                                                            <h6 class="color-heading font-14">
                                                                <?php echo e($student_notification->sender->name); ?>

                                                            </h6>
                                                            <p class="font-13"><?php echo e($student_notification->text); ?></p>
                                                            <div class="font-11 color-gray mt-1">
                                                                <?php echo e(@$student_notification->created_at->diffForHumans()); ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <div class="no-notification-found-box">
                                                <p class="text-center"><?php echo e(__('No Data Found')); ?></p>
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if(count($student_notifications)): ?>
                                        <div class="dropdown-divider"></div>
                                        <form action="<?php echo e(route('notification.all-read')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="type" value="3">
                                            <button type="submit" class="dropdown-item dropdown-footer">
                                                <?php echo e(__("Mark all as read")); ?>

                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tab-pane fade" id="pills-instructor" role="tabpanel" aria-labelledby="pills-instructor-tab">
                                        <div class="custom-scrollbar menu-notification-tab-content">
                                            <?php $__empty_1 = true; $__currentLoopData = $instructor_notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor_notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <a href="<?php echo e(route('notification.url', [$instructor_notification->uuid])); ?>" class="message-user-item d-flex align-items-center">
                                                <div class="message-user-item-left">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="user-img-wrap position-relative radius-50">
                                                                <img src="<?php echo e(asset(@$instructor_notification->sender->image_path)); ?>" alt="img" class="radius-50">
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 <?php echo e($selectedLanguage->rtl == 1 ? 'me-2' : 'ms-2'); ?>">
                                                            <h6 class="color-heading font-14">
                                                                <?php echo e(@$instructor_notification->sender->name); ?>

                                                            </h6>
                                                            <p class="font-13"><?php echo e($instructor_notification->text); ?></p>
                                                            <div class="font-11 color-gray mt-1">
                                                                <?php echo e(@$instructor_notification->created_at->diffForHumans()); ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <div class="no-notification-found-box">
                                                <p class="text-center"><?php echo e(__('No Data Found')); ?></p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if(count($instructor_notifications)): ?>
                                        <div class="dropdown-divider"></div>
                                        <form action="<?php echo e(route('notification.all-read')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="type" value="2">
                                            <button type="submit" class="dropdown-item dropdown-footer"><?php echo e(__("Mark all as read")); ?></button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php endif; ?>

                        <?php if($authUser): ?>
                        <?php if(@$authUser->role != USER_ROLE_INSTRUCTOR && @$authUser->role != USER_ROLE_ADMIN && @$authUser->role != USER_ROLE_ORGANIZATION && $authUser->role != 5): ?>
                        <li class="nav-item menu-round-btn menu-cart-btn">
                            <a class="nav-link menu-cart-btn me-0" aria-current="page" href="<?php echo e(route('student.cartList')); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag-icon lucide-shopping-bag"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cartQuantity">
                                    <?php echo e(@$cartQuantity); ?>

                                </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php if(Route::has('login')): ?>
                        <?php if(auth()->guard()->check()): ?>
                        <?php if(isEnableOpenAI()): ?>

                        <li class="nav-item menu-round-btn">
                            <a id="ai-content-toggle" class="nav-link" aria-current="page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M8.356 5H7.01L5 13h1.028l.464-1.875h2.316L9.26 13h1.062Zm-1.729 5.322L7.644 5.95h.045l.984 4.373ZM11.238 13V5h1v8Zm.187 1H4V4h10v4.78a5.504 5.504 0 0 1 4-.786V6h-2V4a2.006 2.006 0 0 0-2-2h-2V0h-2v2H8V0H6v2H4a2.006 2.006 0 0 0-2 2v2H0v2h2v2H0v2h2v2a2.006 2.006 0 0 0 2 2h2v2h2v-2h2v2h2v-1.992A5.547 5.547 0 0 1 11.425 14Zm2.075-.5A3.5 3.5 0 1 1 17 17a3.499 3.499 0 0 1-3.5-3.5ZM17 19c-2.336 0-7 1.173-7 3.5V24h14v-1.5c0-2.328-4.664-3.5-7-3.5Z" />
                                </svg>
                            </a>
                        </li>

                        <?php endif; ?>

                        <li class="nav-item dropdown menu-round-btn menu-user-btn dropdown-top-space">
                            <a class="nav-link" href="#">
                                <img src="<?php echo e(asset(auth::user()->image_path)); ?>" alt="user" class="radius-50">
                            </a>
                            <div class="dropdown-menu-profile dropdown-menu <?php echo e($selectedLanguage->rtl == 1 ? 'dropdown-menu-start' : 'dropdown-menu-end'); ?>" data-bs-popper="none">
                                <div class="dropdown-user-info">
                                    <div class="message-user-item d-flex align-items-center">
                                        <div class="message-user-item-left">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="user-img-wrap position-relative radius-50">
                                                        <img src="<?php echo e(asset(auth::user()->image_path)); ?>" alt="img" class="radius-50">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 <?php echo e($selectedLanguage->rtl == 1 ? 'me-2' : 'ms-2'); ?>">
                                                    <h6 class="color-heading font-14"><?php echo e(auth::user()->name); ?></h6>
                                                    <p class="font-13"><?php echo e(auth::user()->email); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if(@$authUser->role != USER_ROLE_ADMIN): ?>
                                <ul class="user-dropdown-item-box">
                                    <?php if(@$authUser->role == USER_ROLE_STUDENT && auth()->user()->student->organization_id != NULL): ?>

                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('student.organization_course')); ?>">
                                            <span class="iconify mr-15" data-icon="ion:log-in-outline"></span>
                                            <?php echo e(__('Organization Course')); ?>

                                        </a>
                                    </li>

                                    <?php endif; ?>

                                    <?php if(Auth::user()->role == USER_ROLE_STUDENT): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('student.chat.index')); ?>">
                                            <span class="iconify" data-icon="ic:outline-chat"></span>
                                            <?php echo e(__('Chat')); ?>

                                        </a>
                                    </li>
                                    <?php endif; ?>

                                    <?php if(@$authUser->role != USER_ROLE_INSTRUCTOR && @$authUser->role != USER_ROLE_ADMIN && @$authUser->role != USER_ROLE_ORGANIZATION && $authUser->role != 5): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('student.wishlist')); ?>">
                                            <span class="iconify" data-icon="bx:bx-heart"></span>
                                            <?php echo e(__('Wishlist')); ?>

                                        </a>
                                    </li>

                                    <?php endif; ?>

                                    <?php if(get_option('subscription_mode')): ?>

                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('subscription_panel')); ?>">
                                            <span class="iconify" data-icon="akar-icons:desktop-device"></span>
                                            <?php echo e(__('Subscription Panel')); ?>

                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('student.subscription_plan')); ?>">
                                            <span class="iconify" data-icon="eos-icons:subscriptions-created"></span>
                                            <?php echo e(__('Subscription Plan')); ?>

                                        </a>
                                    </li>

                                    <?php endif; ?>

                                    <?php if(auth()->user()->role != USER_ROLE_STUDENT && get_option('saas_mode')): ?>

                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('saas_panel')); ?>">
                                            <span class="iconify" data-icon="ic:outline-view-list"></span>
                                            <?php echo e(__('SaaS Panel')); ?>

                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('saas_plan')); ?>">
                                            <span class="iconify" data-icon="mdi:subscriptions"></span>
                                            <?php echo e(__('SaaS Plan')); ?>

                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>

                                <?php if(@$authUser->role == USER_ROLE_STUDENT && get_option('device_control')): ?>
                                <ul class="user-dropdown-item-box">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('student.all_login_device')); ?>">
                                            <span class="iconify" data-icon="akar-icons:desktop-device"></span>
                                            <?php echo e(__('Device Control')); ?>

                                        </a>
                                    </li>
                                </ul>
                                <?php endif; ?>

                                <?php if(Auth::user()->is_affiliator == NOT_AFFILIATOR && get_option('referral_status') == 1): ?>
                                <ul
                                    class="user-dropdown-item-box">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('affiliate.become-an-affiliate')); ?>">
                                            <span class="iconify" data-icon="tabler:affiliate"></span>
                                            <?php echo e(__('Become an Affiliator')); ?>

                                        </a>
                                    </li>
                                </ul>

                                <?php elseif(Auth::user()->is_affiliator == AFFILIATE_REQUEST_PENDING && get_option('referral_status') == 1): ?>
                                <ul class="user-dropdown-item-box">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <span class="iconify" data-icon="tabler:affiliate"></span>
                                            <?php echo e(__('Affiliate Request Pending')); ?>

                                        </a>
                                    </li>
                                </ul>

                                <?php elseif(Auth::user()->is_affiliator == AFFILIATE_REQUEST_REJECTED && get_option('referral_status') == 1): ?>
                                <ul class="user-dropdown-item-box">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <span class="iconify" data-icon="tabler:affiliate"></span>
                                            <?php echo e(__('Affiliate Request Rejected')); ?>

                                        </a>
                                    </li>
                                </ul>

                                <?php elseif(Auth::user()->is_affiliator == AFFILIATOR): ?>
                                <ul class="user-dropdown-item-box">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('affiliate.dashboard')); ?>">
                                            <span class="iconify" data-icon="tabler:affiliate"></span>
                                            <?php echo e(__('Affiliator Panel')); ?>

                                        </a>
                                    </li>
                                </ul>
                                <?php endif; ?>
                                <?php endif; ?>

                                <?php if(@$authUser->role == USER_ROLE_ADMIN): ?>
                                <ul class="user-dropdown-item-box">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('admin.dashboard')); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--bxs" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="bxs:dashboard">
                                                <path fill="currentColor" d="M4 13h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1zm-1 7a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v4zm10 0a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-7a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v7zm1-10h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1z">
                                                </path>
                                            </svg>
                                            <?php echo e(__('Admin Dashboard')); ?>

                                        </a>
                                    </li>
                                </ul>
                                <?php endif; ?>

                                <?php if(@$authUser->role != USER_ROLE_INSTRUCTOR && @$authUser->role != USER_ROLE_ADMIN && @$authUser->role != USER_ROLE_ORGANIZATION && $authUser->role != 5): ?>
                                <ul class="user-dropdown-item-box">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('support-ticket-faq')); ?>">
                                            <span class="iconify" data-icon="bx:bx-help-circle"></span>
                                            <?php echo e(__('Help Support')); ?>

                                        </a>
                                    </li>
                                </ul>
                                <?php endif; ?>

                                <ul class="user-dropdown-item-box">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('logout')); ?>">
                                            <span class="iconify" data-icon="ic:round-logout"></span>
                                            <?php echo e(__('Logout')); ?>

                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <?php else: ?>

                            <?php if(!auth()->user()): ?>
                                <li class="nav-item menu-sign-in-btn">
                                    <a href="https://portal.cefonlineacademy.com" id="signInBtn" aria-current="page">
                                        <?php echo e(__('Dashboard')); ?>

                                    </a>
                                </li>
                            <?php else: ?>
                                <?php if(@$authUser->role == USER_ROLE_STUDENT): ?>
                                    <?php if(@$authUser->student?->status == 1): ?>
                                        <li class="nav-item menu-sign-in-btn">
                                            <a href="https://portal.cefonlineacademy.com" id="signInBtn" aria-current="page">
                                                <?php echo e(__('Dashboard')); ?>

                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="nav-item nav-panel-btn" >
                                            <?php if(checkPaymentPending($authUser)): ?>
                                                <a href="https://portal.cefonlineacademy.com" id="signInBtn" aria-current="page">
                                                    <?php echo e(__('Dashboard')); ?>

                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('student.cartList')); ?>" id="signInBtn" aria-current="page">
                                                    <?php echo e(__('Payment Pending')); ?>

                                                </a>
                                            <?php endif; ?>
                                           
                                        </li>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <li class="nav-item menu-sign-in-btn">
                                        <a href="https://portal.cefonlineacademy.com" id="signInBtn" aria-current="page">
                                            <?php echo e(__('Dashboard')); ?>

                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                          



                      

                        <li style="margin-left:2px" class="nav-item menu-sign-in-btn">
                            <a href="<?php echo e(route('sign-up-as-student')); ?>" aria-current="page">
                                <?php echo e(__('Book a Demo')); ?>

                            </a>
                        </li>

                        <?php if(Route::has('register')): ?>

                        <li class="nav-item menu-sign-in-btn">
                            <a href="<?php echo e(route('register')); ?>" class=" theme-button1 rounded-lg btn-nav" aria-current="page">
                                <?php echo e(__('Sign Up')); ?>

                            </a>
                        </li>

                        <?php endif; ?>

                        <?php endif; ?>

                        <?php endif; ?>

                    </ul>

                    <!-- <form action="#" class="mx-2">
                        <div class="input-group landing">
                            <button class="input-group-text pe-0" id="basic-addon1" style="background: transparent !important; border: none !important;"><img
                                    src="<?php echo e(asset('frontend/assets/img/icons-svg/search.svg')); ?>" alt="Search"></button>
                            <input class="form-control me-2 searchCourse" id="searchCourse" type="search" name="keyword"
                                value="<?php echo e(request('keyword')); ?>" placeholder="<?php echo e(__('Search Course')); ?>..."
                                aria-label="Search">
                        </div>
                        <div class="search-bar-suggestion-box searchBox d-none custom-scrollbar">
                            <ul class="appendCourseSearchList">
                            </ul>
                        </div>
                    </form> -->
                </div>
            </div>
        </div>
    </nav>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.login-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('login-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
</section>

<?php if(isEnableOpenAI()): ?> <?php echo $__env->make('addon.AI.content-generation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php endif; ?>

<script>
    
        // Add console logs to debug element selection
        const mobileMenu = document.querySelector('.main-menu-collapse');
        const mobileMenuButton = document.querySelector('.navbar-toggler');
        const closeButton = document.querySelector('.nav-close-mobile');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function(event) {
                event.stopPropagation(); 
                mobileMenu.classList.add('d-block');
                mobileMenu.classList.remove('d-none');
            });
        }
        
        // Close button handler
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                if (mobileMenu) {
                    mobileMenu.classList.remove('d-block');
                    mobileMenu.classList.add('d-none');
                }
            });
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (mobileMenu && mobileMenu.classList.contains('d-block')) {
                const isClickInside = mobileMenu.contains(event.target) || 
                                    mobileMenuButton.contains(event.target);
                
                if (!isClickInside) {
                    mobileMenu.classList.remove('d-block');
                    mobileMenu.classList.add('d-none');
                }
            }
        });
        
</script>


<script>
    const searchHover = document.querySelector('.search-hover');
    const formSearch = document.querySelector('.form-search');
    const searchInput = document.querySelector('#searchCourse');

    function showForm() {
        formSearch.style.display = 'block';
        searchHover.style.display = 'none'; 
    }

    function hideForm() {
        formSearch.style.display = 'none';  
        searchHover.style.display = 'block'; 
    }

    searchHover.addEventListener('click', showForm);

    searchInput.addEventListener('blur', hideForm);

    document.addEventListener('click', function(event) {
        if (!formSearch.contains(event.target) && !searchHover.contains(event.target)) {
            hideForm();
        }
    });
</script><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/frontend/layouts/navbar.blade.php ENDPATH**/ ?>