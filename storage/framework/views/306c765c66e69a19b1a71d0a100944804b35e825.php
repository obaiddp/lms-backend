<div class="sidebar__area">
    <div class="sidebar__close">
        <button class="close-btn">
            <i class="fa fa-close"></i>
        </button>
    </div>

    <div class="sidebar__brand">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_dashboard','all_dashboard'])): ?>
        <a href="<?php echo e(route('admin.dashboard')); ?>">
            <?php if(get_option('app_logo') != ''): ?>
                <img src="<?php echo e(getImageFile(get_option('app_logo'))); ?>" alt="">
            <?php else: ?>
                <img src="" alt="">
            <?php endif; ?>
        </a>
        <?php endif; ?>
    </div>
    <ul id="sidebar-menu" class="sidebar__menu">

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_dashboard','all_dashboard'])): ?>
        <li class=" <?php echo e(active_if_full_match('admin/dashboard')); ?> ">
            <a href="<?php echo e(route('admin.dashboard')); ?>">
                <span class="iconify" data-icon="bxs:dashboard"></span>
                <span><?php echo e(__('Dashboard')); ?></span>
            </a>
        </li>
         <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_course','add_course', 'pending_course', 'hold_course', 'approved_course', 'all_course','manage_course_reference', 'manage_course_category', 'manage_course_subcategory', 'manage_course_tag', 'manage_course_language', 'manage_course_difficulty_level'])): ?>
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="dashicons:welcome-learn-more"></span>
                    <span><?php echo e(__('Courses')); ?></span>
                </a>
                <ul>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_course')): ?>
                    <li class="<?php echo e(active_if_full_match('admin/course')); ?>">
                        <a href="<?php echo e(route('admin.course.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_course')): ?>
                    <li class="<?php echo e(active_if_match('admin/course/add-course')); ?>">
                        <a target="_blank" href="<?php echo e(route('admin.course.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add Self Learning')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_course')): ?>
                    <li class="<?php echo e(active_if_match('admin/course/add-course-standard')); ?>">
                        <a  href="<?php echo e(route('admin.course.create_standard')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add Live')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_course')): ?>
                <li>
                    <a href="<?php echo e(route('admin.course.orders')); ?>" class="<?php echo e(@$subNavOrderProductActiveClass); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Orders')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pending_course')): ?>
                    <li class="<?php echo e(active_if_match('admin/course/review-pending')); ?>">
                        <a href="<?php echo e(route('admin.course.review_pending')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Review Pending')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('hold_course')): ?>
                    <li class="<?php echo e(active_if_match('admin/course/hold')); ?>">
                        <a href="<?php echo e(route('admin.course.hold')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Hold')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approved_course')): ?>
                    <li class="<?php echo e(active_if_match('admin/course/approved')); ?>">
                        <a href="<?php echo e(route('admin.course.approved')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Approved')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pending_course')): ?>
                    <li class="<?php echo e(active_if_match('admin/course/review-upcoming')); ?>">
                        <a href="<?php echo e(route('admin.course.review_upcoming')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Upcoming')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="<?php echo e(active_if_full_match('admin/course/enroll')); ?>">
                    <a href="<?php echo e(route('admin.course.enroll')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Enroll')); ?></span>
                    </a>
                </li>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_course_category')): ?>
                    <li class="<?php echo e(active_if_match('admin/category')); ?>">
                        <a href="<?php echo e(route('category.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Categories')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_course_subcategory')): ?>
                    <li class="<?php echo e(active_if_match('admin/subcategory')); ?>">
                        <a href="<?php echo e(route('subcategory.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Subcategory')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_course_tag')): ?>
                    <li class="<?php echo e(active_if_match('admin/tag')); ?>">
                        <a href="<?php echo e(route('tag.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Tags')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_course_language')): ?>
                    <li class="<?php echo e(active_if_match('admin/course-language')); ?>">
                        <a href="<?php echo e(route('course-language.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Languages')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_course_difficulty_level')): ?>
                    <li class="<?php echo e(active_if_match('admin/difficulty-level')); ?>">
                        <a href="<?php echo e(route('difficulty-level.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Difficulty Levels')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="<?php echo e(@$subNavSpecialPromotionIndexActiveClass); ?>">
                    <a href="<?php echo e(route('special_promotional_tag.index')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Promotional Tag')); ?></span>
                    </a>
                </li>
                <li class="<?php echo e(active_if_match('admin/course-upload-rules')); ?>">
                    <a href="<?php echo e(route('course-rules.index')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Rules & Benefits')); ?></span>
                    </a>
                </li>
                </ul>
            </li>
        <?php endif; ?>
    
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['product_module_product', 'product_module_tag', 'product_module_category'])): ?>
        <?php if(isAddonInstalled('LMSZAIPRODUCT')): ?>
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="gridicons:product"></span>
                    <span><?php echo e(__('Products')); ?>

                        <?php if(env('LOGIN_HELP') == 'active'): ?>
                        <span class="badge bg-warning">(Addon)</span>
                        <?php endif; ?>
                    </span>
                </a>
                <ul>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product_module_product')): ?>
                    <li class="<?php echo e(@$subNavProductActiveClass); ?>">
                        <a href="<?php echo e(route('lms_product.admin.product.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_match('admin/product/pending')); ?>">
                        <a href="<?php echo e(route('lms_product.admin.product.pending')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Pending')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_match('admin/product/approved')); ?>">
                        <a href="<?php echo e(route('lms_product.admin.product.approved')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Approved')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_match('admin/product/hold')); ?>">
                        <a href="<?php echo e(route('lms_product.admin.product.hold')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Hold')); ?></span>
                        </a>
                    </li>

                    <?php if(isAddonInstalled('LMSZAIPRODUCT')): ?>
                    <li>
                        <a href="<?php echo e(route('lms_product.admin.product.dashboard')); ?>" class="<?php echo e(@$subNavProductDashboardActiveClass); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Dashboard')); ?></span>
                        </a>
                    </li>
                  
                    <li>
                        <a href="<?php echo e(route('lms_product.admin.product.orders')); ?>" class="<?php echo e(@$subNavOrderProductActiveClass); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Orders')); ?></span>
                        </a>
                    </li>

                    <?php endif; ?>
                    <li class="<?php echo e(active_if_match('admin/product/add')); ?>">
                        <a href="<?php echo e(route('lms_product.admin.product.create',['step'=>"step-1"])); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                    
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product_module_category')): ?>
                    <li class="<?php echo e(active_if_match('admin/product-category')); ?>">
                        <a href="<?php echo e(route('lms_product.admin.category.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Category')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('product_module_tag')): ?>
                    <li class="<?php echo e(active_if_match('admin/product-tag')); ?>">
                        <a href="<?php echo e(route('lms_product.admin.tag.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Tags')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_instructor', 'pending_instructor', 'approved_instructor', 'all_instructor'])): ?>
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                    <span><?php echo e(__('Instructors')); ?></span>
                </a>
                <ul>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_instructor')): ?>
                        <li class="
                        <?php echo e(active_if_full_match('admin/instructor')); ?>

                        <?php echo e(active_if_match('admin/instructor/view/*')); ?>

                    ">
                            <a href="<?php echo e(route('instructor.index')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('All')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_instructor')): ?>
                    <li class="<?php echo e(active_if_match('admin/instructor/create')); ?>">
                        <a href="<?php echo e(route('instructor.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pending_instructor')): ?>
                        <li class="<?php echo e(active_if_match('admin/instructor/pending')); ?>">
                            <a href="<?php echo e(route('instructor.pending')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Pending')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approved_instructor')): ?>
                        <li class="<?php echo e(active_if_match('admin/instructor/approved')); ?>">
                            <a href="<?php echo e(route('instructor.approved')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Approved')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approved_instructor')): ?>
                        <li class="<?php echo e(active_if_match('admin/instructor/blocked')); ?>">
                            <a href="<?php echo e(route('instructor.blocked')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Blocked')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>


        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_coordinator','add_coordinator', 'blocked_coordinator', 'all_coordinator'])): ?>
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                    <span><?php echo e(__('Coordinators')); ?></span>
                </a>
                <ul>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_coordinator')): ?>
                        <li class="
                        <?php echo e(active_if_full_match('admin/coordinator')); ?>

                        <?php echo e(active_if_match('admin/coordinator/view/*')); ?>

                    ">
                            <a href="<?php echo e(route('coordinator.index')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('All')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_coordinator')): ?>
                    <li class="<?php echo e(active_if_match('admin/coordinator/create')); ?>">
                        <a href="<?php echo e(route('coordinator.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('blocked_coordinator')): ?>
                        <li class="<?php echo e(active_if_match('admin/coordinator/blocked')); ?>">
                            <a href="<?php echo e(route('coordinator.blocked')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Blocked')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_resources','manage_resource_categories'])): ?>
                <li class=" <?php echo e(active_if_full_match('admin/resource')); ?> ">
                    <a class="has-arrow" href="#">
                        <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                        <span><?php echo e(__('Resources')); ?></span>
                    </a>
                    <ul>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_resource_categories')): ?>
                        <li class="<?php echo e(active_if_match('admin/resource/category/view')); ?>">
                            <a href="<?php echo e(route('admin.resource.category.index')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('View Categories')); ?></span>
                            </a>
                        </li>
                        
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_resources')): ?>
                        <li class="<?php echo e(active_if_match('admin/resource/view')); ?>">
                            <a href="<?php echo e(route('admin.resource.index')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('View All')); ?></span>
                            </a>
                        </li>
                        <li class="<?php echo e(active_if_match('admin/resource/add')); ?>">
                            <a href="<?php echo e(route('admin.resource.add')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Add New')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_classes','add_classes', 'all_classes','all_individual','all_group'])): ?>
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                    <span><?php echo e(__('Classes')); ?></span>
                </a>
                <ul>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_classes')): ?>

                        <li class="
                        <?php echo e(active_if_full_match('admin/classes')); ?>

                        <?php echo e(active_if_match('admin/classes/view_detail/*')); ?>

                    ">
                            <a href="<?php echo e(route('classes.index')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('All')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_individual')): ?>
                        <li class="
                                <?php echo e(active_if_full_match('admin/classes/view_individual_classes')); ?>

                                <?php echo e(active_if_match('admin/classes/view_detail/*')); ?>

                            ">
                            <a href="<?php echo e(route('classes.view_individual_classes')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Individual')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_group')): ?>

                            <li class="
                            <?php echo e(active_if_full_match('admin/classes/view_group_classes')); ?>

                            <?php echo e(active_if_match('admin/classes/view_detail/*')); ?>

                        ">
                                <a href="<?php echo e(route('classes.view_group_classes')); ?>">
                                    <i class="fa fa-circle"></i>
                                    <span><?php echo e(__('Group')); ?></span>
                                </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_classes')): ?>
                        <li class="<?php echo e(active_if_match('admin/classes/create_individual')); ?>">
                            <a href="<?php echo e(route('classes.create_individual')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Add Individual')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_classes')): ?>
                        <li class="<?php echo e(active_if_match('admin/classes/create_group')); ?>">
                            <a href="<?php echo e(route('classes.create_group')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Add Group')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_individual')): ?>
                        <li class="
                                <?php echo e(active_if_match('admin/classes/view_individual_classes_history')); ?>

                            ">
                            <a href="<?php echo e(route('classes.view_individual_classes_history')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Individual Classes History')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_group')): ?>
                    <li class="
                            <?php echo e(active_if_match('admin/classes/view_group_classes_history')); ?>

                        ">
                        <a href="<?php echo e(route('classes.view_group_classes_history')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Group Classes History')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>



    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_time_slot','add_time_slot','all_time_slots'])): ?>
        <li>
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                <span><?php echo e(__('TimeSlots')); ?></span>
            </a>
            <ul>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_time_slots')): ?>
                    <li class="
                    <?php echo e(active_if_full_match('admin/time_slots')); ?>

                    <?php echo e(active_if_match('admin/time_slots/view/*')); ?>

                ">
                        <a href="<?php echo e(route('time_slots.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_time_slot')): ?>
                <li class="<?php echo e(active_if_match('admin/time_slots/create')); ?>">
                    <a href="<?php echo e(route('time_slots.create')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Add New')); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; ?>




        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_organization', 'pending_organization', 'approved_organization', 'all_organization'])): ?>
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="la:chalkboard-teacher"></span>
                    <span><?php echo e(__('Organizations')); ?></span>
                </a>
                <ul>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_organization')): ?>

                        <li class="
                        <?php echo e(active_if_full_match('admin/organizations')); ?>

                        <?php echo e(active_if_match('admin/organizations/view/*')); ?>

                    ">
                            <a href="<?php echo e(route('organizations.index')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('All')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_organization')): ?>
                    <li class="<?php echo e(active_if_match('admin/organizations/create')); ?>">
                        <a href="<?php echo e(route('organizations.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pending_organization')): ?>
                        <li class="<?php echo e(active_if_match('admin/organizations/pending')); ?>">
                            <a href="<?php echo e(route('organizations.pending')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Pending')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approved_organization')): ?>
                        <li class="<?php echo e(active_if_match('admin/organizations/approved')); ?>">
                            <a href="<?php echo e(route('organizations.approved')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Approved')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('approved_organization')): ?>
                        <li class="<?php echo e(active_if_match('admin/organizations/blocked')); ?>">
                            <a href="<?php echo e(route('organizations.blocked')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Blocked')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_student')): ?>
            <li class=" <?php echo e(active_if_full_match('admin/student')); ?> ">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span><?php echo e(__('Students')); ?></span>
                </a>
                <ul>
                    <li class="<?php echo e(active_if_match('admin/student')); ?>">
                        <a href="<?php echo e(route('student.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All')); ?></span>
                        </a>
                    </li>
                    <?php if(get_option('private_mode')): ?>
                    <li class="<?php echo e(active_if_match('admin/student/pending')); ?>">
                        <a href="<?php echo e(route('student.pending_list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Pending')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="<?php echo e(active_if_match('admin/student/create')); ?>">
                        <a href="<?php echo e(route('student.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_match('admin/student/free_classes')); ?>">
                        <a href="<?php echo e(route('student.free_classes')); ?>">
                            <i class="fa fa-circle"></i>
                            
                            <span><?php echo e(__('New Enrollments')); ?></span>
                        </a>
                    </li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['resend_monthly_fee_invoice', 'manage_student'])): ?>
                    <li class="<?php echo e(active_if_match('admin/monthly-fee-invoices')); ?>">
                        <a href="<?php echo e(route('admin.monthly-fee-invoices.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Fee Invoice Emails')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['resend_payment_receipt', 'manage_student'])): ?>
                    <li class="<?php echo e(active_if_match('admin/payment-receipt-emails')); ?>">
                        <a href="<?php echo e(route('admin.payment-receipt-emails.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Payment Receipt Emails')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="<?php echo e(active_if_match('admin/student_payment/donation')); ?>">
                        <a href="<?php echo e(route('student_payment.donation')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Pending Donations')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_match('admin/student_payment/pending')); ?>">
                        <a href="<?php echo e(route('student_payment.pending')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Pending Payments')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_match('admin/student_payment/completed_donation')); ?>">
                        <a href="<?php echo e(route('student_payment.completed_donation')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Completed Donations')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_match('admin/student_payment/completed')); ?>">
                        <a href="<?php echo e(route('student_payment.completed')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Completed Payments')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_match('admin/student_payment/add_payment')); ?>">
                        <a href="<?php echo e(route('student_payment.add_payment')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add Payment')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>




        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_licenses','add_license','all_licenses','delete_license','update_license'])): ?>
            <li class=" <?php echo e(active_if_full_match('admin/license')); ?> ">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span><?php echo e(__('Licenses')); ?></span>
                </a>
                <ul>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('all_licenses')): ?>
                        <li class="<?php echo e(active_if_match('admin/license')); ?>">
                            <a href="<?php echo e(route('admin.license.index')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('All')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_license')): ?>
                        <li class="<?php echo e(active_if_match('admin/license/create')); ?>">
                            <a href="<?php echo e(route('admin.license.create')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Add New')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>



        <?php if(get_option('subscription_mode')): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_subscriptions')): ?>
            <li class="<?php echo e(@$navSubscriptionParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span><?php echo e(__('Subscriptions')); ?></span>
                </a>
                <ul class="<?php echo e(@$navSubscriptionParentShowClass); ?>">
                    <li class="<?php echo e(@$subNavSubscriptionActiveClass); ?>">
                        <a href="<?php echo e(route('admin.subscriptions.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e((request()->route()->getName() == "admin.subscriptions.create") ? 'mm-active' : ''); ?>">
                        <a href="<?php echo e(route('admin.subscriptions.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e((request()->route()->getName() == "admin.subscriptions.purchase_pending_list") ? 'mm-active' : ''); ?>">
                        <a href="<?php echo e(route('admin.subscriptions.purchase_pending_list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Sale Pending')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e((request()->route()->getName() == "admin.subscriptions.purchase_list") ? 'mm-active' : ''); ?>">
                        <a href="<?php echo e(route('admin.subscriptions.purchase_list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Sale')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if(get_option('wallet_recharge_system', 0)): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_wallet_recharge')): ?>
            <li class="<?php echo e(@$navWalletParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span><?php echo e(__('Wallets')); ?></span>
                </a>
                <ul class="<?php echo e(@$navWalletParentShowClass); ?>">
                    <li class="<?php echo e((request()->route()->getName() == "admin.wallet_recharge.pending_list") ? 'mm-active' : ''); ?>">
                        <a href="<?php echo e(route('admin.wallet_recharge.pending_list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Recharge Pending')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e((request()->route()->getName() == "admin.wallet_recharge.list") ? 'mm-active' : ''); ?>">
                        <a href="<?php echo e(route('admin.wallet_recharge.list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Recharge List')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if(get_option('saas_mode')): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_saas')): ?>
            <li class=" <?php echo e(@$navSaasParentActiveClass); ?> ">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ph:student"></span>
                    <span><?php echo e(__('SaaS')); ?></span>
                </a>
                <ul class="<?php echo e(@$navSaasParentShowClass); ?>">
                    <li class="<?php echo e(@$subNavSaasActiveClass); ?>">
                        <a href="<?php echo e(route('admin.saas.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All ')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e((request()->route()->getName() == "admin.saas.create") ? 'mm-active' : ''); ?>">
                        <a href="<?php echo e(route('admin.saas.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e((request()->route()->getName() == "admin.saas.purchase_pending_list") ? 'mm-active' : ''); ?>">
                        <a href="<?php echo e(route('admin.saas.purchase_pending_list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Sale Pending')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e((request()->route()->getName() == "admin.saas.purchase_list") ? 'mm-active' : ''); ?>">
                        <a href="<?php echo e(route('admin.saas.purchase_list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Sale')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_coupon')): ?>
            <li class="<?php echo e(@$navCouponActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ri:coupon-3-fill"></span>
                    <span><?php echo e(__('Coupons')); ?></span>
                </a>
                <ul>
                    <li class="<?php echo e(@$subNavCouponIndexActiveClass); ?>">
                        <a href="<?php echo e(route('coupon.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('List')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$navCouponAddActiveClass); ?>">
                        <a href="<?php echo e(route('coupon.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_promotion')): ?>
        <li class="<?php echo e(@$navPromotionParentActiveClass); ?>">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="ic:round-discount"></span>
                <span><?php echo e(__('Promotions')); ?></span>
            </a>
            <ul>
                <li class="<?php echo e(@$subNavPromotionIndexActiveClass); ?>">
                    <a href="<?php echo e(route('promotion.index')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('List')); ?></span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavAddPromotionActiveClass); ?>">
                    <a href="<?php echo e(route('promotion.create')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Add New')); ?></span>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payout')): ?>
            <li class="<?php echo e(@$navFinanceParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="clarity:dollar-bill-solid"></span>
                    <span><?php echo e(__('Payouts')); ?></span>
                </a>
                <ul class="<?php echo e(@$navFinanceShowClass); ?>">
                    <li class="<?php echo e(@$subNavFinanceNewWithdrawActiveClass); ?>">
                        <a href="<?php echo e(route('payout.new-withdraw')); ?>">
                            <i class="fa fa-circle"></i>
                            <span> <?php echo e(__('Request')); ?></span>
                        </a>
                    </li>

                    <li class="<?php echo e(@$subNavFinanceCompleteWithdrawActiveClass); ?>">
                        <a href="<?php echo e(route('payout.complete-withdraw')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Complete')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavFinancerejectedWithdrawActiveClass); ?>">
                        <a href="<?php echo e(route('payout.rejected-withdraw')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Rejected')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavFinanceDistributeSubscriptionActiveClass); ?>">
                        <a href="<?php echo e(route('payout.distribute.subscriptions')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Subscription')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>


        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('finance')): ?>
            <li class="">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="map:finance"></span>
                    <span><?php echo e(__('Financial Reports')); ?></span>
                </a>
                <ul>
                    <li class="<?php echo e(active_if_full_match('admin/report/course-revenue-report')); ?><?php echo e(active_if_full_match('admin/report/bundle-revenue-report')); ?><?php echo e(active_if_full_match('admin/report/consultation-revenue-report')); ?>">
                        <a href="<?php echo e(route('course-report.revenue-report')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Revenue')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_full_match('admin/report/order-report')); ?>">
                        <a href="<?php echo e(route('report.order-report')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Order')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_full_match('admin/report/order-pending')); ?>">
                        <a href="<?php echo e(route('report.order-pending')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Order Pending')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_full_match('admin/report/order-cancelled')); ?>">
                        <a href="<?php echo e(route('report.order-cancelled')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Order Cancelled')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_full_match('admin/report/cancel-consultation-list')); ?>">
                        <a href="<?php echo e(route('report.cancel-consultation-list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Consultation Cancel')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_full_match('admin/report/student-fee-ledger')); ?>">
                        <a href="<?php echo e(route('report.student-fee-ledger')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Student Fee Ledger')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_certificate')): ?>
            <li class="<?php echo e(@$navCertificateActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="fluent:certificate-20-filled"></span>
                    <span><?php echo e(__('Certificates')); ?></span>
                </a>
                <ul>
                    <li class="<?php echo e(@$subNavAllCertificateActiveClass); ?>">
                        <a href="<?php echo e(route('certificate.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavAddCertificateActiveClass); ?>">
                        <a href="<?php echo e(route('certificate.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add New')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>


        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('open_ai_setting')): ?>
        <?php if(isAddonInstalled('LMSZAIAI')): ?>
            <li class="<?php echo e(@$navWriteAISettingParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="eos-icons:ai"></span>
                    <span><?php echo e(__('Write AI Settings')); ?>

                        <?php if(env('LOGIN_HELP') == 'active'): ?>
                        <span class="badge bg-warning">(Addon)</span>
                        <?php endif; ?>
                    </span>
                </a>
                <ul class="">
                    <li class="<?php echo e(@$subNavOpenAISettingsActiveClass); ?>">
                        <a href="<?php echo e(route('ai.open_ai_setting')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Write AI Setting')); ?></span>
                        </a>
                    </li>

                    <li class="<?php echo e(@$subNavOpenAIContentActiveClass); ?>">
                        <a href="<?php echo e(route('ai.generated_content_list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Generated Content')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ranking_level')): ?>
            <li class="<?php echo e(@$navRankingActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="fa6-solid:ranking-star"></span>
                    <span><?php echo e(__('Badges')); ?></span>
                </a>
                <ul>
                    <li class="<?php echo e(@$subNavRankingActiveClass); ?>">
                        <a href="<?php echo e(route('ranking.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Badges')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('skill')): ?>
            <li class="<?php echo e(@$subNavSkillActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="carbon:skill-level-intermediate"></span>
                    <span><?php echo e(__('Skills')); ?></span>
                </a>
                <ul>
                    <li class="<?php echo e(@$subNavSkillActiveClass); ?>">
                        <a href="<?php echo e(route('skill.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Skills')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_language')): ?>
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="fa6-solid:language"></span>
                    <span><?php echo e(__('Languages')); ?></span>
                </a>
                <ul>
                    <li class="<?php echo e(active_if_match('admin/language')); ?>">
                        <a href="<?php echo e(route('language.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Settings')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('support_ticket')): ?>
            <li class="<?php echo e(@$navSupportTicketParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="ic:twotone-support-agent"></span>
                    <span><?php echo e(__('Support Tickets')); ?></span>
                </a>
                <ul class="">
                    <li class="<?php echo e(@$subNavSupportTicketIndexActiveClass); ?>">
                        <a href="<?php echo e(route('support-ticket.admin.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span> <?php echo e(__('All')); ?> </span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavSupportTicketOpenActiveClass); ?>">
                        <a href="<?php echo e(route('support-ticket.admin.open')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Open')); ?></span>
                        </a>
                    </li>

                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_management')): ?>
            <li class="<?php echo e(@$navUserParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="bxs:user-account"></span>
                    <span><?php echo e(__('Admin Management')); ?></span>
                </a>
                <ul class="<?php echo e(@$navUserParentShowClass); ?>">
                    <li class="<?php echo e(@$subNavUserCreateActiveClass); ?>">
                        <a href="<?php echo e(route('user.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span> <?php echo e(__('Add User')); ?> </span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavUserActiveClass); ?>">
                        <a href="<?php echo e(route('user.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All Users')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavUserRoleActiveClass); ?>">
                        <a href="<?php echo e(route('role.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Roles')); ?></span>
                        </a>
                    </li>


                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_management')): ?>
            <li class="<?php echo e(@$navEmailActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="dashicons:email"></span>
                    <span><?php echo e(__('Email Management')); ?></span>
                </a>
                <ul class="<?php echo e(@$navEmailParentShowClass); ?>">
                    <li class="<?php echo e(@$subNavEmailTemplateActiveClass); ?>">
                        <a href="<?php echo e(route('email-template.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Template')); ?></span>
                        </a>
                    </li>

                    <li class="<?php echo e(@$subNavSendMailActiveClass); ?>">
                        <a href="<?php echo e(route('email-template.send-email')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Send')); ?></span>
                        </a>
                    </li>

                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('email_notification_template')): ?>
            <li class="<?php echo e($navEmailNotificationActiveClass ?? ''); ?>">
                <a href="<?php echo e(route('email-notification.index')); ?>">
                    <span class="iconify" data-icon="dashicons:email"></span>
                    <span><?php echo e(__('Email Notification')); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('page_management')): ?>
        <li class="<?php echo e(@$navPageParentActiveClass); ?>">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="dashicons:edit-page"></span>
                <span><?php echo e(__('Pages')); ?></span>
            </a>
            <ul class="">
                <li class="<?php echo e(@$subNavPageIndexActiveClass); ?>">
                    <a href="<?php echo e(route('page.index')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('All')); ?></span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavPageAddActiveClass); ?>">
                    <a href="<?php echo e(route('page.create')); ?>">
                        <i class="fa fa-circle"></i>
                        <span> <?php echo e(__('Add New')); ?> </span>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('menu_management')): ?>
        <li class="<?php echo e(@$navMenuParentActiveClass); ?>">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="bi:menu-up"></span>
                <span><?php echo e(__('Menus')); ?></span>
            </a>
            <ul class="">
                <li class="<?php echo e(@$subNavStaticMenuIndexActiveClass); ?>">
                    <a href="<?php echo e(route('menu.static')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Nav')); ?></span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavDynamicMenuIndexActiveClass); ?>">
                    <a href="<?php echo e(route('menu.dynamic')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Nav Dynamic')); ?></span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavFooterCompanyMenuIndexActiveClass); ?>">
                    <a href="<?php echo e(route('menu.footer-company')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Footer Left')); ?></span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavFooterSupportMenuIndexActiveClass); ?>">
                    <a href="<?php echo e(route('menu.footer-support')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Footer Right')); ?></span>
                    </a>
                </li>

            </ul>
        </li>
        <?php endif; ?>


        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['application_setting', 'global_setting', 'home_setting', 'mail_configuration', 'payment_option', 'content_setting'])): ?>
            <li class="<?php echo e(@$navApplicationSettingParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="mdi:application-cog-outline"></span>
                    <span><?php echo e(__('Application Settings')); ?></span>
                </a>
                <ul class="">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('global_setting')): ?>
                        <li class="<?php echo e(@$subNavGlobalSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.general_setting')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Global')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>


                    <li class="<?php echo e(@$subNavLocationSettingsActiveClass); ?>">
                        <a href="<?php echo e(route('settings.location.country.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Location')); ?></span>
                        </a>
                    </li>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('home_setting')): ?>
                        <li class="<?php echo e(@$subNavHomeSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.theme-setting')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Home')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('mail_configuration')): ?>

                        <li class="<?php echo e(@$subNavMailConfigSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.mail-configuration')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Mail Configuration')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payment_option')): ?>
                        <li class="<?php echo e(@$subNavPaymentOptionsSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.payment_method_settings')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Payment Options')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('content_setting')): ?>
                        <li class="<?php echo e(@$subNavInstructorSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.instructor-feature')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Become Instructor')); ?></span>
                            </a>
                        </li>

                        <li class="<?php echo e(@$subNavFAQSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.faq.cms')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('FAQ')); ?></span>
                            </a>
                        </li>
                        <li class="<?php echo e(@$subNavSupportSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.support-ticket.cms')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Support Ticket')); ?></span>
                            </a>
                        </li>

                        <li class="<?php echo e(@$subNavAboutUsSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.about.gallery-area')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('About Us')); ?></span>
                            </a>
                        </li>

                        <li class="<?php echo e(@$subNavContactUsSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.contact.cms')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Contact Us')); ?></span>
                            </a>
                        </li>
                        <li class="<?php echo e(@$subNavMaintenanceModeActiveClass); ?>">
                            <a href="<?php echo e(route('settings.maintenance')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Maintenance Mode')); ?></span>
                            </a>
                        </li>
                        <li class="<?php echo e(@$subNavComingSoonModeSettingsActiveClass); ?>">
                            <a href="<?php echo e(route('settings.coming-soon')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Coming Soon Mode')); ?></span>
                            </a>
                        </li>
                        <li class="<?php echo e(@$subNavCacheActiveClass); ?>">
                            <a href="<?php echo e(route('settings.cache-settings')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Cache')); ?></span>
                            </a>
                        </li>
                        <li class="<?php echo e(@$subNavMigrateActiveClass); ?>">
                            <a href="<?php echo e(route('settings.migrate-settings')); ?>">
                                <i class="fa fa-circle"></i>
                                <span><?php echo e(__('Migrate')); ?></span>
                            </a>
                        </li>

                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('policy_management')): ?>
        <li class="<?php echo e(@$navPolicyActiveClass); ?>">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="dashicons:privacy"></span>
                <span><?php echo e(__('Policy Settings')); ?></span>
            </a>
            <ul>
                <li class="<?php echo e(@$subNavTermConditionsActiveClass); ?>">
                    <a href="<?php echo e(route('admin.terms-conditions')); ?>">
                        <i class="fa fa-circle"></i>
                        <span> <?php echo e(__('Terms Conditions')); ?> </span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavPrivacyPolicyActiveClass); ?>">
                    <a href="<?php echo e(route('admin.privacy-policy')); ?>">
                        <i class="fa fa-circle"></i>
                        <span> <?php echo e(__('Privacy Policy')); ?> </span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavRefundPolicyActiveClass); ?>">
                    <a href="<?php echo e(route('admin.refund-policy')); ?>">
                        <i class="fa fa-circle"></i>
                        <span> <?php echo e(__('Refund Policy')); ?> </span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavCookiePolicyActiveClass); ?>">
                    <a href="<?php echo e(route('admin.cookie-policy')); ?>">
                        <i class="fa fa-circle"></i>
                        <span> <?php echo e(__('Cookie Policy')); ?> </span>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('content_setting')): ?>
            <li class="<?php echo e(@$navContactUsParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="fluent:contact-card-32-regular"></span>
                    <span><?php echo e(__('Contact Us')); ?></span>
                </a>
                <ul class="<?php echo e(@$navContactUsParentShowClass); ?>">
                    <li class="<?php echo e(@$subNavContactUsIndexActiveClass); ?>">
                        <a href="<?php echo e(route('contact.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span> <?php echo e(__('All')); ?> </span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavContactUsIssueIndexActiveClass); ?>">
                        <a href="<?php echo e(route('contact.issue.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All Issues')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavContactUsIssueAddActiveClass); ?>">
                        <a href="<?php echo e(route('contact.issue.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add Issue')); ?></span>
                        </a>
                    </li>

                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_blog')): ?>
            <li class="<?php echo e(@$navBlogParentActiveClass); ?>">
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="jam:blogger-square"></span>
                    <span><?php echo e(__('Blogs')); ?> </span>
                </a>
                <ul>
                    <li class="<?php echo e(active_if_full_match('admin/blog')); ?> <?php echo e(active_if_full_match('admin/blog/edit/*')); ?>">
                        <a href="<?php echo e(route('blog.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('All')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_full_match('admin/blog/create')); ?>">
                        <a href="<?php echo e(route('blog.create')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Add')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavBlogCommentListActiveClass); ?>">
                        <a href="<?php echo e(route('blog.blog-comment-list')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Comment List')); ?></span>
                        </a>
                    </li>
                    <li class="<?php echo e(@$subNavBlogCategoryIndexActiveClass); ?>">
                        <a href="<?php echo e(route('blog.blog-category.index')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Category')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('forum_management')): ?>
        <li class="<?php echo e(@$navForumParentActiveClass); ?>">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="carbon:forum"></span>
                <span><?php echo e(__('Forums')); ?></span>
            </a>
            <ul>
                <li class="<?php echo e(@$subNavForumCategoryIndexActiveClass); ?>">
                    <a href="<?php echo e(route('admin.forum.category.index')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Category')); ?></span>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('account_setting')): ?>
            <li>
                <a class="has-arrow" href="#">
                    <span class="iconify" data-icon="bytesize:settings"></span>
                    <span><?php echo e(__('Account Settings')); ?></span>
                </a>
                <ul class="<?php echo e(@$navUserParentShowClass); ?>">
                    <li class="<?php echo e(active_if_full_match('admin/profile')); ?>">
                        <a href="<?php echo e(route('admin.profile')); ?>">
                            <i class="fa fa-circle"></i>
                            <span> <?php echo e(__('Profile')); ?> </span>
                        </a>
                    </li>
                    <li class="<?php echo e(active_if_full_match('admin/profile/change-password')); ?>">
                        <a href="<?php echo e(route('admin.change-password')); ?>">
                            <i class="fa fa-circle"></i>
                            <span><?php echo e(__('Change Password')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_affiliate')): ?>
        <li class="">
            <a class="has-arrow" href="#">
                <span class="iconify" data-icon="tabler:affiliate"></span>
                <span><?php echo e(__('Affiliates')); ?></span>
            </a>
            <ul class="">
                <li class="<?php echo e(@$subNavAffiliateManageListActiveClass); ?>">
                    <a href="<?php echo e(route('affiliate.affiliate-request-list')); ?>">
                        <i class="fa fa-circle"></i>
                        <span> <?php echo e(__('Request List')); ?> </span>
                    </a>
                </li>
                <li class="<?php echo e(active_if_full_match('admin/affiliate/affiliation-settings')); ?>">
                    <a href="<?php echo e(route('affiliate.affiliation-settings')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('Settings')); ?></span>
                    </a>
                </li>
                <li class="<?php echo e(@$subNavAffiliateHistoryActiveClass); ?>">
                    <a href="<?php echo e(route('affiliate.affiliate-history')); ?>">
                        <i class="fa fa-circle"></i>
                        <span><?php echo e(__('History')); ?></span>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_version_update')): ?>
            <li class="<?php echo e(@$subNavVersionUpdateActiveClass); ?>">
                <a href="<?php echo e(route('settings.file-version-update')); ?>">
                    <i class="fa fa-circle"></i>
                    <span><?php echo e(__('Version Update')); ?></span>
                </a>
            </li>
        <?php endif; ?>

        <li class="mb-5 text-center">
            <a href="#">
                <span>
                    <h3><?php echo e(__('Software Version')); ?> <?php echo e(get_option('current_version', 2.4)); ?></h3>
                </span>
            </a>
        </li>
    </ul>
</div>
<?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/common/sidebar.blade.php ENDPATH**/ ?>