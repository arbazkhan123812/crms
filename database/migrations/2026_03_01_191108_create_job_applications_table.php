<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['applied', 'under_review', 'shortlisted', 'rejected'])->default('applied');
            $table->text('cover_note')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'candidate_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_applications');
    }
};