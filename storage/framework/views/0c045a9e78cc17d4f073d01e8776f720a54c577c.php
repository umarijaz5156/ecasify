<?php $__env->startPush('custom-scripts'); ?>
<?php if(env('RECAPTCHA_MODULE') == 'yes'): ?>
<?php echo NoCaptcha::renderJs(); ?>

<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('page-title'); ?>
<?php echo e(__('Login')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('auth-lang'); ?>
<select name="language" id="language" class="btn btn-light-primary dropdown-toggle custom_btn ms-2 me-2 language_option_bg"
    onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
    <?php $__currentLoopData = App\Models\Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option class="dropdown-item" <?php if($lang == $code): ?> selected <?php endif; ?> value="<?php echo e(route('login',$code)); ?>"><?php echo e(ucFirst($language)); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="">
    <h2 class="mb-3 f-w-600"><?php echo e(__('Login')); ?></h2>
</div>
<?php echo e(Form::open(['route' => 'login', 'method' => 'post', 'id' => 'loginForm'])); ?>

<?php echo csrf_field(); ?>
<div class="">
    <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>
    <div class="form-group mb-3">
        <label for="email" class="form-label"><?php echo e(__('Email')); ?></label>
        <input class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" type="email" name="email"
            value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="invalid-feedback" role="alert"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
   
        <div class="form-group mb-3">
            <label for="password" class="form-label"><?php echo e(__('Password')); ?></label>
            <div class="input-group">
                <input class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password_confirmation" type="password"
                name="password" required autocomplete="current-password">
                <div class="input-group-append password_eye_wraappe">
                    <span style="height: 100%;"  class="input-group-text password_eye password-toggle3" onclick="togglePasswordVisibility3('password_confirmation')">
                        <i class="far fa-eye-slash"></i>
                    </span>
                </div>
            </div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" role="alert"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div id="password_confirmation" class="pwindicator">
                <div class="bar"></div>
                <div class="label"></div>
            </div>
        </div>
    

    <?php if(env('RECAPTCHA_MODULE') == 'yes'): ?>
    <div class="form-group mb-3">
        <?php echo NoCaptcha::display(); ?>

        <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <span class="small text-danger" role="alert">
            <strong><?php echo e($message); ?></strong>
        </span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <?php endif; ?>
    <div class="form-group mb-4">
        <?php if(Route::has('password.request')): ?>
        <a href="<?php echo e(route('password.request', $lang)); ?>" class="text-xs"><?php echo e(__('Forgot Your Password?')); ?></a>
        <?php endif; ?>
    </div>
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-block mt-2" id="login_button"><?php echo e(__('Login')); ?></button>
    </div>

    <?php if(App\Models\Utility::getValByName('signup_button')=='on'): ?>
        <p class="my-4 text-center"><?php echo e(__("Don't have an account?")); ?>

            <a href="<?php echo e(route('register',$lang)); ?>" class="my-4 text-primary"><?php echo e(__('Register')); ?></a>
        </p>
    <?php endif; ?>

</div>
<?php echo e(Form::close()); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/umar/code/ecasify/resources/views/auth/login.blade.php ENDPATH**/ ?>