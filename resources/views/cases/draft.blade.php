@extends('layouts.app')

@section('page-title', __('Drafts Cases'))

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
    <li class="breadcrumb-item">{{ __('Draft Cases') }}</li>
@endsection

@section('content')

    <div class="row p-0">
        <div class="col-xl-12">

            <div class="card-header card-body table-border-style">
                <h5></h5>
                <div class="table-responsive">
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                {{-- <th>{{ __('Court') }}</th> --}}
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
                                    {{-- <td>
                                        <a href="#" class="btn btn-sm"
                                            data-url="{{ route('cases.show', $case->id) }}" data-size="md"
                                            data-ajax-popup="true" data-title="{{ __('View Case') }}">
                                            {{ App\Models\CauseList::getCourtById($case->court) }} -
                                            {{ App\Models\CauseList::getHighCourtById($case->highcourt) == '-'
                                                ? $case->casenumber
                                                : App\Models\CauseList::getHighCourtById($case->highcourt) }}
                                            - {{ App\Models\CauseList::getBenchById($case->bench) }}
                                        </a> 
                                    </td> --}}
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
            </div>

        </div>
    </div>
@endsection

