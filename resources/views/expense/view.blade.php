<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">

            <div class="">
                <dl class="row">
                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Expense Title:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $expense->title }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Expense Type:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $expense->type }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Date:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $expense->date }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Money Spent:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $expense->money }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Description:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $expense->description }}</span></dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Member:') }}</span></dt>
                    <dd class="col-md-8"><span
                            class="text-md">{{ $expense->member != '' ? App\Models\User::getTeams($expense->member) : '--' }}</span>
                    </dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Case:') }}</span></dt>
                    <dd class="col-md-8"><span
                            class="text-md">{{ $expense->case != '' ? App\Models\Cases::getCasesById($expense->case) : '--' }}</span>
                    </dd>

                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Notes:') }}</span></dt>
                    <dd class="col-md-8"><span class="text-md">{{ $expense->notes }}</span></dd>
                    {{-- {{ dd($expense->attachment) }} --}}
                    <dt class="col-md-8"><span class="h6 text-md mb-0">{{ __('Attachments:') }}</span></dt>
                    <dt class="col-md-4"><span class="h6 text-md mb-0">{{ __('Action:') }}</span></dt>
                    

                    @php
                        $attachmentData = json_decode($expense->attachment);
                    @endphp
                    <div class="row" id='attachments'>
                        @forelse ($attachmentData->files as $key => $file)
                        @php
                            $filePath = storage_path('app/public/uploads/expense_docs/'.$file);
                            $tmpPath = storage_path('app/public/uploads/tmp/'.Auth::user()->id . '-docs');
                            $tmpfile = decryptFile($filePath,pathinfo($file)['extension'],$tmpPath);
                            $fileTmpPath = \App\Models\Utility::get_file('uploads/tmp/'.Auth::user()->id.'-docs/');
                       
                        @endphp
                            <div class="row" id='attachment_{{ $key}}'>
                                <dd class="col-md-8 p-2"><span class="text-md mb-0">{{ Str::limit(basename($file), 35) }}</span></dd>
                                <dd class="col-md-4">
                                    <a href="javascript:void(0)" class="btn action_btn p-2" onclick="deleteAttachment({{ $expense->id }}, '{{ $file }}','{{ $key }}')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                
                                    <a href="{{ $fileTmpPath . $tmpfile }}"
                                           target="_blank" download
                                        class="btn action_btn p-2">
                                       <i class="bi bi-box-arrow-down"></i>
                                     </a>
                                </dd>
                            </div>
                        @empty
                            <dd class="col-md-12 text-center text-justify">
                                <span class="text-md">{{ __('No attachments available.') }}</span>
                            </dd>
                        @endforelse
                    </div>
                  

                </dl>
            </div>

        </div>

    </div>
</div>
<script>
    // deleteAttachment
    function deleteAttachment(id, file,removeId) {
        var url = "{{ route('expenses.deleteExpenseDoc') }}";
        var data = {
            id:id,
            file: file,
            _token: "{{ csrf_token() }}"
        };
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'JSON',
            success: function(response) {
                if (response.status == 'success') {
                $('#attachment_' + removeId).remove();
                show_toastr('success', response.message);
                if(response.fileCount < 1){
                    $('#attachments').append('<dd class="col-md-12 text-center text-justify"><span class="text-md">{{ __('No attachments available.') }}</span></dd>');
                }
                } else {
                show_toastr('error', "Something Went Wrong");
                }
            }
        });
    }
</script>