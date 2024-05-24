@extends('layouts.app')
@section('page-title')
    {{ __('Manage Plans') }}
@endsection
@section('action-button')
    @php
        $coAdminCompany = \App\Models\User::where('id', \Auth::user()->creatorId())->first();
        if (\Auth::user()->type == 'co admin') {
            $assignPlan = $coAdminCompany->plan;
        } else {
            $assignPlan = \Auth::user()->plan;
        }
    @endphp
    <div>
        @can('create plan')
            {{-- @if (count($payment_setting) > 0) --}}
            <div class="float-end">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-url="{{ route('plans.create') }}" data-size="lg"
                    data-ajax-popup="true" data-title="{{ __('Create Plan') }}" title="{{ __('Create') }}" data-bs-toggle="tooltip"
                    data-bs-placement="top">
                    <i class="ti ti-plus"></i>
                </a>
            </div>
            {{-- @endif --}}
        @endcan
        @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'co admin')
            <div class="float-end">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-url="{{ route('plans.create-company') }}"
                    data-size="lg" data-ajax-popup="true" data-title="{{ __('Create Plan Request') }}"
                    title="{{ __('Create') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                    <i class="ti ti-plus"></i>
                </a>
            </div>
        @endif
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Plan') }}</li>
@endsection

@section('content')
    @can('create plan')
        <div class="row g-o p-0">
            <div class="col-12">
                {{-- @if (count($payment_setting) == 0)
                    <div class="alert alert-warning"><i class="fe fe-info"></i> {{__('Please set payment api key & secret key for add new plan')}}</div>
                @endif --}}
            </div>
        </div>
    @endcan


    <div class="container">
        <div class="row mb-5 mt-5">
            @if(Auth::user()->type != 'super admin')
            <div class="col-12 text-center ">
              <h3 class="main_plan_title">
                Choose a plan that suits for your business
              </h3>
            </div>
            @endif
          </div>
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-center align-items-center month_plan_switcher gap-2">
                    <p class="mb-0 plan_switcher_label">Monthly</p>
                    <div class="form-check form-switch mb-0 d-flex justify-content-center align-items-center">
                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" />
                    </div>
                    <p class="mb-0 plan_switcher_label active_plan">Annual</p>
                </div>
               
            </div>
        </div>
        <div id="card_monthly" class="card_month">
            <div  class="row plans_card_wrapper  mx-2">
                @forelse ($plansMonthly as $plan)
                    <div class="col-md-4 col-xl-3">
                        <div class="plan_card_holder">
                            <div class="text-center">
                                <p class="mb-0 plan_card_status">{{ $plan->name }}</p>
                                <div class="d-flex flex-row-reverse m-0 p-0 ">
                                    @can('edit plan')
                                        <div class="action-btn bg-light-secondary ms-2">
                                            <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                title="{{ __('Edit') }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-ajax-popup="true" data-size="lg" data-title="{{ __('Edit Plan') }}"
                                                data-url="{{ route('plans.edit', $plan->id) }}" data-bs-toggle="tooltip"
                                                data-bs-placement="top"><i class="ti ti-edit"></i></a>
                                        </div>
                                    @endcan

                                </div>
                                <div class="" style="position: relative">
                                    @if (
                                        (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id) ||
                                            (\Auth::user()->type == 'co admin' && $coAdminCompany->plan == $plan->id))
                                        <p style="position: absolute; top:0; right:0;" class="d-flex align-items-center ">
                                            <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                            <span class="ms-2">{{ __('Active') }}</span>
                                        </p>
                                    @endif
                                    <h1 class="plan_card_title">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}
                                        {{ number_format($plan->price) }} </h1>
                                </div>

                                <p class="mb-0 per_month_title">{{ $plan->duration }}</p>

                            </div>
                            <div class="mt-4">
                            
                                @can('buy plan')
                                    @if ($plan->id != $assignPlan && $plan->price >= 0)
                                        <div class="d-grid text-center ">
                                            <a href="#"
                                                class="btn btn-outline-primary plans_card_btn subscribe-button btn-sm d-flex justify-content-center align-items-center"
                                                data-plan-id="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">{{ __(' Try It Now') }}
                                                <i class="fas fa-arrow-right m-1"></i>
                                            </a>
                                            <p></p>
                                        </div>
                                    @endif
                                @endcan

                                @if (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id)
                                    @if (empty(\Auth::user()->plan_expire_date))
                                    <button type="button" style="font-family: fangsong;" class="btn btn-outline-primary plans_card_btn">
                                        {{ __('Lifetime') }}
                                    </button>
                                        {{-- <p class="mb-0 text-center">{{ __('Lifetime') }}</p> --}}
                                    @else
                                        <p class="mb-0 text-center">
                                            <button type="button" style="font-family: fangsong;" class="btn btn-outline-primary plans_card_btn">
                                                {{ __('Expire on ') }}
                                            {{ date('d M Y', strtotime(\Auth::user()->plan_expire_date)) }}
                                            </button>
                                        
                                        </p>
                                    @endif
                                @elseif(\Auth::user()->type == 'co admin' && $coAdminCompany->plan == $plan->id)
                                    @if (empty($coAdminCompany->plan_expire_date))
                                    <button type="button" style="font-family: fangsong;" class="btn btn-outline-primary plans_card_btn">
                                        {{ __('Lifetime') }}
                                    </button>
                                    @else
                                    <button type="button" style="font-family: fangsong;" class="btn btn-outline-primary plans_card_btn">
                                        {{ __('Expire on ') }}
                                        {{ date('d M Y', strtotime($coAdminCompany->plan_expire_date)) }}
                                    </button>
                                    
                                    @endif
                                @endif

                                <ul class="list-group mt-4">
                                    <li
                                        class="list-group-item d-flex justify-content-start align-items-center gap-2 p-0 border-0 mb-2">
                                        <span class="badge badge-success p-0">
                                            <!-- Tick icon SVG -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none">
                                                <path d="M4.16663 10.8333L7.49996 14.1667L15.8333 5.83333" stroke="#485AFF"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        {{ $plan->max_users < 0 ? __('Unlimited') : $plan->max_users }} Users

                                    </li>
                                    @php
                                         $detailsData = json_decode($plan->details, true); 
                                    @endphp
                                    @if ($detailsData)
                                    @foreach ($detailsData as $details)
                                    <li
                                    class="list-group-item d-flex justify-content-start align-items-center gap-2 p-0 border-0 mb-2">
                                    <span class="badge badge-success p-0">
                                        <!-- Tick icon SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none">
                                            <path d="M4.16663 10.8333L7.49996 14.1667L15.8333 5.83333" stroke="#485AFF"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    {{ $details }}
                                        </li>
                                    @endforeach
                                   
                                    @endif
                                   

                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="p-4  m-0 text-center">No Plan Added By the Admin</p>
                @endforelse 
                
    
            </div>
        </div>
        <div style="display: none" id="card_yearly" class="card_yearly">
        <div  class="row plans_card_wrapper  mx-2">
            
            @forelse ($plansYearly as $plan)
                <div class="col-md-4 col-xl-3">
                    <div class="plan_card_holder">
                        <div class="text-center">
                            <p class="mb-0 plan_card_status">{{ $plan->name }}</p>
                            <div class="d-flex flex-row-reverse m-0 p-0 ">
                                @can('edit plan')
                                    <div class="action-btn bg-light-secondary ms-2">
                                        <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                            title="{{ __('Edit') }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-ajax-popup="true" data-size="lg" data-title="{{ __('Edit Plan') }}"
                                            data-url="{{ route('plans.edit', $plan->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top"><i class="ti ti-edit"></i></a>
                                    </div>
                                @endcan

                            </div>
                            <div class="" style="position: relative">
                                @if (
                                    (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id) ||
                                        (\Auth::user()->type == 'co admin' && $coAdminCompany->plan == $plan->id))
                                    <p style="position: absolute; top:0; right:0;" class="d-flex align-items-center ">
                                        <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                        <span class="ms-2">{{ __('Active') }}</span>
                                    </p>
                                @endif
                                <h1 class="plan_card_title">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}
                                    {{ number_format($plan->price) }} </h1>
                            </div>

                            <p class="mb-0 per_month_title">{{ $plan->duration }}</p>

                        </div>
                        <div class="mt-4">
                           
                            @can('buy plan')
                                @if ($plan->id != $assignPlan && $plan->price >= 0)
                                    <div class="d-grid text-center ">
                                        <a href="#"
                                            class="btn btn-outline-primary plans_card_btn subscribe-button btn-sm d-flex justify-content-center align-items-center"
                                            data-plan-id="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">{{ __(' Try It Now') }}
                                            <i class="fas fa-arrow-right m-1"></i>
                                        </a>
                                        <p></p>
                                    </div>
                                @endif
                            @endcan

                            @if (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id)
                                @if (empty(\Auth::user()->plan_expire_date))
                                <button type="button" style="font-family: fangsong;" class="btn btn-outline-primary plans_card_btn">
                                    {{ __('Lifetime') }}
                                </button>
                                    {{-- <p class="mb-0 text-center">{{ __('Lifetime') }}</p> --}}
                                @else
                                    <p class="mb-0 text-center">
                                        <button type="button" style="font-family: fangsong;" class="btn btn-outline-primary plans_card_btn">
                                            {{ __('Expire on ') }}
                                        {{ date('d M Y', strtotime(\Auth::user()->plan_expire_date)) }}
                                        </button>
                                       
                                    </p>
                                @endif
                            @elseif(\Auth::user()->type == 'co admin' && $coAdminCompany->plan == $plan->id)
                                @if (empty($coAdminCompany->plan_expire_date))
                                <button type="button" style="font-family: fangsong;" class="btn btn-outline-primary plans_card_btn">
                                    {{ __('Lifetime') }}
                                </button>
                                @else
                                <button type="button" style="font-family: fangsong;" class="btn btn-outline-primary plans_card_btn">
                                    {{ __('Expire on ') }}
                                    {{ date('d M Y', strtotime($coAdminCompany->plan_expire_date)) }}
                                </button>
                                   
                                @endif
                            @endif

                            <ul class="list-group mt-4">
                                <li
                                    class="list-group-item d-flex justify-content-start align-items-center gap-2 p-0 border-0 mb-2">
                                    <span class="badge badge-success p-0">
                                        <!-- Tick icon SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none">
                                            <path d="M4.16663 10.8333L7.49996 14.1667L15.8333 5.83333" stroke="#485AFF"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    {{ $plan->max_users < 0 ? __('Unlimited') : $plan->max_users }} Users

                                </li>
                                  @php
                                         $detailsData = json_decode($plan->details, true); 
                                    @endphp
                                    @if ($detailsData)
                                    @foreach ($detailsData as $details)
                                    <li
                                    class="list-group-item d-flex justify-content-start align-items-center gap-2 p-0 border-0 mb-2">
                                    <span class="badge badge-success p-0">
                                        <!-- Tick icon SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none">
                                            <path d="M4.16663 10.8333L7.49996 14.1667L15.8333 5.83333" stroke="#485AFF"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    {{ $details }}
                                        </li>
                                    @endforeach
                                   
                                    @endif

                            </ul>
                        </div>
                    </div>
                </div>
                @empty
                <p class="p-4 m-0 text-center">No Plan Added By the Admin</p>
            @endforelse 

        </div>
    </div>
    </div>


    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" id="editTaskModalBody">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="trialExpiredModal" tabindex="-1" aria-labelledby="trialExpiredModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trialExpiredModalLabel">Trial Expired</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="modal-body text-center">
                    <img style="height: 80px" src='{{ asset('storage/uploads/warning.png') }}' alt="warning">

                    <p style="font-size: 16px" class="text-center mt-3">
                        Your 15 days trial has expired. If you want to continue using ecasify, please pay at earliest in the Plans Section.
                    </p>
                </div>
                <div class="modal-footer text-center justify-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    

 

@endsection

@push('custom-script')

<script>
    @if(session('trialExpired'))
    $(document).ready(function() {
        // Show the modal when the trial has expired
        $('#trialExpiredModal').modal('show');
    });
    @endif
</script>

    <script>

        const switchInput = document.getElementById('flexSwitchCheckDefault');
        const cardYearly = document.getElementById('card_yearly');
        const cardMonthly = document.getElementById('card_monthly');

        // Initial state
        if (switchInput.checked) {
            
            cardYearly.style.display = 'block';
            cardMonthly.style.display = 'none';
        } else {
        
            cardYearly.style.display = 'none';
            cardMonthly.style.display = 'block';
        
        }

        // Listen for changes in the switch state
        switchInput.addEventListener('change', function () {
        
            if (switchInput.checked) {
                console.log('hh');
                cardYearly.style.display = 'block';
                cardMonthly.style.display = 'none';
            } else {
                cardYearly.style.display = 'none';
                cardMonthly.style.display = 'block';
            }
        });


        $(document).ready(function() {

            $(document).on("click", ".subscribe-button", function(e) {
                e.preventDefault();

                var planId = $(this).data('plan-id');
                console.log(planId);
                var url = "{{ route('payment', ':code') }}".replace(':code', planId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {

                        console.log(response.downgrade);
                     
                        if (response.action === 'openModal') {
                          
                            $('#editTaskModalBody').html(response.view);
                            $('#editTaskModal').modal('show');
                        } else if (response.action === 'openNewTab') {
                            
                            window.location.href = response.url;

                        }
                    }
                });

            });
        });
    </script>
@endpush
