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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('current_company')->nullable();
            $table->string('current_designation')->nullable();
            $table->integer('experience_years')->default(0);
            $table->decimal('current_ctc', 15, 2)->nullable();
            $table->decimal('expected_ctc', 15, 2)->nullable();
            $table->text('skills')->nullable();
            $table->text('qualifications')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('cover_letter_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('source', ['website', 'linkedin', 'referral', 'job_portal', 'walk_in', 'other'])->default('website');
            $table->enum('status', [
                'applied', 'screening', 'shortlisted', 
                'interview_scheduled', 'interviewed', 
                'technical_round', 'hr_round', 
                'selected', 'offered', 'hired', 
                'rejected', 'withdrawn'
            ])->default('applied');
            $table->text('remarks')->nullable();
            $table->json('interview_feedback')->nullable();
            $table->float('rating')->nullable();
            $table->date('applied_date');
            $table->date('available_from_date')->nullable();
            $table->string('notice_period')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_id', 'status']);
            $table->index('email');
            $table->index('applied_date');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidates');
    }
};
