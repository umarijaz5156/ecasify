@extends('layouts.guest')
@section('page-title')
    {{ __('Register') }}
@endsection
@php
    $logo = \App\Models\Utility::get_file('uploads/logo');
@endphp
@push('custom-scripts')
    @if (env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
@section('auth-lang')
    <li class="nav-item ">
        <select name="language" id="language"
            class="btn btn-light-primary dropdown-toggle custom_btn ms-2 me-2 language_option_bg"
            onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
            @foreach (App\Models\Utility::languages() as $code => $language)
                <option class="dropdown-item" @if ($lang == $code) selected @endif
                    value="{{ route('register', $code) }}">{{ ucFirst($language) }}</option>
            @endforeach
        </select>
    </li>
@endsection


@section('content')
    <div class="">
        <h2 class="mb-3 f-w-600">{{ __('Register Your Law Firm') }}</h2>
    </div>
    <form method="POST" action="{{ route('register') }}">
        @if (session('status'))
            <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                {{ __('Email SMTP settings does not configured so please contact to your site admin.') }}
            </div>
        @endif
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="name" class="form-label">{{ __('Company Name') }}</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                        name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="email" class="form-label">{{ __('Company Email') }}</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" type="email"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="invalid-feedback">
                        {{ __('Please fill in your email') }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {{ Form::label('country', __('Country'), ['class' => 'col-form-label']) }}
                    <select class="form-control" id="country" name="country" required>
                        <option value="">{{ __('Select Country') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ $country->name == $selectedCountry ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
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
                <div class="form-group mb-3">
                    {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
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
                <div class="form-group mb-3">
                    {{ Form::label('timezone', __('Timezone'), ['class' => 'col-form-label']) }}
                    <select class="form-control" id="timezone" name="timezone">
                        <option value="">{{ __('Select Timezone') }}</option>
                        @foreach ($timezones as $timezone)
                            <option value="{{ $timezone->id }}"
                                {{ $timezone->timezone == $selectedTimezone ? 'selected' : '' }}>(UTC
                                {{ $timezone->utc_offset }}) {{ $timezone->timezone }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <div class="input-group" style="position: relative;">
                        <input id="password" type="password" data-indicator="pwindicator"
                            class="form-control pwstrength @error('password') is-invalid @enderror" name="password" required
                            autocomplete="new-password">
                        <div class="input-group-append  password_eye_wraappe">
                            <span style="height: 100%;" class="input-group-text  password_eye password-toggle"
                                onclick="togglePasswordVisibility('password')">
                                <i class="far fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div id="pwindicator" class="pwindicator">
                        <div class="bar"></div>
                        <div class="label"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('Password Confirmation') }}</label>
                    <div class="input-group">
                        <input id="password_confirmation" type="password" data-indicator="password_confirmation"
                            class="form-control pwstrength @error('password_confirmation') is-invalid @enderror"
                            name="password_confirmation" required autocomplete="new-password">
                        <div class="input-group-append password_eye_wraappe">
                            <span style="height: 100%;" class="input-group-text password_eye password-toggle2"
                                onclick="togglePasswordVisibility2('password_confirmation')">
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

            @if (env('RECAPTCHA_MODULE') == 'yes')
                <div class="form-group mb-3">
                    {!! NoCaptcha::display() !!}
                    @error('g-recaptcha-response')
                        <span class="small text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            @endif

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-block mt-2">{{ __('Register') }}</button>
            </div>

        </div>
        <p class="my-4 text-center">{{ __('Already have an account?') }} <a
                href="{{ route('login', !empty(\Auth::user()->lang) ? \Auth::user()->lang : 'en') }}"
                class="text-primary">{{ __('Login') }}</a></p>

    </form>
@endsection

@push('custom-scripts')
    <script>
        $(document).ready(function() {

            $.ajax({
                url: "{{ route('get.country') }}",
                type: "GET",
                success: function(result) {

                    $.each(result.data, function(key, value) {


                        $("#country").append('<option value="' + value.id + '" ' + selected +
                            ' >' + value
                            .name + "</option>");

                    });
                },
            });


            $("#country").on("change", function() {
                var country_id = this.value;
                console.log(country_id);
                $("#state").html("");
                $("#timezone").html("");
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

        });
    </script>
@endpush
