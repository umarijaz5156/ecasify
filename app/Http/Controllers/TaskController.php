<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Models\TaskData;
use App\Models\Cases;
use App\Models\group;
use App\Models\subTask;
use App\Models\SubTaskLog;
use App\Models\TaskLog;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Traits\TaskTrait;

class TaskController extends Controller
{
    use TaskTrait;

    public function index(Request $request)
    {
       
        if (Auth::user()->can('manage tasks')) {

            $today = Carbon::today();

            $taskId = $request->input('task_id') ?? '';

            if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin' || (Auth::user()->type !== 'client' && Utility::getValByName('viewCases') === 'all')) {
                $user = Auth::user();
                $userIds = $user->coAdminIds();
                $userId[] = intval($user->creatorId());

                $todos = TaskData::with(['associatedCase', 'createdByUser', 'tasks'])
                ->where('deleted_at',null)
                ->where(function ($query) use ($userId) {
                        $query->whereHas('associatedCase', function ($caseQuery) use ($userId) {
                            $caseQuery->whereIn('created_by', $userId);
                        })
                            ->orWhereIn('created_by', $userId);
                    })
                    ->orderBy('id', 'desc')
                    ->get();

            } else {
                $user = Auth::user()->id;

                $todos = TaskData::with(['tasks', 'associatedCase', 'createdByUser'])
                ->where('deleted_at',null)
                    ->where(function ($query) use ($user) {
                        $query->whereRaw("find_in_set('" . $user . "', (select your_team from cases where id = task_data.cases_id))")
                            ->orWhereRaw("find_in_set('" . $user . "', (select your_advocates from cases where id = task_data.cases_id))");
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

            }



            return view('todo.index', compact('todos', 'taskId'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function taskStore(Request $request)
    {

        $requestData = $request->all();
      

        $inputDateString = $requestData['date'];
        $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

        if (preg_match($dateFormatRegex, $inputDateString)) {
            $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
            $formattedDate = $dateObject->format('Y-m-d');
            $convertedDate_openDate = $formattedDate;
        } else {
            $convertedDate_openDate = $inputDateString;
        }

        $taskData = TaskData::create([
            'cases_id' => $request->case_id,
            'title' => $requestData['title'],
            'description' => $requestData['description'],
            'date' => $convertedDate_openDate,
            'status' => $requestData['status'],
            'priority' => $requestData['priority'],
            'task_team' => isset($requestData['task_team']) ? implode(',', $requestData['task_team']) : null,
            'created_by' => Auth::user()->id,
        ]);


        if (isset($requestData['task'])) {

            foreach ($requestData['task'] as $task) {


                $taskDetails = Task::create([
                    'task_data_id' => $taskData->id,
                    'title' => $task['title'] ?? '-',
                    'status' => 0,
                ]);

                TaskLog::create([
                    'user_id' => Auth::user()->id,
                    'task_id' => $taskDetails->id,
                    'action' => 'New task Created',
                    'task_title' => $task['title'],
                ]);

                // Check if there are subtasks
                if (isset($task['subtask'])) {

                    foreach ($task['subtask'] as  $subtaskTitle) {


                        $subTask =  subTask::create([
                            'task_id' => $taskDetails->id,
                            'title' => $subtaskTitle,
                            'status' => 0,
                        ]);

                        SubTaskLog::create([
                            'user_id' => Auth::user()->id,
                            'task_id' => $taskDetails->id,
                            'action' => 'New Subtask Created',
                            'task_title' => $subtaskTitle,
                            'subtask_id' => $subTask->id,
                        ]);
                    }
                }
            }
        }

        Activity::create([
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->creatorId(),
            'target_id' => $taskData->id,
            'target_type' => 'Task',
            'action' => 'Created',
        ]);

        if(isset($requestData['tab_tasks']) ){
            return redirect()->back()->with('success', 'Tasks successfully created.');
        }else{
            return redirect()->route('cases.show', ['case' => $request->case_id, 'tab' => 'tasks'])
            ->with('success', 'Tasks successfully created.');
        }
        
    }

    public function edit($id)
    {

        $taskData = TaskData::with('tasks')->findOrFail($id);
       
        $relate_to = Cases::where('created_by', Auth::user()->creatorId())->where('id', $taskData->cases_id)->get();

        if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin'  ) {
            $user = Auth::user();
            $userIds = $user->coAdminIds();
            $userIds[] = intval($user->creatorId());

            $cases = Cases::where('id', $taskData->cases_id)->first('id');
   
        } else {

            $user = Auth::user()->id;
            $userIds = [$user];
            $cases = Cases::where('id', $taskData->cases_id)->first('id');
            
        }
        
        $casesAssign = Cases::where('id', $cases->id)->first();
       
        $userIdsInCase = explode(',', $casesAssign->your_advocates);

        $combinedUserIds = array_merge($userIds, $userIdsInCase);
        
        $users = User::whereIn('id', $combinedUserIds)->get();


        
        $allOptions = $users->pluck('name', 'id');

       
        // $cases = Cases::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $your_teams = User::whereIn('id', explode(',', $taskData->task_team))->get();

        $teams = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'client')->orWhere('id',Auth::user()->id)->get()->pluck('name', 'id');

        // $groups = group::where('created_by', Auth::user()->creatorId())->pluck('name', 'members', 'id');
        // $allOptions = $teams->union($groups);
        // $allOptions = $allOptions->sortKeys();

        return view('cases.taskEdit', compact('taskData','relate_to','cases','teams','your_teams','allOptions'));
       
    }

    public function show($id)
    {

        $taskData = TaskData::with('tasks', 'taskLogs')->findOrFail($id);

        $taskTitleLines = preg_split('/\r\n|\r|\n/', $taskData->tasks->title);

        $lineData = [];

        foreach ($taskData->taskLogs as $log) {
            $lines = preg_split('/\r\n|\r|\n/', $log->task_title);
            foreach ($lines as $line) {
                $normalizedLine = str_replace(["\r\n", "\r"], "\n", $line);
                if (!empty($normalizedLine) && in_array($normalizedLine, $taskTitleLines)) {
                    $lineData[] = [
                        'line' => $normalizedLine,
                        'user' => $log->user->name,
                        'time' => $log->created_at->diffForHumans(),
                    ];
                }
            }
        }

        Activity::create([
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->creatorId(),
            'target_id' => $taskData->id,
            'target_type' => 'Task',
            'action' => 'Viewed',
        ]);

        return view('cases.taskShow', compact('taskData', 'lineData'));

        // $taskData = TaskData::with('tasks', 'taskLogs')->findOrFail($id);

        // return view('cases.taskShow', compact('taskData'));
    }

    public function updateTaskStatus(Request $request)
    {
      
        $taskId = $request->input('task_id');
        $subtaskId = $request->input('subtask_id');
        $isChecked = $request->input('is_checked');

        if ($subtaskId) {

            $subtask = SubTask::findOrFail($subtaskId);

            $subtask->update(['status' => $isChecked]);

            $task = Task::where('id', $subtask->task_id)->first();

            $taskDataId = $task->task_data_id;

            $subtasks = subTask::where('task_id', $subtask->task_id)->get();

            $allSubtasksChecked = true; // Assume all subtasks are checked initially

            foreach ($subtasks as $sub) {
                if ($sub->status == 0) {
                    $allSubtasksChecked = false;
                    break;
                }
            }

            if ($allSubtasksChecked) {
                $task->update(['status' => 1]);
            } else {
                $task->update(['status' => 0]);
            }

            Activity::create([
                'user_id' => Auth::user()->id,
                'company_id' => Auth::user()->creatorId(),
                'target_id' => $taskDataId,
                'target_type' => 'Task',
                'action' => 'Status Changed',
            ]);

            return response()->json(['success' => true]);
        }

        // Perform the update based on the data received
        if ($taskId) {
            // Update task status
            $task = Task::findOrFail($taskId);
            $taskDataId = $task->task_data_id;
            $task->update(['status' => $isChecked]);

            if ($isChecked == 1) {
                SubTask::where('task_id', $taskId)->update(['status' => 1]);
            } else {
                SubTask::where('task_id', $taskId)->update(['status' => 0]);
            }

            if ($task->subtasks->isNotEmpty()) {
                $allSubtasksChecked = $task->subtasks->every(function ($subtask) {
                    return $subtask->status == 1;
                });

                // If all subtasks are checked, update the parent task status
                if ($allSubtasksChecked) {
                    $task->update(['status' => 1]);
                } else {
                    $task->update(['status' => 0]);
                }
            }

            Activity::create([
                'user_id' => Auth::user()->id,
                'company_id' => Auth::user()->creatorId(),
                'target_id' => $taskDataId,
                'target_type' => 'Task',
                'action' => 'Status Changed',
            ]);

            return response()->json(['success' => true]);
        }

    
    }

    public function updatePriority(Request $request)
    {


        $taskId = $request->input('taskId');
        $status = $request->input('status');
        if ($status == 'Not') {

            $task = TaskData::findOrFail($taskId);
            $task->update(['status' => 'Not Started Yet']);
        } elseif ($status == 'In') {

            $task = TaskData::findOrFail($taskId);
            $task->update(['status' => 'In Progress']);
        } else {
            $task = TaskData::findOrFail($taskId);
            $task->update(['status' => $status]);
        }


        return response()->json(['success' => true]);
    }


    public function destroy($id)
    {

        $taskData = TaskData::findOrFail($id);
       

        $taskData = TaskData::findOrFail($id);
        $taskData->deleted_at = now();
        $taskData->save();

        Activity::create([
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->creatorId(),
            'target_id' => $taskData->id,
            'target_type' => 'Task',
            'action' => 'Deleted ',
        ]);

        return redirect()->back()
            ->with('success', 'Task data and related tasks have been deleted.');
    }

    public function permanentlyDelete($id)
    {

        $taskData = TaskData::findOrFail($id);

        $taskData->tasks->each(function ($task) {
            $task->subtasks->each(function ($subtask) {
                $subtask->subtaskLogs()->delete();

                $subtask->delete();
            });
            $task->taskLogs()->delete();
            $task->delete();
        });

        $taskData->delete();

        Activity::create([
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->creatorId(),
            'target_id' => $taskData->id,
            'target_type' => 'Task',
            'action' => 'Deleted this ' . $taskData->title,
        ]);

        return redirect()->back()
            ->with('success', 'Task data and related tasks have been deleted.');
    }


    public function update(Request $request, $id)
    {

        $taskData = TaskData::findOrFail($id);

        $inputDateString = $request->input('date');
        $dateFormatRegex = '/^[A-Z][a-z]{2} [A-Z][a-z]{2} \d{1,2} \d{4}$/';

        if (preg_match($dateFormatRegex, $inputDateString)) {
            $dateObject = Carbon::createFromFormat('D M j Y', $inputDateString);
            $formattedDate = $dateObject->format('Y-m-d');
            $convertedDate_openDate = $formattedDate;
        } else {
            $convertedDate_openDate = $inputDateString;
        }

        $taskData->update([
            'cases_id' => $request->case_id,
            'title' => $request['title'],
            'description' => $request['description'],
            'date' => $convertedDate_openDate,
            'status' => $request['status'],
            'priority' => $request['priority'],
            'task_team' => isset($request['task_team']) ? implode(',', $request['task_team']) : null,
            'created_by' => Auth::user()->id,
        ]);

        if (isset($request['task'])) {
            $existingTaskIds = []; 
            $existingSubtaskIds = []; 
            foreach ($request['task'] as $key => $task) {
                // Find the existing task or create a new one if it doesn't exist
                $oldTask = Task::where('id', $key)
                    ->where('task_data_id', $taskData->id)
                    ->first();
       

                if ($oldTask) {

                    if(isset($task['title'])){
                   
                        // Compare existing values with new values
                        $titleChanged = $oldTask->title !== $task['title'] ?? '';
        
                        if ($titleChanged) {
                            // Update the existing task
                            $oldTask->update([
                                'title' => $task['title'],
                            ]);
            
                            // Create a log only if values changed
                            TaskLog::create([
                                'user_id' => Auth::user()->id,
                                'task_id' => $oldTask->id,
                                'action' => 'task title update',
                                'task_title' => $task['title'],
                            ]);
                        }
        
                         $existingTaskIds[] = $oldTask->id;
                     }
                } else {
                      // Create a new task
                        $taskDetails = Task::create([
                            'task_data_id' => $taskData->id,
                            'title' => $task['title'] ?? '',
                            'status' => 0, // Set the initial status as needed
                        ]);

                        $existingTaskIds[] = $taskDetails->id;

                        // Create a log for task creation
                        TaskLog::create([
                            'user_id' => Auth::user()->id,
                            'task_id' => $taskDetails->id,
                            'action' => 'New task Created',
                            'task_title' => $task['title'],
                        ]);
                }

               
                // Handle subtasks within the same loop
                if (isset($task['subtask'])) {
                    // Store the IDs of existing subtasks for this task
                    
                    foreach ($task['subtask'] as $subtaskKey => $subtask) {

                      

                        $subTask = SubTask::where('id', $subtaskKey)
                            ->where('task_id', $oldTask ? $oldTask->id : $taskDetails->id)
                            ->first();

                            
                        if ($subTask) {
                            // Compare existing values with new values
                            $titleChanged = $subTask->title !== $subtask;
                           

                            if ($titleChanged) {
                                // Update the existing subtask
                                $subTask->update([
                                    'title' => $subtask ?? '',
                                ]);
        
                                // Create a log only if values changed
                                SubTaskLog::create([
                                    'user_id' => Auth::user()->id,
                                    'task_id' => $oldTask ? $oldTask->id : $taskDetails->id,
                                    'action' => 'Subtask title update',
                                    'task_title' => $subtask,
                                    'subtask_id' => $subTask->id,
                                ]);
                            }
                            // Add the ID of the existing subtask to the array
                            $existingSubtaskIds[] = $subTask->id;
                          
                        } else {
                            // Create a new subtask
                            $subTask = SubTask::create([
                                'task_id' => $oldTask ? $oldTask->id : $taskDetails->id,
                                'title' => $subtask,
                                'status' => 0, // Set the initial status as needed
                            ]);
                            $existingSubtaskIds[] = $subTask->id;
                            // Create a log for subtask creation
                            SubTaskLog::create([
                                'user_id' => Auth::user()->id,
                                'task_id' => $oldTask ? $oldTask->id : $taskDetails->id,
                                'action' => 'New Subtask Created',
                                'task_title' => $subtask,
                                'subtask_id' => $subTask->id,
                            ]);
                        }
                    }

                   
                   
                }
            }
        
          

            $allTasks = Task::where('task_data_id', $taskData->id)->get();
            foreach ($allTasks as $allTask) {
             
              

            $subTasksToDelete = subTask::where('task_id', $allTask->id)
            ->whereNotIn('id', $existingSubtaskIds)
            ->get(); 
           
           
                foreach ($subTasksToDelete as $subtask) {
                    $subtask->subtaskLogs()->delete(); 
                    $subtask->delete(); 
                }
            }
           
            $tasksToDelete = Task::where('task_data_id', $taskData->id)
            ->whereNotIn('id', $existingTaskIds)
            ->get(); 
           
                foreach ($tasksToDelete as $task) {
                  
                    $task->taskLogs()->delete(); 
                    $task->delete(); 
                }



        }

        Activity::create([
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->creatorId(),
            'target_id' => $taskData->id,
            'target_type' => 'Task',
            'action' => 'Updated',
        ]);

        if(isset($request['tab_tasks']) ){
            return redirect()->back()->with('success', 'Tasks successfully created.');
        }else{
            return redirect()->route('cases.show', ['case' => $request->case_id, 'tab' => 'tasks'])
            ->with('success', 'Tasks successfully created.');
        }
        
    }


    public function removeAssignee(Request $request)
    {


        $taskId = $request->task_id;
        $userIdToRemove = $request->user_id;

        $task = TaskData::find($taskId);

        $teamTaskArray = explode(',', $task->task_team);
        $teamTaskArray = array_filter($teamTaskArray, function ($userId) use ($userIdToRemove) {
            return $userId != $userIdToRemove;
        });



        $modifiedTeamTask = implode(',', $teamTaskArray);
        $task->task_team = $modifiedTeamTask;
        $task->save();

        Activity::create([
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->creatorId(),
            'target_id' => $task->id,
            'target_type' => 'Task',
            'action' => 'Remove Assignee',
        ]);

        return true;
    }


    // task case related  
    public function TaskCreateCase($id){

        if (Auth::user()->can('create tasks')) {

            if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin'  ) {
                $user = Auth::user();
                $userIds = $user->coAdminIds();
                $userIds[] = intval($user->creatorId());

                $cases = Cases::where('id', $id)->first('id');
                   
            } else {
                $user = Auth::user()->id;
                $userIds = [$user->id];
                $cases = Cases::where('id', $id)->first('id');
             
            }
            
            $casesAssign = Cases::where('id', $cases->id)->first();
           
           
            $userIdsInCase = explode(',', $casesAssign->your_advocates);
           

            $combinedUserIds = array_merge($userIds, $userIdsInCase);
            
            $users = User::whereIn('id', $combinedUserIds)->get();


            
            $allOptions = $users->pluck('name', 'id');
            

            $teams = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'client')->orWhere('id',Auth::user()->id)->get()->pluck('name', 'id');
            // $groups = group::where('created_by', Auth::user()->creatorId())->pluck('name', 'members', 'id');
            // $allOptions = $teams->union($groups);
           
            return view('cases.createTask', compact('cases', 'teams','allOptions'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    
}
