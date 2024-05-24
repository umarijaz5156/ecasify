<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Cases;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimeSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('manage timesheet')) {
            if(Auth::user()->type == 'company'){
                $timesheets = Timesheet::where('created_by',Auth::user()->creatorId())->where('status',1)->orderBy('id', 'DESC')->get();
            }else{
                $timesheets = Timesheet::where('member',Auth::user()->id)->where('status',1)->orderBy('id', 'DESC')->get();
            }
            return view('timesheet.index',compact('timesheets'));

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
        if (Auth::user()->can('manage timesheet')) {
            $cases = Cases::where('created_by',Auth::user()->creatorId())->get()->pluck('name', 'id');
            $members = User::where('created_by',Auth::user()->creatorId())->where('type','!=','advocate')->get()->pluck('name', 'id');

            return view('timesheet.create',compact('cases','members'));

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
        if (Auth::user()->can('create timesheet')) {
            $validator = Validator::make(
                $request->all(), [
                    'case' => 'required',
                    'date' => 'required',
                    'particulars' => 'required',
                    'time' => 'required',
                ]
            );
            if ($request->case != 0) {
                $validator = Validator::make(
                    $request->all(), [
                        'member' => 'required',
                    ]
                );
            }
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $timesheet = new Timesheet();
            $timesheet['case'] = $request->case == 0 ? '' : $request->case ;
            $timesheet['date'] = $request->date;
            $timesheet['particulars'] = $request->particulars;
            $timesheet['time'] = $request->time;
            $timesheet['member'] = $request->member ?? 0;
            $timesheet['created_by'] = Auth::user()->creatorId();
            $timesheet['status'] = $request->case == 0 ? 0 : 1;
            $timesheet->save();
            return redirect()->route('timesheet.index')->with('success', __('Timesheet successfully created.'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }

    }

    public function timeSheetTimer(Request $request)
    {
        if (empty($request->caseSelect) && !is_int(intval($request->caseSelect))) {
            return response()->json(['error' => true]);
        } else {
            if ($request->draftId) {
                $timesheet = Timesheet::find($request->draftId);
            } else {
                $timesheet = new Timesheet();
            }
            $timesheet->case = $request->caseSelect == 0 ? '' : $request->caseSelect;
            $timesheet->date = today();
            $timesheet->particulars = $request->notes;
            $timesheet->time = $request->pausedTime;
            $timesheet->member = Auth::user()->id;
            $timesheet->created_by = Auth::user()->creatorId();
            $timesheet->status = $request->caseSelect == 0 ? 0 : 1;
            $timesheet->save();

           
            if($request->caseSelect == 0){
               
                $action = 'Saved to draft';
            }else{
                $action = 'Saved';
            }
            Activity::create([
                'user_id' => Auth::user()->id,
                'company_id' => Auth::user()->creatorId(),
                'target_type' => 'Timer',
                'target_id' => $timesheet->id,
                'action' => $action,
            ]);
    
            return response()->json(['success' => true]);
        }
    }
    
    public function timeSheetTimerDraft(Request $request)
    {
        if (!empty($request->draftId)) {
            $timesheet = Timesheet::find($request->draftId);
            $timesheet->time = $request->pausedTime ?? $timesheet->time;
            $timesheet->save();
            // return draft value
            return response()->json(['success' => true, 'draftId' => $timesheet->id]);
        } else {
            $timesheet = new Timesheet();
            $timesheet->date = today();
            $timesheet->time = $request->pausedTime ?? null;
            $timesheet->member = Auth::user()->id;
            $timesheet->created_by = Auth::user()->creatorId();
            $timesheet->status = 0;
            $timesheet->save();
            // return draft value
            return response()->json(['success' => true, 'draftId' => $timesheet->id]);
        }
    }
    // deleteDraft
    public function deleteDraft(Request $request)
    {
        if (!empty($request->draftId)) {
            $timesheet = Timesheet::find($request->draftId);
            $timesheet->delete();
            return response()->json(['success' => true]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Auth::user()->can('view timesheet')) {
            $cases = Cases::get()->pluck('name', 'id');
            $members = User::get()->pluck('name', 'id');
            $timesheet = Timesheet::find($id);
            return view('timesheet.view', compact('cases', 'members', 'timesheet'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->can('edit timesheet')) {
            $cases = Cases::where('created_by',Auth::user()->creatorId())->get()->pluck('name', 'id');
            $members = User::where('created_by',Auth::user()->creatorId())->where('type','!=','advocate')->get()->pluck('name', 'id');
            $timesheet = Timesheet::find($id);
            return view('timesheet.edit', compact('cases', 'members','timesheet'));

        } else {
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
        if (Auth::user()->can('edit timesheet')) {
            $validator = Validator::make(
                $request->all(), [
                    'case' => 'required',
                    'date' => 'required',
                    'particulars' => 'required',
                    'member' => 'required',
                    'time' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $timesheet = Timesheet::find($id);
            $timesheet['case'] = $request->case;
            $timesheet['date'] = $request->date;
            $timesheet['particulars'] = $request->particulars;
            $timesheet['time'] = $request->time;
            $timesheet['member'] = $request->member ?? 0;
            $timesheet['created_by'] = Auth::user()->id;
            $timesheet['status'] = 1;
            $timesheet->save();
            return redirect()->route('timesheet.index')->with('success', __('Timesheet successfully updated.'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->can('edit timesheet')) {
            $timesheet = Timesheet::find($id);
            if ($timesheet) {
                $timesheet->delete();
            }
            return redirect()->route('timesheet.index')->with('success', __('Timesheet successfully deleted.'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }

    }
    // draftView
    public function draftView()
    {
        if (Auth::user()->can('manage timesheet')) {
            if(Auth::user()->type == 'company'){
                $timesheets = Timesheet::where('created_by',Auth::user()->creatorId())->where('status',0)->orderBy('id', 'DESC')->get();
            }else{
                $timesheets = Timesheet::where('member',Auth::user()->id)->where('status',0)->orderBy('id', 'DESC')->get();
            }
            $isDraftView = true;
            return view('timesheet.index',compact('timesheets','isDraftView'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }

    }

   // Retrieve users belonging to the specified case's advocate and team, excluding clients.
    // Return user names and IDs.
    public function getCaseTeam(Request $request)
    {
        if($request->case_id == 0){
            $users = User::where('type', '!=', 'client')
            ->where('created_by',Auth::user()->creatorId())
            ->get(['id', 'name']);
            return response()->json($users);
        } else {
            $case = Cases::find($request->case_id);
            if($case){
            $advocate = $case->your_advocates;
            $team = $case->your_team;
            $users = User::where('type', '!=', 'client')
            ->whereIn('id', explode(',', "$advocate,$team"))
            ->get(['id', 'name']);
            }else{
                $users = [];
            }
            return response()->json($users);
        }
      
    }

    public function timeSheetTimerLog(Request $request){


        $buttonId = $request->input('buttonId');

        // Check the value of $buttonId and modify it accordingly
        if ($buttonId === 'start') {
            $buttonId = 'Started';
        } elseif ($buttonId === 'resume') {
            $buttonId = 'Resumed';
        }elseif($buttonId === 'pause'){
            $buttonId = 'Paused';
        }

        Activity::create([
            'user_id' => Auth::user()->id,
            'company_id' => Auth::user()->creatorId(),
            'target_type' => 'Timer',
            'action' => $buttonId ?? '',
        ]);

    }
}
