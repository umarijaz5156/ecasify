<?php
    use App\Models\Utility;
    $logo = asset('storage/uploads/logo/');
    $company_favicon = Utility::getValByName('company_favicon');
    $SITE_RTL = env('SITE_RTL');
    $setting = \App\Models\Utility::colorset();
    $seo_setting = App\Models\Utility::getSeoSetting();
    $color = 'theme-1';
    if (!empty($setting['color'])) {
        $color = $setting['color'];
    }

    $SITE_RTL = 'theme-1';
    if (!empty($setting['SITE_RTL'])) {
        $SITE_RTL = $setting['SITE_RTL'];
    }
    $mode_setting = \App\Models\Utility::mode_layout();

?>

<!DOCTYPE html>
<html lang="en" dir="<?php echo e($SITE_RTL == 'on' ? 'rtl' : ''); ?>">

<head>
    <title>
        <?php echo e(Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'ERPGO')); ?>

        - <?php echo $__env->yieldContent('page-title'); ?> </title>
    <!-- Meta -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Rajodiya Infotech" />
    <meta name="base-url" content="<?php echo e(URL::to('/')); ?>">


    <!-- Primary Meta Tags -->
    <meta name="title" content=<?php echo e($seo_setting['meta_keywords']); ?>>
    <meta name="description" content=<?php echo e($seo_setting['meta_description']); ?>>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content=<?php echo e(env('APP_URL')); ?>>
    <meta property="og:title" content=<?php echo e($seo_setting['meta_keywords']); ?>>
    <meta property="og:description" content=<?php echo e($seo_setting['meta_description']); ?>>
    <meta property="og:image" content=<?php echo e(asset(Storage::url('uploads/metaevent/' . $seo_setting['meta_image']))); ?>>

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content=<?php echo e(env('APP_URL')); ?>>
    <meta property="twitter:title" content=<?php echo e($seo_setting['meta_keywords']); ?>>
    <meta property="twitter:description" content=<?php echo e($seo_setting['meta_description']); ?>>
    <meta property="twitter:image"
        content=<?php echo e(asset(Storage::url('uploads/metaevent/' . $seo_setting['meta_image']))); ?>>

    <!-- Favicon icon -->
    <link rel="icon"
        href="<?php echo e($logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '?' . time()); ?>"
        type="image">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">

    <!-- notification css -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugins/notifier.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugins/bootstrap-switch-button.css')); ?>">

    <!-- datatable css -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugins/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/plugins/dataTables.bootstrap5.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/daterangepicker-master/daterangepicker.css')); ?>">

    <!-- font css -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/tabler-icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/feather.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/fontawesome.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/material.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customizer.css')); ?>">

    <?php echo $__env->yieldPushContent('style'); ?>

    <?php if($SITE_RTL == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-rtl.css')); ?>">
    <?php endif; ?>
    <?php if($setting['cust_darklayout'] == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-dark.css')); ?>" id="style">
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-dark.css')); ?>" id="">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>" id="style">
        <link rel="stylesheet" href="" id="custom-dark">
    <?php endif; ?>

    
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.css">
    <style>
        [dir="rtl"] .dash-sidebar {
            left: auto !important;
        }

        [dir="rtl"] .dash-header {
            left: 0;
            right: 280px;
        }

        [dir="rtl"] .dash-header:not(.transprent-bg) .header-wrapper {
            padding: 0 0 0 30px;
        }

        [dir="rtl"] .dash-header:not(.transprent-bg):not(.dash-mob-header)~.dash-container {
            margin-left: 0px !important;
        }

        [dir="rtl"] .me-auto.dash-mob-drp {
            margin-right: 10px !important;
        }

        [dir="rtl"] .me-auto {
            margin-left: 10px !important;
        }

        .app-time-tracker {
            user-select: none;

        }

        .btn-close {
            background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3e%3cpath fill='none' stroke='%23000000' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M4 12h16'/%3e%3c/svg%3e") center/1em auto no-repeat !important;
        }
    </style>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        var pusherAppKey = "<?php echo e(env('PUSHER_APP_KEY')); ?>";
        var pusherCluster = "<?php echo e(env('PUSHER_APP_CLUSTER')); ?>";
        var userId = <?php echo e(Auth::user()->id); ?>;
        var baseUrl = "<?php echo e(url('/')); ?>";

        Pusher.logToConsole = false;

        var pusher = new Pusher(pusherAppKey, {
            cluster: pusherCluster
        });

        var channel = pusher.subscribe('popup-channel.' + userId);

        channel.bind('case-notification', function(data) {

            show_toastr('<?php echo e(__('Success')); ?>', data.data.message, 'success')

            var notificationCount = document.getElementById('notification-count');
            var currentCount = parseInt(notificationCount.textContent);
            notificationCount.textContent = currentCount + 1;



            var existingNotifications = document.getElementById('existing-notifications');

            // Create a new notification item
            var notificationItem = document.createElement('ul');
            notificationItem.style.paddingLeft = "0";

            if (data.data.type === 'case') {
                var caseShowUrl = baseUrl + "/cases/" + data.data.target_id;

                notificationItem.innerHTML = `
                    <li class="notification">
                        <a href="${caseShowUrl}" class="top-text-block">
                            ${data.data.message}
                            <div class="top-text-light">recently</div>
                        </a>
                    </li>
                `;
            } else if (data.data.type === 'task') {
                notificationItem.innerHTML = `
                    <li class="notification">
                        <a href="#" class="top-text-block">
                            ${data.data.message}
                            <div class="top-text-light">${data.data.timestamp}</div>
                        </a>
                    </li>
                `;
            }


            existingNotifications.insertBefore(notificationItem, existingNotifications.firstChild);
        });
    </script>
</head>

<body class="<?php echo e($color); ?>">


    <?php if(Auth::user()->type != 'super admin'): ?>
        
        <div class="app-time-tracker" id="draggableTimer" onmousedown="startDrag(event, 'draggableTimer')"
            onmouseup="stopDrag(event)">

            <div class="timer-img-wrapper" id="startTimerButton" onmousedown="startDrag(event, 'startTimerButton')"
                data-toggle="tooltip" title="<?php echo e(__('Tap to Start Timer!')); ?>"
                data-bs-original-title="<?php echo e(__('Tap to Start Timer!')); ?>" data-bs-placement="top"
                data-bs-toggle="tooltip">

                <div id="statusElement" onmousedown="startDrag(event, 'statusElement')">
                </div>

            </div>

            <div class="timerdiv" id="timerDiv" style="display: none;">
                <div class="text-end">
                    <button type="button" class="btn-close" data-toggle="tooltip" data-bs-placement="top"
                        data-bs-toggle="tooltip" title="<?php echo e(__('Minimize')); ?>"
                        data-bs-original-title="<?php echo e(__('Minimize')); ?>" id="clossBtnTimer"></button>
                </div>
                <div class="counter-title">
                    <h4 class="mb-0">Timer</h4>
                    <div class="counter-pause-stop">
                        <h5 id="timer" class="mb-0"></h5>
                        <button id="start" class="btn timmer-btn p-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 18 18" fill="none">
                                <path
                                    d="M7 11.6326V6.36671C7.00011 6.30082 7.01856 6.23618 7.05342 6.17955C7.08828 6.12293 7.13826 6.0764 7.19812 6.04485C7.25798 6.0133 7.32552 5.99789 7.39367 6.00023C7.46181 6.00257 7.52805 6.02258 7.58544 6.05816L11.8249 8.69035C11.8786 8.72358 11.9228 8.76933 11.9534 8.82337C11.984 8.87742 12 8.93803 12 8.99963C12 9.06123 11.984 9.12185 11.9534 9.17589C11.9228 9.22994 11.8786 9.27568 11.8249 9.30891L7.58544 11.9418C7.52805 11.9774 7.46181 11.9974 7.39367 11.9998C7.32552 12.0021 7.25798 11.9867 7.19812 11.9551C7.13826 11.9236 7.08828 11.8771 7.05342 11.8204C7.01856 11.7638 7.00011 11.6992 7 11.6333V11.6326Z"
                                    fill="black" fill - opacity="0.84" />
                                <path
                                    d="M0 9C0 4.02955 4.02955 0 9 0C13.9705 0 18 4.02955 18 9C18 13.9705 13.9705 18 9 18C4.02955 18 0 13.9705 0 9ZM9 1.22727C6.93854 1.22727 4.96152 2.04618 3.50385 3.50385C2.04618 4.96152 1.22727 6.93854 1.22727 9C1.22727 11.0615 2.04618 13.0385 3.50385 14.4961C4.96152 15.9538 6.93854 16.7727 9 16.7727C11.0615 16.7727 13.0385 15.9538 14.4961 14.4961C15.9538 13.0385 16.7727 11.0615 16.7727 9C16.7727 6.93854 15.9538 4.96152 14.4961 3.50385C13.0385 2.04618 11.0615 1.22727 9 1.22727Z"
                                    fill="black" fill - opacity="0.84"></path>
                            </svg>

                            <button id="stop" class="btn  p-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none">
                                    <path
                                        d="M19 10C19 14.9688 14.9688 19 10 19C5.03125 19 1 14.9688 1 10C1 5.03125 5.03125 1 10 1C14.9688 1 19 5.03125 19 10Z"
                                        stroke="black" stroke-opacity="0.84" stroke-width="1.5"
                                        stroke-miterlimit="10" />
                                    <path
                                        d="M12.72 14H7.28C6.9406 13.9997 6.61519 13.8648 6.3752 13.6248C6.13521 13.3848 6.00026 13.0594 6 12.72V7.28C6.00026 6.9406 6.13521 6.61519 6.3752 6.3752C6.61519 6.13521 6.9406 6.00026 7.28 6H12.72C13.0594 6.00026 13.3848 6.13521 13.6248 6.3752C13.8648 6.61519 13.9997 6.9406 14 7.28V12.72C13.9997 13.0594 13.8648 13.3848 13.6248 13.6248C13.3848 13.8648 13.0594 13.9997 12.72 14Z"
                                        fill="black" fill-opacity="0.84" />
                                </svg>
                            </button>
                    </div>
                </div>
                <div class="counter-started">
                    <h5>Started at: <span id="startedAtTime" class="counter-started-time "></span></h5>
                </div>
                <div class="counter-started">
                    
                </div>
            </div>

        </div>
    <?php endif; ?>
    <?php

        if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {
            $user = Auth::user()->id;
            $userData = Auth::user();
            $userIds = $userData->coAdminIds();
            $userIds[] = intval($userData->creatorId());

            $cases = App\Models\Cases::whereIn('created_by', $userIds)
                ->where('draft', 0)
                ->orderByDesc('id')
                ->get();
        } else {
            $user = Auth::user()->id;

            $cases = DB::table('cases')
                ->select('cases.*')
                ->where(function ($query) use ($user) {
                    $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                })
                ->where('draft', 0)
                ->orderBy('id', 'DESC')
                ->get();
        }

    ?>

    <div class="modal fade" id="timerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Save Time</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Total Time: <span id="pausedTime"></span></p>
                    <form id="timerFormId">
                        <?php echo csrf_field(); ?>
                        <input disable type="hidden" name="pausedTime" id="pausedTimeInput">
                        <label for="witness_name"><?php echo e(__('Notes')); ?></label>
                        <input class="form-control" type="text" name="notes">
                        <label for="caseSelect" class="mt-2 form-label">Select a Case:</label>
                        <select id="caseSelect" name="caseSelect" class="form-select">
                            <option disabled value="">Select a Case</option>
                            <option value="0">Send To Drafts</option>
                            <?php $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($case->id); ?>"><?php echo e($case->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <div class="text-end justify-end mt-3">
                            <button type="button" id="modalBtnresume" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Resume</button>
                            <button type="button" id="discardButton" class="btn btn-sm btn-danger"
                                data-bs-dismiss="modal">Discard Time</button>
                            <button type="submit" id="submitButton"
                                class="btn text-end btn-sm btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="timerDiscardModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Discard Time</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="modal-title">Are you sure to discard this time?</h5>

                    <div class="text-end justify-end mt-3">
                        <button id="discardTimer" type="button" class="btn btn-sm btn-danger">Discard</button>
                        <button type="submit" class="btn text-end btn-sm btn-secondary">close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <?php echo $__env->make('partision.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('partision.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



    <!-- [ Main Content ] start -->
    <div class="dash-container">
        <div class="dash-content p-0">
            <!-- [ breadcrumb ] start -->
            <div class="page-header px-4 py-4 border-bottom">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="page-header-title">
                                <h4 class="m-b-10"><?php echo $__env->yieldContent('page-title'); ?></h4>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a>
                                </li>
                                <?php echo $__env->yieldContent('breadcrumb'); ?>
                            </ul>
                        </div>
                        <div class="col-sm-7">
                            <?php echo $__env->yieldContent('action-button'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
    <!-- [ Main Content ] end -->

    <div id="commanModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modelCommanModelLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelCommanModelLabel"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="extra"></div>
            </div>
        </div>
    </div>



    <?php echo $__env->make('partision.footerlink', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldPushContent('custom-script'); ?>
    <?php echo $__env->yieldPushContent('custom-script1'); ?>

    <?php echo $__env->make('layouts.cookie_consent', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



    <style>
        .toast-success-icon:before {
            content: url("<?php echo e(asset('assets/images/notification/ok-48.png')); ?>");
        }

        .toast-error-icon:before {
            content: url("<?php echo e(asset('assets/images/notification/high_priority-48.png')); ?>");
        }

        .select2-container .select2-selection--single {
            height: 41px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
            /* Adjust as needed */
        }

        .dot-online {
            background-color: #34bfa3;
            /* Green color for online */
        }

        .dot-offline {
            background-color: #ccc;
            /* Gray color for offline */
        }

        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(0 0 0 / 72%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #5271FF;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            left: 50%;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .password_eye {
            white-space: normal !important;
            background-color: transparent !important;
            border: none !important;
            border-radius: 6px;
        }

        .password_eye_wraappe {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            height: 100%;
            z-index: 999;
        }

        .app-time-tracker:hover {
            cursor: grabbing;
            cursor: -moz-grabbing;
            cursor: -webkit-grabbing;

        }

        .timer-img-wrapper:hover {
            cursor: pointer;
        }

        /* notification  */
        .top-text-block {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: 400;
            line-height: 1.42857143;
            color: #333;
            white-space: inherit !important;
            border-bottom: 1px solid #f4f4f4;
            position: relative;

            &:hover {
                &:before {
                    content: '';
                    width: 4px;
                    background: #f05a1a;
                    left: 0;
                    top: 0;
                    bottom: 0;
                    position: absolute;
                }
            }
        }
    </style>


    <?php if($message = Session::get('success')): ?>
        <script>
            show_toastr('<?php echo e(__('Success')); ?>', '<?php echo $message; ?>', 'success')
        </script>
    <?php endif; ?>

    <?php if($message = Session::get('error')): ?>
        <script>
            show_toastr('<?php echo e(__('Error')); ?>', '<?php echo $message; ?>', 'error')
        </script>
    <?php endif; ?>

    <script>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const passwordToggle = document.querySelector('.password-toggle i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            }
        }

        function togglePasswordVisibility2(inputId) {
            const passwordInput = document.getElementById(inputId);
            const passwordToggle = document.querySelector('.password-toggle2 i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            }
        }

        function togglePasswordVisibility3(inputId) {
            const passwordInput = document.getElementById(inputId);
            const passwordToggle = document.querySelector('.password-toggle3 i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            }
        }
    </script>

    <?php if(Auth::user()->type != 'super admin'): ?>
        <script>
            // REMOVE draftId   
            function removeDraftId() {
                localStorage.removeItem('draftId');
            }

            $('a').on('click', function(e) {
                redirectUrl = $(this).attr('href');
                removeWebsiteTab(window.location.href);
                updateWebsiteTabs(redirectUrl);
                localStorage.setItem('redirectUrl', redirectUrl);
            });

            let isDragging = false;
            let initialX;
            let initialY;
            let currentDraggedElementId;


            function startDrag(event, elementId) {
                isDragging = true;

                const element = document.getElementById(elementId);
                const computedStyle = window.getComputedStyle(element);
                const insetValue = computedStyle.getPropertyValue('inset');
                const insetParts = insetValue.split(' ').map(part => parseFloat(part));

                // Get the screen dimensions
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;

                // Calculate left and top values dynamically from right and bottom
                const rightValue = insetParts[1] + 80; // Right value
                const bottomValue = insetParts[2] + 70; // Bottom value

                const leftValue = viewportWidth - rightValue; // Calculate left value
                const topValue = viewportHeight - bottomValue; // Calculate top value

                initialX = event.clientX - leftValue;
                initialY = event.clientY - topValue;

                currentDraggedElementId = elementId;
            }




            function stopDrag() {
                isDragging = false;
                currentDraggedElementId = null;
            }

            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);

            function drag(event) {
                if (!isDragging) return;

                const tracker = document.getElementById(currentDraggedElementId);

                // Get the dimensions of the viewport
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;

                // Get the dimensions of the tracker div
                const trackerWidth = tracker.offsetWidth;
                const trackerHeight = tracker.offsetHeight;

                // Calculate the maximum allowed positions
                const maxX = viewportWidth - trackerWidth;
                const maxY = viewportHeight - trackerHeight;

                const xOffset = event.clientX - initialX;
                const yOffset = event.clientY - initialY;


                // Ensure the div stays within the boundaries
                const newX = Math.min(maxX, Math.max(0, xOffset));
                const newY = Math.min(maxY, Math.max(0, yOffset));

                if (!isNaN(initialX) && !isNaN(initialY)) {
                    tracker.style.left = newX + 'px';
                    tracker.style.top = newY + 'px';
                    tracker.style.right = 'auto'; // Reset the right property
                    tracker.style.bottom = 'auto'; // Reset the bottom property
                }
            }


            document.getElementById('startTimerButton').addEventListener('click', function() {
                this.style.display = 'none';
                document.getElementById('timerDiv').style.display = 'block';
            });
            document.getElementById('clossBtnTimer').addEventListener('click', function() {

                document.getElementById('timerDiv').style.display = 'none';
                document.getElementById('startTimerButton').style.display = 'block';
            });

            document.getElementById('discardButton').addEventListener('click', function() {
                $('#timerDiscardModal').modal('show');
            });

            const startedAtTime = localStorage.getItem('startedAtTime');
            if (startedAtTime) {
                $('#startedAtTime').text(startedAtTime);
            }
            const storedData = localStorage.getItem('timerData');
            const defaultData = {
                status: 'discard',
                startTime: null,
                pausedTime: 0
            };

            let timerData;

            if (storedData === null || storedData === "undefined") {
                timerData = defaultData;
            } else {
                try {
                    timerData = JSON.parse(storedData);
                } catch (error) {
                    console.error('Error parsing stored data:', error);
                    timerData = defaultData; // Fallback to default data if parsing fails
                }
            }

            //  update timerData bariable after each 1 second
            function updateTimerData() {
                const storedData = localStorage.getItem('timerData');
                // console.log('updateTimerData');
                if (storedData) {
                    try {
                        const latestData = JSON.parse(storedData);
                        if (latestData) {
                            timerData = latestData;
                            const startedAt = localStorage.getItem('startedAtTime');
                            if (startedAt) {
                                $('#startedAtTime').text(startedAt);
                            }
                            updateUI(timerData.status);
                        }
                    } catch (error) {
                        console.error('Error parsing JSON from localStorage:', error);
                    }
                }


                setTimeout(updateTimerData, 2000); // Update every 1 second
            }

            updateTimerData();


            const status = '';
            updateUI(timerData.status);

            function updateUI(status) {

                const $button = $('.timmer-btn');
                const startTimerButton = document.getElementById('startTimerButton');

                if (status === 'started') {
                    // Update the title and data-bs-original-title attributes
                    // startTimerButton.setAttribute('title', '<?php echo e(__('Timer is Minimized and Active')); ?>');
                    document.getElementById('statusElement').innerHTML =
                        `
            <img style="cursor: pointer;" width='45' height='45' id="timerimgid" onmousedown="startDrag(event, 'timerimgid')" class="img-fluid" src="<?php echo e(asset('storage/uploads/stopwatch.gif')); ?>" alt="Stopwatch">`;
                    startTimerButton.setAttribute('data-bs-original-title', '<?php echo e(__('Timer is Minimized and Active')); ?>');
                    $button.attr('id', 'pause');
                    $button.html(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"> <path d="M9 0C11.3869 0 13.6761 0.948211 15.364 2.63604C17.0518 4.32387 18 6.61305 18 9C18 11.3869 17.0518 13.6761 15.364 15.364C13.6761 17.0518 11.3869 18 9 18C6.61305 18 4.32387 17.0518 2.63604 15.364C0.948211 13.6761 0 11.3869 0 9C0 6.61305 0.948211 4.32387 2.63604 2.63604C4.32387 0.948211 6.61305 0 9 0ZM9 16.7143C11.046 16.7143 13.0081 15.9015 14.4548 14.4548C15.9015 13.0081 16.7143 11.046 16.7143 9C16.7143 6.95404 15.9015 4.99189 14.4548 3.54518C13.0081 2.09847 11.046 1.28571 9 1.28571C6.95404 1.28571 4.99189 2.09847 3.54518 3.54518C2.09847 4.99189 1.28571 6.95404 1.28571 9C1.28571 11.046 2.09847 13.0081 3.54518 14.4548C4.99189 15.9015 6.95404 16.7143 9 16.7143ZM7.07143 5.78571C7.5 5.78571 7.71429 6 7.71429 6.42857V11.5714C7.71429 12 7.5 12.2143 7.07143 12.2143C6.64286 12.2143 6.42857 12 6.42857 11.5714V6.42857C6.42857 6 6.64286 5.78571 7.07143 5.78571ZM10.9286 5.78571C11.3571 5.78571 11.5714 6 11.5714 6.42857V11.5714C11.5714 12 11.3571 12.2143 10.9286 12.2143C10.5 12.2143 10.2857 12 10.2857 11.5714V6.42857C10.2857 6 10.5 5.78571 10.9286 5.78571Z" fill = "black"  fill - opacity = "0.85" / ></svg>'
                    );
                } else if (status === 'stopped') {
                    document.getElementById('statusElement').innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48">
                    <path d="M 24 4 C 12.972066 4 4 12.972074 4 24 C 4 35.027926 12.972066 44 24 44 C 35.027934 44 44 35.027926 44 24 C 44 12.972074 35.027934 4 24 4 z M 24 7 C 33.406615 7 41 14.593391 41 24 C 41 33.406609 33.406615 41 24 41 C 14.593385 41 7 33.406609 7 24 C 7 14.593391 14.593385 7 24 7 z M 22.476562 11.978516 A 1.50015 1.50015 0 0 0 21 13.5 L 21 24.5 A 1.50015 1.50015 0 0 0 21.439453 25.560547 L 26.439453 30.560547 A 1.50015 1.50015 0 1 0 28.560547 28.439453 L 24 23.878906 L 24 13.5 A 1.50015 1.50015 0 0 0 22.476562 11.978516 z"></path>
                </svg>
            `;
                    // startTimerButton.setAttribute('title', '<?php echo e(__('Tap to Start Timer!')); ?>');
                    startTimerButton.setAttribute('data-bs-original-title', '<?php echo e(__('Tap to Start Timer!')); ?>');
                    $button.attr('id', 'resume');
                    $button.html(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"> <path d="M7 11.6326V6.36671C7.00011 6.30082 7.01856 6.23618 7.05342 6.17955C7.08828 6.12293 7.13826 6.0764 7.19812 6.04485C7.25798 6.0133 7.32552 5.99789 7.39367 6.00023C7.46181 6.00257 7.52805 6.02258 7.58544 6.05816L11.8249 8.69035C11.8786 8.72358 11.9228 8.76933 11.9534 8.82337C11.984 8.87742 12 8.93803 12 8.99963C12 9.06123 11.984 9.12185 11.9534 9.17589C11.9228 9.22994 11.8786 9.27568 11.8249 9.30891L7.58544 11.9418C7.52805 11.9774 7.46181 11.9974 7.39367 11.9998C7.32552 12.0021 7.25798 11.9867 7.19812 11.9551C7.13826 11.9236 7.08828 11.8771 7.05342 11.8204C7.01856 11.7638 7.00011 11.6992 7 11.6333V11.6326Z" fill="black" fill - opacity="0.84" /><path d="M0 9C0 4.02955 4.02955 0 9 0C13.9705 0 18 4.02955 18 9C18 13.9705 13.9705 18 9 18C4.02955 18 0 13.9705 0 9ZM9 1.22727C6.93854 1.22727 4.96152 2.04618 3.50385 3.50385C2.04618 4.96152 1.22727 6.93854 1.22727 9C1.22727 11.0615 2.04618 13.0385 3.50385 14.4961C4.96152 15.9538 6.93854 16.7727 9 16.7727C11.0615 16.7727 13.0385 15.9538 14.4961 14.4961C15.9538 13.0385 16.7727 11.0615 16.7727 9C16.7727 6.93854 15.9538 4.96152 14.4961 3.50385C13.0385 2.04618 11.0615 1.22727 9 1.22727Z" fill="black" fill - opacity="0.84" / ></svg>'
                    );
                } else if (status === 'discard') {
                    document.getElementById('statusElement').innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="36" height="36" viewBox="0 0 48 48">
            <path d="M 24 4 C 12.972066 4 4 12.972074 4 24 C 4 35.027926 12.972066 44 24 44 C 35.027934 44 44 35.027926 44 24 C 44 12.972074 35.027934 4 24 4 z M 24 7 C 33.406615 7 41 14.593391 41 24 C 41 33.406609 33.406615 41 24 41 C 14.593385 41 7 33.406609 7 24 C 7 14.593391 14.593385 7 24 7 z M 22.476562 11.978516 A 1.50015 1.50015 0 0 0 21 13.5 L 21 24.5 A 1.50015 1.50015 0 0 0 21.439453 25.560547 L 26.439453 30.560547 A 1.50015 1.50015 0 1 0 28.560547 28.439453 L 24 23.878906 L 24 13.5 A 1.50015 1.50015 0 0 0 22.476562 11.978516 z"></path>
        </svg>
    `;
                    // startTimerButton.setAttribute('title', '<?php echo e(__('Tap to Start Timer!')); ?>');
                    startTimerButton.setAttribute('data-bs-original-title', '<?php echo e(__('Tap to Start Timer!')); ?>');
                    $button.attr('id', 'start');
                    $button.html(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"> <path d="M7 11.6326V6.36671C7.00011 6.30082 7.01856 6.23618 7.05342 6.17955C7.08828 6.12293 7.13826 6.0764 7.19812 6.04485C7.25798 6.0133 7.32552 5.99789 7.39367 6.00023C7.46181 6.00257 7.52805 6.02258 7.58544 6.05816L11.8249 8.69035C11.8786 8.72358 11.9228 8.76933 11.9534 8.82337C11.984 8.87742 12 8.93803 12 8.99963C12 9.06123 11.984 9.12185 11.9534 9.17589C11.9228 9.22994 11.8786 9.27568 11.8249 9.30891L7.58544 11.9418C7.52805 11.9774 7.46181 11.9974 7.39367 11.9998C7.32552 12.0021 7.25798 11.9867 7.19812 11.9551C7.13826 11.9236 7.08828 11.8771 7.05342 11.8204C7.01856 11.7638 7.00011 11.6992 7 11.6333V11.6326Z" fill="black" fill - opacity="0.84" /><path d="M0 9C0 4.02955 4.02955 0 9 0C13.9705 0 18 4.02955 18 9C18 13.9705 13.9705 18 9 18C4.02955 18 0 13.9705 0 9ZM9 1.22727C6.93854 1.22727 4.96152 2.04618 3.50385 3.50385C2.04618 4.96152 1.22727 6.93854 1.22727 9C1.22727 11.0615 2.04618 13.0385 3.50385 14.4961C4.96152 15.9538 6.93854 16.7727 9 16.7727C11.0615 16.7727 13.0385 15.9538 14.4961 14.4961C15.9538 13.0385 16.7727 11.0615 16.7727 9C16.7727 6.93854 15.9538 4.96152 14.4961 3.50385C13.0385 2.04618 11.0615 1.22727 9 1.22727Z" fill="black" fill - opacity="0.84" / ></svg>'
                    );
                } else {
                    document.getElementById('statusElement').innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="36" height="36" viewBox="0 0 48 48">
            <path d="M 24 4 C 12.972066 4 4 12.972074 4 24 C 4 35.027926 12.972066 44 24 44 C 35.027934 44 44 35.027926 44 24 C 44 12.972074 35.027934 4 24 4 z M 24 7 C 33.406615 7 41 14.593391 41 24 C 41 33.406609 33.406615 41 24 41 C 14.593385 41 7 33.406609 7 24 C 7 14.593391 14.593385 7 24 7 z M 22.476562 11.978516 A 1.50015 1.50015 0 0 0 21 13.5 L 21 24.5 A 1.50015 1.50015 0 0 0 21.439453 25.560547 L 26.439453 30.560547 A 1.50015 1.50015 0 1 0 28.560547 28.439453 L 24 23.878906 L 24 13.5 A 1.50015 1.50015 0 0 0 22.476562 11.978516 z"></path>
        </svg>
    `;
                    // startTimerButton.setAttribute('title', '<?php echo e(__('Tap to Start Timer!')); ?>');
                    startTimerButton.setAttribute('data-bs-original-title', '<?php echo e(__('Tap to Start Timer!')); ?>');
                    $button.attr('id', 'resume');
                    $button.html(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"> <path d="M7 11.6326V6.36671C7.00011 6.30082 7.01856 6.23618 7.05342 6.17955C7.08828 6.12293 7.13826 6.0764 7.19812 6.04485C7.25798 6.0133 7.32552 5.99789 7.39367 6.00023C7.46181 6.00257 7.52805 6.02258 7.58544 6.05816L11.8249 8.69035C11.8786 8.72358 11.9228 8.76933 11.9534 8.82337C11.984 8.87742 12 8.93803 12 8.99963C12 9.06123 11.984 9.12185 11.9534 9.17589C11.9228 9.22994 11.8786 9.27568 11.8249 9.30891L7.58544 11.9418C7.52805 11.9774 7.46181 11.9974 7.39367 11.9998C7.32552 12.0021 7.25798 11.9867 7.19812 11.9551C7.13826 11.9236 7.08828 11.8771 7.05342 11.8204C7.01856 11.7638 7.00011 11.6992 7 11.6333V11.6326Z" fill="black" fill - opacity="0.84" /><path d="M0 9C0 4.02955 4.02955 0 9 0C13.9705 0 18 4.02955 18 9C18 13.9705 13.9705 18 9 18C4.02955 18 0 13.9705 0 9ZM9 1.22727C6.93854 1.22727 4.96152 2.04618 3.50385 3.50385C2.04618 4.96152 1.22727 6.93854 1.22727 9C1.22727 11.0615 2.04618 13.0385 3.50385 14.4961C4.96152 15.9538 6.93854 16.7727 9 16.7727C11.0615 16.7727 13.0385 15.9538 14.4961 14.4961C15.9538 13.0385 16.7727 11.0615 16.7727 9C16.7727 6.93854 15.9538 4.96152 14.4961 3.50385C13.0385 2.04618 11.0615 1.22727 9 1.22727Z" fill="black" fill - opacity="0.84" / ></svg>'
                    );
                }

                // need ajax request which send the status in controller


            }


            function updateTimerDisplay() {
                const currentTime = calculateCurrentTime();
                $('#timer').text(currentTime);
            }

            function calculateCurrentTime() {
                if (timerData.status === 'started') {
                    const elapsedSeconds = Math.floor((Date.now() - timerData.startTime) / 1000);
                    const hours = Math.floor(elapsedSeconds / 3600);
                    const minutes = Math.floor((elapsedSeconds % 3600) / 60);
                    const seconds = elapsedSeconds % 60;
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                } else if (timerData.status === 'paused') {
                    const pausedSeconds = Math.floor(timerData.pausedTime / 1000);
                    const hours = Math.floor(pausedSeconds / 3600);
                    const minutes = Math.floor((pausedSeconds % 3600) / 60);
                    const seconds = pausedSeconds % 60;
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                } else if (timerData.status === 'discard') {
                    return '00:00:00';
                } else {
                    const pausedSeconds = Math.floor(timerData.pausedTime / 1000);
                    const hours = Math.floor(pausedSeconds / 3600);
                    const minutes = Math.floor((pausedSeconds % 3600) / 60);
                    const seconds = pausedSeconds % 60;
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }
            }


            $('.app-time-tracker').on('click', '.timmer-btn', function() {
                const buttonId = $(this).attr('id');

                var formData = new FormData();
                // Append the buttonId to the FormData
                formData.append('buttonId', buttonId);

                if (buttonId === 'start') {

                    const currentTime = new Date();
                    const formattedCurrentTime = currentTime.toLocaleTimeString('en-US', {
                        hour12: true,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                    });

                    localStorage.setItem('startedAtTime', formattedCurrentTime);
                    $('#startedAtTime').text(formattedCurrentTime);

                    timerData.status = 'started';
                    timerData.startTime = Date.now();
                    updateUI('started');
                    updateTimerDisplay();
                    localStorage.setItem('timerData', JSON.stringify(timerData));

                } else if (buttonId === 'pause') {

                    timerData.status = 'paused';
                    timerData.pausedTime = Date.now() - timerData.startTime;
                    updateUI('paused');
                    updateTimerDisplay();

                    localStorage.setItem('timerData', JSON.stringify(timerData));

                } else if (buttonId === 'resume') {

                    timerData.status = 'started';
                    timerData.startTime = Date.now() - timerData.pausedTime;
                    updateUI('started');
                    updateTimerDisplay();
                    localStorage.setItem('timerData', JSON.stringify(timerData));

                }

                $.ajax({
                    type: 'POST',
                    url: '<?php echo e(route('timeSheetTimerLog')); ?>',
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    success: function(response) {

                    },
                    error: function() {

                    },
                });
            });

            $('#stop').click(function() {
                if (timerData.status === 'started') {
                    // Pause the timer first
                    timerData.status = 'paused';
                    timerData.pausedTime = Date.now() - timerData.startTime;
                    updateUI('paused');
                    updateTimerDisplay();
                    localStorage.setItem('timerData', JSON.stringify(timerData));
                }

                // Now, you can stop the timer
                timerData.status = 'stopped';
                updateUI('stopped');
                updateTimerDisplay();

                const pausedHours = Math.floor(timerData.pausedTime / 3600000);
                const pausedMinutes = Math.floor((timerData.pausedTime % 3600000) / 60000);
                const pausedSeconds = Math.floor((timerData.pausedTime % 60000) / 1000);

                let formattedPausedTime = '';

                if (pausedHours > 0) {
                    formattedPausedTime += `${pausedHours}h `;
                }

                formattedPausedTime += `${pausedMinutes}m ${pausedSeconds}s`;

                $('#pausedTimeInput').val(formattedPausedTime);
                $('#pausedTime').text(formattedPausedTime);

                $('#timerModal').modal('show');
                localStorage.setItem('timerData', JSON.stringify(timerData));
            });


            $('#discardTimer').click(function() {
                resetTimer();
            });


            $('#timerFormId').submit(function(e) {
                e.preventDefault();
                var formData = new FormData($('#timerFormId')[0]); // Create a FormData object from the form
                // Get draftId from local storage and add it to formData
                const draftId = localStorage.getItem('draftId');
                if (draftId) {
                    formData.append('draftId', draftId);
                }
                console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: '<?php echo e(route('timeSheetTimer')); ?>',
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    success: function(response) {
                        if (response.success) {
                            removeDraftId();
                            resetTimer('model');
                            $('#timerModal').modal('hide');
                            show_toastr('<?php echo e(__('Success')); ?>', 'Timesheet successfully created', 'success')
                        } else {
                            $('#timerModal').modal('hide');
                            show_toastr('<?php echo e(__('Error')); ?>', 'Error creating timesheet', 'error')
                        }
                        // rest timerFormId 
                        $('#timerFormId').trigger("reset");
                    },
                    error: function() {
                        alert('An error occurred.');
                    },
                });
            });


            function resetTimer(model) {

                timerData.status = 'discard';
                timerData.startTime = null;
                timerData.pausedTime = 0;
                updateUI('discard');
                updateTimerDisplay();
                localStorage.setItem('timerData', JSON.stringify(timerData));
                $('#timerDiscardModal').modal('hide');
                if (model != 'model') {
                    show_toastr('<?php echo e(__('Success')); ?>', 'Time Discard successfully', 'success')
                }

            }



            setInterval(updateTimerDisplay, 1000);

            // on click modalBtnresume 
            $('#modalBtnresume').click(function() {
                $('#timerModal').modal('hide');
                timerData.status = 'started';
                timerData.startTime = Date.now() - timerData.pausedTime;
                updateUI('started');
                updateTimerDisplay();
                localStorage.setItem('timerData', JSON.stringify(timerData));
            });
            // disable the select all text broswer behavior on .app-time-tracker
            $('.app-time-tracker').on('mousedown', function(e) {
                e.preventDefault();
            });
            // Constants for activity timeout and tracking interval (in milliseconds)
            const ACTIVITY_TIMEOUT = 420000; // 10 seconds
            const TRACKING_INTERVAL = 30000; // 1 second
            const WEBSITE_KEY = '<?php echo env('APP_URL'); ?>'; // Root URL of the website

            // Function to reset the activity timer
            function resetActivityTimer() {
                // localStorage.setItem('pageAccessedByReload', false);
                localStorage.setItem('lastActivityTime', Date.now());
            }

            // Function to check for inactivity and run abc() if all tabs are closed
            function checkInactivity() {
                // console.log((Date.now() - localStorage.getItem('lastActivityTime')) / 1000 + " seconds");
                const lastActivityTime = parseInt(localStorage.getItem('lastActivityTime')) || 0;
                const currentTime = Date.now();
                const elapsedTime = currentTime - lastActivityTime;

                // Check if the current tab is the last one open
                const websiteTabs = JSON.parse(localStorage.getItem(WEBSITE_KEY)) || [];
                const isLastTab = websiteTabs.every(tab => tab.startsWith(WEBSITE_KEY));
                // console.log(elapsedTime, ACTIVITY_TIMEOUT, isLastTab, websiteTabs);
                if (elapsedTime >= ACTIVITY_TIMEOUT && isLastTab && timerData.status == 'started') {
                    var data = JSON.parse(localStorage.getItem("timerData"));
                    data.status = 'paused';
                    data.pausedTime = Date.now() - timerData.startTime;
                    localStorage.setItem('timerData', JSON.stringify(data));

                    updateUI('paused');
                    updateTimerDisplay();
                    // show alert
                    show_toastr('<?php echo e(__('Update')); ?>', 'Timer Paused due to Inactivity',
                        'error')
                }
            }

            // Function to update the list of open tabs for the website
            function updateWebsiteTabs(href = '') {
                const websiteTabs = JSON.parse(localStorage.getItem(WEBSITE_KEY)) || [];
                // get redirectUrl from local storeage if it url exist in websiteTabs remove it
                const redirectUrl = localStorage.getItem('redirectUrl');
                if (redirectUrl) {
                    const index = websiteTabs.indexOf(redirectUrl);
                    if (index !== -1) {
                        websiteTabs.splice(index, 1);
                        // localStorage.setItem(WEBSITE_KEY, JSON.stringify(websiteTabs));
                    }
                    // remove the redirectUrl
                    localStorage.removeItem('redirectUrl');
                }

                const currentUrl = (href != '') ? href : window.location.href;
                // if (!websiteTabs.includes(currentUrl)) {
                websiteTabs.push(currentUrl);
                localStorage.setItem(WEBSITE_KEY, JSON.stringify(websiteTabs));
                // }
            }

            // Function to remove the current tab from the list of open tabs
            function removeWebsiteTab(currentUrl) {
                const websiteTabs = JSON.parse(localStorage.getItem(WEBSITE_KEY)) || [];

                const index = websiteTabs.indexOf(currentUrl);
                if (index !== -1) {
                    websiteTabs.splice(index, 1);
                    // console.log(websiteTabs);
                    localStorage.setItem(WEBSITE_KEY, JSON.stringify(websiteTabs));
                }
            }


            // Check if jQuery is loaded on the page
            function isJQueryLoaded() {
                return typeof jQuery !== 'undefined';
            }

            // Set up a timer to check for inactivity at regular intervals
            setInterval(checkInactivity, TRACKING_INTERVAL);

            // // Event listeners to track user activity
            // $(document).on('mousemove keydown', function() {
            //     // // console.log("mouse");
            //     resetActivityTimer();
            // });
            // Add an event listener for the "keydown" event
            document.addEventListener('keydown', function(event) {
                if (event.isTrusted) {
                    resetActivityTimer();
                }
            });

            // Add an event listener for the "mousemove" event
            document.addEventListener('mousemove', function(event) {
                if (event.isTrusted) {
                    resetActivityTimer();
                }
            });


            // Event listener to handle website focus
            $(window).on('focus', function() {
                if (isJQueryLoaded()) {
                    resetActivityTimer();
                }
            });

            $(window).on('blur', function() {
                if (isJQueryLoaded()) {
                    const currentUrl = window.location.href;
                }
            });

            // Initial setup of the last activity time and website tabs
            resetActivityTimer();
            updateWebsiteTabs();

            // Check if this is the first page load
            var pageRefreshed = false;
            document.addEventListener('keydown', function(event) {
                // Check for F5 key (keyCode 116), Ctrl+R (Ctrl key code is 17), Ctrl+F5 (116), Cmd+R (Cmd key code is 91), or your custom refresh key (e.g., R key code is 82)
                if (
                    event.keyCode === 116 || // F5
                    (event.ctrlKey && event.keyCode === 82) || // Ctrl+R (Windows)
                    (event.ctrlKey && event.keyCode === 116) || // Ctrl+F5 (Windows)
                    (event.metaKey && event.keyCode === 82) || // Cmd+R (Mac)
                    event.key.toLowerCase() === 'r' // 'R' key (case insensitive)
                ) {
                    // localStorage.setItem('pageAccessedByReload', true);
                    pageRefreshed = true;

                }
            });
            // update draft send ajax request 
            function setDraftRequest() {
                const data = JSON.parse(localStorage.getItem("timerData"));
                // if (data.pausedTime < 1000) {
                //     return false;
                // }
                if (data.status === 'started') {
                    // Pause the timer first
                    data.status = 'paused';
                    data.pausedTime = Date.now() - data.startTime;
                    updateUI('paused');
                    updateTimerDisplay();
                }

                // Stop the timer
                data.status = 'stopped';
                updateUI('stopped');
                updateTimerDisplay();

                const pausedHours = Math.floor(data.pausedTime / 3600000);
                const pausedMinutes = Math.floor((data.pausedTime % 3600000) / 60000);
                const pausedSeconds = Math.floor((data.pausedTime % 60000) / 1000);

                let formattedPausedTime = '';

                if (pausedHours > 0) {
                    formattedPausedTime += `${pausedHours}h `;
                }

                formattedPausedTime += `${pausedMinutes}m ${pausedSeconds}s`;
                $('#pausedTimeInput').val(formattedPausedTime);
                $('#pausedTime').text(formattedPausedTime);
                var formData = new FormData($('#timerFormId')[0]); // Create a FormData object from the form
                // Get draftId from local storage and add it to formData
                const draftId = localStorage.getItem('draftId');
                if (draftId) {
                    formData.append('draftId', draftId);
                }
                $.ajax({
                    type: 'POST',
                    url: '<?php echo e(route('timeSheetTimerDraft')); ?>',
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    success: function(response) {
                        if (response.draftId) {
                            localStorage.setItem('draftId', response.draftId);
                        }
                    },
                });
            }
            // set insertval of 10sec for setDraftRequest
            setInterval(setDraftRequest, 420000);

            window.addEventListener('beforeunload', function(e) {

                // const pageAccessedByReload = (
                //     (window.performance.navigation && window.performance.navigation.type === 1) ||
                //     window.performance
                //     .getEntriesByType('navigation')
                //     .map((nav) => nav.type)
                //     .includes('reload')
                // );
                // localStorage.setItem('pageAccessedByReload', pageAccessedByReload);
                //    const pageLoaded = localStorage.getItem('pageLoaded');

                const currentUrl = window.location.href;
                // removeWebsiteTab(currentUrl);
                if (!currentUrl.endsWith("create") && !currentUrl.endsWith("edit")) {
                    removeWebsiteTab(currentUrl);
                }
                // if (  !pageRefreshed) {

                const appUrl = "<?php echo env('APP_URL'); ?>";
                const websiteTabs = JSON.parse(localStorage.getItem(appUrl)) || [];
                //  count websiteTabs with appUrl
                const count = websiteTabs.filter(function(item) {
                    return item.includes(appUrl);
                }).length;
                // Check if this is the last tab open for your website
                const isLastTab = websiteTabs.every(tab => tab.startsWith(WEBSITE_KEY));

                const data = JSON.parse(localStorage.getItem("timerData"));
                if (!data.startTime || data.startTime === null) {
                    var temPausedTime = Date.now();
                } else {
                    var temPausedTime = Date.now() - data.startTime;
                }
                if (count < 1 && (Date.now() - temPausedTime) > 2000) {

                    if (data.status === 'started') {
                        // Pause the timer first
                        data.pausedTime = Date.now() - data.startTime;
                        updateUI('paused');
                        updateTimerDisplay();
                    }

                    // Stop the timer
                    data.status = 'stopped';
                    updateUI('stopped');
                    updateTimerDisplay();

                    const pausedHours = Math.floor(data.pausedTime / 3600000);
                    const pausedMinutes = Math.floor((data.pausedTime % 3600000) / 60000);
                    const pausedSeconds = Math.floor((data.pausedTime % 60000) / 1000);

                    let formattedPausedTime = '';

                    if (pausedHours > 0) {
                        formattedPausedTime += `${pausedHours}h `;
                    }

                    formattedPausedTime += `${pausedMinutes}m ${pausedSeconds}s`;
                    $('#pausedTimeInput').val(formattedPausedTime);
                    $('#pausedTime').text(formattedPausedTime);

                    var formData = new FormData($('#timerFormId')[0]); // Create a FormData object from the form
                    // Get draftId from local storage and add it to formData
                    const draftId = localStorage.getItem('draftId');
                    if (draftId) {
                        formData.append('draftId', draftId);
                    }
                    // Call resetTimer('model') after the AJAX request has completed
                    localStorage.removeItem('draftId');
                    resetTimer('model');

                    $.ajax({
                        type: 'POST',
                        url: '<?php echo e(route('timeSheetTimerDraft')); ?>',
                        data: formData,
                        processData: false, // Ensure that jQuery doesn't process the data
                        contentType: false, // Ensure that jQuery doesn't set the content type
                        success: function(response) {

                        },
                        error: function(xhr, status, error) {
                            // Handle errors here
                        }

                    });

                }

                if (currentUrl.endsWith("create") || currentUrl.endsWith("edit")) {
                    removeWebsiteTab(currentUrl);
                }
                // }
            });
            $(document).ready(function() {
                var modal = $('#timerModal');
                var closedByClickOutside = false;

                modal.on('click', function(e) {
                    if (e.target === modal[0]) {
                        closedByClickOutside = true;
                    }
                });

                modal.on('hidden.bs.modal', function() {
                    if (closedByClickOutside) {
                        show_toastr('<?php echo e(__('Update')); ?>', 'Timer has been paused.', 'error');
                        closedByClickOutside = false;
                    }
                });
            });
        </script>
    <?php endif; ?>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>


</body>

</html>
<?php /**PATH /home/umar/code/ecasify/resources/views/layouts/app.blade.php ENDPATH**/ ?>