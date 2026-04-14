<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_meetings', function (Blueprint $table) {
            $table->string('location_type', 50)->nullable()->after('meeting_type');
            $table->dateTime('starts_at')->nullable()->after('meeting_date');
            $table->dateTime('ends_at')->nullable()->after('starts_at');
        });

        DB::statement('ALTER TABLE crm_meetings MODIFY entity_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE crm_meetings SET entity_id = 0 WHERE entity_id IS NULL');
        DB::statement('ALTER TABLE crm_meetings MODIFY entity_id BIGINT UNSIGNED NOT NULL');

        Schema::table('crm_meetings', function (Blueprint $table) {
            $table->dropColumn([
                'location_type',
                'starts_at',
                'ends_at',
            ]);
        });
    }
};
