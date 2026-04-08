<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lead_calls', function (Blueprint $table) {
            $table->foreignId('call_owner')->nullable()->after('called_by')->constrained('users')->nullOnDelete();
            $table->index('call_owner');
        });
    }

    public function down()
    {
        Schema::table('lead_calls', function (Blueprint $table) {
            $table->dropForeign(['call_owner']);
            $table->dropIndex(['call_owner']);
            $table->dropColumn('call_owner');
        });
    }
};
