<header class="header__area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="header__navbar">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_dashboard','all_dashboard'])): ?>
                    <div class="header__navbar__left">
                        <button class="sidebar-toggler">
                            <img src="<?php echo e(asset('admin/images/icons/header/bars.svg')); ?>" alt="">
                        </button>
                        <a href="<?php echo e(route('main.index')); ?>" class="btn btn-blue"><?php echo e(__('Visit Site')); ?></a>
                    </div>
                    <?php endif; ?>

                    <div class="header__navbar__right">
                        <ul class="header__menu">

                            <?php if(isEnableOpenAI()): ?> 
                            <!-- AI Option Start -->
                            <li>
                               <a id="ai-content-toggle" class="nav-link text-white" aria-current="page">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M8.356 5H7.01L5 13h1.028l.464-1.875h2.316L9.26 13h1.062Zm-1.729 5.322L7.644 5.95h.045l.984 4.373ZM11.238 13V5h1v8Zm.187 1H4V4h10v4.78a5.504 5.504 0 0 1 4-.786V6h-2V4a2.006 2.006 0 0 0-2-2h-2V0h-2v2H8V0H6v2H4a2.006 2.006 0 0 0-2 2v2H0v2h2v2H0v2h2v2a2.006 2.006 0 0 0 2 2h2v2h2v-2h2v2h2v-1.992A5.547 5.547 0 0 1 11.425 14Zm2.075-.5A3.5 3.5 0 1 1 17 17a3.499 3.499 0 0 1-3.5-3.5ZM17 19c-2.336 0-7 1.173-7 3.5V24h14v-1.5c0-2.328-4.664-3.5-7-3.5Z"/></svg>
                               </a>
                           </li>
                           <!-- AI Option End -->
                           <?php endif; ?>

                            <li class="admin-notification-menu position-relative">
                                <a href="#" class="btn btn-dropdown site-language" id="dropdownNotification" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo e(@$totalAdminNotifications); ?></span>
                                    <img src="<?php echo e(asset('admin/images/icons/header/notification.svg')); ?>" alt="icon">
                                </a>
                                <!-- Notification Dropdown Start -->
                                <div class="dropdown-menu" aria-labelledby="dropdownNotification">
                                    <ul class="dropdown-list custom-scrollbar">
                                    <?php $__empty_1 = true; $__currentLoopData = @$adminNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php if($notification->sender): ?>
                                            <li>
                                                <a href="<?php echo e(route('notification.url', [$notification->uuid])); ?>" class="message-user-item dropdown-item">
                                                    <div class="message-user-item-left">
                                                        <div class="single-notification-item d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <div class="user-img-wrap position-relative radius-50">
                                                                    <img src="<?php echo e(asset($notification->sender->image_path)); ?>" alt="img" class="radius-50">
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 ms-2">
                                                                <h6 class="color-heading font-14"><?php echo e($notification->sender->name); ?></h6>
                                                                <p class="font-13 mb-0"><?php echo e(__($notification->text)); ?></p>
                                                                <div class="font-11 color-gray mt-1"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <p class="text-center"><?php echo e(__('No Data Found')); ?></p>
                                    <?php endif; ?>
                                    </ul>
                                    <?php if(count($adminNotifications)): ?>
                                    <div class="dropdown-divider"></div>
                                    <form action="<?php echo e(route('notification.all-read')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item dropdown-footer">Mark all as read</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                                <!-- Notification Dropdown End -->
                            </li>

                            <li>
                                <a href="#" class="btn btn-dropdown site-language" id="dropdownLanguage" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo e(asset(selectedLanguage()->flag)); ?>" alt="icon">
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownLanguage">
                                    <?php $__currentLoopData = appLanguages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app_lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo e(url('/local/'.$app_lang->iso_code)); ?>">
                                                <img src="<?php echo e(asset($app_lang->flag)); ?>" alt="icon">
                                                <span><?php echo e($app_lang->language); ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </ul>
                            </li>
                            <li>
                                <a href="#" class="btn btn-dropdown user-profile" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo e(getImageFile(auth::user()->image_path)); ?>" alt="icon">
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownUser">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('admin.profile')); ?>">
                                            <img src="<?php echo e(asset('admin/images/icons/user.svg')); ?>" alt="icon">
                                            <span><?php echo e(__('Profile')); ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('admin.change-password')); ?>">
                                            <img src="<?php echo e(asset('admin/images/icons/settings.svg')); ?>" alt="icon">
                                            <span><?php echo e(__('Change Password')); ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo e(route('logout')); ?>">
                                            <img src="<?php echo e(asset('admin/images/icons/logout.svg')); ?>" alt="icon">
                                            <span><?php echo e(__('Logout')); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/common/header.blade.php ENDPATH**/ ?>