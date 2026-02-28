<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('asset_type');
            $table->string('asset_name');
            $table->string('serial_no')->nullable();
            $table->enum('status', ['assigned', 'returned', 'damaged'])->default('assigned');
            $table->date('given_on');
            $table->date('returned_on')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_assets');
    }
};