@extends('layouts.app')

@if (\Auth::user()->type == 'super admin')
@section('page-title', __('Companies'))
@else
@section('page-title', __('Users'))
@endif

@section('action-button')
<div class="row align-items-end mb-3">
    <div class="col-md-12 d-flex justify-content-sm-end">
        <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true"
                data-size="md" data-title="Add Company" data-toggle="tooltip" title="{{ __('Grid View') }}" data-bs-original-title="{{__('Grid View')}}" data-bs-placement="top" data-bs-toggle="tooltip">
                <i class="ti ti-border-all"></i>
            </a>
        </div>


        @canany(['create member','create user'])
        <div class="text-end d-flex all-button-box justify-content-md-end justify-content-center">
            <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="md"
                data-title="Add Company" data-url="{{ route('users.create') }}" data-toggle="tooltip"
                title="{{ __('Create New Company') }}">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    </div>
</div>
@endcan

@endsection


@if (\Auth::user()->type == 'super admin')
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Companies') }}</li>
    @endsection
@else
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Users') }}</li>
    @endsection

@endif


@section('content')
<div class="row p-0">
    <div class="col-xl-12">
        <div class="">
            <div class="card-header card-body table-border-style">
                <h5></h5>
                <div class="table-responsive">
                    <table class="table dataTable data-table ">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Designation') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Mobile Number') }}</th>
                                <th width="100px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($users as $key => $user)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    @php
                                    // Split user type and user extra roles strings into arrays
                                    $userTypes = explode(',', $user->type);
                                    $userExtraRoles = explode(',', $user->role_title);

                                    // Merge the arrays, remove duplicates, and filter out empty values
                                    $userTypes = array_unique(array_filter(array_merge($userTypes, $userExtraRoles)));

                                    // Check if 'co-Admin' exists in the array
                                    $isCoAdmin = in_array('co-Admin', $userTypes);
                                    
                                    // Map "advocate" to "Attorney" if it exists in the array
                                    $userTypes = array_map(function ($type) {
                                        return ($type == 'advocate') ? 'Attorney' : $type;
                                    }, $userTypes);

                                    //  Join the values with a comma and space And Capitalize the first letter of each value
                                    $assignRoles = implode(', ', array_map('ucfirst', $userTypes));

                                @endphp
                                    
                                       {{ ucfirst($assignRoles ?? '-') }}
                                   </td>
                                <td>{{ $user->email }}</td>

                                <td>{{ $user_details[$key]->mobile_number ?? '-' }}</td>
                                <td class="float-end">
                                    @if (Auth::user()->type == 'company' & $user->type == 'advocate')
                                        @php
                                            $actionText = $isCoAdmin ? 'Demote from Co-Admin' : 'Promote to Co-Admin';
                                            $modalId = $isCoAdmin ? '#demotionConfirmationModal' : '#promotionConfirmationModal';
                                            $imgPath = $isCoAdmin ? '../../storage/uploads/demote.svg' : '../../storage/uploads/promote.svg';
                                            $tooltipText = $isCoAdmin ? __('Demote from Co-Admin') : __('Promote to Co-Admin');
                                            $route = $isCoAdmin ? route('users.demote-to-co-admin') : route('users.promote-to-co-admin');
                                            $successMessage = $isCoAdmin ? __('User demoted from Co-Admin successfully') : __('User promoted to Co-Admin successfully');
                                            $errorMessage = $isCoAdmin ? __('User demoted from Co-Admin failed') : __('User promoted to Co-Admin failed');
                                       @endphp
                                        <div class="action-btn  ms-2" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="{{ $tooltipText }}" data-size="md"
                                            data-title="{{ $actionText }}" style="ackground: background;
                                            border: 2px solid #e2e3e5">
                                            {{-- PromotionConfirmationModal --}}
                                            <a data-bs-toggle="modal" href=""
                                                class="mx-3 btn btn-sm  align-items-center"
                                                data-bs-target="{{ $modalId }}"
                                                data-route="{{ $route }}"
                                                data-user-id="{{ $user->id }}"
                                                data-success-message = "{{ $successMessage }}"
                                                data-error-message = "{{ $errorMessage }}">
                                                <img style="width:12px; height: 12.63px;" src="{{ $imgPath }}" alt="">

                                                {{-- <i class="{{ $iconClass }} "></i> --}}

                                            </a>
                                        </div>
                                    @endif
                                    @if (Auth::user()->type == 'company' || \Auth::user()->type == 'co admin')

                                        <div class="action-btn bg-light-secondary ms-2">
                                            <a data-url="{{route('users.show', $user->id)}}" href="#"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                data-ajax-popup="true" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ __('View Groups') }}" data-size="md"
                                                data-title="{{$user->name . __("'s Group")}}">

                                                                    <i class="ti ti-eye "></i>

                                            </a>
                                        </div>

                                    @endif

                                    @canany(['edit member','edit user'])
                                        <div class="action-btn bg-light-secondary ms-2">
                                            <a href="{{route('users.edit', $user->id)}}"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ __('Edit') }}">

                                                <i class="ti ti-edit "></i>

                                            </a>
                                        </div>
                                    @endcan

                                    @if(\Auth::user()->type == "super admin")
                                        <div class="action-btn bg-light-secondary ms-2">
                                            <a href="#" data-url="{{route('plan.upgrade',$user->id)}}"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                    data-tooltip="Edit" data-ajax-popup="true" data-title="{{__('Upgrade Plan')}}" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{__('Upgrade Plan')}}">

                                                <i class="ti ti-trophy "></i>

                                            </a>
                                        </div>
                                    @endif

                                    <div class="action-btn bg-light-secondary ms-2">
                                        <a href="#" data-url="{{route('company.reset',\Crypt::encrypt($user->id))}}"
                                            class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                data-tooltip="Edit" data-ajax-popup="true" data-title="{{__('Reset Password')}}" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{__('Reset Password')}}">

                                            <i class="ti ti-key "></i>

                                        </a>
                                    </div>


                                    @canany(['delete member','delete user'])
                                        <div class="action-btn bg-light-secondary ms-2">
                                            <a href="#"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para "
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $user->id }}" title="{{ __('Delete') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        </div>
                                    @endcan

                                    @if(\Auth::user()->type == "super admin" || \Auth::user()->type == "company" || \Auth::user()->type == "co admin"  )
                                    @if($user->last_activity == 'Active')
                                        <a href="#" class="btn btn-sm bg-light-secondary text-sm" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-user-id="{{ $user->id }}" title="Click to log out user">
                                            <i class="bi bi-circle-fill" style="color: green;"></i> <!-- Green circle for online -->
                                        </a>
                                    @else
                                        <a href="#" class="btn btn-sm bg-light-secondary text-sm" title="offline">
                                            <i class="bi bi-circle-fill" style="color: red;"></i> <!-- Red circle for offline -->
                                        </a>
                                    @endif
                                    @endif

                                    {!! Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['users.destroy', $user->id],
                                    'id' => 'delete-form-' . $user->id,
                                    ]) !!}
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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