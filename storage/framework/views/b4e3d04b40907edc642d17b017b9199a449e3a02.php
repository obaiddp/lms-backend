<div class="customers__table">
    <table id="classes-table" class="row-border data-table-filter table-style">
        <thead>
        <tr>
            <th><?php echo e(__('Student')); ?></th>
            <th><?php echo e(__('Instructor')); ?></th>
            <th><?php echo e(__('Total Students')); ?></th>
            <th><?php echo e(__('Total Classes')); ?></th>
            <th><?php echo e(__('Duration')); ?></th>
            <th><?php echo e(__('Learning Tool')); ?></th>
            <th class="text-center"><?php echo e(__('Action')); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
        ?>
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="removable-item">
              
                <td>
                    <?php if(isset($class->student)): ?>
                    <a href="<?php echo e(route('student.view', [$class->student->uuid])); ?>" class="btn-action mr-30" title="View Details">
                    <?php echo e(// $class->student->first_name.' '.$class->student->last_name
                        $class->student?->user?->name); ?>

                    </a>
                    <?php endif; ?>
                    <?php if($class->free_class): ?>
                    <span style="color:white; font-size:12px;padding:4px" class="badge bg-success"><?php echo e(__('Free')); ?></span>
                <?php endif; ?>
                </td>
                <td>
                    <?php if(isset($class->instructor)): ?>
                    <a href="<?php echo e(route('instructor.view', [$class->instructor->uuid])); ?>"> 
                    
                    <?php echo e($class->instructor?->user?->name); ?>

                    </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo e($class->no_of_students); ?>

                </td>

                <td>
                    <a href="<?php echo e(route('classes.view_detail', [$class->id])); ?>" class="btn-action mr-30" title="View Details">
                    <?php echo e($class->no_of_classes); ?>

                    </a>
                </td>
                <td>
                    <?php echo e($class->duration); ?>

                </td>
           
                <td>
                    <?php echo e($class->learning_tool); ?>

                </td>
                <td>
                    <div class="action__buttons">
                        <a href="<?php echo e(route('classes.view_detail', [$class->id])); ?>" class="btn-action mr-30" title="View Details">
                            <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                        </a>
                    <?php if($class->free_class): ?>
                        <a href="<?php echo e(route('classes.edit_free_class', [$class->id])); ?>" class="btn-action mr-30" title="Edit Free Class">
                            <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('classes.edit', [$class->id])); ?>" class="btn-action mr-30" title="Edit Class">
                            <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                        </a>
                    <?php endif; ?>
                        
                        <a href="javascript:void(0);" data-url="<?php echo e(route('classes.delete', [$class->id])); ?>" title="Delete" class="btn-action deleteBtn">
                            <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div class="mt-3">
        <?php echo e($classes->links()); ?>

    </div>
</div><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/classes/table-data-individual.blade.php ENDPATH**/ ?>