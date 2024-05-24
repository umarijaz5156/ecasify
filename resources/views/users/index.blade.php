@extends('layouts.app')



@if (\Auth::user()->type == 'super admin' && ($companyUser == false))
    @section('page-title', __('Companies'))
@elseif ($companyUser == true)
    @section('page-title', __('Company Users'))
@else
    @section('page-title', __('Users'))
@endif


@if ($companyUser == true)
    @section('action-button')
        <div class="row align-items-center mb-3">
            <div class="col-md-12 d-flex align-items-center  justify-content-end">
                <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
                    <a href="{{ route('users.list') }}" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true"
                        data-size="lg" data-title="Add Company" data-toggle="tooltip" title="{{ __('List View') }}"
                        data-bs-original-title="{{ __('List View') }}" data-bs-placement="top" data-bs-toggle="tooltip">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </div>

                @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'co admin')
                    <a href="{{ route('userlog.index') }}" class="btn btn-sm btn-primary btn-icon m-1"
                        data-bs-toggle="tooltip"title="{{ __('Company Log') }}">
                        <i class="ti ti-user-check"></i>
                    </a>
                @endif

                @canany(['create member', 'create user'])
                    <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
                        <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="lg"
                            data-title="Add Company" data-url="{{ route('users.create') }}" data-toggle="tooltip"
                            title="{{ __('Create New Company') }}">
                            <i class="ti ti-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endcan
    @endsection


@else
@section('action-button')
<div class="row align-items-center mb-3">
    <div class="col-md-12 d-flex align-items-center  justify-content-end">
        <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
            <a href="{{ route('users.list') }}" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true"
                data-size="lg" data-title="Add User" data-toggle="tooltip" title="{{ __('List View') }}"
                data-bs-original-title="{{ __('List View') }}" data-bs-placement="top" data-bs-toggle="tooltip">
                <i class="ti ti-menu-2"></i>
            </a>
        </div>

        @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'co admin')
            <a href="{{ route('userlog.index') }}" class="btn btn-sm btn-primary btn-icon m-1"
                data-bs-toggle="tooltip"title="{{ __('User Log') }}">
                <i class="ti ti-user-check"></i>
            </a>
        @endif

        @canany(['create member', 'create user'])
            <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
                <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="lg"
                    data-title="Add User" data-url="{{ route('users.create') }}" data-toggle="tooltip"
                    title="{{ __('Create New User') }}">
                    <i class="ti ti-plus"></i>
                </a>
            </div>
        </div>
    </div>
@endcan
@endsection

@endif

@if (\Auth::user()->type == 'super admin' && ($companyUser == false))
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Companies') }}</li>
    @endsection
@elseif ($companyUser == true)
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Company Users') }}</li>
    @endsection
@else
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Users') }}</li>
    @endsection

@endif

@section('content')


    <div class="row g-0 pt-0">
        <div class="col-xxl-12">
            <div class="row g-0">

                @foreach ($users as $user)
                    <div class="col-md-6 col-xxl-3 col-lg-4 col-sm-6 border-end border-bottom">
                        <div class="card  shadow-none bg-transparent border h-100 text-center rounded-0">
                            <div class="card-header border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        @if (\Auth::user()->type == 'super admin')
                                            <div class="">
                                                @if ($companyUser == false)
                                                <a href="#" class="btn btn-sm btn-light-primary text-sm"
                                                    data-url="{{ route('plan.upgrade', $user->id) }}" data-size="lg"
                                                    data-ajax-popup="true" data-title="{{ __('Upgrade Plan') }}">
                                                    {{ __('Upgrade Plan') }}
                                                </a>
                                                @endif
                                                @if ($user->last_activity == 'Active')
                                                    <a href="#" class="btn btn-sm btn-light-primary text-sm"
                                                        data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                                        data-user-id="{{ $user->id }}">
                                                        <span class="dot dot-online"></span> Online
                                                    </a>
                                                @else
                                                <button class="btn btn-sm btn-light-danger text-sm" disabled style="background-color: #f8d7da; border-color: #f8d7da; color: #721c24;">
                                                    <span class="dot dot-offline"></span> Offline
                                                </button>
                                                
                                                @endif


                                            </div>
                                        @else
                                        {{-- $user->type (string) to array --}}
                                        @php
                                            $userTypes = array_unique(array_filter(explode(',', $user->type . ',' . $user->role_title)));
                                            $isCoAdmin = in_array('co-Admin', $userTypes);
                                        @endphp

                                            {{-- @if ($user->type == 'advocate') --}}
                                                @foreach ($userTypes as $userType)
                                                <div class="badge p-2 px-3 rounded bg-primary">
                                                    {{ ucfirst($userType === 'advocate' ? 'Attorney' : $userType) }}
                                               </div>
                                                @endforeach
                                            {{-- @else
                                                <div class="badge p-2 px-3 rounded bg-primary">
                                                {{ ucfirst($user->type) }}
                                                </div>
                                            @endif --}}


                                            
                                            {{-- <div class="badge p-2 px-3 rounded bg-primary">
                                                
                                                @if ($user->type === 'advocate')
                                                 Attorney
                                                @else
                                                    {{ ucfirst($user->type) }}
                                                @endif --}}
                                            {{-- </div> --}}
                                            @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'co admin')
                                                @if ($user->last_activity == 'Active')
                                                    <a href="#" class="btn btn-sm btn-light-primary text-sm"
                                                        data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                                        data-user-id="{{ $user->id }}">
                                                        <span class="dot dot-online"></span> Online
                                                    </a>
                                                @else
                                                <button class="btn btn-sm btn-light-danger text-sm" disabled style="background-color: #f8d7da; border-color: #f8d7da; color: #721c24;">
                                                    <span class="dot dot-offline"></span> Offline
                                                </button>
                                                
                                                @endif
                                            @endif
                                        @endif
                                    </h6>
                                </div>

                                <div class="card-header-right">
                                    <div class="btn-group card-option">

                                        @if (Auth::user()->type == 'super admin')
                                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if(Auth::user()->type == 'company' && $user->type != 'client')
                                                    @php
                                                        $actionText = $isCoAdmin ? 'Demote from Co-Admin' : 'Promote to Co-Admin';
                                                        $iconClass = $isCoAdmin ? 'ti ti-arrow-down' : 'ti ti-arrow-up';
                                                        $tooltipText = $isCoAdmin ? __('Demote from Co-Admin') : __('Promote to Co-Admin');
                                                        $route = $isCoAdmin ? 'user.demote-to-co-admin' : 'user.promote-to-co-admin';

                                                    @endphp

                                                    <a href="{{ route($route, $user->id) }}" class="dropdown-item"
                                                        data-bs-original-title="{{ $tooltipText }}">
                                                        <i class="{{ $iconClass }}"></i>
                                                        <span>{{ $actionText }}</span>
                                                    </a>
                                                 @endif
                                                @canany(['edit member', 'edit user'])
                                                    <a href="{{ route('users.edit', $user->id) }}" class="dropdown-item"
                                                        data-bs-original-title="{{ __('Edit User') }}">
                                                        <i class="ti ti-pencil"></i>
                                                        <span>{{ __('Edit') }}</span>
                                                    </a>
                                                @endcan

                                                <a href="#!"
                                                    data-url="{{ route('company.reset', \Crypt::encrypt($user->id)) }}"
                                                    data-ajax-popup="true" data-size="md" class="dropdown-item"
                                                    data-bs-original-title="{{ __('Reset Password') }}"
                                                    data-title="{{ __('Reset Password') }}"
                                                    title="{{ __('Reset Password') }}">
                                                    <i class="ti ti-adjustments"></i>
                                                    <span> {{ __('Reset Password') }}</span>
                                                </a>

                                                @canany(['delete member', 'delete user'])
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['users.destroy', $user->id],
                                                        'id' => 'delete-form-' . $user->id,
                                                    ]) !!}
                                                    <a href="#" class="dropdown-item bs-pass-para"
                                                        data-id="{{ $user['id'] }}" data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $user->id }}"
                                                        title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top">
                                                        <i class="ti ti-archive"></i>
                                                        <span> {{ __('Delete') }}</span>
                                                    </a>
                                                    {!! Form::close() !!}
                                                @endcan


                                            </div>
                                        @else
                                            @if ($user->is_active == 1)
                                                <button type="button" class="btn dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>

                                                <div class="dropdown-menu dropdown-menu-end">
                                                   @if (Auth::user()->type == 'company' && $user->type != 'client')
                                                        @php
                                                            $actionText = $isCoAdmin ? 'Demote from Co-Admin' : 'Promote to Co-Admin';
                                                            $modalId = $isCoAdmin ? '#demotionConfirmationModal' : '#promotionConfirmationModal';
                                                            $iconClass = $isCoAdmin ? 'ti ti-arrow-down' : 'ti ti-arrow-up';
                                                            $imgPath = $isCoAdmin ? '../../storage/uploads/demote.svg' : '../../storage/uploads/promote.svg';
                                                            $tooltipText = $isCoAdmin ? __('Demote from Co-Admin') : __('Promote to Co-Admin');
                                                            $route = $isCoAdmin ? route('users.demote-to-co-admin') : route('users.promote-to-co-admin');
                                                            $successMessage = $isCoAdmin ? __('User demoted from Co-Admin successfully') : __('User promoted to Co-Admin successfully');
                                                            $errorMessage = $isCoAdmin ? __('User demoted from Co-Admin failed') : __('User promoted to Co-Admin failed');
                                                        @endphp
                                                        <a data-bs-toggle="modal" href="" class="dropdown-item"
                                                            data-bs-target="{{ $modalId }}"
                                                            data-route="{{ $route }}"
                                                            data-user-id="{{ $user->id }}"
                                                            data-success-message="{{ $successMessage }}"
                                                            data-error-message="{{ $errorMessage }}">

                                                            {{-- <i class="{{ $iconClass }} "></i> --}}
                                                            <img style="width:15px; height: 15.63px;" src="{{ $imgPath }}" alt="">

                                                            <span>{{ $actionText }}</span>

                                                        </a>
                                                    @endif

                                                    @canany(['edit member', 'edit user'])
                                                        <a href="{{ route('users.edit', $user->id) }}" class="dropdown-item"
                                                            data-bs-original-title="{{ __('Edit User') }}">
                                                            <i class="ti ti-pencil"></i>
                                                            <span>{{ __('Edit') }}</span>
                                                        </a>
                                                    @endcan

                                                    <a href="#!"
                                                        data-url="{{ route('company.reset', \Crypt::encrypt($user->id)) }}"
                                                        data-ajax-popup="true" data-size="md" class="dropdown-item"
                                                        data-bs-original-title="{{ __('Reset Password') }}"
                                                        data-title="{{ __('Reset Password') }}"
                                                        title="{{ __('Reset Password') }}">
                                                        <i class="ti ti-adjustments"></i>
                                                        <span> {{ __('Reset Password') }}</span>
                                                    </a>

                                                    @canany(['delete member', 'delete user'])
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['users.destroy', $user->id],
                                                            'id' => 'delete-form-' . $user->id,
                                                        ]) !!}
                                                        <a href="#" class="dropdown-item bs-pass-para"
                                                            data-id="{{ $user['id'] }}"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $user->id }}"
                                                            title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                            <i class="ti ti-archive"></i>
                                                            <span> {{ __('Delete') }}</span>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    @endcan


                                                </div>
                                            @else
                                                <a href="#" class="action-item"><i class="ti ti-lock"></i></a>
                                            @endif
                                        @endif


                                    </div>
                                </div>
                            </div>

                            @if (\Auth::user()->type == 'super admin'  && $companyUser == false)
                                <a href="{{ route('users.companyUsers', $user->id) }}" class="card-body full-card">
                                    <div class="img-fluid rounded-circle card-avatar">
                                        <img src="{{ !empty($user->avatar)
                                            ? asset('storage/uploads/profile/' . $user->avatar)
                                            : asset('storage/uploads/profile/avatar.png') }}"
                                            class="img-user wid-80 round-img
                                    rounded-circle">
                                    </div>
                                    <h4 class=" mt-3 text-primary">{{ $user->name }}</h4>

                                    <small class="text-primary">{{ $user->email }}</small>
                                    <p></p>
                                    <div class="text-center" data-bs-toggle="tooltip" title="{{ __('Last Login') }}">
                                        {{ !empty($user->last_login_at) ? $user->last_login_at : '' }}
                                    </div>

                                </a>
                            @else
                            <div class="card-body full-card">
                                <div class="img-fluid rounded-circle card-avatar">
                                    <img src="{{ !empty($user->avatar)
                                        ? asset('storage/uploads/profile/' . $user->avatar)
                                        : asset('storage/uploads/profile/avatar.png') }}"
                                        class="img-user wid-80 round-img
                                rounded-circle">
                                </div>
                                <h4 class=" mt-3 text-primary">{{ $user->name }}</h4>

                                <small class="text-primary">{{ $user->email }}</small>
                                <p></p>
                                <div class="text-center" data-bs-toggle="tooltip" title="{{ __('Last Login') }}">
                                    {{ !empty($user->last_login_at) ? $user->last_login_at : '' }}
                                </div>

                            </div>
                            @endif

                        </div>
                    </div>
                @endforeach

                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to logout this User?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                <button type="button" class="btn btn-primary confirm-action">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if (Gate::check('create member') || Gate::check('create user') || \Auth::user()->type == 'super admin')
                    @if ($companyUser == false)
                        <div class="col-md-6 col-xxl-3 col-lg-4 col-sm-6 border-end border-bottom">
                            <div class="card  shadow-none bg-transparent border h-100 text-center rounded-0">
                                <div class="card-body border-0 pb-0">
                                    @if (\Auth::user()->type == 'super admin')
                                        <a href="#" class="btn-addnew-project border-0" data-ajax-popup="true"
                                            data-size="lg" data-title="Create New Company"
                                            data-url="{{ route('users.create') }}">
                                            <div class="bg-primary proj-add-icon">
                                                <i class="ti ti-plus"></i>
                                            </div>
                                            <h6 class="mt-4 mb-2">{{ __('New Company') }}</h6>
                                            <p class="text-muted text-center">{{ __('Click here to add New Company') }}
                                            </p>
                                        </a>
                                    @else
                                        <a href="#" class="btn-addnew-project border-0" data-ajax-popup="true"
                                            data-size="xl" data-title="Create New User"
                                            data-url="{{ route('users.create') }}">
                                            <div class="bg-primary proj-add-icon">
                                                <i class="ti ti-plus"></i>
                                            </div>
                                            <h6 class="mt-4 mb-2">{{ __('New User') }}</h6>
                                            <p class="text-muted text-center">{{ __('Click here to add New User') }}</p>



                                        </a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    {{-- promote --}}
    <div class="modal fade" id="promotionConfirmationModal" tabindex="-1" aria-labelledby="promotionConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promotionConfirmationModalLabel">Confirm Promotion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to promote this User?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmPemotion">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    {{-- demote --}}
    <div class="modal fade" id="demotionConfirmationModal" tabindex="-1" aria-labelledby="demotionConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demotionConfirmationModalLabel">Confirm Demotion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to demote this User?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDemotion">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('custom-script')
    <script>
        $(document).ready(function() {
            var userId;

            $("a[data-bs-toggle='modal']").on("click", function(e) {
                e.preventDefault();
                userId = $(this).data("user-id");
            });

            $(".confirm-action").on("click", function() {
                // Perform action using userId
                // You can perform AJAX requests or other actions here
                console.log("Performing action for user with ID:", userId);

                if (userId) {
                    $.ajax({
                        url: "{{ route('delete.sessions') }}",
                        type: "POST",
                        data: {
                            user_id: userId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#confirmationModal').modal('hide');
                            location.reload();
                        },
                        error: function(error) {
                            alert(error);
                        }
                    });
                }
                // Close the modal
                $('#confirmationModal').modal('hide');
            });

            $('#confirmationModal').on('shown.bs.modal', function() {
                $('.confirm-action').focus();
            });
            var requestRoute;
            var requestUserID;
            var modalId;
            var successMessage;

            // When the modal is shown, store the task_id and user_id
            $('#promotionConfirmationModal,#demotionConfirmationModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                requestRoute = button.data('route');
                requestUserID = button.data('user-id');
                successMessage = button.data('success-message');
                errorMessage = button.data('error-message');

                // -bs-target
                modalId = button.data('bs-target');
                 console.log(requestRoute,requestUserID,modalId)
            });
        // // When the confirmPemotion  button is clicked, make the AJAX request
        $('#confirmPemotion,#confirmDemotion').click(function() {

            $(modalId).modal('hide');
            $.ajax({
                url: requestRoute,
                type: "POST",
                data: {
                    user_id: requestUserID,
        			_token: '{{csrf_token()}}'
                },
                success: function(response) {
                    if(response.success){
                        show_toastr('{{ __('Success') }}', successMessage,
                                'success')
                    }else{
                        show_toastr('{{ __('Error') }}', errorMessage,
                                'error')
                    }
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                },
                error: function(error) {
                    console.log('error Response:', error);
                    show_toastr('{{ __('Error') }}', errorMessage,
                            'error')
                }
            });
        });

        });
    </script>
@endpush
