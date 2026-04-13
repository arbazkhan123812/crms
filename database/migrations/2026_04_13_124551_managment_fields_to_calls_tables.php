<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_calls', function (Blueprint $table) {
            $table->string('related_to_type', 50)->nullable()->after('call_owner');
            $table->unsignedBigInteger('related_to_id')->nullable()->after('related_to_type');
            $table->string('outgoing_call_status', 50)->nullable()->after('related_to_id');

            $table->index(['related_to_type', 'related_to_id']);
        });

        Schema::table('crm_calls', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('call_purpose');
            $table->dateTime('start_time')->nullable()->after('call_date');
            $table->dateTime('end_time')->nullable()->after('start_time');
            $table->string('related_to_type', 50)->nullable()->after('call_owner');
            $table->unsignedBigInteger('related_to_id')->nullable()->after('related_to_type');
            $table->string('outgoing_call_status', 50)->nullable()->after('related_to_id');

            $table->index(['related_to_type', 'related_to_id']);
        });
    }

    public function down(): void
    {
        Schema::table('lead_calls', function (Blueprint $table) {
            $table->dropIndex(['related_to_type', 'related_to_id']);
            $table->dropColumn([
                'subject',
                'start_time',
                'end_time',
                'related_to_type',
                'related_to_id',
                'outgoing_call_status',
            ]);
        });

        Schema::table('crm_calls', function (Blueprint $table) {
            $table->dropIndex(['related_to_type', 'related_to_id']);
            $table->dropColumn([
                'subject',
                'start_time',
                'end_time',
                'related_to_type',
                'related_to_id',
                'outgoing_call_status',
            ]);
        });
    }
};
