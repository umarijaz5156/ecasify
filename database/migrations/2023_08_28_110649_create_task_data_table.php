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
        Schema::create('task_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cases_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            $table->foreign('cases_id')->references('id')->on('cases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taskData');
    }
};
