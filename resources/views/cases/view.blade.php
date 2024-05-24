@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}" id="style">

    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Case') }}</li>
    @endsection
    @php
        $docfile = \App\Models\Utility::get_file('uploads/case_docs/');
    @endphp
    @php
        $setting = App\Models\Utility::settings();
        $selectedThemeColor = '--color-' . $setting['color'] ?? '--color-theme-3';
        
    @endphp


    {{-- tui links --}}
    <link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui-calendar/latest/tui-calendar.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/color-calendar/dist/css/theme-basic.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" />
    <!-- If you use the default popups, use this. -->
    <link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.css" />
    <link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.css" />
    {{-- end tui links --}}
    <!-- /* Font Awsome Cdn */ -->
    <link href="https://cdn.jsdelivr.net/gh/duyplus/fontawesome-pro/css/all.min.css" rel="stylesheet" type="text/css" />

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

@section('page-title', __('Case'))

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Case') }}</li>
@endsection
@php
    $docfile = \App\Models\Utility::get_file('uploads/case_docs/');
    // docfile public path
    $docfileTmp = \App\Models\Utility::get_file('uploads/case_docs/tmp/' . Auth::user()->id . '-case-docs/');
@endphp
@php
    $originalPath = storage_path('app/public/uploads/case_docs/');
    $setting = App\Models\Utility::settings();
    //$userDetail
    $userDetail = \App\Models\UserDetail::getUserDetail(Auth::User()->id);
    $timeZone = \App\Models\UserDetail::getTimeZone($userDetail?->timezone ?? 'America/New_York');
@endphp

@section('content')

    <div class="p-3">
        <ul class="nav nav-pills p-4 mb-3 custom_tabs_case" id="pills-tab" role="tablist" style="gap: 16px;">
            <li class="nav-item" role="presentation">
                <button class="nav-link case_tabs_btn {{ $tab == 'data' ? 'active' : '' }}" id="pills-home-tab"
                    data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab"
                    aria-controls="pills-home" aria-selected="true">
                    <i class="bi bi-database"></i>
                    Data
                </button>
            </li>
            @if (Auth::user()->type == 'company' ||
                    Auth::user()->type == 'co admin' ||
                    Auth::user()->type == 'super admin' ||
                    Auth::User()->can('timeline case'))
                <li class="nav-item" role="presentation">
                    <button class="nav-link case_tabs_btn {{ $tab == 'timeline' ? 'active' : '' }}" id="pills-profile-tab"
                        data-bs-toggle="pill" data-bs-target="#pills-timeline" type="button" role="tab"
                        aria-controls="pills-profile" aria-selected="false">
                        <i class="bi bi-list-check"></i>
                        Timeline
                    </button>
                </li>
            @endif
            @can('calendar case')
                <li class="nav-item" role="presentation">
                    <button class="nav-link case_tabs_btn  {{ $tab == 'calendar' ? 'active' : '' }}" id="pills-calendar-tab"
                        data-bs-toggle="pill" data-bs-target="#pills-calendar" type="button" role="tab"
                        aria-controls="pills-calendar" aria-selected="false">
                        <i class="bi bi-calendar"></i> Calendar
                    </button>
                </li>
            @endcan
            @if (Auth::user()->type == 'company' ||
                    Auth::user()->type == 'co admin' ||
                    Auth::user()->type == 'super admin' ||
                    Auth::User()->can('tasks case'))
                <li class="nav-item" role="presentation">
                    <button class="nav-link case_tabs_btn  {{ $tab == 'tasks' ? 'active' : '' }}" id="pills-tasks-tab"
                        data-bs-toggle="pill" data-bs-target="#pills-tasks" type="button" role="tab"
                        aria-controls="pills-tasks" aria-selected="false">
                        <i class="bi bi-list-task"></i> Tasks
                    </button>
                </li>
            @endif
           
                <li class="nav-item" role="presentation">
                    <button class="nav-link case_tabs_btn  {{ $tab == 'document' ? 'active' : '' }}" id="pills-document-tab"
                        data-bs-toggle="pill" data-bs-target="#pills-document" type="button" role="tab"
                        aria-controls="pills-document" aria-selected="false">
                        <i class="bi bi-file-earmark-break"></i> Documents
                    </button>
                </li>
         
        </ul>

        <div class="tab-content" id="pills-tabContent">

            <div class="alert alert-success alert-dismissible fade show" role="alert" style="display: none">
                <button type="button" class="btn-close alert-success" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none">
                <button type="button" class="btn-close alert-danger" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            {{-- caseDetails start  --}}

            <div class="tab-pane fade {{ $tab == 'data' ? ' show active ' : '' }}" id="pills-home" role="tabpanel"
                aria-labelledby="pills-home-tab">
                <div class="case_tab_content">
                    @can('edit case')
                        <div class="action-btn bg-light-secondary ms-2 float-right m-3 edit_btn_view">
                            <a href="{{ route('cases.edit', $case->id) }}"
                                class="mx-3 btn btn-sm d-inline-flex align-items-center" title="{{ __('Edit') }}"
                                data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="ti ti-edit "></i>
                            </a>
                        </div>
                    @endcan
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="col-xl-12">
                                <div class="case_information_box">
                                    <div class="case_information_box_title">
                                        <h3 class="mb-0">
                                            Case Information
                                        </h3>
                                    </div>
                                    <div class="case_information_box_list">
                                        <ul>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Name:
                                                </p>
                                                <span class="">
                                                    {{ $case->name }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Case Number:
                                                </p>
                                                <span class="">
                                                    {{ $case->case_number }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Open Date:
                                                </p>
                                                <span class="">
                                                    {{ $case->open_date }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Close Date:
                                                </p>
                                                <span class="">
                                                    {{ $case->close_date }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Incident Date:
                                                </p>
                                                <span class="">
                                                    {{ $case->incident_date }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Statute of Limitations:
                                                </p>
                                                <span class="">
                                                    {{ $case->statute_of_limitations }}
                                                </span>
                                            </li>

                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Case Stage:
                                                </p>
                                                <span class="">
                                                    {{ $case->case_stage }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Practice Area:
                                                </p>
                                                <span class="">
                                                    {{ $case->practice_area }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-star">
                                                <p class="mb-0">
                                                    Members:
                                                </p>
                                                <span class="">
                                                    @php
                                                        $taskTeamArray = explode(',', $case->your_advocates);
                                                        $totalUsers = count($taskTeamArray);
                                                    @endphp
                                                    @foreach ($taskTeamArray as $index => $team)
                                                        @if (!empty($team))
                                                        <span style="font-size: 15px;font-family: 'tabler-icons';">
                                                            <i>  {{ UsersNameById($team) }}  </i> 
                                                                  @if ($index < $totalUsers - 1)
                                                                  <i>  |  </i> 
                                                        @endif
                                                        </span>
                                                        @else
                                                            <p>No Assignees</p>
                                                        @endif
                                                    @endforeach
                                                </span>
                                                
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-star">
                                                <p class="mb-0">
                                                    Clients:
                                                </p>
                                                <span class="">
                                                    @php
                                                        $taskTeamArray = explode(',', $case->your_team);
                                                        $totalUsers = count($taskTeamArray);
                                                    @endphp
                                                    @foreach ($taskTeamArray as $index => $team)
                                                        @if (!empty($team))
                                                            <span style="font-size: 15px;font-family: 'tabler-icons';">
                                                              <i> {{ UsersNameById($team) }} </i>
                                                              @if ($index < $totalUsers - 1)
                                                              <i>    | </i>
                                                                 @endif  
                                                            </span>
                                                           
                                                        @else
                                                            <p>No Assignees</p>
                                                        @endif
                                                    @endforeach
                                                </span>
                                                
                                            </li>
                                            <li
                                                class="list_Info active-scroll d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Description:
                                                </p>
                                                <span class="">
                                                    {!! $case->description !!}
                                                </span>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="case_information_box">
                                    <div class="case_information_box_title">
                                        <h3 class="mb-0">
                                            General
                                        </h3>
                                    </div>
                                    <div class="case_information_box_list">
                                        <ul>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Location of Accident:
                                                </p>
                                                <span class="">
                                                    {{ $case->location_of_accident }}
                                                </span>
                                            </li>

                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Intersection:
                                                </p>
                                                <span class="">
                                                    {{ $case->intersection }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Map Link/Coordinates:
                                                </p>
                                                <span class="">
                                                    {{ $case->coordinates }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="case_information_box">
                                    <div class="case_information_box_title">
                                        <h3 class="mb-0">
                                            Insurance
                                        </h3>
                                    </div>
                                    <div class="case_information_box_list">
                                        <ul>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Personal Injury Type:
                                                </p>
                                                <span class="">
                                                    {{ $case->injury_type }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    Case Manager:
                                                </p>
                                                <span class="">
                                                    {{ $case->case_manager }}
                                                </span>
                                            </li>
                                            <li class="list_Info d-flex justify-content-between align-items-start">
                                                <p class="mb-0">
                                                    File Location:
                                                </p>
                                                <span class="">
                                                    {{ $case->file_location }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="case_opponents_box custom-scroll">
                                <div class="Opponents_title">
                                    <h3>
                                        3rd Party
                                    </h3>
                                </div>
                                @if (!empty($case->opponents))
                                    @foreach (json_decode($case->opponents, true) as $key => $opp)
                                        <div class="Opponents_list mt-5">
                                            <h4>
                                                {{ __('3rd Party ' . $key + 1 . ':') }}
                                            </h4>
                                            <ul>
                                                <li
                                                    class="Opponents_list_info d-flex justify-content-between align-items-start">
                                                    <b class="mb-0">
                                                        Name:
                                                    </b>
                                                    <span class="">
                                                        {{ $opp['opponents_name'] }}
                                                    </span>
                                                </li>
                                                <li
                                                    class="Opponents_list_info d-flex justify-content-between align-items-start">
                                                    <b class="mb-0">
                                                        Email:
                                                    </b>
                                                    <span class="">
                                                        {{ $opp['opponents_email'] }}
                                                    </span>
                                                </li>
                                                <li
                                                    class="Opponents_list_info d-flex justify-content-between align-items-start">
                                                    <b class="mb-0">
                                                        Phone:
                                                    </b>
                                                    <span class="">
                                                        {{ $opp['opponents_phone'] }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    @endforeach
                                @endif

                            </div>

                            <div class="case_opponents_box custom-scroll" style="height: 570px">
                                <div class="Opponents_title">
                                    <h3>
                                        3rd Party Attorneys
                                    </h3>
                                </div>
                                @if (!empty($case->opponent_advocates))
                                    @foreach (json_decode($case->opponent_advocates, true) as $key => $opp)
                                        <div class="Opponents_list mt-5">
                                            <h4>
                                                {{ __('3rd Party Attorneys ' . $key + 1 . ':') }}
                                            </h4>
                                            <ul>
                                                <li
                                                    class="Opponents_list_info d-flex justify-content-between align-items-start">
                                                    <b class="mb-0">
                                                        Name:
                                                    </b>
                                                    <span class="">
                                                        {{ $opp['opp_advocates_name'] }}
                                                    </span>
                                                </li>
                                                <li
                                                    class="Opponents_list_info d-flex justify-content-between align-items-start">
                                                    <b class="mb-0">
                                                        Email:
                                                    </b>
                                                    <span class="">
                                                        {{ $opp['opp_advocates_email'] }}
                                                    </span>
                                                </li>
                                                <li
                                                    class="Opponents_list_info d-flex justify-content-between align-items-start">
                                                    <b class="mb-0">
                                                        Phone:
                                                    </b>
                                                    <span class="">
                                                        {{ $opp['opp_advocates_phone'] }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </div>






                    </div>

                    {{-- first_party and third --}}

                    <div class="row">
                        <div class="col-xl-6">
                            <div class="case_information_box">
                                <div class="case_information_box_title">
                                    <h3 class="mb-0">
                                        First Party
                                    </h3>
                                </div>
                                <div class="case_information_box_list">
                                    <ul>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Company Name:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_company_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Policy Number:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_policy_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Insurance Phone Number:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_insurance_phone_number }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Name:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Phone Number:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_phone_number }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Policy Limits:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_policy_limits }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Insured Name(s):
                                            </p>
                                            <span class="">
                                                {{ $case->first_insured_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Claim Number:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_claim_number }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Adjuster:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_adjuster }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Email:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_email }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Fax Number:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_fax }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="case_information_box">
                                <div class="case_information_box_title">
                                    <h3 class="mb-0">
                                        Third Party:
                                    </h3>
                                </div>
                                <div class="case_information_box_list">
                                    <ul>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Company Name:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_company_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Policy Number:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_policy_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Insurance Phone Number:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_phone_number }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Name:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Phone Number:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_phone_number }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Policy Limits:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_policy_limits }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Insured Name(s):
                                            </p>
                                            <span class="">
                                                {{ $case->third_insured_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Claim Number:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_claim_number }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Adjuster:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_adjuster }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Email:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_email }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Fax Number:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_fax }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>


                    </div>

                    {{-- vehicle info --}}
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="case_information_box">
                                <div class="case_information_box_title">
                                    <h3 class="mb-0">
                                        Vehicle Information (First Party)
                                    </h3>
                                </div>
                                <div class="case_information_box_list">
                                    <ul>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Driver Name:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_driver_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Vehicle Year:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_vehicle_year }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Vehicle Model:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_vehicle_model }}
                                            </span>
                                        </li>

                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Passenger Name:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_passenger_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Vehicle Make:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_vehicle_make }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Vehicle License Plate Number:
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_vehicle_license }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Are you driver or Passenger?
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_customer_type }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Airbags Deployed?
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_airbags_developed }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Seat belts worn?
                                            </p>
                                            <span class="">
                                                {{ $case->first_party_seat_belts_worn }}
                                            </span>
                                        </li>

                                        <h4 class="my-3">
                                            Emergency Contact
                                        </h4>

                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Name:
                                            </p>
                                            <span class="">
                                                {{ $case->emergency_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Phone Number:
                                            </p>
                                            <span class="">
                                                {{ $case->emergency_phone }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="case_information_box" style="height: 94%">
                                <div class="case_information_box_title">
                                    <h3 class="mb-0">
                                        Vehicle Information (Third Party)
                                    </h3>
                                </div>
                                <div class="case_information_box_list">
                                    <ul>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Driver Name:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_driver_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Vehicle Year:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_vehicle_year }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Vehicle Model:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_vehicle_model }}
                                            </span>
                                        </li>

                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Passenger Name:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_passenger_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Vehicle Make:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_vehicle_make }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Vehicle License Plate Number:
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_vehicle_license }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Are you driver or Passenger?
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_customer_type }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Airbags Deployed?
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_airbags_developed }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_other d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Seat belts worn?
                                            </p>
                                            <span class="">
                                                {{ $case->third_party_seat_belts_worn }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>


                    </div>

                    {{-- other --}}
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="case_information_box">
                                <div class="case_information_box_title">
                                    <h3 class="mb-0">
                                        Other
                                    </h3>
                                </div>
                                <div class="case_information_box_list">
                                    <ul>
                                        <li
                                            class="list_Info list_Info_setting_2 d-flex justify-content-between align-items-start ">

                                            <p class="mb-0">
                                                Police Report Number:
                                            </p>
                                            <span class="">
                                                {{ $case->police_report }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_setting_2 d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Have you spoken or recorded Statements with any insurance company?
                                            </p>
                                            <span class="">
                                                {{ $case->recorded_statement }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_setting_2 d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Recoded Statement Description:
                                            </p>
                                            <span class="">
                                                {{ $case->recorded_statement_description }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_setting_2 d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Name:
                                            </p>
                                            <span class="">
                                                {{ $case->other_name }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_setting_2 d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Phone Number:
                                            </p>
                                            <span class="">
                                                {{ $case->other_phone_number }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_setting_2 d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Email Address:
                                            </p>
                                            <span class="">
                                                {{ $case->other_email_address }}
                                            </span>
                                        </li>
                                        <li
                                            class="list_Info list_Info_setting_2 d-flex justify-content-between align-items-start">
                                            <p class="mb-0">
                                                Fax:
                                            </p>
                                            <span class="">
                                                {{ $case->other_fax }}
                                            </span>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>

            {{-- caseDetails end  --}}

            {{-- timeline start  --}}

            <div class="tab-pane fade {{ $tab == 'timeline' && \Auth::User()->can('timeline case') ? ' show active ' : '' }}"
                id="pills-timeline" role="tabpanel" aria-labelledby="pills-timeline-tab">
                <div class="case_tab_content">
                    <div class="case_information_box_title">
                        <h3 class="mb-0">
                            Change log
                        </h3>
                    </div>
                    <div class="">
                        <div id="accordion" class="myaccordion">

                            @php
                                $colors = ['#eff5e7', '#e9f4f2', '#ececf3', '#e9eff5'];
                                $colors_border = ['#8dc313', '#1dc1a3', '#7777bd', '#2584e4'];
                                $colorIndex = 0;
                                
                            @endphp
                            <style>
                                .before_bg_color::before {
                                    background-color: #8dc313;
                                }
                            </style>
                            @foreach ($timelines as $key => $timeline)
                                @php
                                    
                                    $day = substr($key, 8, 2); // Extract day from the key
                                    $month = date('M', strtotime($key));
                                    $colorClass = 'before_bg_color_' . $colorIndex;
                                    
                                    $isLastIndex = $key === array_key_last($timelines);
                                    $cardClass = 'card_' . ($isLastIndex ? 'last' : 'not-last');
                                @endphp


                                <div class="timeline_tabs">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="timeline_date"
                                            style="border:1px solid {{ $colors_border[$colorIndex] }}">
                                            <div class="timeline_date_inner text-center"
                                                style="border:1px solid {{ $colors[$colorIndex] }}">
                                                <div class="timeline_date_inner_calander"
                                                    style="color: {{ $colors_border[$colorIndex] }}">
                                                    <i class="fa-regular fa-calendar"></i>
                                                </div>
                                                <h4>
                                                    <span>
                                                        {{ $day }}
                                                    </span>
                                                    {{ $month }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div style="margin-left: -3px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="138" height="7"
                                                viewBox="0 0 138 7" fill="none">
                                                <circle cx="3.87711" cy="3.44266" r="3.29508"
                                                    fill="{{ $colors_border[$colorIndex] }}" />
                                                <path d="M8.8197 3.44263H137.328"
                                                    stroke="{{ $colors_border[$colorIndex] }}" stroke-dasharray="2 2" />
                                            </svg>
                                        </div>
                                    </div>


                                    <div
                                        class="timeline_tabs {{ count($timelines) === 1 ? 'timeline_wrapper_one' : ($key === key($timelines) ? 'timeline_wrapper_content' : ($key === array_key_last($timelines) ? 'timeline_wrapper_content_bottom' : 'timeline_wrapper_content_mid')) }} content_area before_bg_color_{{ $colorIndex }}">

                                        <style>
                                            .{{ $colorClass }}::before {
                                                background-color: {{ $colors_border[$colorIndex] }};
                                            }
                                        </style>
                                        <div class="card {{ $cardClass }}"
                                            style="background-color: {{ $colors[$colorIndex] }}; {{ $isLastIndex ? 'margin-bottom: 0px;' : '' }}">
                                            <div class="card-header" id="headingOne">
                                                <h2 class="mb-0 w-100 d-flex align-items-center justify-content-between">
                                                    <span class="timeline_tabs_title"
                                                        style="border-bottom: 2px solid {{ $colors_border[$colorIndex] }};">
                                                        Change logs
                                                    </span>
                                                    <button style="border: 2px solid {{ $colors_border[$colorIndex] }};"
                                                        class="d-flex align-items-center p-0 justify-content-center btn btn-link collapsed"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-{{ $key }}"
                                                        aria-expanded="false" aria-controls="collapseOne">
                                                        <span class="">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </span>
                                                    </button>
                                                </h2>
                                            </div>
                                            <div id="collapse-{{ $key }}" class="collapse"
                                                aria-labelledby="headingOne">
                                                <div class="card-body">
                                                    @foreach ($timeline as $index => $time)
                                                        @php
                                                            $start_time = $time->start_time;
                                                            $startDateTime = new DateTime($start_time);
                                                            
                                                            $formattedDay = $startDateTime->format('d');
                                                            $formattedMonth = $startDateTime->format('F');
                                                            
                                                            $end_time = $time->end_time;
                                                            
                                                            $startDateTime = new DateTime($start_time);
                                                            $endDateTime = new DateTime($end_time);
                                                            
                                                            $timeDifference = $startDateTime->diff($endDateTime);
                                                            
                                                            $formattedTimeDifference = $timeDifference->format('%H:%I:%S');
                                                            
                                                        @endphp

                                                        <div class="changes_log_detail">

                                                            <h4
                                                                class="mb-0 w-100 d-flex align-items-center justify-content-between">
                                                                <span class="timeline_tabs_title">
                                                                    {{ $time->log }}
                                                                </span>

                                                            </h4>
                                                            <ul class="px-3 py-4">
                                                                <li
                                                                    class="list_Info d-flex justify-content-between align-items-start">
                                                                    <p class="mb-0">
                                                                        Created By:
                                                                    </p>
                                                                    <span class="">
                                                                        {{ $time->user->email }}
                                                                    </span>
                                                                </li>
                                                                <li
                                                                    class="list_Info d-flex justify-content-between align-items-start">
                                                                    <p class="mb-0">
                                                                        Access Time:
                                                                    </p>
                                                                    <span class="">
                                                                        {{ $start_time }} (Timezone: UTC
                                                                        {{ $utc_offset . ' ' . $timezoneName }})
                                                                    </span>
                                                                </li>
                                                                <li
                                                                    class="list_Info d-flex justify-content-between align-items-start">
                                                                    <p class="mb-0">
                                                                        Save Time:
                                                                    </p>
                                                                    <span class="">
                                                                        {{ $end_time }} (Timezone: UTC
                                                                        {{ $utc_offset . ' ' . $timezoneName }})
                                                                    </span>
                                                                </li>
                                                                <li
                                                                    class="list_Info d-flex justify-content-between align-items-start">
                                                                    <p class="mb-0">
                                                                        Elapsed Time:
                                                                    </p>
                                                                    <span class="">
                                                                        {{ $formattedTimeDifference }}
                                                                    </span>
                                                                </li>
                                                                <li
                                                                    class="list_Info d-flex justify-content-between align-items-start changes_done_log">
                                                                    <p class="mb-0">
                                                                        Changes Done:
                                                                    </p>
                                                                    <span class="">
                                                                        @php
                                                                            $change_case = ucwords(str_replace('_', ' ', $time->edit_case ?? ''));
                                                                            $final_change = ucwords(str_replace(',', ' | ', $change_case ?? ''));
                                                                        @endphp
                                                                        {{ $final_change }}
                                                                    </span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $colorIndex = ($colorIndex + 1) % count($colors); // Cycle through colors
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- timeline end  --}}

            {{-- calendar start  --}}

            <div class="tab-pane fade {{ $tab == 'calendar' && (Auth::user()->type == 'company' || Auth::user()->type == 'co admin' || Auth::user()->type == 'super admin' || Auth::User()->can('calendar case')) ? ' show active ' : '' }}"
                id="pills-calendar" role="tabpanel" aria-labelledby="pills-calendar-tab">

                @php
                    function getDayLabel($date)
                    {
                        $carbonDate = Carbon\Carbon::parse($date);
                        $now = Carbon\Carbon::now();
                    
                        if ($carbonDate->isSameDay($now)) {
                            return 'Today';
                        } elseif ($carbonDate->isTomorrow($now)) {
                            return 'Tomorrow';
                        } else {
                            return $carbonDate->format('l');
                        }
                    }
                @endphp
                <div class="responsive_calander">
                    <div class="calendar_wrapper mt-0">
                        <div class="mini_calendar_wrapper">
                            <div id="calendar-a"></div>
                            <div id="calendar-stats"></div>
                        </div>

                        <div class="tui_calendar_wrapper">
                            <div>
                                <div class="form-group has-search">
                                    <span class="fa fa-search form-control-feedback"></span>
                                    <input type="text" class="form-control" placeholder="Search">
                                </div>
                                <div id="calendar" style="height: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- calendar end  --}}

            {{-- task start  --}}

            <div class="tab-pane fade {{ $tab == 'tasks' ? ' show active ' : '' }}" id="pills-tasks" role="tabpanel"
                aria-labelledby="pills-tasks-tab">

                <div class="case_tab_content">
                    @can('create tasks')
                        <div class="text-end my-3">
                            <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true"
                                data-size="xl addTaskModal_wrapper" data-title="Add Task"
                                data-url="{{ route('taskcase.create', $case->id) }}" data-toggle="tooltip"
                                title="{{ __('Create New Task') }}" data-bs-original-title="{{ __('Create New Task') }}"
                                data-bs-placement="top" data-bs-toggle="tooltip">
                                <i class="ti ti-plus"></i>
                            </a>
                        </div>
                    @endcan



                    @php
                        $colors = ['#0095D5', '#FFAE1F', '#FF7171', '#37C99D'];
                        $colorIndex = 0;
                    @endphp

                    <div class="container ">
                        <h1 class="text-primary text-center fw-bold fs-2">Tasks Progress</h1>
                        <div class="row mt-4">



                            @foreach ($taskDataByStatus as $status => $taskData)
                                @php
                                    $colorClass = $colors[$colorIndex % count($colors)];
                                    $colorIndex++;
                                @endphp

                                <div class="col-md-3">
                                    <div class="card Task_dropzone">
                                        <div style="background-color: {{ $colorClass }}"
                                            class="card-header text-white fw-bold text-center">
                                            {{ $status }}
                                        </div>
                                        <ul id="sortable-list-{{ explode(' ', $status)[0] }}"
                                            class="list-group custom-scroll list-group-flush"
                                            style="height: 500px; overflow-y: scroll;">

                                            @foreach ($taskData as $task)
                                                <style>
                                                    @php $colors =['#0095D5', '#FFAE1F', '#FF7171', '#37C99D'];
                                                    $priority =$task->priority;

                                                    if ($priority =='Low') {
                                                        $taskColor =$colors[0];
                                                    }

                                                    elseif ($priority =='Medium') {
                                                        $taskColor =$colors[1];
                                                    }

                                                    elseif ($priority =='High') {
                                                        $taskColor =$colors[2];
                                                    }

                                                    else {
                                                        $taskColor =$colors[3];
                                                    }
                                                    @endphp

                                                    .border_color_priority-{{ $task->id }}::after {
                                                        background: {{ $taskColor }};
                                                        content: '';
                                                        position: absolute;
                                                        border-radius: 0px 5px 5px 0px;
                                                        width: 5px;
                                                        height: 100%;
                                                        top: 0;
                                                        right: 0;
                                                        bottom: 0;
                                                    }

                                                    .text-priority-color-{{ $task->id }} {
                                                        color: {{ $taskColor }} !important;
                                                    }
                                                </style>

                                                <li class="task-item card-drag js-sortable sortablejs-custom"
                                                    style="border-radius: 5px;" data-task-id="{{ $task->id }}">
                                                    <div class="accordion border_color_priority border_color_priority-{{ $task->id }}"
                                                        id="accordionExample">
                                                        <div class="accordion-item task_accordion">
                                                            <h2 class="accordion-header" id="headingOne">
                                                                <button class="accordion-button task_title" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseOne-{{ $task->id }}"
                                                                    aria-expanded="false"
                                                                    aria-controls="collapseOne-{{ $task->id }}">
                                                                    Task #{{ $task->id }}
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOne-{{ $task->id }}"
                                                                class="accordion-collapse collapse"
                                                                aria-labelledby="headingOne"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="action-models text-end px-3">
                                                                    @can('view tasks')
                                                                        <div class="action-btn bg-light-secondary ms-2">

                                                                            <a href="#"
                                                                                class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                                                data-url="{{ route('to-do.show', $task->id) }}"
                                                                                data-size="xl addTaskModal_wrapper"
                                                                                data-ajax-popup="true"
                                                                                data-title="{{ __('Task View') }}"
                                                                                title="{{ __('View Task') }}"
                                                                                data-bs-toggle="tooltip"
                                                                                data-bs-placement="top"> <i
                                                                                    class="ti ti-eye "></i> </a>
                                                                        </div>
                                                                    @endcan
                                                                    @can('delete tasks')
                                                                        <div class="action-btn bg-light-secondary ms-2">
                                                                            <a href="#" id="delete-button"
                                                                                class="mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para delete-button"
                                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                                data-confirm-yes="delete-form-{{ $task->id }}"
                                                                                title="{{ __('Delete') }}"
                                                                                data-bs-toggle="tooltip"
                                                                                data-bs-placement="top">
                                                                                <i class="ti ti-trash"></i>
                                                                            </a>
                                                                        </div>
                                                                    @endcan
                                                                    {!! Form::open([
                                                                        'method' => 'DELETE',
                                                                        'route' => ['tasks.destroy', $task->id],
                                                                        'id' => 'delete-form-' . $task->id,
                                                                    ]) !!}
                                                                    {!! Form::close() !!}
                                                                </div>
                                                                <div class="accordion-body">
                                                                    <div class="case_information_accordion">

                                                                        <ul>
                                                                            <li
                                                                                class="d-flex justify-content-between align-items-start gap-3">
                                                                                <p class="mb-0">
                                                                                    Task title:
                                                                                </p>
                                                                                <span class="">
                                                                                    {{ $task->title }}
                                                                                </span>
                                                                            </li>
                                                                            <li
                                                                                class="d-flex justify-content-between align-items-start gap-3">
                                                                                <p class="mb-0">
                                                                                    Task Priority:
                                                                                </p>
                                                                                <span
                                                                                    class="Priority_span text-priority-color-{{ $task->id }}">
                                                                                    {{ $task->priority }}
                                                                                </span>
                                                                            </li>
                                                                            <li
                                                                                class="d-flex justify-content-between align-items-start gap-3">
                                                                                <p class="mb-0">
                                                                                    Date :
                                                                                </p>
                                                                                <span class="Priority_span">
                                                                                    {{ $task->date }}
                                                                                </span>
                                                                            </li>
                                                                            <li
                                                                                class="d-flex justify-content-between align-items-start gap-3">
                                                                                <p class="mb-0">
                                                                                    Added By :
                                                                                </p>
                                                                                <span class="Priority_span">
                                                                                    @if ($task->created_by)
                                                                                        {{ UsersNameById($task->created_by) }}
                                                                                    @endif

                                                                                </span>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach

                                            @can('create tasks')
                                                <div class="m-2 notask" id="addTaskButtonPlaceholder">
                                                    <div class="alert alert-info text-center">
                                                        <a href="#"
                                                            class="p-0 m-0 btn text-end btn-transparnt add-task-list"
                                                            data-ajax-popup="true" data-size="xl addTaskModal_wrapper"
                                                            data-title="Add Task"
                                                            data-url="{{ route('taskcase.create', $case->id) }}"
                                                            data-toggle="tooltip" title="{{ __('Create New Task') }}"
                                                            data-bs-original-title="{{ __('Create New Task') }}"
                                                            data-bs-placement="top" data-bs-toggle="tooltip">
                                                            Click to Add Task
                                                        </a>

                                                    </div>
                                                </div>
                                            @endcan

                                        </ul>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>


                </div>

                {{-- end task priority wise --}}


            </div>

            {{--  task end --}}

            {{-- document start --}}
            <div class="tab-pane fade {{ $tab == 'document' ? ' show active ' : '' }}" id="pills-document" role="tabpanel"
                aria-labelledby="pills-document-tab">
                <div class="case_tab_content">

                    {{-- documents --}}
                    @if (!empty($documents))
                        <div class="row mt-5">
                            <div class="col">
                                <div class="case_information_box">
                                    <div class="case_information_box_title">
                                        <h3 class="mb-0" id="documents-section">
                                            Documents
                                        </h3>
                                    </div>
                                    @php
                                        $indx = 0;
                                    @endphp
                                    @foreach (json_decode($case->case_docs) as $folder)
                                        @php
                                            $indx++;
                                        @endphp
                                        <div class="mt-4 custom_table">
                                            <div class="row mb-3">
                                                <h4 class="mb-3 text-primary-{{ $setting['color'] }} mt-2">
                                                    <b> {{ $folder->folder_name }}:</b>
                                                </h4>
                                                <div class="col-md-11 list_Info">
                                                    <span class="">
                                                        {{ $folder->folder_description }}
                                                    </span>
                                                </div>
                                            </div>

                                            <table class="table table-borderless mt-3">
                                                <thead class="thead_light">
                                                    <tr>
                                                        <th scope="col">Sr.No</th>
                                                        <th scope="col">File Name</th>
                                                        <th scope="col">File Description</th>
                                                        <th scope="col">File Upload Time</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>


                                                    @foreach ($folder->docData as $document)
                                                        @foreach ($document->files as $index => $file)
                                                            @php
                                                                
                                                                if (preg_match('/\/(\d+)\./', $file, $matches)) {
                                                                    $numericPart = $matches[1];
                                                                
                                                                    $timestamp = intval($numericPart);
                                                                    $humanReadableTime = date('F j, Y', $timestamp);
                                                                } else {
                                                                    $humanReadableTime = '----';
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $document->doc_name }}</td>
                                                                <td><span>{{ $document->doc_des }}</span></td>
                                                                <td>{{ $humanReadableTime }}</td>
                                                                <td>
                                                                    @php
                                                                        $temFile = decryptFile($originalPath . $file, pathinfo($file)['extension']);
                                                                    @endphp
                                                                    <div
                                                                        class="d-flex justify-content-start align-items-center">
                                                                        <a onclick="openFilePreview('{{ $docfileTmp . $temFile }}')"  class="btn action_btn p-2">
                                                                            <i class="bi bi-eye-fill"></i>
                                                                        </a>
                                                                        <a href="{{ $docfileTmp . $temFile }}"
                                                                            target="_blank" download
                                                                            class="btn action_btn p-2">
                                                                            <i class="bi bi-box-arrow-down"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <br>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @endif
                </div>
            </div>
            {{-- document end --}}

        </div>
    </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                  
                            <a href="" target="_blank" download
                            class="btn action_btn_download p-2">
                            <i class="bi bi-box-arrow-down"></i>
                        </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="modal-body">


                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-script')
    <script src="https://uicdn.toast.com/tui.code-snippet/v1.5.2/tui-code-snippet.min.js"></script>
    <script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.js"></script>
    <script src="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.js"></script>
    <script src="https://uicdn.toast.com/tui-calendar/latest/tui-calendar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/color-calendar/dist/bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

     
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function openFilePreview(url) {
            // Clear previous content in the modal
            $('#previewModal .modal-body').empty();

            // Check the file type based on its extension
            var fileExtension = url.split('.').pop().toLowerCase();

            console.log(fileExtension);

            if (fileExtension === 'pdf') {
                // For PDF files, use an iframe to display the PDF
                $('#previewModal .modal-body').html('<iframe src="' + url +
                    '" frameborder="0" style="width: 100%; height: 400px;"></iframe>');

                    const downloadLink = $('.action_btn_download'); 
                        downloadLink.attr('href', url);
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {

                $('#previewModal .modal-body').html('<img src="' + url +
                    '" style="max-width: 100%; max-height: 400px;" alt="Preview">');

                    const downloadLink = $('.action_btn_download'); 
                        downloadLink.attr('href', url);

            } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
                fetch(url)
                    .then(function(response) {
                        return response.arrayBuffer();
                    })
                    .then(function(data) {
                        var workbook = XLSX.read(data, {
                            type: 'array'
                        });

                        var sheet = workbook.Sheets[workbook.SheetNames[0]];

                        var tableHTML = XLSX.utils.sheet_to_html(sheet);

                        // Set a class for the table for styling
                        var tableClass = 'custom-table';

                        // Wrap the table in a div with a class
                        var styledTableHTML = '<div style="overflow-x: auto;" class="' + tableClass + '">' + tableHTML +
                            '</div>';

                        $('#previewModal .modal-body').html(styledTableHTML);
                        const downloadLink = $('.action_btn_download'); 
                        downloadLink.attr('href', url);
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                    });
            } else if (fileExtension === 'docx') {
                fetch(url)
                    .then(response => response.arrayBuffer())
                    .then(arrayBuffer => mammoth.convertToHtml({
                        arrayBuffer: arrayBuffer
                    }))
                    .then(result => {
                       
                        const htmlContent = result.value;
                        // Wrap the HTML content in a temporary div to manipulate it
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = htmlContent;

                        // Find all images in the HTML content and add the 'width: 100%' style
                        const images = tempDiv.querySelectorAll('img');
                        images.forEach(img => {
                            img.style.width = '100%';
                        });

                        // Set the modified HTML content in the modal's body
                        $('#previewModal .modal-body').html(tempDiv.innerHTML);
                        const downloadLink = $('.action_btn_download'); 
                        downloadLink.attr('href', url);
                        $('#previewModal').modal('show');
                    })
                    .catch(error => console.error(error));
            }

            else {
                // Handle other formats here or show an error message
                $('#previewModal .modal-body').html('<p class="text-center">This file type is not supported for preview.</p>');
                $('#previewModal').modal('show');
                const downloadLink = $('.action_btn_download'); 
                downloadLink.attr('href', url);
            }

            $('#previewModal').modal('show');
        }
  
    </script>
    <script>
        $("#accordion").on("hide.bs.collapse show.bs.collapse", e => {
            $(e.target)
                .prev()
                .find("i:last-child")
                .toggleClass("fa-eye fa-eye-slash");
        });
    </script>
    <script>
        tinymce.init({
            selector: 'textarea[name="tasks[title]"]',
            height: "400",
            content_style: 'body { font-family: "Inter", sans-serif; }',
            setup: function(editor) {
                editor.on('submit', function(e) {
                    // Update the hidden textarea with the editor content
                    editor.save();
                });
            }
        });
    </script>
    <script>
        // Function to initialize Sortable.js for a given list
        function initializeSortable(listId) {
            new Sortable(document.getElementById(listId), {
                animation: 150,
                group: "listGroup",
                onEnd: function(evt) {
                    // Get the dragged card element
                    const draggedCard = evt.item;

                    // Get the source and destination status lists
                    const sourcestatus = draggedCard.getAttribute("data-task-status");
                    const taskId = draggedCard.getAttribute("data-task-id");

                    const deststatus = evt.to.id.replace("sortable-list-", "");

                    $.ajax({
                        url: '/tasks/update-priority/',
                        method: 'POST',
                        data: {
                            status: deststatus,
                            taskId: taskId
                        },
                        success: function(response) {},
                        error: function(xhr, status, error) {}
                    });

                    // Update the card's data-task-status attribute
                    draggedCard.setAttribute("data-task-status", deststatus);

                    toggleNoTasksMessage(sourcestatus);
                    toggleNoTasksMessage(deststatus);
                },
            });
        }


        // Function to hide/show the "No tasks present" message
        function toggleNoTasksMessage(status) {
            const $ul = $('#sortable-list-' + status);
            const $noTasksMessage = $ul.find('.notask');

            if ($ul.find('.task-item').length === 0) {
                $noTasksMessage.show();
            } else {
                $noTasksMessage.hide();
            }
        }




        const statusParts = {};
        const simplifiedStatus = {};

        @foreach ($taskDataByStatus as $status => $taskData)
            statusParts["{{ $status }}"] = "{{ $status }}".split(' ');
            simplifiedStatus["{{ $status }}"] = statusParts["{{ $status }}"][0];

            initializeSortable(`sortable-list-${simplifiedStatus["{{ $status }}"]}`);
            toggleNoTasksMessage(simplifiedStatus["{{ $status }}"]);
        @endforeach



        document.addEventListener("DOMContentLoaded", function() {
            function setModalstatus(status) {
                const modal = document.getElementById("addTaskModal");
                const statusInput = modal.querySelector('select[name="tasks[status]"]');
                statusInput.value = status;
            }

            // Event listener for the Add Task button clicks
            const addTaskButtons = document.querySelectorAll(".add-task-list");
            addTaskButtons.forEach(function(button) {
                button.addEventListener("click", function() {
                    const status = button.getAttribute("data-status");
                    setModalstatus(status);
                });
            });
        });
    </script>




    <script>
        $(document).ready(function() {


            const incidentDateInput = document.getElementById('Task_Date');
            const redBorderColorClass = 'red-border';


            const incidentPicker = new Pikaday({
                field: incidentDateInput,
                format: 'YYYY-MM-DD',
                onSelect: function(selectedDate) {
                    const formattedDate = formatDate(selectedDate);
                    incidentDateInput.value = formattedDate;
                    const currentDate = new Date();
                    currentDate.setHours(0, 0, 0, 0); // Set the time to midnight
                    if (selectedDate <= currentDate) {
                        incidentDateInput.classList.add(redBorderColorClass);
                    } else {
                        incidentDateInput.classList.remove(
                            redBorderColorClass);
                    }
                },
            });

            function formatDate(date) {
                console.log(date);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2,
                    '0'); // Adding 1 because months are zero-based
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            incidentDateInput.addEventListener('input', function() {
                console.log(this);
                autoFormatDate(this);
                validateAndAdjustDate(this);
                incidentDateInput.classList.remove(redBorderColorClass);

            });

            function autoFormatDate(input) {
                let value = input.value.replace(/[^\d-]/g, ''); // Allow only digits and hyphens

                // Remove extra hyphens
                value = value.replace(/-{2,}/g, '-');

                // Restrict to a maximum of 10 characters
                value = value.substring(0, 10);

                if (value.length >= 4 && value[4] !== '-') {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                }

                if (value.length >= 7 && value[7] !== '-') {
                    value = value.substring(0, 7) + '-' + value.substring(7);
                }

                input.value = value;
            }
            const calendarIconIncident = document.getElementById('calendar_icon_Task_Date');
            calendarIconIncident.addEventListener('click', function() {
                incidentPicker.show();
            });

            incidentDateInput.addEventListener('input', function() {
                autoFormatDate(this);
                validateAndAdjustDate(this);
                incidentDateInput.classList.remove(redBorderColorClass);

            });

            function autoFormatDate(input) {
                let value = input.value.replace(/[^\d-]/g, ''); // Allow only digits and hyphens
                console.log(value);
                // Remove extra hyphens
                value = value.replace(/-{2,}/g, '-');

                // Restrict to a maximum of 10 characters
                value = value.substring(0, 10);

                if (value.length >= 4 && value[4] !== '-') {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                }

                if (value.length >= 7 && value[7] !== '-') {
                    value = value.substring(0, 7) + '-' + value.substring(7);
                }

                input.value = value;
            }

            function validateAndAdjustDate(input) {

                const parts = input.value.split('-');
                console.log(parts);

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

                    console.log(numberOfDayDigits);

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


        });


        $(document).ready(function() {
            $(document).on("click", ".open-edit-modal", function(e) {
                e.preventDefault();

                var taskId = $(this).data('task-id');
                var url = "{{ route('tasks.edit', ['taskId' => ':taskId']) }}".replace(':taskId', taskId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#editTaskModalBody').html(response);
                        $('#editTaskModal').modal('show');
                    }
                });
            });

            $(document).on("click", ".open-show-modal", function(e) {
                e.preventDefault();

                var taskId = $(this).data('task-id');
                var url = "{{ route('tasks.show', ['taskId' => ':taskId']) }}".replace(':taskId', taskId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#showTaskModalBody').html(response);
                        $('#showTaskModal').modal('show');
                    }
                });
            });


        });
    </script>


    {{-- task js end --}}

    <script>
        var isUpdate = false;
        $(document).on('click', '.tui-full-calendar-popup-save', function() {
            isUpdate = true;
        });
        var userTimezone = {!! json_encode($timeZone?->timezone ?? 'America/New_York') !!};
        var userTimeZoneUTC = {!! json_encode($timeZone?->utc_offset ?? 'America/New_York') !!};

        var Calendar2 = window.tui.Calendar;
        var calendar = new Calendar2('#calendar', {
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
            useFormPopup: true,
            useDetailPopup: true,
            // Set the timezone for TUI Calendar
            timezones: [{
                timezoneName: userTimezone,
            }],
            usageStatistics: false,

            template: {
                // Customize the add event popup
                popupDetailBody: function() {
                    // Customize the content of the add event popup
                    const content = `
                <p class='button-bg-color'>Update</p>`;
                    return content;
                }
            },
            timezone: {
                zones: [{
                    timezoneName: {!! json_encode($timeZone?->timezone ?? 'America/New_York') !!},
                }, ],
            },

        });


        function utcOffsetToMinutes(utcOffsetString) {
            const sign = utcOffsetString.charAt(0) === '-' ? -1 : 1;
            const [hours, minutes] = utcOffsetString.substr(1).split(':').map(Number);

            return sign * (hours * 60 + minutes);
        }

        function resetDateTime(date, multi = 2) {
            const gmtOffsetMinutes = -date.getTimezoneOffset();
            var utcmint = utcOffsetToMinutes(userTimeZoneUTC);

            const date2 = new Date(date);
            addedMinutes = multi * ((gmtOffsetMinutes) - (utcmint));
            return new Date(date2.getTime() + addedMinutes * 60 * 1000);

        }
        calendar.on('beforeCreateSchedule', (eventData) => {

            const startDateISO = eventData.start._date;
            const startSetDate = resetDateTime(startDateISO);
            const startDate = new Date(startSetDate);
            // Apply the user's selected timezone to the start date
            const formattedStartDate = startDate.toLocaleString('en-US', {
                timeZone: userTimezone
            });
            // console.log(formattedStartDate); return false;
            eventData.start = formattedStartDate;

            const endDateISO = eventData.end._date;
            const endSetDate = resetDateTime(endDateISO);

            const endDate = new Date(endSetDate);

            // Apply the user's selected timezone to the end date
            const formattedEndDate = endDate.toLocaleString('en-US', {
                timeZone: userTimezone
            });

            eventData.end = formattedEndDate;
            // caseId
            const userTimezoneOffset = new Date().getTimezoneOffset(); // Time zone offset in minutes
            const gmtOffsetMinutes = -startDateISO.getTimezoneOffset();
            var utcmint = utcOffsetToMinutes(userTimeZoneUTC);
            const date = new Date(eventData.start);

            const addedMinutes = (gmtOffsetMinutes) - (utcmint);
            const newDate = new Date(date.getTime() + addedMinutes * 60 * 1000);

            eventData.caseId = '{{ $case->id }}';

            let url = '{{ route('google.calendar.create-event') }}';
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    eventData: eventData
                },
                success: function(response) {
                    if (response) {
                        updateCalender();
                        // .alert-success to show success message
                        if (response.message) {
                            $('.alert-success').show();
                            $('.alert-success').html(response.message);
                        } else {

                            $('.alert-danger').show();
                            $('.alert-danger').html(response.error);
                        }
                    } else {
                        console.log('swdwouehyw');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            })
        });

        calendar.on('beforeUpdateSchedule', (eventData) => {

            setTimeout(function() {
                const updatedEvent = {
                    id: eventData.schedule.id,
                    calendarId: eventData.schedule.calendarId,
                    title: eventData.changes.title || eventData.schedule.title,
                    category: eventData.changes.category || eventData.schedule.category,
                    start: eventData.changes.start ? eventData.changes.start : eventData.schedule.start,
                    end: eventData.changes.end ? eventData.changes.end : eventData.schedule.end,
                };

                // Format start date if changes are provided
                if (eventData.changes.start) {
                    const startDateISO = eventData.changes.start._date;
                    var multiple = isUpdate ? 2 : 1;

                    const startSetDate = resetDateTime(startDateISO, multiple);
                    const startDate = new Date(startSetDate);
                    const formattedStartDate = startDate.toLocaleString('en-US', {
                        timeZone: userTimezone
                    });
                    updatedEvent.start = formattedStartDate;
                } else {

                    const startDateISO = eventData.schedule.start._date;
                    const startSetDate = resetDateTime(startDateISO, 1);
                    const startDate = new Date(startSetDate);
                    const formattedStartDate = startDate.toLocaleString('en-US', {
                        timeZone: userTimezone
                    });
                    updatedEvent.start = formattedStartDate;
                }

                // Format end date if changes are provided
                if (eventData.changes.end) {
                    const endDateISO = eventData.changes.end._date;
                    var multiple = isUpdate ? 2 : 1;

                    const endSetDate = resetDateTime(endDateISO, multiple);

                    const endDate = new Date(endSetDate);
                    const formattedEndDate = endDate.toLocaleString('en-US', {
                        timeZone: userTimezone
                    });

                    updatedEvent.end = formattedEndDate;
                } else {
                    const endDateISO = eventData.schedule.end._date;
                    const endSetDate = resetDateTime(endDateISO, 1);
                    const endDate = new Date(endSetDate);
                    const formattedEndDate = endDate.toLocaleString('en-US', {
                        timeZone: userTimezone
                    });

                    updatedEvent.end = formattedEndDate;
                }
                multiple = 1;
                isUpdate = false;

                // console.log(updatedEvent); return false;
                let url = '{{ route('google.calendar.update-event') }}';

                // Perform an AJAX request to update the event on the server
                $.ajax({
                    url: url, // Replace with your update event route
                    method: 'POST',
                    dataType: 'json',
                    data: updatedEvent,
                    success: function(response) {
                        if (response) {
                            updateCalender();
                            // .alert-success to show success message
                            if (response.message) {
                                $('.alert-success').show();
                                $('.alert-success').html(response.message);
                            } else {

                                $('.alert-danger').show();
                                $('.alert-danger').html(response.error);
                            }
                        } else {
                            console.error('Error updating event on the server');
                        }
                    },
                    error: function(error) {
                        console.error('AJAX error:', error);
                    }
                });
            }, 1000);
        });


        calendar.on('beforeDeleteSchedule', (eventData) => {
            console.log(eventData.schedule.id);
            // url 
            let url = '{{ route('google.calendar.delete-event') }}';
            // Perform an AJAX request to delete the event on the server
            $.ajax({
                url: url, // Replace with your delete event route
                method: 'POST',
                dataType: 'json',
                data: {
                    id: eventData.schedule.id,
                    calendarId: eventData.schedule.calendarId,
                },
                success: function(response) {
                    if (response) {
                        updateCalender();
                        // .alert-success to show success message
                        if (response.message) {
                            $('.alert-success').show();
                            $('.alert-success').html(response.message);
                        } else {

                            $('.alert-danger').show();
                            $('.alert-danger').html(response.error);
                        }
                    } else {
                        console.error('Error deleting event on the server');
                    }
                },
                error: function(error) {
                    console.error('AJAX error:', error);
                }
            });

        });
    </script>
    @can('calendar case')
        <script>
            $(document).ready(function() {
                var currentUrl = window.location.href;
                if (currentUrl.includes("?tab=calendar")) {
                    updateCalender();
                }

                var calendarTabButton = document.getElementById("pills-calendar-tab");
                calendarTabButton.addEventListener("click", function() {
                    updateCalender();
                });
            });
        </script>
    @endcan

    <script>
        $("#accordion").on("hide.bs.collapse show.bs.collapse", e => {
            $(e.target)
                .prev()
                .find("i:last-child")
                .toggleClass("fa-eye fa-eye-slash");
        });

        function updateCalender() {
            var url = '/cases/getEvents/' + '{{ $case->id }}';
            $.ajax({
                url: url, // Replace with your actual route URL
                type: 'GET',
                dataType: 'json',
                success: function(events) {
                    if (events.redirectTo) {
                        window.location.href = events.redirectTo;
                        return;
                    }
                    const processedEvents = events.map(event => {
                        const startDay = new Date(event.start.dateTime).getDay();
                        let backgroundColor = '';
                        let textColor = '#000000'; // Default text color

                        switch (startDay) {
                            case 0: // Sunday
                                backgroundColor = '#808080'; // White background
                                break;
                            case 1: // Monday
                                backgroundColor = '#0074D9'; // Blue background
                                break;
                            case 2: // Tuesday
                                backgroundColor = '#FF4136'; // Red background
                                break;
                            case 3: // Wednesday
                                backgroundColor = '#2ECC40'; // Green background
                                break;
                            case 4: // Thursday
                                backgroundColor = '#FF851B'; // Orange background
                                break;
                            case 5: // Friday
                                backgroundColor = '#FFDC00'; // Yellow background
                                break;
                            case 6: // Saturday
                                backgroundColor = '#B10DC9'; // Purple background
                                break;
                            default:
                                break;
                        }
                        return {
                            id: event.id,
                            calendarId: 'primary',
                            title: event.summary,
                            category: 'time',
                            start: new Date(event.start.dateTime),
                            end: new Date(event.end.dateTime),
                            color: '#FFFFFF',
                            bgColor: backgroundColor,
                            // Add other properties as needed
                        };
                    });
                    const miniCalEventData = events.map(event => ({
                        id: event.id,
                        name: event.summary,
                        start: new Date(event.start.dateTime.toLocaleString('en-US', {
                            timeZone: userTimezone
                        })),
                        end: new Date(event.end.dateTime.toLocaleString('en-US', {
                            timeZone: userTimezone
                        })),
                        // Add other properties as needed
                    }));
                    let calA = new Calendar({
                        id: "#calendar-a",
                        theme: "basic",
                        weekdayType: "long-upper",
                        monthDisplayType: "long",
                        calendarSize: "small",
                        layoutModifiers: ["month-left-align"],
                        eventsData: miniCalEventData,
                        dateChanged: (currentDate, events) => {
                            console.log("date change", currentDate, events);
                            calendar.setDate(currentDate);
                        },
                        monthChanged: (currentDate, events) => {
                            console.log("month change", currentDate, events);
                            calendar.setDate(currentDate);
                        }
                    });
                    // SET  TIME ZONE 
                    // calA.setTimezone("{{ $userDetail->timezone ?? 'UTC' }}");
                    const formattedEvents = processedEvents.map(event => ({
                        ...event,
                        // CONVERT START AND EN TO USERTIMEZONE
                        start: event.start.toLocaleString(),
                        end: event.end.toLocaleString(),
                        // start: new Date(event.start.toLocaleString('en-US', { timeZone: userTimezone })),
                        // end: new Date(event.end.toLocaleString('en-US', { timeZone: userTimezone })),
                        // const easternTime = date.toLocaleString("en-US", {timeZone: "America/New_York"});
                    }));
                    var groupedEvents = {};

                    events.forEach(function(eventData, index) {
                        var date = new Date(eventData.start.dateTime);
                        var formattedDate = date.toISOString().substring(0,
                            10); // Extract YYYY-MM-DD

                        if (!groupedEvents[formattedDate]) {
                            groupedEvents[formattedDate] = [];
                        }

                        groupedEvents[formattedDate].push({
                            "id": index,
                            "title": eventData.summary || 'Untitled Event',
                            "start_time": eventData.start.dateTime,
                            "end_time": eventData.end.dateTime,
                            "meeting_link": eventData.hangoutLink,
                            // Add other properties as needed
                        });
                    });
                    var calendarStats = $('#calendar-stats');
                    calendarStats.empty();

                    function formatDate(date, format) {
                        return new Intl.DateTimeFormat('en-US', {
                            weekday: format
                        }).format(date);
                    }

                    function isSameDay(date1, date2) {
                        return (
                            date1.getDate() === date2.getDate() &&
                            date1.getMonth() === date2.getMonth() &&
                            date1.getFullYear() === date2.getFullYear()
                        );
                    }

                    function isTomorrow(date1, date2) {
                        var tomorrow = new Date(date2);
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        return isSameDay(date1, tomorrow);
                    }

                    var now = new Date();
                    var today = new Date(now.getFullYear(), now.getMonth(), now
                        .getDate()); // Set time to midnight
                    var endDate = new Date(today);
                    endDate.setDate(endDate.getDate() + 3);

                    $.each(groupedEvents, function(date, events) {
                        var carbonDate = new Date(date);

                        if (carbonDate >= today && carbonDate <= endDate) {
                            var dayLabel = (isSameDay(carbonDate, now)) ?
                                'Today' :
                                (isTomorrow(carbonDate, now)) ?
                                'Tomorrow' :
                                formatDate(carbonDate, 'long');

                            var formattedDate = carbonDate.toLocaleDateString('en-US', {
                                month: 'numeric',
                                day: 'numeric',
                                year: 'numeric'
                            });

                            var eventsHtml = `
                            <div class="Today_todo_list">
                                <div class="todo_list_title">
                                    <h6>${dayLabel.toUpperCase()} <span style="font-weight: 400;">${formattedDate}</span></h6>
                                </div>
                                <div class="All_day_meeting_list mt-3">
                                    <div class="all_day_meeting_list_details">
                                        <ul>
                                            ${events.map(function(event, index) {
                                                var startTime = new Date(event.start_time).toLocaleTimeString('en-US', { timeZone: userTimezone }, { hour: 'numeric', minute: '2-digit' });
                                                var endTime = new Date(event.end_time).toLocaleTimeString('en-US', { timeZone: userTimezone }, { hour: 'numeric', minute: '2-digit' });
                                                return `
                                                    <li>
                                                        <h6 class="allday_meeting_time">${startTime} - ${endTime}</h6>
                                                        <p class="allday_meeting_info mb-0" style="color: white;">${event.title}</p>
                                                    </li>`;
                                            }).join('')}
                                        </ul>
                                    </div>
                                </div>
                            </div>`;


                            calendarStats.append(eventsHtml);
                        }
                    });
                    calendar.clear();
                    calendar.createSchedules(formattedEvents);
                },
                error: function(error) {
                    console.error('Error fetching events:', error);
                }
            });
        }

        // Function to update calendar view based on screen width
        function updateCalendarView() {
            var screenWidth = window.innerWidth;
            if (screenWidth < 767) {
                calendar.changeView('day', true) // Switch to day view
            } else {
                calendar.changeView('week', true) // Switch back to week view or any other view you prefer
            }
        }
        // Initial call to set calendar view based on current screen width
        updateCalendarView();
        // Attach event listener to window resize event
        window.addEventListener('resize', function() {
            updateCalendarView();
            // calendar.changeView('day', true)
        });
    </script>
@endpush
