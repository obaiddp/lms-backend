<div class="customers__table all-course">
    <table id="resources-table" class="row-border data-table-filter table-style">
        
        <thead>
        <tr>
            <th><?php echo e(__('Sr. #')); ?></th>
            <th><?php echo e(__('Resource ID')); ?></th>
            <th><?php echo e(__('Resource Name')); ?></th>
            <th><?php echo e(__('Resource Category')); ?></th>
            <th><?php echo e(__('Resource Type')); ?></th>
            <th><?php echo e(__('Action')); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 0
            ?>
        <?php $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="removable-item">
                <td>
                   <?php echo e(++$i); ?>

                </td>
                <td>
                    <?php echo e('LMS-R-'); ?><?php echo e($resource->id); ?>

                </td>
                <td>
                       <?php if($resource->type == 'Text' || $resource->type == 'Youtube'): ?>
                            <a target="_blank"  href="<?php echo e(route('admin.resource.viewfullother', ['resource_id'=>$resource->id])); ?>" class="btn-action mr-30" title="View Resource">
                                <?php echo e($resource->name); ?>

                            </a>
                        <?php else: ?>
                             <?php if($resource->file_extension && $resource->file_name): ?>
                                <a target="_blank"  href="<?php echo e(route('admin.resource.viewfull', ['file_ext'=>$resource->file_extension , 'file_path'=> $resource->file_name])); ?>" class="btn-action mr-30" title="View Resource">
                                    <?php echo e($resource->name); ?>

                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                   
                </td>
                <td>
                    <?php echo e($resource->category->name); ?>

                </td>
                <td>
                    <?php if($resource->type == 'Text' || $resource->type == 'Youtube'): ?>
                        <?php echo e($resource->type); ?>

                    <?php else: ?>
                        <?php echo e($resource->file_extension); ?>

                    <?php endif; ?>
                </td>
                <td>

                    <div class="action__buttons">
                      
                        <?php if($resource->type == 'Text' || $resource->type == 'Youtube'): ?>
                            <a target="_blank"  href="<?php echo e(route('admin.resource.viewfullother', ['resource_id'=>$resource->id])); ?>" class="btn-action mr-30" title="View Resource">
                                <img src="<?php echo e(asset('admin/images/icons/eye.svg')); ?>" alt="view">
                            </a>
                        <?php else: ?>
                            <?php if($resource->file_extension && $resource->file_name): ?>
                                <a target="_blank"  href="<?php echo e(route('admin.resource.viewfull', ['file_ext'=>$resource->file_extension , 'file_path'=> $resource->file_name])); ?>" class="btn-action mr-30" title="View Resource">
                                    <img src="<?php echo e(asset('admin/images/icons/eye.svg')); ?>" alt="view">
                                </a>
                            <?php endif; ?>
                          
                        <?php endif; ?>
                      

                        <a  href="<?php echo e(route('admin.resource.edit', [$resource->id])); ?>" class="btn-action mr-30" title="Edit Details">
                            <img src="<?php echo e(asset('admin/images/icons/edit.svg')); ?>" alt="edit">
                        </a>
                      
                        <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_<?php echo e($resource->id); ?>">
                            <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                        </button>
                      
                        <form action="<?php echo e(route('admin.resource.delete', [$resource->id])); ?>" method="post" id="delete_row_form_<?php echo e($resource->id); ?>">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div class="mt-3">
        <?php echo e($resources->links()); ?>

    </div>
</div><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/resource/table-data.blade.php ENDPATH**/ ?>