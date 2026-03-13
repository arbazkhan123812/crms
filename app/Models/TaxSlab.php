<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSlab extends Model
{
    protected $fillable = [
        'financial_year',
        'min_income',
        'max_income',
        'tax_rate',
        'fixed_amount'
    ];

    protected $casts = [
        'min_income' => 'decimal:2',
        'max_income' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'fixed_amount' => 'decimal:2'
    ];

    public static function calculateTax($annualIncome, $financialYear)
    {
        $slabs = self::where('financial_year', $financialYear)
            ->orderBy('min_income')
            ->get();

        $tax = 0;
        foreach ($slabs as $slab) {
            if ($annualIncome > $slab->min_income) {
                $taxableAmount = min($annualIncome, $slab->max_income) - $slab->min_income;
                $tax += ($taxableAmount * $slab->tax_rate / 100) + $slab->fixed_amount;
            }
        }

        return $tax;
    }
}