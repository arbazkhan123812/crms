<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Contact Information Fields
            if (!Schema::hasColumn('contacts', 'contact_owner')) {
                $table->foreignId('contact_owner')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('contacts', 'lead_source')) {
                $table->string('lead_source')->nullable()->after('twitter');
            }
            if (!Schema::hasColumn('contacts', 'department')) {
                $table->string('department')->nullable()->after('title');
            }
            if (!Schema::hasColumn('contacts', 'home_phone')) {
                $table->string('home_phone')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('contacts', 'other_phone')) {
                $table->string('other_phone')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('contacts', 'fax')) {
                $table->string('fax')->nullable()->after('home_phone');
            }
            if (!Schema::hasColumn('contacts', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('fax');
            }
            if (!Schema::hasColumn('contacts', 'assistant_phone')) {
                $table->string('assistant_phone')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('contacts', 'secondary_email')) {
                $table->string('secondary_email')->nullable()->after('email');
            }
            
            // Address Fields
            if (!Schema::hasColumn('contacts', 'mailing_street')) {
                $table->string('mailing_street')->nullable()->after('description');
            }
            if (!Schema::hasColumn('contacts', 'mailing_city')) {
                $table->string('mailing_city')->nullable()->after('mailing_street');
            }
            if (!Schema::hasColumn('contacts', 'mailing_state')) {
                $table->string('mailing_state')->nullable()->after('mailing_city');
            }
            if (!Schema::hasColumn('contacts', 'mailing_code')) {
                $table->string('mailing_code')->nullable()->after('mailing_state');
            }
            if (!Schema::hasColumn('contacts', 'mailing_country')) {
                $table->string('mailing_country')->nullable()->after('mailing_code');
            }
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $columns = [
                'contact_owner', 'lead_source', 'department', 'home_phone', 'other_phone',
                'fax', 'date_of_birth', 'assistant_phone', 'secondary_email',
                'mailing_street', 'mailing_city', 'mailing_state', 'mailing_code', 'mailing_country'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('contacts', $column)) {
                    if ($column === 'contact_owner') {
                        $table->dropForeign(['contact_owner']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};