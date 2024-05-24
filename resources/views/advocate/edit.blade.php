@extends('layouts.app')

@section('page-title', __('Edit Attorneys'))


@section('breadcrumb')
    <li class="breadcrumb-item">{{ __(' Edit Attorneys') }}</li>
@endsection
@php
    $settings = App\Models\Utility::settings();
@endphp
@section('content')

{{ Form::model($advocate,['route' => ['advocate.update',$advocate->id],'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-lg-10">
        <div class="card shadow-none rounded-0 border">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-sm-6 ">
                        <div class="form-group">

                            {{ Form::label('name', __('Full Name'), ['class' => 'col-form-label']) }}
                            {{ Form::text('name',$advocate->getAdvUser($advocate->user_id)->name, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            {{ Form::label('email', __('Email Address'), ['class' => 'col-form-label']) }}
                            {{ Form::text('email', $advocate->getAdvUser($advocate->user_id)->email, ['class' => 'form-control']) }}
                        </div>
                    </div>


                    <div class="col-md-6 col-sm-6">
                        <div class="form-group">
                            {{ Form::label('phone_number', __('Phone Number'), ['class' => 'col-form-label']) }}
                            {{ Form::number('phone_number', null, ['class' => 'form-control']) }}
                        </div>
                    </div>

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
                            {{ Form::label('gstin', __('GST Identification Number (GSTIN)'), ['class' =>
                            'col-form-label']) }}
                            {{ Form::text('gstin', null, ['class' => 'form-control']) }}
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('pan_number', __('Permanent Account Number (PAN)'), ['class' =>
                            'col-form-label']) }}
                            {{ Form::text('pan_number', null, ['class' => 'form-control']) }}
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('hourly_rate', __('Hourly Rate') .' ('.$settings['site_currency'] .')', ['class' => 'col-form-label']) }}
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

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('country', __('Country'), ['class' => 'col-form-label']) }}
                            <select class="form-control" id="country" name="ofc_country">
                                <option value="">{{ __('Select Country') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
                            <select class="form-control" id="state" name="ofc_state">
                                <option value="">{{ __('Select State') }}</option>
                                @foreach ($advocate->getStateByCountry($advocate->ofc_country) as $state)
                                <option value="{{$state->id}}" {{$state->id ==
                                    $advocate->getSelectedState($advocate->ofc_state) ? 'selected' : ''}}>{{
                                    $state->name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
                            <select class="form-control" id="city" name="ofc_city">
                                <option value="">{{ __('Select City') }}</option>
                                @foreach ($advocate->getCityByState($advocate->ofc_state) as $city)
                                <option value="{{$city->id}}" {{$city->id ==
                                    $advocate->getSelectedCity($advocate->ofc_city) ? 'selected' : ''}}>{{ $city->name
                                    }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

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
                            {{ Form::label('home_address_line_1', __('Address Line 1'), ['class' => 'col-form-label'])
                            }}
                            {{ Form::text('home_address_line_1', null, ['class' => 'form-control']) }}
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('home_address_line_2', __('Address Line 2'), ['class' => 'col-form-label'])
                            }}
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
                                @foreach ($advocate->getStateByCountry($advocate->home_country) as $state)
                                <option value="{{$state->id}}" {{$state->id ==
                                    $advocate->getSelectedState($advocate->home_state) ? 'selected' : ''}}>{{
                                    $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
                            <select class="form-control" id="home_city" name="home_city">
                                <option value="">{{ __('Select City') }}</option>
                                @foreach ($advocate->getCityByState($advocate->home_state) as $city)
                                <option value="{{$city->id}}" {{$city->id ==
                                    $advocate->getSelectedCity($advocate->home_city) ? 'selected' : ''}}>{{ $city->name
                                    }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            {{ Form::label('zip_code', __('Zip/Postal Code'), ['class' => 'col-form-label']) }}
                            {{ Form::number('home_zip_code', null, ['class' => 'form-control']) }}
                        </div>
                    </div>

                    <input type="hidden" value="{{$contacts}}" name="old_contacts">

                    <div class="col-md-12 repeater" data-value='{!! json_encode($contacts) !!}'>

                        <div class="row">
                            <div class="col-12">
                                <div class="card my-3 shadow-none rounded-0 border">
                                    <div class="card-header">
                                        <div class="row gy-3 flex-grow-1">
                                            <div class="col-sm-6 d-flex align-items-center col-6">
                                                <h5 class="card-header-title">{{ __('Point of Contacts') }}</h5>
                                            </div>

                                            <div
                                                class="col-sm-6 d-flex justify-content-end align-items-center col-6">
                                                <a data-repeater-create=""
                                                    class="btn btn-primary btn-sm add-row text-white"
                                                    data-toggle="modal" data-target="#add-bank">
                                                    <i class="fas fa-plus"></i> {{ __('Add Row') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style">
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
                                                            <input type="text" class="form-control contact_phone"
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
    <div class="col-md-1"></div>
    <div class="col-md-1"></div>
    <div class="col-lg-10">
        <div class="card shadow-none rounded-0 border ">
            <div class="card-body p-2">
                <div class="form-group col-12 d-flex justify-content-end col-form-label mb-0">

                    <a href="{{ route('advocate.index') }}" class="btn btn-secondary btn-light ms-3">{{ __('Cancel') }}</a>
                    <input type="submit" value="{{ __('Save') }}" class="btn btn-primary ms-2">
                </div>
            </div>
        </div>
    </div>

</div>
{{ Form::close() }}
<!-- [ Main Content ] end -->
@endsection


@push('custom-script')
<script>
    $(document).ready(function() {

            var get_selected = '{{!empty($advocate->ofc_country) ? $advocate->getCountryName($advocate->ofc_country) : $advocate->getCountryName(113)}}';
            var home_selected = '{{!empty($advocate->home_country) ? $advocate->getCountryName($advocate->home_country) : $advocate->getCountryName(113)}}';

            $.ajax({
                url: "{{ route('get.country') }}",
                type: "GET",
                success: function(result) {

                    $.each(result.data, function(key, value) {
                        if(value.id == get_selected){
                            var selected = 'selected';
                        }else{
                            var selected = '';
                        }

                        if(value.id == home_selected){
                            var selected_home = 'selected';
                        }else{
                            var selected_home = '';
                        }

                        $("#country").append('<option value="' + value.id + '" '+ selected +' >' + value
                            .name + "</option>");

                        $("#home_country").append('<option value="' + value.id + '" '+ selected_home +'>' + value
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
                            $("#home_state").append('<option value="' + value.id + '">' +
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
        });
</script>

<script src="{{ asset('public/assets/js/jquery-ui.js') }}"></script>
<script src="{{ asset('public/assets/js/repeater.js') }}"></script>
<script>
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
</script>
@endpush
