@extends('layouts.app')

@section('page-title', __('Add Case'))


@section('breadcrumb')
    <li class="breadcrumb-item">{{ __(' Add Case') }}</li>
@endsection

@php
    $setting = App\Models\Utility::settings();
    
@endphp

@section('content')

    {{ Form::open(['route' => 'cases.store', 'method' => 'post', 'id' => 'caseForm', 'enctype' => 'multipart/form-data']) }}
    <div class="row">

        <div class="col-md-1"></div>
        <div class="col-lg-10">
            <div class="card shadow-none rounded-0">
                <div class="card-body">
                    <h2 class="py-3">Case Information</h2>
                    <input class="draft_fields" type="hidden" id="caseId" name="caseId">
                    <div class="dashed-border row">
                        

                            <div class="col-md-6">
                                <div class="form-group">

                                    {!! Form::label('Select Case', __('Assign to Team'), ['class' => 'form-label']) !!}
                                    {!! Form::select(
                                        'your_advocates[]',
                                        $allOptions,
                                        request()->isMethod('post')
                                            ? old('your_advocates')
                                            : $allOptions->except(
                                                    $allOptions->filter(function ($value, $key) {
                                                            return strpos($key, ',') !== false;
                                                        })->keys()->toArray(),
                                                )->keys()->toArray(),
                                        [
                                            'class' => 'form-control draft_fields',
                                            'id' => 'selectOptions',
                                            'multiple' => 'multiple',
                                            'data-role' => 'tagsinput',
                                        ],
                                    ) !!}
                                </div>


                            </div>
                                 {{-- {!! Form::select('your_team[]', array_merge(['create-client' => 'Add New Client'], $team->toArray()), request()->isMethod('post') ? old('your_team') : null, [
                                        'class' => 'form-control draft_fields choices-multiple-multi-select',
                                        'id' => 'choices-multiple multi-select',
                                        'multiple',
                                        'data-role' => 'tagsinput',
                                        'onchange' => 'handleOptionSelection(this)',
                                        'autocomplete' => 'off' 
                                    ]) !!} --}}
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('Select Case', __('Assign to Clients'), ['class' => 'form-label']) !!}
                                            <div class="input-group  custom_choose_class">
                                                {!! Form::select('your_team[]', ['create-client' => 'Add new Client'] + $team->toArray(), null, [
                                                    'class' => 'form-control draft_fields choices-multiple-multi-select',
                                                    'multiple' => 'multiple',
                                                    'id' => 'clientSelectOption',
                                                    'data-role' => 'tagsinput',
                                                    'autocomplete' => 'off'
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>                                 
                            <div class="col-md-12">
                                <div class="form-group d-none" id="casetype_div">
                                </div>
                            </div>
                            <input type="hidden" value="{{ $timeLine->id }}" name="timeLineId">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('name', __('Name*'), ['class' => 'col-form-label']) }}
                                    <input type="text" name="name"
                                        value="{{ old('name') }}"class="form-control draft_fields" required>
                                    <small> {{ __('(Please enter the name which you can remember easily)') }} </small>
                                    @if ($errors->has('name'))
                                        <p class="help-block" style="color: red">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12 " id="case_number_div">
                                <div class="form-group">
                                    {{ Form::label('case_number', __('Case Number*'), ['class' => 'col-form-label']) }}
                                    <input type="text" name="case_number"
                                        value="{{ old('case_number') }}"class="form-control draft_fields" required>
                                    <small>{{ __('(Please enter the case number assigned by court)') }}</small>
                                    @if ($errors->has('case_number'))
                                        <p class="help-block" style="color: red">
                                            <strong>{{ $errors->first('case_number') }}</strong>
                                        </p>
                                    @endif
                                </div>

                            </div>



                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('open_date', __('Date Opened*'), ['class' => 'col-form-label']) }}
                                    <div class="input-group justify-content-end">
                                        <input type="text" id="open_date" name="open_date" required
                                            placeholder="YYYY-MM-DD" autocomplete="off" class="form-control draft_fields"
                                            value="{{ old('open_date') }}">
                                        <span style="position: absolute; padding: 10px; cursor: pointer;"
                                            class="input-group-addon calendar_field">
                                            <i class="fa fa-calendar" id="calendar_icon_open_date"></i>
                                        </span>
                                    </div>

                                    @if ($errors->has('open_date'))
                                        <p class="help-block" style="color: red">
                                            <strong>{{ $errors->first('open_date') }}</strong>
                                        </p>
                                    @endif
                                </div>

                            </div>


                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('close_date', __('Date Closed'), ['class' => 'col-form-label']) }}
                                    <div class="input-group justify-content-end">
                                        <input type="text" id="close_date" name="close_date" autocomplete="off"
                                            placeholder="YYYY-MM-DD" class="form-control draft_fields"
                                            value="{{ old('close_date') }}">
                                        <span style="position: absolute;padding: 10px;cursor: pointer;"
                                            class="input-group-addon calendar_field">
                                            <i class="fa fa-calendar " id="calendar_icon_close_date"></i>
                                        </span>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('incident_date', __('Date of Incident'), ['class' => 'col-form-label']) }}
                                    <div class="input-group justify-content-end">
                                        {{ Form::text('incident_date', old('incident_date'), ['class' => 'form-control', 'id' => 'incident_date', 'autocomplete' => 'off', 'placeholder' => 'YYYY-MM-DD', 'required' => true]) }}
                                        <span style="position: absolute;padding: 10px;cursor: pointer;"
                                            class="input-group-addon calendar_field draft_fields">
                                            <i class="fa fa-calendar " id="calendar_icon_incident_date"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('statute_of_limitations', __('Statute of Limitations'), ['class' => 'col-form-label']) }}
                                    {{ Form::select(
                                        'statute_of_limitations',
                                        [
                                            '1 Year' => '1 Year',
                                            '2 Years' => '2 Years',
                                            '3 Years' => '3 Years',
                                            '4 Years' => '4 Years',
                                            '5 Years' => '5 Years',
                                            '6 Years' => '6 Years',
                                            '7 Years' => '7 Years',
                                            '8 Years' => '8 Years',
                                            '9 Years' => '9 Years',
                                            '10 Years' => '10 Years',
                                        ],
                                        old('statute_of_limitations'),
                                        ['class' => 'form-control draft_fields  statute_of_limitations select2', 'required' => true],
                                    ) }}
                                </div>
                            </div>




                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('case_stage', __('Case Stage'), ['class' => 'col-form-label']) }}
                                    <select name="case_stage" id="case_stage" class="form-control draft_fields">
                                        <option value=""> {{ __('Please Select') }} </option>
                                        <option value="Investigation"
                                            {{ old('case_stage') === 'Investigation' ? 'selected' : '' }}>Investigation
                                        </option>
                                        <option value="Ready For Demand"
                                            {{ old('case_stage') === 'Ready For Demand' ? 'selected' : '' }}>Ready For
                                            Demand
                                        </option>
                                        <option value="Demand Sent"
                                            {{ old('case_stage') === 'Demand Sent' ? 'selected' : '' }}>Demand Sent
                                        </option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('practice_area', __('Practice Area'), ['class' => 'col-form-label']) }}
                                    <select name="practice_area" id="practice_area" class="form-control draft_fields"
                                        required>
                                        <option value=""> {{ __('Please Select') }} </option>
                                        <option value="Personal Injury"
                                            {{ old('practice_area') === 'Personal Injury' ? 'selected' : '' }}>Personal
                                            Injury
                                        </option>
                                        <option value="Labor" {{ old('practice_area') === 'Labor' ? 'selected' : '' }}>
                                            Labor
                                        </option>
                                        <option value="Motor Vehicle Accident"
                                            {{ old('injury_type') === 'Motor Vehicle Accident' ? 'selected' : '' }}>
                                            Motor Vehicle Accident</option>
                                        <option value="Slip & Fall"
                                            {{ old('injury_type') === 'Slip & Fall' ? 'selected' : '' }}>
                                            Slip & Fall</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
                                    <small> {{ __('(Please enter primary details about the case, client, etc)') }} </small>
                                    {{ Form::textarea('description', old('description'), ['class' => 'form-control draft_fields pc-tinymce-2', 'rows' => 1, 'placeholder' => __('Description'), 'id' => 'description']) }}
                                </div>
                            </div>
                        </div>
                   
                    {{-- slip fall from --}}

                    <div class="slip_fall_from row">

                        {{-- client information --}}
                       
                        <div class="row mt-3">

                            {{-- client info --}}

                            <div class="col-md-6">
                                <h4 class="py-3 text-primary-{{ $setting['color'] }}">Client Information</h4>
                                <div class="dashed-border-extra row" style="margin-right: 1px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_name', __('Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_name', old('fall_c_name'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>
                                    <div class="row">


                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('fall_c_gender', __('Gender:'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('fall_c_gender', old('fall_c_gender'), ['class' => 'form-control draft_fields']) }}

                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('fall_c_marital_status', __('Marital Status:'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('fall_c_marital_status', old('fall_c_marital_status'), ['class' => 'form-control draft_fields']) }}

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_spouse_name', __('Spouse Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_spouse_name', old('fall_c_spouse_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_emergency_contact_name', __('Emergency Contact Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_emergency_contact_name', old('fall_c_emergency_contact_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_emergency_contact_number', __('Emergency Contact Number:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_emergency_contact_number', old('fall_c_emergency_contact_number'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_dob', __('Date of Birth:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_dob', old('fall_c_dob'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_social_security', __('Social Security:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_social_security', old('fall_c_social_security'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_address', __('Address:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_address', old('fall_c_address'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_phone', __('Phone:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_phone', old('fall_c_phone'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_email', __('Email:'), ['class' => 'col-form-label']) }}
                                            {{ Form::email('fall_c_email', old('fall_c_email'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_driver_license', __('Driver License #:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_driver_license', old('fall_c_driver_license'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_health_insurance', __('Health Insurance, Medicare / Medicaid / Tricare?'), ['class' => 'bold col-form-label']) }}

                                            <div class="form-check">
                                                <input value="Yes"
                                                    {{ old('fall_c_health_insurance') === 'Yes' ? 'checked' : '' }}
                                                    class="form-check-input" type="radio"
                                                    name="fall_c_health_insurance" id="fc_health_insurance_yes"
                                                    onclick="fallHealthInsurance(true)">
                                                <label class="form-check-label" for="fc_health_insurance_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input value="No" class="form-check-input"
                                                    {{ old('fall_c_health_insurance') === 'No' ? 'checked' : '' }}
                                                    type="radio" name="fall_c_health_insurance"
                                                    id="fc_health_insurance_no" onclick="fallHealthInsurance(false)">
                                                <label class="form-check-label" for="fc_health_insurance_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12" id="fallHealthInsurance_div">
                                        <div class="form-group">
                                            {{ Form::label('fall_c_id', __('ID:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_c_id', old('fall_c_id'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- 3rd party info --}}

                            <div class="col-md-6">

                                <h4 class="py-3 text-primary-{{ $setting['color'] }}">Third Party Information</h4>
                                <div class="dashed-border-extra row" style="margin-left: 1px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_tpi_name', __('Entity Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_tpi_name', old('fall_tpi_name'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_tpi_phone', __('Phone:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_tpi_phone', old('fall_tpi_phone'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_tpi_address', __('Address:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_tpi_address', old('fall_tpi_address'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_tpi_email', __('Email:'), ['class' => 'col-form-label']) }}
                                            {{ Form::email('fall_tpi_email', old('fall_tpi_email'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>


                                    {{-- Management --}}
                                    
                                    <div class="col-md-12 col-sm-12 col-xs-12 hide-mobile-div">
                                        <div class="form-group">
                                            <h5 style="margin-top: 19%;"
                                                class="py-3 text-primary-{{ $setting['color'] }}">
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <h5 style=""
                                                class="py-3 text-primary-{{ $setting['color'] }}">Management Information
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_mi_name', __('Entity Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_mi_name', old('fall_mi_name'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_mi_address', __('Address:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_mi_address', old('fall_mi_address'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_mi_phone', __('Phone:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_mi_phone', old('fall_mi_phone'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_mi_email', __('Email:'), ['class' => 'col-form-label']) }}
                                            {{ Form::email('fall_mi_email', old('fall_mi_email'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>

                        {{-- Incident Information --}}

                      
                        <div class="mt-3">
                            <h4 class="py-3 text-primary-{{ $setting['color'] }}">Incident Information</h4>
                            <div class="dashed-border row">
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fall_ii_incident_date', __('Date of Incident:'), ['class' => 'col-form-label']) }}
                                        <div class="input-group justify-content-end">
                                            {{ Form::text('fall_ii_incident_date', old('fall_ii_incident_date'), ['class' => 'form-control', 'id' => 'fii_incident_date', 'autocomplete' => 'off', 'placeholder' => 'YYYY-MM-DD']) }}
                                            <span style="position: absolute;padding: 10px;cursor: pointer;"
                                                class="input-group-addon calendar_field draft_fields">
                                                <i class="fa fa-calendar " id="calendar_icon_fii_incident_date"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fall_ii_time', __('Time:'), ['class' => 'col-form-label']) }}
                                        {{ Form::time('fall_ii_time', old('fall_ii_time'), ['class' => 'draft_fields form-control']) }}

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fall_ii_location_of_incident', __('Location of Incident:'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('fall_ii_location_of_incident', old('fall_ii_location_of_incident'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fall_ii_address', __('Address:'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('fall_ii_address', old('fall_ii_address'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fall_ii_maps_link', __('Maps Link/Coordinates:'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('fall_ii_maps_link', old('fall_ii_maps_link'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fall_ii_cause_incident', __('Cause of the Incident:'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('fall_ii_cause_incident', old('fall_ii_cause_incident'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fc_police_notified', __('Police Notified?'), ['class' => 'bold col-form-label']) }}

                                        <div class="form-check">
                                            <input value="Yes"
                                                {{ old('fc_police_notified') === 'Yes' ? 'checked' : '' }}
                                                class="form-check-input" type="radio" name="fc_police_notified"
                                                id="fc_police_notified_yes" onclick="fallPOliceNotified(true)">
                                            <label class="form-check-label" for="fc_police_notified_yes">
                                                Yes
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input value="No" class="form-check-input"
                                                {{ old('fc_police_notified') === 'No' ? 'checked' : '' }} type="radio"
                                                name="fc_police_notified" id="fc_police_notified_no"
                                                onclick="fallPOliceNotified(false)">
                                            <label class="form-check-label" for="fc_police_notified_no">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fc_incident_report_filed', __('Incident Report Filed?'), ['class' => 'bold col-form-label']) }}

                                        <div class="form-check">
                                            <input value="Yes"
                                                {{ old('fc_incident_report_filed') === 'Yes' ? 'checked' : '' }}
                                                class="form-check-input" type="radio" name="fc_incident_report_filed"
                                                id="fc_incident_report_filed_yes" onclick="fallReportField(true)">
                                            <label class="form-check-label" for="fc_incident_report_filed_yes">
                                                Yes
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input value="No" class="form-check-input"
                                                {{ old('fc_incident_report_filed') === 'No' ? 'checked' : '' }}
                                                type="radio" name="fc_incident_report_filed"
                                                id="fc_incident_report_filed_no" onclick="fallReportField(false)">
                                            <label class="form-check-label" for="fc_incident_report_filed_no">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group" id="fallPOliceNotified_div">
                                        {{ Form::label('fc_police_department', __('Police Department:'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('fc_police_department', old('fc_police_department'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group" id="fallReportField_div">
                                        {{ Form::label('fc_incident_report', __('Incident Report #:'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('fc_incident_report', old('fc_incident_report'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Insurance --}}


                     

                        <div class="row mt-3">

                            {{-- Insurance (First Party) --}}

                            <div class="col-md-6">
                                <h4 class="py-3 text-primary-{{ $setting['color'] }}">Insurance (First Party)</h4>
                                <div class="dashed-border-extra row" style="margin-right: 1px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_company_name', __('Company Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_ifp_company_name', old('fall_ifp_company_name'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_insured_name', __('Insured Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_ifp_insured_name', old('fall_ifp_insured_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('fall_ifp_poilicy', __('Group/Policy #:'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('fall_ifp_poilicy', old('fall_ifp_poilicy'), ['class' => 'form-control draft_fields']) }}

                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('fall_ifp_member', __('Member:'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('fall_ifp_member', old('fall_ifp_member'), ['class' => 'form-control draft_fields']) }}

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_claim', __('Claim #:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_ifp_claim', old('fall_ifp_claim'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_insurance_phone', __('Insurance Phone:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_ifp_insurance_phone', old('fall_ifp_insurance_phone'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <h5 style="margin-top: 9%;"
                                                class="py-3 text-primary-{{ $setting['color'] }}">Adjuster</h5>
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_adjuster_name', __('Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_ifp_adjuster_name', old('fall_ifp_adjuster_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_adjuster_email', __('Email:'), ['class' => 'col-form-label']) }}
                                            {{ Form::email('fall_ifp_adjuster_email', old('fall_ifp_adjuster_email'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_adjuster_phone', __('Phone:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_ifp_adjuster_phone', old('fall_ifp_adjuster_phone'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_adjuster_fax', __('Fax Number:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_ifp_adjuster_fax', old('fall_ifp_adjuster_fax'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_ifp_adjuster_policy_limits', __('Policy Limits:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_ifp_adjuster_policy_limits', old('fall_ifp_adjuster_policy_limits'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Insurance (third Party) --}}

                            <div class="col-md-6">
                                <h4 class="py-3 text-primary-{{ $setting['color'] }}">Insurance (Third Party)</h4>
                                <div class="dashed-border-extra row" style="margin-left: 1px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_company_name', __('Company Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_company_name', old('fall_itp_company_name'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_insured_name', __('Insured Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_insured_name', old('fall_itp_insured_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_poilicy', __('Group:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_poilicy', old('fall_itp_poilicy'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>



                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_claim', __('Claim #:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_claim', old('fall_itp_claim'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_insurance_phone', __('Insurance Phone:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_insurance_phone', old('fall_itp_insurance_phone'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <h5 style="margin-top: 9%;"
                                                class="py-3 text-primary-{{ $setting['color'] }}">Adjuster</h5>
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_adjuster_name', __('Name:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_adjuster_name', old('fall_itp_adjuster_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_adjuster_email', __('Email:'), ['class' => 'col-form-label']) }}
                                            {{ Form::email('fall_itp_adjuster_email', old('fall_itp_adjuster_email'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_adjuster_phone', __('Phone:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_adjuster_phone', old('fall_itp_adjuster_phone'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_adjuster_fax', __('Fax Number:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_adjuster_fax', old('fall_itp_adjuster_fax'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_itp_adjuster_policy_limits', __('Policy Limits:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_itp_adjuster_policy_limits', old('fall_itp_adjuster_policy_limits'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        {{-- witness --}}

                   

                        <div class="row">
                            <h4 class="py-3 text-primary-{{ $setting['color'] }}">Witness Information</h4>
                            <div class="col-md-12 repeater">
                                <div class=" dashed-border row" style="height: 100%">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card my-3 shadow-none rounded-0 border">
                                                <div class="card-header">
                                                    <div class="row flex-grow-1">
                                                        <div class="col-md d-flex align-items-center col-6">
                                                            <h5
                                                                class="card-header-title text-primary-{{ $setting['color'] }}">
                                                                {{ __('Witness') }}</h5>
                                                        </div>

                                                        <div
                                                            class="col-md-6 justify-content-between align-items-center col-6">
                                                            <div
                                                                class="col-md-12 d-flex align-items-center justify-content-end">
                                                                <a data-repeater-create=""
                                                                    class="btn btn-primary btn-sm add-row-witness text-white"
                                                                    data-toggle="modal">
                                                                    <i class="fas fa-plus"></i></a><span style="margin-left: 10px;"> {{ __('  Add another witness') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body" id="repeater-container-witness">
                                                    <div class="row mt-3 repeater-row-witness">
                                                        <div class="col-md-5 offset-md-1">
                                                            <label for="witness_name">{{ __('Full Name') }}</label>
                                                            <input type="text" class="form-control"
                                                                name="witness[witness_name][]">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label for="witness_email">{{ __('Email Address') }}</label>
                                                            <input type="text" class="form-control"
                                                                name="witness[witness_email][]">
                                                        </div>
                                                        <div class="col-md-1 text-center m-auto">
                                                            <a href="javascript:;"
                                                                class="btn btn-danger btn-sm delete-row-witness">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-5 offset-md-1">
                                                            <label for="witness_phone">{{ __('Phone Number') }}</label>
                                                            <input type="text" class="form-control"
                                                                name="witness[witness_phone][]">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label for="witness_address">{{ __('Address') }}</label>
                                                            <input type="text" class="form-control"
                                                                name="witness[witness_address][]">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- other spill and fall --}}
                       
                        <div class="mt-3">
                            <h4 class="py-3 text-primary-{{ $setting['color'] }}">Other</h4>
                            <div class="dashed-border row">
                                <div class="col-md-6 col-sm-12 col-xs-12 ">
                                    <div class="form-group">
                                        {{ Form::label('fall_o_incident_report', __('Accident or incident Report:'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('fall_o_incident_report', old('fall_o_incident_report'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>
                                <div class="col-md-6"></div>


                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('fall_o_recorded_statements', __('Have you spoken or recorded Statements with any insurance company??'), ['class' => 'bold col-form-label']) }}

                                        <div class="form-check">
                                            <input value="Yes"
                                                {{ old('fall_o_recorded_statements') === 'Yes' ? 'checked' : '' }}
                                                class="form-check-input" type="radio" name="fall_o_recorded_statements"
                                                id="fall_o_recorded_statements_yes" onclick="fallOtherToggle(true)">
                                            <label class="form-check-label" for="fall_o_recorded_statements_yes">
                                                Yes
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input value="No" class="form-check-input"
                                                {{ old('fall_o_recorded_statements') === 'No' ? 'checked' : '' }}
                                                type="radio" name="fall_o_recorded_statements"
                                                id="fall_o_recorded_statements_no" onclick="fallOtherToggle(false)">
                                            <label class="form-check-label" for="fall_o_recorded_statements_no">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>

                                <div class="row" id="fall_other_yes">
                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_o_opponent_counsel', __('Opponent Counsel:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_o_opponent_counsel', old('fall_o_opponent_counsel'), ['class' => 'draft_fields form-control']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_o_name', __('Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_o_name', old('fall_o_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_o_phone', __('Phone:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_o_phone', old('fall_o_phone'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_o_email', __('Email:'), ['class' => 'col-form-label']) }}
                                            {{ Form::email('fall_o_email', old('fall_o_email'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('fall_o_fax', __('Fax:'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('fall_o_fax', old('fall_o_fax'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                    </div>

                    <div class="hide_div">


                      


                        <div class="mt-3">
                            <h4 class="py-3 text-primary-{{ $setting['color'] }}">General</h4>
                            <div class="dashed-border row">
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('location_of_accident', __('Location of Accident'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('location_of_accident', old('location_of_accident'), ['class' => 'draft_fields form-control']) }}

                                    </div>
                                </div>




                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('intersection', __('Intersection'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('intersection', old('intersection'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('coordinates', __('Map Link/Coordinates'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('coordinates', old('coordinates'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="mt-3">
                            <h4 class="py-3 text-primary-{{ $setting['color'] }}">Insurance</h4>

                            <div class="dashed-border row" >
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('case_manager', __('Case Manager'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('case_manager', old('case_manager'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('file_location', __('File Location'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('file_location', old('file_location'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- compnay details --}}
                      
                        <div class="row mt-3">

                            <div class="col-md-6">
                                <h4 class="py-3 text-primary-{{ $setting['color'] }}">First Party</h4>
                                <div class="dashed-border-extra row" style="margin-right: 1px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_company_name', __('Company Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_company_name', old('first_party_company_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_policy_name', __('Policy Number'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_policy_name', old('first_party_policy_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_insurance_phone_number', __('Insurance Phone Number'), ['class' => 'col-form-label']) }}
                                            <input value="{{ old('first_party_insurance_phone_number') }}"
                                                id="first_party_insurance_phone_number" type="text"
                                                name="first_party_insurance_phone_number"
                                                class="form-control draft_fields">
                                            <small>{{ __('(Enter valid phone number  i.e, 10-13 digits)') }}</small>
                                            @if ($errors->has('first_party_insurance_phone_number'))
                                                <p class="help-block" style="color: red">
                                                    <strong>{{ $errors->first('first_party_insurance_phone_number') }}</strong>
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_name', __('Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_name', old('first_party_name'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_phone_number', __('Phone Number'), ['class' => 'col-form-label']) }}
                                            <input value="{{ old('first_party_phone_number') }}"
                                                id="first_party_phone_number" type="text"
                                                name="first_party_phone_number" class="form-control draft_fields">
                                            <small>{{ __('(Enter valid phone number  i.e, 10-13 digits)') }}</small>
                                            @if ($errors->has('first_party_phone_number'))
                                                <p class="help-block" style="color: red">
                                                    <strong>{{ $errors->first('first_party_phone_number') }}</strong>
                                                </p>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_policy_limits', __('Policy Limits'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_policy_limits', old('first_party_policy_limits'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_insured_name', __('Insured Name(s)'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_insured_name', old('first_insured_name'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_claim_number', __('Claim Number'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_claim_number', old('first_party_claim_number'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_adjuster', __('Adjuster'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_adjuster', old('first_party_adjuster'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_email', __('Email'), ['class' => 'col-form-label']) }}
                                            <input type="email" name="first_party_email"
                                                value="{{ old('first_party_email') }}"class="form-control draft_fields">


                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_fax', __('Fax Number'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_fax', old('first_party_fax'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="py-3 text-primary-{{ $setting['color'] }}">Third Party</h4>
                                <div class="dashed-border-extra row" style="margin-left: 1px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_company_name', __('Company Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_company_name', old('third_party_company_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_policy_name', __('Policy Number'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_policy_name', old('third_party_policy_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_insurance_phone_number', __('Insurance Phone Number'), ['class' => 'col-form-label']) }}
                                            <input value="{{ old('third_party_insurance_phone_number') }}"
                                                id="third_party_insurance_phone_number" type="text"
                                                name="third_party_insurance_phone_number"
                                                class="form-control draft_fields">
                                            <small>{{ __('(Enter valid phone number  i.e, 10-13 digits)') }}</small>
                                            @if ($errors->has('third_party_insurance_phone_number'))
                                                <p class="help-block" style="color: red">
                                                    <strong>{{ $errors->first('third_party_insurance_phone_number') }}</strong>
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_name', __('Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_name', old('third_party_name'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_phone_number', __('Phone Number'), ['class' => 'col-form-label']) }}
                                            <input value="{{ old('third_party_phone_number') }}"
                                                id="third_party_phone_number" type="text"
                                                name="third_party_phone_number" class="form-control draft_fields">
                                            <small>{{ __('(Enter valid phone number  i.e, 10-13 digits)') }}</small>
                                            @if ($errors->has('third_party_phone_number'))
                                                <p class="help-block" style="color: red">
                                                    <strong>{{ $errors->first('third_party_phone_number') }}</strong>
                                                </p>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_policy_limits', __('Policy Limits'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_policy_limits', old('third_party_policy_limits'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_insured_name', __('Insured Name(s)'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_insured_name', old('third_insured_name'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_claim_number', __('Claim Number'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_claim_number', old('third_party_claim_number'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_adjuster', __('Adjuster'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_adjuster', old('third_party_adjuster'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_email', __('Email'), ['class' => 'col-form-label']) }}
                                            <input type="email" name="third_party_email"
                                                value="{{ old('third_party_email') }}"class="form-control draft_fields">


                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_fax', __('Fax Number'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_fax', old('third_party_fax'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        {{-- vehicle information --}}
                       
                        <div class="row mt-3">

                            <div class="col-md-6">
                                <h4 class="py-3 text-primary-{{ $setting['color'] }}">Vehicle Information (First
                                    Party)</h4>
                                <div class="dashed-border-extra row" style="margin-right: 1px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_driver_name', __('Driver Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_driver_name', old('first_party_driver_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_vehicle_year', __('Vehicle Year'), ['class' => 'col-form-label']) }}
                                            <input type="number" class="form-control draft_fields" placeholder="2023"
                                                name="first_party_vehicle_year">

                                            {{-- <select class="form-control draft_fields" name="first_party_vehicle_year" id="year">
                                                <option value="{{ old('year') }}" selected>{{ __('Please Select') }}
                                                </option>
                                                @foreach (App\Models\Utility::getYears() as $year)
                                                    <option value="{{ $year }}">
                                                        {{ $year }}</option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_vehicle_model', __('Vehicle Model'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_vehicle_model', old('first_party_vehicle_model'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_vehicle_make', __('Vehicle Make'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_vehicle_make', old('first_party_vehicle_make'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_vehicle_license', __('Vehicle License Plate Number'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_vehicle_license', old('first_party_vehicle_license'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('first_party_passenger_name', __('Passenger Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('first_party_passenger_name', old('first_party_passenger_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('Are_you_driver_or_Passenger', __('Are you driver or Passenger?'), ['class' => 'bold col-form-label']) }}

                                            <div class="form-check">
                                                <input value="Driver"
                                                    {{ old('first_party_customer_type') === 'Driver' ? 'checked' : '' }}
                                                    class="form-check-input" type="radio"
                                                    name="first_party_customer_type"
                                                    id="_first_Are_you_driver_or_Passenger_yes">
                                                <label class="form-check-label"
                                                    for="_first_Are_you_driver_or_Passenger_yes">
                                                    Driver
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input value="Passenger" class="form-check-input"
                                                    {{ old('first_party_customer_type') === 'Passenger' ? 'checked' : '' }}
                                                    type="radio" name="first_party_customer_type"
                                                    id="first_Are_you_driver_or_Passenger_no">
                                                <label class="form-check-label"
                                                    for="first_Are_you_driver_or_Passenger_no">
                                                    Passenger
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('Airbags_Deployed', __('Airbags Deployed?'), ['class' => 'bold col-form-label']) }}

                                            <div class="form-check">
                                                <input value="Yes"
                                                    {{ old('first_party_airbags_developed') === 'Yes' ? 'checked' : '' }}
                                                    class="form-check-input" type="radio"
                                                    name="first_party_airbags_developed" id="first_Airbags_Deployed_yes">
                                                <label class="form-check-label" for="first_Airbags_Deployed_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input value="No" class="form-check-input"
                                                    {{ old('first_party_airbags_developed') === 'No' ? 'checked' : '' }}
                                                    type="radio" name="first_party_airbags_developed"
                                                    id="first_Airbags_Deployed_no">
                                                <label class="form-check-label" for="first_Airbags_Deployed_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('Seat_belts_worn', __('Seat belts worn?'), ['class' => 'bold col-form-label']) }}

                                            <div class="form-check">
                                                <input value="Yes" class="form-check-input"
                                                    {{ old('first_party_seat_belts_worn') === 'Yes' ? 'checked' : '' }}
                                                    type="radio" name="first_party_seat_belts_worn"
                                                    id="first_seat_belts_worn_yes">
                                                <label class="form-check-label" for="first_seat_belts_worn_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input value="No" class="form-check-input"
                                                    {{ old('first_party_seat_belts_worn') === 'No' ? 'checked' : '' }}
                                                    type="radio" name="first_party_seat_belts_worn"
                                                    id="first_seat_belts_worn_no">
                                                <label class="form-check-label" for="first_seat_belts_worn_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="py-4 text-primary-{{ $setting['color'] }}">Emergency Contact</h5>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('emergency_name', __('Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('emergency_name', old('emergency_name'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('emergency_phone', __('Phone Number'), ['class' => 'col-form-label']) }}
                                            <input type="text" value="{{ old('emergency_phone') }}"
                                                name="emergency_phone" class="form-control draft_fields">
                                            <small>{{ __('(Enter valid phone number  i.e, 10-13 digits)') }}</small>
                                            @if ($errors->has('emergency_phone'))
                                                <p class="help-block" style="color: red">
                                                    <strong>{{ $errors->first('emergency_phone') }}</strong>
                                                </p>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h4 class="py-3 text-primary-{{ $setting['color'] }}">Vehicle Information (Third
                                    Party)</h4>
                                <div class="dashed-border-extra row" style="margin-left: 1px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_driver_name', __('Driver Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_driver_name', old('third_party_driver_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_vehicle_year', __('Vehicle Year'), ['class' => 'col-form-label']) }}
                                            <input type="number" class="form-control draft_fields" placeholder="2023"
                                                name="third_party_vehicle_year">

                                            {{-- <select class="form-control draft_fields" name="third_party_vehicle_year" id="year">
                                                <option value="{{ old('year') }}" selected>{{ __('Please Select') }}
                                                </option>
                                                @foreach (App\Models\Utility::getYears() as $year)
                                                    <option value="{{ $year }}">
                                                        {{ $year }}</option>
                                                @endforeach
                                            </select> --}}
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_vehicle_model', __('Vehicle Model'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_vehicle_model', old('third_party_vehicle_model'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_vehicle_make', __('Vehicle Make'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_vehicle_make', old('third_party_vehicle_make'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_vehicle_license', __('Vehicle License Plate Number'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_vehicle_license', old('third_party_vehicle_license'), ['class' => 'form-control draft_fields']) }}
                                        </div>
                                    </div>



                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('third_party_passenger_name', __('Passenger Name'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('third_party_passenger_name', old('third_party_passenger_name'), ['class' => 'form-control draft_fields']) }}

                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('Are_you_driver_or_Passenger', __('Are you driver or Passenger?'), ['class' => 'bold col-form-label']) }}

                                            <div class="form-check">
                                                <input value="Driver"
                                                    {{ old('third_party_customer_type') === 'Driver' ? 'checked' : '' }}
                                                    class="form-check-input" type="radio"
                                                    name="third_party_customer_type"
                                                    id="third_Are_you_driver_or_Passenger_yes">
                                                <label class="form-check-label"
                                                    for="third_Are_you_driver_or_Passenger_yes">
                                                    Driver
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input value="Passenger"
                                                    {{ old('third_party_customer_type') === 'Passenger' ? 'checked' : '' }}
                                                    class="form-check-input" type="radio"
                                                    name="third_party_customer_type"
                                                    id="third_Are_you_driver_or_Passenger_no">
                                                <label class="form-check-label"
                                                    for="third_Are_you_driver_or_Passenger_no">
                                                    Passenger
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('Airbags_Deployed', __('Airbags Deployed?'), ['class' => 'bold col-form-label']) }}

                                            <div class="form-check">
                                                <input value="Yes" class="form-check-input"
                                                    {{ old('third_party_airbags_developed') === 'Yes' ? 'checked' : '' }}
                                                    type="radio" name="third_party_airbags_developed"
                                                    id="third_Airbags_Deployed_yes">
                                                <label class="form-check-label" for="third_Airbags_Deployed_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input value="No" class="form-check-input"
                                                    {{ old('third_party_airbags_developed') === 'No' ? 'checked' : '' }}
                                                    type="radio" name="third_party_airbags_developed"
                                                    id="third_Airbags_Deployed_no">
                                                <label class="form-check-label" for="third_Airbags_Deployed_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            {{ Form::label('Seat_belts_worn', __('Seat belts worn?'), ['class' => 'bold col-form-label']) }}

                                            <div class="form-check">
                                                <input value="Yes" class="form-check-input"
                                                    {{ old('third_party_seat_belts_worn') === 'Yes' ? 'checked' : '' }}
                                                    type="radio" name="third_party_seat_belts_worn"
                                                    id="seat_belts_worn_yes">
                                                <label class="form-check-label" for="seat_belts_worn_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input value="No" class="form-check-input"
                                                    {{ old('third_party_seat_belts_worn') === 'No' ? 'checked' : '' }}
                                                    type="radio" name="third_party_seat_belts_worn"
                                                    id="seat_belts_worn_no">
                                                <label class="form-check-label" for="seat_belts_worn_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- other --}}
                     
                        <div class="mt-3">
                            <h4 class="py-3 text-primary-{{ $setting['color'] }}">Other</h4>
                            <div class="dashed-border row">
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('police_report', __('Police Report Number'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('police_report', old('police_report'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-xs-12"></div>
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('insurance_company_phone_2', __('Have you spoken or recorded Statements with any insurance company?'), ['class' => 'bold col-form-label']) }}

                                        <div class="form-check">
                                            <input value="Yes" class="form-check-input" type="radio"
                                                {{ old('recorded_statement') === 'Yes' ? 'checked' : '' }}
                                                name="recorded_statement" id="recorded_statement_yes"
                                                onclick="toggleDiv(true)">
                                            <label class="form-check-label" for="recorded_statement_yes">
                                                Yes
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input value="No" class="form-check-input" type="radio"
                                                {{ old('recorded_statement') === 'No' ? 'checked' : '' }}
                                                name="recorded_statement" id="recorded_statement_no"
                                                onclick="toggleDiv(false)">
                                            <label class="form-check-label" for="recorded_statement_no">
                                                No
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12 ">
                                    <div class="form-group col-md-12" id="record_des">
                                        {!! Form::label('recorded_statement_description', __('Recorded Statement Description'), [
                                            'class' => 'form-label',
                                        ]) !!}
                                        {!! Form::textarea('recorded_statement_description', old('recorded_statement_description'), [
                                            'rows' => 4,
                                            'class' => 'form-control draft_fields',
                                        ]) !!}
                                    </div>
                                </div>


                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('other_name', __('Name'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('other_name', old('other_name'), ['class' => 'form-control draft_fields']) }}
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('other_phone_number', __('Phone Number'), ['class' => 'col-form-label']) }}
                                        <input value="{{ old('other_phone_number') }}" id="other_phone_number"
                                            type="text" name="other_phone_number" class="form-control draft_fields">
                                        <small>{{ __('(Enter valid phone number  i.e, 10-13 digits)') }}</small>
                                        @if ($errors->has('other_phone_number'))
                                            <p class="help-block" style="color: red">
                                                <strong>{{ $errors->first('other_phone_number') }}</strong>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('other_email_address', __('Email Address'), ['class' => 'col-form-label']) }}
                                        <input type="email" name="other_email_address"
                                            value="{{ old('other_email_address') }}" class="form-control draft_fields">

                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        {{ Form::label('other_fax', __('Fax'), ['class' => 'col-form-label']) }}
                                        {{ Form::text('other_fax', old('other_fax'), ['class' => 'form-control draft_fields']) }}

                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        <div class="dashed-border mt-3 row">
                         <div class="col-md-12 repeater">
                            
                            <div class="row">
                                <div class="col-12">
                                   

                                    <div class="card my-3 shadow-none rounded-0 border">
                                        <div class="card-header">
                                            <div class="row flex-grow-1">
                                                <div class="col-md d-flex align-items-center col-6">
                                                    <h5 class="card-header-title text-primary-{{ $setting['color'] }}">
                                                        {{ __('3rd Party') }}</h5>
                                                </div>

                                                <div class="col-md-6 justify-content-between align-items-center col-6">
                                                    <div class="col-md-12 d-flex align-items-center  justify-content-end">
                                                        <a data-repeater-create=""
                                                            class="btn btn-primary btn-sm add-row text-white"
                                                            data-toggle="modal">
                                                            <i class="fas fa-plus"></i> {{ __('Add Row') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table  mb-0 table-custom-style"
                                                    data-repeater-list="opponents" id="sortable-table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Full Name') }}</th>
                                                            <th>{{ __('Email Address') }}</th>
                                                            <th>{{ __('Phone Number') }}</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody class="ui-sortable" data-repeater-item>
                                                        <tr>
                                                            <td width="25%" class="form-group">
                                                                <input type="text"
                                                                    class="form-control draft_fields opponents_name"
                                                                    name="opponents[opponents_name][]">
                                                            </td>
                                                            <td width="25%">
                                                                <input type="text"
                                                                    class="form-control draft_fields opponents_email"
                                                                    name="opponents[opponents_email][]">
                                                            </td>
                                                            <td width="25%" style="padding-top: 30px;">

                                                                <input type="text" name="opponents[opponents_phone][]"
                                                                    class="form-control draft_fields opponents_phone">
                                                                <small>{{ __('(Enter valid phone number  i.e, 10-13 digits)') }}</small>
                                                            </td>
                                                            <td width="5%">
                                                                <a href="javascript:;"
                                                                    class="ti ti-trash text-white action-btn bg-danger btn_danger_color p-3 desc_delete"
                                                                    data-repeater-delete data-toggle="tooltip"
                                                                    data-placement="bottom"
                                                                    title="Cannot delete the last row!"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    
                        <div class="dashed-border mt-3 row">
                        <div class="col-md-12 repeater">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card shadow-none rounded-0 border my-3">
                                        <div class="card-header">
                                            <div class="row flex-grow-1">
                                                <div class="col-md d-flex align-items-center col-6">
                                                    <h5 class="card-header-title text-primary-{{ $setting['color'] }}">
                                                        {{ __('3rd Party Attorneys') }}</h5>
                                                </div>

                                                <div class="col-md-6 justify-content-between align-items-center col-6">
                                                    <div class="col-md-12 d-flex align-items-center  justify-content-end">
                                                        <a data-repeater-create=""
                                                            class="btn btn-primary btn-sm add-row text-white"
                                                            data-toggle="modal" data-target="#add-bank">
                                                            <i class="fas fa-plus"></i> {{ __('Add Row') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style">
                                            <div class="table-responsive">
                                                <table class="table  mb-0 table-custom-style"
                                                    data-repeater-list="opponent_advocates" id="sortable-table">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Full Name') }}</th>
                                                            <th>{{ __('Email Address') }}</th>
                                                            <th>{{ __('Phone Number') }}</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody class="ui-sortable" data-repeater-item>
                                                        <tr>
                                                            <td width="25%" class="form-group">
                                                                <input type="text"
                                                                    class="form-control draft_fields opp_advocates_name"
                                                                    name="opponent_advocates[opp_advocates_name][]">
                                                            </td>
                                                            <td width="25%">
                                                                <input type="text"
                                                                    class="form-control draft_fields opp_advocates_email"
                                                                    name="opponent_advocates[opp_advocates_email][]">
                                                            </td>
                                                            <td width="25%" style="padding-top: 30px;">

                                                                <input type="text"
                                                                    name="opponent_advocates[opp_advocates_phone][]"
                                                                    class="form-control draft_fields opp_advocates_phone">
                                                                <small>{{ __('(Enter valid phone number  i.e, 10-13 digits)') }}</small>
                                                            </td>
                                                            <td width="5%">
                                                                <a href="javascript:;"
                                                                    class="ti ti-trash text-white action-btn bg-danger btn_danger_color p-3 desc_delete"
                                                                    data-repeater-delete data-toggle="tooltip"
                                                                    data-placement="bottom"
                                                                    title="Cannot delete the last row!"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        </div>

                    </div>

                   
                    <div class="dashed-border row mt-3">
                  
                        <div class="col-md-12">

                            <div class="row" id="folderTemplate">

                            </div>
                            <div class="row">
                                <div id="folderContainer" class="col-md-12">
                                    <a class="btn mt-4 mb-3 btn-primary justify-content-end btn-sm text-white"
                                        id="createFolderButton"><i class="fas fa-plus"></i> Create Folder</a>
                                </div>
                            </div>
                        </div>
                  
                    </div>

                 

                    <div class=" row mt-3">
                        <div class="card col-lg-12  shadow-none rounded-0 border ">
                            <div class="card-body p-2">
                                <div class="form-group col-12 d-flex justify-content-end col-form-label mb-0">

                                    <a href="{{ route('cases.index') }}"
                                        class="btn btn-secondary btn-light ms-3">{{ __('Cancel') }}</a>
                                    <input type="submit" value="{{ __('Save') }}" class="btn btn-primary ms-2">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>

    {{ Form::close() }}
    <!-- [ Main Content ] end -->

{{--  CLIENT MODEL --}}
         <div class="modal fade" id="createClientModal" tabindex="-1" role="dialog" aria-labelledby="createClientModalLabel" aria-hidden="true">
             <div class="modal-dialog modal-lg" role="document">
                 <div class="modal-content">
                     <!-- Your modal content here -->
                     <div class="modal-header">
                         <h5 class="modal-title" id="createClientModalLabel">Create Client</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                     </div>
                 
                     {{ Form::open(['route' => 'users.store', 'method' => 'post', 'id' => 'create-client-form']) }}
                     <div class="modal-body">
                        <div id="loading-spinner" style="position: absolute;right: 45%;top: 40%; display:none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading...</p>
                        </div>
                     <div class="row">
                         <div class="form-group col-md-6">
                             {!! Form::label('name', __('Name'), ['class' => 'form-label']) !!}
                             {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                         </div>
                        <input type="hidden" name="ajax_client_call" value="1">
                             <div class="col-md-6 d-none">
                                 <div class="form-group">
                                     {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                                     <div>
                 
                                         <label class="radio-inline" style="padding-right: 15px">
                                             {{ Form::radio('type', 'client', true, ['required' => 'required']) }}
                                             {{ __('Client') }}
                                         </label>
                                     </div>
                                 </div>
                             </div>
                      
                         <div class="form-group col-md-6">
                             {{ Form::label('Email', __('Email'), ['class' => 'form-label']) }}
                             {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
                         </div>
                 
                         <div class="col-md-6">
                             <div class="form-group">
                                 {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                                
                                     <select class="form-control" id="country"  name="country"  required>
                                         <option value="">{{ __('Select Country') }}</option>
                                         @foreach ($countries as $country)
                                             <option value="{{ $country->id }}"
                                                 {{ $country->name == $selectedCountry ? 'selected' : '' }}>{{ $country->name }}
                                             </option>
                                         @endforeach
                                     </select>
                             </div>
                         </div>
                 
                 
                         <div class="col-md-6">
                             <div class="form-group">
                                 {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
                                
                                 <select class="form-control" id="state" name="state" required>
                                     <option value="">{{ __('Select State') }}</option>
                                     @foreach ($states as $state)
                                         <option value="{{ $state->id }}" {{ $state->name == $selectedState ? 'selected' : '' }}>
                                             {{ $state->name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                         </div>
                 
                         <div class="col-md-6">
                             <div class="form-group">
                                 {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
                                 
                                 <select class="form-control" id="city" name="city" required>
                                     <option value="">{{ __('Select City') }}</option>
                                     @foreach ($cities as $city)
                                         <option value="{{ $city->id }}" {{ $city->name == $selectedCity ? 'selected' : '' }}>
                                             {{ $city->name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="form-group">
                                 {{ Form::label('timezone', __('Timezone'), ['class' => 'form-label']) }}
                                 <select class="form-control" id="timezone" name="timezone" required>
                                     <option value="">{{ __('Select Timezone') }}</option>
                                     @foreach ($timezones as $timezone)
                                         <option value="{{ $timezone->id }}">(UTC {{ $timezone->utc_offset }})
                                             {{ $timezone->timezone }}</option>
                                     @endforeach
                                 </select>
                             </div>
                 
                         </div>
                         {{-- mobile_bumber --}}
                         <div class="form-group col-md-6">
                             {!! Form::label('mobile_number', __('Mobile Number'), ['class' => 'form-label']) !!}
                             {!! Form::tel('mobile_number', null, ['class' => 'form-control', 'required' => 'required']) !!}
                         </div>
                 
                         <div class="form-group col-md-6">
                             <label for="password" class="form-label">{{ __('Password') }}</label>
                             <div class="input-group" style="position: relative;">
                                 <input class="form-control" data-indicator="pwindicator" name="password" type="password" id="password"
                                     required autocomplete="password" placeholder="{{ __('Enter New Password') }}" minlength="8">
                                 <div class="input-group-append  password_eye_wraappe">
                                     <span style="height: 100%;" class="input-group-text  password_eye password-toggle3"
                                         onclick="togglePasswordVisibility3('password')">
                                         <i class="far fa-eye-slash"></i>
                                     </span>
                                 </div>
                             </div>
                             @error('password')
                                 <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                 </span>
                             @enderror
                             <div id="password" class="pwindicator">
                                 <div class="bar"></div>
                                 <div class="label"></div>
                             </div>
                         </div>
                 
                 
                 
                           
                                 @if (Auth::user()->type != 'super admin' )
                                 {{-- user role --}}
                                 <div class="col-md-6 user_form">
                                     <div class="form-group">
                                         {{ Form::label('role', __('Role'), ['class' => 'form-label']) }}
                                         {!! Form::select('role', $roles, null, ['class' => 'form-control select2', 'required' => 'required']) !!}
                                     </div>
                                 </div>
                                 {{-- user Type => team or client --}}
                                 @if($caseCount > 0)
                                     <div class="col-md-6 assign_cases">
                                         <div class="form-group">
                                             {{ Form::label('assign_all_cases', __('Assign to Existing Cases'), ['class' => 'form-label']) }}
                                             <div>
                 
                                                     <label class="radio-inline" style="padding-right: 15px">
                                                         {{ Form::radio('assign_all_cases', 'yes', false, ['required' => 'required']) }}
                                                         {{ __('Yes') }}
                                                     </label>
                                             
                                                     <label class="radio-inline" style="padding-right: 15px">
                                                         {{ Form::radio('assign_all_cases', 'no', true, ['required' => 'required']) }}
                                                         {{ __('No') }}
                                                     </label>
                                             </div>
                                         </div>
                                     </div>
                                 @endif
                                 @endif
                 
                     </div>
                 </div>
                 <div class="modal-footer">
                     <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
                     <input type="submit" value="{{ __('Create') }}" class="btn btn-primary ms-2">
                 </div>
                 {{ Form::close() }}
                 </div>
             </div>
         </div>
   
 
        
@endsection



@push('custom-script')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ asset('public/assets/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('public/assets/js/repeater.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/tinymce/tinymcenew.js') }}"></script>



    <script>

        $(document).ready(function () {
            $('#create-client-form').submit(function (event) {
                event.preventDefault();
        
                var form = $(this);
                var url = form.attr('action');
                var loadingSpinner = $('#loading-spinner');

                    // Show the loading spinner
                    loadingSpinner.show();

                $.ajax({
                    type: form.attr('method'),
                    url: url,
                    data: form.serialize(), // Serialize the form data
                    success: function (data) {
                    // append select input #refreshable-content base on data.team[value, key]
                    show_toastr('Success', 'Client Added Successfully', 'success');
                            loadingSpinner.hide();
                            $('#createClientModal').modal('hide');
                            // reset #create-client-form 
                            $('#create-client-form').trigger("reset");

                            // Create a new 'div' container
                            var newDiv = $('<div>', {
                                class: 'input-group custom_choose_class',
                            });

                            // Create a new 'select' element with the updated options
                            var newSelect = $('<select>', {
                                class: 'form-control draft_fields choices-multiple-multi-select',
                                id: 'clientSelectOption',
                                name: 'your_team[]',
                                multiple: true,
                                
                            });
                            data.team = {
                                'create-client': ['Create New Client'],
                                ...data.team,
                            };
                            $.each(data.team, function (key, value) {
                                newSelect.append($('<option>', {
                                    value: key,
                                    text: value,
                                }));
                            });

                            // Create the inner structure of the 'div'
                            newDiv.append(newSelect);
                         

                            // Replace the old 'div' with the new one
                            var oldDiv = $('.input-group.custom_choose_class');
                            oldDiv.replaceWith(newDiv);
                            // newSelect.select2();
                            clientChoicesInstance =  new Choices(newSelect[0], {
                                removeItemButton: true,
                                shouldSort: false, // Disable sorting
                                searchEnabled: true, // Enable search
                            });
                            clientSelectOption = document.getElementById('clientSelectOption');
                            clientSelectOption.addEventListener('change', function(e) {
                                if (e.target.value == 'create-client') {
                                    clientChoicesInstance.removeActiveItemsByValue('create-client');
                                    $('#createClientModal').modal('show');
                                }
                            });


                  
                },

                    error: function (data) {
                        // Handle errors, e.g., show validation errors
                        loadingSpinner.hide();
                        show_toastr('error', 'Client Not Created', 'error');

                    }
                });
            });
        });
   

     </script>


    <script>



        $(document).ready(function() {
            $('#record_des').hide();
            $('#fall_other_yes').hide();
            $('#fallPOliceNotified_div').hide();
            $('#fallReportField_div').hide();
            $('#fallHealthInsurance_div').hide();
        });

        function toggleDiv(showDiv) {
            const additionalDiv = document.getElementById('record_des');
            if (showDiv) {
                additionalDiv.style.display = 'block';
            } else {
                additionalDiv.style.display = 'none';
            }
        }

        function fallOtherToggle(showDiv) {
            const fall_other_yes = document.getElementById('fall_other_yes');
            if (showDiv) {
                fall_other_yes.style.display = 'inherit';
            } else {
                fall_other_yes.style.display = 'none';
            }
        }

        function fallPOliceNotified(showDiv) {
            const fallPOliceNotified_div = document.getElementById('fallPOliceNotified_div');
            if (showDiv) {
                fallPOliceNotified_div.style.display = 'block';
            } else {
                fallPOliceNotified_div.style.display = 'none';
            }
        }

        function fallReportField(showDiv) {
            const fallReportField_div = document.getElementById('fallReportField_div');
            if (showDiv) {
                fallReportField_div.style.display = 'block';
            } else {
                fallReportField_div.style.display = 'none';
            }
        }

        function fallHealthInsurance(showDiv) {
            const fallHealthInsurance_div = document.getElementById('fallHealthInsurance_div');
            if (showDiv) {
                fallHealthInsurance_div.style.display = 'block';
            } else {
                fallHealthInsurance_div.style.display = 'none';
            }
        }


        $(document).ready(function() {
            $('.statute_of_limitations').select2({
                placeholder: 'Search for a statute of limitations...',
                allowClear: true,
                width: '100%'
            }).addClass('form-control draft_fields custom-scroll');

            // ajax call for draft

            $('.draft_fields').on('blur', function() {
                // Serialize the form data
                console.log('hereee');
                var formData = $('#caseForm').serialize();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('case.draft') }}",
                    method: 'POST',
                    data: formData,
                    success: function(data) {
                        if (data.success) {
                            $('#caseId').val(data.caseId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });

            autoCallGroup();

        });
    </script>

    <script>
        var allOptions = @json($allOptions);
   
        var selectOptions = document.getElementById('selectOptions');
        var choicesInstance = new Choices(selectOptions, {
            removeItemButton: true,
            itemSelectText: '',
        });
        // clientSelectOption
        var clientSelectOption = document.getElementById('clientSelectOption');
        var clientChoicesInstance = new Choices(clientSelectOption, {
            removeItemButton: true,
            itemSelectText: '',
        });

        clientSelectOption.addEventListener('change', function(e) {
            console.log(e.target.value);
            if (e.target.value == 'create-client') {
                clientChoicesInstance.removeActiveItemsByValue('create-client');
                $('#createClientModal').modal('show');
            }
        });


        function autoCallGroup() {
            
            const choices_items = document.querySelectorAll('.choices__item');

            choices_items.forEach((choice_item) => {
                const data_value = choice_item.getAttribute('data-value');
                if (data_value.includes(',')) {
                    choice_item.style.color = 'blue';
                }
            });
        }

        var shouldUpdateSelection = true;

        function selectUsersInList(userIds) {

            if (!Array.isArray(userIds)) {
                userIds = [userIds];
            }

            // Clear previous selections
            choicesInstance.clearInput();
            userIds.forEach(function(userId) {
                if (allOptions.hasOwnProperty(userId)) {
                    if (allOptions[userId].startsWith("group")) {
                        // This is a group, add individual users from the group
                        var groupUserIds = allOptions[userId].split(',').slice(1);
                        groupUserIds.forEach(function(individualUserId) {
                            choicesInstance.setChoiceByValue(individualUserId, true);
                        });
                    } else {
                        // This is an individual user
                        choicesInstance.setChoiceByValue(userId, true);
                    }
                }
            });

        }
       
        selectOptions.addEventListener('change', function(e) {
            e.preventDefault();
            var selectedValue = e.target.value;

            if (shouldUpdateSelection) {
                var selectedValues = e.detail.value.split(',');
                choicesInstance.clearInput();
                selectUsersInList(selectedValues);
            }

            choices_list = document.querySelector('.choices__list--multiple');
            choices_items = choices_list.querySelectorAll('.choices__item');

            for (let i = 0; i < choices_items.length; i++) {
                const choice_item = choices_items[i];
                const data_value = choice_item.getAttribute('data-value');
                if (data_value.includes(',')) {
                    choice_item.style.display = 'none';
                }
            }
            shouldUpdateSelection = true;
        });

        function showChoiceItem(value) {

            choices_list = document.querySelector('.choices__list--multiple');
            choices_items = choices_list.querySelectorAll('.choices__item');

            for (let i = 0; i < choices_items.length; i++) {
                const choice_item = choices_items[i];
                const data_value = choice_item.getAttribute('data-value');

                if (data_value.includes(value)) {

                    shouldUpdateSelection = false;
                    choicesInstance.removeActiveItemsByValue(data_value);
                }
            }
        }

        // Listen to the Choices.js removeItem event
        selectOptions.addEventListener('removeItem', function(event) {

            var removedValue = event.detail.value;
            showChoiceItem(removedValue);
            autoCallGroup();
            shouldUpdateSelection = false;
            choicesInstance.removeActiveItemsByValue(removedValue);

        });

        var initialSelectedValues = choicesInstance.getValue();
        selectUsersInList(initialSelectedValues);
    </script>


    <script>
        const folderContainer = document.getElementById("folderContainer");
        const folderTemplate = document.getElementById("folderTemplate");
        const createFolderButton = document.getElementById("createFolderButton");
        const templateContainer = document.getElementById('appendDIv'); // The container for new templates

        const newdiv = document.getElementById("newdiv");

        let folderCounts = 0;
        let itemCounts = 0;


        createFolderButton.addEventListener("click", () => {
            const makeFolder = createRowFolder(folderCounts);
            folderTemplate.appendChild(makeFolder);
            folderCounts++;
        });

        function createRowFolder(folderCounts) {
            const folderClone = document.createElement('div');
            folderClone.classList.add('col-md-12');

            folderClone.innerHTML = `<div class="card card_closest shadow-none rounded-0 border my-3">
                                            <div class="card-header">
                                                <div class="row flex-grow-1">
                                                    <div class="col-md d-flex align-items-center col-6">
                                                        <h5 class="card-header-title text-primary">Folder Name and
                                                            Description</h5>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                            <a 
                                                            class="ti ti-trash btn btn-danger btn_danger_color text-white btn-sm remove-folder-button"
                                                            ></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body appendDIv">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="">Folder Name</label>
                                                        <input type="text" class="form-control draft_fields"
                                                            name="folders[${folderCounts}][folder_name]">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="">Folder Description</label>
                                                        <input type="text" class="form-control draft_fields"
                                                            name="folders[${folderCounts}][folder_description]">
                                                    </div>
                                                </div>
                                                    <br>
                                                    <hr class="py-2">

                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <a style="float: right;" class="btn btn-primary btn-sm text-white add-row-button"><i class="fas fa-plus"></i> Add
                                                                Files</a>
                                                        </div>
                                                    </div>
                                                    <div class="unique_closest_class"></div> 
                                            </div>
                                        </div>`;

            return folderClone;

        }
        document.addEventListener("click", event => {
            if (event.target.classList.contains("add-row-button")) {

                const closestAppendDiv = event.target.closest(".appendDIv");
                const closestUniqueDiv = closestAppendDiv.querySelector(".unique_closest_class");

                const folderCountsInput = closestAppendDiv.querySelector(
                    '[name^="folders["][name$="[folder_name]"]');
                const nameAttribute = folderCountsInput.getAttribute('name');
                const startIndex = nameAttribute.indexOf("[") + 1;

                const endIndex = nameAttribute.indexOf("]", startIndex);
                const folderCounts = parseInt(nameAttribute.substring(startIndex, endIndex));

                const rowTemplateee = createRowTemplate(folderCounts, itemCounts);
                itemCounts++;

                closestUniqueDiv.appendChild(rowTemplateee);
            } else if (event.target.classList.contains("remove-row-button")) {
                const row = event.target.closest(".row-template");
                row.remove();
            } else if (event.target.classList.contains("remove-folder-button")) {
                const folder = event.target.closest(".card");
                folder.remove();
            }
        });

        function createRowTemplate(folderIndex, itemIndex) {
            const rowClone = document.createElement('div');
            rowClone.classList.add('row', 'mt-4', 'mb-4', 'row-template', 'template-container', 'px-5');
            rowClone.style.display = 'flex';

            rowClone.innerHTML = `
                <div class="col-md-6">
                    <input type="text" class="form-control draft_fields doc_name" placeholder="Name"
                        name="folders[${folderIndex}][folder_doc][${itemIndex}][doc_name]">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control draft_fields"
                        placeholder="Description"
                        name="folders[${folderIndex}][folder_doc][${itemIndex}][doc_description]">
                </div>
                <div class="col-md-1 text-end">
                    <a class="ti ti-trash btn btn-danger btn_danger_color text-end text-white btn-sm remove-row-button"></a>
                </div>
                <div class="col-md-11 py-2">
                    <div class="col-md-12 ">
                        <input name="folders[${folderIndex}][folder_doc][${itemIndex}][files][]" class="filepond-input form-control draft_fields" type="file"   id="photo">
                    </div>
                </div>
            `;

            const inputElement = rowClone.querySelector('.filepond-input');

            initializeFilePond(inputElement, folderIndex, itemIndex);


            return rowClone;
        }

        function initializeFilePond(inputElement, folderIndex, docIndex) {

            const pond = FilePond.create(inputElement, {
                allowMultiple: true,
                allowImageValidateSize: false,
                dropOnElement: true,
                dropOnPage: false,
                imageValidateSizeMinHeight: 370,
                imageValidateSizeMinWidth: 550,
                allowImagePreview: true,
                imagePreviewHeight: 200,
                acceptedFileTypes: [],
                server: {
                    process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                        // Handle file upload using Ajax
                        const formData = new FormData();
                        const headers = new Headers();
                        headers.append('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        formData.append(fieldName, file);
                        fetch('/cases/case_docs', {
                                method: 'POST',
                                body: formData,
                                headers: headers,
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    load(result.fileUrl);
                                    const fileItem = pond.getFiles().find(item => item.file === file);
                                    console.log(fileItem);
                                    if (fileItem) {
                                        fileItem.file.name = result
                                            .fileName; // Update the name of the uploaded file

                                    }
                                } else {
                                    error(result.error);
                                }
                            })
                            .catch(() => {
                                error('Upload failed');
                            });
                    }
                }


            });
            inputElement.setAttribute("name", `folders[${folderIndex}][folder_doc][${docIndex}][files][]`);
        }
    </script>

    <script>
        $(document).ready(function() {

            $('#folderTemplate').on('blur', '.draft_fields', function() {
                // $('.draft_fields').on('blur', function() {
                // Serialize the form data
                console.log('hereee');
                var formData = $('#caseForm').serialize();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('case.draft') }}",
                    method: 'POST',
                    data: formData,
                    success: function(data) {
                        if (data.success) {
                            $('#caseId').val(data.caseId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });




            const openDateInput = document.getElementById('open_date');
            const closeDateInput = document.getElementById('close_date');
            const incidentDateInput = document.getElementById('incident_date');

            const fiiincidentDateInput = document.getElementById('fii_incident_date');


            const openPicker = new Pikaday({
                field: openDateInput,
                format: 'YYYY-MM-DD',
                // maxDate: new Date(), // Disable future dates
                onSelect: function(selectedDate) {
                    const minCloseDate = new Date(selectedDate);
                    minCloseDate.setDate(minCloseDate.getDate() + 1);
                    closePicker.setMinDate(minCloseDate);

                    openDateInput.value = formatDate(selectedDate);
                },
            });


            const closePicker = new Pikaday({
                field: closeDateInput,
                format: 'YYYY-MM-DD',
                onSelect: function(selectedDate) {
                    const formattedDate = formatDate(selectedDate);
                    closeDateInput.value = formattedDate;
                },
            });

            const incidentPicker = new Pikaday({
                field: incidentDateInput,
                format: 'YYYY-MM-DD',
                onSelect: function(selectedDate) {
                    const formattedDate = formatDate(selectedDate);
                    incidentDateInput.value = formattedDate;
                },
            });

            const fiiincidentPicker = new Pikaday({
                field: fiiincidentDateInput,
                format: 'YYYY-MM-DD',
                onSelect: function(selectedDate) {
                    const formattedDate = formatDate(selectedDate);
                    fiiincidentDateInput.value = formattedDate;
                },
            });


            const calendarIconOpen = document.getElementById('calendar_icon_open_date');
            calendarIconOpen.addEventListener('click', function() {
                openPicker.show();
            });

            const calendarIconClose = document.getElementById('calendar_icon_close_date');
            calendarIconClose.addEventListener('click', function() {
                closePicker.show();
            });

            const calendarIconIncident = document.getElementById('calendar_icon_incident_date');
            calendarIconIncident.addEventListener('click', function() {
                incidentPicker.show();
            });

            const calendarIconFiiIncident = document.getElementById('calendar_icon_fii_incident_date');
            calendarIconFiiIncident.addEventListener('click', function() {
                fiiincidentPicker.show();
            });

            openDateInput.addEventListener('input', function() {
                autoFormatDate(this);
                validateAndAdjustDate(this);

            });

            closeDateInput.addEventListener('input', function() {
                autoFormatDate(this);
                validateAndAdjustDate(this);

            });

            incidentDateInput.addEventListener('input', function() {
                autoFormatDate(this);
                validateAndAdjustDate(this);

            });

            function autoFormatDate(input) {
                let value = input.value.replace(/[^\d-]/g, '');

                // Remove extra hyphens
                value = value.replace(/-{2,}/g, '-');

                // Restrict to a maximum of 10 characters
                value = value.substring(0, 10);

                if (value.length >= 4 && value[4] !== '-') {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                }

                if (value.length >= 7 && value[7] !== '-') {
                    // if (value.length >= 7 && value[7] === '-') {
                    value = value.substring(0, 7) + '-' + value.substring(7);
                }


                input.value = value;
            }

            function validateAndAdjustDate(input) {

                const parts = input.value.split('-');

                let year = parseInt(parts[0], 10);
                let month = parseInt(parts[1], 10);
                if (parts.length >= 2) {
                    let year = parseInt(parts[0], 10);
                    let month = parseInt(parts[1], 10);

                    if (isNaN(year) || isNaN(month)) {
                        return;
                    }

                    const currentDate = new Date();
                    const currentYear = currentDate.getFullYear();
                    const currentMonth = currentDate.getMonth() + 1; // JavaScript months are zero-based

                    const monthString = month.toString();
                    const numberOfDigits = monthString.length;

                    if (numberOfDigits === 2) {

                        if (month >= 1 && month <= 12) {

                            input.value = `${year}-${String(month).padStart(2, '0')}-`;
                        } else {

                            month = currentMonth;
                            input.value = `${year}-${String(month).padStart(2, '0')}-`;
                        }
                    }

                }
                if (parts.length >= 3) {
                    let day = parseInt(parts[2], 10);

                    if (isNaN(year) || isNaN(month) || isNaN(day)) {
                        return;
                    }

                    const currentDate = new Date();
                    const currentYear = currentDate.getFullYear();
                    const currentDay = currentDate.getDate();


                    const dayString = day.toString();
                    const numberOfDayDigits = dayString.length;


                    if (numberOfDayDigits === 2) {
                        const daysInSelectedMonth = new Date(year, month - 1, 0).getDate(); // Subtract 1 from month
                        if (day >= 1 && day <= daysInSelectedMonth) {
                            input.value =
                                `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        } else {
                            day = currentDay;
                            input.value =
                                `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        }
                    }

                    if (numberOfDayDigits === 1) {
                        input.value = `${year}-${String(month).padStart(2, '0')}-${day}`;
                        // input.value = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(1, '0')}`;

                    }

                }

            }


            // Function to format date as YYYY-MM-DD
            function formatDate(date) {
                const year = date.getFullYear();
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const day = date.getDate().toString().padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
        });
    </script>

    <script>
        $(document).ready(function() {


            var selecteddate = $('#open_date').val();
            if (selecteddate === '') {
                // $('#close_date').show();
                document.getElementById("close_date").disabled = true;

            } else {
                document.getElementById("close_date").disabled = false;

            }


            $('#open_date').change(function() {
                var selecteddate = $('#open_date').val();
                if (selecteddate === '') {
                    // $('#close_date').show();
                    document.getElementById("close_date").disabled = true;

                } else {
                    document.getElementById("close_date").disabled = false;

                }
            });


            $('#practice_area').change(function() {
                var practiceAreaValue = $('#practice_area').val();
                if (practiceAreaValue ===
                    'Motor Vehicle Accident') {
                    $('.hide_div').show();
                    $('.slip_fall_from').hide();
                } else if (practiceAreaValue === 'Slip & Fall') {

                    $('.slip_fall_from').show();
                    $('.hide_div').hide();
                } else {

                    $('.hide_div').hide();
                    $('.slip_fall_from').hide();
                }
            });

            // Initial check when the page loads
            $(document).ready(function() {
                var practiceAreaValue = $('#practice_area').val();

                if (practiceAreaValue ===
                    'Motor Vehicle Accident') {
                    $('.hide_div').show();
                    $('.slip_fall_from').hide();
                } else if (practiceAreaValue === 'Slip & Fall') {

                    $('.slip_fall_from').show();
                    $('.hide_div').hide();
                } else {

                    $('.hide_div').hide();
                    $('.slip_fall_from').hide();
                }
            });

            var selectedValue = $('#practice_area').val();

            if (selectedValue === 'Personal Injury') {
                $('#injury_type_div').show();
            } else {
                $('#injury_type_div').hide();
            }

            $('#practice_area').change(function() {
                var selectedValue = $(this).val();

                if (selectedValue === 'Personal Injury') {
                    $('#injury_type_div').show();
                } else {
                    $('#injury_type_div').hide();
                }
            });

        });

        $(document).ready(function() {
            // Add Row Button Click Event
            $('.add-row').on('click', function() {
                var $rowToDuplicate = $(this).closest('.repeater').find('tbody[data-repeater-item]:last');
                var $clonedRow = $rowToDuplicate.clone();

                $clonedRow.find('input[type="text"]').val('');
                $clonedRow.find('input[type="email"]').val('');


                $rowToDuplicate.after($clonedRow);
            });

            // Delete Row Button Click Event
            $(document).on('click', '.desc_delete', function() {
                var $rows = $(this).closest('.repeater').find('tbody[data-repeater-item]');
                if ($rows.length > 1) {
                    var $rowToDelete = $(this).closest('tbody[data-repeater-item]');
                    $rowToDelete.remove();
                } else {
                    $(this).tooltip('show');
                    // alert("Cannot delete the last row!");
                }
            });


            $(".add-row-witness").on("click", function() {
                var $newRow = $(".repeater-row-witness:first").clone();
                console.log($newRow);
                $newRow.find("input").val("");
                $("#repeater-container-witness").append($newRow);
            });

            // Delete row
            $("#repeater-container-witness").on("click", ".delete-row-witness", function() {
                if ($(".repeater-row-witness").length > 1) {
                    $(this).closest(".repeater-row-witness").remove();
                } else {
                    alert("Cannot delete the last row!");
                }
            });

        });




        $(document).on('change', '#court', function() {
            var selected_opt = $(this).val();
            var seletor = $(this);

            $.ajax({
                url: "{{ route('get.highcourt') }}",
                datType: 'json',
                method: 'POST',
                data: {
                    selected_opt: selected_opt
                },
                success: function(data) {
                    if (data.status == 1) {
                        $('#highcourt_div').removeClass('d-none');
                        $('#highcourt_div').empty();
                        $('#casetype_div').addClass('d-none').empty();
                        $('#casenumber_div').addClass('d-none');
                        $('#diarybumber_div').addClass('d-none');

                        $('#highcourt_div').append(
                            '<label for="highcourt" class="form-label">High Court</label> <select class="form-control draft_fields" name="highcourt" id="highcourt"> </select>'
                        );
                        $('#highcourt').append('<option value="">{{ __('Please Select') }}</option>');

                        $.each(data.dropdwn, function(key, value) {
                            $('#highcourt').append('<option value="' + key + '">' + value +
                                '</option>');
                        });

                    } else {
                        var text = $("#court option:selected").text();

                        $('#highcourt_div').addClass('d-none').empty();
                        $('#bench_div').addClass('d-none').empty();

                        $('#casetype_div').removeClass('d-none').append(
                            '<label for="casetype" class="form-label">' + text +
                            '</label><select class="form-control draft_fields" name="casetype" id="casetype"> <option value="">{{ __('Please Select') }}</option><option value="Case Number">{{ __('Case Number') }}</option><option value="Diary Number">{{ __('Diary Number') }}</option></select>'
                        );



                        $(document).on('change', '#casetype', function() {
                            var type = $("#casetype option:selected").text();
                            $('#casenumber_div').addClass('d-none');
                            $('#diarybumber_div').addClass('d-none');
                            if (type == 'Case Number') {
                                $('#casenumber_div').removeClass('d-none');
                                $('#case_number_div').removeClass('d-none');

                            }
                            if (type == 'Diary Number') {
                                $('#case_number_div').addClass('d-none');
                                $('#diarybumber_div').removeClass('d-none');

                            }
                        });

                    }

                }
            })
        });

        $(document).on('change', '#highcourt', function() {
            var selected_opt = $(this).val();

            $.ajax({
                url: "{{ route('get.bench') }}",
                datType: 'json',
                method: 'POST',
                data: {
                    selected_opt: selected_opt
                },
                success: function(data) {
                    if (data.status == 1) {
                        $('#bench_div').removeClass('d-none');
                        $('#bench_div').empty();
                        $('#bench_div').append(
                            '<label for="bench" class="form-label">Bench</label> <select class="form-control draft_fields" name="bench" id="bench"> </select>'
                        );
                        $('#bench').append('<option value="">{{ __('Please Select') }}</option>');

                        $.each(data.dropdwn, function(key, value) {
                            $('#bench').append('<option value="' + key + '">' + value +
                                '</option>');
                        });

                        $('#danger-span').addClass('d-none').remove();
                    } else {
                        $('#bench_div').addClass('d-none').empty();
                        $('#danger-span').addClass('d-none').remove();
                        $('#highcourt_div').removeClass('d-none').append(
                            '<a href="#" data-url={{ route('bench.create') }} data-title="Add Bench" data-ajax-popup="true" data-size="md" title={{ __('Create New Bench') }}><span class="text-danger" id="danger-span">Please add bench to current high court</span></a>'
                        )
                    }

                }
            })

        });

        $(document).on('change', '#causelist_by', function() {
            $('#adv_label').html($(this).val())
        });

        $(document).on('change', '#bench', function() {

        });
    </script>


    <script>
        if ($(".pc-tinymce-2").length) {
            tinymce.init({
                selector: '.pc-tinymce-2',
                height: "400",
                content_style: 'body { font-family: "Inter", sans-serif; }'
            });
        }
    </script>
@endpush