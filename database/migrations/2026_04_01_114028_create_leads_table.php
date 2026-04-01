<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            
            // Lead Image
            $table->string('lead_image')->nullable();
            
            // Lead Information
            $table->unsignedBigInteger('lead_owner');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('lead_source')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('lead_status')->nullable();
            $table->integer('no_of_employees')->nullable();
            
            // Address Information
            $table->string('street')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            
            // Description Information
            $table->text('description')->nullable();
            
            $table->timestamps();
            
            $table->foreign('lead_owner')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
};