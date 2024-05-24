{{ Form::open(['route' => 'expenses-types.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
    <div class="modal-body">
        <div class="row">

            <div class="form-group col-md-12">
                {!! Form::label('name', __('Expense Title'), ['class' => 'form-label','id'=>'adv_label']) !!}
                {{ Form::text('name', null, ['class' => 'form-control ', 'required' => 'required']) }}
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn btn-primary ms-2">
    </div>
{{Form::close()}}
