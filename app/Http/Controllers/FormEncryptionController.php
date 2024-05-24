<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FormEncryption;



class FormEncryptionController extends Controller
{
    public function index()
    {
        $motorVehicleFormFields = array(
            'general' => array(
                            'location_of_accident' => 'Location of Accident',
                            'intersection' => 'Intersection' ,
                            'coordinates' => 'coordinates',
                        ),
            'insurance' => array(
                            'case_manager' => 'Case Manager',
                            'file_location' => 'File Location'
                        ),
            'col-md-6@col-md-6@first_party' => array(
                            'first_party_company_name'  => 'Company Name',
                            'first_party_policy_name' => 'Policy Name' ,
                            'first_party_insurance_phone_number'  => 'Insurance Phone Number',
                            'first_party_name' => 'Name',
                            'first_party_phone_number'  => 'Phone Number',
                            'first_party_policy_limits' => 'Policy Limits',
                            'first_insured_name' => 'Insured Name(s)',
                            'first_party_claim_number' => 'Claim Number',
                            'first_party_adjuster' => 'Adjuster',
                            'first_party_email' => 'Email',
                            'first_party_fax' => 'Fax Number',
                        ),
            'col-md-6@col-md-6@third_party' => array(
                            'third_party_company_name' => 'Company Name',
                            'third_party_policy_name' => 'Policy Name',
                            'third_party_insurance_phone_number' => 'Insurance Phone Number',
                            'third_party_name' => 'Name',
                            'third_party_phone_number' => 'Phone Number',
                            'third_party_policy_limits' => 'Policy Limits',
                            'third_insured_name' => 'Insured Name(s)',
                            'third_party_claim_number' => 'Claim Number',
                            'third_party_adjuster' => 'Adjuster',
                            'third_party_email' => 'Email',
                            'third_party_fax' => 'Fax Number',
                        ),
            'col-md-6@col-md-6@first_party_vehicle_information' => array(
                        'first_party_driver_name' => 'Driver Name',
                        'first_party_vehicle_model' => 'Vehicle Model',
                        'first_party_vehicle_make' => 'Vehicle Make',
                        'first_party_vehicle_license' => 'Vehicle License Plate Number',
                        'first_party_passenger_name' => 'Passenger Name',
                        'first_party_customer_type' =>'Are you driver or Passenger?',
                        'first_party_airbags_developed' => 'Airbags Deployed?',
                        'first_party_seat_belts_worn' =>'Seat belts worn?',
                    ),
            'col-md-6@col-md-6@third_party_vehicle_information' => array(
                        'third_party_driver_name' => 'Driver Name',
                        'third_party_vehicle_model' => 'Vehicle Model',
                        'third_party_vehicle_make' => 'Vehicle Make',
                        'third_party_vehicle_license' => 'Vehicle License Plate Number',
                        'third_party_passenger_name' =>'Passenger Name',
                        'third_party_customer_type' =>'Are you driver or Passenger?',
                        'third_party_airbags_developed' => 'Airbags Deployed?',
                        'third_party_seat_belts_worn' =>'Seat belts worn?',
                    ),
            'emergency_contact' => array(
                        'emergency_name'=> 'Name',
                        'emergency_phone' => 'Phone Number'
                    ),
            'other' => array(
                        'police_report' => 'Police Report Number',
                        'recorded_statement' => 'Recorded Statements',
                        'other_name' => 'Name',
                        'other_email_address' => 'Phone Number',
                        'other_phone_number' => 'Email Address',
                        'other_fax' => 'Fax',
                    ),
        );
    
        $slipAndFallForm = array(
            'client_information' => array(
                        'fall_c_name' => 'Name',
                        'fall_c_gender' =>  'Gender',
                        'fall_c_marital_status' => 'Martial Status',
                        'fall_c_spouse_name' => 'Spouse Name',
                        'fall_c_emergency_contact_name' => 'Emergency Contact Name',
                        'fall_c_emergency_contact_number' => 'Emergency Contact Number',
                        'fall_c_dob' => 'Date of Birth',
                        'fall_c_social_security' => 'Social Security',
                        'fall_c_address' => 'Address',
                        'fall_c_phone' => 'Phone',
                        'fall_c_email' => 'Email',
                        'fall_c_driver_license' => 'Driver License #',
                        'fall_c_health_insurance' => 'Health Insurance, Medicare / Medicaid / Tricare?',
                        'fall_c_id' => 'ID',
                    ),
            'incident_information' => array(
                        'fall_ii_location_of_incident' => 'Location of Incident',
                        'fall_ii_address' => 'Address',
                        'fall_ii_maps_link' => 'Maps Link/Coordinates',
                        'fall_ii_cause_incident' => 'Cause of the Incident:',
                        'fc_police_department' => 'Police Department:',
                        'fc_incident_report' => 'Incident Report #',
                    ),
            'col-md-6@col-md-6@insurance_(First_party)' => array(
                        'fall_ifp_company_name' => 'Company Name',
                        'fall_ifp_insured_name' => 'Insured Name',
                        'fall_ifp_poilicy' => 'Group/Policy #',
                        'fall_ifp_member' => 'Member',
                        'fall_ifp_claim' => 'Claim #',
                        'fall_ifp_insurance_phone' => 'Insurance Phone',
                    ),
            'col-md-6@col-md-6@insurance_(Third_party)' => array(
                    'fall_itp_company_name' => 'Company Name',
                    'fall_itp_insured_name' => 'Insured Name',
                    'fall_itp_poilicy' => 'Group/Policy #',
                    'fall_itp_member' => 'Member',
                    'fall_itp_claim' => 'Claim #',
                    'fall_itp_insurance_phone' => 'Insurance Phone',
                    ),
            'col-md-6@col-md-6@adjuster_(First_party)' => array(
                    'fall_ifp_adjuster_name' => 'Name',
                    'fall_ifp_adjuster_email' => 'Email',
                    'fall_ifp_adjuster_phone' => 'Phone',
                    'fall_ifp_adjuster_fax' => 'Fax Number',
                    'fall_ifp_adjuster_policy_limits' => 'Policy Limits',
                    
            ),
            'col-md-6@col-md-6@adjuster_(Third_party)' => array(
                    'fall_itp_adjuster_name' => 'Name',
                    'fall_itp_adjuster_email' => 'Email',
                    'fall_itp_adjuster_phone' => 'Phone',
                    'fall_itp_adjuster_fax' => 'Fax Number',
                    'fall_itp_adjuster_policy_limits' => 'Policy Limits',
            ),
            'other' => array(
                    'fall_o_incident_report' => 'Accident or incident Report',
                    'fall_o_opponent_counsel' => 'Opponent Counsel',
                    'fall_o_name' => 'Name',
                    'fall_o_phone' => 'Phone',
                    'fall_o_email' => 'Email',
                    'fall_o_fax' => 'Fax',
            ),
    
    
        );

    // Retrieve form data by form name
            $motorVehicleFormData = json_decode(FormEncryption::where('form_name', 'Motor Vehicle Accident')->value('form_array'), true);
            $slipAndFallFormData = json_decode(FormEncryption::where('form_name', 'Slip & Fall')->value('form_array'), true);
            

            return view('form_encryption.index', compact('motorVehicleFormFields','slipAndFallForm','motorVehicleFormData','slipAndFallFormData'));
    }

    //  store
    public function store(Request $request)
    {
        // check if $reques->form type = motor_vehicle and check form_encryption == request->form_name 
        // if true then update else create
        $form = FormEncryption::where('form_name', $request->form_name)->first();
        // $json_array = $request->all() in json
        $json_array = json_encode($request->all());
        // dd($json_array);
        if($form){
            $form->update([
                'form_name' => $request->form_name,
                'form_array' => $json_array,
            ]);
        }else{
            FormEncryption::create([
                'form_name' => $request->form_name,
                'form_array' => $json_array,
            ]);
        }
        return redirect()->back()->with('success', 'Form Data Saved Successfully');

    }
}
