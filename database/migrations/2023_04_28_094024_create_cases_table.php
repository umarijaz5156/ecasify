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
        Schema::create('cases', function (Blueprint $table) {

            $table->id();
            $table->integer('court')->default(1);
            $table->integer('highcourt')->nullable();
            $table->integer('bench')->nullable();
            $table->string('casetype')->nullable();
            $table->string('casenumber')->nullable();
            $table->bigInteger('diarybumber')->nullable();
            $table->integer('year')->nullable();
            $table->string('case_number')->nullable();
            $table->date('filing_date')->nullable();
            $table->bigInteger('court_hall')->nullable();
            $table->bigInteger('floor')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('before_judges')->nullable();
            $table->string('referred_by')->nullable();
            $table->string('section')->nullable();
            $table->string('priority')->nullable();
            $table->string('under_acts')->nullable();
            $table->string('under_sections')->nullable();
            $table->string('FIR_police_station')->nullable();
            $table->bigInteger('FIR_number')->nullable();
            $table->integer('FIR_year')->nullable();
            $table->string('your_advocates')->nullable();
            $table->string('your_team')->nullable();
            $table->longText('opponents')->nullable();
            $table->longText('opponent_advocates')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cases');
    }
};
