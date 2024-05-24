@php
$setting = App\Models\Utility::settings();
$setting_currency = $setting['site_currency'] ?? 'USD';
@endphp


{{Form::model($plan, array('route' => array('acceptCompany', $plan->id), 'method' => 'PUT', 'enctype' => "multipart/form-data")) }}
    <div class="modal-body">
       
        <div class="row">
            <div class="form-group col-md-6">
                {{Form::label('name',__('Name'),['class'=>'col-form-label'])}}
                {{Form::text('name', null, array('class'=>'form-control ','placeholder'=>__('Enter Plan Name'),'required'=>'required'))}}
            </div>
            <div class="form-group col-md-6">
                {{Form::label('price',__('Price'),['class'=>'col-form-label'])}}
                {{Form::number('price', null, array('class'=>'form-control','placeholder'=>__('Enter Plan Price')))}}
                <b>Price Range: {{ $plan->price_range  .' (' . $setting_currency . ')' }} </b>
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('duration', __('Duration'),['class'=>'col-form-label'])}}
                {!! Form::select('duration', $arrDuration, null,array('class' => 'form-select','required'=>'required')) !!}
            </div>
            <div class="form-group col-md-6">
                {{Form::label('max_users',__('Maximum Users'),['class'=>'col-form-label'])}}
                {{Form::number('max_users', null, array('class'=>'form-control','placeholder'=>__('Enter Maximum Users'),'required'=>'required'))}}
            </div>
            {{-- <div class="form-group col-md-6">
                {{Form::label('max_advocates',__('Maximum Advocates'),['class'=>'col-form-label'])}}
                {{Form::number('max_advocates', null, array('class'=>'form-control','placeholder'=>__('Enter Maximum
                Advocates'),'required'=>'required'))}}
            </div> --}}
            <div class="form-group col-md-12">
                {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
                <div id="description-fields">
                    <div class="input-group">
                        {!! Form::text('details[]', null, ['class' => 'form-control', 'placeholder' => __('Enter Plan Description')]) !!}
                        <div style="padding-left:5px" class="input-group-append m-auto">
                            <button class="btn btn-sm btn-success add-description" type="button">+</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Accept')}}" class="btn btn-primary ms-2">
    </div>
{{ Form::close() }}
<script>
    $(document).ready(function() {
        // Add Description Field
        $('#description-fields').on('click', '.add-description', function() {
            var template = `
                <div class="input-group mt-2">
                    {!! Form::text('details[]', null, ['class' => 'form-control', 'placeholder' => __('Enter Plan Description')]) !!}
                    <div style="padding-left:5px" class="input-group-append m-auto">
                        <button class="btn btn-sm btn-danger remove-description" type="button">-</button>
                    </div>
                </div>
            `;
            $(template).appendTo('#description-fields');
        });

        // Remove Description Field
        $('#description-fields').on('click', '.remove-description', function() {
            $(this).closest('.input-group').remove();
        });
    });
</script>
