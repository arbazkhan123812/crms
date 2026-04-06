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
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('type'); // call, email, meeting, task, note
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index('lead_id');
            $table->index('type');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_activities');
    }
};
