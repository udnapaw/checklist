<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectdomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objectdomains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->foreign('checklist_id')->references('id')->on('checklists');
            $table->string('object_domain');            
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by');
            $table->foreign('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('objectdomains');
    }
}
