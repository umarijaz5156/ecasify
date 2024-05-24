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
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('timezone')->nullable();
            $table->string('country')->nullable();
            $table->string('mobile_number', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
                
                $table->dropColumn('timezone');
                $table->dropColumn('country');
                $table->bigInteger('mobile_number')->nullable()->change();
        });
    }
};
