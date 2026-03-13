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
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->unique();
            $table->foreignId('salary_template_id')->constrained();
            $table->decimal('basic', 15, 2);
            $table->decimal('hra', 15, 2)->nullable();
            $table->decimal('da', 15, 2)->nullable();
            $table->decimal('conveyance', 15, 2)->nullable();
            $table->decimal('medical', 15, 2)->nullable();
            $table->decimal('special', 15, 2)->nullable();
            $table->decimal('pf', 15, 2)->nullable();
            $table->decimal('esi', 15, 2)->nullable();
            $table->decimal('pt', 15, 2)->nullable();
            $table->decimal('tds', 15, 2)->nullable();
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('net_salary', 15, 2);
            $table->json('other_earnings')->nullable();
            $table->json('other_deductions')->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('employee_salarie');
    }
};
