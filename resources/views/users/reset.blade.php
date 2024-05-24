{{Form::model($employee,array('route' => array('member.change.password', $employee->id), 'method' => 'post')) }}
<div class="modal-body">
<div class="form-group col-md-12">

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
<div class="form-group col-md-12">
    {{-- {{ Form::label('confirm_password', __('Confirm Password'),['class'=>'form-label']) }}
    <input id="password-confirm" type="password" class="form-control" name="confirm_password" required
        autocomplete="new-password"> --}}

        <label for="password" class="form-label">{{ __('Confirm Password') }}</label>
        <div class="input-group" style="position: relative;">
        <input class="form-control" data-indicator="pwindicator" name="confirm_password" type="password" id="confirm_password"
        required autocomplete="password"
        placeholder="{{ __('Enter New Password') }}">
            <div class="input-group-append  password_eye_wraappe">
                <span style="height: 100%;" class="input-group-text  password_eye password-toggle2" onclick="togglePasswordVisibility2('confirm_password')">
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
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal"> {{ __('Close') }} </button>
    {{Form::submit(__('Reset'),array('class'=>'btn btn-primary'))}}
</div>

{{ Form::close() }}
</div>
