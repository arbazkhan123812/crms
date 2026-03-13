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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->integer('month');
            $table->integer('year');
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
            $table->json('other_earnings')->nullable();
            $table->json('other_deductions')->nullable();
            $table->integer('working_days');
            $table->integer('present_days');
            $table->integer('absent_days');
            $table->integer('leave_days');
            $table->decimal('gross_salary', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('net_salary', 15, 2);
            $table->enum('status', ['draft', 'processed', 'paid', 'cancelled'])->default('draft');
            $table->date('processed_date')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->date('payment_date')->nullable();
            $table->string('payment_mode')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
};
