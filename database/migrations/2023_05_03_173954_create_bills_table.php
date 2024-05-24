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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_from');
            $table->integer('advocate')->nullable();
            $table->string('custom_advocate')->nullable();
            $table->string('custom_address')->nullable();
            $table->string('custom_email')->nullable();
            $table->string('title');
            $table->string('bill_number');
            $table->string('due_date');
            $table->longText('items')->nullable();
            $table->string('subtotal');
            $table->string('total_tax');
            $table->string('total_amount');
            $table->longText('description')->nullable();
            $table->integer('created_by');
            $table->string('bill_to');
            $table->string('status')->default('PENDING');
            $table->string('due_amount')->default(0);
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
        Schema::dropIfExists('bills');
    }
};
