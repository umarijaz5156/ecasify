<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cases', function (Blueprint $table) {

            $table->string('name');
            $table->date('open_date');
            $table->date('close_date')->nullable();
            $table->string('case_stage')->nullable();
            $table->string('practice_area')->nullable();

            $table->string('location_of_accident')->nullable();

            $table->string('statute_of_limitations')->nullable();
            
            $table->string('intersection')->nullable();
            $table->string('coordinates')->nullable();

            $table->string('injury_type')->nullable();
            $table->date('incident_date')->nullable();
            $table->string('case_manager')->nullable();
            $table->string('file_location')->nullable();


            $table->string('first_party_company_name')->nullable();
            $table->string('first_party_policy_name')->nullable();
            $table->string('first_party_insurance_phone_number')->nullable();
            $table->string('first_party_name')->nullable();
            $table->string('first_party_phone_number')->nullable();
            $table->string('first_party_policy_limits')->nullable();
            $table->string('first_insured_name')->nullable();
            $table->string('first_party_claim_number')->nullable();
            $table->string('first_party_adjuster')->nullable();
            $table->string('first_party_email')->nullable();
            $table->string('first_party_fax')->nullable();

            $table->string('third_party_company_name')->nullable();
            $table->string('third_party_policy_name')->nullable();
            $table->string('third_party_insurance_phone_number')->nullable();
            $table->string('third_party_name')->nullable();
            $table->string('third_party_phone_number')->nullable();
            $table->string('third_party_policy_limits')->nullable();
            $table->string('third_insured_name')->nullable();
            $table->string('third_party_claim_number')->nullable();
            $table->string('third_party_adjuster')->nullable();
            $table->string('third_party_email')->nullable();
            $table->string('third_party_fax')->nullable();


            $table->string('first_party_driver_name')->nullable();
            $table->integer('first_party_vehicle_year')->nullable();
            $table->string('first_party_vehicle_model')->nullable();
            $table->string('first_party_customer_type')->nullable();
            $table->string('first_party_passenger_name')->nullable();
            $table->string('first_party_vehicle_make')->nullable();
            $table->string('first_party_vehicle_license')->nullable();
            $table->string('first_party_airbags_developed')->nullable();
            $table->string('first_party_seat_belts_worn')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone')->nullable();
            

            $table->string('third_party_driver_name')->nullable();
            $table->integer('third_party_vehicle_year')->nullable();
            $table->string('third_party_vehicle_model')->nullable();
            $table->string('third_party_customer_type')->nullable();
            $table->string('third_party_passenger_name')->nullable();
            $table->string('third_party_vehicle_make')->nullable();
            $table->string('third_party_vehicle_license')->nullable();
            $table->string('third_party_airbags_developed')->nullable();
            $table->string('third_party_seat_belts_worn')->nullable();
           

            $table->string('police_report')->nullable();
            $table->string('recorded_statement')->nullable();
            $table->string('recorded_statement_description')->nullable();
            $table->string('other_name')->nullable();
            $table->string('other_phone_number')->nullable();
            $table->string('other_email_address')->nullable();
            $table->string('other_fax')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cases', function (Blueprint $table) {
            
        });
    }
};
