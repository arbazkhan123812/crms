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
            $table->foreignId('department_id')->constrained();
            $table->foreignId('designation_id')->constrained();
            $table->integer('vacancies');
            $table->text('description');
            $table->text('requirements');
            $table->enum('type', ['full_time', 'part_time', 'contract', 'internship']);
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->date('posted_date');
            $table->date('closing_date');
            $table->decimal('min_salary', 15, 2)->nullable();
            $table->decimal('max_salary', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('resume')->nullable();
            $table->enum('status', ['applied', 'shortlisted', 'interviewed', 'hired', 'rejected'])->default('applied');
            $table->integer('experience_years')->nullable();
            $table->text('skills')->nullable();
            $table->date('applied_date');
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
        Schema::dropIfExists('jobs');
    }
};
