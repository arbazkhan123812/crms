<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_calls', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');
            $table->string('call_type', 50);
            $table->string('call_purpose')->nullable();
            $table->text('notes')->nullable();
            $table->string('duration', 20)->nullable();
            $table->string('status', 50)->default('completed');
            $table->dateTime('call_date');
            $table->foreignId('called_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('call_owner')->constrained('users');
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('call_owner');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_calls');
    }
};
