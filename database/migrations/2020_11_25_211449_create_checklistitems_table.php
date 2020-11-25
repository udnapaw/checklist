<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklistitems', function (Blueprint $table) {
            $table->id();  
            $table->unsignedBigInteger('checklist_id');
            $table->foreign('checklist_id')->references('id')->on('checklists');          
            $table->string('description');
            $table->dateTime('due')->nullable();            
            $table->integer('urgency'); 
            $table->boolean('is_completed');    
            $table->dateTime('completed_at')->nullable();       
            $table->dateTime('deleted_at')->nullable();
            $table->string('asignee_id');
            $table->integer('task_id');
            $table->string('link');
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
        Schema::dropIfExists('checklistitems');
    }
}
