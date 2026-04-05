<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('call_type')->default('outbound'); // inbound, outbound
            $table->string('call_purpose')->nullable();
            $table->text('notes')->nullable();
            $table->string('duration')->nullable(); // e.g., "5:30"
            $table->string('status')->default('completed'); // completed, missed, voicemail, no_answer
            $table->dateTime('call_date');
            $table->foreignId('called_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('lead_id');
            $table->index('called_by');
            $table->index('call_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_calls');
    }
};