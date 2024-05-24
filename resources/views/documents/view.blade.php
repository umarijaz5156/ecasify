@php
    $docfile = \App\Models\Utility::get_file('uploads/documents/');

@endphp
<div class="modal-body">
<div class="row">
    <div class="col-lg-12">

        <div class="">
            <dl class="row">

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Related Cases:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $cases }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Term:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->term }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Type:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->getDocType->name }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Judgement Date:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->judgement_date }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Expiry Date:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->expiry_date }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Purpose:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->purpose }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('First Party:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->first_party }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Second Party:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->second_party }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Description:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->description }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Headed by:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->headed_by }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Uploaded by:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ App\Models\User::getUser($doc->created_by)->name }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('File:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->file }}</span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Doc Size:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md">{{ $doc->doc_size }} MB</span></dd>



                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('View:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md"><a href="{{$docfile.$doc->file}}" target="_blank">{{__('Click here')}}</a></span></dd>

                <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Download:') }}</span></dt>
                <dd class="col-md-8"><span class="text-md"><a href="{{$docfile.$doc->file}}" target="_blank" download>{{__('Click here')}}</a></span></dd>


            </dl>
        </div>

    </div>

</div>
</div>
