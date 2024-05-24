<!-- Your edit form -->
{{ Form::model($taskData, ['route' => ['tasks.update', $taskData->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
@csrf
@method('put')
<div class="modal-body">

    <div class="container p-4">

        <div class="row">
            <div class="col-12">
                <div class="mb-3 add_modal_input form-group">
                    {{ Form::label('Task_Date', __('Task Title'), ['class' => 'col-form-label']) }}

                    <input type="text" value="{{ $taskData->title }}" class="form-control" name="title" required
                        placeholder="Add task title here">
                        <input type="hidden" name="tab_tasks" value="tab_tasks">


                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex justify-content-start align-items-center gap-2">
                    <div class="mb-3" style="max-width: 380px; width: 100%;">
                        <div class="form-group">
                            {{ Form::label('Task_Date', __('Task Date'), ['class' => 'col-form-label']) }}
                            <div class="input-group justify-content-start add_modal_input">
                                {{ Form::text('date', old('date'), ['class' => 'form-control', 'id' => 'Task_Date', 'autocomplete' => 'off', 'placeholder' => 'YYYY-MM-DD', 'required' => true]) }}
                                <span
                                    style="position: absolute;padding: 10px;cursor: pointer; top: 50%;
                                    transform: translateY(-50%); right: 0px;"
                                    class="input-group-addon calendar_field">
                                    <i class="fa fa-calendar " id="calendar_icon_Task_Date"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- <div>
                        <button type="button" class="btn btn-primary btn_modal_notification">
                            Notification:
                            <svg width="16" height="14" viewBox="0 0 16 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6.2002 6.99995C6.2002 6.52256 6.38984 6.06472 6.7274 5.72716C7.06497 5.38959 7.52281 5.19995 8.0002 5.19995C8.47758 5.19995 8.93542 5.38959 9.27299 5.72716C9.61055 6.06472 9.8002 6.52256 9.8002 6.99995C9.8002 7.47734 9.61055 7.93518 9.27299 8.27274C8.93542 8.61031 8.47758 8.79995 8.0002 8.79995C7.52281 8.79995 7.06497 8.61031 6.7274 8.27274C6.38984 7.93518 6.2002 7.47734 6.2002 6.99995Z"
                                    fill="currentColor" fill-opacity="0.8" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M0 7.0001C0 8.3121 0.34 8.7529 1.02 9.6369C2.3776 11.4001 4.6544 13.4001 8 13.4001C11.3456 13.4001 13.6224 11.4001 14.98 9.6369C15.66 8.7537 16 8.3113 16 7.0001C16 5.6881 15.66 5.2473 14.98 4.3633C13.6224 2.6001 11.3456 0.600098 8 0.600098C4.6544 0.600098 2.3776 2.6001 1.02 4.3633C0.34 5.2481 0 5.6889 0 7.0001ZM8 4.0001C7.20435 4.0001 6.44129 4.31617 5.87868 4.87878C5.31607 5.44139 5 6.20445 5 7.0001C5 7.79575 5.31607 8.55881 5.87868 9.12142C6.44129 9.68403 7.20435 10.0001 8 10.0001C8.79565 10.0001 9.55871 9.68403 10.1213 9.12142C10.6839 8.55881 11 7.79575 11 7.0001C11 6.20445 10.6839 5.44139 10.1213 4.87878C9.55871 4.31617 8.79565 4.0001 8 4.0001Z"
                                    fill="currentColor" fill-opacity="0.8" />
                            </svg>
                        </button>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div>
                    <label for="task-title" class="form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="10" viewBox="0 0 16 10"
                            fill="none">
                            <path d="M1 1H15M1 5H15M1 9H7" stroke="#818181" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        Description:
                    </label>
                    <textarea class="form-control" name="description">{{ $taskData->description }}</textarea>
                </div>

                <div id="inputContainer">
                    <div class="row">
                        <div class="col-md-12 repeater">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card my-3 shadow-none rounded-0 border">
                                        <div class="card-header p-2">
                                            <div class="row flex-grow-1">
                                                <div class="col-md-12 d-flex align-items-center justify-content-end">
                                                    <button type="button" id="addTask"
                                                        class="btn btn-sm btn-primary"> <i
                                                            class="fas fa-plus"></i></button>


                                                </div>

                                            </div>
                                        </div>

                                        <div id="taskList" class="sortable">
                                            <!-- Tasks will be added here -->
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-4 d-flex justify-content-start align-items-start flex-column ">
                <h6>
                    Add the following info to the card
                </h6>
                <div class="dropdowns_wrapper">
                    <div class="dropdown_icons">
                        <label for="case_id">Select Case</label>
                        {!! Form::select('case_id', $cases, null, [
                            'class' => 'form-control multi-select',
                            'id' => 'case_id',
                        ]) !!}
                    </div>
                    <div class="dropdown_icons">

                        <div class="from-control">
                            <label for="task_team">Select Users</label>
                            {{-- {!! Form::select('task_team[]', $teams->toArray(), request()->isMethod('post') ? old('task_team') : null, [
                                'class' => 'form-control',
                                'id' => 'task_team',
                                'multiple',
                                'data-role' => 'tagsinput',
                            ]) !!} --}}
                            {!! Form::select('task_team[]', $allOptions, $your_teams->pluck('id')->toArray(), [
                                'class' => 'form-control',
                                'id' => 'selectOptions',
                                'multiple',
                                'data-role' => 'tagsinput',
                            ]) !!}
                           
                        </div>

                    </div>
                    <div class="dropdown_icons">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                            fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.941 7.033C16.1623 8.84936 15.7542 10.6867 14.7848 12.2386C13.8155 13.7905 12.3434 14.9633 10.6141 15.5612C8.88474 16.1592 7.00269 16.1463 5.28174 15.5246C3.56079 14.9029 2.10496 13.7101 1.157 12.145C1.05383 11.9749 1.02247 11.7707 1.06983 11.5775C1.11718 11.3842 1.23936 11.2177 1.4095 11.1145C1.57964 11.0113 1.78379 10.98 1.97704 11.0273C2.1703 11.0747 2.33683 11.1969 2.44 11.367C2.95064 12.2102 3.64639 12.9263 4.47459 13.4609C5.30279 13.9956 6.24176 14.3348 7.22043 14.4531C8.1991 14.5713 9.19184 14.4654 10.1235 14.1433C11.0552 13.8212 11.9015 13.2915 12.5982 12.5941C13.295 11.8967 13.824 11.05 14.1452 10.118C14.4664 9.18606 14.5714 8.19323 14.4523 7.21466C14.3332 6.23609 13.9931 5.29744 13.4577 4.46971C12.9223 3.64199 12.2057 2.94689 11.362 2.437C11.1917 2.3341 11.0693 2.16777 11.0217 1.97461C10.974 1.78144 11.0051 1.57727 11.108 1.407C11.2109 1.23673 11.3772 1.11431 11.5704 1.06668C11.7636 1.01905 11.9677 1.0501 12.138 1.153C13.1763 1.78059 14.0582 2.63609 14.717 3.65479C15.3759 4.67349 15.7944 5.82869 15.941 7.033ZM9 1C9 1.26522 8.89464 1.51957 8.70711 1.70711C8.51957 1.89464 8.26522 2 8 2C7.73478 2 7.48043 1.89464 7.29289 1.70711C7.10536 1.51957 7 1.26522 7 1C7 0.734784 7.10536 0.48043 7.29289 0.292893C7.48043 0.105357 7.73478 0 8 0C8.26522 0 8.51957 0.105357 8.70711 0.292893C8.89464 0.48043 9 0.734784 9 1ZM2.804 5C2.8707 4.88623 2.91423 4.76039 2.9321 4.62972C2.94996 4.49906 2.94181 4.36615 2.9081 4.23865C2.87439 4.11115 2.81579 3.99158 2.73569 3.88682C2.65558 3.78206 2.55554 3.69418 2.44133 3.62824C2.32712 3.56229 2.20099 3.5196 2.07021 3.5026C1.93943 3.4856 1.80658 3.49464 1.67931 3.52919C1.55203 3.56374 1.43285 3.62313 1.32863 3.70393C1.2244 3.78473 1.13718 3.88535 1.072 4C0.941483 4.22956 0.907063 4.50142 0.976251 4.75626C1.04544 5.01111 1.21262 5.22824 1.44131 5.36027C1.66999 5.49231 1.94162 5.52853 2.19692 5.46104C2.45221 5.39354 2.67045 5.22781 2.804 5ZM1 7C1.26522 7 1.51957 7.10536 1.70711 7.29289C1.89464 7.48043 2 7.73478 2 8C2 8.26522 1.89464 8.51957 1.70711 8.70711C1.51957 8.89464 1.26522 9 1 9C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8C0 7.73478 0.105357 7.48043 0.292893 7.29289C0.48043 7.10536 0.734784 7 1 7ZM5 2.804C5.11465 2.73882 5.21527 2.6516 5.29607 2.54737C5.37687 2.44315 5.43626 2.32397 5.47081 2.19669C5.50536 2.06942 5.5144 1.93657 5.4974 1.80579C5.4804 1.67501 5.43771 1.54888 5.37176 1.43467C5.30582 1.32046 5.21794 1.22042 5.11318 1.14031C5.00842 1.06021 4.88885 1.00161 4.76135 0.967901C4.63385 0.934192 4.50094 0.926036 4.37028 0.943902C4.23961 0.961767 4.11377 1.0053 4 1.072C3.77219 1.20555 3.60646 1.42379 3.53896 1.67908C3.47147 1.93438 3.50769 2.20601 3.63973 2.43469C3.77176 2.66338 3.98889 2.83056 4.24374 2.89975C4.49858 2.96894 4.77044 2.93452 5 2.804Z"
                                fill="#818181" />
                        </svg>
                        <select name="status" class="form-select" aria-label="Default select example" required>
                            <option value="" selected disabled>Task Status</option>
                            <option value="Not Started Yet"
                                {{ $taskData->status == 'Not Started Yet' ? 'selected' : '' }}>Not Started Yet</option>
                            <option value="Incomplete" {{ $taskData->status == 'Incomplete' ? 'selected' : '' }}>
                                Incomplete</option>
                            <option value="In Progress" {{ $taskData->status == 'In Progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="Completed" {{ $taskData->status == 'Completed' ? 'selected' : '' }}>Completed
                            </option>
                        </select>
                    </div>

                    <div class="dropdown_icons">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                            fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.941 7.033C16.1623 8.84936 15.7542 10.6867 14.7848 12.2386C13.8155 13.7905 12.3434 14.9633 10.6141 15.5612C8.88474 16.1592 7.00269 16.1463 5.28174 15.5246C3.56079 14.9029 2.10496 13.7101 1.157 12.145C1.05383 11.9749 1.02247 11.7707 1.06983 11.5775C1.11718 11.3842 1.23936 11.2177 1.4095 11.1145C1.57964 11.0113 1.78379 10.98 1.97704 11.0273C2.1703 11.0747 2.33683 11.1969 2.44 11.367C2.95064 12.2102 3.64639 12.9263 4.47459 13.4609C5.30279 13.9956 6.24176 14.3348 7.22043 14.4531C8.1991 14.5713 9.19184 14.4654 10.1235 14.1433C11.0552 13.8212 11.9015 13.2915 12.5982 12.5941C13.295 11.8967 13.824 11.05 14.1452 10.118C14.4664 9.18606 14.5714 8.19323 14.4523 7.21466C14.3332 6.23609 13.9931 5.29744 13.4577 4.46971C12.9223 3.64199 12.2057 2.94689 11.362 2.437C11.1917 2.3341 11.0693 2.16777 11.0217 1.97461C10.974 1.78144 11.0051 1.57727 11.108 1.407C11.2109 1.23673 11.3772 1.11431 11.5704 1.06668C11.7636 1.01905 11.9677 1.0501 12.138 1.153C13.1763 1.78059 14.0582 2.63609 14.717 3.65479C15.3759 4.67349 15.7944 5.82869 15.941 7.033ZM9 1C9 1.26522 8.89464 1.51957 8.70711 1.70711C8.51957 1.89464 8.26522 2 8 2C7.73478 2 7.48043 1.89464 7.29289 1.70711C7.10536 1.51957 7 1.26522 7 1C7 0.734784 7.10536 0.48043 7.29289 0.292893C7.48043 0.105357 7.73478 0 8 0C8.26522 0 8.51957 0.105357 8.70711 0.292893C8.89464 0.48043 9 0.734784 9 1ZM2.804 5C2.8707 4.88623 2.91423 4.76039 2.9321 4.62972C2.94996 4.49906 2.94181 4.36615 2.9081 4.23865C2.87439 4.11115 2.81579 3.99158 2.73569 3.88682C2.65558 3.78206 2.55554 3.69418 2.44133 3.62824C2.32712 3.56229 2.20099 3.5196 2.07021 3.5026C1.93943 3.4856 1.80658 3.49464 1.67931 3.52919C1.55203 3.56374 1.43285 3.62313 1.32863 3.70393C1.2244 3.78473 1.13718 3.88535 1.072 4C0.941483 4.22956 0.907063 4.50142 0.976251 4.75626C1.04544 5.01111 1.21262 5.22824 1.44131 5.36027C1.66999 5.49231 1.94162 5.52853 2.19692 5.46104C2.45221 5.39354 2.67045 5.22781 2.804 5ZM1 7C1.26522 7 1.51957 7.10536 1.70711 7.29289C1.89464 7.48043 2 7.73478 2 8C2 8.26522 1.89464 8.51957 1.70711 8.70711C1.51957 8.89464 1.26522 9 1 9C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8C0 7.73478 0.105357 7.48043 0.292893 7.29289C0.48043 7.10536 0.734784 7 1 7ZM5 2.804C5.11465 2.73882 5.21527 2.6516 5.29607 2.54737C5.37687 2.44315 5.43626 2.32397 5.47081 2.19669C5.50536 2.06942 5.5144 1.93657 5.4974 1.80579C5.4804 1.67501 5.43771 1.54888 5.37176 1.43467C5.30582 1.32046 5.21794 1.22042 5.11318 1.14031C5.00842 1.06021 4.88885 1.00161 4.76135 0.967901C4.63385 0.934192 4.50094 0.926036 4.37028 0.943902C4.23961 0.961767 4.11377 1.0053 4 1.072C3.77219 1.20555 3.60646 1.42379 3.53896 1.67908C3.47147 1.93438 3.50769 2.20601 3.63973 2.43469C3.77176 2.66338 3.98889 2.83056 4.24374 2.89975C4.49858 2.96894 4.77044 2.93452 5 2.804Z"
                                fill="#818181" />
                        </svg>
                        <select name="priority" class="form-select" aria-label="Default select example" required>
                            <option value="" selected disabled>Task Priority</option>
                            <option value="Low" {{ $taskData->priority == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ $taskData->priority == 'Medium' ? 'selected' : '' }}>Medium
                            </option>
                            <option value="High" {{ $taskData->priority == 'High' ? 'selected' : '' }}>High</option>
                            <option value="None" {{ $taskData->priority == 'None' ? 'selected' : '' }}>None</option>
                        </select>

                    </div>




                    <div class="dropdown_icons">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.3335 2.33325H13.0002M6.3335 6.99992H13.0002M6.3335 11.6666H13.0002"
                                stroke="#818181" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M3 1H1.66667C1.29848 1 1 1.29848 1 1.66667V3C1 3.36819 1.29848 3.66667 1.66667 3.66667H3C3.36819 3.66667 3.66667 3.36819 3.66667 3V1.66667C3.66667 1.29848 3.36819 1 3 1Z"
                                stroke="#818181" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M3 5.66675H1.66667C1.29848 5.66675 1 5.96522 1 6.33341V7.66675C1 8.03494 1.29848 8.33341 1.66667 8.33341H3C3.36819 8.33341 3.66667 8.03494 3.66667 7.66675V6.33341C3.66667 5.96522 3.36819 5.66675 3 5.66675Z"
                                stroke="#818181" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M3 10.3333H1.66667C1.29848 10.3333 1 10.6317 1 10.9999V12.3333C1 12.7014 1.29848 12.9999 1.66667 12.9999H3C3.36819 12.9999 3.66667 12.7014 3.66667 12.3333V10.9999C3.66667 10.6317 3.36819 10.3333 3 10.3333Z"
                                stroke="#818181" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>


                        <button type="button" id="addInput"
                            class="btn btn_task_checklist button_2">Checklist</button>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}




{{-- task list script --}}

<script>
    autoCallGroup();



        var allOptions = @json($allOptions);


        var selectOptions = document.getElementById('selectOptions');
            var choicesInstance = new Choices(selectOptions, {
                removeItemButton: true,
                itemSelectText: '',
            });





        function autoCallGroup() {

            const choices_items = document.querySelectorAll('.choices__item');
            
            choices_items.forEach((choice_item) => {
                const data_value = choice_item.getAttribute('data-value');

                if (data_value.includes(',')) {
                    choice_item.style.color = 'blue';
                }
            });
        }


        var shouldUpdateSelection = true;

        function selectUsersInList(userIds) {

            if (!Array.isArray(userIds)) {
                userIds = [userIds];
            }

            // Clear previous selections
            choicesInstance.clearInput();
            userIds.forEach(function(userId) {

            
                if (allOptions.hasOwnProperty(userId)) {

                

                    if (allOptions[userId].startsWith("group")) {
                        // This is a group, add individual users from the group
                        var groupUserIds = allOptions[userId].split(',').slice(1);
                        groupUserIds.forEach(function(individualUserId) {
                            choicesInstance.setChoiceByValue(individualUserId, true);
                        });

                    } else {

                        // This is an individual user
                        choicesInstance.setChoiceByValue(userId, true);
                    }
                }
            });

        }

        selectOptions.addEventListener('change', function(e) {
        
            e.preventDefault();
            var selectedValue = e.target.value;
            if (shouldUpdateSelection) {
                var selectedValues = e.detail.value.split(',');
                

                choicesInstance.clearInput();
                selectUsersInList(selectedValues);
            }

            choices_list = document.querySelector('.choices__list--multiple');
            choices_items = choices_list.querySelectorAll('.choices__item');
            

            for (let i = 0; i < choices_items.length; i++) {
                const choice_item = choices_items[i];
                const data_value = choice_item.getAttribute('data-value');
                if (data_value.includes(',')) {
                    choice_item.style.display = 'none';
                }
            }
            shouldUpdateSelection = true;
        });

        function showChoiceItem(value) {

            choices_list = document.querySelector('.choices__list--multiple');
            choices_items = choices_list.querySelectorAll('.choices__item');

            for (let i = 0; i < choices_items.length; i++) {
                const choice_item = choices_items[i];
                const data_value = choice_item.getAttribute('data-value');

                if (data_value.includes(value)) {

                    shouldUpdateSelection = false;
                    choicesInstance.removeActiveItemsByValue(data_value);
                }
            }
        }

        // Listen to the Choices.js removeItem event
        selectOptions.addEventListener('removeItem', function(event) {

            var removedValue = event.detail.value;
            showChoiceItem(removedValue);
            autoCallGroup();
            shouldUpdateSelection = false;
            choicesInstance.removeActiveItemsByValue(removedValue);

        });

        var initialSelectedValues = choicesInstance.getValue();
        selectUsersInList(initialSelectedValues);


        var initialSelectedValues = choicesInstance.getValue();
        selectUsersInList(initialSelectedValues);

            function updateChoicesWithUsers(allOptions) {
            $('#case_id').change(function() {
                var selectedCaseId = $(this).val();
                console.log(selectedCaseId);
                if (selectedCaseId) {
                    // Send an AJAX request to retrieve users associated with the selected case
                    $.ajax({
                        url: '/get-users-for-case/' + selectedCaseId, // Replace with the actual URL
                        type: 'GET',
                        success: function(data) {
                            var userIds = data.your_advocates.split(',');
                           
                            var filteredUsers = [];
                            for (var i = 0; i < userIds.length; i++) {
                                var userId = userIds[i];
                                if (allOptions[userId]) {
                                    filteredUsers.push({
                                        value: userId,
                                        label: allOptions[userId]
                                    });
                                }
                            }
                            // console.log(filteredUsers);
                            // Update the Choices select field with the filtered users
                            choicesInstance.clearChoices();
                            choicesInstance.setChoices(filteredUsers, 'value', 'label', true);
                        },
                        error: function() {
                            // Handle the error, if necessary
                        }
                    });
                }
            });
        
    }

    // Call the function with allOptions
    updateChoicesWithUsers(allOptions);
    $('#case_id').trigger('change');

</script>


<script>
    $(document).ready(function() {

        var incidentDateInput = document.getElementById('Task_Date');

        var redBorderColorClass = 'red-border';

        function formatDate(date) {
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2,
                '0'); // Adding 1 because months are zero-based
            var day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        var incidentPicker = new Pikaday({
            field: incidentDateInput,
            format: 'YYYY-MM-DD',
            onSelect: function(selectedDate) {
                var formattedDate = formatDate(selectedDate);
                incidentDateInput.value = formattedDate;

                // Check if the selected date is today or in the past
                var currentDate = new Date();
                currentDate.setHours(0, 0, 0, 0); // Set the time to midnight
                if (selectedDate <= currentDate) {
                    incidentDateInput.classList.add(redBorderColorClass); // Add red border class
                } else {
                    incidentDateInput.classList.remove(
                        redBorderColorClass); // Remove red border class
                }
            },
        });

        var calendarIconIncident = document.getElementById('calendar_icon_Task_Date');
        calendarIconIncident.addEventListener('click', function() {
            incidentPicker.show();
           
        });

        incidentDateInput.addEventListener('input', function() {
            autoFormatDate(this);
            validateAndAdjustDate(this);

        });

        function autoFormatDate(input) {
            var value = input.value.replace(/[^\d-]/g, ''); // Allow only digits and hyphens

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

            var parts = input.value.split('-');
            

            if (parts.length >= 2) {
                var year = parseInt(parts[0], 10);
                var month = parseInt(parts[1], 10);

                if (isNaN(year) || isNaN(month)) {
                    return;
                }

                var currentDate = new Date();
                var currentYear = currentDate.getFullYear();
                var currentMonth = currentDate.getMonth() + 1; // JavaScript months are zero-based

                var monthString = month.toString();
                var numberOfDigits = monthString.length;

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
                var day = parseInt(parts[2], 10);

                if (isNaN(year) || isNaN(month) || isNaN(day)) {
                    return;
                }

                var currentDate = new Date();
                var currentYear = currentDate.getFullYear();
                var currentDay = currentDate.getDate();


                var dayString = day.toString();
                var numberOfDayDigits = dayString.length;
               

                if (numberOfDayDigits === 2) {
                    var daysInSelectedMonth = new Date(year, month - 1, 0).getDate(); // Subtract 1 from month
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
</script>


<script>
    var taskCounter;

    var addTaskButton = document.getElementById("addTask");
    addTaskButton.addEventListener("click", () => {
        createTask();
    });

    var oldTaskData = {!! json_encode($taskData) !!};

    var taskIds = oldTaskData.tasks.map(task => task.id);
    var subtaskIds = oldTaskData.tasks.reduce((acc, task) => {
        return acc.concat(task.subtasks.map(subtask => subtask.id));
    }, []);

    var allIds = taskIds.concat(subtaskIds);
    var highestId = Math.max(...allIds);
    taskCounter = highestId + 1;


    displayOldTasks(oldTaskData);


    function displayOldTasks(taskData) {
        var taskList = document.getElementById("taskList");

        taskData.tasks.forEach((task) => {
            // Create a task element and display the task
            var idtask = 'task';
            var taskElement = createTaskElement(task, idtask);
            taskList.appendChild(taskElement);
            initializeSortable();

            // Create and display subtasks for this task
            task.subtasks.forEach((subtask) => {
                var idsubtask = 'subtask';
                var subtaskElement = createTaskElement(subtask, idsubtask,task);
                taskList.appendChild(subtaskElement);
                initializeSortable();
            });
        });

        // Initialize Sortable after displaying the tasks
        initializeSortable();
    }
    

    function createTaskElement(taskData, id,task=null) {
        var taskElement = document.createElement("div");
        taskElement.classList.add("task");
        taskElement.classList.add("task_custom");


        var taskRow = document.createElement("div");
        taskRow.classList.add("task-list");

        // Check if id is equal to idsubtask and add the appropriate class
        if (id === "subtask") {
            taskRow.classList.add("subtask-list");
            taskRow.classList.add("row");
        taskRow.setAttribute("data-task-id", taskData.id);
        taskRow.innerHTML = `
        <div class="col-md-12 d-flex m-auto align-items-center gap-1">
            <span class="drag-handle">&#9776;</span>
            <input type="text" class="task-title form-control" name="task[${task.id}][subtask][${taskData.id}]" onkeydown="createNewTask(event)" value="${taskData.title}">
            <button class="delete-task btn btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>`;
        }else{
            taskRow.classList.add("row");
        taskRow.setAttribute("data-task-id", taskData.id);
        taskRow.innerHTML = `
        <div class="col-md-12 d-flex m-auto align-items-center gap-1">
            <span class="drag-handle">&#9776;</span>
            <input type="text" class="task-title form-control" name="task[${taskData.id}][title]" onkeydown="createNewTask(event)" value="${taskData.title}">
            <button class="delete-task btn btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>`;
        }
        

        // Add event listener for task deletion

        var deleteButton = taskRow.querySelector(".delete-task");
        deleteButton.addEventListener("click", () => {
            taskList.removeChild(taskElement);
        });

        // Append the task row to the task element
        taskElement.appendChild(taskRow);

        return taskElement;
    }



    function getParentTaskId(prevTask) {
        var parentTaskId;
        if (prevTask) {
            var prevTaskId = parseInt(prevTask.querySelector(".task-list.row").getAttribute(
                "data-task-id"));
            // Check if prevTask .task-list.row contains subtask-list class
            if (prevTask.querySelector(".task-list.row").classList.contains("subtask-list")) {
                var prevParentTaskId = getParentTaskId(prevTask.previousElementSibling);
                parentTaskId = prevParentTaskId || prevTaskId;
            } else {
                parentTaskId = prevTaskId;
            }
        }
        return parentTaskId;
    }

    // Function to initialize Sortable
    function initializeSortable() {
        var taskList = document.getElementById("taskList");
        var sortable = new Sortable(taskList, {
            group: "tasks",
            animation: 150,
            handle: ".drag-handle",
        });
        var taskIds = [];
        var lastClientX = null;
        var taskId;
        var parentTaskId;
        var prevTaskId;
        taskList.addEventListener("dragover", (event) => {
            var draggedTask = document.querySelector(".task.sortable-chosen");
            var targetTask = event.target.closest(".task-list");
            if (draggedTask) {
                var taskId = parseInt(draggedTask.querySelector(".task-list.row").getAttribute(
                    "data-task-id"));
                var prevTask = draggedTask.previousElementSibling;
                prevTaskId = getParentTaskId(prevTask);
            }
            if (draggedTask && targetTask && draggedTask !== targetTask) {
                var clientXDiff = event.clientX - lastClientX;
                console.log("Parent Task ID:", taskId);
                if (clientXDiff > 0 && taskId != 1) {
                    if (!targetTask.classList.contains("subtask-list")) {
                        targetTask.classList.add("subtask-list");
                        updateInputFieldName(targetTask, prevTaskId);
                    }
                } else if (clientXDiff < 0) {
                    if (targetTask.classList.contains("subtask-list")) {
                        var oldParentTaskId = getParentTaskId(targetTask
                            .previousElementSibling); // Get old parent task ID

                        targetTask.classList.remove("subtask-list");
                        updateInputFieldNameSubTask(targetTask, oldParentTaskId);
                    }
                }
            }
            lastClientX = event.clientX;
        });
    }

    function createTask() {
    taskCounter++; // Increment the task counter
    var taskList = document.getElementById("taskList");
    var task = document.createElement("div");
    task.classList.add("task");
    task.classList.add("task_custom");
    task.innerHTML = `
    <div class="task-list row" data-task-id="${taskCounter}">
        <div class="col-md-12 d-flex m-auto align-items-center gap-1">
            <span class="drag-handle">&#9776;</span>
            <input type="text" class="task-title form-control" name="task[${taskCounter}][title]" placeholder="Type your Task!" onkeydown="createNewTask(event)">
            <button class="delete-task btn  btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>
    </div>`;
    // Add event listeners for task actions
    var deleteButton = task.querySelector(".delete-task");
    deleteButton.addEventListener("click", () => {
        taskList.removeChild(task);
    });
    // Append the task element to the task list
    taskList.appendChild(task);
    // Make the task sortable
    initializeSortable();

    // Focus the newly created input field
    var newTaskInput = task.querySelector(".task-title");
    newTaskInput.focus();
    }

    function createNewTask(event) {
        if (event.key === "Enter") {
            // Prevent the default Enter key behavior (form submission)
            event.preventDefault();

            // Create a new task when Enter key is pressed
            createTask();
        }
    }

    function updateInputFieldName(taskElement, prevTaskId) {
        var taskId = taskElement.getAttribute("data-task-id");
        var inputField = taskElement.querySelector(".task-title");
        inputField.name = `task[${prevTaskId}][subtask][${taskId}]`;

        var subtaskElements = taskElement.querySelectorAll(".task-list.row.subtask-list");

        var currentIndex = -1;
        subtaskElements.forEach((subtaskElement, index) => {
            var subtaskId = subtaskElement.getAttribute("data-task-id");
            if (subtaskId === taskId) {
                currentIndex = index;
            }
        });

        for (let i = currentIndex + 1; i < subtaskElements.length; i++) {
            var subtaskElement = subtaskElements[i];
            var subtaskId = subtaskElement.getAttribute("data-task-id");
            var subtaskInputField = subtaskElement.querySelector(".task-title.form-control");
            subtaskInputField.name = `task[${prevTaskId}][subtask][${subtaskId}]`;
        }
    }

    function updateInputFieldNameSubTask(taskElement, oldParentTaskId) {
        var taskId = taskElement.getAttribute("data-task-id");
        var inputField = taskElement.querySelector(".task-title");
        inputField.name = `task[${taskId}][title]`;

        var subtaskElements = taskElement.querySelectorAll(".task-list.row.subtask-list");

        var currentIndex = -1;
        subtaskElements.forEach((subtaskElement, index) => {
            var subtaskId = subtaskElement.getAttribute("data-task-id");
            if (subtaskId === taskId) {
                currentIndex = index;
            }
        });

        for (let i = currentIndex + 1; i < subtaskElements.length; i++) {
            var subtaskElement = subtaskElements[i];
            var subtaskId = subtaskElement.getAttribute("data-task-id");
            var subtaskInputField = subtaskElement.querySelector(".task-title.form-control");
            subtaskInputField.name = `task[${taskId}][subtask][${subtaskId}]`;
        }
    }
</script>


