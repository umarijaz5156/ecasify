@extends('layouts.app')
@if($user->id == Auth::user()->id)
    @section('page-title', __('Profile'))
@else
    @section('page-title', __('Edit Member'))
@endif


@php
    $logo = asset('storage/uploads/profile/');

    $settings = App\Models\Utility::settings();
@endphp
@section('breadcrumb')
@if($user->id == Auth::user()->id)
    <li class="breadcrumb-item">{{ __('Profile') }}</li>
@else
    <li class="breadcrumb-item">{{ __('Edit Member') }}</li>
@endif
@endsection

@section('content')
    <div class="row p-0 g-0">

        <div class="col-sm-12">
            <div class="row g-0">
                <div class="col-xl-3 border-end border-bottom">
                    <div class="card shadow-none bg-transparent sticky-top" style="top:70px">
                        <div class="list-group list-group-flush rounded-0" id="useradd-sidenav">
                            <a href="#useradd-1"
                                class="list-group-item list-group-item-action">{{ __('Information') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @if($user->id == Auth::user()->id)
                            <a href="#useradd-2"
                                class="list-group-item list-group-item-action">{{ __('Change Password') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @endif

                        </div>
                    </div>
                </div>


                <div class="col-xl-9">
                    <div id="useradd-1" class="card  shadow-none rounded-0 border-bottom">

                            <div class="card-header">
                                @if (Auth::user()->id == 1)
                                    <h5 class="mb-0">{{ __('Personal Information') }}</h5>
                                @else
                                    <h5 class="mb-0">{{ __('Member Information') }}</h5>

                                @endif
                            </div>
                            <div class="card-body">

                                {{ Form::model($user, ['route' => ['users.update',$user->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                                <div class=" setting-card">
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-6 col-md-6">
                                            <div class="card-body text-center">
                                                <div class="logo-content">

                                                    <a href="{{ !empty($user->avatar) ? $logo .'/'. $user : $logo . '/avatar.png' }}" target="_blank">
                                                            <img src="{{ !empty($user->avatar) ? $logo .'/'. $user->avatar : $logo . '/avatar.png' }}" width="100" id="profile">
                                                        </a>
                                                </div>
                                                <div class="choose-files mt-4">
                                                    <label for="profile_pic">
                                                        <div class="bg-primary profile_update"
                                                            style="max-width: 100% !important;"> <i
                                                                class="ti ti-upload px-1"></i>{{__('Choose file here')}}
                                                        </div>
                                                        <input type="file" class="file" name="profile" accept="image/*"
                                                            id="profile_pic"
                                                            onchange="document.getElementById('profile').src = window.URL.createObjectURL(this.files[0])">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-sm-6 col-md-6">
                                            <div class="card-body">

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                         <label class="col-form-label text-dark">{{ __('Name') }}</label>
                                            <input class="form-control " name="name" type="text" id="fullname"
                                                placeholder="{{ __('Enter Your Name') }}" value="{{ $user->name }}"
                                                required autocomplete="name">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="email"
                                                class="col-form-label text-dark">{{ __('Email') }}</label>
                                            <input class="form-control " name="email" type="text" id="email"
                                                placeholder="{{ __('Enter Your Email Address') }}"
                                                value="{{ $user->email }}" required autocomplete="email">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (Auth::user()->type == 'advocate')
                                    <div class="row card-body pt-0 pb-0">

                                        <div class="col-md-6 col-sm-6">
                                            <div class="form-group">
                                                {{ Form::label('phone_number', __('Phone Number'), ['class' => 'col-form-label']) }}
                                                {{ Form::number('phone_number', $advocate->phone_number, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('age', __('Age'), ['class' => 'col-form-label']) }}
                                                {{ Form::number('age',$advocate->age, ['class' => 'form-control']) }}
                                            </div>
                                        </div>


                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('father_name', __('Father\'s Name'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('father_name', $advocate->father_name, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('company_name', __('Company Name'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('company_name', $advocate->company_name, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('website', __('Website'), ['class' => 'col-form-label']) }}
                                                {{ Form::url('website', $advocate->website, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('tin', __('Tax Identification Number'), ['class' => 'col-form-label']) }}
                                                {{ Form::number('tin', $advocate->tin, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('gstin', __('GST Identification Number (GSTIN)'), ['class' =>
                                                'col-form-label']) }}
                                                {{ Form::text('gstin', $advocate->gstin, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('pan_number', __('Permanent Account Number (PAN)'), ['class' =>
                                                'col-form-label']) }}
                                                {{ Form::text('pan_number', $advocate->pan_number, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('hourly_rate', __('Hourly Rate') .' ('.$settings['site_currency'] .')', ['class' => 'col-form-label']) }}
                                                {{ Form::number('hourly_rate', $advocate->hourly_rate, ['class' => 'form-control']) }}
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
                                                {{ Form::text('ofc_address_line_1', $advocate->ofc_address_line_1, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('ofc_address_line_2', __('Address Line 2'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('ofc_address_line_2', $advocate->ofc_address_line_2, ['class' => 'form-control']) }}
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
                                                    <option value="{{$state->id}}" {{$state->id == $advocate->getSelectedState($advocate->ofc_state) ? 'selected' : ''}}>{{
                                                        $state->region }}</option>
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
                                                        $advocate->getSelectedCity($advocate->ofc_city) ? 'selected' : ''}}>{{ $city->city
                                                        }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('zip_code', __('Zip/Postal Code'), ['class' => 'col-form-label']) }}
                                                {{ Form::number('ofc_zip_code', $advocate->ofc_zip_code, ['class' => 'form-control']) }}
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
                                                {{ Form::text('home_address_line_1', $advocate->home_address_line_1, ['class' => 'form-control']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('home_address_line_2', __('Address Line 2'), ['class' => 'col-form-label'])
                                                }}
                                                {{ Form::text('home_address_line_2', $advocate->home_address_line_2, ['class' => 'form-control']) }}
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
                                                        $state->region }}</option>
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
                                                        $advocate->getSelectedCity($advocate->home_city) ? 'selected' : ''}}>{{ $city->city
                                                        }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::label('zip_code', __('Zip/Postal Code'), ['class' => 'col-form-label']) }}
                                                {{ Form::number('home_zip_code', $advocate->home_zip_code, ['class' => 'form-control']) }}
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

                                                                <div class="col-sm-6 d-flex justify-content-end align-items-center col-6">
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

                                        <div class="col-lg-12 text-end mt-2">
                                            <input type="submit" value="{{ __('Save Changes') }}"
                                                class="btn btn-print-invoice  btn-primary m-r-10">
                                        </div>
                                    </div>
                                @else
                                    <div class="row card-body">
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="mobile_number"
                                                    class="col-form-label text-dark">{{ __('Mobile Number') }}</label>
                                                <input class="form-control " name="mobile_number" type="tel"
                                                    id="mobile_number" placeholder="{{ __('Enter Your Mobile Number') }}"
                                                    value="{{ !empty($user_detail->mobile_number) ? $user_detail->mobile_number : '' }}"
                                                    autocomplete="mobile_number">
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="address"
                                                    class="col-form-label text-dark">{{ __('Address') }}</label>
                                                <input class="form-control " name="address" type="text" id="address"
                                                    placeholder="{{ __('Enter Your Address') }}"
                                                    value="{{ !empty($user_detail->address) ? $user_detail->address : '' }}"
                                                    autocomplete="address">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('country', __('Country'), ['class' => 'col-form-label']) }}
                                                <select class="form-control" id="country" name="country" required>
                                                    <option value="">{{ __('Select Country') }}</option>
                                                    @foreach ($countries as $country)
                                                    <option value="{{  $country->id }}" {{$country->id == $user_detail->country  ? 'selected' : '' }}>{{  $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                            
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
                                                <select class="form-control" id="state" name="state" required>
                                                    <option value="">{{ __('Select State') }}</option>
                                                    @foreach ($states as $state)
                                                    <option value="{{  $state->id }}" {{$state->id == $user_detail->state  ? 'selected' : '' }}>{{  $state->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                            
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
                                                <select class="form-control" id="city" name="city" required>
                                                    <option value="">{{ __('Select City') }}</option>
                                                    @foreach ($cities as $city)
                                                    <option value="{{  $city->id }}" {{$city->id == $user_detail->city  ? 'selected' : '' }}>{{  $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {{ Form::label('timezone', __('Timezone'), ['class' => 'col-form-label']) }}
                                                <select class="form-control" id="timezone" name="timezone">
                                                    <option value="">{{ __('Select Timezone') }}</option>
                                                    @foreach ($timezones as $timezone)
                                                    <option value="{{  $timezone->id}}" {{$timezone->id == $user_detail?->timezone  ? 'selected' : '' }}>(UTC {{ $timezone->utc_offset }}) {{  $timezone->timezone}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="city"
                                                    class="col-form-label text-dark">{{ __('City') }}</label>
                                                <input class="form-control " name="city" type="text" id="city"
                                                    placeholder="{{ __('Enter Your City') }}"
                                                    value="{{ !empty($user_detail->city) ? $user_detail->city : '' }}"
                                                    autocomplete="city">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="state"
                                                    class="col-form-label text-dark">{{ __('State') }}</label>
                                                <input class="form-control " name="state" type="text" id="state"
                                                    placeholder="{{ __('Enter Your State') }}"
                                                    value="{{ !empty($user_detail->state) ? $user_detail->state : '' }}"
                                                    autocomplete="state">
                                            </div>
                                        </div> --}}

                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="zip_code"
                                                    class="col-form-label text-dark">{{ __('Zip/Postal Code') }}</label>
                                                <input class="form-control " name="zip_code" type="number" id="zip_code"
                                                    placeholder="{{ __('Enter Your Zip/Postal Code') }}"
                                                    value="{{ !empty($user_detail->zip_code) ? $user_detail->zip_code : '' }}"
                                                    autocomplete="zip_code">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="landmark"
                                                    class="col-form-label text-dark">{{ __('Landmark') }}</label>
                                                <input class="form-control " name="landmark" type="text" id="landmark"
                                                    placeholder="{{ __('Enter Your Landmark') }}"
                                                    value="{{ !empty($user_detail->landmark) ? $user_detail->landmark : '' }}"
                                                    autocomplete="landmark">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label for="about"
                                                    class="col-form-label text-dark">{{ __('Brief About Yourself') }}</label>
                                                <input class="form-control " name="about" type="text" id="about"
                                                    placeholder="{{ __('Enter Your About Yourself') }}"
                                                    value="{{ !empty($user_detail->about) ? $user_detail->about : '' }}"
                                                    autocomplete="about">
                                            </div>
                                        </div>
                                        <div class="col-lg-12 text-end">
                                            <input type="submit" value="{{ __('Save Changes') }}"
                                                class="btn btn-print-invoice  btn-primary m-r-10">
                                        </div>
                                    </div>
                                @endif

                                </form>

                                {{ Form::close() }}
                            </div>

                    </div>
                    @if($user->id == Auth::user()->id)
                    <div id="useradd-2" class="card  shadow-none rounded-0 border-bottom">

                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Change Password') }}</h5>
                                <small> {{ __('Details about your member account password change') }}</small>
                            </div>
                            <div class="card-body">
                                {{ Form::open(['route' => ['member.change.password', $user->id], 'method' => 'POST']) }}

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password" class="form-label">{{ __('Password') }}</label>
                                            <div class="input-group" style="position: relative;">
                                            <input class="form-control" data-indicator="pwindicator" name="password" type="password" id="password"
                                            required autocomplete="password"
                                            placeholder="{{ __('Enter New Password') }}">
                                                <div class="input-group-append  password_eye_wraappe">
                                                    <span style="height: 100%;" class="input-group-text  password_eye password-toggle" onclick="togglePasswordVisibility('password')">
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
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password_confirmation" class="form-label">{{ __('Password Confirmation') }}</label>
                                            <div class="input-group">
                                                <input class="form-control" name="confirm_password" type="password"
                                                id="password_confirmation" required autocomplete="confirm_password"
                                                placeholder="{{ __('Confirm New Password') }}">
                                                <div class="input-group-append password_eye_wraappe">
                                                    <span style="height: 100%;"  class="input-group-text password_eye password-toggle2" onclick="togglePasswordVisibility2('password_confirmation')">
                                                        <i class="far fa-eye-slash"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            @error('password_confirmation')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            <div id="password_confirmation" class="pwindicator">
                                                <div class="bar"></div>
                                                <div class="label"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer pr-0">
                                    {{ Form::submit(__('Update'), ['class' => 'btn  btn-primary']) }}
                                </div>
                                {{ Form::close() }}
                            </div>

                    </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
@endsection


@push('custom-script')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,

        })
        $(".list-group-item").on('click',function() {
            $('.list-group-item').filter(function() {
                return this.href == id;
            }).parent().removeClass('text-primary');
        });
    </script>


<script>
    $(document).ready(function() {
        
            $.ajax({
                url: "{{ route('get.country') }}",
                type: "GET",
                success: function(result) {

                    $.each(result.data, function(key, value) {
                   

                        $("#country").append('<option value="' + value.id + '" '+ selected +' >' + value.name.charAt(0).toUpperCase() + value.name.slice(1) + "</option>");
                        
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
                                value.name.charAt(0).toUpperCase() + value.name.slice(1) + "</option>");
                        });
                        $("#city").html('<option value="">Select State First</option>');
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
                                value.name.charAt(0).toUpperCase() + value.name.slice(1) + "</option>");
                        });
                    },
                });
            });
            
        });
</script>

@if(Auth::user()->type == 'advocate')
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
                            .country + "</option>");

                        $("#home_country").append('<option value="' + value.id + '" '+ selected_home +'>' + value
                            .country + "</option>");
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
                                value.region + "</option>");
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
                                value.region + "</option>");
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
                                value.city + "</option>");
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
                                value.city + "</option>");
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
@endif
@endpush
