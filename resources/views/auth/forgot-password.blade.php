@extends('layouts.guest')

@section('page-title')
    {{__('Forget password')}}
@endsection
@section('auth-lang')
<select name="language" id="language" class="btn btn-light-primary dropdown-toggle custom_btn ms-2 me-2 language_option_bg"
    onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
    @foreach(App\Models\Utility::languages() as $code => $language)
        <option class="dropdown-item" @if($lang == $code) selected @endif value="{{ route('password.request',$code) }}">{{ ucFirst($language)}}</option>
    @endforeach
</select>
@endsection
@section('content')
    <div class="">
        <h2 class="mb-3 f-w-600">{{ __('Forgot Password') }}</h2>
        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <span class="text-danger">{{$error}}</span>
            @endforeach
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <h5 for="" class="form-label">{{ __('Email') }}</h5>
                <x-input id="email" class="form-control" type="email" name="email" :value="old('email')" required
                    autofocus placeholder="{{ __('Enter email Address') }}" />
            </div>
            {!! Form::hidden('type', 'admin') !!}
            <button class="btn btn-primary btn-block mt-3 w-100" type="submit">
                {{ __('Email Password Reset Link') }}
            </button>
            <div class="mt-3 text-center">
                <a class="underline text-gray-600 hover:text-gray-900"
                    href="{{ route('login') }}">
                    {{ __('Back to login') }}
                </a>
            </div>
        </form>
    </div>
@endsection
