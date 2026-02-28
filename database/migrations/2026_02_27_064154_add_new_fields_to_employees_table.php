<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->string('cnic')->nullable()->unique();
            $table->string('religion')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('whatsapp_number')->nullable();

            // Address fields
            $table->string('present_address_line1')->nullable();
            $table->string('present_address_line2')->nullable();
            $table->string('permanent_address_line1')->nullable();
            $table->string('permanent_address_line2')->nullable();

            // Work information
            $table->string('work_location')->nullable();
            $table->string('employee_level')->nullable();
            $table->boolean('is_reporting_manager')->default(false);
            $table->decimal('ctc', 15, 2)->nullable();
            $table->string('seat_location')->nullable();
            $table->string('extension')->nullable();
            $table->enum('experience_status', ['fresher','experienced'])->default('fresher');

            // Personal information
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('hobbies')->nullable();
            $table->enum('marital_status', ['single','married','divorced','widowed'])->nullable();
            $table->string('blood_group')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_relation')->nullable();

            // Bank information
            $table->string('bank_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable();

            // Salary components
            $table->decimal('basic_salary',15,2)->nullable();
            $table->decimal('hra',15,2)->nullable();
            $table->decimal('da',15,2)->nullable();
            $table->decimal('conveyance',15,2)->nullable();
            $table->decimal('medical_allowance',15,2)->nullable();
            $table->decimal('special_allowance',15,2)->nullable();

            // Probation and notice period
            $table->integer('probation_period')->default(6)->nullable();
            $table->integer('notice_period')->default(30)->nullable();

            // Exit reason
            $table->text('exit_reason')->nullable();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'full_name','cnic','religion','alternate_phone','whatsapp_number',
                'present_address_line1','present_address_line2','permanent_address_line1','permanent_address_line2',
                'work_location','employee_level','is_reporting_manager','ctc','seat_location','extension',
                'experience_status','father_name','mother_name','hobbies','marital_status','blood_group',
                'emergency_contact_name','emergency_relation','bank_name','account_holder_name','account_number',
                'basic_salary','hra','da','conveyance','medical_allowance','special_allowance',
                'probation_period','notice_period','exit_reason'
            ]);
        });
    }
};