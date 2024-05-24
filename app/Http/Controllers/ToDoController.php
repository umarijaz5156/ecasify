<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Cases;
use App\Models\group;
use App\Models\TaskData;
use App\Models\ToDo;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\TaskTrait;

class ToDoController extends Controller
{
    use TaskTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        if (Auth::user()->can('manage tasks')) {

            $today = Carbon::today();

          
            if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin'  ) {
                $user = Auth::user();
                $userIds = $user->coAdminIds();
                $userIds[] = intval($user->creatorId());

                $todos = TaskData::with(['associatedCase', 'createdByUser'])->whereIn('created_by', $userIds)->orderBy('id', 'desc')->get();

            }else{
                $todos = TaskData::with(['associatedCase', 'createdByUser'])->where('created_by', Auth::user()->created_by)->orderBy('id', 'desc')->get();

            }
           

            return view('todo.index', compact('todos'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->can('create tasks')) {

            if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin'  ) {
                $user = Auth::user();
                $userIds = $user->coAdminIds();
                $userIds[] = intval($user->creatorId());

                $cases = Cases::whereIn('created_by', $userIds)->orderBy('id', 'DESC')->get()->pluck('name', 'id');
                   
            } else {
                $user = Auth::user()->id;
                
                $cases = DB::table("cases")
                    ->select("cases.*")
                    ->where(function ($query) use ($user) {
                        $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                            ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                    })
                    ->orderBy('id', 'DESC')
                    ->get()->pluck('name', 'id');

             
            }

           
            $teams = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'client')->orWhere('id',Auth::user()->id)->get()->pluck('name', 'id');
            $groups = group::where('created_by', Auth::user()->creatorId())->pluck('name', 'members', 'id');
            $allOptions = $teams->union($groups);
           
            return view('todo.create', compact('cases', 'teams','allOptions'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      

        if (Auth::user()->can('create tasks')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'description' => 'required|max:140',
                    'due_date' => 'required',
                    'relate_to' => 'required',
                    'assign_to' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $parts = explode('to', $request->due_date);
            $start_date = $parts[0];
            $end_date = $parts[1];

            $todo = new ToDo();
            $todo['description'] = $request->description;
            $todo['due_date'] = $request->due_date;
            $todo['start_date'] = trim($start_date);
            $todo['end_date'] = trim($end_date);
            $todo['relate_to'] = implode(',', $request->relate_to);
            $todo['assign_to'] = implode(',', $request->assign_to);
            $todo['assign_by'] = Auth::user()->id;
            $todo['created_by'] = Auth::user()->creatorId();
            $todo->save();

            $newStart_date = explode(' ', trim($start_date));
            $newEnd_date = explode(' ', trim($end_date));

            if ($request->get('is_check') == '1') {
                $type = 'task';
                $request1 = new ToDo();
                $request1->title = $request->description;
                $request1->start_date = $newStart_date[0];
                $request1->end_date = $newEnd_date[0];
                Utility::addCalendarData($request1, $type);
            }

            return redirect()->route('to-do.index')->with('success', __('Task successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function show($id)
    {
        if($this->isAssignedTask($id)){
            $taskData = TaskData::with('tasks')->findOrFail($id);

            Activity::create([
                'user_id' => Auth::user()->id,
                'company_id' => Auth::user()->creatorId(),
                'target_id' => $id,
                'target_type' => 'Task',
                'action' => 'Viewed',
            ]);

            return view('todo.view', compact('taskData'));
        }else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
  

    public function edit($id)
    {

        if($this->isAssignedTask($id)){
        $taskData = TaskData::with('tasks')->findOrFail($id);
       
        $relate_to = Cases::where('created_by', Auth::user()->creatorId())->where('id', $taskData->cases_id)->get();

        if (Auth::user()->type == 'company' || Auth::user()->type == 'co admin') {
            $user = Auth::user();
            $userIds = $user->coAdminIds();
            $userIds[] = intval($user->creatorId());

            $cases = Cases::whereIn('created_by', $userIds)->orderBy('id', 'DESC')->get()->pluck('name', 'id');
   
        } else {

            $user = Auth::user()->id;
            $cases = DB::table("cases")
                ->select("cases.*")
                ->where(function ($query) use ($user) {
                    $query->whereRaw("find_in_set('" . $user . "', cases.your_team)")
                        ->orWhereRaw("find_in_set('" . $user . "', cases.your_advocates)");
                })
                ->orderBy('id', 'DESC')
                ->get()->pluck('name', 'id');
            
        }

       
        // $cases = Cases::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        
        $your_teams = User::whereIn('id', explode(',', trim($taskData->task_team)))
        ->get();
         
        $teams = User::where('created_by', Auth::user()->creatorId())->where('type', '!=', 'client')->orWhere('id',Auth::user()->id)->get()->pluck('name', 'id');

        $groups = group::where('created_by', Auth::user()->creatorId())->pluck('name', 'members', 'id');
        $allOptions = $teams->union($groups);
        $allOptions = $allOptions->sortKeys();
        
        return view('todo.edit', compact('taskData','relate_to','cases','teams','your_teams','allOptions'));
    }else{
        return redirect()->back()->with('error', __('Permission Denied.'));
    }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       

    
        return redirect()->back()->with('success', 'Task updated successfully.');
    }
    

    public function destroy($id)
    {

        $taskData = TaskData::findOrFail($id);
       

        $taskData->tasks()->delete();

        $taskData->delete();

        return redirect()->back()->with('success', 'Task data and related tasks have been deleted.');
    }

    public function status($id)
    {
        if (Auth::user()->can('edit tasks')) {
            $todo = ToDo::find($id);



            return view('todo.status', compact('todo'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function statusUpdate($id)
    {
        if (Auth::user()->can('edit tasks')) {

            $todo = ToDo::find($id);
            if ($todo->status == 0) {
                return redirect()->route('to-do.index')->with('error', __('This Task already marked as completed.'));
            }

            if ($todo->status == 1) {
                $todo->status = 0;
                $todo->completed_at = date("d-m-y h:i");
                $todo->completed_by = Auth::user()->id;
                $todo->save();
            }
            return redirect()->route('to-do.index')->with('success', __('You have successfully completed the Task.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
