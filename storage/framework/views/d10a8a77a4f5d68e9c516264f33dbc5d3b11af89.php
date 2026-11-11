<div class="login-modal d-none" id="loginModalNew" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog login-dialog">
        <div style="max-height: 90vh; overflow-y: auto;" class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" id="closeLoginModal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="col-md-12">
                    <form method="POST" action="<?php echo e(route('login')); ?>">
                        <?php echo csrf_field(); ?>
                        <h5 class="mb-1 text-center h5"><?php echo e(__('Sign In')); ?></h5>
                        <p class="font-14 mb-30 text-center"><?php echo e(__('New User')); ?> ? 
                            <a data-placement="left" href="/sign-up-as-student" class="fw-500 w-100 mb-30 signUp" data-course_id="1" data-route="<?php echo e(route('sign-up-as-student-modal')); ?>/2/course-slug" data-coursetype="2" data-courseslug="course-slug" data-login_route="<?php echo e(route('sign-up-as-student-modal')); ?>/2/course-slug">
                                <span class="msgInfoChange"><?php echo e(__('Sign Up')); ?> 
                                </span>
                            </a>
                        </p>
                        <div class="row mb-30">
                            <div class="col-md-12">
                                <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Email or Phone')); ?></label>
                                <input required type="text" name="email" value="<?php echo e(old('email')); ?>" class="form-control email" placeholder="<?php echo e(__('Type your email or phone number')); ?>">
                                <?php if($errors->has('email')): ?> <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('email')); ?></span> <?php endif; ?>
                            </div>
                        </div>
                        <div class="row mb-30">
                            <div class="col-md-12">
                                <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Password')); ?></label>
                                <div class="form-group mb-0 position-relative">
                                    <input required class="form-control password" name="password" value="<?php echo e(old('password')); ?>" placeholder="*********" type="password">
                                    <span class="toggle cursor fas fa-eye pass-icon"></span>
                                </div>
                                <?php if($errors->has('password')): ?> <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('password')); ?></span> <?php endif; ?>
                            </div>
                        </div>
                        <div id="div-response">
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <button id="login_now" type="submit" class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100"><?php echo e(__('Sign In')); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('closeLoginModal').addEventListener('click', function() {
        $('#loginModalNew').removeClass('d-block');
        $('#loginModalNew').addClass('d-none');
    });
</script>
<!-- <script>
    document.getElementById('login_now').addEventListener('click', function(e) {
        e.preventDefault();
        var email = document.getElementById('loginModalNew2').elements['email'].value;
        var password = document.getElementById('loginModalNew2').elements['password'].value;
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        var data = {
            email: email,
            password: password
        };

        $.ajax({
            type: "POST",
            url: "<?php echo e(url('api/login')); ?>",
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                console.log(response);
                if(response.token) {
                    alert(response.message);
                    window.location.href = '/';
                    // Assuming response.user contains user details
                    // Storing user details in local storage for global access
                    localStorage.setItem('user', JSON.stringify(response.userDetails));
                    localStorage.setItem('token', JSON.stringify(response.token));
                    localStorage.setItem('userLogged', true);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Login failed. Please try again.');
            }
        });
    });
</script> -->

<style>
    .login-modal {
        width: 100%;
        background-color: #0000008c;
        position: fixed;
        height: 100vh;
        z-index: 9999;
    }
    .modal-dialog.login-dialog {
        background: white;
        border-radius: 10px;
        top: 4rem;
    }
    .btn-close {
        right: 10px !important;
    }
    span.toggle.cursor.pass-icon {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
</style>
<?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/components/login-modal.blade.php ENDPATH**/ ?>