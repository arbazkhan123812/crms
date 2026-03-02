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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('designation_id')->constrained()->onDelete('cascade');
            $table->integer('vacancies');
            $table->text('description');
            $table->text('requirements');
            $table->text('responsibilities')->nullable();
            $table->text('qualifications')->nullable();
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'lead', 'manager'])->default('mid');
            $table->enum('job_type', ['full_time', 'part_time', 'contract', 'internship', 'remote'])->default('full_time');
            $table->enum('status', ['draft', 'published', 'closed', 'on_hold'])->default('draft');
            $table->date('posted_date');
            $table->date('closing_date');
            $table->decimal('min_salary', 15, 2)->nullable();
            $table->decimal('max_salary', 15, 2)->nullable();
            $table->string('location')->nullable();
            $table->json('skills_required')->nullable();
            $table->json('benefits')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'closing_date']);
            $table->index('posted_date');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};
