<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('account_owner')->nullable()->after('id');
            $table->string('account_site')->nullable()->after('name');
            $table->foreignId('parent_account_id')->nullable()->after('account_site')->constrained('accounts')->nullOnDelete();
            $table->string('account_number')->nullable()->after('parent_account_id');
            $table->string('rating')->nullable()->after('annual_revenue');
            $table->string('fax')->nullable()->after('phone');
            $table->string('ticker_symbol')->nullable()->after('website');
            $table->string('ownership')->nullable()->after('ticker_symbol');
            $table->string('sic_code')->nullable()->after('no_of_employees');
            $table->text('shipping_street')->nullable()->after('zip_code');
            $table->string('shipping_city')->nullable()->after('shipping_street');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_code')->nullable()->after('shipping_state');
            $table->string('shipping_country')->nullable()->after('shipping_code');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('account_owner')->nullable()->after('account_id');
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('account_owner');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['parent_account_id']);
            $table->dropColumn([
                'account_owner',
                'account_site',
                'parent_account_id',
                'account_number',
                'rating',
                'fax',
                'ticker_symbol',
                'ownership',
                'sic_code',
                'shipping_street',
                'shipping_city',
                'shipping_state',
                'shipping_code',
                'shipping_country',
            ]);
        });
    }
};
