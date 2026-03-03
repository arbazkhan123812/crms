<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('designations', function (Blueprint $table) {
            // Shift timings
            $table->time('shift_start_time')->nullable()->after('max_salary');
            $table->time('shift_end_time')->nullable()->after('shift_start_time');
            
            // Thresholds
            $table->integer('late_threshold')->default(15)->after('shift_end_time')->comment('Minutes after start time considered late');
            $table->integer('early_exit_threshold')->default(15)->after('late_threshold')->comment('Minutes before end time considered early exit');
            $table->integer('overtime_threshold')->default(0)->after('early_exit_threshold')->comment('Minutes after end time considered overtime');
            
            // Break duration
            $table->integer('break_duration')->default(60)->after('overtime_threshold')->comment('Break duration in minutes');
            
            // Night shift flag
            $table->boolean('is_night_shift')->default(false)->after('break_duration');
            
            // Working days (JSON to store which days are working)
            $table->json('working_days')->nullable()->after('is_night_shift');
            
            // Grace period
            $table->integer('grace_period_minutes')->default(0)->after('working_days')->comment('Grace period before marking late');
            
            // Overtime rate
            $table->decimal('overtime_rate', 5, 2)->default(1.5)->after('grace_period_minutes')->comment('Overtime multiplier');
            
            // Flexible timing options
            $table->boolean('has_flexible_timing')->default(false)->after('overtime_rate');
            $table->time('flexible_start_from')->nullable()->after('has_flexible_timing');
            $table->time('flexible_start_to')->nullable()->after('flexible_start_from');
            $table->time('flexible_end_from')->nullable()->after('flexible_start_to');
            $table->time('flexible_end_to')->nullable()->after('flexible_end_from');
            
            // Half-day timing
            $table->time('half_day_start_time')->nullable()->after('flexible_end_to');
            $table->time('half_day_end_time')->nullable()->after('half_day_start_time');
            
            // Weekend settings
            $table->boolean('is_weekend_included')->default(false)->after('half_day_end_time');
        });
    }

    public function down()
    {
        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn([
                'shift_start_time',
                'shift_end_time',
                'late_threshold',
                'early_exit_threshold',
                'overtime_threshold',
                'break_duration',
                'is_night_shift',
                'working_days',
                'grace_period_minutes',
                'overtime_rate',
                'has_flexible_timing',
                'flexible_start_from',
                'flexible_start_to',
                'flexible_end_from',
                'flexible_end_to',
                'half_day_start_time',
                'half_day_end_time',
                'is_weekend_included'
            ]);
        });
    }
};