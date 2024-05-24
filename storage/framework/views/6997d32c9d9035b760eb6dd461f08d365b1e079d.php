

<?php
    $users = \Auth::user();
    $logo = App\Models\Utility::get_file('uploads/profile/');
    $currantLang = $users->currentLanguage();
    $languages = App\Models\Utility::languages();
    $mode_setting = \App\Models\Utility::mode_layout();

    $LangName = \App\Models\Languages::where('code', $currantLang)->first();
    if (empty($LangName)) {
        $LangName  = new App\Models\Utility();
        $LangName->fullName = 'English';
    }
    use Carbon\Carbon;

    $notifications = App\Models\Notification::where('user_id', Auth::user()->id)
    ->orderBy('created_at', 'desc')
    ->get();

    
?>

    <header class="dash-header <?php echo e((isset($mode_setting['cust_theme_bg']) && $mode_setting['cust_theme_bg'] == 'on')?'transprent-bg':''); ?>">
    <div class="header-wrapper">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled">

                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>

                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0 " data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="theme-avtar">
                            <img alt="#" style="width:30px;"
                                src="<?php echo e(!empty(\Auth::user()->avatar) ? $logo.  \Auth::user()->avatar : $logo . '/avatar.png'); ?>"
                                class="header-avtar">
                        </span>
                        <span class="hide-mob ms-2">
                            <?php if(!Auth::guest()): ?>
                                <?php echo e(__('Hi, ')); ?><?php echo e(Auth::user()->name); ?>!
                            <?php else: ?>
                                <?php echo e(__('Guest')); ?>

                            <?php endif; ?>
                        </span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>

                    <div class="dropdown-menu dash-h-dropdown">
                        <a href="<?php echo e(route('users.edit', Auth::user()->id)); ?>" class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span><?php echo e(__('Profile')); ?></span>
                        </a>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" id="form_logout">
                            <?php echo csrf_field(); ?>
                            <a href="#"  class="dropdown-item" id="logout-form">
                                <i class="ti ti-power"></i>
                                <?php echo e(__('Log Out')); ?>

                            </a>
                        </form>
                    </div>
                </li>

            </ul>
        </div>

      
        <div class="ms-auto">
            <ul class="list-unstyled">

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-bell nocolor"></i>
                        <span id="notification-count" class="drp-text hide-mob"> <?php echo e(count($notifications)); ?></span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <?php if(count($notifications) != 0): ?>
                    <div style="width: 270px;height: 400px;overflow-y: scroll;" class="custom-scroll dropdown-menu p-2 dash-h-dropdown dropdown-menu-end " aria-labelledby="dropdownLanguage">
                       
                        <div id="existing-notifications" >
                            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                 <?php
                                $timestamp = Carbon::parse($notification->created_at);
                                $formattedTimestamp = $timestamp->diffForHumans();
                                ?>
                                
                                <?php if($notification->type == 'case'): ?>
                                <ul style="padding-left: 0px" class="pl-0">
                                    <li class="notification">
                                      <a href="<?php echo e(route('cases.show', $notification->target_id)); ?>" class="top-text-block">
                                        <?php echo e($notification->message); ?>                                        
                                        <div class="top-text-light"><?php echo e($formattedTimestamp); ?></div>
                                      </a> 
                                    </li>
                                </ul>
                                <?php elseif($notification->type == 'task'): ?>
                                <ul style="padding-left: 0px" class="pl-0">
                                    <li class="notification">
                                      <a href="#" class="top-text-block">
                                        <?php echo e($notification->message); ?>                                        
                                        <div class="top-text-light"><?php echo e($formattedTimestamp); ?></div>
                                      </a> 
                                    </li>
                                </ul>

                                <?php else: ?>
                                <ul style="padding-left: 0px" class="pl-0">
                                    <li class="notification">
                                      <a href="#" class="top-text-block">
                                        <?php echo e($notification->message); ?>                                        
                                        <div class="top-text-light"><?php echo e($formattedTimestamp); ?></div>
                                      </a> 
                                    </li>
                                </ul>
                                <?php endif; ?>

                              
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        
                    </div>
                    <?php else: ?>
                    <div class="dropdown-menu p-2 dash-h-dropdown dropdown-menu-end " aria-labelledby="dropdownLanguage">
                        <div id="text-center">
                          
                          <ul style="padding-left: 0px" class="pl-0">
                            <li>
                              <div class="top-text-block">
                                No Notifications                                  
                              </div> 
                            </li>
                        </ul>
                        </div>
                        
                    </div>
                    <?php endif; ?>
                   
                </li>
             

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob"><?php echo e(Str::upper($LangName->fullName)); ?></span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end " aria-labelledby="dropdownLanguage">
                        <?php $__currentLoopData = App\Models\Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('change.language', $code)); ?>"
                                class="dropdown-item <?php echo e($currantLang == $code ? 'text-danger' : ''); ?>">
                                <?php echo e(Str::upper($lang)); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create language')): ?>
                            <div class="dropdown-divider m-0"></div>
                            <a href="#" data-url="<?php echo e(route('create.language')); ?>" data-size="md" data-ajax-popup="true" data-title="<?php echo e(__('Create New Language')); ?>"
                            class="dropdown-item  text-primary text-primary" ><?php echo e(__('Create Language')); ?></a>
                            <div class="dropdown-divider m-0"></div>
                            <a href="<?php echo e(route('manage.language', $currantLang)); ?>"
                                class="dropdown-item text-primary"><?php echo e(__('Manage Language')); ?></a>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>
        
    </div>
</header>

<?php $__env->startPush('custom-script'); ?>
    <script>
   

        $('#logout-form').on('click',function(){
            event.preventDefault();
            $('#form_logout').trigger('submit');
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/umar/code/ecasify/resources/views/partision/header.blade.php ENDPATH**/ ?>