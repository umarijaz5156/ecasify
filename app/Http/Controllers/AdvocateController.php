<?php

namespace App\Http\Controllers;

use App\Models\Advocate;
use App\Models\Bill;
use App\Models\Cases;
use App\Models\PointOfContacts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Traits\GoogleCalendarTrait;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttorneyInvitation;
use App\Mail\UserNotification;

class AdvocateController extends Controller
{
    use GoogleCalendarTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('manage advocate')) {

            $advocates = Advocate::where('created_by', Auth::user()->creatorId())->get();

            return view('advocate.index', compact('advocates'));
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

        if (Auth::user()->can('create advocate')) {
            return view('advocate.create');
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
        if (Auth::user()->can('create advocate')) {
            $users = User::where('email', $request->email)->first();
            if (!empty($users)) {
                return redirect()->back()->with('error', __('Email address already exist.'));
            }

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => ['required', 'string', 'min:8'],
                    'phone_number' => 'required',
                    'age' => 'required|numeric',
                    'father_name' => 'required',
                    'company_name' => 'required|max:120',
                    'website' => 'required',
                    'tin' => 'required',
                    'gstin' => 'required|min:15',
                    'pan_number' => 'required|min:10',
                    'hourly_rate' => 'required|numeric',
                    'ofc_address_line_1' => 'required',
                    'ofc_address_line_2' => 'required',
                    'ofc_country' => 'required',
                    'ofc_state' => 'required',
                    'ofc_city' => 'required',
                    'ofc_zip_code' => 'required|numeric',
                    'home_address_line_1' => 'required',
                    'home_address_line_2' => 'required',
                    'home_country' => 'required',
                    'home_state' => 'required',
                    'home_city' => 'required',
                    'home_zip_code' => 'required',
                ]
            );
            if (!empty($request->point_of_contacts)) {
                foreach ($request->point_of_contacts as $items) {
                    foreach ($items as $item) {

                        if (empty($item) && $item < 0) {
                            $msg['flag'] = 'error';
                            $msg['msg'] = __('Please enter your contacts');

                            return $msg;
                        }
                    }
                    $validator = Validator::make(
                        $items,
                        [
                            'contact_name' => 'required',
                            'contact_email' => 'required',
                            'contact_phone' => 'required|numeric|digits:10',
                            'contact_designation' => 'required',
                        ]
                    );
                }
            }

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $user = Auth::user();
            $plan = $user->getPlan();
            $total_user = User::where('created_by', '=', $user->creatorId())->count();

            if ($total_user < $plan->max_users || $plan->max_users == -1) {

                $calendarId = Auth::user()->google_calendar_id;
                $this->shareCalendar($request->email,$calendarId);
                $new_user = User::create(
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'email_verified_at' => now(),
                        'password' => Hash::make($request->password),
                        'type' => 'advocate',
                        'lang' => 'en',
                        'avatar' => '',
                        'created_by' => Auth::user()->creatorId(),
                        'email_verified_at' => now(),
                        'google_calendar_id' => $calendarId,
                    ]
                );
                $new_user->assignRole('advocate');

               

                $advocate = new Advocate();
                $advocate['user_id']                = $new_user->id;
                $advocate['phone_number']         = $request->phone_number;
                $advocate['father_name']          = $request->father_name;
                $advocate['age']                  = $request->age;
                $advocate['company_name']         = $request->company_name;
                $advocate['website']              = $request->website;
                $advocate['tin']                  = $request->tin;
                $advocate['gstin']                = $request->gstin;
                $advocate['pan_number']           = $request->pan_number;
                $advocate['hourly_rate']          = $request->hourly_rate;
                $advocate['ofc_address_line_1']   = $request->ofc_address_line_1;
                $advocate['ofc_address_line_2']   = $request->name;
                $advocate['ofc_country']          = $request->ofc_country;
                $advocate['ofc_state']            = $request->ofc_state;
                $advocate['ofc_city']             = $request->ofc_city;
                $advocate['ofc_zip_code']         = $request->ofc_zip_code;
                $advocate['home_address_line_1']  = $request->home_address_line_1;
                $advocate['home_address_line_2']  = $request->home_address_line_2;
                $advocate['home_country']         = $request->home_country;
                $advocate['home_state']           = $request->home_state;
                $advocate['home_city']            = $request->home_city;
                $advocate['home_zip_code']        = $request->home_zip_code;
                $advocate['created_by']        = Auth::user()->creatorId();
               
                $advocate->save();

                // mail to inform
                 

                if (!empty($request->point_of_contacts)) {
                    foreach ($request->point_of_contacts as $key => $value) {
                        $contacts = new PointOfContacts();

                        $contacts['advocate_id'] = $advocate->id;
                        $contacts['contact_name'] = $value['contact_name'];
                        $contacts['contact_email'] = $value['contact_email'];
                        $contacts['contact_phone'] = $value['contact_phone'];
                        $contacts['contact_designation'] = $value['contact_designation'];

                        $contacts->save();
                    }
                }
                 // send mail
                 $data = [
                    'attorneyName' => $request->name,
                    'username' => $request->email,
                    'password' => $request->password,
                    'loginLink' => route('login'),
                ];
                $userEmails = [];
                while ($new_user && $new_user->created_by) {
                    array_unshift($userEmails, $new_user->email);
                    $new_user = User::find($new_user->created_by);
                    
                }
                    $userEmailsList = array_unique($userEmails);

                    try {
                        Mail::to($userEmailsList)->send((new AttorneyInvitation)->invitation($data));
            
                    } catch (\Exception $e) {
                      
                    }

                return redirect()->route('advocate.index')->with('success', __('Attorney successfully created.'));
            } else {

                return redirect()->route('advocate.index')->with('error', __('Your Attorney limit is over, Please upgrade plan.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        if (Auth::user()->can('view advocate')) {

            $cases = [];
            $cases_data = Cases::get();
            foreach ($cases_data as $key => $case) {
                if (str_contains($case->your_advocates, $id)) {
                    $cases[$key]['id'] = $case->id;
                    $cases[$key]['court'] = $case->court;
                    $cases[$key]['highcourt'] = $case->highcourt;
                    $cases[$key]['casenumber'] = $case->casenumber;
                    $cases[$key]['bench'] = $case->bench;
                    $cases[$key]['diarybumber'] = $case->diarybumber;
                    $cases[$key]['title'] = $case->title;
                    $cases[$key]['your_advocates'] = $case->your_advocates;
                    $cases[$key]['priority'] = $case->priority;
                    $cases[$key]['year'] = $case->year;
                }
            }

            return view('advocate.view', compact('cases'));
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
        if (Auth::user()->can('edit advocate')) {

            $advocate = Advocate::find($id);
            if ($advocate) {
                $userAdd = User::where('email', $advocate->email)->first();
               
                $contacts = PointOfContacts::where('advocate_id', $advocate->id)->get();
                return view('advocate.edit', compact('advocate', 'contacts', 'userAdd'));
            } else {

                return redirect()->back()->with('error', __('Attorney not found.'));
            }
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
        
        if (Auth::user()->can('edit advocate')) {

            // $validator = Validator::make(
            //     $request->all(),
            //     [
            //         'name' => 'required|max:120',
            //         'email' => 'required|email',
            //         'phone_number' => 'required',
            //         'age' => 'required|numeric',
            //         'father_name' => 'required',
            //         'company_name' => 'required|max:120',
            //         'website' => 'required',
            //         'tin' => 'required',
            //         'gstin' => 'required|min:15',
            //         'pan_number' => 'required|min:10',
            //         'hourly_rate' => 'required|numeric',
            //         'ofc_address_line_1' => 'required',
            //         'ofc_address_line_2' => 'required',
            //         'ofc_country' => 'required',
            //         'ofc_state' => 'required',
            //         'ofc_city' => 'required',
            //         'ofc_zip_code' => 'required|numeric',
            //         'home_address_line_1' => 'required',
            //         'home_address_line_2' => 'required',
            //         'home_country' => 'required',
            //         'home_state' => 'required',
            //         'home_city' => 'required',
            //         'home_zip_code' => 'required',
            //     ]
            // );

            if (!empty($request->point_of_contacts)) {
                foreach ($request->point_of_contacts as $items) {
                    foreach ($items as $item) {

                        if (empty($item) && $item < 0) {
                            $msg['flag'] = 'error';
                            $msg['msg'] = __('Please enter your contacts');

                            return redirect()->back()->with('error', $msg);
                        }
                    }
                    // $validator = Validator::make(
                    //     $items,
                    //     [
                    //         'contact_name' => 'required',
                    //         'contact_email' => 'required',
                    //         'contact_phone' => 'required|numeric|digits:10',
                    //         'contact_designation' => 'required',
                    //     ]
                    // );
                }
            }

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $advocate = Advocate::find($id);
            $userAdd = $advocate->getAdvUser($advocate->user_id);

            if ($userAdd->email != $request->email) {

                $users = User::where('email', $request->email)->first();
                if (!empty($users)) {
                    return redirect()->back()->with('error', __('Email address already exist.'));
                }
            }
          
            

            $advocate['phone_number'] = $request->phone_number;
            $advocate['father_name'] = $request->father_name;
            $advocate['age'] = $request->age;
            $advocate['company_name'] = $request->company_name;
            $advocate['website'] = $request->website;
            $advocate['tin'] = $request->tin;
            $advocate['gstin'] = $request->gstin;
            $advocate['pan_number'] = $request->pan_number;
            $advocate['hourly_rate'] = $request->hourly_rate;
            $advocate['ofc_address_line_1'] = $request->ofc_address_line_1;
            $advocate['ofc_address_line_2'] = $request->name;
            $advocate['ofc_country'] = $request->ofc_country;
            $advocate['ofc_state'] = $request->ofc_state;
            $advocate['ofc_city'] = $request->ofc_city;
            $advocate['ofc_zip_code'] = $request->ofc_zip_code;
            $advocate['home_address_line_1'] = $request->home_address_line_1;
            $advocate['home_address_line_2'] = $request->home_address_line_2;
            $advocate['home_country'] = $request->home_country;
            $advocate['home_state'] = $request->home_state;
            $advocate['home_city'] = $request->home_city;
            $advocate['home_zip_code'] = $request->home_zip_code;
            $advocate->save();

            $userAdd->name = $request->name;
            $userAdd->email = $request->email;
            $userAdd->save();

            foreach (json_decode($request->old_contacts, true) as $key => $value) {
                $contacts = PointOfContacts::find($value['id']);
                $contacts->delete();
            }
            if (!empty($request->point_of_contacts)) {
                foreach ($request->point_of_contacts as $key => $value) {
                    $contacts = new PointOfContacts();

                    $contacts['advocate_id'] = $advocate->id;
                    $contacts['contact_name'] = $value['contact_name'];
                    $contacts['contact_email'] = $value['contact_email'];
                    $contacts['contact_phone'] = $value['contact_phone'];
                    $contacts['contact_designation'] = $value['contact_designation'];

                    $contacts->save();
                }
            }
             // send mail
             $data = [
                'loginLink' => route('login'),
                'name' => $request->name,
                'type' => 'advocate',

            ];
            $ccEmails = [];
                $ccUser = User::find($userAdd->created_by);
                while ($ccUser && $ccUser->created_by) {
                    array_unshift($ccEmails, $ccUser->email);
                    $ccUser = User::find($ccUser->created_by);
                }
                $ccEmailsList = array_unique($ccEmails);

                try {
                    Mail::to($userAdd->email)->send((new UserNotification)->cc($ccEmailsList)->updated($data));
        
                } catch (\Exception $e) {
                  
                }


            return redirect()->back()->with('success', __('Attorney successfully updated.'));
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
        if (Auth::user()->can('delete advocate')) {
            try {
                $cases = Cases::where('your_advocates', $id)->get();

                if (count($cases) > 0) {
                    return redirect()->route('advocate.index')->with('error', __('This Attorney is assigned to case.'));
                } else {
                    $adv = Advocate::find($id);
                    $userAdd = $user = $adv->getAdvUser($adv->user_id);

                    if ($userAdd) {
                        $userAdd->delete();
                    }
                    $adv->delete();

                    PointOfContacts::where('advocate_id', $id)->delete();
                    // send mail
                    $data = [
                        'name' => $adv->name,
                    ];
                    $ccEmails = [];
                    $ccUser = User::find($user->created_by);
                    while ($ccUser && $ccUser->created_by) {
                        array_unshift($ccEmails, $ccUser->email);
                        $ccUser = User::find($ccUser->created_by);
                    }
                    $ccEmailsList = array_unique($ccEmails);

                    try {
                        Mail::to($user->email)->send((new UserNotification)->cc($ccEmailsList)->deleted($data));
            
                    } catch (\Exception $e) {
                      
                    }

                    return redirect()->back()->with('success', __('Attorney successfully deleted.'));
                }
            } catch (Throwable $th) {

                return redirect()->back()->with('error', $th);
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function contacts($id)
    {
        if (Auth::user()->can('view advocate')) {
            $contacts = PointOfContacts::where('advocate_id', $id)->get();
            return view('advocate.contacts', compact('contacts'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function bills($id)
    {
        if (Auth::user()->can('view advocate')) {
            $bills = Bill::where('advocate', $id)->get();
            return view('advocate.bills', compact('bills'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function view($id)
    {
        if (Auth::user()->can('view advocate')) {
            $advocate = Advocate::find($id);
            return view('advocate.detail', compact('advocate'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function manageHearings($id)
    {
        if (Auth::user()->can('view advocate')) {
            $advocate = Advocate::find($id);

            return view('advocate.hearings', compact('advocate'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
