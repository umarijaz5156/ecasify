<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Name:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->name }}</span> </dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Email:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->email }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Phone:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->phone_number }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Age:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->age }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Father Name:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->father_name }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Company Name:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->company_name }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Website:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->website }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Tax Identification Number:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->tin }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('GST Identification Number:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->gstin }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Permanent Account Number (PAN):') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->pan_number }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Hourly Rate:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $advocate->hourly_rate }}</span></dd>

                </dl>
            </div>

        </div>

    </div>
</div>
