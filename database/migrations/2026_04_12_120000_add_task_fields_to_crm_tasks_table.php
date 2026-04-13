<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_tasks', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('entity_id')->constrained('accounts')->nullOnDelete();
            $table->string('priority', 20)->default('normal')->after('status');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::table('crm_tasks', function (Blueprint $table) {
            $table->dropIndex(['priority']);
            $table->dropConstrainedForeignId('account_id');
            $table->dropColumn('priority');
        });
    }
};
