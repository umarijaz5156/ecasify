{{ Form::model($expense,['route' => ['expenses.update',$expense->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
    <div class="modal-body">
        <div class="row">
            {{-- Expense Title --}}
            <div class="form-group col-md-12">
                {!! Form::label('title', __('Expense Title'), ['class' => 'form-label']) !!}
                {!! Form::text('title', $expense->title, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                <div class="input-group justify-content-end">
                    <input type="text" id="open_date" name="date" required placeholder="YYYY-MM-DD" autocomplete="off"
                        class="form-control draft_fields" value="{{$expense->date}}">
                    <span style="position: absolute; padding: 10px; cursor: pointer;"
                        class="input-group-addon calendar_field">
                        <i class="fa fa-calendar" id="calendar_icon_open_date"></i>
                    </span>
                </div>
            </div>
            {{-- description --}}
            <div class="form-group col-md-12">
                {!! Form::label('description', __('Description'), ['class' => 'form-label']) !!}
                {!! Form::text('description', $expense->description, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-12">
                {!! Form::label('money', __('Money Spent'), ['class' => 'form-label']) !!}
                {!! Form::number('money', $expense->money, ['class' => 'form-control']) !!}
            </div>
            {{-- type --}}
            <div class="form-group col-md-12">
                {!! Form::label('type', __('Expense Type'), ['class' => 'form-label']) !!}
                {!! Form::select('type', $expenseTypes, $expense->type, ['class' => 'form-control ']) !!}
            </div>
            <div class="form-group col-md-12">
                {!! Form::label('member', __('Member'), ['class' => 'form-label']) !!}
                {!! Form::select('member',$members, $expense->member, ['class' => 'form-control ']) !!}
            </div>
            <div class="form-group col-md-12">
                {!! Form::label('case', __('Case'), ['class' => 'form-label']) !!}
               {!! Form::select('case',$cases, $expense->case, ['class' => 'form-control ']) !!}
            </div>
            {{--  filepond--}}
            <div class="form-group col-md-12">
                {!! Form::label('attachment', __('Attachment'), ['class' => 'form-label']) !!}
                <input type="file" class="filepond" name="attachment[]" multiple data-allow-reorder="true" data-max-files="3" />
            </div>     

            <div class="form-group col-md-12">
                {!! Form::label('notes', __('Notes'), ['class' => 'form-label']) !!}
                {!! Form::text('notes', $expense->notes, ['class' => 'form-control']) !!}
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Update')}}" class="btn btn-primary ms-2">
    </div>
{{Form::close()}}

<script>
     function formatTime(timeInput) {
        // Remove any non-numeric characters
        let cleanedInput = timeInput.value.replace(/[^0-9]/g, '');

        // Apply the formatting rules
        if (cleanedInput.length >= 2) {
            cleanedInput = cleanedInput.substring(0, 2) + 'h:' + cleanedInput.substring(2);
        }

        if (cleanedInput.length >= 6) {
            cleanedInput = cleanedInput.substring(0, 6) + 'm:' + cleanedInput.substring(6);
        }

        if (cleanedInput.length >= 10) {
            cleanedInput = cleanedInput.substring(0, 10) + 's';
        }

        // Update the input field
        timeInput.value = cleanedInput;

        // Prevent further input if the length exceeds 9
        if (cleanedInput.length >= 10) {
            return false;
        }
    }
    $(document).ready(function() {


        const pond = FilePond.create(document.querySelector('.filepond'), {
            allowMultiple: true,                // Allow multiple file uploads
            allowImageValidateSize: false,     // Disable image size validation
            dropOnElement: true,               // Allow dropping files on the element
            dropOnPage: false,                 // Prevent dropping files on the entire page
            imageValidateSizeMinHeight: 370,   // Minimum image height for validation
            imageValidateSizeMinWidth: 550,    // Minimum image width for validation
            allowImagePreview: true,           // Enable image preview
            imagePreviewHeight: 200,          // Height of image preview
            acceptedFileTypes: [],            // Accept all file types
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                    // Handle file upload using Ajax
                    const formData = new FormData();
                    
                    const headers = new Headers();
                    headers.append('X-CSRF-TOKEN', '{{ csrf_token() }}');

                    formData.append(fieldName, file);
                    fetch('/expenses/file-upload', {
                            method: 'POST',
                            body: formData,
                            headers: headers,
                        })
                        .then(response => response.json())
                        .then(result => {
                                if (result.success) {
                                    load(result.fileUrl);
                                    const fileItem = pond.getFiles().find(item => item.file === file);
                                    console.log(fileItem);
                                    if (fileItem) {
                                        fileItem.file.name = result
                                            .fileName; // Update the name of the uploaded file

                                    }
                                } else {
                                    error(result.error);
                                }
                            })
                        .catch(() => {
                            error('Upload failed');
                        });
                }
            }
        });
        // date formet 

        const openDateInput = document.getElementById('open_date');
        const currentDate = new Date(); // Get the current date

        const openPicker = new Pikaday({
            field: openDateInput,
            format: 'YYYY-MM-DD',
            maxDate: currentDate, // Disable future dates
            onSelect: function(selectedDate) {
                openDateInput.value = formatDate(selectedDate);
            },
        });


        const calendarIconOpen = document.getElementById('calendar_icon_open_date');
        calendarIconOpen.addEventListener('click', function() {
            openPicker.show();
        });



        openDateInput.addEventListener('input', function() {
            autoFormatDate(this);
            validateAndAdjustDate(this);

        });


        function autoFormatDate(input) {
            let value = input.value.replace(/[^\d-]/g, '');

            // Remove extra hyphens
            value = value.replace(/-{2,}/g, '-');

            // Restrict to a maximum of 10 characters
            value = value.substring(0, 10);

            if (value.length >= 4 && value[4] !== '-') {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }

            if (value.length >= 7 && value[7] !== '-') {
                // if (value.length >= 7 && value[7] === '-') {
                value = value.substring(0, 7) + '-' + value.substring(7);
            }


            input.value = value;
        }

        function validateAndAdjustDate(input) {

            const parts = input.value.split('-');

            let year = parseInt(parts[0], 10);
            let month = parseInt(parts[1], 10);
            if (parts.length >= 2) {
                let year = parseInt(parts[0], 10);
                let month = parseInt(parts[1], 10);

                if (isNaN(year) || isNaN(month)) {
                    return;
                }

                const currentDate = new Date();
                const currentYear = currentDate.getFullYear();
                const currentMonth = currentDate.getMonth() + 1; // JavaScript months are zero-based

                const monthString = month.toString();
                const numberOfDigits = monthString.length;

                if (numberOfDigits === 2) {

                    if (month >= 1 && month <= 12) {

                        input.value = `${year}-${String(month).padStart(2, '0')}-`;
                    } else {

                        month = currentMonth;
                        input.value = `${year}-${String(month).padStart(2, '0')}-`;
                    }
                }

            }
            if (parts.length >= 3) {
                let day = parseInt(parts[2], 10);

                if (isNaN(year) || isNaN(month) || isNaN(day)) {
                    return;
                }

                const currentDate = new Date();
                const currentYear = currentDate.getFullYear();
                const currentDay = currentDate.getDate();


                const dayString = day.toString();
                const numberOfDayDigits = dayString.length;


                if (numberOfDayDigits === 2) {
                    const daysInSelectedMonth = new Date(year, month - 1, 0).getDate(); // Subtract 1 from month
                    if (day >= 1 && day <= daysInSelectedMonth) {
                        input.value =
                            `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    } else {
                        day = currentDay;
                        input.value =
                            `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    }
                }

                if (numberOfDayDigits === 1) {
                    input.value = `${year}-${String(month).padStart(2, '0')}-${day}`;
                    // input.value = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(1, '0')}`;

                }

            }

        }


        // Function to format date as YYYY-MM-DD
        function formatDate(date) {
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // intialize file pond
    });
</script>
