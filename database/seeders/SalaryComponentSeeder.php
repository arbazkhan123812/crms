<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalaryComponent;

class SalaryComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $components = [
            ['name' => 'Basic Salary', 'code' => 'BASIC', 'type' => 'earning', 'value_type' => 'fixed', 'is_taxable' => true],
            ['name' => 'House Rent Allowance', 'code' => 'HRA', 'type' => 'earning', 'value_type' => 'percentage', 'is_taxable' => true],
            ['name' => 'Dearness Allowance', 'code' => 'DA', 'type' => 'earning', 'value_type' => 'percentage', 'is_taxable' => true],
            ['name' => 'Conveyance Allowance', 'code' => 'CONV', 'type' => 'earning', 'value_type' => 'fixed', 'is_taxable' => false],
            ['name' => 'Medical Allowance', 'code' => 'MED', 'type' => 'earning', 'value_type' => 'fixed', 'is_taxable' => false],
            ['name' => 'Special Allowance', 'code' => 'SPECIAL', 'type' => 'earning', 'value_type' => 'fixed', 'is_taxable' => true],
            ['name' => 'Provident Fund', 'code' => 'PF', 'type' => 'deduction', 'value_type' => 'percentage', 'is_taxable' => false],
            ['name' => 'ESI', 'code' => 'ESI', 'type' => 'deduction', 'value_type' => 'percentage', 'is_taxable' => false],
            ['name' => 'Professional Tax', 'code' => 'PT', 'type' => 'deduction', 'value_type' => 'fixed', 'is_taxable' => false],
            ['name' => 'TDS', 'code' => 'TDS', 'type' => 'deduction', 'value_type' => 'percentage', 'is_taxable' => false],
        ];

        foreach ($components as $component) {
            SalaryComponent::create($component);
        }
    }
}
