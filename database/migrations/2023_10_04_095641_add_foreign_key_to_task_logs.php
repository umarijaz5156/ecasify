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
        Schema::table('task_logs', function (Blueprint $table) {
            $table->foreign('task_id')
            ->references('id')
            ->on('tasks')
            ->onDelete('cascade')
            ->name('custom_task_logs_task_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_logs', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });
    }
};
