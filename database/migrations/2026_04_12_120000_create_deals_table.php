<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_owner')->constrained('users');
            $table->decimal('amount', 15, 2)->default(0);
            $table->foreignId('account_id')->constrained('accounts');
            $table->string('stage');
            $table->unsignedTinyInteger('probability')->default(0);
            $table->string('deal_source');
            $table->foreignId('contact_id')->constrained('contacts');
            $table->date('closing_date')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals');
    }
};
