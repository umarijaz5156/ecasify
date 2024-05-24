{{ Form::open(['route' => 'courts.store', 'method' => 'post']) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                {!! Form::label('', __('Name'), ['class' => 'form-label']) !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn btn-primary ms-2">
    </div>
{{Form::close()}}

<script>

</script>
