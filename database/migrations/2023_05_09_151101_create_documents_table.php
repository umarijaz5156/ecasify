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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('term');
            $table->integer('type');
            $table->string('judgement_date');
            $table->string('expiry_date');
            $table->string('purpose');
            $table->string('first_party');
            $table->string('second_party');
            $table->string('headed_by');
            $table->string('description');
            $table->string('file');
            $table->string('doc_size');
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
        Schema::dropIfExists('documents');
    }
};
