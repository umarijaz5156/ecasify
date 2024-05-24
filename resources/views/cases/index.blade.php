@extends('layouts.app')

@section('page-title', __('Case'))

@section('action-button')
    @can('create case')
        <div class="text-sm-end d-flex all-button-box justify-content-sm-end">
            <a href="{{ route('cases.create') }}" class="btn btn-sm btn-primary mx-1" data-toggle="tooltip"
                title="{{ __('Create Case') }}" data-bs-original-title="{{ __('Add Case') }}" data-bs-placement="top"
                data-bs-toggle="tooltip">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endcan

@endsection
@php
use Carbon\Carbon;
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Case') }}</li>
@endsection

@section('content')

    <div class="row p-0">
        <div class="col-xl-12">

            <div class="row p-0 g-0 justify-content-center mt-5">
                <div class="tsk_table_wrapper">
                    <table class="table dataTable data-table ">
                        <thead>
                            <tr>
                                <th class="p-0">
    
                                </th>
                                <th>
                                    <label class="list-group-item tb_checbox">
                                        {{-- <input class="form-check-input me-1" type="checkbox" value=""> --}}
                                        #
                                    </label>
                                </th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Incident Date') }}</th>
                                <th>{{ __('Practice Area') }}</th>
                                <th>{{ __('Statue of Limitations') }}</th>
                                <th>{{ __('Dates') }}</th>
                                <th>{{ __('Stage') }}</th>
                                <th width="100px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $index = 0;
                            @endphp
                            @foreach ($cases as $case)
                                @php
                                    $index++;
                                @endphp
                                <tr>
                                    <td class="p-0">
                                        <button class="accordion-item btn accordian_table_btn" id="headingOne"
                                            data-bs-toggle="collapse" data-bs-target="#collapseOne-{{ $index }}"
                                            aria-expanded="true" aria-controls="collapseOne">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="8" height="11"
                                                viewBox="0 0 8 11" fill="none">
                                                <path
                                                    d="M0.254561 -1.34278e-05L-1.19898e-05 10.997L7.1254 5.66051L0.254561 -1.34278e-05Z"
                                                    fill="#AFAFAF" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td>
                                        <label class="list-group-item tb_checbox">
                                            {{-- <input class="form-check-input me-1" type="checkbox" value=""> --}}
                                            {{ $index }}
                                        </label>
                                    </td>
                                    <td>
    
                                        <p class="mb-2"><a href="{{ route('cases.show', $case->id) }}"
                                            class="align-items-center "
                                            data-title="{{ __(' View Case') }}" title="{{ __('View Case') }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top">
                                           {{ $case->name }}
                                        </a></p>
                                       <b># {{ $case->id }}</b> 
                                    </td>
    
                                    <td>{{ $case->incident_date }}</td>
    
                                    <td>{{ $case->practice_area?? '' }}
                                           
                                    </td>
                                    <td>{{ $case->statute_of_limitations ?? '' }}
                                          
                                    </td>
                                    <td>
                                        <p>
                                          <img class="pr-2" src="{{ asset('storage/uploads/calendar_case.png') }}" alt="calendar">  <span style="padding-left:5px;padding-right:5px">{{  $case->open_date }}</span>  | <span style="padding-left:5px">{{  $case->close_date }}</span> 
                                        </p>
                                        
                                    </td>
                                    <td> 
                                        @switch($case->case_stage)
                                            @case('Ready For Demand')
                                            <img class="pr-2" src="{{ asset('storage/uploads/ready.png') }}" alt="ready">
                                                @break
                                            @case('Demand Sent')
                                            <img class="pr-2" src="{{ asset('storage/uploads/send.png') }}" alt="send">
                                             @break
                                             @case('Investigation')
                                             <img class="pr-2" src="{{ asset('storage/uploads/frog.png') }}" alt="Investigation">
                                             @break
                                            @default
                                                
                                        @endswitch 
                                       
                                        {{  $case->case_stage }} 
                                    </td>
    
                                    <td>
    
                                        <div class="d-flex justify-content-start align-items-center" style="gap: 0.5rem">
                                        

                                        @can('view case')
                                        <button class="btn tsk_view_btn">
                                            <a href="{{ route('cases.show', $case->id) }}"
                                                class="align-items-center "
                                                data-title="{{ __(' View Case') }}" title="{{ __('View Case') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                    viewBox="0 0 13 9" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M3.77783 4.25C3.77783 3.62379 4.02659 3.02323 4.46939 2.58044C4.91218 2.13764 5.51274 1.88889 6.13894 1.88889C6.76515 1.88889 7.36571 2.13764 7.8085 2.58044C8.2513 3.02323 8.50005 3.62379 8.50005 4.25C8.50005 4.8762 8.2513 5.47676 7.8085 5.91955C7.36571 6.36235 6.76515 6.61111 6.13894 6.61111C5.51274 6.61111 4.91218 6.36235 4.46939 5.91955C4.02659 5.47676 3.77783 4.8762 3.77783 4.25ZM6.13894 2.83333C5.76322 2.83333 5.40289 2.98259 5.13721 3.24826C4.87153 3.51394 4.72228 3.87427 4.72228 4.25C4.72228 4.62572 4.87153 4.98605 5.13721 5.25173C5.40289 5.51741 5.76322 5.66666 6.13894 5.66666C6.51467 5.66666 6.875 5.51741 7.14068 5.25173C7.40635 4.98605 7.55561 4.62572 7.55561 4.25C7.55561 3.87427 7.40635 3.51394 7.14068 3.24826C6.875 2.98259 6.51467 2.83333 6.13894 2.83333Z"
                                                        fill="#8DC313" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M1.30522 3.39748C1.04141 3.77778 0.944444 4.07559 0.944444 4.25C0.944444 4.42441 1.04141 4.72222 1.30522 5.10252C1.56085 5.46959 1.93989 5.86815 2.41967 6.23648C3.38111 6.97441 4.69893 7.55556 6.13889 7.55556C7.57885 7.55556 8.89667 6.97441 9.85811 6.23648C10.3379 5.86815 10.7169 5.46959 10.9726 5.10252C11.2364 4.72222 11.3333 4.42441 11.3333 4.25C11.3333 4.07559 11.2364 3.77778 10.9726 3.39748C10.7169 3.03041 10.3379 2.63185 9.85811 2.26352C8.89667 1.52559 7.57885 0.944444 6.13889 0.944444C4.69893 0.944444 3.38111 1.52559 2.41967 2.26352C1.93989 2.63185 1.56085 3.03041 1.30522 3.39748ZM1.84419 1.51426C2.93407 0.678111 4.44896 0 6.13889 0C7.82881 0 9.3437 0.678111 10.433 1.51426C10.9789 1.93296 11.4297 2.40015 11.7483 2.85915C12.058 3.30556 12.2778 3.79478 12.2778 4.25C12.2778 4.70522 12.0574 5.19444 11.7483 5.64085C11.4297 6.09985 10.9789 6.56641 10.4336 6.98574C9.34433 7.82189 7.82881 8.5 6.13889 8.5C4.44896 8.5 2.93407 7.82189 1.84481 6.98574C1.29893 6.56704 0.848111 6.09985 0.529519 5.64085C0.22037 5.19444 0 4.70522 0 4.25C0 3.79478 0.22037 3.30556 0.529519 2.85915C0.848111 2.40015 1.29893 1.93359 1.84419 1.51426Z"
                                                        fill="#8DC313" />
                                                </svg>
                                            </a>
                                        </button>
                                    @endcan

                                    @can('edit case')
                                        <button class="btn  tsk_edit_btn">
                                            <a href="{{ route('cases.edit', $case->id) }}" class=" align-items-center "
                                                data-title="{{ __('Edit') }}" title="{{ __('Edit') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                    viewBox="0 0 11 11" fill="none">
                                                    <path
                                                        d="M7.60389 2.27461L8.72521 3.39541M8.32496 1.28723L5.29294 4.31925C5.13628 4.4757 5.02944 4.67502 4.98588 4.89209L4.70581 6.29401L6.10773 6.01342C6.32479 5.97 6.52386 5.86359 6.68057 5.70688L9.71259 2.67486C9.8037 2.58375 9.87598 2.47558 9.92528 2.35653C9.97459 2.23749 9.99997 2.1099 9.99997 1.98105C9.99997 1.85219 9.97459 1.7246 9.92528 1.60556C9.87598 1.48651 9.8037 1.37835 9.71259 1.28723C9.62148 1.19612 9.51331 1.12385 9.39426 1.07454C9.27522 1.02523 9.14763 0.999847 9.01878 0.999847C8.88992 0.999847 8.76233 1.02523 8.64329 1.07454C8.52424 1.12385 8.41608 1.19612 8.32496 1.28723Z"
                                                        stroke="#27ADCA" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M8.94126 7.35285V8.94113C8.94126 9.22195 8.8297 9.49127 8.63113 9.68985C8.43256 9.88842 8.16323 9.99998 7.88241 9.99998H2.05873C1.7779 9.99998 1.50858 9.88842 1.31001 9.68985C1.11144 9.49127 0.999878 9.22195 0.999878 8.94113V3.11744C0.999878 2.83662 1.11144 2.5673 1.31001 2.36872C1.50858 2.17015 1.7779 2.05859 2.05873 2.05859H3.64701"
                                                        stroke="#27ADCA" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                        </button>
                                    @endcan

                                    @can('delete case')
                                            <button class="btn  tsk_delete_btn">
                                                <a href="#" class=" align-items-center bs-pass-para"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-confirm-yes="delete-form-{{ $case->id }}"
                                                    title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                        viewBox="0 0 11 12" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M6.85586 0.905365H5.00306C4.88411 0.905365 4.7805 0.905365 4.68293 0.920714C4.49287 0.951161 4.31258 1.02567 4.15648 1.13827C4.00037 1.25088 3.87279 1.39845 3.78394 1.56919C3.73789 1.6569 3.70555 1.75502 3.66773 1.8674L3.60688 2.05103C3.5649 2.20022 3.47358 2.33078 3.34784 2.42138C3.2221 2.51197 3.06935 2.55727 2.91455 2.54986H1.27005C1.16101 2.54986 1.05644 2.59318 0.979336 2.67028C0.902235 2.74738 0.858921 2.85195 0.858921 2.96099C0.858921 3.07003 0.902235 3.1746 0.979336 3.2517C1.05644 3.3288 1.16101 3.37211 1.27005 3.37211H10.5889C10.6979 3.37211 10.8025 3.3288 10.8796 3.2517C10.9567 3.1746 11 3.07003 11 2.96099C11 2.85195 10.9567 2.74738 10.8796 2.67028C10.8025 2.59318 10.6979 2.54986 10.5889 2.54986H8.89504C8.74862 2.54635 8.60718 2.49602 8.49147 2.40625C8.37575 2.31647 8.29184 2.19198 8.25204 2.05103L8.19065 1.8674C8.15337 1.75502 8.12103 1.6569 8.07553 1.56919C7.98662 1.39839 7.85896 1.25077 7.70275 1.13816C7.54655 1.02555 7.36614 0.95108 7.17599 0.920714C7.07842 0.905365 6.97481 0.905365 6.85641 0.905365H6.85586ZM7.56957 2.54986C7.53108 2.47448 7.499 2.396 7.47365 2.31525L7.41883 2.1508C7.36895 2.00115 7.35743 1.971 7.34592 1.94907C7.31633 1.89207 7.27379 1.84279 7.22172 1.80519C7.16965 1.76759 7.10949 1.74271 7.04608 1.73255C6.97462 1.72651 6.90285 1.72487 6.83119 1.72761H5.02773C4.86985 1.72761 4.83696 1.72871 4.81285 1.7331C4.74948 1.7432 4.68936 1.76799 4.63729 1.8055C4.58522 1.843 4.54266 1.89217 4.513 1.94907C4.50149 1.971 4.48997 2.00115 4.44009 2.15135L4.38528 2.3158L4.3639 2.37719C4.34252 2.43749 4.3173 2.4945 4.28935 2.54986H7.56957Z"
                                                            fill="#E94511" />
                                                        <path
                                                            d="M9.26503 4.30398C9.2723 4.19516 9.3225 4.09368 9.40458 4.02188C9.48667 3.95007 9.59392 3.91381 9.70274 3.92108C9.81156 3.92835 9.91303 3.97855 9.98484 4.06064C10.0566 4.14272 10.0929 4.24997 10.0856 4.35879L9.83128 8.16965C9.78469 8.87239 9.74687 9.4403 9.65806 9.8865C9.56542 10.3497 9.40865 10.7367 9.08413 11.0398C8.76017 11.3435 8.36329 11.4745 7.89461 11.5354C7.44402 11.5946 6.87502 11.5946 6.17008 11.5946H5.68824C4.98385 11.5946 4.4143 11.5946 3.96371 11.5354C3.49558 11.4745 3.0987 11.3435 2.77419 11.0398C2.45022 10.7367 2.29345 10.3492 2.20081 9.8865C2.112 9.44084 2.07473 8.87239 2.02759 8.16965L1.77324 4.35879C1.76597 4.24997 1.80223 4.14272 1.87403 4.06064C1.94584 3.97855 2.04731 3.92835 2.15613 3.92108C2.26495 3.91381 2.3722 3.95007 2.45429 4.02188C2.53637 4.09368 2.58657 4.19516 2.59384 4.30398L2.846 8.08632C2.89533 8.8247 2.93042 9.33888 3.00716 9.72534C3.08226 10.1008 3.18641 10.2993 3.33606 10.4396C3.48626 10.5799 3.69127 10.6709 4.0706 10.7203C4.46145 10.7712 4.97672 10.7723 5.71729 10.7723H6.14158C6.8816 10.7723 7.39688 10.7712 7.78827 10.7203C8.1676 10.6709 8.37261 10.5799 8.52281 10.4396C8.67246 10.2993 8.77661 10.1008 8.85171 9.72589C8.92845 9.33888 8.96354 8.8247 9.01287 8.08578L9.26503 4.30398Z"
                                                            fill="#E94511" />
                                                        <path
                                                            d="M7.34095 5.29288C7.2325 5.28201 7.12417 5.31465 7.03978 5.38363C6.95539 5.4526 6.90184 5.55226 6.8909 5.6607L6.61682 8.40154C6.60879 8.50847 6.64286 8.61431 6.71177 8.69648C6.78068 8.77865 6.87896 8.83065 6.98566 8.84138C7.09236 8.8521 7.19903 8.82072 7.28292 8.75391C7.3668 8.6871 7.42126 8.59016 7.43468 8.48376L7.70877 5.74293C7.71963 5.63448 7.68699 5.52615 7.61802 5.44176C7.54905 5.35737 7.44939 5.30382 7.34095 5.29288ZM4.51789 5.29288C4.40955 5.30382 4.30998 5.3573 4.24102 5.44157C4.17207 5.52584 4.13935 5.63402 4.15007 5.74238L4.42415 8.48321C4.43774 8.58941 4.49223 8.68611 4.57601 8.75276C4.6598 8.81941 4.76627 8.85074 4.8728 8.8401C4.97933 8.82946 5.07751 8.77769 5.14646 8.69579C5.21541 8.61389 5.24969 8.50832 5.24202 8.40154L4.96793 5.6607C4.95699 5.55237 4.90352 5.45279 4.81925 5.38384C4.73498 5.31488 4.62625 5.28217 4.51789 5.29288Z"
                                                            fill="#E94511" />
                                                    </svg>
                                                </a>
                                            </button>
                                    @endcan
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['cases.destroy', $case->id],
                                        'id' => 'delete-form-' . $case->id,
                                    ]) !!}
                                    {!! Form::close() !!}
    
    
                                    </td>
                                </tr>
                                <tr>
                                    <td style="display: none"></td>
                                    <td style="display: none"></td>
                                    <td style="display: none"></td>
                                    <td style="display: none"></td>
                                    <td style="display: none"></td>
                                    <td style="display: none"></td>
                                    <td style="display: none"></td>
                                    <td style="display: none"></td>
    
                                    <td colspan="9" class="p-0" style="border: none">
                                        <div id="collapseOne-{{ $index }}" class="collapse collapse_table_wrapper"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <table class="table collapse_table table-borderless">
                                                <thead>
                                                      <th class="p-0 px-3">Assignees: </th>
                                                        <th class="p-0 px-3">Clients: </th>
                                                        <th  class="p-0 px-3">Description:</th>

                                                    </tr>
                                                </thead>
    
                                                <tbody>
                                                    @php
                                                        $taskTeamArray = explode(',', $case->your_advocates);
                                                        $taskClientArray = explode(',', $case->your_team);
                                                    @endphp
                                                    <tr>
                                                        <td style="width:25%">
                                                            <div style="height:100%" class="tsk_table_case_assigness custom-scroll">
                                      
                                                                @foreach ($taskTeamArray as $team)
                                                                    @if (!empty($team))
                                                                        <button class="btn btn_assignes">
                                                                            {{ UsersNameById($team) }}
                                                                        </button>
                                                                    @else
                                                                        <p>No Assignees</p>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td style="width:25%">
                                                            <div style="height:100%"  class="tsk_table_case_assigness custom-scroll">
                                      
                                                                @foreach ($taskClientArray as $team)
                                                                    @if (!empty($team))
                                                                        <button class="btn btn_assignes">
                                                                            {{ UsersNameById($team) }}
                                                                        </button>
                                                                    @else
                                                                        <p>No Assignees</p>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div style="width: 100%" class="tsk_table_case_show_history custom-scroll">
                                                                <ul class="mb-0" style="white-space: normal;">
                                                                    <li class="mb-2">
                                                                        <p class="mb-0 tsk_table_case_show_history_click">
                                                                            {!! $case->description !!}
                                                                        </p>
    
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
    
                        </tbody>
                    </table>
                </div>
    
    
    
    
    
            </div>

            {{-- case view old --}}
            {{-- <div class="card-header card-body table-border-style">
                <h5></h5>
                <div class="table-responsive">
                    <table class="table dataTable">
                        <thead>
                            <tr>
                              <th>{{ __('Name') }}</th>
                                <th>{{ __('Case Number') }}</th>
                                <th>{{ __('Case Type') }}</th>
                                <th>{{ __('Date Case Opened') }}</th>

                                <th>{{ __('SOL') }}</th>
                                <th width="100px">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cases as $case)
                                
                            @php
                        
                            if($case->incident_date){
                                $incidentDate = Carbon::createFromFormat('Y-m-d', $case->incident_date ?? '');
                                // Convert the SOL value (e.g., "2 Years") to days
                                $solValue = $case->statute_of_limitations; // This is the SOL value
                                preg_match('/(\d+) Year/', $solValue, $matches);
                                $solInYears = $matches[1] ?? 0; // Extract the number of years from the SOL value
                                $solInDays = $solInYears * 365; // Convert years to days
                            
                                // Calculate the difference in days between incident date and today
                                $today = Carbon::now();
                                $daysPassed = $incidentDate->diffInDays($today);
                            
                                // Calculate the remaining days considering SOL
                                $remainingDays = $solInDays - $daysPassed;
                            }else{
                                $incidentDate = null;
                                $remainingDays = null;
                            }

                            
                        
                        @endphp
                        


                            <tr>
                                    
                                    <td>
                                        <a href="{{ route('cases.show', $case->id) }}">
                                        {{ !empty($case->name) ? $case->name : ' ' }} 
                                        </a>

                                    </td>
                                    <td>
                                        {{ !empty($case->case_number) ? $case->case_number : ' ' }} 
                                    </td>
                                   
                                    <td>
                                        {{ $case->practice_area }}
                                    </td>

                                    <td>{{ $case->open_date }}</td>


                                    <td style="{{ $remainingDays < 0 ? 'color: red;' : '' }}">
                                        @if($remainingDays != null)
                                        @if ($remainingDays < 0)
                                            {{ abs($remainingDays) . ' days passed' }}
                                        @else
                                            {{ $remainingDays . ' days left' }}
                                        @endif
                                        
                                        @endif
                                    </td>
                                    <td> 

                                        @can('view case')
                                            <div class="action-btn bg-light-secondary ms-2">
                                                <a href="{{ route('cases.show', $case->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                    data-title="{{ __('View Cause') }}"
                                                    title="{{ __('View') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top"><i class="ti ti-eye "></i></a>
                                            </div>
                                        @endcan

                                        @can('edit case')
                                            <div class="action-btn bg-light-secondary ms-2">
                                                <a href="{{ route('cases.edit', $case->id) }}"
                                                    class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                    title="{{ __('Edit') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                    <i class="ti ti-edit "></i>
                                                </a>
                                            </div>
                                        @endcan

                                        @can('delete case')
                                            <div class="action-btn bg-light-secondary ms-2">
                                                <a href="#"
                                                    class="mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-confirm-yes="delete-form-{{ $case->id }}"
                                                    title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </div>
                                        @endcan
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['cases.destroy', $case->id],
                                            'id' => 'delete-form-' . $case->id,
                                        ]) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> --}}

        </div>
    </div>
@endsection

