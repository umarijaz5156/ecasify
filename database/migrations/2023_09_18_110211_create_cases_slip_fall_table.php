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
        Schema::create('cases_slip_fall', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id'); 
            
            // client info (14)
            $table->string('fall_c_name')->nullable();
            $table->string('fall_c_gender')->nullable();
            $table->string('fall_c_marital_status')->nullable();
            $table->string('fall_c_spouse_name')->nullable();
            $table->string('fall_c_emergency_contact_name')->nullable();
            $table->string('fall_c_emergency_contact_number')->nullable();
            $table->string('fall_c_dob')->nullable();
            $table->string('fall_c_social_security')->nullable();
            $table->string('fall_c_address')->nullable();
            $table->string('fall_c_phone')->nullable();
            $table->string('fall_c_email')->nullable();
            $table->string('fall_c_driver_license')->nullable();
            $table->string('fall_c_health_insurance')->nullable();
            $table->string('fall_c_id')->nullable();

            // 3rd party info (8)
            $table->string('fall_tpi_name')->nullable();
            $table->string('fall_tpi_phone')->nullable();
            $table->string('fall_tpi_address')->nullable();
            $table->string('fall_tpi_email')->nullable();
            $table->string('fall_mi_name')->nullable();
            $table->string('fall_mi_phone')->nullable();
            $table->string('fall_mi_address')->nullable();
            $table->string('fall_mi_email')->nullable();

            // Incident Information (10)
            $table->date('fall_ii_incident_date')->nullable();
            $table->string('fall_ii_time')->nullable();
            $table->string('fall_ii_location_of_incident')->nullable();
            $table->string('fall_ii_address')->nullable();
            $table->string('fall_ii_maps_link')->nullable();
            $table->string('fall_ii_cause_incident')->nullable();
            $table->string('fc_police_notified')->nullable();
            $table->string('fc_incident_report_filed')->nullable();
            $table->string('fc_police_department')->nullable();
            $table->string('fc_incident_report')->nullable();

            // Insurance (21)
            $table->string('fall_ifp_company_name')->nullable();
            $table->string('fall_ifp_insured_name')->nullable();
            $table->string('fall_ifp_poilicy')->nullable();
            $table->string('fall_ifp_member')->nullable();
            $table->string('fall_ifp_claim')->nullable();
            $table->string('fall_ifp_insurance_phone')->nullable();
            $table->string('fall_ifp_adjuster_name')->nullable();
            $table->string('fall_ifp_adjuster_email')->nullable();
            $table->string('fall_ifp_adjuster_phone')->nullable();
            $table->string('fall_ifp_adjuster_fax')->nullable();
            $table->string('fall_ifp_adjuster_policy_limits')->nullable();

            $table->string('fall_itp_company_name')->nullable();
            $table->string('fall_itp_insured_name')->nullable();
            $table->string('fall_itp_poilicy')->nullable();
            $table->string('fall_itp_claim')->nullable();
            $table->string('fall_itp_insurance_phone')->nullable();
            $table->string('fall_itp_adjuster_name')->nullable();
            $table->string('fall_itp_adjuster_email')->nullable();
            $table->string('fall_itp_adjuster_phone')->nullable();
            $table->string('fall_itp_adjuster_fax')->nullable();
            $table->string('fall_itp_adjuster_policy_limits')->nullable();

            // witness
            $table->longText('witness')->nullable();

            // other (7)
            $table->string('fall_o_incident_report')->nullable();
            $table->string('fall_o_recorded_statements')->nullable();
            $table->string('fall_o_opponent_counsel')->nullable();
            $table->string('fall_o_name')->nullable();
            $table->string('fall_o_phone')->nullable();
            $table->string('fall_o_email')->nullable();
            $table->string('fall_o_fax')->nullable();




            $table->timestamps();
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cases_slip_fall');
    }
};
