      <div class="customers__table">
                            <table id="students-table" class="row-border data-table-filter table-style">
                                <input type="hidden" name="studentGetDataRoute" id="studentGetDataRoute" value="<?php echo e(route('student.getData')); ?>">
                          
                                <thead>
                                <tr class="removable-item">
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Details')); ?></th>
                                    <th><?php echo e(__('Is Featured')); ?></th>
                                    <th><?php echo e(__('Country')); ?></th>
                                    <th><?php echo e(__('Address')); ?></th>
                                    <th><?php echo e(__('Total Course Enroll')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                             <img src="<?php echo e(getImageFile($student->user ? @$student->user->image_path : '')); ?>" width="80">
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('student.view', [$student->uuid])); ?>" class="btn-action mr-30" title="View Details">
                                            <?php echo e(__('Name')); ?>: <?php echo e($student->name); ?><br>
                                            <?php echo e(__('Email')); ?>: <?php echo e($student->user?->email); ?><br>
                                            <?php echo e(__('Phone')); ?>: <?php echo e($student->phone_number ?? @$student->user->phone_number); ?><br>
                                            </a>
                                        </td>
                                        <td>
                                            <?php
                                                if($student->is_featured === 1){
                                                    $isFeatured = __('Yes');
                                                }
                                                else if($student->is_featured === 0){
                                                    $isFeatured = __('No');
                                                }
                                                else{
                                                    $isFeatured = __('');
                                                }
                                            ?>
                                            <?php echo e($isFeatured); ?>

                                        </td>
                                        <td><?php echo e($student->country ? $student->country->country_name : ''); ?></td>
                                        <td><?php echo e($student->address); ?></td>
                                        <td><?php echo e(studentCoursesCount($student->user_id)); ?></td>
                                        <td>
                                            <span id="hidden_id" style="display: none"><?php echo e($student->id); ?></span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="0" <?php if($student->status == 0): ?> selected <?php endif; ?>><?php echo e(__('Pending')); ?></option>
                                                <option value="1" <?php if($student->status == 1): ?> selected <?php endif; ?>><?php echo e(__('Approved')); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('student.view', [$student->uuid])); ?>" class="btn-action mr-30" title="View Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                                                </a>
                                                <a href="<?php echo e(route('student.edit', [$student->uuid])); ?>" class="btn-action mr-30" title="Edit Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <button class="ms-3">
                                                    <span data-formid="delete_row_form_<?php echo e($student->id); ?>" class="deleteItem">
                                                        <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                    </span>
                                                </button>

                                                <form action="<?php echo e(route('student.delete', [$student->uuid])); ?>" method="post" id="delete_row_form_<?php echo e($student->id); ?>">
                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($students->links()); ?>

                            </div>
                        </div><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student/table-data.blade.php ENDPATH**/ ?>