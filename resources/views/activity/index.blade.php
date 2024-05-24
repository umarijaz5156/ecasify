@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" id="style">

    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Activity') }}</li>
    @endsection
  
    @php
        $setting = App\Models\Utility::settings();
        $selectedThemeColor = '--color-' . $setting['color'] ?? '--color-theme-3';
        
    @endphp


    <style>
        .red-border {
            border-color: red !important;
        }

        ul {
            padding-left: 0rem !important;
        }

        .main_wrapper {
            padding: 32px 20px;
        }

        .primar_color {
            color: #5271ff;
        }

        /*======== Tabs Css =========*/
        .custom_tabs_case {
            margin-top: 71px;
        }

        .case_tabs_btn.active {
            border-radius: 5px !important;
            color: white;
        }

        .case_tabs_btn {
            width: 160px;
            color: rgba(0, 0, 0, 0.8);
            font-size: 16px;
            font-weight: 500;
        }

        .nav-item {
            border-radius: 5px !important;
            border: 0.5px solid var({{ $selectedThemeColor }}) !important;
            padding: 3px !important;
        }

        .case_tab_content {
            border-radius: 10px;
            background: rgba(213, 216, 220, 0.16);
            padding-block: 59px;
            padding-inline: 62px;
            position: relative;
        }

        .case_information_box {
            border-radius: 10px;
            border: 1px dashed var({{ $selectedThemeColor }});
            background: #fff;
            padding-block: 45px;
            padding-inline: 36px;
            margin-block: 1rem;
        }

        .case_information_box_title {
            position: relative;
            padding-left: 16px;
        }

        .case_information_box_title::after {
            content: "";
            position: absolute;
            width: 5px;
            height: 100%;
            border-radius: 10px;
            background: var({{ $selectedThemeColor }});
            top: 0;
            left: 0;
            bottom: 0;
        }

        .case_information_box_title h3 {
            color: rgba(0, 0, 0, 0.8);
            font-size: 20px;
            font-weight: 600;
        }

        .case_information_box_list ul {
            text-decoration: none;
            list-style: none;
            margin-top: 32px;
        }

        .list_Info p {
            color: rgba(0, 0, 0, 0.8);
            font-size: 17px;
            font-weight: 600;
        }

        .list_Info span {
            width: 60%;
            color: rgba(0, 0, 0, 0.6);
            font-size: 18px;
            font-weight: 400;

        }

        .list_Info_other span {
            width: 60%;
            color: rgba(0, 0, 0, 0.6);
            font-size: 18px;
            font-weight: 400;
            text-align: end;
        }

        .list_Info_setting span {
            text-align: center;
        }

        .list_Info_setting_2 span {
            text-align: end;
            padding-right: 20%;
        }

        .active-scroll span {
            border-radius: 5px;
            background: #f8f9f9;
            padding: 16px;
            height: 120px;
            overflow-y: auto;
            scrollbar-gutter: stable;
        }

        .active-scroll span::-webkit-scrollbar {
            width: 7px;
        }

        .active-scroll span::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.04);
            border-radius: 5px;
        }

        .active-scroll span::-webkit-scrollbar-thumb {
            background: rgb(217, 217, 217);
            border-radius: 5px;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 7px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.04);
            border-radius: 5px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgb(217, 217, 217);
            border-radius: 5px;
        }

        .case_opponents_box {
            border-radius: 10px;
            background: #fff;
            padding-block: 34px;
            padding-inline: 26px;
            height: 100%;
            margin-block: 1rem;
            height: 520px;
            overflow-x: hidden;
            overflow-y: auto;

        }

        .chevron-thin {
            border-color: black !important;
        }

        .Opponents_title {
            border-radius: 10px;
            border: 1px dashed var({{ $selectedThemeColor }});
            background: #fff;
            padding-inline: 24px;
            padding-block: 14px;
        }

        .Opponents_title h3 {
            color: rgba(0, 0, 0, 0.8);
            font-size: 20px;
            font-weight: 600;
        }

        .Opponents_list h4 {
            color: rgba(0, 0, 0, 0.8);
            font-size: 16px;
            font-weight: 600;
        }

        .Opponents_list ul {
            text-decoration: none;
            list-style: none;
            margin-top: 32px;
        }

        .Opponents_list ul li {
            padding: 16px;
            background: #f8f9f9;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .Opponents_list .Opponents_list_info span {
            width: 65%;
            color: rgba(0, 0, 0, 0.6);
            font-size: 16px;
            font-weight: 400;
        }

        .action_btn {
            color: rgba(0, 0, 0, 0.30);
        }

        .custom_table .table td,
        .table th {
            padding: 0.5rem;
        }

        .thead_light {
            border-radius: 10px !important;
            background: #F8F9F9;
        }

        @media screen and (max-width: 767px) {
            .case_tab_content {
                padding: 16px;
            }

            .list_Info_other span {

                text-align: left !important;
            }

            .list_Info_setting span {
                text-align: left !important;
            }

            .list_Info_setting_2 span {
                text-align: left !important;
                padding-right: 0% !important;
            }

            .custom_table {
                overflow-x: auto;
            }

            .custom_table table {
                width: 400px;
            }

            .list_Info {
                flex-direction: column;
                margin-top: 1rem;
            }

            .list_Info span {
                width: 100%;
            }

            .list_Info p {
                margin-bottom: 0.5rem;
            }
        }

        ul li {
            padding: 3px;
        }

        .edit_btn_view {
            float: right;
            position: absolute;
            top: 0;
            right: 0;
        }

        @media only screen and (max-width: 600px) {
            ul {
                padding: 0px !important;
            }

            .edit_btn_view {
                top: -14px !important;
            }
        }

        .table td,
        .table th {
            white-space: initial !important;
        }

        /*======== Tabs Css =========*/
        /* timeline and calendar css */
        .color-calendar.basic {
            --cal-color-primary: white;
            --cal-font-family-header: "Poppins", sans-serif;
            --cal-font-family-weekdays: "Poppins", sans-serif;
            --cal-font-family-body: "Poppins", sans-serif;
            --cal-drop-shadow: 0 7px 30px -10px rgba(150, 170, 180, 0.5);
            --cal-border: none;
            --cal-border-radius: 0.5rem;
            --cal-header-color: white;
            --cal-weekdays-color: black;
            border-radius: var(--cal-border-radius);
            box-shadow: none;
            color: var(--cal-color-primary);
            background-color: transparent;
            border: var(--cal-border);
        }

        .color-calendar.basic .calendar__weekdays .calendar__weekday {
            color: var(--cal-color-primary);
            opacity: 0.2;
        }

        .color-calendar.basic .calendar__days .calendar__day-today .calendar__day-box {
            border-radius: 99rem;
        }

        .color-calendar.basic .calendar__days .calendar__day-box {
            border-radius: 99rem;
        }

        .color-calendar.basic .calendar__days .calendar__day-selected .calendar__day-box {
            border-radius: 99rem;
            background-color: #5271ff;
            opacity: 1;
            box-shadow: none;
        }

        .color-calendar.basic .calendar__arrow-inner::before {
            border-width: 0.2em 0.2em 0 0;
        }

        .calendar__month {
            font-size: 1.5rem;
            color: white;
            font-weight: 600;
        }

        .calendar__year {
            color: #5271ff;
            font-size: 1.5rem;
            font-weight: 400;
        }

        .myaccordion {
            margin-top: 80px;
        }

        .myaccordion .timeline_tabs {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .timeline_tabs .content_area {
            flex: 1;
        }

        .timeline_tabs .card {
            width: 100%;
            border-radius: 10px;
            background: rgba(141, 195, 19, 0.08);
            box-shadow: none;
            border: none;
            position: relative;
            margin-bottom: 30px;
        }

        .content_area {
            position: relative;
            padding-left: 40px;
        }

        .timeline_wrapper_content::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            /* background-color: #8dc313; */
            width: 26px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .timeline_wrapper_content_mid::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            /* background-color: #8dc313; */
            width: 26px;
        }

        .timeline_wrapper_content_bottom::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            /* background-color: #8dc313; */
            width: 26px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .timeline_wrapper_one::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            /* background-color: #8dc313; */
            width: 26px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .timeline_tabs .card .card-header {
            border-bottom: none;
            background-color: transparent;
            padding: 17px;
        }

        .timeline_tabs .card .card-header h2 .timeline_tabs_title {
            font-size: 1.2rem;
            border-bottom: 2px solid #8dc313;
            padding-bottom: 4px;
        }

        .myaccordion .btn-link:hover,
        .myaccordion .btn-link:focus {
            text-decoration: none;
            box-shadow: none;
        }

        .myaccordion .btn-link {
            width: 48px;
            height: 48px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: rgba(0, 0, 0, 0.8);
            font-weight: 500;
            font-size: 1rem;
            border-radius: 5px;
            border: 2px solid #8dc313;
        }

        .myaccordion .fa-stack {
            font-size: 18px;
        }

        .timeline_tabs .timeline_date {
            /* position: absolute;
                                          top: 0;
                                          left: 0; */
            border: 1px dashed #8dc313;
            border-radius: 999px;
            width: 84px;
            height: 84px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 6px;
        }

        .timeline_tabs .timeline_date .timeline_date_inner {
            border: 1px solid #d9d9d9;
            border-radius: 999px;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .timeline_tabs .timeline_date .timeline_date_inner h4 {
            font-weight: 400;
            font-size: 1rem;
            margin-bottom: 0;
        }

        .timeline_tabs .timeline_date .timeline_date_inner h4 span {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .timeline_date_inner_calander {
            position: absolute;
            top: -14px;
            right: -14px;
            border-radius: 999px;
            fill: #fff;
            filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.05));
            width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            color: #8dc313;
        }

        .changes_log_detail ul li {
            margin-bottom: 0.8rem;
        }

        /* Calendar Events Css */
        .calendar_wrapper {
            margin-top: 80px;
            display: flex;
            gap: 0.5rem;
        }

        @media screen and (max-width: 767px) {
            .calendar_wrapper {
                flex-direction: column;
            }

            .has-search {
                margin-right: auto;
                margin-left: 0 !important;
            }
        }

        .tui_calendar_wrapper {
            flex: 1;
        }

        .toastui-calendar-panel.toastui-calendar-time {
            height: 91% !important;
        }

        /* @media screen and (max-width: 1200px) {
                                          .calendar_wrapper {
                                            max-width: 1580px;
                                            width: 100%;
                                            flex-direction: column;
                                          }
                                          .toastui-calendar-panel.toastui-calendar-time{
                                            height: 600px !important;
                                          }
                                         } */
        .calendar_wrapper .mini_calendar_wrapper {
            background-color: #373c45;
            grid-column: span 3;
            padding: 1rem;
        }

        #calendar-a {
            width: max-content;
            margin: auto;
        }

        .Today_todo_list .todo_list_title h6 {
            color: #5271ff;
            text-align: center;
            font-weight: 700;
        }

        .All_day_meeting_list .meeting_list_title {
            display: inline-flex;
            padding: 0px 6px;
            align-items: flex-start;
            gap: 10px;
            border-radius: 6px;
            background: #5271ff;
            color: white;
            font-weight: 500;
            padding: 4px;
        }

        .all_day_meeting_list_details ul {
            list-style-type: none;
            margin-top: 1rem;
        }

        .all_day_meeting_list_details ul li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .all_day_meeting_list_details ul li::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 0;
            background-color: #8dc313;
            width: 0.7rem;
            height: 0.7rem;
            border-radius: 999px;
        }

        .all_day_meeting_list_details ul li .allday_meeting_time {
            color: #a1a1aa;
            display: flex;
            justify-content: start;
            align-items: center;
            gap: 0.5rem;
        }

        .all_day_meeting_list_details ul li .allday_meeting_time span {
            background-color: #a1a1aa;
            border-radius: 999px;
            width: 1.2rem;
            height: 1.2rem;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
        }

        .has-search {
            max-width: 285px;
            width: 100%;
            margin-left: auto;
            padding: 10px;
            position: relative;
        }

        .has-search .form-control {
            padding-left: 2.375rem;
        }

        .has-search .form-control-feedback {
            position: absolute;
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 2.375rem;
            text-align: center;
            pointer-events: none;
            color: #aaa;
        }

        .has-search .form-control {
            background-color: #F4F4F5;
        }

        .tui-full-calendar-vlayout-area.tui-view-48.tui-full-calendar-vlayout-container>div:nth-child(1),
        .tui-full-calendar-vlayout-area.tui-view-48.tui-full-calendar-vlayout-container>div:nth-child(2),
        .tui-full-calendar-vlayout-area.tui-view-48.tui-full-calendar-vlayout-container>div:nth-child(3) {
            display: none;
        }

        .color-calendar .calendar__picker .calendar__picker-year-selected,
        .color-calendar .calendar__picker .calendar__picker-month-selected {
            background-color: #5271ff;
        }

        @media screen and (min-width: 768px) and (max-width: 1200px) {
            #calendar {
                width: 850px;
            }

            .tui_calendar_wrapper {
                overflow-x: scroll;
            }

            .has-search {
                margin-right: auto;
                margin-left: 0;
            }
        }

        .text-color {
            color: var({{ $selectedThemeColor }}) !important;
        }

        .tui-full-calendar-confirm {
            background-color: var({{ $selectedThemeColor }}) !important;
        }

        .tui-full-calendar-section-state {
            display: none;
        }

        .tui-full-calendar-popup-section:nth-child(4) {
            margin-bottom: 10px;
        }

        @media screen and (max-width: 640px) {
            .tui-full-calendar-popup-container {
                min-width: 100%;
                display: flex;
                justify-content: start;
                align-items: start;
                flex-direction: column;
            }

            .tui-full-calendar-popup-section {
                width: 100%;
                display: flex;
                justify-content: start;
                align-items: start;
            }

            .tui-full-calendar-section-title input {
                width: 100%
            }

            .tui-full-calendar-popup-section-item.tui-full-calendar-section-title,
            .tui-full-calendar-popup-section-item.tui-full-calendar-section-location {
                display: flex;
                justify-content: start;
                align-items: center;
            }

            .tui-full-calendar-popup-section-item.tui-full-calendar-section-location input {
                width: 100%
            }

            .tui-full-calendar-section-date-dash {
                display: none;
            }

            .tui-full-calendar-popup-section:nth-child(4) {
                flex-direction: column;
                gap: 0.5rem;
                margin-bottom: 0px;
            }

            .tui-full-calendar-popup-section.tui-full-calendar-dropdown.tui-full-calendar-close.tui-full-calendar-section-state {
                display: none;
            }
        }

        .card-drag:hover {
            box-shadow: 0px 4px 15px 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
            cursor: grab;
        }
    </style>
@endpush

@section('page-title', __('Activity'))

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Activity') }}</li>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="">
            <ul class="nav nav-pills p-4 mb-3 custom_tabs_case" id="pills-tab" role="tablist" style="gap: 16px;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link case_tabs_btn {{ $tab == 'all' ? 'active' : '' }}" id="pills-home-tab"
                        data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab"
                        aria-controls="pills-home" aria-selected="true">
                        <i class="bi bi-database"></i>
                        All
                    </button>
                </li>
                
                    <li class="nav-item" role="presentation">
                        <button class="nav-link case_tabs_btn {{ $tab == 'case' ? 'active' : '' }}" id="pills-profile-tab"
                            data-bs-toggle="pill" data-bs-target="#pills-case" type="button" role="tab"
                            aria-controls="pills-profile" aria-selected="false">
                            <i class="ti ti-file-text"></i>
                            Case
                        </button>
                    </li>

                
                    <li class="nav-item" role="presentation">
                        <button class="nav-link case_tabs_btn  {{ $tab == 'document' ? 'active' : '' }}" id="pills-document-tab"
                            data-bs-toggle="pill" data-bs-target="#pills-document" type="button" role="tab"
                            aria-controls="pills-document" aria-selected="false">
                            <i class="ti ti-file-text"></i>Documents
                        </button>
                    </li>

            
                    <li class="nav-item" role="presentation">
                        <button class="nav-link case_tabs_btn  {{ $tab == 'task' ? 'active' : '' }}" id="pills-task-tab"
                            data-bs-toggle="pill" data-bs-target="#pills-task" type="button" role="tab"
                            aria-controls="pills-task" aria-selected="false">
                            <i class="ti ti-file-plus"></i>Task
                        </button>
                    </li>
             
                    <li class="nav-item" role="presentation">
                        <button class="nav-link case_tabs_btn  {{ $tab == 'users' ? 'active' : '' }}" id="pills-users-tab"
                            data-bs-toggle="pill" data-bs-target="#pills-users" type="button" role="tab"
                            aria-controls="pills-users" aria-selected="false">
                            <i class="ti ti-users"></i> Users
                        </button>
                    </li>

               
                    <li class="nav-item" role="presentation">
                        <button class="nav-link case_tabs_btn  {{ $tab == 'userslogin' ? 'active' : '' }}" id="pills-userslogin-tab"
                            data-bs-toggle="pill" data-bs-target="#pills-userslogin" type="button" role="tab"
                            aria-controls="pills-userslogin" aria-selected="false">
                            <i class="ti ti-users"></i> Login Details
                        </button>
                    </li>

            
                    <li class="nav-item" role="presentation">
                        <button class="nav-link case_tabs_btn  {{ $tab == 'timer' ? 'active' : '' }}" id="pills-timer-tab"
                            data-bs-toggle="pill" data-bs-target="#pills-timer" type="button" role="tab"
                            aria-controls="pills-timer" aria-selected="false">
                            <i class="far fa-clock"></i> Timer
                        </button>
                    </li>
              
            </ul>
    
            <div class="tab-content" id="pills-tabContent">
       
    
                <div class="tab-pane fade {{ $tab == 'all' ? ' show active ' : '' }}" id="pills-home" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        <div class="case_tab_content">
                            @if ($allActivities->isNotEmpty())
                            <table class="table case-activities dataTable">
                                <thead>
                                    <tr>
                                        <th>Case No</th>
                                        <th>Activity</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                        <th>Log Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allActivities as $key => $allActivity)
                                        @php
                                            $rowClass = $key % 2 === 0 ? 'even' : 'odd';
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{  $allActivity->target_id }}</td>
                                            <td>
                                             @switch($allActivity->target_type)
                                                @case('Case')
                                                    @if (isset($allActivity->file))
                                                    {{ 'Document ' . $allActivity->action  }}
                                                    @else
                                                    {{ 'Case ' . $allActivity->action  }}
                                                    @endif
                                                    @break
                                                @case('Task')
                                                    {{ 'Task ' . $allActivity->action  }}
                                                    @break
                                                @case('User')
                                                    {{ 'User ' . $allActivity->action  }}
                                                    @break
                                                    @case('Timer')
                                                    {{ 'Timer ' . $allActivity->action  }}
                                                    @break
                                                @default
                                            @endswitch
                                            </td>
                                            <td> {{ UsersNameById($allActivity->user_id) }} </td>
                                            <td>{{  $allActivity->action }}
                                                @switch($allActivity->target_type)
                                                @case('Case')
                                                    @if (isset($allActivity->file))
                                                    <a style="color: #18864B" href="{{ route('cases.show', $allActivity->target_id . '?tab=document') }}"
                                                        class="btn btn-sm d-inline-flex align-items-center" title=""
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    @else
                                                    <a style="color: #18864B" href="{{ route('cases.show', $allActivity->target_id . '?tab=timeline') }}"
                                                        class="btn btn-sm d-inline-flex align-items-center" title=""
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    @endif
                                                    @break
                                                @case('Task')
                                                <a style="color: #18864B" href="#"
                                                class="btn btn-sm d-inline-flex align-items-center"  data-url="{{ route('to-do.show', $allActivity->target_id) }}"
                                                data-size="xl addTaskModal_wrapper" data-ajax-popup="true"
                                                data-title="{{ __(' View Tasks') }}" title="{{ __('View Task') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" >
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                                    @break
                                                @case('User')
                                                <a style="color: #18864B" href="{{ route('users.edit', $allActivity->target_id) }}"
                                                    class="btn btn-sm d-inline-flex align-items-center" title=""
                                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                    @break
                                                    @case('Timer')
                                                    @if (isset($allActivity->target_id))
                                                    <a style="color: #18864B" href="#"
                                                class="btn btn-sm d-inline-flex align-items-center"  data-url="{{ route('timesheet.show', $allActivity->target_id) }}"
                                                data-size="xl addTaskModal_wrapper" data-ajax-popup="true"
                                                data-title="{{ __(' View Timesheet') }}" title="{{ __('View Timesheet') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" >
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                                    @endif
                                                        @break
                                                @default
                                            @endswitch
                                            </td>
                                            <td style="font-size: 12px;">{{ timeAgo($allActivity->created_at) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center">
                                <p>No record found</p>
                            </div>
                        @endif
                        </div>
                    </div>
    
                <div class="tab-pane fade {{ $tab == 'case' }}"
                    id="pills-case" role="tabpanel" aria-labelledby="pills-case-tab">
                    <div class="case_tab_content">
                        @if ($caseActivities->isNotEmpty())
                        <table class="table case-activities dataTable">
                            <thead>
                                <tr>
                                    <th>Case No</th>
                                    <th>Activity</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                    <th>Log Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($caseActivities as $key => $caseActivity)
                                    @php
                                        $rowClass = $key % 2 === 0 ? 'even' : 'odd';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{  $caseActivity->target_id }}</td>
                                        <td>
                                            {{ 'Case ' . $caseActivity->action  }}
                                            
                                        </td>
                                        <td> {{ UsersNameById($caseActivity->user_id) }} </td>
                                        <td>{{  $caseActivity->action }}
                                              <a style="color: #18864B" href="{{ route('cases.show', $caseActivity->target_id . '?tab=timeline') }}"
                                                class="btn btn-sm d-inline-flex align-items-center" title=""
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </td>
                                       
                                        <td style="font-size: 12px;">{{ timeAgo($caseActivity->created_at) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                            <p>No record found</p>
                        </div>
                    @endif
                    
                    </div>
                </div>
                    
                <div class="tab-pane fade {{ $tab == 'document' ? ' show active ' : '' }}" id="pills-document" role="tabpanel"
                    aria-labelledby="pills-document-tab">
                    <div class="case_tab_content">
                        @if ($documentActivities->isNotEmpty())
                            <table class="table document-activities dataTable">
                                <thead>
                                    <tr>
                                        <th>Case No</th>
                                        <th>Activity</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                        <th>Log Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documentActivities as $key => $documentActivity)
                                        @php
                                            $file = preg_replace('/^[0-9]+-case-doc\//', '', $documentActivity->file);
                                            $rowClass = $key % 2 === 0 ? 'even' : 'odd';
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{  $documentActivity->target_id }}</td>
                                            <td>
                                                {{ 'Document ' . $documentActivity->action  }}
                                                
                                            </td>
                                            <td> {{ UsersNameById($documentActivity->user_id) }} </td>
                                            <td>{{  $documentActivity->action }}
                                                  <a style="color: #18864B" href="{{ route('cases.show', $documentActivity->target_id . '?tab=document') }}"
                                                    class="btn btn-sm d-inline-flex align-items-center" title="{{ $file }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </td>
                                           
                                            <td style="font-size: 12px;">{{ timeAgo($documentActivity->created_at) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center">
                                <p>No record found</p>
                            </div>
                        @endif
                    </div>
                </div>
    
                <div class="tab-pane fade {{ $tab == 'task' }}"
                    id="pills-task" role="tabpanel" aria-labelledby="pills-task-tab">
                    <div class="case_tab_content">
         
                        @if ($taskActivities->isNotEmpty())
                        <table class="table document-activities dataTable">
                            <thead>
                                <tr>
                                    <th>Case No</th>
                                    <th>Activity</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                    <th>Log Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($taskActivities as $key => $taskActivity)
                                    @php
                                        $rowClass = $key % 2 === 0 ? 'even' : 'odd';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{  $taskActivity->target_id }}</td>
                                        <td>
                                            {{ 'Task ' . $taskActivity->action  }}
                                            
                                        </td>
                                        <td> {{ UsersNameById($taskActivity->user_id) }} </td>
                                        <td>{{  $taskActivity->action }}
                                              <a style="color: #18864B" href="#"
                                                class="btn btn-sm d-inline-flex align-items-center"  data-url="{{ route('to-do.show', $taskActivity->target_id) }}"
                                                data-size="xl addTaskModal_wrapper" data-ajax-popup="true"
                                                data-title="{{ __(' View Tasks') }}" title="{{ __('View Task') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" >
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                           
                                        </td>
                                       
                                        <td style="font-size: 12px;">{{ timeAgo($taskActivity->created_at) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                            <p>No record found</p>
                        </div>
                    @endif
    
                    </div>
                </div>
    
                <div class="tab-pane fade {{ $tab == 'users' ? ' show active ' : '' }}" id="pills-users" role="tabpanel"
                    aria-labelledby="pills-users-tab">
    
                    <div class="case_tab_content">
                        @if ($userActivities->isNotEmpty())
                        <table class="table case-activities dataTable">
                            <thead>
                                <tr>
                                    <th>Case No</th>
                                    <th>Activity</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                    <th>Log Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userActivities as $key => $userActivity)
                                    @php
                                        $rowClass = $key % 2 === 0 ? 'even' : 'odd';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>{{  $userActivity->target_id }}</td>
                                        <td>
                                            {{ 'User ' . $userActivity->action  }}
                                             
                                        </td>
                                        <td> {{ UsersNameById($userActivity->user_id) }} </td>
                                        <td>{{  $userActivity->action }}
                                              <a style="color: #18864B" href="{{ route('users.edit', $userActivity->target_id) }}"
                                                  class="btn btn-sm d-inline-flex align-items-center" title=""
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </td>
                                       
                                        <td style="font-size: 12px;">{{ timeAgo($userActivity->created_at) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                            <p>No record found</p>
                        </div>
                    @endif
    
                    </div>
    
    
    
                </div>
    
                <div class="tab-pane fade {{ $tab == 'userslogin' ? ' show active ' : '' }}" id="pills-userslogin" role="tabpanel"
                    aria-labelledby="pills-userslogin-tab">
    
                    <div class="case_tab_content">
                        @if ($loginUsersDetails->isNotEmpty())
                        <table id="datatable" class="table dataTable data-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('User Name') }}</th>
                                    <th>{{ __('IP') }}</th>
                                    <th>{{ __('Last Login At') }}</th>
                                    <th>{{ __('Country') }}</th>
                                    <th>{{ __('Device Type') }}</th>
                                    <th>{{ __('Os Name') }}</th>
                                    <th>{{ __('Details') }}</th>
                                </tr>
                            </thead>
                            @foreach ($loginUsersDetails as $user)

                                <tr>
                                    @php
                                        $json = json_decode($user->details);

                                        $userType = App\Models\User::find($user->user_id);

                                    @endphp
                                    <td>{{ $user->user_name }}</td>
                                    <td>{{ $user->ip }}</td>
                                    <td>{{ $user->date }}</td>
                                    <td>{{ $json->country ?? '' }}</td>
                                    <td>{{ $json->device_type }}</td>
                                    <td>{{ $json->os_name }}</td>
                                    <td>

                                        <div class="action-btn bg-light-secondary text-dark ms-2">
                                            <a href="#" data-size="md"
                                                data-url="{{ route('userlog.view', $user->id) }}" data-bs-toggle="tooltip"
                                                title="{{ __('View') }}" data-ajax-popup="true"
                                                data-title="{{ __('View User Logs') }}"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center ">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </div>
                                        @if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') 
                                        <div class="action-btn bg-light-secondary text-dark ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['userlog.destroy', $user->id],'id' => 'delete-form-' . $user->id]) !!}
                                                <a href="#!" class="mx-3 btn btn-sm align-items-center bs-pass-para " data-id="{{ $user->id }}"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-confirm-yes="delete-form-{{ $user->id }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                    <i class="ti ti-trash " data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Delete') }}"></i>
                                                </a>
                                            {!! Form::close() !!}
                                        </div>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                            <p>No record found</p>
                        </div>
                    @endif
    
                    </div>
    
    
    
                </div>
                <div class="tab-pane fade {{ $tab == 'timer' ? ' show active ' : '' }}" id="pills-timer" role="tabpanel"
                aria-labelledby="pills-timer-tab">

                <div class="case_tab_content">
                    @if ($timerActivities->isNotEmpty())
                    <table class="table case-activities dataTable">
                        <thead>
                            <tr>
                                <th>Case No</th>
                                <th>Activity</th>
                                <th>Created By</th>
                                <th>Action</th>
                                <th>Log Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timerActivities as $key => $timerActivity)
                                @php
                                    $rowClass = $key % 2 === 0 ? 'even' : 'odd';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td>{{  $timerActivity->target_id ?? '---' }}</td>
                                    <td>
                                        {{ 'Timer ' . $timerActivity->action  }}
                                        
                                    </td>
                                    <td> {{ UsersNameById($timerActivity->user_id) }} </td>
                                    <td>{{  $timerActivity->action }}
                                        @if (isset($timerActivity->target_id))
                                        <a style="color: #18864B" href="#"
                                        class="btn btn-sm d-inline-flex align-items-center"  data-url="{{ route('timesheet.show', $timerActivity->target_id) }}"
                                        data-size="xl addTaskModal_wrapper" data-ajax-popup="true"
                                        data-title="{{ __(' View Timesheet') }}" title="{{ __('View Timesheet') }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" >
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                        @endif
                                    </td>
                                   
                                    <td style="font-size: 12px;">{{ timeAgo($timerActivity->created_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="text-center">
                            <p>No record found</p>
                        </div>
                    @endif

                </div>



            </div>
   
          
            </div>
        </div>
    </div>
</div>

  
@endsection

@push('custom-script')
  
@endpush
