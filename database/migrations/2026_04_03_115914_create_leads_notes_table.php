<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads');
            $table->text('note');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('lead_id');
            $table->index('created_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_notes');
    }
};