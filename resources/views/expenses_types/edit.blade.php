{{ Form::model($expenseType,['route' => ['expenses-types.update',$expenseType->id], 'method' => 'put']) }}
    <div class="modal-body">

        <div class="row">

            <div class="form-group col-md-12">
                {!! Form::label('name', __('Expens Title'), ['class' => 'form-label']) !!}
                {{ Form::text('name', $expenseType->name , ['class' => 'form-control ', 'required' => 'required','id'=>'name']) }}
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn btn-primary ms-2">
    </div>
{{Form::close()}}
