{{ Form::model($hearing,['route' => ['hearing.update',$hearing->id], 'method' => 'put']) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                {!! Form::label('hearType', __('Hearing Type'), ['class' => 'form-label']) !!}
                {!! Form::select('hearing_type', $hearing_types, $hearing_type->id, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group col-md-12">
                {!! Form::label('date', __('Hearing date'), ['class' => 'form-label']) !!}
                {!! Form::date('date', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-12">
                {!! Form::label('time', __('Time'), ['class' => 'form-label']) !!}
                {!! Form::time('time', null, ['class' => 'form-control']) !!}
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn btn-primary ms-2">
    </div>
{{Form::close()}}

