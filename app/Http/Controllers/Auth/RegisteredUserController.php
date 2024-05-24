<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;


use App\Models\Countries;
use App\Models\States;
use App\Models\Cities;
use App\Models\Timezone;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use App\Traits\GoogleCalendarTrait;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Http;
use League\ISO3166\ISO3166;

class RegisteredUserController extends Controller
{
    use GoogleCalendarTrait;
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $lang = '')
    {

        $preferredLanguage = $request->getPreferredLanguage();
      
     
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
        $selectedTimezone = $locationData['timezone'] ?? '';
        
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
        $states = States::all();
        
        // $timezones = Timezone::all();


        $settings = Utility::settings();

        if ($settings['signup_button'] == 'on') {
            if ($lang == '') {
                $lang = Utility::getValByName('default_language');
            }
            
           
            $preferredLanguage = $request->getPreferredLanguage();
           
            if(!empty($preferredLanguage)){
                
                $lang  = explode('_', $preferredLanguage)[0];
            }else{

                $lang = Utility::getValByName('default_language');
            }
          
        if($lang == 'ar' || $lang =='he'){
            $value = 'on';
        }
        else{
            $value = 'off';
        }
        DB::insert(
            'insert into settings (`value`, `name`,`created_by`) values ( ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', [
                $value,
                'SITE_RTL',
                1,

            ]
        );
        
            App::setLocale($lang);
           
            return view('auth.register', compact('lang','selectedCountry','selectedState','selectedCity','selectedTimezone','countries','states','cities','timezones'));
        } else {
            return \Redirect::to('login');
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
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        if (env('RECAPTCHA_MODULE') == 'yes') {
            $validation['g-recaptcha-response'] = 'required|captcha';
        } else {
            $validation = [];
        }

        $this->validate($request, $validation);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string',
                'min:8', 'confirmed', Rules\Password::defaults()],
        ]);

        // createCalendar 
        $date = Carbon::now()->format('Y-m-d');
        $planExpiryDate = Carbon::now()->addDays(15)->format('Y-m-d');


        $calendarId = $this->createCalendar($request->name);
        $this->shareCalendar($request->email,$calendarId);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'type' => 'company',
            'plan' => 1,
            'plan_active_date' => $date,
            'plan_expire_date' => $planExpiryDate,
            'lang' => Utility::getValByName('default_language'),
            'avatar' => '',
            'created_by' => 1,
            'google_calendar_id' => $calendarId,
        ]);

     
        $detail = new UserDetail();
        $detail->user_id = $user->id;
        $detail->city = $request->city ?? null;
        $detail->state = $request->state ?? null;
        $detail->country = $request->country ?? null;
        $detail->timezone = $request->timezone ?? null;
        $detail->save();

        Auth::login($user);

        $settings = Utility::settings();

        // if ($settings['email_verification'] == 'on') {
        //     try {
        //         event(new Registered($user));

        //         $role_r = Role::findByName('company');
        //         $user->assignRole($role_r);
        //         $user->MakeRole($user->id);

        //     } catch (\Exception $e) {

        //         $user->delete();
        //         return redirect('/register/lang?')->with('status', __('Email SMTP settings does not configure so please contact to your site admin.'));
        //     }
        //     return view('auth.verify');
        // } else {
            $user->google_calendar_id = $calendarId;
            $user->email_verified_at = date('h:i:s');
            $user->save();

            $role_r = Role::findByName('company');
            $user->assignRole($role_r);
                //create company default roles
            $user->MakeRole($user->id,'co-Admin');
            $user->MakeRole($user->id,'advocate');
            $user->MakeRole($user->id,'client');
            $user->MakeRole($user->id,'staff');

            return redirect(RouteServiceProvider::HOME);
        // }

    }
}
