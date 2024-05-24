<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use PragmaRX\Countries\Package\Countries;
use PragmaRX\Countries\Package\Services\Config;


class CitySeeder extends Seeder
{
   
    public function run()
    {
        $countries = new Countries(new Config());
        
        $countriesData = $countries->all();
    
        foreach ($countriesData as $countryCode => $countryData) {
           
            $country = Country::create([
                'country' => $countryData['name']['common'],
            ]);
    
            dd($country);
            foreach ($countryData['states'] as $stateCode => $stateData) {
                $state = State::create([
                    'country_id' => $country->id,
                    'region' => $stateData['name'],
                ]);
    
                foreach ($stateData['cities'] as $cityData) {
                    if (!empty($cityData['name'])) {
                        $city = City::create([
                            'country_id' => $country->id,
                            'region_id' => $state->id,
                            'city' => $cityData['name'],
                        ]);
                    }
                }
            }
        }
    }
}