@extends('layouts.app')

@section('page-title')
    {{ __('Manage Form Encryption') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Form Encryption') }}</li>
@endsection
@php
    $setting = App\Models\Utility::settings();
    
    $currency = $setting['site_currency_symbol'] ?? '$';
@endphp
@section('content')
    <style>
        .encryptionForm form {
            padding: 0 10px;
        }
    </style>
    @php
        
        //  $motorVehicleFormFields =  Array( 'location_of_accident','case_docs', 'coordinates', 'descriptionfile_location', 'FIR_number', 'FIR_police_station', 'FIR_year', 'first_party_email', 'first_party_fax', 'first_party_insurance_phone_number', 'first_party_phone_number', 'first_party_vehicle_license', 'first_party_vehicle_make', 'first_party_vehicle_model', 'first_party_vehicle_year', 'incident_date', 'police_report', 'recorded_statement_description', 'referred_by', 'your_advocates');
    @endphp
    <div class="row pt-0">

        <div class="col-xl-12">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-6 row">
                        <h4 class="page-header-title">Select Form Type</h4>
                    </div>
                    <div class="col-md-6  row">
                        {{-- select for form type --}}
                        <select class="form-select" aria-label="Default select example" id="formType">
                            <option value="motorVehicleForm">Motor Vehicle</option>
                            <option value="slipAndFallForm">Slip & Fall</option>
                        </select>
                    </div>
                    <!-- Motor Vehicle Form -->
                    <div class="mb-4 col-md-12">
                        <div class="collapse mt-3 encryptionForm show" id="motorVehicleForm">
                            <form action="{{ route('form.encryption.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="form_type" value="motor_vehicle">
                                <input type="hidden" name="form_name" value="Motor Vehicle Accident">

                                <div class="row mb-3">
                                    @foreach ($motorVehicleFormFields as $section => $fields)
                                        @php
                                            $parts = explode('@', $section);
                                            // Define variables with default values
                                            $parentDiv = null;
                                            $childDiv = null;
                                            $section = null;
                                            
                                            // Assign values based on the number of "@" symbols
                                            if (count($parts) == 1) {
                                                $section = $parts[0];
                                            } elseif (count($parts) == 2) {
                                                $childDiv = $parts[0];
                                                $section = $parts[1];
                                            } elseif (count($parts) == 3) {
                                                $parentDiv = $parts[0];
                                                $childDiv = $parts[1];
                                                $section = $parts[2];
                                            }
                                        @endphp
                                        <div class="{{ isset($parentDiv) ? $parentDiv : 'col-md-12' }} row">
                                            <div class="col-md-6 mt-3 mb-3">
                                                <h4>{{ ucwords(str_replace('_', ' ', $section)) }}</h4>
                                            </div>
                                            <div class="col-md-6 mt-3 mb-3">
                                                <h4>Encrypt</h4>
                                            </div>
                                            @foreach ($fields as $fieldName => $label)
                                                <div class="{{ isset($childDiv) ? $childDiv : 'col-md-6' }} ">
                                                    <label class="col-form-label font-weight-bold"
                                                        for="{{ $fieldName }}">{{ ucfirst(str_replace('_', ' ', $label)) }}</label>
                                                </div>
                                                <div class="{{ isset($childDiv) ? $childDiv : 'col-md-6' }} ">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="{{ $fieldName }}" id="{{ $fieldName }}_yes"
                                                            value="Yes"
                                                            {{ ($motorVehicleFormData[$fieldName] ?? '') == 'Yes' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="{{ $fieldName }}_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="{{ $fieldName }}" id="{{ $fieldName }}_no"
                                                            value="No"
                                                            {{ ($motorVehicleFormData[$fieldName] ?? '') == 'No' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="{{ $fieldName }}_no">No</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- Fall and Slip --}}
                    <div class="mb-4 col-md-12">

                        <div class="collapse mt-3 encryptionForm show" id="slipAndFallForm">
                            <form action="{{ route('form.encryption.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="form_type" value="slip_fall">
                                <input type="hidden" name="form_name" value="Slip & Fall">

                                <div class="row mb-3">
                                    @foreach ($slipAndFallForm as $section => $fields)
                                        @php
                                            $parts = explode('@', $section);
                                            // Define variables with default values
                                            $parentDiv = null;
                                            $childDiv = null;
                                            $section = null;
                                            
                                            // Assign values based on the number of "@" symbols
                                            if (count($parts) == 1) {
                                                $section = $parts[0];
                                            } elseif (count($parts) == 2) {
                                                $childDiv = $parts[0];
                                                $section = $parts[1];
                                            } elseif (count($parts) == 3) {
                                                $parentDiv = $parts[0];
                                                $childDiv = $parts[1];
                                                $section = $parts[2];
                                            }
                                        @endphp
                                        <div class="{{ isset($parentDiv) ? $parentDiv : 'col-md-12' }} row">
                                            <div class="col-md-6 mt-3 mb-3">
                                                <h4>{{ ucwords(str_replace('_', ' ', $section)) }}</h4>
                                            </div>
                                            <div class="col-md-6 mt-3 mb-3">
                                                <h4>Encrypt</h4>
                                            </div>
                                            @foreach ($fields as $fieldName => $label)
                                                <div class="{{ isset($childDiv) ? $childDiv : 'col-md-6' }} ">
                                                    <label class="col-form-label font-weight-bold"
                                                        for="{{ $fieldName }}">{{ ucfirst(str_replace('_', ' ', $label)) }}</label>
                                                </div>
                                                <div class="{{ isset($childDiv) ? $childDiv : 'col-md-6' }} ">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="{{ $fieldName }}" id="{{ $fieldName }}_yes"
                                                            value="Yes"
                                                            {{ ($slipAndFallFormData[$fieldName] ?? '') == 'Yes' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="{{ $fieldName }}_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                            name="{{ $fieldName }}" id="{{ $fieldName }}_no"
                                                            value="No"
                                                            {{ ($slipAndFallFormData[$fieldName] ?? '') == 'No' ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="{{ $fieldName }}_no">No</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>



                                <!-- Repeat the same structure for other input fields -->

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Get the select element and the form elements
        const formTypeSelect = document.getElementById('formType');
        const motorVehicleForm = document.getElementById('motorVehicleForm');
        const slipFallForm = document.getElementById('slipAndFallForm');

        // Function to show/hide forms based on the selected option
        function toggleForms() {
            console.log(formTypeSelect.value);
            if (formTypeSelect.value === 'motorVehicleForm') {
                motorVehicleForm.style.display = 'block';
                slipFallForm.style.display = 'none';
            } else if (formTypeSelect.value === 'slipAndFallForm') {
                motorVehicleForm.style.display = 'none';
                slipFallForm.style.display = 'block';
            }
        }

        // Initial call to toggleForms to set the initial form visibility
        toggleForms();

        // Attach an event listener to the select element
        formTypeSelect.addEventListener('change', toggleForms);
    </script>
@endsection
