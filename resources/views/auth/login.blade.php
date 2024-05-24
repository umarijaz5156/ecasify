@extends('layouts.guest')

@push('custom-scripts')
@if (env('RECAPTCHA_MODULE') == 'yes')
{!! NoCaptcha::renderJs() !!}
@endif
@endpush

@section('page-title')
{{ __('Login') }}
@endsection

@section('auth-lang')
<select name="language" id="language" class="btn btn-light-primary dropdown-toggle custom_btn ms-2 me-2 language_option_bg"
    onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
    @foreach(App\Models\Utility::languages() as $code => $language)
        <option class="dropdown-item" @if($lang == $code) selected @endif value="{{ route('login',$code) }}">{{ ucFirst($language)}}</option>
    @endforeach
</select>
@endsection

@section('content')
<div class="">
    <h2 class="mb-3 f-w-600">{{ __('Login') }}</h2>
</div>
{{ Form::open(['route' => 'login', 'method' => 'post', 'id' => 'loginForm']) }}
@csrf
<div class="">
    @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    <div class="form-group mb-3">
        <label for="email" class="form-label">{{ __('Email') }}</label>
        <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email"
            value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
        <div class="invalid-feedback" role="alert">{{ $message }}</div>
        @enderror
    </div>
   
        <div class="form-group mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <div class="input-group">
                <input class="form-control @error('password') is-invalid @enderror" id="password_confirmation" type="password"
                name="password" required autocomplete="current-password">
                <div class="input-group-append password_eye_wraappe">
                    <span style="height: 100%;"  class="input-group-text password_eye password-toggle3" onclick="togglePasswordVisibility3('password_confirmation')">
                        <i class="far fa-eye-slash"></i>
                    </span>
                </div>
            </div>
            @error('password')
            <div class="invalid-feedback" role="alert">{{ $message }}</div>
            @enderror
            <div id="password_confirmation" class="pwindicator">
                <div class="bar"></div>
                <div class="label"></div>
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
    <div class="form-group mb-4">
        @if (Route::has('password.request'))
        <a href="{{ route('password.request', $lang) }}" class="text-xs">{{ __('Forgot Your Password?') }}</a>
        @endif
    </div>
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-block mt-2" id="login_button">{{ __('Login') }}</button>
    </div>

    @if(App\Models\Utility::getValByName('signup_button')=='on')
        <p class="my-4 text-center">{{ __("Don't have an account?") }}
            <a href="{{route('register',$lang)}}" class="my-4 text-primary">{{__('Register')}}</a>
        </p>
    @endif

</div>
{{ Form::close() }}
@endsection

