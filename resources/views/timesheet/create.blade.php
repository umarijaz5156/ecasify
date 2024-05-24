{{ Form::open(['route' => 'timesheet.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                {!! Form::label('case', __('Case'), ['class' => 'form-label']) !!}
                {!! Form::select('case', ['0' => 'Save To Drafts'] + $cases->toArray(), null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                <div class="input-group justify-content-end">
                    <input type="text" id="open_date" name="date" required
                        placeholder="YYYY-MM-DD" autocomplete="off" class="form-control draft_fields"
                        value="{{ old('open_date') }}">
                    <span style="position: absolute; padding: 10px; cursor: pointer;"
                        class="input-group-addon calendar_field">
                        <i class="fa fa-calendar" id="calendar_icon_open_date"></i>
                    </span>
                </div>
                {{-- <input id="timesheet_date"  placeholder="DD/MM/YYYY" data-input class="form-control text-center" name="date" required/> --}}
            </div>
            <div class="form-group col-md-12">
                {!! Form::label('particulars', __('Notes'), ['class' => 'form-label']) !!}
                {!! Form::text('particulars', null, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('time', __('Time Spent (in Hours)'), ['class' => 'form-label']) }}
                {{-- {{ Form::time('time', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Select time']) }} --}}
                {{-- <input type="text" name="time" id="timeInput" class="form-control" required="required" placeholder="--h:--m:--s" onblur="validateInput()"> --}}
                <input id="txtStartTime" name="time" class="form-control" type="text" placeholder="HH:MM:SS" onkeypress="formatTime(this)" MaxLength="11" />

            <div id="errorMessage" style="color: red;"></div>
        </div>
        <div class="form-group col-md-12">
            {!! Form::label('member', __('Member'), ['class' => 'form-label']) !!}
            {!! Form::select('member', ['' => 'Select Member'], null, ['class' => 'form-control ']) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}
<script>
    // on change case show
    $(document).ready(function() {
        // Initial call to populate the #member select when the document is ready
        getCaseTeam();

        $(document).on('change', '#case', function() {
            getCaseTeam();
        });

        function getCaseTeam() {
            var case_id = $('#case').val(); // Use $('#case') to get the selected value
            $.ajax({
                url: "{{ route('timesheet.getCaseTeam') }}",
                type: 'POST',
                data: {
                    "case_id": case_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    // Clear and populate the #member select
                    var memberSelect = $('#member');
                    memberSelect.empty().append($('<option>', {
                        value: '0',
                        text: 'Select Member'
                    }));
                    $.each(data, function(i, item) {
                        memberSelect.append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                }
            });
        }
    });
</script>


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
    });
</script>
