<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('interviewer_id')->constrained('users');
            $table->enum('round', ['screening', 'technical_1', 'technical_2', 'hr', 'manager', 'final'])->default('screening');
            $table->datetime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('mode', ['online', 'offline', 'phone'])->default('online');
            $table->string('location_or_link')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->float('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('result', ['pending', 'selected', 'rejected', 'on_hold'])->default('pending');
            $table->timestamps();

            $table->index(['candidate_id', 'status']);
            $table->index('scheduled_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('interviews');
    }
};