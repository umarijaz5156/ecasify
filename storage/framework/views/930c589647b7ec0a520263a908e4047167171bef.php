<?php
    use App\Models\Utility;
    $logo = asset('storage/uploads/logo');
    $logo_file = asset('uploads/logo');
    
    $company_favicon = Utility::getValByName('company_favicon');
    $setting = Utility::colorset();
    $SITE_RTL = $setting['SITE_RTL'];
    $seo_setting = Utility::getSeoSetting();
    $color = 'theme-3';
    
    if (!empty($setting['color'])) {
        $color = $setting['color'];
    }
    
    $SITE_RTL = 'off';
    if (!empty($setting['SITE_RTL'])) {
        $SITE_RTL = $setting['SITE_RTL'];
    }
    
    $mode_setting = Utility::mode_layout();
    
    $logo_light = Utility::getValByName('company_logo_light');
    $logo_dark = Utility::getValByName('company_logo_dark');
    
    $company_logo = Utility::get_company_logo();
    $company_logos = Utility::getValByName('company_logo_light');
    
?>

<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e($SITE_RTL == 'on' ? 'rtl' : ''); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Rajodiya Infotech" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>
        <?php echo e(Utility::getValByName('title_text') ? Utility::getValByName('title_text') : config('app.name', 'Ecasify-SaaS')); ?>

        - <?php echo $__env->yieldContent('page-title'); ?> </title>

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

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Favicon icon -->

    <link rel="icon"
        href="<?php echo e($logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') . '?timestamp=' . time()); ?>"
        type="image" sizes="800x800">

    <!-- font css -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/tabler-icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/feather.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/fontawesome.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/fonts/material.css')); ?>">

    <!-- vendor css -->

    <?php if($SITE_RTL == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-rtl.css')); ?>" id="main-style-link">
    <?php endif; ?>

    <?php if($setting['cust_darklayout'] == 'on'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style-dark.css')); ?>">
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>" id="main-style-link">
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/customizer.css')); ?>">
</head>
<style>
    .password_eye{
            white-space: normal !important; 
            background-color: transparent !important; 
                border: none !important; 
                border-radius: 6px;
    }
    .password_eye_wraappe{
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        height: 100%;
        z-index: 999;
    }
</style>

<body class="<?php echo e($color); ?>">
    <!-- [ auth-signup ] start -->
    <div class="auth-wrapper auth-v3">
        <div class="bg-auth-side bg-primary"></div>
        <div class="auth-content">
            <nav class="navbar navbar-expand-md navbar-light default">
                <div class="container-fluid pe-2">

                    <a class="navbar-brand" href="#">
                        <?php if($mode_setting['cust_darklayout'] && $mode_setting['cust_darklayout'] == 'on'): ?>
                            <img src="<?php echo e($logo . '/' . (isset($company_logos) && !empty($company_logos) ? $company_logos : 'logo-dark.png') . '?' . time()); ?>"
                                alt="<?php echo e(config('app.name', 'Ecasify-SaaS')); ?>" class="logo " style=" width: 180px;">
                        <?php else: ?>
                            <img src="<?php echo e($logo . '/' . (isset($company_logo->value) && !empty($company_logo->value) ? $company_logo->value : 'logo-dark.png') . '?' . time()); ?>"
                                alt="<?php echo e(config('app.name', 'Ecasify-SaaS')); ?>" class="logo " style=" width: 180px;">
                        <?php endif; ?>
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarTogglerDemo01" style="flex-grow: 0;">
                        <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <?php echo $__env->yieldContent('auth-lang'); ?>
                            </li>
                        </ul>
                    </div>

                </div>
            </nav>
            <div class="card">
                <div class="row align-items-center text-start">
                    <div class="col-xl-6">
                        <div class="card-body" style="width: 100%;">
                            <?php echo $__env->yieldContent('content'); ?>
                        </div>
                    </div>
                    <div class="col-xl-6 img-card-side">
                        <div class="auth-img-content">
                            <img src="<?php echo e(asset('assets/images/auth/img-auth-3.svg')); ?>" alt=""
                                class="img-fluid" />
                            <h3 style="    font-size: 23px;" class="text-white text-center mb-4 mt-5">
                                <?php echo e(__('Simplify Legal Firm Management')); ?>

                            </h3>
                            <p class="text-white text-center">
                                <?php echo e(__('Creating the most useful legal tools to empower your firm.')); ?>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="auth-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-6">

                            <p class="text-black">
                                <?php echo e(App\Models\Utility::getValByName('footer_text')
                                    ? App\Models\Utility::getValByName('footer_text')
                                    : __('Copyright') . ' ' . env('APP_NAME')); ?>

                                <?php echo e(date('Y')); ?>

                            </p>
                        </div>
                        <div class="col-6 text-end">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ auth-signup ] end -->
    <?php echo $__env->make('layouts.cookie_consent', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script src="<?php echo e(asset('assets/js/vendor-all.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/bootstrap.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/plugins/feather.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.js')); ?>"></script>
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
    <?php echo $__env->yieldPushContent('custom-scripts'); ?>
</body>

</html>
<?php /**PATH /home/umar/code/ecasify/resources/views/layouts/guest.blade.php ENDPATH**/ ?>