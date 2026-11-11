<div class="email__sidebar bg-style">
    <div class="sidebar__item">
        <ul class="sidebar__mail__nav">
            <li>
                <a href="<?php echo e(route('settings.theme-setting')); ?>" class="list-item <?php echo e(@$themeSettingsActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Theme Settings')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.section-settings')); ?>" class="list-item <?php echo e(@$sectionSettingsActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Section Settings')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.banner-section')); ?>" class="list-item <?php echo e(@$bannerSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Banner Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.special-feature-section')); ?>" class="list-item <?php echo e(@$specialSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Special Feature Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.course-section')); ?>" class="list-item <?php echo e(@$courseSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Course Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.category-course-section')); ?>" class="list-item <?php echo e(@$categoryCourseSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Category Course Section')); ?></span>
                </a>
            </li>
            <?php if(isAddonInstalled('LMSZAIPRODUCT')): ?>
            <li>
                <a href="<?php echo e(route('settings.product-section')); ?>" class="list-item <?php echo e(@$productSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Product Section')); ?></span>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo e(route('settings.upcoming-course-section')); ?>" class="list-item <?php echo e(@$upcomingCourseSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Upcoming Course Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.bundle-course-section')); ?>" class="list-item <?php echo e(@$bundleCourseSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Bundle Course Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.top-category-section')); ?>" class="list-item <?php echo e(@$topCategorySectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Top Category Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.top-instructor-section')); ?>" class="list-item <?php echo e(@$topInstructorSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Top Instructor Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.become-instructor-video-section')); ?>" class="list-item <?php echo e(@$becomeInstructorVideoSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Become Instructor Video Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.customer-say-section')); ?>" class="list-item <?php echo e(@$customerSaySectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Customer Say Section')); ?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('settings.achievement-section')); ?>" class="list-item <?php echo e(@$achievementSectionActiveClass); ?>">
                    <img src="<?php echo e(asset('admin/images/heroicon/outline/cog.svg')); ?>" alt="icon">
                    <span><?php echo e(__('Achievement Section')); ?></span>
                </a>
            </li>

        </ul>
    </div>
</div>
<?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/home-sidebar.blade.php ENDPATH**/ ?>