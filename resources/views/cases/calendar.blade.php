@extends('layouts.app')

@push('style')
{{-- <link rel="stylesheet" href="{{ asset('public/vendor/tui-calendar.css') }}">
<link rel="stylesheet" href="{{ asset('public/vendor/tui-datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('public/vendor/tui-time-picker.css') }}"> --}}

<link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui-calendar/latest/tui-calendar.css" />

<!-- If you use the default popups, use this. -->
<link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.css" />
<link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.css" />
@endpush

@section('content')
<div id="calendar" style="height: 800px;"></div>
@endsection

@push('custom-script')


{{-- <script src="{{ asset('public/vendor/tui-code-snippet.js') }}"></script>
<script src="{{ asset('public/vendor/tui-datepicker.min.js') }}"></script>
<script src="{{ asset('public/vendor/tui-time-picker.min.js') }}"></script>
<script src="{{ asset('public/vendor/tui-calendar.min.js') }}"></script> --}}
<script src="https://uicdn.toast.com/tui.code-snippet/v1.5.2/tui-code-snippet.min.js"></script>
<script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.js"></script>
<script src="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.js"></script>
<script src="https://uicdn.toast.com/tui-calendar/latest/tui-calendar.js"></script>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendar = new tui.Calendar(document.getElementById('calendar'), {
            // Calendar options and settings
        });
        
        // Initialize the date picker
        var datePicker = new tui.DatePicker('#calendar', {
            type: 'date',
            date: new Date(), // initial date
            showAlways: true,
        });
        
        // Initialize the time picker
        var timePicker = new tui.TimePicker('#calendar', {
            initialHour: 9,
            initialMinute: 0,
            format: 'h:mm a', // format for displaying selected time
            showMeridiem: true,
        });
    });
</script> --}}

<script>
    var calendar = new tui.Calendar('#calendar', {
        defaultView: 'week',
        taskView: true,
        scheduleView: true,
        template: {
            milestone: function(schedule) {
                return '<span style="color:red;"><i class="fa fa-flag"></i> ' + schedule.title + '</span>';
            },
            task: function(schedule) {
                return '&nbsp;&nbsp;#' + schedule.title;
            },
            allday: function(schedule) {
                return schedule.title + ' <i class="fa fa-refresh"></i>';
            },
        },
        useCreationPopup: true,
        useDetailPopup: true,
    });

    var template = {
        milestone: function(schedule) {
            return '<span style="color:red;"><i class="fa fa-flag"></i> ' + schedule.title + '</span>';
        },
        task: function(schedule) {
            return '&nbsp;&nbsp;#' + schedule.title;
        },
        allday: function(schedule) {
            return schedule.title + ' <i class="fa fa-refresh"></i>';
        },
    };

    calendar.createSchedules([
        {
            id: '1',
            calendarId: '1',
            title: 'My Schedule',
            category: 'time',
            dueDateClass: '',
            start: '2023-08-01T09:00:00',
            end: '2023-08-01T10:00:00',
        },
    ]);
</script>
@endpush