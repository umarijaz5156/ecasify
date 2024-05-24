{{ Form::open(['route' => 'hearing.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
@php
    $setting = App\Models\Utility::settings();
@endphp
<input type="hidden" value="{{ $case_id }}" name="case_id">
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                {!! Form::label('hearType', __('Hearing Type'), ['class' => 'form-label']) !!}
                {!! Form::select('hearing_type', $hearing_type, null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col-md-12">
                {!! Form::label('date', __('Hearing date'), ['class' => 'form-label']) !!}
                {!! Form::date('date', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-12">
                {!! Form::label('time', __('Time'), ['class' => 'form-label']) !!}
                {!! Form::time('time', null, ['class' => 'form-control']) !!}
            </div>
            @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
                <div class="form-group col-md-12" >
                    <label for="is_check" class="form-check-label">{{__('Synchronize in Google Calendar')}}</label>
                    <div class="form-check form-switch pt-2 custom-switch-v1">
                        <input id="switch-shadow" class="form-check-input" value="1" name="is_check" type="checkbox" id="is_check">
                        <label class="form-check-label" for="switch-shadow"></label>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn btn-primary ms-2">
    </div>
{{Form::close()}}

