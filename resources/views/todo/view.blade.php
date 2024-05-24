<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end align-items-end">
                @can('edit tasks')
                    <div class="action-btn bg-light-secondary ms-2">

                        <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center edit-button"
                        data-url="{{ route('tasks.edit', $taskData->id) }}" data-size="xl addTaskModal_wrapper"
                        data-ajax-popup="true" data-title="{{ __('Task Edit') }}" title="{{ __('Edit Task') }}"
                        data-bs-toggle="tooltip" data-bs-placement="top"> <i class="ti ti-edit"></i></a>
                    </div>
                @endcan
            </div>
        </div>

        <div class="tsk_model_body mt-2">
            <div class="d-flex justify-content-between align-items-center  gap-2">
                <h6>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                        fill="none">
                        <path d="M6 8L9 11L17 3" stroke="#818181" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path
                            d="M17 9V15C17 15.5304 16.7893 16.0391 16.4142 16.4142C16.0391 16.7893 15.5304 17 15 17H3C2.46957 17 1.96086 16.7893 1.58579 16.4142C1.21071 16.0391 1 15.5304 1 15V3C1 2.46957 1.21071 1.96086 1.58579 1.58579C1.96086 1.21071 2.46957 1 3 1H12"
                            stroke="#818181" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ $taskData->title ?? '' }}
                </h6>
                <p class="text-muted mb-0">{{ $taskData->date }}</p>
            </div>

            <div class="my-3">
                <div>
                    <b>Description: </b><span class="pl-3">{{ $taskData->description }}</span>
                </div>
                <div>
                    <b>Assignees:</b><span class="pl-3"> @php
                        $taskTeamArray = explode(',', $taskData->task_team);
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
                </div>
                <div>
                    <b>Selected Case: </b><span class="pl-3"><a
                            href="{{ route('cases.show', $taskData->associatedCase->id) }}">
                            {{ !empty($taskData->associatedCase->name) ? $taskData->associatedCase->name : ' ' }}
                        </a></span>
                </div>
                <div>
                    <b>Task Status: </b><span>{{ $taskData->status }}</span>
                </div>
                <div>
                    <b>Task Priority: </b><span class="pl-3">{{ $taskData->priority }}</span>
                </div>
            </div>
            @if(count($taskData->tasks) > 0)
            <div class="tsk_model_progress_container">
                @php
                    $totalTasks = 0;
                    $checkedTasks = 0;
                    $totalSubtasks = 0;
                    $checkedSubtasks = 0;
                @endphp
                <div class="progress">
                    @foreach ($taskData->tasks as $task)
                        @php
                            $totalTasks++;
                            if ($task->status == 1) {
                                $checkedTasks++;
                            }
                        @endphp
                    @endforeach
                    @foreach ($taskData->tasks as $task)
                        @if (!$task->subtasks->isEmpty())
                            @foreach ($task->subtasks as $subtask)
                                @php
                                    $totalSubtasks++;
                                    if ($subtask->status == 1) {
                                        $checkedSubtasks++;
                                    }
                                @endphp
                            @endforeach
                        @endif
                    @endforeach
                    @php
                        $denominator = $totalTasks + $totalSubtasks;
                        $totalPercentage = ($denominator === 0) ? 0 : (($checkedTasks + $checkedSubtasks) / $denominator) * 100;                        
                    @endphp
                    <div class="progress-bar" role="progressbar" style="width: {{ $totalPercentage }}%"
                        aria-valuenow="{{ $totalPercentage }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <p class="mb-0 progress_bar_all">
                    {{ number_format($totalPercentage, 2) }}%

                </p>
            </div>
            @endif
            <div class="mt-5 tsk_model_list card-body">
                @if ($taskData->tasks->isEmpty())
                    <p class="text-center">No tasks found.</p>
                @else
                    <ul class="p-0 mb-0" style="list-style-type: none">
                        @foreach ($taskData->tasks as $task)
                            <li class="mb-2">
                                <label class="list-group-item tb_checbox">
                                    <input class="form-check-input me-1 task-checkbox" type="checkbox"
                                        data-task-id="{{ $task->id }}" {{ $task->status == 1 ? 'checked' : '' }}>
                                    {{ $task->title }}
                                </label>
                                @if (!$task->subtasks->isEmpty())
                                    <ul class="mt-2" style="list-style-type: none;margin-left:5px">
                                        @foreach ($task->subtasks as $subtask)
                                            <li>
                                                <label class="list-group-item tb_checbox">
                                                    <input class="form-check-input me-1 subtask-checkbox"
                                                        type="checkbox" data-task-id="{{ $task->id }}"
                                                        data-subtask-id="{{ $subtask->id }}"
                                                        {{ $subtask->status == 1 ? 'checked' : '' }}>
                                                    {{ $subtask->title }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>

    </div>

    <div class="d-flex mt-3 justify-content-between align-items-center  gap-2">
        <h6>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                fill="none">
                <path d="M6 8L9 11L17 3" stroke="#818181" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                <path
                    d="M17 9V15C17 15.5304 16.7893 16.0391 16.4142 16.4142C16.0391 16.7893 15.5304 17 15 17H3C2.46957 17 1.96086 16.7893 1.58579 16.4142C1.21071 16.0391 1 15.5304 1 15V3C1 2.46957 1.21071 1.96086 1.58579 1.58579C1.96086 1.21071 2.46957 1 3 1H12"
                    stroke="#818181" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
           History
        </h6>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div style="height: 177px" class="tsk_table_case_edit_history custom-scroll">
                <ul class="p-0 mb-0" style="list-style-type: none">
                    <li class="mb-2">
                        @foreach ($taskData->tasks as $task)
                            @foreach (TasksDataById($task->id) as $taskLog)
                                <button class="btn btn_show_history"
                                    data-title="{{ $taskLog->task_title }}">
                                    <span>
                                        {{ UsersNameById($taskLog->user_id) }}
                                    </span>
                                    <span>
                                        {{ ucfirst($taskLog->action) }}
                                    </span>
                                    <span>
                                        {{ $taskLog->created_at }}
                                    </span>
        
        
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="5" height="6"
                                            viewBox="0 0 5 6" fill="none">
                                            <path
                                                d="M0.970298 0.113098L0.984253 5.30566L4.28164 2.7005L0.970298 0.113098Z"
                                                fill="#AFAFAF" />
                                        </svg>
                                    </span>
                                </button>
                            @endforeach
        
                            {{-- subtask --}}
        
                            @foreach ($task->subtasks as $subtask)
                                @foreach (SubTasksDataById($subtask->task_id) as $subtaskLog)
                                    <button class="btn btn_show_history"
                                        data-title="{{ $subtaskLog->task_title }}">
                                        <span>
        
                                            {{ UsersNameById($subtaskLog->user_id) }}
                                        </span>
                                        <span>
                                            {{ ucfirst($subtaskLog->action) }}
                                        </span>
                                        <span>
                                            {{ $subtaskLog->created_at }}
                                        </span>
        
        
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                width="5" height="6"
                                                viewBox="0 0 5 6"
                                                fill="none">
                                                <path
                                                    d="M0.970298 0.113098L0.984253 5.30566L4.28164 2.7005L0.970298 0.113098Z"
                                                    fill="#AFAFAF" />
                                            </svg>
                                        </span>
                                    </button>
                                @endforeach
                            @endforeach
                        @endforeach
        
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div style="width: 100%" class="tsk_table_case_show_history custom-scroll">
                <ul class="mb-0" style="white-space: normal;">
                    <li class="mb-2">
                        <p class="mb-0 tsk_table_case_show_history_click">Not
                            Selected</p>

                    </li>
                </ul>
            </div>
        </div>
    </div>
    

</div>


<style>
    .btn_show_history.active {
        font-weight: bold;
    }
</style>


<script>

$('.btn_show_history:first').trigger('click');

$('.btn_show_history').click(function() {
    // Remove the "active" class from all buttons to reset their styles
    $('.btn_show_history').removeClass('active');

    // Add the "active" class to the clicked button
    $(this).addClass('active');

    var title = $(this).data('title');
    $('.tsk_table_case_show_history_click').text(title);
});

    function updateProgressBar() {
        const totalTasks = $('.task-checkbox').length;
        const totalCheckedTasks = $('.task-checkbox:checked').length;
        const totalSubtasks = $('.subtask-checkbox').length;
        const totalCheckedSubtasks = $('.subtask-checkbox:checked').length;

        const totalChecked = totalCheckedTasks + totalCheckedSubtasks;
        const totalItems = totalTasks + totalSubtasks;
        
        const totalPercentage = totalItems > 0 ? (totalChecked / totalItems) * 100 : 0;

        const progressBar = $('.progress-bar');

        const progressBarall = $('.progress_bar_all');

        progressBar.css('width', totalPercentage + '%');
        progressBar.attr('aria-valuenow', totalPercentage);
        progressBarall.text(totalPercentage.toFixed(2) + '%');

        const formattedPercentage = totalPercentage.toFixed(2) + '%';
        progressBarall.text(formattedPercentage);

    }

    $(document).ready(function() {
        // Handle the checkbox change event
        $('.task-checkbox').change(function() {
            var checkbox = $(this);
            var isChecked = checkbox.is(':checked') ? 1 : 0;
            var taskId = checkbox.data('task-id');
            var subtaskId = checkbox.data('subtask-id');

            if (isChecked) {
                $('.subtask-checkbox[data-task-id="' + taskId + '"]').prop('checked', true);
            } else {
                $('.subtask-checkbox[data-task-id="' + taskId + '"]').prop('checked', false);
            }

            $.ajax({
                method: 'POST',
                url: "{{ route('tasks.updateTaskStatus') }}",
                data: {
                    task_id: taskId,
                    subtask_id: subtaskId,
                    is_checked: isChecked
                },
                success: function(response) {
                    updateProgressBar();
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });

        $('.subtask-checkbox').change(function() {
            var checkbox = $(this);
            var isChecked = checkbox.is(':checked') ? 1 : 0;
            var taskId = checkbox.data('task-id');
            var subtaskId = checkbox.data('subtask-id');

            var allSubtasksChecked = $('.subtask-checkbox[data-task-id="' + taskId + '"]').length ===
                $('.subtask-checkbox[data-task-id="' + taskId + '"]:checked').length;

            if (allSubtasksChecked) {
                $('.task-checkbox[data-task-id="' + taskId + '"]').prop('checked', true);
            } else {

                $('.task-checkbox[data-task-id="' + taskId + '"]').prop('checked', false);
            }

            $.ajax({
                method: 'POST',
                url: "{{ route('tasks.updateTaskStatus') }}",
                data: {
                    task_id: taskId,
                    subtask_id: subtaskId,
                    is_checked: isChecked
                },
                success: function(response) {
                    updateProgressBar();
                },
                error: function(error) {
                    console.error(error);
                }
            });
        });
    });
</script>
