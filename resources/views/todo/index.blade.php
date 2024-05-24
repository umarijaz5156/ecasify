@extends('layouts.app')

@section('page-title', __('Tasks'))

@section('action-button')
    @can('create tasks')
        <div class="text-sm-end d-flex all-button-box justify-content-sm-end">
            <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="xl addTaskModal_wrapper"
                data-title="Add Task" data-url="{{ route('to-do.create') }}" data-toggle="tooltip"
                title="{{ __('Create New Task') }}" data-bs-original-title="{{ __('Create New Task') }}" data-bs-placement="top"
                data-bs-toggle="tooltip">
                <i class="ti ti-plus"></i>
            </a>
        </div>
    @endcan
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Tasks') }}</li>
@endsection

@section('content')
    {{-- <div class="row p-0 g-0 justify-content-center">
        <div class=" border-bottom">

            <div class="p-2 border-bottom">
                <ul class="nav nav-pills nav-fill" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-user-tab-1" data-bs-toggle="pill"
                            data-bs-target="#pills-user-1" type="button">{{ __('All') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-user-tab-2" data-bs-toggle="pill" data-bs-target="#pills-user-2"
                            type="button">{{ __('Previous') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-user-tab-3" data-bs-toggle="pill" data-bs-target="#pills-user-3"
                            type="button">{{ __('Upcoming') }}</button>
                    </li>
                </ul>
            </div>
            <div class="card shadow-none bg-transparent">
                <div class="">
                    <div class="tab-content table-border-style" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-user-1" role="tabpanel"
                            aria-labelledby="pills-user-tab-1 table-responsive">
                            <table class="table dataTable data-table ">
                                <thead>
                                    <tr>
                                        <th>{{ __('Tasks') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Related to Case') }}</th>
                                        <th>{{ __('Created by') }}</th>
                                        <th width="100px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                    @foreach ($todos as $todo)
                                        <tr>
                                            <td>
                                              
                                                <a href="#"
                                                class=""
                                                data-url="{{ route('to-do.show', $todo->id) }}" data-size="xl"
                                                data-ajax-popup="true" data-title="{{ __(' View Tasks') }}"
                                                title="{{ __('View Task') }}" data-bs-toggle="tooltip"
                                                data-bs-placement="top">  {{ $todo->title }}
                                            </a>
                                            </td>
                                           
                                            <td>{{ $todo->date }}</td>
                                            <td>{{ $todo->tasks->status ?? '' }}</td>

                                            <td> 
                                                <a href="{{ route('cases.show', $todo->associatedCase->id) }}">
                                                    {{ !empty($todo->associatedCase->name) ? $todo->associatedCase->name : ' ' }} 
                                                    </a>
                                            </td>
                                            <td> {{ $todo->createdByUser->name }} </td>
                                          
                                            <td>
                                           

                                                @can('view tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            id="taskLink{{ $todo->id }}" 
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                            data-url="{{ route('to-do.show', $todo->id) }}" data-size="xl"
                                                            data-ajax-popup="true" data-title="{{ __(' View Tasks') }}"
                                                            title="{{ __('View Task') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"><i class="ti ti-eye "></i></a>
                                                    </div>
                                                    
                                                @endcan

                                                @can('edit tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                            data-url="{{ route('to-do.edit', $todo->id) }}" data-size="xl"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Tasks') }}"
                                                            title="{{ __('Edit Task') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"><i class="ti ti-edit "></i></a>
                                                    </div>
                                                @endcan

                                                @can('delete tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-confirm-yes="delete-form-{{ $todo['id'] }}"
                                                            title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['tasks.destroy', $todo->id],
                                                    'id' => 'delete-form-' . $todo->id,
                                                ]) !!}
                                                {!! Form::close() !!}
                                              

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade " id="pills-user-2" role="tabpanel"
                            aria-labelledby="pills-user-tab-2 table-responsive">
                            <table class="table dataTable data-table ">
                                <thead>
                                    <tr>
                                        <th>{{ __('Tasks') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Relate to Case') }}</th>
                                        <th>{{ __('Created by') }}</th>
                                        <th width="100px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pending_todo as $todo)
                                        <tr>
                                            <td>
                                                {{ $todo->title }}
                                            </td>
                                          
                                            <td>{{ $todo->date }}</td>
                                            <td>{{ $todo->tasks->status ?? '' }}</td>
                                            <td> 
                                                <a href="{{ route('cases.show', $todo->associatedCase->id) }}">
                                                    {{ !empty($todo->associatedCase->name) ? $todo->associatedCase->name : ' ' }} 
                                                    </a>
                                            </td>
                                            <td> {{ $todo->createdByUser->name }} </td>
                                          
                                            <td>
                                           

                                                @can('view tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                            data-url="{{ route('to-do.show', $todo->id) }}" data-size="xl"
                                                            data-ajax-popup="true" data-title="{{ __(' View Tasks') }}"
                                                            title="{{ __('View Tasks') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"><i class="ti ti-eye "></i></a>
                                                    </div>
                                                @endcan

                                                @can('edit tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                            data-url="{{ route('to-do.edit', $todo->id) }}" data-size="xl"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Tasks') }}"
                                                            title="{{ __('Edit Tasks') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"><i class="ti ti-edit "></i></a>
                                                    </div>
                                                @endcan

                                                @can('delete tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-confirm-yes="delete-form-{{ $todo['id'] }}"
                                                            title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['tasks.destroy', $todo->id],
                                                    'id' => 'delete-form-' . $todo->id,
                                                ]) !!}
                                                {!! Form::close() !!}
                                              

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade " id="pills-user-3" role="tabpanel"
                            aria-labelledby="pills-user-tab-3 table-responsive">
                            <table class="table dataTable data-table ">
                                <thead>
                                    <tr>
                                        <th>{{ __('Task') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Relate to Case') }}</th>
                                        <th>{{ __('Created by') }}</th>
                                        <th width="100px">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcoming_todo as $todo)
                                        <tr>
                                            <td>
                                                {{ $todo->title }}
                                            </td>
                                            <td>{{ $todo->date }}</td>
                                            <td>{{ $todo->tasks->status ?? '' }}</td>
                                            <td> 
                                                <a href="{{ route('cases.show', $todo->associatedCase->id) }}">
                                                    {{ !empty($todo->associatedCase->name) ? $todo->associatedCase->name : ' ' }} 
                                                    </a>
                                            </td>
                                            <td> {{ $todo->createdByUser->name }} </td>
                                          
                                            <td>
                                           

                                                @can('view tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                            data-url="{{ route('to-do.show', $todo->id) }}" data-size="xl"
                                                            data-ajax-popup="true" data-title="{{ __(' View ToDo') }}"
                                                            title="{{ __('View ToDo') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"><i class="ti ti-eye "></i></a>
                                                    </div>
                                                @endcan

                                                @can('edit tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center "
                                                            data-url="{{ route('to-do.edit', $todo->id) }}" data-size="xl"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Tasks') }}"
                                                            title="{{ __('Edit Tasks') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top"><i class="ti ti-edit "></i></a>
                                                    </div>
                                                @endcan

                                                @can('delete tasks')
                                                    <div class="action-btn bg-light-secondary ms-2">
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-confirm-yes="delete-form-{{ $todo['id'] }}"
                                                            title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['tasks.destroy', $todo->id],
                                                    'id' => 'delete-form-' . $todo->id,
                                                ]) !!}
                                                {!! Form::close() !!}
                                              

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                     
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <input type="hidden" id="taskId" value="{{ $taskId ?? '' }}">
    <section class="table_section">
        {{-- <div class="row p-0">
            <div class="col-sm-6 col-md-4 col-xl-2 custom_select_col">
                <label for="task-Priority" class="form-label">View</label>
                <select name="tasks[priority]" class="form-select" aria-label="Default select example" required="">
                    <option value="" selected="" disabled="">View</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="None">None</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-4 col-xl-2 custom_select_col">
                <label for="task-Priority" class="form-label">Status</label>
                <select name="tasks[priority]" class="form-select" aria-label="Default select example" required="">
                    <option value="" selected="" disabled="">Status</option>
                    <option value="Low">Incomplete</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="None">None</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-4 col-xl-2 custom_select_col">
                <label for="task-Priority" class="form-label">Date</label>
                <select name="tasks[priority]" class="form-select" aria-label="Default select example" required="">
                    <option value="" selected="" disabled="">Date</option>
                    <option value="Low">Last 7 days</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="None">None</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-4 col-xl-2 custom_select_col">
                <label for="task-Priority" class="form-label">Priority</label>
                <select name="tasks[priority]" class="form-select" aria-label="Default select example" required="">
                    <option value="" selected="" disabled="">Priority</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="None">None</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-4 col-xl-2 custom_select_col">
                <label for="task-Priority" class="form-label">Assigned to</label>
                <select name="tasks[priority]" class="form-select" aria-label="Default select example" required="">
                    <option value="" selected="" disabled="">Assigned to</option>
                    <option value="Low">John Doe</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="None">None</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-4 col-xl-2 custom_select_col">
                <label for="task-Priority" class="form-label">By Case </label>
                <select name="tasks[priority]" class="form-select" aria-label="Default select example" required="">
                    <option value="" selected="" disabled="">By Case </option>
                    <option value="Low"> The Battle Over a </option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="None">None</option>
                </select>
            </div>
            <div class="col-md-12">
                <div class="d-flex justify-content-end align-items-center filter_btn">
                    <button type="button" class="btn btn-primary">Apply Filters</button>
                    <button type="button" class="btn btn_clear">Clear Filters</button>
                </div>
            </div>
        </div> --}}
        <div class="row p-0 g-0 justify-content-center mt-5">
            <div class="tsk_table_wrapper">
                <table class="table dataTable data-table ">
                    <thead>
                        <tr>
                            <th class="p-0">

                            </th>
                            <th>
                                <label class="list-group-item tb_checbox">
                                    {{-- <input class="form-check-input me-1" type="checkbox" value=""> --}}
                                    #
                                </label>
                            </th>
                            <th>{{ __('Tasks') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('PRIORITY') }}</th>
                            <th>{{ __('Related to Case') }}</th>
                            <th>{{ __('Created by') }}</th>
                            <th width="100px">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $index = 0;
                        @endphp
                        @foreach ($todos as $todo)
                            @php
                                $index++;
                            @endphp
                            <tr>
                                <td class="p-0">
                                    <button class="accordion-item btn accordian_table_btn" id="headingOne"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne-{{ $index }}"
                                        aria-expanded="true" aria-controls="collapseOne">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="11"
                                            viewBox="0 0 8 11" fill="none">
                                            <path
                                                d="M0.254561 -1.34278e-05L-1.19898e-05 10.997L7.1254 5.66051L0.254561 -1.34278e-05Z"
                                                fill="#AFAFAF" />
                                        </svg>
                                    </button>
                                </td>
                                <td>
                                    <label class="list-group-item tb_checbox">
                                        {{-- <input class="form-check-input me-1" type="checkbox" value=""> --}}
                                        {{ $index }}
                                    </label>
                                </td>
                                <td>

                                    <a href="#"  data-url="{{ route('to-do.show', $todo->id) }}"
                                        data-size="xl" data-ajax-popup="true" data-title="{{ __(' View Tasks') }}"
                                        title="{{ __('View Task') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                        {{ $todo->title }}
                                    </a>
                                </td>

                                <td>{{ $todo->date }}</td>

                                <td>{{ $todo->status ?? '' }}

                                    @if ($todo->status === 'Not Started Yet')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M8.8 12.8C9.22435 12.8 9.63131 12.9686 9.93137 13.2686C10.2314 13.5687 10.4 13.9757 10.4 14.4C10.4 14.8243 10.2314 15.2313 9.93137 15.5314C9.63131 15.8314 9.22435 16 8.8 16C8.37565 16 7.96869 15.8314 7.66863 15.5314C7.36857 15.2313 7.2 14.8243 7.2 14.4C7.2 13.9757 7.36857 13.5687 7.66863 13.2686C7.96869 12.9686 8.37565 12.8 8.8 12.8ZM3.7928 10.4C4.32323 10.4 4.83194 10.6107 5.20701 10.9858C5.58209 11.3609 5.7928 11.8696 5.7928 12.4C5.7928 12.9304 5.58209 13.4391 5.20701 13.8142C4.83194 14.1893 4.32323 14.4 3.7928 14.4C3.26237 14.4 2.75366 14.1893 2.37859 13.8142C2.00351 13.4391 1.7928 12.9304 1.7928 12.4C1.7928 11.8696 2.00351 11.3609 2.37859 10.9858C2.75366 10.6107 3.26237 10.4 3.7928 10.4ZM13.0552 10.8C13.4795 10.8 13.8865 10.9686 14.1866 11.2686C14.4866 11.5687 14.6552 11.9757 14.6552 12.4C14.6552 12.8243 14.4866 13.2313 14.1866 13.5314C13.8865 13.8314 13.4795 14 13.0552 14C12.6309 14 12.2239 13.8314 11.9238 13.5314C11.6238 13.2313 11.4552 12.8243 11.4552 12.4C11.4552 11.9757 11.6238 11.5687 11.9238 11.2686C12.2239 10.9686 12.6309 10.8 13.0552 10.8ZM14.8 7.4552C15.1183 7.4552 15.4235 7.58163 15.6485 7.80667C15.8736 8.03172 16 8.33694 16 8.6552C16 8.97346 15.8736 9.27868 15.6485 9.50373C15.4235 9.72877 15.1183 9.8552 14.8 9.8552C14.4817 9.8552 14.1765 9.72877 13.9515 9.50373C13.7264 9.27868 13.6 8.97346 13.6 8.6552C13.6 8.33694 13.7264 8.03172 13.9515 7.80667C14.1765 7.58163 14.4817 7.4552 14.8 7.4552ZM2 4.8C2.53043 4.8 3.03914 5.01071 3.41421 5.38579C3.78929 5.76086 4 6.26957 4 6.8C4 7.33043 3.78929 7.83914 3.41421 8.21421C3.03914 8.58929 2.53043 8.8 2 8.8C1.46957 8.8 0.960859 8.58929 0.585786 8.21421C0.210714 7.83914 0 7.33043 0 6.8C0 6.26957 0.210714 5.76086 0.585786 5.38579C0.960859 5.01071 1.46957 4.8 2 4.8ZM14.2288 4.1656C14.441 4.1656 14.6445 4.24989 14.7945 4.39991C14.9445 4.54994 15.0288 4.75343 15.0288 4.9656C15.0288 5.17777 14.9445 5.38126 14.7945 5.53129C14.6445 5.68131 14.441 5.7656 14.2288 5.7656C14.0166 5.7656 13.8131 5.68131 13.6631 5.53129C13.5131 5.38126 13.4288 5.17777 13.4288 4.9656C13.4288 4.75343 13.5131 4.54994 13.6631 4.39991C13.8131 4.24989 14.0166 4.1656 14.2288 4.1656ZM6.4 0C7.03652 0 7.64697 0.252856 8.09706 0.702944C8.54714 1.15303 8.8 1.76348 8.8 2.4C8.8 3.03652 8.54714 3.64697 8.09706 4.09706C7.64697 4.54714 7.03652 4.8 6.4 4.8C5.76348 4.8 5.15303 4.54714 4.70294 4.09706C4.25286 3.64697 4 3.03652 4 2.4C4 1.76348 4.25286 1.15303 4.70294 0.702944C5.15303 0.252856 5.76348 0 6.4 0ZM12.4 2.4C12.5061 2.4 12.6078 2.44214 12.6828 2.51716C12.7579 2.59217 12.8 2.69391 12.8 2.8C12.8 2.90609 12.7579 3.00783 12.6828 3.08284C12.6078 3.15786 12.5061 3.2 12.4 3.2C12.2939 3.2 12.1922 3.15786 12.1172 3.08284C12.0421 3.00783 12 2.90609 12 2.8C12 2.69391 12.0421 2.59217 12.1172 2.51716C12.1922 2.44214 12.2939 2.4 12.4 2.4Z"
                                                fill="#27ADCA" />
                                        </svg>
                                    @elseif ($todo->status === 'Incomplete')
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
                                    @elseif ($todo->status === 'In Progress')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M5.22449 11.6735L0.326531 6.77551C-0.108844 6.34014 -0.108844 5.65986 0.326531 5.22449L5.22449 0.326531C5.65986 -0.108844 6.34014 -0.108844 6.77551 0.326531L11.6735 5.22449C12.1088 5.65986 12.1088 6.34014 11.6735 6.77551L6.77551 11.6735C6.34014 12.1088 5.63265 12.1088 5.22449 11.6735Z"
                                                fill="#4CAF50" />
                                            <path d="M6.03657 8.59999L4.14771 6.33334H7.92544L6.03657 8.59999Z"
                                                fill="#FFEB3B" />
                                            <path d="M5.49695 3.58099H6.5763V6.90001H5.49695V3.58099Z" fill="#FFEB3B" />
                                        </svg>
                                    @elseif ($todo->status === 'Completed')
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
                                    @endif
                                </td>
                                <td>{{ $todo->priority ?? '' }}
                                    @if ($todo->priority === 'High')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M5.22449 11.6735L0.326531 6.77551C-0.108844 6.34014 -0.108844 5.65986 0.326531 5.22449L5.22449 0.326531C5.65986 -0.108844 6.34014 -0.108844 6.77551 0.326531L11.6735 5.22449C12.1088 5.65986 12.1088 6.34014 11.6735 6.77551L6.77551 11.6735C6.34014 12.1088 5.63265 12.1088 5.22449 11.6735Z"
                                                fill="#F44336" />
                                            <path
                                                d="M5.3335 8.38096C5.3335 8.29933 5.36071 8.2177 5.38792 8.13606C5.41513 8.05443 5.46955 8.00001 5.52397 7.94559C5.57839 7.89116 5.66003 7.83674 5.74166 7.80953C5.82329 7.78232 5.90492 7.75511 6.01377 7.75511C6.12261 7.75511 6.20424 7.78232 6.28588 7.80953C6.36751 7.83674 6.44914 7.89116 6.50356 7.94559C6.55799 8.00001 6.61241 8.05443 6.63962 8.13606C6.66683 8.2177 6.69404 8.29933 6.69404 8.38096C6.69404 8.46259 6.66683 8.54423 6.63962 8.62586C6.61241 8.70749 6.55799 8.76191 6.50356 8.81633C6.44914 8.87076 6.36751 8.92518 6.28588 8.95239C6.20424 8.9796 6.12261 9.00681 6.01377 9.00681C5.90492 9.00681 5.82329 8.9796 5.74166 8.95239C5.66003 8.92518 5.6056 8.87076 5.52397 8.81633C5.46955 8.76191 5.41513 8.70749 5.38792 8.62586C5.36071 8.54423 5.3335 8.4898 5.3335 8.38096ZM6.47635 7.12926H5.49676L5.36071 3.02042H6.61241L6.47635 7.12926Z"
                                                fill="white" />
                                        </svg>
                                    @elseif ($todo->priority === 'Low')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M5.22449 11.6735L0.326531 6.77551C-0.108844 6.34014 -0.108844 5.65986 0.326531 5.22449L5.22449 0.326531C5.65986 -0.108844 6.34014 -0.108844 6.77551 0.326531L11.6735 5.22449C12.1088 5.65986 12.1088 6.34014 11.6735 6.77551L6.77551 11.6735C6.34014 12.1088 5.63265 12.1088 5.22449 11.6735Z"
                                                fill="#FFC107" />
                                            <path
                                                d="M5.98636 6.55783C6.28692 6.55783 6.53057 6.31417 6.53057 6.01361C6.53057 5.71305 6.28692 5.46939 5.98636 5.46939C5.68579 5.46939 5.44214 5.71305 5.44214 6.01361C5.44214 6.31417 5.68579 6.55783 5.98636 6.55783Z"
                                                fill="#37474F" />
                                            <path
                                                d="M8.16324 6.55783C8.4638 6.55783 8.70745 6.31417 8.70745 6.01361C8.70745 5.71305 8.4638 5.46939 8.16324 5.46939C7.86267 5.46939 7.61902 5.71305 7.61902 6.01361C7.61902 6.31417 7.86267 6.55783 8.16324 6.55783Z"
                                                fill="#37474F" />
                                            <path
                                                d="M3.8096 6.55783C4.11016 6.55783 4.35382 6.31417 4.35382 6.01361C4.35382 5.71305 4.11016 5.46939 3.8096 5.46939C3.50904 5.46939 3.26538 5.71305 3.26538 6.01361C3.26538 6.31417 3.50904 6.55783 3.8096 6.55783Z"
                                                fill="#37474F" />
                                        </svg>
                                    @elseif ($todo->priority === 'Medium')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M5.22449 11.6735L0.326531 6.77551C-0.108844 6.34014 -0.108844 5.65986 0.326531 5.22449L5.22449 0.326531C5.65986 -0.108844 6.34014 -0.108844 6.77551 0.326531L11.6735 5.22449C12.1088 5.65986 12.1088 6.34014 11.6735 6.77551L6.77551 11.6735C6.34014 12.1088 5.63265 12.1088 5.22449 11.6735Z"
                                                fill="#4CAF50" />
                                            <path d="M6.03657 8.59999L4.14771 6.33334H7.92544L6.03657 8.59999Z"
                                                fill="#FFEB3B" />
                                            <path d="M5.49695 3.58099H6.5763V6.90001H5.49695V3.58099Z" fill="#FFEB3B" />
                                        </svg>
                                    @elseif ($todo->priority === 'none')
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cases.show', $todo->associatedCase->id) }}">
                                        {{ !empty($todo->associatedCase->name) ? $todo->associatedCase->name : ' ' }}
                                    </a>
                                </td>
                                <td> {{ $todo->createdByUser->name }} </td>

                                <td>

                                    <div class="d-flex justify-content-start align-items-center" style="gap: 0.5rem">
                                        @can('edit tasks')
                                            <button class="btn  tsk_edit_btn">
                                                <a href="#" class=" align-items-center "
                                                    data-url="{{ route('to-do.edit', $todo->id) }}"
                                                    data-size="xl addTaskModal_wrapper" data-ajax-popup="true"
                                                    data-title="{{ __('Edit Tasks') }}" title="{{ __('Edit Task') }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                        viewBox="0 0 11 11" fill="none">
                                                        <path
                                                            d="M7.60389 2.27461L8.72521 3.39541M8.32496 1.28723L5.29294 4.31925C5.13628 4.4757 5.02944 4.67502 4.98588 4.89209L4.70581 6.29401L6.10773 6.01342C6.32479 5.97 6.52386 5.86359 6.68057 5.70688L9.71259 2.67486C9.8037 2.58375 9.87598 2.47558 9.92528 2.35653C9.97459 2.23749 9.99997 2.1099 9.99997 1.98105C9.99997 1.85219 9.97459 1.7246 9.92528 1.60556C9.87598 1.48651 9.8037 1.37835 9.71259 1.28723C9.62148 1.19612 9.51331 1.12385 9.39426 1.07454C9.27522 1.02523 9.14763 0.999847 9.01878 0.999847C8.88992 0.999847 8.76233 1.02523 8.64329 1.07454C8.52424 1.12385 8.41608 1.19612 8.32496 1.28723Z"
                                                            stroke="#27ADCA" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M8.94126 7.35285V8.94113C8.94126 9.22195 8.8297 9.49127 8.63113 9.68985C8.43256 9.88842 8.16323 9.99998 7.88241 9.99998H2.05873C1.7779 9.99998 1.50858 9.88842 1.31001 9.68985C1.11144 9.49127 0.999878 9.22195 0.999878 8.94113V3.11744C0.999878 2.83662 1.11144 2.5673 1.31001 2.36872C1.50858 2.17015 1.7779 2.05859 2.05873 2.05859H3.64701"
                                                            stroke="#27ADCA" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </a>
                                            </button>
                                        @endcan

                                        @can('view tasks')
                                            <button class="btn tsk_view_btn">
                                                <a href="#" id="taskLink{{ $todo->id }}"
                                                    class="align-items-center "
                                                    data-url="{{ route('to-do.show', $todo->id) }}"
                                                    data-size="xl addTaskModal_wrapper" data-ajax-popup="true"
                                                    data-title="{{ __(' View Tasks') }}" title="{{ __('View Task') }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                        viewBox="0 0 13 9" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M3.77783 4.25C3.77783 3.62379 4.02659 3.02323 4.46939 2.58044C4.91218 2.13764 5.51274 1.88889 6.13894 1.88889C6.76515 1.88889 7.36571 2.13764 7.8085 2.58044C8.2513 3.02323 8.50005 3.62379 8.50005 4.25C8.50005 4.8762 8.2513 5.47676 7.8085 5.91955C7.36571 6.36235 6.76515 6.61111 6.13894 6.61111C5.51274 6.61111 4.91218 6.36235 4.46939 5.91955C4.02659 5.47676 3.77783 4.8762 3.77783 4.25ZM6.13894 2.83333C5.76322 2.83333 5.40289 2.98259 5.13721 3.24826C4.87153 3.51394 4.72228 3.87427 4.72228 4.25C4.72228 4.62572 4.87153 4.98605 5.13721 5.25173C5.40289 5.51741 5.76322 5.66666 6.13894 5.66666C6.51467 5.66666 6.875 5.51741 7.14068 5.25173C7.40635 4.98605 7.55561 4.62572 7.55561 4.25C7.55561 3.87427 7.40635 3.51394 7.14068 3.24826C6.875 2.98259 6.51467 2.83333 6.13894 2.83333Z"
                                                            fill="#8DC313" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M1.30522 3.39748C1.04141 3.77778 0.944444 4.07559 0.944444 4.25C0.944444 4.42441 1.04141 4.72222 1.30522 5.10252C1.56085 5.46959 1.93989 5.86815 2.41967 6.23648C3.38111 6.97441 4.69893 7.55556 6.13889 7.55556C7.57885 7.55556 8.89667 6.97441 9.85811 6.23648C10.3379 5.86815 10.7169 5.46959 10.9726 5.10252C11.2364 4.72222 11.3333 4.42441 11.3333 4.25C11.3333 4.07559 11.2364 3.77778 10.9726 3.39748C10.7169 3.03041 10.3379 2.63185 9.85811 2.26352C8.89667 1.52559 7.57885 0.944444 6.13889 0.944444C4.69893 0.944444 3.38111 1.52559 2.41967 2.26352C1.93989 2.63185 1.56085 3.03041 1.30522 3.39748ZM1.84419 1.51426C2.93407 0.678111 4.44896 0 6.13889 0C7.82881 0 9.3437 0.678111 10.433 1.51426C10.9789 1.93296 11.4297 2.40015 11.7483 2.85915C12.058 3.30556 12.2778 3.79478 12.2778 4.25C12.2778 4.70522 12.0574 5.19444 11.7483 5.64085C11.4297 6.09985 10.9789 6.56641 10.4336 6.98574C9.34433 7.82189 7.82881 8.5 6.13889 8.5C4.44896 8.5 2.93407 7.82189 1.84481 6.98574C1.29893 6.56704 0.848111 6.09985 0.529519 5.64085C0.22037 5.19444 0 4.70522 0 4.25C0 3.79478 0.22037 3.30556 0.529519 2.85915C0.848111 2.40015 1.29893 1.93359 1.84419 1.51426Z"
                                                            fill="#8DC313" />
                                                    </svg>
                                                </a>
                                            </button>
                                        @endcan
                                        @can('delete tasks')
                                            <button class="btn  tsk_delete_btn">
                                                <a href="#" class=" align-items-center bs-pass-para"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-confirm-yes="delete-form-{{ $todo['id'] }}"
                                                    title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                                        viewBox="0 0 11 12" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M6.85586 0.905365H5.00306C4.88411 0.905365 4.7805 0.905365 4.68293 0.920714C4.49287 0.951161 4.31258 1.02567 4.15648 1.13827C4.00037 1.25088 3.87279 1.39845 3.78394 1.56919C3.73789 1.6569 3.70555 1.75502 3.66773 1.8674L3.60688 2.05103C3.5649 2.20022 3.47358 2.33078 3.34784 2.42138C3.2221 2.51197 3.06935 2.55727 2.91455 2.54986H1.27005C1.16101 2.54986 1.05644 2.59318 0.979336 2.67028C0.902235 2.74738 0.858921 2.85195 0.858921 2.96099C0.858921 3.07003 0.902235 3.1746 0.979336 3.2517C1.05644 3.3288 1.16101 3.37211 1.27005 3.37211H10.5889C10.6979 3.37211 10.8025 3.3288 10.8796 3.2517C10.9567 3.1746 11 3.07003 11 2.96099C11 2.85195 10.9567 2.74738 10.8796 2.67028C10.8025 2.59318 10.6979 2.54986 10.5889 2.54986H8.89504C8.74862 2.54635 8.60718 2.49602 8.49147 2.40625C8.37575 2.31647 8.29184 2.19198 8.25204 2.05103L8.19065 1.8674C8.15337 1.75502 8.12103 1.6569 8.07553 1.56919C7.98662 1.39839 7.85896 1.25077 7.70275 1.13816C7.54655 1.02555 7.36614 0.95108 7.17599 0.920714C7.07842 0.905365 6.97481 0.905365 6.85641 0.905365H6.85586ZM7.56957 2.54986C7.53108 2.47448 7.499 2.396 7.47365 2.31525L7.41883 2.1508C7.36895 2.00115 7.35743 1.971 7.34592 1.94907C7.31633 1.89207 7.27379 1.84279 7.22172 1.80519C7.16965 1.76759 7.10949 1.74271 7.04608 1.73255C6.97462 1.72651 6.90285 1.72487 6.83119 1.72761H5.02773C4.86985 1.72761 4.83696 1.72871 4.81285 1.7331C4.74948 1.7432 4.68936 1.76799 4.63729 1.8055C4.58522 1.843 4.54266 1.89217 4.513 1.94907C4.50149 1.971 4.48997 2.00115 4.44009 2.15135L4.38528 2.3158L4.3639 2.37719C4.34252 2.43749 4.3173 2.4945 4.28935 2.54986H7.56957Z"
                                                            fill="#E94511" />
                                                        <path
                                                            d="M9.26503 4.30398C9.2723 4.19516 9.3225 4.09368 9.40458 4.02188C9.48667 3.95007 9.59392 3.91381 9.70274 3.92108C9.81156 3.92835 9.91303 3.97855 9.98484 4.06064C10.0566 4.14272 10.0929 4.24997 10.0856 4.35879L9.83128 8.16965C9.78469 8.87239 9.74687 9.4403 9.65806 9.8865C9.56542 10.3497 9.40865 10.7367 9.08413 11.0398C8.76017 11.3435 8.36329 11.4745 7.89461 11.5354C7.44402 11.5946 6.87502 11.5946 6.17008 11.5946H5.68824C4.98385 11.5946 4.4143 11.5946 3.96371 11.5354C3.49558 11.4745 3.0987 11.3435 2.77419 11.0398C2.45022 10.7367 2.29345 10.3492 2.20081 9.8865C2.112 9.44084 2.07473 8.87239 2.02759 8.16965L1.77324 4.35879C1.76597 4.24997 1.80223 4.14272 1.87403 4.06064C1.94584 3.97855 2.04731 3.92835 2.15613 3.92108C2.26495 3.91381 2.3722 3.95007 2.45429 4.02188C2.53637 4.09368 2.58657 4.19516 2.59384 4.30398L2.846 8.08632C2.89533 8.8247 2.93042 9.33888 3.00716 9.72534C3.08226 10.1008 3.18641 10.2993 3.33606 10.4396C3.48626 10.5799 3.69127 10.6709 4.0706 10.7203C4.46145 10.7712 4.97672 10.7723 5.71729 10.7723H6.14158C6.8816 10.7723 7.39688 10.7712 7.78827 10.7203C8.1676 10.6709 8.37261 10.5799 8.52281 10.4396C8.67246 10.2993 8.77661 10.1008 8.85171 9.72589C8.92845 9.33888 8.96354 8.8247 9.01287 8.08578L9.26503 4.30398Z"
                                                            fill="#E94511" />
                                                        <path
                                                            d="M7.34095 5.29288C7.2325 5.28201 7.12417 5.31465 7.03978 5.38363C6.95539 5.4526 6.90184 5.55226 6.8909 5.6607L6.61682 8.40154C6.60879 8.50847 6.64286 8.61431 6.71177 8.69648C6.78068 8.77865 6.87896 8.83065 6.98566 8.84138C7.09236 8.8521 7.19903 8.82072 7.28292 8.75391C7.3668 8.6871 7.42126 8.59016 7.43468 8.48376L7.70877 5.74293C7.71963 5.63448 7.68699 5.52615 7.61802 5.44176C7.54905 5.35737 7.44939 5.30382 7.34095 5.29288ZM4.51789 5.29288C4.40955 5.30382 4.30998 5.3573 4.24102 5.44157C4.17207 5.52584 4.13935 5.63402 4.15007 5.74238L4.42415 8.48321C4.43774 8.58941 4.49223 8.68611 4.57601 8.75276C4.6598 8.81941 4.76627 8.85074 4.8728 8.8401C4.97933 8.82946 5.07751 8.77769 5.14646 8.69579C5.21541 8.61389 5.24969 8.50832 5.24202 8.40154L4.96793 5.6607C4.95699 5.55237 4.90352 5.45279 4.81925 5.38384C4.73498 5.31488 4.62625 5.28217 4.51789 5.29288Z"
                                                            fill="#E94511" />
                                                    </svg>
                                                </a>
                                            </button>
                                        @endcan
                                    </div>
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => ['tasks.destroy', $todo->id],
                                        'id' => 'delete-form-' . $todo->id,
                                    ]) !!}
                                    {!! Form::close() !!}


                                </td>
                            </tr>
                            <tr>
                                <td style="display: none"></td>
                                <td style="display: none"></td>
                                <td style="display: none"></td>
                                <td style="display: none"></td>
                                <td style="display: none"></td>
                                <td style="display: none"></td>
                                <td style="display: none"></td>
                                <td style="display: none"></td>

                                <td colspan="9" class="p-0" style="border: none">
                                    <div id="collapseOne-{{ $index }}" class="collapse collapse_table_wrapper"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <table class="table collapse_table table-borderless">
                                            <thead>
                                                <tr class="">
                                                    <th class="p-0 px-3">
                                                        <div class="d-flex justify-content-start align-items-center gap-1">
                                                            <p class="mb-0">
                                                                {{ $todo->title }}
                                                            </p>
                                                            <button class="btn p-0">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                    height="11" viewBox="0 0 12 11" fill="none">
                                                                    <path
                                                                        d="M0 10.1538H12V11H0V10.1538ZM10.0286 2.96154C10.3714 2.62308 10.3714 2.11538 10.0286 1.77692L8.48571 0.253846C8.14286 -0.0846154 7.62857 -0.0846154 7.28571 0.253846L0.857143 6.6V9.30769H3.6L10.0286 2.96154ZM7.88571 0.846154L9.42857 2.36923L8.14286 3.63846L6.6 2.11538L7.88571 0.846154ZM1.71429 8.46154V6.93846L6 2.70769L7.54286 4.23077L3.25714 8.46154H1.71429Z"
                                                                        fill="#AFAFAF" fill-opacity="0.52" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </th>
                                                    <th class="p-0 px-3">Assignees: </th>
                                                    <th class="p-0 px-3">History:</th>
                                                    <th class="p-0 px-3"></th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                <tr>
                                                    <td>
             
                                                        <div class="tsk_table_case_checkbox custom-scroll">
                                                            <div class=" tsk_model_list card-body">
                                                                @if ($todo->tasks->isEmpty())
                                                                    <p>No tasks found.</p>
                                                                @else
                                                                    <ul class="p-0 mb-0" style="list-style-type: none">
                                                                        @foreach ($todo->tasks as $task)
                                                                            <li class="mb-2" style="    white-space: normal;max-width: 250px;">
                                                                                <label class="list-group-item tb_checbox">
                                                                                    <input
                                                                                        class="form-check-input me-1 task-checkbox2"
                                                                                        type="checkbox"
                                                                                        data-task-id="{{ $task->id }}"
                                                                                        {{ $task->status == 1 ? 'checked' : '' }}>
                                                                                    {{ $task->title }}
                                                                                </label>
                                                                                @if (!$task->subtasks->isEmpty())
                                                                                    <ul class="mt-2"
                                                                                        style="list-style-type: none">
                                                                                        @foreach ($task->subtasks as $subtask)
                                                                                            <li style="    white-space: normal;max-width: 250px;">
                                                                                                <label
                                                                                                    class="list-group-item tb_checbox">
                                                                                                    <input
                                                                                                        class="form-check-input me-1 subtask-checkbox2"
                                                                                                        type="checkbox"
                                                                                                        data-task-id="{{ $task->id }}"
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
                                                    </td>
                                                    <td>
                                                        <div style="height: 130px" class="tsk_table_case_assigness custom-scroll">
                                                            @php
                                                                $taskTeamArray = explode(',', $todo->task_team);
                                                                
                                                            @endphp
                                                            @foreach ($taskTeamArray as $team)
                                                                @if (!empty($team))
                                                                    <button class="btn btn_assignes"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#confirmationModal"
                                                                    data-task-id="{{ $todo->id }}"
                                                                    data-user-id="{{ $team }}">

                                                                        {{ UsersNameById($team) }}
                                                                        <span class="btn_cross">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="11" height="11"
                                                                                viewBox="0 0 11 11" fill="none">
                                                                                <circle cx="5.5" cy="5.5"
                                                                                    r="5.5" fill="#AFAFAF" />
                                                                                <path
                                                                                    d="M8 7.15166L6.34894 5.5003L7.9994 3.84924L7.15106 3L5.4997 4.65106L3.84864 3L3 3.84924L4.65046 5.5003L3 7.15136L3.84924 8L5.4997 6.34894L7.15016 8L8 7.15166Z"
                                                                                    fill="white" />
                                                                            </svg>
                                                                        </span>
                                                                    </button>
                                                                @else
                                                                    <p>No Assignees</p>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="tsk_table_case_edit_history custom-scroll">
                                                            <ul class="p-0 mb-0" style="list-style-type: none">
                                                                <li class="mb-2">
                                                                    @foreach ($todo->tasks as $task)
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
                                                    </td>
                                                    <td>
                                                        <div class="tsk_table_case_show_history custom-scroll">
                                                            <ul class="mb-0" style="white-space: normal;">
                                                                <li class="mb-2">
                                                                    <p class="mb-0 tsk_table_case_show_history_click">Not
                                                                        Selected</p>

                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>





        </div>
    </section>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove this Assignee?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemove">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .btn_show_history.active {
            font-weight: bold;
        }
    </style>

@endsection
@push('custom-script')
    <script>
        $(document).ready(function() {

            // Handle the checkbox change event
            $('.task-checkbox2').change(function() {
                var checkbox = $(this);
                var isChecked = checkbox.is(':checked') ? 1 : 0;
                var taskId = checkbox.data('task-id');
                var subtaskId = checkbox.data('subtask-id');

                if (isChecked) {
                    $('.subtask-checkbox2[data-task-id="' + taskId + '"]').prop('checked', true);
                } else {
                    $('.subtask-checkbox2[data-task-id="' + taskId + '"]').prop('checked', false);
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
                       
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });

            $('.subtask-checkbox2').change(function() {
                var checkbox = $(this);
                var isChecked = checkbox.is(':checked') ? 1 : 0;
                var taskId = checkbox.data('task-id');
                var subtaskId = checkbox.data('subtask-id');

                var allSubtasksChecked = $('.subtask-checkbox2[data-task-id="' + taskId + '"]').length ===
                    $('.subtask-checkbox2[data-task-id="' + taskId + '"]:checked').length;

                if (allSubtasksChecked) {
                    $('.task-checkbox2[data-task-id="' + taskId + '"]').prop('checked', true);
                } else {

                    $('.task-checkbox2[data-task-id="' + taskId + '"]').prop('checked', false);
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
                       
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });

            // Simulate a click on the first button when the page is loaded
            $('.btn_show_history:first').trigger('click');

            $('.btn_show_history').click(function() {
                // Remove the "active" class from all buttons to reset their styles
                $('.btn_show_history').removeClass('active');

                // Add the "active" class to the clicked button
                $(this).addClass('active');

                var title = $(this).data('title');
                $('.tsk_table_case_show_history_click').text(title);
            });
        });

        $(document).ready(function() {
            // var taskId = {{ $taskId ?? 'null' }};
            var taskId = $('#taskId').val();
            if (taskId !== null) {
                // Construct the ID of the anchor tag
                var anchorId = '#taskLink' + taskId;
                $(anchorId).click();
            }



            var taskIdToRemove;
            var userIdToRemove;

            // When the modal is shown, store the task_id and user_id
            $('#confirmationModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                taskIdToRemove = button.data('task-id');
                userIdToRemove = button.data('user-id');
            });

            // When the confirmation button is clicked, make the AJAX request
            $('#confirmRemove').on('click', function() {


                $.ajax({
                    type: "POST",
                    url: "{{ route('task.remove.assignee') }}",
                    data: {
                        task_id: taskIdToRemove,
                        user_id: userIdToRemove
                    },
                    success: function(response) {

                        $('#confirmationModal').modal('hide');
                        show_toastr('{{ __('Success') }}',
                            'You successfully Remove this Assignee', 'success')
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    },
                    error: function(error) {
                        $('#confirmationModal').modal('hide');
                        show_toastr('{{ __('Error') }}', 'Error remove this Assignee',
                            'error')
                    }
                });
            });

        });
    </script>
@endpush
