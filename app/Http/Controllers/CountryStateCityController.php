<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\CountryStateCity;
use App\Models\City;
use App\Models\Countries;
use App\Models\State;
use App\Models\States;
use App\Models\Timezone;
use DateTimeZone;

class CountryStateCityController extends Controller
{
    private $locationManger;

    public function __construct(CountryStateCity $countryStateCity)
    {
        $this->locationManger = $countryStateCity;
    }
    public function getCountry()
    {
        return $this->locationManger->getCountry();
    }
    public function getState(Request $request)
    {
        
        $country_id = $request->country_id;
        
        return $this->locationManger->getState($country_id);
    }
    public function getCity(Request $request)
    {
      
        $region_id = $request->state_id;
        // dd($this->locationManger->getCity($region_id));
        return $this->locationManger->getCity($region_id);
    }
    public function getAllState()
    {
        return States::all()->toArray();
    }

    public function getTimezone(Request $request){

        $countryCode = $request['country_id'];

        $countryTimezones = $this->getTimezonesForCountry($countryCode);
        $timezones = Timezone::whereIn('timezone', $countryTimezones)->get()->toArray();
        
        return $timezones;

    }

    function getTimezonesForCountry($countryCode)
    {
        $countryCode = strtoupper($countryCode);
        $code = Countries::where('id',$countryCode)->first('iso2');
        
        
        $allTimezones = DateTimeZone::listIdentifiers();
        $countryTimezones = [];
        
        foreach ($allTimezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezoneCountryCode = $tz->getLocation()['country_code'];
            
            if ($timezoneCountryCode === $code->iso2) {
               
                $countryTimezones[] = $timezone;
            }
        }
        

        return $countryTimezones;
    }
}
