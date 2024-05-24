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
      
        
            Schema::table('plan_requests', function (Blueprint $table) {
            $table->dropColumn(['plan_id']);

            $table->string('name', 100)->unique()->nullable();
            $table->float('price')->default(0);
            $table->integer('max_users')->default(0);
            $table->integer('max_advocates')->default(0);
            $table->text('description')->nullable();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_requests', function (Blueprint $table) {

         
            $table->dropColumn('name');
            $table->dropColumn('price');
            $table->dropColumn('duration');
            $table->dropColumn('max_users');
            $table->dropColumn('max_advocates');
            $table->dropColumn('description');


            
        });
    }
};
