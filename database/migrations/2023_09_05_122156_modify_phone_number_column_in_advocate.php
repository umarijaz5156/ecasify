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
        Schema::table('advocates', function (Blueprint $table) {
            $table->string('phone_number', 20)->change(); // Adjust the size (e.g., 20) as needed

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advocates', function (Blueprint $table) {
            $table->string('phone_number', 15)->change(); // Revert the size change if needed
        });
    }
};
