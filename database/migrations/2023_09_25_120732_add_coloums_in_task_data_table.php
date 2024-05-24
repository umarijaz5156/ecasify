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
        Schema::table('task_data', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->string('priority')->nullable();
            $table->string('task_team')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_data', function (Blueprint $table) {
            Schema::dropIfExists('status');
            Schema::dropIfExists('priority');
            Schema::dropIfExists('task_team'); 
        });
    }
};
