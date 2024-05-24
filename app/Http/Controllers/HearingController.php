<?php

namespace App\Http\Controllers;

use App\Models\Cases;
use App\Models\Hearing;
use App\Models\HearingType;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HearingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($case_id)
    {

        $hearing_type = HearingType::where('created_by',Auth::user()->creatorId())->pluck('type','id');
        return view('hearings.create',compact('hearing_type','case_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
                'hearing_type' => 'required',
                'date' => 'required',
                'time' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $hearing = new Hearing();
        $hearing['case_id'] = $request->case_id;
        $hearing['type'] = $request->hearing_type;
        $hearing['date'] = $request->date;
        $hearing['time'] = $request->time;
        $hearing['created_by'] = Auth::user()->creatorId();
        $hearing->save();

        $case = Cases::find($hearing->case_id);

        if ($request->get('is_check') == '1') {
            $type = 'appointment';
            $request1 = new Cases();
            $request1->title = $case->title;
            $request1->start_date = $request->date;
            $request1->end_date = $request->date;
            Utility::addCalendarData($request1, $type);
        }

        return redirect()->back()->with('success', __('Hearing successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $hearing = Hearing::find($id);
        $hearing_types = HearingType::where('created_by',Auth::user()->creatorId())->pluck('type','id');
        $hearing_type = HearingType::find($hearing->type);

        return view('hearings.edit',compact('hearing','hearing_types','hearing_type'));
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

        $validator = Validator::make(
            $request->all(), [
                'hearing_type' => 'required',
                'date' => 'required',
                'time' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        
        $hearing = Hearing::find($id);
        $hearing['type'] = $request->hearing_type;
        $hearing['date'] = $request->date;
        $hearing['time'] = $request->time;
        $hearing->update();

        return redirect()->back()->with('success', __('Hearing successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $HearingType = Hearing::find($id);

        if ($HearingType) {
            $HearingType->delete();
        }

        return redirect()->back()->with('success', __('Hearing successfully deleted.'));
    }
}
