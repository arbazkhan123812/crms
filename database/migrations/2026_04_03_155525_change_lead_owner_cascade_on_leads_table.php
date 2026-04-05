<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            // 1. Pehle purani foreign key drop karein
            // Laravel default name convention: table_column_foreign
            $table->dropForeign(['lead_owner']); 

            // 2. Ab dobara foreign key lagayein 'restrict' ya 'set null' ke saath
            $table->foreign('lead_owner')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict'); // Isse user delete nahi hoga agar uski leads hain
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            // Rollback ke liye wapas cascade laga dein
            $table->dropForeign(['lead_owner']);
            $table->foreign('lead_owner')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};