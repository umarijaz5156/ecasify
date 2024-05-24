<?php

namespace App\Http\Controllers;

use App\Mail\AttorneyInvitation;
use App\Models\Advocate;
use App\Models\group;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PointOfContacts;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Utility;
    use App\Models\Cases;
use Database\Seeders\UserSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Traits\GoogleCalendarTrait;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreatedMail;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Activity;
use App\Models\Timezone;
use Illuminate\Support\Facades\DB;
use App\Mail\UserNotification;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\States;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use League\ISO3166\ISO3166;
use DateTimeZone;

class UserController extends Controller
{
    use GoogleCalendarTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::user()->can('manage member') || Auth::user()->can('manage user')) {
            

            $users = User::where('created_by', '=', Auth::user()->creatorId())->where('id', '!=', Auth::user()->id)->get();

            foreach ($users as $user) {
                $sessions = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->get();
                $user->last_activity =  $sessions->isEmpty() ? 'Non-active' : 'Active';
            }

            $companyUser = false;
            $user_details = UserDetail::get();

            return view('users.index', compact('users', 'user_details','companyUser'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userList()
    {

        if (Auth::user()->can('manage member') || Auth::user()->can('manage user')) {

            $users = User::where('created_by', '=', Auth::user()->creatorId())->where('id', '!=', Auth::user()->id)->get();

            foreach ($users as $user) {
                $sessions = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->get();
                $user->last_activity =  $sessions->isEmpty() ? 'Non-active' : 'Active';
            }

            $user_details = UserDetail::get();
            $companyUser = false;
            return view('users.list', compact('users', 'user_details','companyUser'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function companyUsers($id){
        
        $users = User::where('created_by', '=', $id)->get();

        foreach ($users as $user) {
            $sessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->get();
            $user->last_activity =  $sessions->isEmpty() ? 'Non-active' : 'Active';
        }

        $user_details = UserDetail::get();
        $companyUser = true;
        return view('users.index', compact('users', 'user_details','companyUser'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (Auth::user()->can('create member') || Auth::user()->can('create user')) {

            $ip = $request->ip();
            $apiResponse = Http::get("http://ipinfo.io/{$ip}/json");
            // dd($apiResponse);
            $locationData = $apiResponse->json();
            if(isset($locationData['country'])){
                $countryCode = $locationData['country'];
    
            }else{
                $countryCode = 'US';
            }
            $iso3166 = new ISO3166();
           
            $countryInfo = $iso3166->alpha2($countryCode);
            $selectedCountry = $countryInfo['name'] ?? '';
            $selectedState = $locationData['region'] ?? 'Alabama';
            $selectedCity = $locationData['city'] ?? '';
          
            $countryTimezones = $this->getTimezonesForCountry($countryCode);
            
            $timezones = Timezone::whereIn('timezone', $countryTimezones)->get();

            if ($selectedCountry == "United States of America") {
                $selectC = Countries::where('name', 'United States')->select('id', 'name')->first();
            } else {
                $selectC = Countries::where('name', $selectedCountry)->select('id', 'name')->first();
            }
            $selectedCountry = $selectC->name;
            $selectS = States::where('country_id',$selectC->id)->where('name',$selectedState)->first('id');
    
            $cities = Cities::where('country_id',$selectC->id)->where('state_id',$selectS->id)->get();
          
            $countries = Countries::where('name',$selectC->name)->get();
            
            // $countries = Countries::all();
            $states = States::where('country_id',$selectC->id)->get();
            

            $creatorId = Auth::user()->creatorId();
            $roles = Role::where('created_by', $creatorId)
                ->whereNotIn('name', ['Advocate', 'co-Admin'])
                ->get(['id', 'name'])
                ->pluck('name', 'id');

            $caseCount = Cases::where('created_by', Auth::user()->creatorId())->count();


            return view('users.create', compact('caseCount','roles', 'timezones', 'selectedCountry', 'selectedState', 'selectedCity', 'countries', 'states','cities'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    function getTimezonesForCountry($countryCode)
    {
        $countryCode = strtoupper($countryCode);

        $allTimezones = DateTimeZone::listIdentifiers();
        $countryTimezones = [];

        foreach ($allTimezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezoneCountryCode = $tz->getLocation()['country_code'];

            if ($timezoneCountryCode === $countryCode) {
                $countryTimezones[] = $timezone;
            }
        }

        return $countryTimezones;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
      
        if (Auth::user()->can('create member') || Auth::user()->can('create user')) {
         

            $rawPhoneNumber = $request->input('mobile_number');
            if(!empty($rawPhoneNumber)){

                $cleanedPhoneNumber = preg_replace('/[^0-9+]/', '', $rawPhoneNumber);
                if (substr($cleanedPhoneNumber, 0, 1) !== '+') {
                    $cleanedPhoneNumber = '+' . $cleanedPhoneNumber;
                }
            }else{
                $cleanedPhoneNumber = null;
            }
           

            if (Auth::user()->type != 'super admin') {


                $type = $request->input('type');
                $userId = null;

                if($type == 'attorney'){

                    $users = User::where('email', $request->email)->first();
                    if (!empty($users)) {
                        return redirect()->back()->with('error', __('Email address already exist.'));
                    }
        
                    // $validator = Validator::make(
                    //     $request->all(),
                    //     [
                    //         'name' => 'required|max:120',
                    //         'email' => 'required|string|email|max:255|unique:users',
                    //         'password' => ['required', 'string', 'min:8'],
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
        
                                    return $msg;
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
                        // assignUserToAllCases
                        if($request->assign_all_cases == 'yes'){
                            $this->assignUserToAllCases($new_user->id,'your_advocates');
                        }
                        $detail = new UserDetail();
                        $detail->user_id = $new_user->id;
                        $detail->city = $request->city ?? null;
                        $detail->state = $request->state ?? null;
                        $detail->country = $request->country ?? null;
                        $detail->timezone = $request->timezone ?? null;
                        $detail->mobile_number = $cleanedPhoneNumber ?? null;
                        $detail->save();
                        $userId = $new_user->id;

        
                        $advocate = new Advocate();
                        $advocate['user_id']                = $new_user->id;
                        $advocate['phone_number']         = $cleanedPhoneNumber;
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
                        $advocate['ofc_country']          = $request->country;
                        $advocate['ofc_state']            = $request->state;
                        $advocate['ofc_city']             = $request->city;
                        $advocate['ofc_zip_code']         = $request->ofc_zip_code;
                        $advocate['home_address_line_1']  = $request->home_address_line_1;
                        $advocate['home_address_line_2']  = $request->home_address_line_2;
                        $advocate['home_country']         = $request->home_country;
                        $advocate['home_state']           = $request->home_state;
                        $advocate['home_city']            = $request->home_city;
                        $advocate['home_zip_code']        = $request->home_zip_code;
                        $advocate['created_by']        = Auth::user()->creatorId();
                       
                        $advocate->save();


                            // activity log
                            Activity::create([
                                'user_id' => Auth::user()->id,
                                'company_id' => Auth::user()->creatorId(),
                                'target_id' => $new_user->id,
                                'target_type' => 'User',
                                'action' => 'Created',
                            ]);
        
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
                        // updateaddor update group
                        $this->groupAddORUpdate($userId);
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
        
                        return redirect()->back()->with('success', __('Attorney successfully created.'));
                    } else {
        
                        return redirect()->back()->with('error', __('Your Attorney limit is over, Please upgrade plan.'));
                    }

                    


                }else{

                    $validator = Validator::make(
                        $request->all(),
                        [
                            'name' => 'required|max:120',
                            'email' => 'required|email|unique:users',
                            'password' => 'required|min:8',
                            'role' => 'required',
                            'type' => ['required',Rule::in(['team', 'client','co admin'])],
                            'country' => 'required',
                            'state' => 'required',
                            'city' => 'required',
                            'timezone' => 'required',
                            'mobile_number' => 'required',
                        ],
                    );
    
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();
                      
                        if(isset($request->ajax_client_call)){
                            return response()->json(['error' => $messages], 400); // Use an appropriate HTTP status code (e.g., 400 for Bad Request)
                        }
                        return redirect()->back()->with('error', $messages->first());
                    }
    
                    $user = Auth::user();
                    $plan = $user->getPlan();
                    
                    $total_user = User::where('created_by', '=', $user->creatorId())->count();
                  
                    if ($total_user < $plan->max_users || $plan->max_users == -1) {
                        $user = new User();
                        $user['name'] = $request->name;
                        $user['email'] = $request->email;
                        $user['password'] = Hash::make($request->password);
                        $user['lang'] = 'en';
                        $user['created_by'] = Auth::user()->creatorId();
                        $user['email_verified_at'] = date('Y-m-d H:i:s');
    
                        // $requested_role = () ? 'company' : $request->role;
                        if($request->type == 'co admin'){
                            $role_r = Role::findByName('company');
                        }else{
                            $role_r = Role::findById($request->role);
                        }

                        $user->assignRole($role_r);
                        $user['type'] = $request->type;
                        // $user['role_title'] = $role_r->name;
    
                        if (Auth::user()->google_calendar_id) {
                            $calendarId = Auth::user()->google_calendar_id;
                        } else {
                            // createCalendar 
                            $calendarId = $this->createCalendar($user->name);
                        }
    
                        $this->shareCalendar($user->email, $calendarId);
                        $user['google_calendar_id'] = $calendarId;
    
                        $user->save();

                        // activity log
                        Activity::create([
                            'user_id' => Auth::user()->id,
                            'company_id' => Auth::user()->creatorId(),
                            'target_id' => $user->id,
                            'target_type' => 'User',
                            'action' => 'Created',
                        ]);
                        

                        $userId = $user->id;
                        // assignUserToAllCases
                        if($request->assign_all_cases == 'yes' && $request->type == 'team'){
                            $this->assignUserToAllCases($user->id,'your_team');
                        }
                        $detail = new UserDetail();
                        $detail->user_id = $user->id;
                        $detail->city = $request->city ?? null;
                        $detail->state = $request->state ?? null;
                        $detail->country = $request->country ?? null;
                        $detail->timezone = $request->timezone ?? null;
                        $detail->mobile_number = $cleanedPhoneNumber ?? null;
                        $detail->save();

                        $this->groupAddORUpdate($userId);
                        // send mail
                        $data = [
                            'name' => $request->name,
                            'username' => $request->email,
                            'password' => $request->password,
                            'loginLink' => route('login'),
                            'subject' => 'Invitation to Join ' .  User::getCompanyName(Auth::user()->creatorId()) . '',
                            'type' => $user['type'],
                        ];
                        $ccEmails = [];
                        $ccUser = User::find($user->created_by);
                        while ($ccUser && $ccUser->created_by) {
                            array_unshift($ccEmails, $ccUser->email);
                            $ccUser = User::find($ccUser->created_by);
                        }
                        $ccEmailsList = array_unique($ccEmails);
    
    
                        try {
                            Mail::to($user->email)->send((new UserNotification)->cc($ccEmailsList)->added($data));
                
                        } catch (\Exception $e) {
                          
                        }
                        
                       
                        if(isset($request->ajax_client_call)){
                            $team = User::where('created_by', Auth::user()->creatorId())->where('type', '=', 'client')->pluck('name', 'id');
                           
                            return response()->json(['team' => $team]);

                        }
                     
                        return redirect()->route('users.index')->with('success', __('Member successfully created.'));
                    } else {
                        if(isset($request->ajax_client_call)){
                            return response()->json(['error' => 'Your member limit is over, Please upgrade plan.'], 400); // Use an appropriate HTTP status code (e.g., 400 for Bad Request)
                        }
                        return redirect()->route('users.index')->with('error', __('Your member limit is over, Please upgrade plan.'));
                    }
                    
                }
                

                


            } else {

                $validator = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                        'password' => 'required|min:8',
                        'country' => 'required',
                        'state' => 'required',
                        'city' => 'required',
                        'timezone' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                $date = Carbon::now()->format('Y-m-d');
                $planExpiryDate = Carbon::now()->addDays(15)->format('Y-m-d');
        
                $user = new User();
                $user['name'] = $request->name;
                $user['email'] = $request->email;
                $user['email_verified_at'] = now();
                $user['password'] = Hash::make($request->password);
                $user['lang'] = 'en';
                $user['created_by'] = Auth::user()->creatorId();
                $user['plan'] = Plan::first()->id;
                $user['plan_active_date'] = $date;
                $user['plan_expire_date']  = $planExpiryDate;
                if (Utility::settings()['email_verification'] == 'off') {
                    $user['email_verified_at'] = date('Y-m-d H:i:s');
                }

                $role_r = Role::findByName('company');
                $user->assignRole($role_r);
                $user['type'] = 'company';
                // $user['role_title'] = $role_r->name;

                if (Auth::user()->google_calendar_id) {
                    $calendarId = Auth::user()->google_calendar_id;
                } else {
                    // createCalendar 
                    $calendarId = $this->createCalendar($user->name);
                }

                // dd($calendarId);
                $this->shareCalendar($user->email, $calendarId);

                $user['google_calendar_id'] = $calendarId;
                $user->save();
                $detail = new UserDetail();
                $detail->user_id = $user->id;
                $detail->city = $request->city ?? null;
                $detail->state = $request->state ?? null;
                $detail->country = $request->country ?? null;
                $detail->timezone = $request->timezone ?? null;
                $detail->save();
                
                //create company default roles
                $user->MakeRole($user->id,'co-Admin');
                $user->MakeRole($user->id,'advocate');
                $user->MakeRole($user->id,'client');
                $user->MakeRole($user->id,'staff');
                // send mail
                $data = [
                    'name' => $request->name,
                    'username' => $request->email,
                    'password' => $request->password,
                    'loginLink' => route('login'),
                    'subject' => 'Invitation to Join ' .  User::getCompanyName(Auth::user()->creatorId()) . '',
                    'type' => $user['type'],
                ];
                $ccEmails = [];
                $ccUser = User::find($user->created_by);
                while ($ccUser && $ccUser->created_by) {
                    array_unshift($ccEmails, $ccUser->email);
                    $ccUser = User::find($ccUser->created_by);
                }
                $ccEmailsList = array_unique($ccEmails);

                try {
                    Mail::to($user->email)->send((new UserNotification)->cc($ccEmailsList)->added($data));
        
                } catch (\Exception $e) {
                  
                }

                return redirect()->route('users.index')->with('success', __('Member successfully created.'));
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
    public function show($user_id)
    {
        if (Auth::user()->can('show member')) {

            $user_detail = UserDetail::where('user_id', $user_id)->first();

            if ($user_detail) {
                $data = explode(',', $user_detail->my_group);
                $my_groups = group::whereIn('id', $data)->get()->pluck('name');
                return view('users.view', compact('my_groups'));
            }
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
        
        $user = User::withTrashed()->find($id);

        
        if($user->type == 'advocate'){
            $advocate = Advocate::where('user_id',$id)->first();
          
                $userAdd = User::where('id', $id)->first();
               
                $contacts = PointOfContacts::where('advocate_id', $advocate->id)->get();
                return view('advocate.edit', compact('advocate', 'contacts', 'userAdd'));
           
        }
        $user_detail = UserDetail::where('user_id', $user->id)->first();
        $roles = Role::where('created_by', '=', $user->creatorId())->get()->pluck('name', 'id');
        $advocate = $contacts = [];

        if (Auth::user()->type == 'advocate') {
            $advocate = Advocate::where('user_id', $user->id)->first();
            $contacts = PointOfContacts::where('advocate_id', $advocate->id)->get();
        }
        


        $countries = Countries::all();
        $states = States::where('country_id',$user_detail->country)->get();
        $cities = Cities::where('state_id',$user_detail->state)->get();
        $timezones = Timezone::all();

        return view('users.edit', compact('user', 'roles', 'user_detail', 'advocate', 'contacts', 'countries', 'states', 'cities', 'timezones'));
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
            $request->all(),
            [
                'name' => 'required|max:120',
                'email' => 'required|email',
                'timezone' => 'required',
            ]
        );
        if (!empty($request->mobile_number)) {

            $validator = Validator::make(
                $request->all(),
                [
                    'mobile_number' => 'required|numeric|digits:10',
                ]
            );
        }

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user = User::find($id);

        if ($user) {
            if (Auth::user()->type == 'advocate') {

                $adv = Advocate::where('user_id', $user->id)->first();

                if ($adv) {
                    if (!empty($request->point_of_contacts)) {
                        foreach ($request->point_of_contacts as $items) {
                            foreach ($items as $item) {

                                if (empty($item) && $item < 0) {
                                    $msg['flag'] = 'error';
                                    $msg['msg'] = __('Please enter your contacts');

                                    return redirect()->back()->with('error', $msg);
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

                    $advocate = Advocate::find($adv->id);
                    $userAdd = $advocate->getAdvUser($advocate->user_id);

                    if ($userAdd->email != $request->email) {

                        $users = User::where('email', $request->email)->first();
                        if (!empty($users)) {
                            return redirect()->back()->with('error', __('Email address already exist.'));
                        }
                    }

                    $advocate['phone_number'] = !empty($request->phone_number) ? $request->phone_number : NULL;
                    $advocate['father_name'] = $request->father_name;
                    $advocate['age'] = !empty($request->age) ? $request->age : NULL;
                    $advocate['company_name'] = $request->company_name;
                    $advocate['website'] = $request->website;
                    $advocate['tin'] = $request->tin;
                    $advocate['gstin'] = $request->gstin;
                    $advocate['pan_number'] = $request->pan_number;
                    $advocate['hourly_rate'] = !empty($request->hourly_rate) ? $request->hourly_rate : NULL;
                    $advocate['ofc_address_line_1'] = $request->ofc_address_line_1;
                    $advocate['ofc_address_line_2'] = $request->name;
                    $advocate['ofc_country'] = $request->ofc_country;
                    $advocate['ofc_state'] = !empty($request->ofc_state) ? $request->ofc_state : NULL;
                    $advocate['ofc_city'] = $request->ofc_city;
                    $advocate['ofc_zip_code'] = !empty($request->ofc_zip_code) ? $request->ofc_zip_code : NULL;
                    $advocate['home_address_line_1'] = $request->home_address_line_1;
                    $advocate['home_address_line_2'] = $request->home_address_line_2;
                    $advocate['home_country'] = $request->home_country;
                    $advocate['home_state'] = $request->home_state;
                    $advocate['home_city'] = $request->home_city;
                    $advocate['home_zip_code'] = !empty($request->home_zip_code) ? $request->home_zip_code : NULL;
                    $advocate->save();

                    $userAdd->name = $request->name;
                    $userAdd->email = $request->email;

                    if ($request->hasFile('profile')) {
                        $filenameWithExt = $request->file('profile')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('profile')->getClientOriginalExtension();
                        $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                        $dir = 'uploads/profile/';
                        $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);

                        if ($path['flag'] == 1) {
                            $url = $path['url'];
                        } else {
                            return redirect()->route('users.index', Auth::user()->id)->with('error', __($path['msg']));
                        }

                        $userAdd->avatar = $fileNameToStore;
                    }


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
                        'name' => $request->name,
                        'username' => $request->email,
                        'loginLink' => route('login'),
                        'subject' => 'Profile Edited Notification',
                        'type' => $user['type'],
                    ];
                $ccEmails = [];
                $ccUser = User::find($user->created_by);
                while ($ccUser && $ccUser->created_by) {
                    array_unshift($ccEmails, $ccUser->email);
                    $ccUser = User::find($ccUser->created_by);
                }
                $ccEmailsList = array_unique($ccEmails);

                try {
                    Mail::to($user->email)->send((new UserNotification)->cc($ccEmailsList)->updated($data));
        
                } catch (\Exception $e) {
                  
                }


                // activity log
                Activity::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => Auth::user()->creatorId(),
                    'target_id' => $user->id,
                    'target_type' => 'User',
                    'action' => 'Updated',
                ]);
        
                    return redirect()->back()->with('success', __('Successfully Updated!'));
                } else {
                    return redirect()->back()->with('error', __('Advocate not found.'));
                }
            } else {
               
                $user['name'] = $request->name;
                $user['email'] = $request->email;

                if ($request->hasFile('profile')) {
                    if (Auth::user()->type == 'super admin') {
                        $filenameWithExt = $request->file('profile')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('profile')->getClientOriginalExtension();
                        $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                        $settings = Utility::Settings();
                        $url = '';
                        $dir = 'uploads/profile/';
                        $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
                       
                        if ($path['flag'] == 1) {
                            $url = $path['url'];
                        } else {
                            return redirect()->route('users.index', Auth::user()->id)->with('error', __($path['msg']));
                        }

                        $user->avatar = $fileNameToStore;
                    } else {
                        $dir        = 'uploads/profile/';
                        $file_path = $dir . $user['avatar'];
                        $image_size = $request->file('profile')->getSize();

                        $result = Utility::updateStorageLimit(Auth::user()->id, $image_size);
                        // dd($result);
                        // if ($result == 1) {

                            Utility::changeStorageLimit(Auth::user()->id, $file_path);
                            $filenameWithExt = $request->file('profile')->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension = $request->file('profile')->getClientOriginalExtension();
                            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                            $settings = Utility::Settings();
                            $url = '';
                            $dir = 'uploads/profile/';
                            $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);

                            if ($path['flag'] == 1) {
                                $url = $path['url'];
                            } else {
                                return redirect()->route('users.index', Auth::user()->id)->with('error', __($path['msg']));
                            }

                            $user->avatar = $fileNameToStore;
                        // }
                    }
                }

                $user->update();

                $detail = UserDetail::where('user_id', $user->id)->first();

                $detail->mobile_number = !empty($request->mobile_number) ? $request->mobile_number : null;
                $detail->address = $request->address;
                $detail->country = $request->country;
                $detail->state = $request->state;
                $detail->city = $request->city;
                $detail->timezone = $request->timezone;
                $detail->zip_code = !empty($request->zip_code) ? $request->zip_code : null;
                $detail->landmark = $request->landmark;
                $detail->about = $request->about;

                $detail->save();
                // send mail
                $data = [
                    'name' => $request->name,
                    'username' => $request->email,
                    'loginLink' => route('login'),
                    'subject' => 'Profile Edited Notification',
                    'type' => $user['type'],
                ];
                $ccEmails = [];
                $ccUser = User::find($user->created_by);
                while ($ccUser && $ccUser->created_by) {
                    array_unshift($ccEmails, $ccUser->email);
                    $ccUser = User::find($ccUser->created_by);
                }
                $ccEmailsList = array_unique($ccEmails);


                try {
                    Mail::to($user->email)->send((new UserNotification)->cc($ccEmailsList)->updated($data));
        
                } catch (\Exception $e) {
                  
                }

                     // activity log
                     Activity::create([
                        'user_id' => Auth::user()->id,
                        'company_id' => Auth::user()->creatorId(),
                        'target_id' => $user->id,
                        'target_type' => 'User',
                        'action' => 'Updated',
                    ]);

                return redirect()->route('users.index')->with('success', __('Successfully Updated!'));
            }
        } else {
            return redirect()->back()->with('error', __('Member not found.'));
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
       
        if (Auth::user()->can('delete member') || Auth::user()->can('delete user')) {
            $user = User::find($id);
            $detail = UserDetail::where('user_id', $user->id)->first();

            if ($user->created_by != Auth::user()->creatorId()) {
                return redirect()->back()->with('error', __('You cant delete yourself.'));
            } else {
                if ($user && $detail) {
                    
               
                    $userDetails = $user;
                    $user->delete();

                    // activity log
                    Activity::create([
                        'user_id' => Auth::user()->id,
                        'company_id' => Auth::user()->creatorId(),
                        'target_id' => $user->id,
                        'target_type' => 'User',
                        'action' => 'Deleted',
                    ]);
                

                    $data = explode(',', $detail->my_group);
                    $my_groups = group::whereIn('id', $data)->get();

                    foreach ($my_groups as $key => $value) {
                        if (str_contains($value->members, $detail->user_id)) {
                            $value->members = trim($value->members, $detail->user_id);
                            $value->save();
                        }
                    }
                    // send mail
                    $data = [
                        'name' => $userDetails->name,
                        'subject' => 'Profile Deleted Notification',
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


                    return redirect()->back()->with('success', __('Member deleted successfully.'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Member not found.'));
        }
    }

    public function permanentlyDelete($id)
    {
       
        if (Auth::user()->can('delete member') || Auth::user()->can('delete user')) {
            $user = User::find($id);
            $detail = UserDetail::where('user_id', $user->id)->first();

            if ($user->created_by != Auth::user()->creatorId()) {
                return redirect()->back()->with('error', __('You cant delete yourself.'));
            } else {
                if ($user && $detail) {
                    
                    if($user->type == 'advocate'){
                        $attorney = Advocate::where('user_id', $user->id)->first();
                        $attorney->delete();
                    }
               
                    $userDetails = $user;
                    $user->delete();
                    $detail->delete();

                    $data = explode(',', $detail->my_group);
                    $my_groups = group::whereIn('id', $data)->get();

                    foreach ($my_groups as $key => $value) {
                        if (str_contains($value->members, $detail->user_id)) {
                            $value->members = trim($value->members, $detail->user_id);
                            $value->save();
                        }
                    }
                    // send mail
                    $data = [
                        'name' => $userDetails->name,
                        'subject' => 'Profile Deleted Notification',
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


                    return redirect()->back()->with('success', __('Member deleted successfully.'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Member not found.'));
        }
    }

    public function changeMemberPassword(Request $request, $id)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'required|same:confirm_password',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $objUser = User::find($id);
        $objUser->password = Hash::make($request->password);
        $objUser->save();

        return redirect()->back()->with('success', __('Password updated successfully.'));
    }

    public function companyPassword($id)
    {
        $eId   = Crypt::decrypt($id);
        $user  = User::find($eId);

        $employee = User::where('id', $eId)->first();

        return view('users.reset', compact('user', 'employee'));
    }

    public function upgradePlan($user_id)
    {
        $user  = User::find($user_id);
        if($user == 'co admin'){
            $user  = User::find($user->creatorId());
        }
        $plans = Plan::get();
        $admin_payment_setting = Utility::settings();
        return view('users.plan', compact('user', 'plans', 'admin_payment_setting'));
    }

    public function activePlan($user_id, $plan_id)
    {
        $user       = User::find($user_id);
        $user->plan = $plan_id;
        $user->save();
        $assignPlan = $user->assignPlan($plan_id);
        $plan       = Plan::find($plan_id);

        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $plan->price,
                    'price_currency' => env('CURRENCY'),
                    'txn_id' => '',
                    'payment_type' => __('Manually Upgrade By Super Admin'),
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );
        }

        return redirect()->back()->with('success', __('Plan successfully activated.'));
    }

    public function deleteSession(Request $request)
    {

        $id = $request->user_id;
        $this->groupAddOrUpdate($id,'delete');
        DB::table('sessions')->where('user_id', $id)->delete();
        return response()->json(['message' => 'Sessions deleted successfully.']);
    }

    // assign user to all existing cases
    public function assignUserToAllCases($user_id,$type)
    {
      
        $cases = Cases::where('created_by', Auth::user()->creatorId())->get();

        foreach ($cases as $key => $value) {
            $case = Cases::find($value->id);
            //  cases->$type (comma spreaded string) to array
            $staff = explode(',', $case->$type);
            // push new advocate id to array
            array_push($staff, $user_id);
            // array to comma spreaded string
            $case->$type = implode(',', $staff);
            // save 
            $case->save();
        }
    }
    //demote-to-co-admin
    public function demoteToCoAdmin(Request $request)
    {
        
        $user = User::find($request->user_id);
        // dd($user->getRoleNames(),$user->hasAnyRole('advocate'),$user->hasAnyRole(array('company','advocate')));
        if ($user && $user->type != 'client') {
            $user->removeRole('company');
            $user->update([
                'role_title' => implode(',', array_diff(explode(',', $user->role_title), ['co-Admin']))
            ]);
            $user->save();
            // eho json encoded
            return response()->json(['success' => 'User demoted successfully.']);
        }else{
            return response()->json(['error' => 'User not found.']);
        }

    }
    //promote-to-co-admin
    public function promoteToCoAdmin(Request $request)
    {
        // removeRole(role)
        $user = User::find($request->user_id);
        if ($user && $user->type != 'client') {
            $role_r = Role::findByName('company');
            $user->assignRole($role_r);
            $user->update([
                'role_title' => implode(',', array_filter(array_merge(explode(',', $user->role_title), ['co-Admin'])))
            ]);
            $user->save();
            // eho json encoded
            return response()->json(['success' => 'User promoted successfully.']);
        }else{
            return response()->json(['error' => 'User not found.']);
        }
        // dd($user);



    }

    public function groupAddOrUpdate($userIds, $action = 'add',$creatorId = '')
    {
        $creatorId = ($creatorId != '') ? $creatorId : Auth::user()->creatorId();
        $grp = Group::where('created_by', $creatorId)
            ->where('is_all_user', 1)
            ->first();

        if (!$grp) {
            // Group doesn't exist, create a new group
            $grp = new Group();
            $grp->name = 'All Users';
            $grp->members = (is_array($userIds)) ? implode(',', $userIds) : $userIds;
            $grp->created_by = $creatorId;
            $grp->is_all_user = 1;
            $grp->assigned_at = now();
            $grp->save();
        }

        $members = explode(',', $grp->members);

        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        foreach ($userIds as $userId) {
            if ($action === 'add' && !in_array($userId, $members)) {
                // Add the new user to the group
                $members[] = $userId;
            } elseif ($action === 'delete') {
                // Remove the user from the group
                $key = array_search($userId, $members);
                if ($key !== false) {
                    unset($members[$key]);
                }
            }
            // Update the group members
            $grp->members = implode(',', $members);
            $grp->save();

            // Update my_group attribute for existing users
            $existingUserIds = array_unique(explode(',', $grp->members));
            UserDetail::whereIn('user_id', $existingUserIds)->chunk(200, function ($users) use ($grp, $action) {
                foreach ($users as $user) {
                    $groupIds = array_unique(explode(',', $user->my_group));

                    if ($action === 'add') {
                        $groupIds[] = $grp->id;
                    } elseif ($action === 'delete') {
                        $key = array_search($grp->id, $groupIds);
                        if ($key !== false) {
                            unset($groupIds[$key]);
                        }
                    }

                    $user->my_group = implode(',', $groupIds);
                    $user->save();
                }
            });
        }
    }
}
