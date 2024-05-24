{{ Form::model($doc,['route' => ['documents.update',$doc->id], 'method' => 'put', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('cases', __('Cases'), ['class' => 'form-label']) !!}
                {!! Form::select('cases[]', $cases, $my_cases, ['class' => 'form-control multi-select', 'id' => 'choices-multiple', 'multiple','data-role'=>'tagsinput']) !!}

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('term', __('Term'), ['class' => 'form-label']) }}
                {{ Form::text('term', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('type', __('Type'),['class'=>'form-label']) }}
                {{ Form::select('type', $types, null, array('class' => 'form-control','required'=>'required')) }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('judgement_date', __('Judgement Date'), ['class' => 'form-label']) }}
                {{ Form::date('judgement_date', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('expiry_date', __('Expiry Date'), ['class' => 'form-label']) }}
                {{ Form::date('expiry_date', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('purpose', __('Purpose'), ['class' => 'form-label']) }}
                {{ Form::text('purpose', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('first_party', __('First Party'), ['class' => 'form-label']) }}
                {{ Form::text('first_party', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('second_party', __('Second Party'), ['class' => 'form-label']) }}
                {{ Form::text('second_party', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('headed_by', __('Headed By'), ['class' => 'form-label']) }}
                {{ Form::text('headed_by', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'required' => 'required', 'rows' => '1','maxlength'=>"250"]) }}
            </div>
        </div>

        <div class="col-md-6 choose-files mt-4">
            <label for="profile_pic">
                <div class="bg-primary profile_update" style="max-width: 100% !important;"> <i
                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                </div>
                <input type="file" class="file" name="file" id="profile_pic" multiple >
            </label>
        </div>

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>

{{ Form::close() }}
