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
        Schema::create('lead_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('meeting_type')->default('virtual'); // virtual, physical, phone
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable(); // For virtual meetings (Zoom, Google Meet, etc.)
            $table->dateTime('meeting_date');
            $table->string('duration')->nullable(); // e.g., "1 hour", "30 minutes"
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, rescheduled
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index('lead_id');
            $table->index('assigned_to');
            $table->index('meeting_date');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_meetings');
    }
};
