<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Account Information Fields
            if (!Schema::hasColumn('accounts', 'account_owner')) {
                $table->foreignId('account_owner')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('accounts', 'account_site')) {
                $table->string('account_site')->nullable()->after('name');
            }
            if (!Schema::hasColumn('accounts', 'parent_account_id')) {
                $table->foreignId('parent_account_id')->nullable()->after('account_site')->constrained('accounts')->nullOnDelete();
            }
            if (!Schema::hasColumn('accounts', 'account_number')) {
                $table->string('account_number')->nullable()->after('parent_account_id');
            }
            if (!Schema::hasColumn('accounts', 'rating')) {
                $table->string('rating')->nullable()->after('annual_revenue');
            }
            if (!Schema::hasColumn('accounts', 'fax')) {
                $table->string('fax')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('accounts', 'ticker_symbol')) {
                $table->string('ticker_symbol')->nullable()->after('website');
            }
            if (!Schema::hasColumn('accounts', 'ownership')) {
                $table->string('ownership')->nullable()->after('ticker_symbol');
            }
            if (!Schema::hasColumn('accounts', 'sic_code')) {
                $table->string('sic_code')->nullable()->after('no_of_employees');
            }
            
            // Shipping Address Fields
            if (!Schema::hasColumn('accounts', 'shipping_street')) {
                $table->string('shipping_street')->nullable()->after('street');
            }
            if (!Schema::hasColumn('accounts', 'shipping_city')) {
                $table->string('shipping_city')->nullable()->after('shipping_street');
            }
            if (!Schema::hasColumn('accounts', 'shipping_state')) {
                $table->string('shipping_state')->nullable()->after('shipping_city');
            }
            if (!Schema::hasColumn('accounts', 'shipping_code')) {
                $table->string('shipping_code')->nullable()->after('shipping_state');
            }
            if (!Schema::hasColumn('accounts', 'shipping_country')) {
                $table->string('shipping_country')->nullable()->after('shipping_code');
            }
        });
    }

    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $columns = [
                'account_owner', 'account_site', 'parent_account_id', 'account_number',
                'rating', 'fax', 'ticker_symbol', 'ownership', 'sic_code',
                'shipping_street', 'shipping_city', 'shipping_state', 'shipping_code', 'shipping_country'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('accounts', $column)) {
                    if (in_array($column, ['account_owner', 'parent_account_id'])) {
                        $table->dropForeign([$column]);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};