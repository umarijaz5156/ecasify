<?php
    use App\Models\Utility;
    
    $company_logo = App\Models\Utility::getValByName('company_logo');
    $company_small_logo = App\Models\Utility::getValByName('company_small_logo');
    $mode_setting = \App\Models\Utility::mode_layout();
    $logo = asset('storage/uploads/logo/');
    $company_logo = Utility::get_company_logo();
    $SITE_RTL = !empty($setting['SITE_RTL']) ? $setting['SITE_RTL'] : 'off';
    $draft_timesheet = 0;
    if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {
        $user = Auth::user();
        $userIds = $user->coAdminIds();
        $userIds[] = intval($user->creatorId());
        $draft_cases = App\Models\Cases::whereIn('created_by', $userIds)
            ->where('draft', 1)
            ->orderBy('created_at', 'desc')
            ->count();
    
        //  draft timesheet
        $draft_timesheet = App\Models\Timesheet::whereIn('created_by', $userIds)
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->count();
    } elseif (Auth::user()->type == 'super admin') {
    } else {
        //  draft timesheet
        $draft_timesheet = App\Models\Timesheet::where('member', Auth::user()->id)
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->count();
        $user = Auth::user()->id;
        $draft_cases = DB::table('cases')
            ->select('cases.*')
            ->where(function ($query) use ($user) {
                $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
            })
            ->where('draft', 1)
            ->orderBy('id', 'DESC')
            ->count();
    }
    
?>

<style>
    /* color: #00000052; */
    .disabled{
        color: #00000052 !important;
    }
    .disabled:hover{
        color: #00000052 !important;
    }
    </style>

<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

<!-- [ navigation menu ] start -->
<nav
    class="dash-sidebar light-sidebar <?php echo e(isset($mode_setting['cust_theme_bg']) && $mode_setting['cust_theme_bg'] == 'on' ? 'transprent-bg' : ''); ?>">

    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="<?php echo e(route('dashboard')); ?>" class="b-brand">

                <!-- ========   change your logo hear   ============ -->
                <img src="<?php echo e($logo . '/' . (isset($company_logo->value) && !empty($company_logo->value) ? $company_logo->value : 'logo-dark.png') . '?' . time()); ?>"
                    alt="" class="logo logo-lg" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">


                <li class="dash-item dash-hasmenu <?php echo e(\Request::route()->getName() == 'dashboard' ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('dashboard')); ?>" class="dash-link ">
                        <span class="dash-micon"><i class="ti ti-home"></i>
                        </span><span class="dash-mtext"><?php echo e(__('Dashboard')); ?></span>
                        <span class="dash-arrow"></span>
                    </a>
                </li>

                <?php if(Auth::user()->type == 'super admin'): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage user')): ?>
                        <li class="dash-item dash-hasmenu <?php echo e(request()->is('users*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('users.index')); ?>" class="dash-link"><span class="dash-micon"><i
                                        class="ti ti-users"></i></span><span class="dash-mtext"><?php echo e(__('Companies')); ?></span>
                            </a>
                        </li>
                        
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage member', 'manage group', 'manage role'])): ?>
                        <li
                            class="dash-item dash-hasmenu <?php echo e(Request::route()->getName() == 'users.edit' || Request::route()->getName() == 'users.list' || Request::route()->getName() == 'userlog.index' ? 'active dash-trigger' : ''); ?>">
                            <a href="<?php echo e(route('users.index')); ?>" class="dash-link ">
                                <span class="dash-micon"><i class="ti ti-users"></i>
                                </span><span class="dash-mtext"><?php echo e(__('Staff')); ?></span>
                                <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul
                                class="dash-submenu <?php echo e(Request::segment(1) == 'roles' || Request::segment(1) == 'users' || Request::route()->getName() == 'users.list' || Request::segment(1) == 'groups' ? 'show' : ''); ?>">

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage role')): ?>
                                    <li class="dash-item <?php echo e(in_array(Request::segment(1), ['roles', '']) ? ' active' : ''); ?>">
                                        <a class="dash-link" href="<?php echo e(route('roles.index')); ?>"><?php echo e(__('Role')); ?></a>
                                    </li>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage member')): ?>
                                    <li
                                        class="dash-item <?php echo e(Request::route()->getName() == 'users.edit' || Request::route()->getName() == 'users.list' || Request::route()->getName() == 'userlog.index' ? 'active' : ''); ?>">
                                        <a class="dash-link" href="<?php echo e(route('users.index')); ?>"><?php echo e(__('Users')); ?></a>
                                    </li>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage group')): ?>
                                    <li class="dash-item <?php echo e(in_array(Request::segment(1), ['groups', '']) ? ' active' : ''); ?>">
                                        <a class="dash-link" href="<?php echo e(route('groups.index')); ?>"><?php echo e(__('Group')); ?></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                

                <style>
                    /* active draft */
                    .active_draft {
                        color:#5271FF !important;
                        background-color:white !important;
                    }
                    
                </style>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage case')): ?>
                    <li
                        class="dash-item dash-hasmenu cases_active <?php echo e(Request::route()->getName() == 'cases.index' ? 'active dash-trigger' : ''); ?>">
                        <a href="<?php echo e(route('cases.index')); ?>" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-text"></i></span>
                            <span class="dash-mtext"><?php echo e(__('Cases')); ?></span>
                            <?php if($draft_cases != 0): ?>
                                <span
                                    style="
                                   border-radius: 20px;
                                    font-size: 10px;
                                    padding-left: 10px;
                                    padding-right: 10px;
                                    padding-top: 6px;
                                    margin-left: 8px;
                                    padding-bottom: 6px;"
                                    class=" <?php echo e(Request::route()->getName() == 'cases.index' ? 'active_draft' : 'bg-primary text-white'); ?> case_draft"> <i style="font-size: 10px"
                                        class="fas fa-edit"></i>
                                    <?php echo e($draft_cases); ?> </span>
                            <?php endif; ?>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>

                        <ul class="dash-submenu <?php echo e(Request::is('cases*') ? 'show' : ''); ?>">
                            <li class="dash-item <?php echo e(Request::route()->getName() == 'cases.index' ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('cases.index')); ?>" class="dash-link">
                                    <span class="dash-mtext"><?php echo e(__('Cases')); ?></span>
                                </a>
                            </li>
                            <li class="dash-item <?php echo e(Request::route()->getName() == 'cases.draft.view' ? 'active' : ''); ?>">
                                <a class="dash-link" href="<?php echo e(route('cases.draft.view')); ?>"><?php echo e(__('Draft Cases')); ?>

                                    <?php if($draft_cases != 0): ?>
                                        <span class="bg-primary ml-2 text-white"
                                            style="
                                            border-radius: 50%;
                                            font-size: 10px;
                                            padding-left: 7px;
                                            padding-right: 7px;
                                            padding-top: 4px;
                                            padding-bottom: 4px;
                                            margin-left: 6px;">
                                            <?php echo e($draft_cases); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>
                    </li>



                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage tasks')): ?>
                    <li class="dash-item dash-hasmenu <?php echo e(in_array(Request::segment(1), ['todo']) ? ' active' : ''); ?>">
                        <a href="<?php echo e(route('tasks.index')); ?>" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-plus"></i></span>
                            <span class="dash-mtext"><?php echo e(__('Tasks')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                



                


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage document')): ?>
                    
                   <li class="dash-item dash-hasmenu cases_active <?php echo e(Request::route()->getName() == 'documents.index' ? 'active dash-trigger' : ''); ?>">
                    <a href="<?php echo e(route('documents.index')); ?>" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-file-text"></i></span>
                        <span class="dash-mtext"><?php echo e(__('Documents')); ?></span>
                        <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                    </a>

                    <ul class="dash-submenu <?php echo e(Request::is('documents*') ? 'show' : ''); ?>">
                        <li class="dash-item <?php echo e(Request::route()->getName() == 'documents.index' ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('documents.index')); ?>" class="dash-link">
                                <span class="dash-mtext"><?php echo e(__('Case Documents')); ?></span>
                            </a>
                        </li>
                        <li class="disabled dash-item <?php echo e(Request::route()->getName() == 'documents.index2' ? 'active' : ''); ?>">
                            <a class=" disabled dash-link" disabled ><?php echo e(__('Firm Documents')); ?>

                            </a>
                        </li>
                        <li class="disabled dash-item <?php echo e(Request::route()->getName() == 'documents.index3' ? 'active' : ''); ?>">
                            <a class="disabled dash-link" disabled><?php echo e(__('E-Signatures')); ?>

                            </a>
                        </li>
                        <li class="disabled text-muted dash-item <?php echo e(Request::route()->getName() == 'documents.e.signatures' ? 'active' : ''); ?>">
                            <a class="disabled dash-link" disabled  ><?php echo e(__('Intake Forms')); ?>

                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage bill')): ?>
                    <li class="dash-item dash-hasmenu <?php echo e(in_array(Request::segment(1), ['bills']) ? ' active' : ''); ?>">
                        <a href="<?php echo e(route('bills.index')); ?>" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-analytics"></i></span>
                            <span class="dash-mtext"><?php echo e(__('Bills')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
              
                



                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage timesheet')): ?>
                    <li
                        class="dash-item dash-hasmenu  <?php echo e(Request::route()->getName() == 'timesheet.index' ? 'active dash-trigger' : ''); ?>">
                        <a href="<?php echo e(route('timesheet.index')); ?>" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-text"></i></span>
                            <span class="dash-mtext span-draft"><?php echo e(__('Timesheet')); ?></span>
                            <?php if($draft_timesheet != 0): ?>
                                <span
                                    style="
                           border-radius: 20px;
                            font-size: 10px;
                            padding-left: 10px;
                            padding-right: 10px;
                            padding-top: 6px;
                            margin-left: 8px;
                            padding-bottom: 6px;
                            "
                                    class=" <?php echo e(Request::route()->getName() == 'timesheet.index' ? 'active_draft' : 'bg-primary text-white'); ?>  case_draft"> <i style="font-size: 10px"
                                        class="fas fa-edit"></i>
                                    <?php echo e($draft_timesheet); ?> </span>
                            <?php endif; ?>
                            <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>

                        <ul class="dash-submenu <?php echo e(Request::is('timesheet*') ? 'show' : ''); ?>">
                            <li class="dash-item <?php echo e(Request::route()->getName() == 'timesheet.index' ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('timesheet.index')); ?>" class="dash-link">
                                    <span class="dash-mtext"><?php echo e(__('Timesheet')); ?></span>
                                </a>
                            </li>
                            <li
                                class="dash-item <?php echo e(Request::route()->getName() == 'timesheet.draft.view' ? 'active' : ''); ?>">
                                <a class="dash-link" href="<?php echo e(route('timesheet.draft.view')); ?>"><?php echo e(__('Timesheet Drafts')); ?>

                                    <?php if($draft_timesheet != 0): ?>
                                        <span class="bg-primary ml-2 text-white"
                                            style="
                                    border-radius: 50%;                                                                                 
                                    font-size: 10px;
                                    padding-left: 7px;
                                    padding-right: 7px;
                                    padding-top: 4px;
                                    padding-bottom: 4px;
                                    margin-left: 6px;">
                                            <?php echo e($draft_timesheet); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                <?php endif; ?>
                
                <?php if(Auth::user()->type != 'super admin'): ?>

                
                <li class="dash-item dash-hasmenu <?php echo e(in_array(Request::segment(1), ['activities']) ? ' active' : ''); ?>">
                    <a href="<?php echo e(route('activities.index')); ?>" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-file-analytics"></i></span>
                        <span class="dash-mtext"><?php echo e(__('Activities')); ?></span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage expense')): ?>
                    <li class="dash-item dash-hasmenu <?php echo e(in_array(Request::segment(1), ['expenses']) ? ' active' : ''); ?>">
                        <a href="<?php echo e(route('expenses.index')); ?>" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-report"></i></span>
                            <span class="dash-mtext"><?php echo e(__('Expense')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage feereceived')): ?>
                    <li
                        class="dash-item dash-hasmenu <?php echo e(in_array(Request::segment(1), ['fee-receive']) ? ' active' : ''); ?>">
                        <a href="<?php echo e(route('fee-receive.index')); ?>" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-receipt-2"></i></span>
                            <span class="dash-mtext"><?php echo e(__('Fee Received')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(\Auth::user()->type != 'super admin'): ?>
                    <li class="dash-item <?php echo e(\Request::route()->getName() == 'chats' ? ' active' : ''); ?>">
                        <a href="<?php echo e(url('chats')); ?>"
                            class="dash-link <?php echo e(Request::segment(1) == 'chats' ? 'active' : ''); ?>">
                            <span class="dash-micon"><i class="ti ti-brand-messenger"></i></span><span
                                class="dash-mtext"><?php echo e(__('Messenger')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(\Auth::user()->type == 'super admin'): ?>
                    <li
                        class="dash-item <?php echo e(Request::segment(1) == 'plans' || Request::route()->getName() == 'payment' ? 'active' : ''); ?>">
                        <a class="dash-link" href="<?php echo e(route('plans.index')); ?>">
                            <span class="dash-micon"><i class="ti ti-trophy"></i></span><span
                                class="dash-mtext"><?php echo e(__('Plan')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                



                



                <?php if(\Auth::user()->type == 'super admin'): ?>
                    <li class="dash-item <?php echo e(request()->is('plan_request*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('plan_request.index')); ?>" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-git-pull-request"></i></span><span
                                class="dash-mtext"><?php echo e(__('Plan Request')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage coupon')): ?>
                    <li class="dash-item <?php echo e(Request::segment(1) == 'coupons' ? 'active' : ''); ?>">
                        <a class="dash-link" href="<?php echo e(route('coupons.index')); ?>">
                            <span class="dash-micon"><i class="ti ti-gift"></i></span><span
                                class="dash-mtext"><?php echo e(__('Coupons')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage order')): ?>
                    <li class="dash-item <?php echo e(Request::segment(1) == 'orders' ? 'active' : ''); ?>">
                        <a class="dash-link" href="<?php echo e(route('order.index')); ?>">
                            <span class="dash-micon"><i class="ti ti-credit-card"></i></span><span
                                class="dash-mtext"><?php echo e(__('Order')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type == 'super admin'): ?>
                    <li class="dash-item <?php echo e(Request::segment(1) == 'form-encryption' ? 'active' : ''); ?>">
                        <a class="dash-link" href="<?php echo e(route('form.encryption.index')); ?>">
                            <span class="dash-micon"><i class="ti ti-lock"></i></span><span
                                class="dash-mtext"><?php echo e(__('Form Encryption')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage setting')): ?>
                    <li
                        class="dash-item dash-hasmenu <?php echo e(in_array(Request::segment(1), ['app-setting']) ? ' active' : ''); ?>">
                        <a href="<?php echo e(route('settings.index')); ?>" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-settings"></i></span>
                            <span class="dash-mtext"><?php echo e(__('Settings')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage system settings')): ?>
                    <li class="dash-item <?php echo e(Request::route()->getName() == 'admin.settings' ? ' active' : ''); ?>">
                        <a class="dash-link" href="<?php echo e(route('admin.settings')); ?>">
                            <span class="dash-micon"><i class="ti ti-settings"></i></span><span
                                class="dash-mtext"><?php echo e(__('System Settings')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
<!-- [ navigation menu ] end -->
<?php /**PATH /home/umar/code/ecasify/resources/views/partision/sidebar.blade.php ENDPATH**/ ?>