<?php

namespace App\Classes;

use App\Models\Cities;
use App\Models\Countries;
use App\Models\States;

class CountryStateCity
{

    public function __construct(Countries $country)
    {
        $this->country = $country;
    }

    public function getCountry(){
        try {
            
            // $country = $this->country->getCountry();
            $country = Countries::get();
          
            if (isset($country) && !empty($country)){
                return response()->json(['status'=>true,'message'=>"country get success",'data'=>$country])->setStatusCode(200);
            }
            return response()->json(['status'=>false,'message'=>"error while get country"])->setStatusCode(400);
        }catch (\Exception $ex){
            return response()->json(['status'=>false,'message'=>"internal server error"])->setStatusCode(500);
        }
    }

    public function getState($country_id){
       
        try {
         
            // $stateModel = new States();
            // $state = $stateModel->getState($country_id);
            $state = States::where('country_id',$country_id)->get();

            if (isset($state) && !empty($state)){
                return response()->json(['status'=>true,'message'=>"state get success",'data'=>$state])->setStatusCode(200);
            }
            return response()->json(['status'=>false,'message'=>"error while get state"])->setStatusCode(400);
        }catch (\Exception $ex){
            return response()->json(['status'=>false,'message'=>"internal server error"])->setStatusCode(500);
        }
    }

    public function getCity($region_id){
        try {
            
            // $cityModel = new Cities();
            // $city = $cityModel->getCity($region_id);
            $city = Cities::where('state_id',$region_id)->get();
            if (isset($city) && !empty($city)){
                return response()->json(['status'=>true,'message'=>"city get success",'data'=>$city])->setStatusCode(200);
            }
            return response()->json(['status'=>false,'message'=>"error while get city"])->setStatusCode(400);
        }catch (\Exception $ex){
            return response()->json(['status'=>false,'message'=>"internal server error"])->setStatusCode(500);
        }
    }
}
