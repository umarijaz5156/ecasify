@php
    $settings = App\Models\Utility::settings();
    $userID = Auth::user()->id;

    $userData = App\Models\User::where('id', $userID)
        ->with('userDetails')
        ->first();
    $userDetail = $userData->userDetails;
@endphp

{{ Form::open(['route' => 'users.store', 'method' => 'post']) }}
<div class="modal-body">

    <div class="row">
        <div class="form-group col-md-6">
            {!! Form::label('name', __('Name'), ['class' => 'form-label']) !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
        @if (Auth::user()->type != 'super admin')
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('type', __('Type'), ['class' => 'form-label']) }}
                    <div>

                        <label class="radio-inline" style="padding-right: 15px">
                            {{ Form::radio('type', 'client', false, ['required' => 'required']) }}
                            {{ __('Client') }}
                        </label>

                        <label class="radio-inline" style="padding-right: 15px">
                            {{ Form::radio('type', 'team', false, ['required' => 'required']) }}
                            {{ __('Staff') }}
                        </label>

                        <label class="radio-inline" style="padding-right: 15px">
                            {{ Form::radio('type', 'attorney', false, ['required' => 'required']) }}
                            {{ __('Attorney') }}
                        </label>
                        <label class="radio-inline" style="padding-right: 15px">
                            {{ Form::radio('type', 'co admin', false, ['required' => 'required']) }}
                            {{ __('Co Admin') }}
                        </label>
                    </div>
                </div>
            </div>
        @endif
        <div class="form-group col-md-6">
            {{ Form::label('Email', __('Email'), ['class' => 'form-label']) }}
            {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                @if (Auth::user()->type == 'super admin')
                    {{-- <select class="form-control country select2" id='country' name="country" required>
                        <option value="">{{ __('Select Country') }}</option>
                    </select> --}}
                    <select class="form-control" id="country" required name="country">
                        <option value="">{{ __('Select Country') }}</option>

                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ $country->name == $selectedCountry ? 'selected' : '' }}>{{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="type" value="company">
                @else
                    <select class="form-control" id="country" name="country" required>
                        <option value="">{{ __('Select Country') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ $country->name == $selectedCountry ? 'selected' : '' }}>{{ $country->name }}
                            </option>
                        @endforeach
                    </select>

                @endif
            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
                {{-- <select class="form-control select2" id="state" name="state" required>
                        <option value="">{{ __('Select State') }}</option>
                    </select> --}}
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
                {{-- <select class="form-control" id="city" name="city" required>
                        <option value="">{{ __('Select City') }}</option>
                    </select> --}}
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
                    required autocomplete="password" placeholder="{{ __('Enter New Password') }}">
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




        @if (Auth::user()->type != 'super admin')
            {{-- user role --}}
            <div class="col-md-6 user_form">
                <div class="form-group">
                    {{ Form::label('role', __('Role'), ['class' => 'form-label']) }}
                    {!! Form::select('role',  $roles->map(function($role) {return ucfirst($role);}), null, ['class' => 'form-control select2', 'required' => 'required']) !!}
                </div>
            </div>
            {{-- user Type => team or client --}}
            @if ($caseCount > 0)
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


        <div class="attorney_form">
            <div class="row">
                {{-- <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            {{ Form::label('password', __('Password'), ['class' => 'col-form-label']) }}
                            <input class="form-control" id="password" type="password" name="password" required>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            {{ Form::label('phone_number', __('Phone Number'), ['class' => 'col-form-label']) }}
                            {{ Form::text('phone_number', null, ['class' => 'form-control','required' => 'required']) }}
                        </div>
                    </div>
                        --}}


                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('age', __('Age'), ['class' => 'col-form-label']) }}
                        {{ Form::number('age', null, ['class' => 'form-control']) }}
                    </div>
                </div>



                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('father_name', __('Father\'s Name'), ['class' => 'col-form-label']) }}
                        {{ Form::text('father_name', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('company_name', __('Company Name'), ['class' => 'col-form-label']) }}
                        {{ Form::text('company_name', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('website', __('Website'), ['class' => 'col-form-label']) }}
                        {{ Form::url('website', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('tin', __('Tax Identification Number'), ['class' => 'col-form-label']) }}
                        {{ Form::number('tin', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('gstin', __('GST Identification Number (GSTIN)'), ['class' => 'col-form-label']) }}
                        {{ Form::text('gstin', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('pan_number', __('Permanent Account Number (PAN)'), ['class' => 'col-form-label']) }}
                        {{ Form::text('pan_number', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('hourly_rate', __('Hourly Rate') . ' (' . $settings['site_currency'] . ')', ['class' => 'col-form-label']) }}
                        {{ Form::number('hourly_rate', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="card-header">
                    <div class="row flex-grow-1">
                        <div class="col-md d-flex align-items-center">
                            <h5 class="card-header-title">
                                {{ __('Office Address') }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('ofc_address_line_1', __('Address Line 1'), ['class' => 'col-form-label']) }}
                        {{ Form::text('ofc_address_line_1', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('ofc_address_line_2', __('Address Line 2'), ['class' => 'col-form-label']) }}
                        {{ Form::text('ofc_address_line_2', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                {{-- <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('country', __('Country'), ['class' => 'col-form-label']) }}
                            <select class="form-control" id="country" name="ofc_country" required>
                                <option value="">{{ __('Select Country') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
                            <select class="form-control" id="state" name="ofc_state" required>
                                <option value="">{{ __('Select State') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
                            <select class="form-control" id="city" name="ofc_city">
                                <option value="">{{ __('Select City') }}</option>
                            </select>
                        </div>
                    </div> --}}

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('zip_code', __('Zip/Postal Code'), ['class' => 'col-form-label']) }}
                        {{ Form::number('ofc_zip_code', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="card-header">
                    <div class="row flex-grow-1">
                        <div class="col-md d-flex align-items-center">
                            <h5 class="card-header-title">
                                {{ __('Home Address') }}</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('home_address_line_1', __('Address Line 1'), ['class' => 'col-form-label']) }}
                        {{ Form::text('home_address_line_1', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('home_address_line_2', __('Address Line 2'), ['class' => 'col-form-label']) }}
                        {{ Form::text('home_address_line_2', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('country', __('Country'), ['class' => 'col-form-label']) }}
                        <select class="form-control" id="home_country" name="home_country">
                            <option value="">{{ __('Select Country') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
                        <select class="form-control" id="home_state" name="home_state">
                            <option value="">{{ __('Select State') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
                        <select class="form-control" id="home_city" name="home_city">
                            <option value="">{{ __('Select City') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        {{ Form::label('zip_code', __('Zip/Postal Code'), ['class' => 'col-form-label']) }}
                        {{ Form::number('home_zip_code', null, ['class' => 'form-control']) }}
                    </div>
                </div>


                <div class="col-md-12 repeater" id="wholesale_disc_content">


                    <div class="row">
                        <div class="col-12">
                            <div class="card my-3 shadow-none rounded-0 border">
                                <div class="card-header">
                                    <div class="row gy-3 flex-grow-1">
                                        <div class="col-sm-6 d-flex align-items-center col-6">
                                            <h5 class="card-header-title">{{ __('Point of Contacts') }}</h5>
                                        </div>

                                        <div class="col-sm-6 d-flex justify-content-end align-items-center col-6">
                                            <a data-repeater-create=""
                                                class="btn btn-primary btn-sm add-row text-white" data-toggle="modal"
                                                data-target="#add-bank">
                                                <i class="fas fa-plus"></i> {{ __('Add Row') }}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body table-border-style ">
                                    <div class="table-responsive">
                                        <table class="table  mb-0 table-custom-style"
                                            data-repeater-list="point_of_contacts" id="sortable-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Full Name') }}</th>
                                                    <th>{{ __('Email Address') }}</th>
                                                    <th>{{ __('Phone Number') }}</th>
                                                    <th>{{ __(' Designation') }}</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="ui-sortable" data-repeater-item>
                                                <tr>
                                                    <td width="25%" class="form-group">
                                                        <input type="text" class="form-control contact_name"
                                                            name="contact_name">
                                                    </td>
                                                    <td width="25%">
                                                        <input type="email" class="form-control contact_email"
                                                            name="contact_email">
                                                    </td>
                                                    <td width="25%">
                                                        <input type="number" class="form-control contact_phone"
                                                            name="contact_phone">
                                                    </td>
                                                    <td width="25%">
                                                        <input type="text" class="form-control contact_designation"
                                                            name="contact_designation">
                                                    </td>
                                                    <td>
                                                        <a href="javascript:;"
                                                            class="ti ti-trash text-white action-btn bg-danger p-3 desc_delete"
                                                            data-repeater-delete></a>
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



    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}



<script>
    $(document).ready(function() {

        var userType = @json(Auth::user()->type);
        var userDetail = @json($userDetail);


        if (userType === 'super admin') {
            // $.ajax({
            //     url: "{{ route('get.country') }}",
            //     type: "GET",
            //     success: function(result) {
            //         $.each(result.data, function(key, value) {
            //             $("#country").append('<option value="' + value.id + '">' + value
            //                 .name.charAt(0).toUpperCase() + value.name.slice(1) +
            //                 "</option>");
            //         });
            //         $("#country").val("236");
            //         $("#country").trigger("change");

            //     },
            // });

        } else {

            // $.ajax({
            //     url: "{{ route('get.country') }}",
            //     type: "GET",
            //     success: function(result) {
            //         $.each(result.data, function(key, value) {
            //             $("#country").append('<option value="' + value.id + '">' + value
            //                 .name.charAt(0).toUpperCase() + value.name.slice(1) +
            //                 "</option>");
            //         });
            //         $("#country").val(userDetail.country);
            //         $("#country").trigger("change");
            //         $("#country").prop("disabled", true);


            //     },
            // });


        }




        $("#country").on("change", function() {
            var country_id = this.value;
            $("#timezone").html("");
            $.ajax({
                url: "{{ route('get.timezone') }}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(result) {

                    if (result) {
                        $.each(result, function(key, value) {
                            $("#timezone").append('<option value="' + value.id +
                                '">' +
                                '(UTC ' + value.utc_offset + ') ' + value
                                .timezone.charAt(0).toUpperCase() + value
                                .timezone.slice(1) +
                                '</option>');
                        });
                    } else {
                        // Handle the case where no timezones were returned or an error occurred
                        $("#timezone").append(
                            '<option value="">No timezones available</option>');
                    }
                },
            });
        });


        $("#home_country").on("change", function() {
            var country_id = this.value;
            $("#home_state").html("");
            $.ajax({
                url: "{{ route('get.state') }}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(result) {
                    $.each(result.data, function(key, value) {
                        if (value) {
                            $("#home_state").append('<option value="' + value.id +
                                '">' +
                                value.name + "</option>");
                        }

                    });

                    $("#home_city").html('<option value="">Select State First</option>');
                },
            });
        });

        $("#state").on("change", function() {
            var state_id = this.value;

            $("#city").html("");
            $.ajax({
                url: "{{ route('get.city') }}",
                type: "POST",
                data: {
                    state_id: state_id,
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(result) {
                    $.each(result.data, function(key, value) {
                        $("#city").append('<option value="' + value.id + '">' +
                            value.name.charAt(0).toUpperCase() + value.name
                            .slice(1) + "</option>");
                    });
                },
            });
        });

        // $("#home_state").on("change", function() {
        //     var state_id = this.value;
        //     $("#home_city").html("");
        //     $.ajax({
        //         url: "{{ route('get.city') }}",
        //         type: "POST",
        //         data: {
        //             state_id: state_id,
        //             _token: "{{ csrf_token() }}",
        //         },
        //         dataType: "json",
        //         success: function(result) {
        //             $.each(result.data, function(key, value) {
        //                 $("#home_city").append('<option value="' + value.id + '">' +
        //                     value.city + "</option>");
        //             });
        //         },
        //     });
        // });
    });
</script>


<script>
    $(document).ready(function() {

        $('.attorney_form').hide();
        $('.user_form').hide();
        $('.assign_cases').hide();

        // When a radio button is clicked
        $('input[type=radio][name=type]').change(function() {
            // Hide both forms
            $('.attorney_form').hide();
            $('.user_form').hide();
            $('.assign_cases').hide();

            // Check which radio button is selected
            var selectedType = $(this).val();

            // Show the corresponding form based on the selected radio button
            if (selectedType === 'attorney') {
                $('.attorney_form').show();
            } else if (selectedType === 'co admin') {
                $('.user_form').hide();
            } else {
                $('.user_form').show();
            }

            if (selectedType === 'team' || selectedType === 'attorney') {
                $('.assign_cases').show();
            }
        });


        $.ajax({
            url: "{{ route('get.country') }}",
            type: "GET",
            success: function(result) {
                $.each(result.data, function(key, value) {
                    $("#home_country").append('<option value="' + value.id + '">' +
                        value
                        .name + "</option>");

                });
            },
        });


        $("#country").on("change", function() {
            var country_id = this.value;

            $("#state").html("");
            $.ajax({
                url: "{{ route('get.state') }}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(result) {
                    $.each(result.data, function(key, value) {
                        $("#state").append('<option value="' + value.id + '">' +
                            value.name + "</option>");
                    });
                    $("#city").html('<option value="">Select State First</option>');
                },
            });
        });

        $("#home_country").on("change", function() {
            var country_id = this.value;
            $("#home_state").html("");
            $.ajax({
                url: "{{ route('get.state') }}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(result) {
                    $.each(result.data, function(key, value) {
                        $("#home_state").append('<option value="' + value.id +
                            '">' +
                            value.name + "</option>");
                    });
                    $("#home_city").html('<option value="">Select State First</option>');
                },
            });
        });

        $("#state").on("change", function() {
            var state_id = this.value;
            $("#city").html("");
            $.ajax({
                url: "{{ route('get.city') }}",
                type: "POST",
                data: {
                    state_id: state_id,
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(result) {
                    $.each(result.data, function(key, value) {
                        $("#city").append('<option value="' + value.id + '">' +
                            value.name + "</option>");
                    });
                },
            });
        });

        $("#home_state").on("change", function() {
            var state_id = this.value;
            $("#home_city").html("");
            $.ajax({
                url: "{{ route('get.city') }}",
                type: "POST",
                data: {
                    state_id: state_id,
                    _token: "{{ csrf_token() }}",
                },
                dataType: "json",
                success: function(result) {
                    $.each(result.data, function(key, value) {
                        $("#home_city").append('<option value="' + value.id + '">' +
                            value.name + "</option>");
                    });
                },
            });
        });

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

    });
</script>

<script src="{{ asset('public/assets/js/jquery-ui.js') }}"></script>
<script src="{{ asset('public/assets/js/repeater.js') }}"></script>


{{-- <script>
    var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater tbody").sortable({
                handle: '.sort-handler'
            });
            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
                },
                show: function() {
                    $(this).slideDown();
                    var file_uploads = $(this).find('input.multi');
                    if (file_uploads.length) {
                        $(this).find('input.multi').MultiFile({
                            max: 3,
                            accept: 'png|jpg|jpeg',
                            max_size: 2048
                        });
                    }
                    if ($('.select2').length) {
                        $('.select2').select2();
                    }

                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        if ($('.disc_qty').length < 6) {
                            $(".add-row").show();

                        }
                        $(this).slideUp(deleteElement);
                        $(this).remove();

                        var inputs = $(".amount");
                        var subTotal = 0;
                        for (var i = 0; i < inputs.length; i++) {
                            subTotal = parseFloat(subTotal) + parseFloat($(inputs[i]).html());
                        }
                        $('.subTotal').html(subTotal.toFixed(2));
                        $('.totalAmount').html(subTotal.toFixed(2));
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });
            var value = $(selector + " .repeater").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }

        }

        $(".add-row").on('click',function(event){
        
            var $length = $('.disc_qty').length;
            if ($length == 5) {
                $(this).hide();
            }
        });

        $(".desc_delete").on('click',function(event) {

            var $length = $('.disc_qty').length;
        });
</script> --}}
