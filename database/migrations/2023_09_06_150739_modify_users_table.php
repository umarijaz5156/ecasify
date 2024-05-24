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
        Schema::table('users', function (Blueprint $table) {
            // Change the default value of the 'type' column to 'company'
            $table->string('type')->default('company')->change();

            // Add a new 'role_title' column
            $table->string('role_title', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert the 'type' column's default value in the 'down' method, if needed
            $table->string('type')->default('user')->change();
            $table->dropColumn('role_title');
        });
    }
};
