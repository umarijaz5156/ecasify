<?php $__env->startSection('page-title', __('Dashboard')); ?>

<?php $__env->startSection('content'); ?>
<?php
    use App\Http\Helper\EncryptionHelper;
    $encryptionHelper = new EncryptionHelper();
    $user_id = Auth::user()->id;
    $cases = \App\Models\Cases::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');

    $userDetail = \App\Models\UserDetail::getUserDetail($user_id);
    $timeZone = \App\Models\UserDetail::getTimeZone($userDetail?->timezone ?? 'America/New_York');
?>

<style>
   
   .calendar_wrapper .mini_calendar_wrapper {
            background-color: #373c45;
            grid-column: span 3;
            padding: 1rem;
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

        /* css */
        * {
            padding: 0;
            margin: 0;
        }

        .sideBar {
            display: none;
        }

        @media screen and (min-width: 1024px) {
            .sideBar {
                display: block;
            }
        }

        .welcome_text {
            color: #18181B;
            font-size: 1.2rem;
        }

        .welcome_text span {
            color: #71717A;
        }

        .dashboard_cards {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1rem;
            margin-top: 4rem;
        }

        @media screen and (min-width: 768px) {
            .dashboard_cards {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
                margin-top: 4rem;
            }
        }

        @media screen and (min-width: 1200px) {
            .dashboard_cards {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 1rem;
                margin-top: 4rem;
            }
        }

        .dashboard_main_wrapper {
            padding: 46px 18px;
        }

        .dashboard_cards .cards_box {
            border: 1px solid #5271FF;
            border-radius: 0.5rem;
            padding: 20px 15px 16px 15px;
            display:flex;
            justify-content: space-between;
            gap:0.6rem;
            align-items: start;
            flex-direction: column;
        }
       

        .cards_box .card_title {
            color: rgba(0, 0, 0, 0.50);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .cards_box .card_info {
            color: rgba(0, 0, 0, 0.80);
            font-size: 21px;
            font-weight: 700;
            line-height: 32px;
        }

        .widget_wrapper {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1rem;
        }



        @media screen and (min-width:1200px) {
            .widget_wrapper {
                grid-template-columns: repeat(12, 1fr);
            }

            .case_task_holder {
                grid-column: span 5 / span 4;
            }

            .activity_logged_holder {
                grid-column: span 4 / span 4;
            }

            .calendar_widget_holder {
                grid-column: span 3 / span 4;
            }

        }


         .mini_calendar_wrapper{
            border-radius: 10px;
            border: 0.5px solid #D9D9D9;
        }

        .widget_holder_in {
            width: 100%;
            padding: 28px 20px;
            border-radius: 10px;
            border: 0.5px solid #D9D9D9;
            background: #FFF;
        }

        .widget_title {
            color: rgba(0, 0, 0, 0.80);
            font-size: 16px;
            font-weight: 700;
            line-height: 18px;
            /* 112.5% */
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            overflow: hidden;
        }

        .Priority_table_text {
            color: #818181;
        }

        .widget_holder_in.recent_activity {
            flex:1;
            display: flex;
            justify-content: start;
            align-items: start;
            flex-direction: column;
        }

        .activities_log_status {
            margin-top: 26px;
            width: 100%;
            flex: 1;
            overflow-y: auto;
            /* overflow-y: scroll; */
        }

        .recent_activity_links {
            text-decoration: none;

        }

        .recent_activity_links:hover {
            color: inherit;
            text-decoration: underline;
            padding-left: 2px !important;

        }

        .recent_activity_time {
            color: #4CAF50;
            font-size: 13px;
            font-style: italic;
            font-weight: 700;
            line-height: normal;
            text-wrap: nowrap;

        }
        #chart_types{
            width: max-content !important;
            padding-right: 2rem !important;
        }
        #time_range{
            width: max-content !important;
            padding-right: 2rem !important;
        }
        .sortable-inner-container{
            height: 100%;
            display: flex;
            justify-content: start;
            align-items:start;
            flex-direction: column;
            gap:2rem;
        }

        /* calendar-acalendar-a */
        #calendar-a .color-calendar{
            box-shadow: none;
        }
        #calendar-stats{
            padding:1rem;

        }
        .todo_list_title h6{
            color:#5271FF;
        }
        .color-calendar.basic .calendar__days .calendar__day-selected .calendar__day-box {
            background-color:#5271FF !important;
        }
        .calendar__day-bullet {
                background-color: #5271FF !important;

            }
        .calendar__day.calendar__day-active.calendar__day-event.calendar__day-selected .calendar__day-bullet {
                background-color: white !important;

            }
            .calendar__month{
                color:black;
            }
        .allday_meeting_time{
            color:#818181;
        }

        /* #818181 */
        
  
</style>


<?php if(Auth::user()->type == 'company'): ?>

    <div class="dashboard_main_wrapper" >
        <?php if(Auth::user()->type == 'company'): ?>
        <div class="">
          <span class=" welcome_text">
            <span style="    margin-top: 0;
            margin-bottom: 0.5rem;
            font-weight: 600;
            line-height: 1.2;
            color: #060606;
            font-size: 1.3rem;">Hey <?php echo e(UsersNameById($user_id)); ?>  -</span>
        Here's sneak peak into your Law Firm!
    </span>
          
        </div>

        <?php endif; ?>

        <?php if(Auth::user()->type == 'company'): ?>
        <div class="dashboard_cards" id="sortable-cards">
            <div class="cards_box" data-card-id='attorney_widget'>
              <h6 class="card_title">Total attorneys</h6>
              <h6 class="card_info"><?php echo e(count($attorneys)); ?></h6>
            </div>
            <div class="cards_box" data-card-id='staff_member_widget'>
              <h6 class="card_title">Total staff members</h6>
              <h6 class="card_info"><?php echo e(count($staffMembers)); ?></h6>
            </div>
            <div class="cards_box" data-card-id='client_widget'>
              <h6 class="card_title">Total clients</h6>
              <h6 class="card_info"><?php echo e(count($clients)); ?></h6>
            </div>
            <div class="cards_box" data-card-id='case_widget'>
              <h6 class="card_title">Total new cases</h6>
              <h6 class="card_info"><?php echo e(count($cases)); ?></h6>
            </div>
            <div class="cards_box" data-card-id='message_widget'>
              <h6 class="card_title">nEW MESSAGES</h6>
              <h6 class="card_info">--</h6>
            </div>
          </div>
        <?php else: ?>
        <div class="dashboard_cards" style="grid-template-columns: repeat(3, 1fr);" id="sortable-cards">
            <div class="cards_box" data-card-id='taskhighpriority_widget'>
              <h6 class="card_title">Tasks (high priority)</h6>
              <h6 class="card_info"><?php echo e(count($todos)); ?></h6>
            </div>
            <div class="cards_box" data-card-id='totaltask_widget'>
              <h6 class="card_title">Total Tasks</h6>
              <h6 class="card_info"><?php echo e($totalTodos); ?></h6>
            </div>
            <div class="cards_box" data-card-id='upcomingtask_widget'>
              <h6 class="card_title">Upcoming Tasks</h6>
              <h6 class="card_info"><?php echo e(count($upcoming_todo)); ?></h6>
            </div>
            <div class="cards_box" data-card-id='case_widget'>
              <h6 class="card_title">Total new cases</h6>
              <h6 class="card_info"><?php echo e(count($cases)); ?></h6>
            </div>
            <div class="cards_box" data-card-id='upcomingcase_widget'>
                <h6 class="card_title">Upcoming cases</h6>
                <h6 class="card_info"><?php echo e(count($upcoming_case)); ?></h6>
              </div>
            
            <div class="cards_box" data-card-id='message_widget'>
              <h6 class="card_title">nEW MESSAGES</h6>
              <h6 class="card_info">--</h6>
            </div>
          </div>
        <?php endif; ?>
        
      

        <div class="widget_wrapper" id="sortable-widget-wrapper">

            <div class="case_task_holder col-widget" widget-name = 'case-task-col'>
                <div class="drag-handle" >
                    <b>Total Cases</b>

                </div>
                <div class="sortable-inner-container">

                    <div class="sortable-item widget_holder_in row-widget" widget-name = 'chart-row'>
                        <div class="form-group d-flex justify-content-end align-items-end gap-2 " >
                            
                            <input style="width: fit-content;" type="text" name="date_range_graph" id="date_range_graph" class="form-control " required="" />

                            <select name="chart_types" id="chart_types" class="form-control draft_fields" required="">
                                <option value="line">Line</option>
                                <option value="bar">Bar</option>
                                <option value="pie" selected>Pie</option>
                                <option value="donut">Doughnut</option>
                               
                            </select>
                        
                        
                        </div>
                        <div id="chart"></div>
                    </div>
                    <div style="flex:1" class=" sortable-item high_priorty_widget widget_holder_in row-widget"      widget-name = 'task-row'>
                        <h5 class="widget_title">High Priority Tasks</h5>
                        <table class="table table-borderless ">
                            <thead>
                            <tr>
                                <th scope="col">Case</th>
                                <th scope="col">Status</th>
                                <th scope="col">Priority</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $todos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td > <?php echo e($task->associatedCase->name ?? ''); ?></td>
                                <td><?php echo e($task->status ?? ''); ?>


                                    <?php if($task->status === 'Not Started Yet'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M8.8 12.8C9.22435 12.8 9.63131 12.9686 9.93137 13.2686C10.2314 13.5687 10.4 13.9757 10.4 14.4C10.4 14.8243 10.2314 15.2313 9.93137 15.5314C9.63131 15.8314 9.22435 16 8.8 16C8.37565 16 7.96869 15.8314 7.66863 15.5314C7.36857 15.2313 7.2 14.8243 7.2 14.4C7.2 13.9757 7.36857 13.5687 7.66863 13.2686C7.96869 12.9686 8.37565 12.8 8.8 12.8ZM3.7928 10.4C4.32323 10.4 4.83194 10.6107 5.20701 10.9858C5.58209 11.3609 5.7928 11.8696 5.7928 12.4C5.7928 12.9304 5.58209 13.4391 5.20701 13.8142C4.83194 14.1893 4.32323 14.4 3.7928 14.4C3.26237 14.4 2.75366 14.1893 2.37859 13.8142C2.00351 13.4391 1.7928 12.9304 1.7928 12.4C1.7928 11.8696 2.00351 11.3609 2.37859 10.9858C2.75366 10.6107 3.26237 10.4 3.7928 10.4ZM13.0552 10.8C13.4795 10.8 13.8865 10.9686 14.1866 11.2686C14.4866 11.5687 14.6552 11.9757 14.6552 12.4C14.6552 12.8243 14.4866 13.2313 14.1866 13.5314C13.8865 13.8314 13.4795 14 13.0552 14C12.6309 14 12.2239 13.8314 11.9238 13.5314C11.6238 13.2313 11.4552 12.8243 11.4552 12.4C11.4552 11.9757 11.6238 11.5687 11.9238 11.2686C12.2239 10.9686 12.6309 10.8 13.0552 10.8ZM14.8 7.4552C15.1183 7.4552 15.4235 7.58163 15.6485 7.80667C15.8736 8.03172 16 8.33694 16 8.6552C16 8.97346 15.8736 9.27868 15.6485 9.50373C15.4235 9.72877 15.1183 9.8552 14.8 9.8552C14.4817 9.8552 14.1765 9.72877 13.9515 9.50373C13.7264 9.27868 13.6 8.97346 13.6 8.6552C13.6 8.33694 13.7264 8.03172 13.9515 7.80667C14.1765 7.58163 14.4817 7.4552 14.8 7.4552ZM2 4.8C2.53043 4.8 3.03914 5.01071 3.41421 5.38579C3.78929 5.76086 4 6.26957 4 6.8C4 7.33043 3.78929 7.83914 3.41421 8.21421C3.03914 8.58929 2.53043 8.8 2 8.8C1.46957 8.8 0.960859 8.58929 0.585786 8.21421C0.210714 7.83914 0 7.33043 0 6.8C0 6.26957 0.210714 5.76086 0.585786 5.38579C0.960859 5.01071 1.46957 4.8 2 4.8ZM14.2288 4.1656C14.441 4.1656 14.6445 4.24989 14.7945 4.39991C14.9445 4.54994 15.0288 4.75343 15.0288 4.9656C15.0288 5.17777 14.9445 5.38126 14.7945 5.53129C14.6445 5.68131 14.441 5.7656 14.2288 5.7656C14.0166 5.7656 13.8131 5.68131 13.6631 5.53129C13.5131 5.38126 13.4288 5.17777 13.4288 4.9656C13.4288 4.75343 13.5131 4.54994 13.6631 4.39991C13.8131 4.24989 14.0166 4.1656 14.2288 4.1656ZM6.4 0C7.03652 0 7.64697 0.252856 8.09706 0.702944C8.54714 1.15303 8.8 1.76348 8.8 2.4C8.8 3.03652 8.54714 3.64697 8.09706 4.09706C7.64697 4.54714 7.03652 4.8 6.4 4.8C5.76348 4.8 5.15303 4.54714 4.70294 4.09706C4.25286 3.64697 4 3.03652 4 2.4C4 1.76348 4.25286 1.15303 4.70294 0.702944C5.15303 0.252856 5.76348 0 6.4 0ZM12.4 2.4C12.5061 2.4 12.6078 2.44214 12.6828 2.51716C12.7579 2.59217 12.8 2.69391 12.8 2.8C12.8 2.90609 12.7579 3.00783 12.6828 3.08284C12.6078 3.15786 12.5061 3.2 12.4 3.2C12.2939 3.2 12.1922 3.15786 12.1172 3.08284C12.0421 3.00783 12 2.90609 12 2.8C12 2.69391 12.0421 2.59217 12.1172 2.51716C12.1922 2.44214 12.2939 2.4 12.4 2.4Z"
                                                fill="#27ADCA" />
                                        </svg>
                                    <?php elseif($task->status === 'Incomplete'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12"
                                            viewBox="0 0 8 12" fill="none">
                                            <path
                                                d="M7.60002 1.76001V0.760006L4.00002 0.600006L0.400024 0.760006V1.76001C0.400024 3.36001 1.52002 4.68001 3.00002 5.04001C3.12002 5.08001 3.20002 5.20001 3.20002 5.32001V5.80001C3.20002 5.92001 3.12002 6.04001 3.00002 6.04001C1.52002 6.40001 0.400024 7.72001 0.400024 9.32001V10.4L4.00002 10.8L7.60002 10.4V9.32001C7.60002 7.72001 6.48002 6.40001 5.00002 6.04001C4.88002 6.00001 4.80002 5.92001 4.80002 5.80001V5.32001C4.80002 5.20001 4.88002 5.08001 5.00002 5.08001C6.48002 4.68001 7.60002 3.36001 7.60002 1.76001Z"
                                                fill="#83CBFF" />
                                            <path
                                                d="M0.4 0.8H7.6C7.84 0.8 8 0.64 8 0.4C8 0.16 7.84 0 7.6 0H0.4C0.16 0 0 0.16 0 0.4C0 0.64 0.16 0.8 0.4 0.8ZM0.4 11.2H7.6C7.84 11.2 8 11.04 8 10.8C8 10.56 7.84 10.4 7.6 10.4H0.4C0.16 10.4 0 10.56 0 10.8C0 11.04 0.16 11.2 0.4 11.2Z"
                                                fill="#9B9B9B" />
                                            <path
                                                d="M4.40005 6.95999V5.23999C4.40005 4.95999 4.60005 4.75999 4.84005 4.67999C5.52005 4.51999 6.12005 4.11999 6.56005 3.59999C6.84005 3.27999 6.60005 2.79999 6.20005 2.79999H1.80005C1.40005 2.79999 1.16005 3.27999 1.44005 3.59999C1.88005 4.11999 2.48005 4.47999 3.16005 4.67999C3.44005 4.75999 3.60005 4.95999 3.60005 5.23999V6.79999C3.60005 7.07999 3.48005 7.19999 3.36005 7.23999C1.88005 7.51999 0.800049 8.63999 0.800049 9.95999V10.4H7.20005V9.95999C7.20005 8.63999 6.12005 7.51999 4.64005 7.23999C4.52005 7.23999 4.40005 7.11999 4.40005 6.95999Z"
                                                fill="#FFB02E" />
                                            <path
                                                d="M6.19995 1.52C6.19995 1.32 6.35995 1.16 6.55995 1.16C6.79995 1.2 6.95995 1.36 6.91995 1.52C6.87995 2.16 6.71995 2.72 6.39995 3.2C6.07995 3.76 5.59995 4.16 5.03995 4.36C4.83995 4.44 4.63995 4.36 4.55995 4.16C4.47995 3.96 4.55995 3.76 4.75995 3.68C5.71995 3.32 6.11995 2.28 6.19995 1.52ZM6.19995 9.56C6.19995 9.76 6.35995 9.92 6.55995 9.92C6.79995 9.92 6.95995 9.72 6.91995 9.56C6.87995 8.92 6.71995 8.36 6.39995 7.88C6.07995 7.32 5.59995 6.92 5.03995 6.72C4.83995 6.64 4.63995 6.72 4.55995 6.92C4.47995 7.12 4.55995 7.32 4.75995 7.4C5.71995 7.76 6.11995 8.8 6.19995 9.56Z"
                                                fill="white" />
                                        </svg>
                                    <?php elseif($task->status === 'In Progress'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M5.22449 11.6735L0.326531 6.77551C-0.108844 6.34014 -0.108844 5.65986 0.326531 5.22449L5.22449 0.326531C5.65986 -0.108844 6.34014 -0.108844 6.77551 0.326531L11.6735 5.22449C12.1088 5.65986 12.1088 6.34014 11.6735 6.77551L6.77551 11.6735C6.34014 12.1088 5.63265 12.1088 5.22449 11.6735Z"
                                                fill="#4CAF50" />
                                            <path d="M6.03657 8.59999L4.14771 6.33334H7.92544L6.03657 8.59999Z"
                                                fill="#FFEB3B" />
                                            <path d="M5.49695 3.58099H6.5763V6.90001H5.49695V3.58099Z" fill="#FFEB3B" />
                                        </svg>
                                    <?php elseif($task->status === 'Completed'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                            viewBox="0 0 10 10" fill="none">
                                            <g clip-path="url(#clip0_1_292)">
                                                <path
                                                    d="M5 0C5.45898 0 5.90169 0.0585938 6.32812 0.175781C6.75456 0.292969 7.15169 0.46224 7.51953 0.683594C7.88737 0.904948 8.22428 1.16536 8.53027 1.46484C8.83626 1.76432 9.09831 2.10124 9.31641 2.47559C9.5345 2.84993 9.70215 3.2487 9.81934 3.67188C9.93652 4.09505 9.99675 4.53776 10 5C10 5.45898 9.94141 5.90169 9.82422 6.32812C9.70703 6.75456 9.53776 7.15169 9.31641 7.51953C9.09505 7.88737 8.83464 8.22428 8.53516 8.53027C8.23568 8.83626 7.89876 9.09831 7.52441 9.31641C7.15006 9.5345 6.7513 9.70215 6.32812 9.81934C5.90495 9.93652 5.46224 9.99675 5 10C4.54102 10 4.09831 9.94141 3.67188 9.82422C3.24544 9.70703 2.84831 9.53776 2.48047 9.31641C2.11263 9.09505 1.77572 8.83464 1.46973 8.53516C1.16374 8.23568 0.901693 7.89876 0.683594 7.52441C0.465495 7.15006 0.297852 6.7513 0.180664 6.32812C0.0634766 5.90495 0.00325521 5.46224 0 5C0 4.54102 0.0585938 4.09831 0.175781 3.67188C0.292969 3.24544 0.46224 2.84831 0.683594 2.48047C0.904948 2.11263 1.16536 1.77572 1.46484 1.46973C1.76432 1.16374 2.10124 0.901693 2.47559 0.683594C2.84993 0.465495 3.2487 0.297852 3.67188 0.180664C4.09505 0.0634766 4.53776 0.00325521 5 0ZM7.94434 3.34473L7.28027 2.68066L4.0625 5.89844L2.71973 4.55566L2.05566 5.21973L4.0625 7.22656L7.94434 3.34473Z"
                                                    fill="#5271FF" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_1_292">
                                                    <rect width="10" height="10" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($task->priority ?? ''); ?>

                                    <?php if($task->priority === 'High'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M5.22449 11.6735L0.326531 6.77551C-0.108844 6.34014 -0.108844 5.65986 0.326531 5.22449L5.22449 0.326531C5.65986 -0.108844 6.34014 -0.108844 6.77551 0.326531L11.6735 5.22449C12.1088 5.65986 12.1088 6.34014 11.6735 6.77551L6.77551 11.6735C6.34014 12.1088 5.63265 12.1088 5.22449 11.6735Z"
                                                fill="#F44336" />
                                            <path
                                                d="M5.3335 8.38096C5.3335 8.29933 5.36071 8.2177 5.38792 8.13606C5.41513 8.05443 5.46955 8.00001 5.52397 7.94559C5.57839 7.89116 5.66003 7.83674 5.74166 7.80953C5.82329 7.78232 5.90492 7.75511 6.01377 7.75511C6.12261 7.75511 6.20424 7.78232 6.28588 7.80953C6.36751 7.83674 6.44914 7.89116 6.50356 7.94559C6.55799 8.00001 6.61241 8.05443 6.63962 8.13606C6.66683 8.2177 6.69404 8.29933 6.69404 8.38096C6.69404 8.46259 6.66683 8.54423 6.63962 8.62586C6.61241 8.70749 6.55799 8.76191 6.50356 8.81633C6.44914 8.87076 6.36751 8.92518 6.28588 8.95239C6.20424 8.9796 6.12261 9.00681 6.01377 9.00681C5.90492 9.00681 5.82329 8.9796 5.74166 8.95239C5.66003 8.92518 5.6056 8.87076 5.52397 8.81633C5.46955 8.76191 5.41513 8.70749 5.38792 8.62586C5.36071 8.54423 5.3335 8.4898 5.3335 8.38096ZM6.47635 7.12926H5.49676L5.36071 3.02042H6.61241L6.47635 7.12926Z"
                                                fill="white" />
                                        </svg>
                                        <?php endif; ?>
                                    </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3">No Data Found</td>
                            </tr>

                                
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="activity_logged_holder col-widget" widget-name = 'activity-logged-col'>
                <div class="drag-handle" >
                    <b>Most logged in time</b>
                </div>
                <div class="sortable-inner-container" >

                  <div class="sortable-item widget_holder_in row-widget" widget-name = 'logged-row' >
                    <div>
                        <div class="form-group d-flex justify-content-between align-items-center gap-2 " >
                            <h6 class="Priority_table_text text-uppercase">
                                most logged time
                                </h6>                           
                                <input style="width: fit-content;" type="text" name="date_range" id="date_range" class="form-control draft_fields" required="" />

                        
                        </div>
                        <div class="mt-5">
                            <div class="d-flex justify-content-start align-items-center">
                                <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="19"
                                height="15"
                                viewBox="0 0 19 15"
                                fill="none"
                                >
                                <path
                                    d="M18.02 3.92127C18.02 3.4318 17.6207 3.0325 17.1312 3.0325C16.6418 3.0325 16.2425 3.4318 16.2425 3.92127C16.2425 4.1016 16.3004 4.27549 16.3906 4.41073C16.2489 4.41073 16.1072 4.44293 15.972 4.5331L13.4409 6.16894C13.106 6.38147 12.6681 6.30418 12.4298 5.98217L9.61539 2.16306C9.59533 2.13233 9.57153 2.10421 9.54455 2.07934C9.75998 1.96073 9.92985 1.77385 10.0274 1.54811C10.125 1.32237 10.1447 1.07059 10.0835 0.832415C10.0222 0.594236 9.88354 0.383185 9.6892 0.232486C9.49486 0.0817874 9.25592 0 9.01 0C8.76408 0 8.52514 0.0817874 8.3308 0.232486C8.13646 0.383185 7.99776 0.594236 7.93653 0.832415C7.8753 1.07059 7.89502 1.32237 7.99258 1.54811C8.09015 1.77385 8.26002 1.96073 8.47545 2.07934C8.44969 2.1051 8.42393 2.13086 8.40461 2.16306L5.59019 5.98217C5.35834 6.30418 4.91396 6.38147 4.57906 6.16894L2.04802 4.5331C1.91277 4.44293 1.77109 4.41073 1.6294 4.41073C1.726 4.26904 1.77753 4.1016 1.77753 3.92127C1.77753 3.4318 1.37823 3.0325 0.888763 3.0325C0.3993 3.0325 0 3.4318 0 3.92127C0 4.41073 0.3993 4.81003 0.888763 4.81003C0.920965 4.81003 0.953167 4.81003 0.978928 4.80359C0.895204 4.95172 0.863002 5.12561 0.901644 5.31238L2.57613 13.6655C2.69849 14.2644 3.2266 14.6959 3.83843 14.6959H14.1816C14.7934 14.6959 15.3215 14.2644 15.4439 13.6655L17.1184 5.31238C17.157 5.12561 17.1184 4.94528 17.0411 4.80359C17.0733 4.80359 17.1055 4.81003 17.1312 4.81003C17.6207 4.81003 18.02 4.41073 18.02 3.92127Z"
                                    fill="#FFB02E"
                                />
                                </svg>
                                <p class="mb-0" id="mostTimeLoggedText">
                                Most Time Logged <b><?php echo e($maxLoggedTime); ?> </b> by <b> <?php echo e($maxLoggedMember); ?></b>
                                </p>
                            </div>
                        </div>
                    </div>
                  </div>

                  <div class="sortable-item widget_holder_in recent_activity row-widget" widget-name = 'activity-row' >
                    <h5 class="widget_title">Recent Activity</h5>
                      <div class="activities_log_status">
                          <ul class="px-0" style="list-style: none;">
                            <?php $__currentLoopData = $allActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allActivity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $userName = UsersNameById($allActivity->user_id);
                                $activityDescription = '';
                                $activitytype = '';
                                $activitytag = '';

                                switch ($allActivity->target_type) {
                                    case 'Case':
                                        $caseName = \App\Models\Cases::where('id', $allActivity->target_id)->pluck('name')->first();
                                        $activitytype = "Case";
                                        $activitytag = " #{{$caseName}}";
                                        if (isset($allActivity->file)) {
                                            $activitytype = "uploaded Property Papers and related images in Case ";
                                            $activitytag = " #{{$allActivity->target_id}}";
                                        }
                                        break;
                                    case 'Task':
                                        $TaskName = \App\Models\Task::where('id', $allActivity->target_id)->pluck('title')->first();
                                        $activitytype = "Task";
                                        $activitytag = " #{{$TaskName}}";

                                        break;
                                    case 'User':
                                    $userName = \App\Models\User::where('id', $allActivity->target_id)->pluck('name')->first();
                                        $activitytype = "User";
                                        $activitytag = " #{{$userName}}";

                                        break;
                                    case 'Timer':
                                        $activitytype = "Timer";
                                        break;
                                }
                        
                                switch ($allActivity->action) {
                                    case 'Created':
                                        $activityDescription = "added a new " . $activityDescription;
                                        break;
                                    case 'Updated':
                                        $activityDescription = "updated " . $activityDescription;
                                        break;
                                    case 'Deleted':
                                        $activityDescription = "deleted " . $activityDescription;
                                        break;
                                    case 'Started':
                                        $activityDescription = "started " . $activityDescription;
                                        break;
                                    case 'Saved to draft':
                                        $activityDescription = "saved to draft " . $activityDescription;
                                        break;
                                        
                                        case 'Saved':
                                        $activityDescription = "Saved " . $activityDescription;
                                        break;
                                    case 'Paused':
                                        $activityDescription = "paused " . $activityDescription;
                                        break;
                                    case 'Resumed':
                                        $activityDescription = "resumed " . $activityDescription;
                                        break;
                                    case 'Status Changed':
                                        $activityDescription = "changed the status " . $activityDescription;
                                        break;
                                    case 'Viewed':
                                        $activityDescription = "viewed " . $activityDescription;
                                        break;
                                }
                            ?>
                        
                            <li class="mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <?php switch($allActivity->target_type):
                                        case ('Case'): ?>
                                                <span class="line-clamp-1"><?php echo e($userName); ?>  <?php echo e($activityDescription); ?> 
                                                    <a style="text-align: left;padding:0px;color:#116bf1" class="Priority_table_text text-left recent_activity_links" href="<?php echo e(isset($allActivity->file) ? route('cases.show', $allActivity->target_id . '?tab=document') : route('cases.show', $allActivity->target_id . '?tab=timeline')); ?>" class="btn btn-sm d-inline-flex align-items-center" title="" data-bs-toggle="tooltip" data-bs-placement="top">
                                                        <?php echo e($activitytype); ?>

                                              
                                                    </a>  
                                                    <a style="text-align: left;padding:0px;color:#b443ff" href="<?php echo e(route('activities.index')); ?>" class="btn btn-sm d-inline-flex recent_activity_links align-items-center">
                                                        <?php echo e($activitytag); ?>    
                                                    </a>    
                                                </span> 
                                            
                                            <?php break; ?>
                                        <?php case ('Task'): ?>
                                                <span class="line-clamp-1"><?php echo e($userName); ?> <?php echo e($activityDescription); ?> 
                                                    <a style="text-align: left;padding:0px;color:#116bf1" href="#" class="Priority_table_text text-left recent_activity_links btn btn-sm d-inline-flex align-items-center"  data-url="<?php echo e(route('to-do.show', $allActivity->target_id)); ?>" data-size="xl addTaskModal_wrapper" data-ajax-popup="true" data-title="<?php echo e($activityDescription); ?>" title="<?php echo e($activityDescription); ?>" data-bs-toggle="tooltip" data-bs-placement="top" >
                                                        <?php echo e($activitytype); ?>

                                                    </a>
                                                    <a style="text-align: left;padding:0px;color:#b443ff" href="<?php echo e(route('activities.index')); ?>" class="btn btn-sm d-inline-flex recent_activity_links align-items-center">
                                                        <?php echo e($activitytag); ?>    
                                                    </a>
                                                    </span>
                                           
                                            <?php break; ?>
                                        <?php case ('User'): ?>
                                                <span class="line-clamp-1"><?php echo e($userName); ?> <?php echo e($activityDescription); ?> 
                                                    <a style="text-align: left;padding:0px;color:#116bf1" href="<?php echo e(route('users.edit', $allActivity->target_id)); ?>" class="Priority_table_text recent_activity_links btn btn-sm d-inline-flex align-items-center" title="" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    
                                                    <?php echo e($activitytype); ?>

                                                </a>
                                                    <a style="text-align: left;padding:0px;color:#b443ff" href="<?php echo e(route('activities.index')); ?>" class="btn btn-sm d-inline-flex recent_activity_links align-items-center">
                                                        <?php echo e($activitytag); ?>    
                                                    </a>
                                                </span>
                                            
                                            <?php break; ?>
                                        <?php case ('Timer'): ?>
                                            <?php if(isset($allActivity->target_id)): ?>
                                                    <span class="line-clamp-1"><?php echo e($userName); ?> <?php echo e($activityDescription); ?> 
                                                        <a style="text-align: left;padding:0px;color:#116bf1" href="#" class="btn btn-sm d-inline-flex recent_activity_links align-items-center"  data-url="<?php echo e(route('timesheet.show', $allActivity->target_id)); ?>" data-size="xl addTaskModal_wrapper" data-ajax-popup="true" data-title="<?php echo e(__(' View Timesheet')); ?>" title="<?php echo e(__('View Timesheet')); ?>" data-bs-toggle="tooltip" data-bs-placement="top" >
                                                        <?php echo e($activitytype); ?>

                                                    </a>
                                                   
                                                    <a style="text-align: left;padding:0px;color:#b443ff" href="<?php echo e(route('activities.index')); ?>" class="btn btn-sm d-inline-flex recent_activity_links align-items-center">
                                                        <?php echo e($activitytag); ?>    
                                                    </a>

                                                </span>
                                                
                                                <?php else: ?>
                                                
                                                <div  class="Priority_table_text recent_activity_links">
                                                    <span class="line-clamp-1" style="text-align: left;padding:0px"><?php echo e($userName); ?> <?php echo e($activityDescription); ?> <?php echo e($activitytype); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php break; ?>
                                        <?php default: ?>
                                    <?php endswitch; ?>
                        
                                    <p class="recent_activity_time mb-0"><?php echo e(timeAgo($allActivity->created_at)); ?></p>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                          </ul>
                      </div>
                  
                  </div>

                </div>
            </div>


            <div class="calendar_widget_holder col-widget" widget-name = 'calendar-col'>
                    <div class="drag-handle h-100" >
                        <b>Calendar</b>

                        <div class="mini_calendar_wrapper h-100">
                            
                            <div  class="text-end p-2" id="calender_id">
                                
                            </div>
                           
                        
                            <div id="calendar-a" class="d-flex justify-content-center"></div>
                            <div id="calendar-stats">
                            </div>
                        </div>
                    </div>
                </div>
        </div>


       
    </div>
        
    <?php else: ?>

    <div class="col-12">
        <div class="row overflow-hidden g-0 pt-0 g-0 pt-0">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage case')): ?>
                <div class="col border-end border-bottom">
                    <div class="p-3">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="theme-avtar bg-primary">
                                <i class="ti ti-home"></i>
                            </div>
                            <div>
                                <p class="text-muted text-sm mb-0"><?php echo e(__('Total')); ?></p>
                                <?php if('manage case'): ?>
                                    <h6 class="mb-0"><a href="<?php echo e(route('cases.index')); ?>">
                                            <span class="dash-mtext"><?php echo e(__('Cases')); ?></span>
                                        </a></h6>
                                <?php else: ?>
                                    <h6 class="mb-0"><?php echo e(__('Cases')); ?></h6>
                                <?php endif; ?>

                            </div>
                        </div>
                        <h3 class="mb-0"><?php echo e(count($cases)); ?> </h3>
                    </div>
                </div>
            <?php endif; ?>


            <?php if(Auth::user()->type == 'company' || Auth::user()->type == 'co admin'): ?>
                <div class="col border-end border-bottom">
                    <div class="p-3">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="theme-avtar bg-info">
                                <i class="ti ti-click"></i>
                            </div>
                            <div>
                                <p class="text-muted text-sm mb-0">
                                    <?php echo e(__('Total')); ?>

                                </p>
                                <h6 class="mb-0"><a href="<?php echo e(route('advocate.index')); ?>">
                                        <span class="dash-mtext"><?php echo e(__('Attorneys')); ?></span>
                                    </a></h6>
                            </div>
                        </div>
                        <h3 class="mb-0"> <?php echo e(count($advocate)); ?> </h3>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if(Auth::user()->type == 'company' || Auth::user()->type == 'co admin'): ?>
                <div class="col border-end border-bottom">
                    <div class="p-3">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="theme-avtar bg-secondary">
                                <i class="ti ti-users"></i>
                            </div>
                            <div>
                                <p class="text-muted text-sm mb-0"><?php echo e(__('Total')); ?></p>
                                <h6 class="mb-0"><a href="<?php echo e(route('users.index')); ?>" >
                                        <span class="dash-mtext"><?php echo e(__('Team Members')); ?></span>
                                    </a></h6>
                            </div>
                        </div>
                        <h3 class="mb-0"><?php echo e(count($members)); ?></h3>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage tasks')): ?>
                <div class="col border-end border-bottom">
                    <div class="p-3">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="theme-avtar bg-danger">
                                <i class="ti ti-thumb-up"></i>
                            </div>
                            <div>
                                <p class="text-muted text-sm mb-0"><?php echo e(__('Total')); ?></p>
                                <h6 class="mb-0"><a href="<?php echo e(route('tasks.index')); ?>" >
                                        <span class="dash-mtext"><?php echo e(__('Tasks')); ?></span>
                                    </a></h6>
                            </div>
                        </div>
                        <h3 class="mb-0"><?php echo e(count($todos)); ?> </h3>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-xxl-12">
            <div class="row g-0">
                <!-- [ sample-page ] start -->
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage case')): ?>
                    <div class="col-md-6 col-lg-3 border-end border-bottom scrollbar">
                        <div class="card shadow-none bg-transparent force-overflow">
                            <div class="card-header card-header border-bottom-0">
                                <h5><?php echo e(__('Recent Cases')); ?></h5>

                            </div>
                            <div class="card-body p-0">
                                <div class="scroll-add">
                                    <ul class="list-group list-group-flush" id="todayhear">

                                        <?php if(count($cases) > 0): ?>
                                            <?php $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $upcoming): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="list-group-item">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <a href="<?php echo e(route('cases.show', $upcoming->id)); ?>">
                                                                <?php echo e(!empty($upcoming->name) ? $encryptionHelper->decryptAES($upcoming->name) : ' '); ?>

                                                            </a>

                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            <?php echo e($upcoming->open_date); ?>

                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <li class="list-group-item">
                                                <?php echo e(__('No record found')); ?>

                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage tasks')): ?>
                    <div class="col-md-6 col-lg-3 border-end border-bottom scrollbar">
                        <div class="card shadow-none bg-transparent force-overflow">
                            <div class="card-header card-header border-bottom-0">
                                <h5><?php echo e(__('Recent Tasks')); ?></h5>

                            </div>
                            <div class="card-body p-0">
                                <div class="scroll-add">
                                    <ul class="list-group list-group-flush" id="todaytodo">


                                        <?php if(count($todos) > 0): ?>
                                            <?php $__currentLoopData = $todos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upcoming): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="list-group-item">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <a
                                                                href="<?php echo e(route('tasks.index', ['task_id' => $upcoming->id])); ?>">
                                                                <?php echo e(!empty($upcoming['title']) ? $upcoming['title'] : ' '); ?>

                                                            </a>

                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">

                                                            <?php echo e($upcoming['date']); ?>

                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <li class="list-group-item">
                                                <?php echo e(__('No record found')); ?>

                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage case')): ?>
                    <div class="col-md-6 col-lg-3 border-end border-bottom scrollbar">
                        <div class="card shadow-none bg-transparent force-overflow">
                            <div class="card-header card-header border-bottom-0">
                                <h5><?php echo e(__('Upcoming Cases')); ?></h5>

                            </div>
                            <div class="card-body p-0">
                                <div class="scroll-add">
                                    <ul class="list-group list-group-flush" id="cominghere">
                                        <?php if(count($upcoming_case) > 0): ?>
                                            <?php $__currentLoopData = $upcoming_case; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upcoming): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="list-group-item">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <a href="<?php echo e(route('cases.show', $upcoming->id)); ?>">
                                                                <?php echo e(!empty($upcoming->name) ? $encryptionHelper->decryptAES($upcoming->name) : ' '); ?>

                                                            </a>

                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            <?php echo e($upcoming->open_date); ?>

                                                        </div>

                                                    </div>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <li class="list-group-item">
                                                <?php echo e(__('No record found')); ?>

                                            </li>
                                        <?php endif; ?>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage tasks')): ?>
                    <div class="col-md-6 col-lg-3 border-end border-bottom scrollbar">
                        <div class="card shadow-none bg-transparent force-overflow">
                            <div class="card-header card-header border-bottom-0">
                                <h5><?php echo e(__('Upcoming Tasks')); ?></h5>

                            </div>
                            <div class="card-body p-0">
                                <div class="scroll-add">
                                    <ul class="list-group list-group-flush "id="comingtodo">
                                        <?php if(count($upcoming_todo) > 0): ?>
                                            <?php $__currentLoopData = $upcoming_todo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upcoming): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="list-group-item">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <a
                                                                href="<?php echo e(route('tasks.index', ['task_id' => $upcoming->id])); ?>">
                                                                <?php echo e(!empty($upcoming['title']) ? $upcoming['title'] : ' '); ?>

                                                            </a>
                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">

                                                            <?php echo e($upcoming['date']); ?>

                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <li class="list-group-item">
                                                <?php echo e(__('No record found')); ?>

                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- [ sample-page ] end -->
            </div>
        </div>

    </div>
    <?php endif; ?>


    
    <div
                class="modal fade"
                id="calendarmodelA"
                tabindex="-1"
                aria-labelledby="calendarmodelALabel"
                aria-hidden="true"
            >
        <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header daterange_model_header">
            <h1 class="modal-title fs-5" id="calendarmodelALabel">Add Details here</h1>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
            </div>
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <?php echo Form::label('case', __('Select Case'), ['class' => 'form-label']); ?>

                <?php echo Form::select('case',$cases, null, ['class' => 'form-control' ]); ?>

                </div>
            <div class="form-floating mb-3">
                <input
                type="email"
                class="form-control"
                id="floatingInputSubject"
                placeholder="name@example.com"
                />
                <label for="floatingInput">Subject</label>
            </div>
            <div class="form-floating mb-3">
                <input
                type="text"
                class="form-control"
                id="floatingLocation"
                placeholder="location"
                />
                <label for="floatingLocation">Location</label>
            </div>
            <div class="form-floating mb-3">
                <input
                type="text"
                class="form-control"
                name="daterange"
                id="floatingInputDateRange"
                placeholder="name@example.com"
                />
                <label for="floatingInput">Date Range & Time</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckAllDay">
                <label class="form-check-label" for="flexCheckDefault">
                All Day
                </label>
            </div>
            </div>
            <div class="modal-footer daterange_model_footer">
            <button type="button" id="saveButtonCalendera" class="btn btn-primary">Save</button>
            </div>
        </div>
        </div>
    </div>  
<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom-script'); ?>


<script>

// cards js
    document.addEventListener('DOMContentLoaded', function () {
        const sortableCards = document.getElementById('sortable-cards');
        const cards = sortableCards.getElementsByClassName('cards_box');

        // Initialize Sortable
        const sortable = new Sortable(sortableCards, {
            animation: 500,
            // Element dragging ended
            onEnd: function (evt) {
            if (evt.newIndex !== evt.oldIndex) {
                // Determine the direction of the card movement
                const direction = evt.newIndex > evt.oldIndex ? 1 : -1;

                // Swap the positions of the cards visually
                for (let i = evt.oldIndex; i !== evt.newIndex; i += direction) {
                cards[i].style.order = i + direction;
                }
                cards[evt.newIndex].style.order = evt.newIndex;
            }
                // Handle card movement on the client side
                const card = cards[evt.oldIndex];
                // sortableCards.removeChild(card);
                // sortableCards.insertBefore(card, cards[evt.newIndex]);

                // Send an AJAX request to update widget positions in the database
                const positionsToSave = Array.from(cards).map((card, index) => ({
                    widget_name: card.getAttribute('data-card-id'),
                    position: index,
                    dashboard_type: 'company',
                }));
            

                // Send an AJAX request to update widget positions in the database
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/update-widget-position', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(positionsToSave),
                })
                .then((response) => {
                console.log(response);
                    if (response.ok) {
                        console.log('Widget positions updated successfully.');
                    } else {
                        console.error('Failed to update widget positions.');
                    }
                })
                .catch((error) => {
                    console.error('An error occurred while updating widget positions:', error);
                });
            },
        });

        // Initialize the initial order for the cards based on the user's saved positions
        var userPositions = <?php echo json_encode($widgetsPositions); ?>; // Retrieve user's card positions
        Array.from(cards).forEach((card, index) => {
            var widgetName = card.getAttribute('data-card-id');
            if (userPositions[widgetName]) {
                card.style.order = userPositions[widgetName];
            }
        });
    });

// div js 
document.addEventListener('DOMContentLoaded', function() {
    sortAndRearrangeDivs();
    const widgetWrapper = document.getElementById('sortable-widget-wrapper');
    const widgets = widgetWrapper.getElementsByClassName('col-widget'); // Update your selector as needed
    const rowWidgets = widgetWrapper.getElementsByClassName('row-widget'); // Update your selector as needed

    const sortable = new Sortable(widgetWrapper, {
        handle: '.drag-handle',
        swap: true,
        swapThreshold: 1,
        animation: 500,
        onEnd: function (evt) {
            if (evt.newIndex !== evt.oldIndex) {
                const direction = evt.newIndex > evt.oldIndex ? 1 : -1;

                for (let i = evt.oldIndex; i !== evt.newIndex; i += direction) {
                    widgets[i].style.order = i + direction;
                }
                widgets[evt.newIndex].style.order = evt.newIndex;

                const positionsToSave = Array.from(widgets).map((widget, index) => ({
                    widget_name: widget.getAttribute('widget-name'),
                    position: index,
                    dashboard_type: 'company',
                }));
                saveWidgetPositions(positionsToSave);
            }
        }
    });

    const innerContainers = widgetWrapper.querySelectorAll('.sortable-inner-container');
    innerContainers.forEach((innerContainer) => {
        new Sortable(innerContainer, {
            swap: true,
            swapThreshold: 1,
            animation: 500,
            group: 'nested',
            onEnd: function (evt) {
                if (evt.newIndex !== evt.oldIndex || evt.to !== evt.from) {
                    const direction = evt.newIndex > evt.oldIndex ? 1 : -1;

                    const sourceWidgets = evt.from.querySelectorAll('.row-widget');
                    const targetWidgets = evt.to.querySelectorAll('.row-widget');

                    // Check if the source and target containers have an imbalance in the number of children
                    if (sourceWidgets.length > 2 || targetWidgets.length > 2) {
                        const sourceCount = sourceWidgets.length;
                        const targetCount = targetWidgets.length;

                        if (sourceCount > targetCount) {
                            // Swap from source to target
                            const item = sourceWidgets[sourceCount - 1];
                            const originalParent = item.parentNode;
                            originalParent.removeChild(item);
                            evt.to.appendChild(item);
                        } else {
                            // Swap from target to source
                            const item = targetWidgets[targetCount - 1];
                            const originalParent = item.parentNode;
                            originalParent.removeChild(item);
                            evt.from.appendChild(item);
                        }
                    }

                    // Save positions after the swap
                    const updatedRowWidgets = widgetWrapper.getElementsByClassName('row-widget');
                    const positionsToSave = Array.from(updatedRowWidgets).map((widget, index) => ({
                        widget_name: widget.getAttribute('widget-name'),
                        position: index,
                        dashboard_type: 'company',
                    }));
                    saveWidgetPositions(positionsToSave);
                }
            }
        });
    });
});

function saveWidgetPositions(positionsToSave) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/update-widget-position', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(positionsToSave),
    })
    .then((response) => {
        if (response.ok) {
            console.log('Widget positions updated successfully.');
        } else {
            console.error('Failed to update widget positions.');
        }
    })
    .catch((error) => {
        console.error('An error occurred while updating widget positions:', error);
    });
}

const widgetPositions = <?php echo json_encode($widgetsPositions, 15, 512) ?>;

function sortAndRearrangeDivs() {
    const widgetWrappers = document.querySelectorAll('.sortable-inner-container');

    widgetWrappers.forEach(widgetWrapper => {
        const sortableItems = Array.from(widgetWrapper.querySelectorAll('.sortable-item'));

        // Sort the sortable items based on their positions fetched from the database
        sortableItems.sort((a, b) => {
            const aPosition = widgetPositions[a.getAttribute('widget-name')];
            const bPosition = widgetPositions[b.getAttribute('widget-name')];
            return aPosition - bPosition;
        });

        // Rearrange the sorted sortable items within the container
        sortableItems.forEach(item => widgetWrapper.appendChild(item));
    });
}




 


</script>




    <script>
        var px = new SimpleBar(document.querySelector("#todaytodo"), {
            autoHide: true
        });
        var px = new SimpleBar(document.querySelector("#cominghere"), {
            autoHide: true
        });
        var px = new SimpleBar(document.querySelector("#comingtodo"), {
            autoHide: true
        });
        var px = new SimpleBar(document.querySelector("#todayhear"), {
            autoHide: true
        });
    </script>

    <script>
        (function() {
            var options = {
                series: [<?php echo e($storage_limit); ?>],
                chart: {
                    height: 350,
                    type: 'radialBar',
                    offsetY: -20,
                    sparkline: {
                        enabled: true
                    }
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        track: {
                            background: "#e7e7e7",
                            strokeWidth: '97%',
                            margin: 5, // margin is in pixels
                        },
                        dataLabels: {
                            name: {
                                show: true
                            },
                            value: {
                                offsetY: -50,
                                fontSize: '20px'
                            }
                        }
                    }
                },
                grid: {
                    padding: {
                        top: -10
                    }
                },
                colors: ["#5271FF"],
                labels: ['Used'],
            };
            var chart = new ApexCharts(document.querySelector("#device-chart"), options);
            chart.render();
        })();



    </script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Add daterangepicker library -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.js"></script>
        
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/color-calendar/dist/css/theme-basic.css" />
    
    <!-- /* Font Awsome Cdn */ -->
    <link href="https://cdn.jsdelivr.net/gh/duyplus/fontawesome-pro/css/all.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/color-calendar/dist/bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>


<script>

    $(document).ready(function () {
            var dateRangePicker = $('#date_range');

                // Initialize the date range picker
                dateRangePicker.daterangepicker({
                opens: 'left', // or 'right'
                startDate: moment(), // initial start date (today)
                endDate: moment(), // initial end date (today)
                ranges: {
                    'Today': [moment(), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, function (start, end, label) {
                // On date range change, update the data
                updateMostTimeLoggedData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            });

            function updateMostTimeLoggedData(startDate, endDate) {
                var url = "<?php echo e(route('getTimesheetData', ':startDate/:endDate')); ?>";
                url = url.replace(':startDate', startDate);
                url = url.replace(':endDate', endDate);

                // Make an AJAX call to retrieve updated data based on the selected date range
                $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    // Update the mostTimeLoggedText with the latest data
                    $('#mostTimeLoggedText').html('Most Time Logged <b>' + data.maxLoggedTime + '</b> by <b>' + data.maxLoggedMember + '</b>');

                    // Example: Log the data to the console
                    console.log(data);
                },
                error: function (error) {
                    console.error('Error:', error);
                }
                });
            }

            // Initial div rendering
            updateMostTimeLoggedData();
            // Event listener for date range change
            dateRangePicker.on('apply.daterangepicker', function (ev, picker) {
                updateMostTimeLoggedData(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
            });

    });



    document.addEventListener("DOMContentLoaded", function () {


    var chartData = <?php echo json_encode($chartData, 15, 512) ?>;
    var piechartData = <?php echo json_encode($piechartData, 15, 512) ?>;
    var lineChartData = <?php echo json_encode($lineChartData, 15, 512) ?>;
    var dateRangePickerGraph = $('#date_range_graph');
    var chartTypesSelect = document.getElementById('chart_types');
    var myChart = null;
    
                        // Function to fetch updated chart data
                        function updateChartData(startDate, endDate) {
                           
                            var url = "<?php echo e(route('update.graph', ':startDate/:endDate')); ?>";
                            url = url.replace(':startDate', startDate);
                             url = url.replace(':endDate', endDate);
                             $.ajax({
                                url: url,
                                type: 'GET',
                                dataType: 'json',
                                success: function (response) {

                                    piechartData = response.piechartData;
                                    chartData = response.chartData;
                                    lineChartData = response.lineChartData;
                        
                                updateChart();
                                },
                                error: function (error) {
                                    console.error('Error:', error);
                                }
                            });
                        }

            // Date range picker initialization
            dateRangePickerGraph.daterangepicker({
                opens: 'left',
                startDate: moment(),
                endDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            },  function (start, end, label) {
                    var startDate = start.format('YYYY-MM-DD');
                    var endDate = end.format('YYYY-MM-DD');
                
                    updateChartData(startDate, endDate);
                });

            // Initial rendering of chart based on default date range
            updateChart();




            function updateChart() {
    var chartType = chartTypesSelect.value ?? 'pie';
    var data;

    if (chartType === 'pie' || chartType === 'donut') {
        data = piechartData;
    } else if (chartType === 'line') {
        data = lineChartData.reduce(function (acc, current) {
        if (!acc[current.category]) {
            acc[current.category] = { name: current.category, data: [] };
        }
        acc[current.category].data.push({ x: current.month, y: current.value });
        return acc;
    }, {});

    // Convert the object to an array of objects
    series = Object.values(data).map(function (dataset) {
        return {
            name: dataset.name,
            data: dataset.data.map(function (point) {
                return { x: point.x, y: point.y };
            }),
        };
    });
    } else {
        data = chartData.reduce(function (acc, current) {
            acc[current.category] = current.value;
            return acc;
        }, {});
    }

    var chartLabels = Object.keys(data);
    var chartDatasets = Object.values(data);
    var series;

    if (chartType === 'bar') {
        series = [{ data: chartDatasets }];
    } else if (chartType === 'line') {
        var lineDatasets = chartDatasets.map(function (dataset) {
            return {
                name: dataset.name,
                data: dataset.data.map(function (point) {
                    return { x: point.x, y: point.y };
                }),
            };
        });
        series = lineDatasets;
    } else {
        series = chartDatasets;
    }

    var colors = ['#31c0ce', '#227ec3', '#b443ff', '#f47a00', '#ffbe25'];

    var options = {
        chart: {
            type: chartType,
        },
        labels: chartLabels,
        series: series,
        colors: colors,
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '10px',
                fontWeight: 'bold',
                colors: ['#fff'],
            },
        },
    };

    if (!chartLabels.length || chartLabels.length === 0) {
    if (chartType === 'pie' || chartType === 'donut') {
        options.series = [0]; // Set an empty array to show an empty pie/donut chart
        options.labels = ['No Data']; // Add a label to signify no data
    } else if (chartType === 'line') {
        // Show a line chart with two points for no data
        options.series = [{ name: 'No Data', data: [{ x: 0, y: 0 }, { x: 1, y: 1 }] }];
    } else if (chartType === 'bar') {
        // Show a bar chart with two bars for no data
        options.series = [{ name: 'No Data', data: [{ x: 0, y: 0 }, { x: 1, y: 1 }] }];
    } else {
        // For any other chart type, default to a line chart with two points
        options.chart.type = 'line';
        options.series = [{ name: 'No Data', data: [{ x: 0, y: 0 }, { x: 1, y: 1 }] }];
    }
}



    if (myChart) {
        myChart.updateOptions(options, false, true);
    } else {
        myChart = new ApexCharts(document.getElementById('chart'), options);
        myChart.render();
    }
}


    // Initial chart rendering (pie chart)
    updateChart();

    // Event listener for dropdown change
    chartTypesSelect.addEventListener('change', function () {
        updateChart();
    });

    function randomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    });


</script>


    <script>
      var isUpdate = false;
      $(document).on('click', '.tui-full-calendar-popup-save', function() {
          isUpdate = true;
      });
      var userTimezone = 'America/New_York';
      var userTimeZoneUTC =  'America/New_York';

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
      $(document).ready(function() {
              updateCalender();
                
            });
      $("#accordion").on("hide.bs.collapse show.bs.collapse", e => {
          $(e.target)
              .prev()
              .find("i:last-child")
              .toggleClass("fa-eye fa-eye-slash");
      });

      function updateCalender() {
          var url = '/cases/getEvents/' + '<?php echo e(0); ?>';
          $.ajax({
              url: url, // Replace with your actual route URL
              type: 'GET',
              dataType: 'json',
              success: function(events) {
                  if (events.redirectTo) {
                      window.location.href = events.redirectTo;
                      return;
                  }
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
                      calendarSize: "large",
                      layoutModifiers: ["month-left-align"],
                      eventsData: miniCalEventData,
                      dateChanged: (currentDate, events) => {
                            console.log("date change", currentDate, events);
                            //  dateInt = compare today date with currentDate
                            var dayNumber = currentDate.getDate(); // Get the day of the month (1-31)
                            var monthNumber = currentDate.getMonth(); // Get the month (0-11)
                            var yearNumber = currentDate.getFullYear(); // Get the four digit year (yyyy)
                             updateCalendarStats(dayNumber,monthNumber,yearNumber)
                            
                            
                        },
                        monthChanged: (currentDate, events) => {
                            console.log("month change", currentDate, events);
                        }
                      
                  });
                  
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
                  endDate.setDate(endDate.getDate() + 1);
                //   groupedEvents store to local storage
                    localStorage.setItem('groupedEvents', JSON.stringify(groupedEvents));

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
                                                    <h5 class="allday_meeting_time mb-0">${event.title}</h5>
                                                <p class="allday_meeting_info" style="color: black;">${startTime} - ${endTime}</p>
                                                  </li>`;
                                          }).join('')}
                                      </ul>
                                  </div>
                              </div>
                          </div>`;


                          calendarStats.append(eventsHtml);
                      }
                  });
              },
              error: function(error) {
                  console.error('Error fetching events:', error);
                  var calendarErrorDiv = document.getElementById('calender_id');

                       // Parse the JSON response to get the error message
                    var errorMessage;
                    try {
                        var errorObject = JSON.parse(error.responseText);
                        errorMessage = errorObject.error;
                    } catch (e) {
                        errorMessage = 'An error occurred.';
                    }

                    // Display the extracted error message in the specified div
                    if (calendarErrorDiv) {
                        calendarErrorDiv.innerHTML = errorMessage; // You can modify this to display the error in a way you prefer
                    }
              }
          });
      }
      // updateCalendarStats
      function updateCalendarStats(dayNumber, monthNumber, yearNumber) {
        var calendarStats = $('#calendar-stats');
        calendarStats.empty();

        var targetDate = new Date(yearNumber, monthNumber, dayNumber);
        var now = new Date();

        // groupedEvents get from local storage
        var groupedEvents = JSON.parse(localStorage.getItem('groupedEvents'));

        var eventsScheduled = false; // Flag to check if events are scheduled for the target date

        $.each(groupedEvents, function (date, events) {
            var carbonDate = new Date(date);

            if (isSameDay(carbonDate, targetDate)) {
                var dayLabel;

                if (isSameDay(carbonDate, now)) {
                    dayLabel = 'Today';
                } else if (isTomorrow(carbonDate, now)) {
                    dayLabel = 'Tomorrow';
                } else {
                    dayLabel = formatDate(carbonDate, 'long');
                }

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
                                    ${events.map(function (event, index) {
                                        var startTime = new Date(event.start_time).toLocaleTimeString('en-US', {
                                            timeZone: userTimezone
                                        }, {
                                            hour: 'numeric',
                                            minute: '2-digit'
                                        });
                                        var endTime = new Date(event.end_time).toLocaleTimeString('en-US', {
                                            timeZone: userTimezone
                                        }, {
                                            hour: 'numeric',
                                            minute: '2-digit'
                                        });
                                        return `
                                            <li>
                                                <h5 class="allday_meeting_time mb-0">${event.title}</h5>
                                                <p class="allday_meeting_info" style="color: black;">${startTime} - ${endTime}</p>
                                            </li>`;
                                    }).join('')}
                                </ul>
                            </div>
                        </div>
                    </div>`;

                calendarStats.append(eventsHtml);
                eventsScheduled = true; // Events are scheduled for the target date
            }
        });

        if (!eventsScheduled) {
            var formattedTargetDate = targetDate.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'numeric',
                day: 'numeric',
                year: 'numeric'
            }).toUpperCase();

            // If no events are scheduled for the target date, display a message with the formatted date
            calendarStats.append(`<div class="todo_list_title"><h6>${formattedTargetDate}</h6></div>`);
            calendarStats.append('<div class="all_day_meeting_list_details"><ul><li><h6 class="allday_meeting_time">No events scheduled for this date.</h6> </li> </ul></div>');
        }
        }

            function isSameDay(date1, date2) {
            return date1.getFullYear() === date2.getFullYear() &&
                date1.getMonth() === date2.getMonth() &&
                date1.getDate() === date2.getDate();
        }

                  function isTomorrow(date1, date2) {
                      var tomorrow = new Date(date2);
                      tomorrow.setDate(tomorrow.getDate() + 1);
                      return isSameDay(date1, tomorrow);
                  }
                  function formatDate(date, format) {
                      return new Intl.DateTimeFormat('en-US', {
                          weekday: format
                      }).format(date);
                  }
  </script>


  <script>
        document.getElementById('saveButtonCalendera').addEventListener('click', function() {
            var formData = {
        caseId: document.getElementById('case').value,
        title: document.getElementById('floatingInputSubject').value,
        location: document.getElementById('floatingLocation').value,
        dateRange: document.getElementById('floatingInputDateRange').value,
        isAllDay: document.getElementById('flexCheckAllDay').checked,

    };


    // Separate start and end dates from the dateRange string
    const [startDateString, endTimeString] = formData.dateRange.split(' - ');

    // Parse the start and end dates
    const startDate = parseDateString(startDateString.trim());
    const endDate = parseDateString(endTimeString.trim());

    // Update formData with separate start and end dates
    formData.start = formatDate(startDate);
    formData.end = formatDate(endDate);

    const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    let url = '<?php echo e(route('google.calendar.create-event')); ?>';
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                    eventData: formData
                },
                headers: {
               'X-CSRF-TOKEN': csrfToken
             },
            success: function(response) {
                // Handle the success response
                $('#calendarmodelA').modal('hide');

                show_toastr('success', response.message, 'success');

                console.log(response);
            },
            error: function(error) {
                // Handle the error response
                $('#calendarmodelA').modal('hide');

                show_toastr('error', "Something Went Wrong");
            }
        });
    });

    function parseDateString(dateString) {
        return new Date(dateString);
    }

    function formatDate(date) {
        const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const formattedDate = new Intl.DateTimeFormat('en-US', options).format(date);
        return formattedDate;
    }


    
  </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/umar/code/ecasify/resources/views/dashboard.blade.php ENDPATH**/ ?>