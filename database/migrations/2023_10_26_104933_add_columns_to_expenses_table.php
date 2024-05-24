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
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->text('attachment')->nullable();
            $table->string('description')->nullable();
            // make coulmn nullable
            $table->string('case')->nullable()->change();
            $table->string('date')->nullable()->change();
            $table->string('particulars')->nullable()->change();
            $table->bigInteger('money')->nullable()->change();
            $table->string('method')->nullable()->change();
            $table->longText('notes')->nullable()->change();
            $table->integer('member')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('type');
            $table->dropColumn('attachment');
            $table->dropColumn('description');
            // revese nullable coulmn to not nullable
            $table->string('case')->nullable(false)->change();
            $table->string('date')->nullable(false)->change();
            $table->string('particulars')->nullable(false)->change();
            $table->bigInteger('money')->nullable(false)->change();
            $table->string('method')->nullable(false)->change();
            $table->longText('notes')->nullable(false)->change();
            $table->integer('member')->nullable(false)->change();
            
        });
    }
};
