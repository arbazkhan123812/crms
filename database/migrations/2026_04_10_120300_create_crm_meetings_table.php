<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('meeting_type', 50)->nullable();
            $table->string('location')->nullable();
            $table->string('meeting_link', 500)->nullable();
            $table->dateTime('meeting_date');
            $table->string('duration', 50)->nullable();
            $table->string('status', 50)->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_meetings');
    }
};
