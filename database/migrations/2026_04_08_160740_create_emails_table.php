<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('from_email');
            $table->string('to_email');
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->string('entity_type'); // account, contact, lead
            $table->unsignedBigInteger('entity_id'); // account_id, contact_id, lead_id
            $table->string('status')->default('sent');
            $table->foreignId('sent_by')->constrained('users');
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index(['entity_type', 'entity_id']);
            $table->index('sent_by');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('emails');
    }
};