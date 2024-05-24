
@php
$setting = App\Models\Utility::settings();
$setting_currency = $setting['site_currency'] ?? 'USD';
@endphp

{{ Form::open(array('url' => 'plans', 'enctype' => "multipart/form-data")) }}
    <div class="modal-body">
        <div class="row">
            <input disable type="hidden" name="request_by" value="company">
            <div class="form-group col-md-6">
                {{ Form::label('duration', __('Duration'),['class'=>'col-form-label'])}}
                {!! Form::select('duration', $arrDuration, null,array('class' => 'form-select','required'=>'required')) !!}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('price_range', __('Price Range'), ['class' => 'col-form-label']) }}
                {{ Form::text('price_range', null, ['class' => 'form-control', 'placeholder' => __('Enter Price Range (e.g., 3000-4000)'), 'required' => 'required', 'pattern' => '^\d+-\d+$']) }}
            </div>
            
            
            <div class="form-group col-md-6">
                {{Form::label('max_users',__('Maximum Users'),['class'=>'col-form-label'])}}
                {{Form::number('max_users', null, array('class'=>'form-control','placeholder'=>__('Enter Maximum Users'),'required'=>'required'))}}
            </div>
          
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Request')}}" class="btn btn-primary ms-2">
    </div>
{{ Form::close() }}

<script>
        var currency = "{{ $setting_currency }}"; 
        var placeholderText = "100 " + currency + " - 100 " + currency; 
        document.querySelector('input[name="price_range"]').placeholder = placeholderText;
</script>
