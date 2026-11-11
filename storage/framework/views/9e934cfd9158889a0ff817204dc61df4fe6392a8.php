<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Donation Payments')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('All Donation Payments')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="search-container">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search Donation Payments...">
                        </div>
                        <br>

                        <div id="donation-payments-table-container">
                            <?php echo $__env->make('admin.student_payment.table-data-donation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>

                     
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
    <style>
        /* Loader Container */
        .table-loader {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 120px; /* Increased height for top area */
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-bottom: 1px solid #eee;
        }

        /* Spinner Animation */
        .spinner {
            width: 100px; /* Increased width */
            text-align: center;
            margin: 0 auto;
        }

        .spinner > div {
            width: 25px; /* Increased dot size */
            height: 25px; /* Increased dot size */
            background-color: #3498db;
            border-radius: 100%;
            display: inline-block;
            animation: bounce 1.4s infinite ease-in-out both;
            margin: 0 4px; /* Increased margin between dots */
        }

        .spinner .bounce1 {
            animation-delay: -0.32s;
        }

        .spinner .bounce2 {
            animation-delay: -0.16s;
        }

        @keyframes bounce {
            0%, 80%, 100% { 
                transform: scale(0);
            } 
            40% { 
                transform: scale(1.0);
            }
        }

        /* Make sure the customers__area has relative positioning */
        .customers__area {
            position: relative;
            min-height: 200px;
        }

        /* Add some padding to the table container to prevent overlap with loader */
        #donation-payments-table-container {
            padding-top: 10px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>


    <script>
        $(document).ready(function () {
          let searchTimer;
          const searchInput = $('#searchInput');
          const tableLoader = $('#table-loader');
          const tableContainer = $('#donation-payments-table-container');
       
          // Function to show loader
          function showLoader() {
              tableLoader.fadeIn(200);
              tableContainer.css('opacity', '0.5');
          }

          // Function to hide loader
          function hideLoader() {
              tableLoader.fadeOut(200);
              tableContainer.css('opacity', '1');
          }

          searchInput.on('input', function() {
              clearTimeout(searchTimer);
              searchTimer = setTimeout(performSearch, 500);
          });

          function performSearch() {
              const searchTerm = searchInput.val();
              showLoader(); // Show loader before making the request

              $.ajax({
                  url: '<?php echo e(route("student_payment.donation")); ?>',
                  type: 'GET',
                  data: {
                      search: searchTerm
                  },
                  success: function(response) {
                      $('#donation-payments-table-container').html(response.donationPayments);

                      // Reinitialize any necessary plugins or event listeners
                      initializeEventListeners();
                  },
                  error: function(xhr) {
                      console.error('Search failed:', xhr);
                      toastr.error('Search failed. Please try again.');
                  },
                  complete: function() {
                      hideLoader(); // Hide loader after request completes
                  }
              });
          }


          function initializeEventListeners() {
          
              // Handle pagination clicks
              $(document).on('click', '.pagination a', function(e) {
                  e.preventDefault();
                  let page = $(this).attr('href').split('page=')[1];
                  let searchTerm = searchInput.val();
                  showLoader(); // Show loader before making the request

                  $.ajax({
                      url: '<?php echo e(route("student_payment.donation")); ?>',
                      data: {
                          page: page,
                          search: searchTerm
                      },
                      success: function(response) {
                          $('#donation-payments-table-container').html(response.donationPayments);
                          initializeEventListeners();
                      },
                      complete: function() {
                          hideLoader(); // Hide loader after request completes
                      }
                  });
              });
          }

          // Initialize event listeners on page load
          initializeEventListeners();
      });
  </script>




    <script>
        'use strict'
        $(".status").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).closest('tr').find('.status option:selected').val();
            Swal.fire({
                title: "<?php echo e(__('Are you sure to change status?')); ?>",
                text: "<?php echo e(__('You won`t be able to revert this!')); ?>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "<?php echo e(__('Yes, Change it!')); ?>",
                cancelButtonText: "<?php echo e(__('No, cancel!')); ?>",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('admin.student.changeStudentStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Student status has been updated')); ?>");
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student_payment/donation.blade.php ENDPATH**/ ?>