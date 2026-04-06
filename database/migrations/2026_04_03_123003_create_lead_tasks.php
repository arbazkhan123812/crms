<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->constrained('users');
            $table->dateTime('due_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('lead_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_tasks');
    }
};