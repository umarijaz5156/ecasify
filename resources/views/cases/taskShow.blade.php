<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Task Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="text-center mb-0 flex-grow-1">{{ $taskData->title }}</h4>
                <p class="text-muted mb-0">{{ $taskData->date }}</p>
            </div>
        </div>
        
        <div class="col-md-12 mt-4">
            <!-- Task Title -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title ms-2">Task Title</h5>
                    </div>
                   
                    {{-- <div class="task-title-history mt-3">
                        @foreach($taskData->taskLogs as $log)
                            <div class="task-change">
                                <div class="change-title">
                                    {!! $log->task_title !!}
                                </div>
                                <div class="change-details">
                                    <span class="change-user">{{ $log->user->name }}</span>
                                    <span class="change-time text-muted small">({{ $log->created_at->diffForHumans() }})</span>
                                </div>
                            </div>
                        @endforeach
                    </div> --}}
                    <div class="task-title-history mt-3">
                        <div class="task-change">
                        @foreach($lineData as $lineInfo)
                           
                                <div class="line-container mt-2">
                                    <div class="line-content">
                                        <p class="line-paragraph">
                                            {!! $lineInfo['line'] !!}
                                        </p>
                                    </div>
                                    <div class="user-info">
                                        <span class="change-user">{{ $lineInfo['user'] }}</span>
                                        <span class="change-time text-muted small">{{ $lineInfo['time'] }}</span>
                                    </div>
                                </div>
                            
                        @endforeach
                    </div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .task-change {
    margin-bottom: 15px;
    background-color: #f7f7f7;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    }

    .line-container {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .line-content {
        flex-grow: 1;
    }

    .line-paragraph {
        margin: 0;
        font-size: 16px;
        line-height: 1.4;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        color: #777;
        font-size: 14px;
    }

    .change-user {
        font-weight: bold;
    }


</style>