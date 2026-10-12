<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestmentPackagesSeeder extends Seeder
{
	public function run(): void
	{
		$now = now();
		$packages = [
			['amount' => 1000.00, 'profit_rate' => 15.00],
			['amount' => 2500.00, 'profit_rate' => 20.00],
			['amount' => 3800.00, 'profit_rate' => 25.00],
			['amount' => 5000.00, 'profit_rate' => 27.00],
			['amount' => 7200.00, 'profit_rate' => 29.00],
			['amount' => 9600.00, 'profit_rate' => 31.00],
			['amount' => 10000.00, 'profit_rate' => 33.00],
			['amount' => 15000.00, 'profit_rate' => 35.00],
			['amount' => 20000.00, 'profit_rate' => 37.00],
			['amount' => 25000.00, 'profit_rate' => 39.00],
			['amount' => 30000.00, 'profit_rate' => 41.00],
			['amount' => 35000.00, 'profit_rate' => 43.00],
			['amount' => 40000.00, 'profit_rate' => 45.00],
			['amount' => 45000.00, 'profit_rate' => 47.00],
		];

		foreach ($packages as $p) {
			$name = 'حزمة ' . number_format($p['amount']) . ' ريال';
			DB::table('investment_packages')->updateOrInsert(
				['amount' => $p['amount']],
				[
					'name' => $name,
					'profit_rate' => $p['profit_rate'],
					'duration_months' => 1,
					'description' => 'حزمة استثمارية بعائد شهري مضمون',
					'is_active' => 1,
					'updated_at' => $now,
					'created_at' => DB::raw('COALESCE(created_at, NOW())'),
				]
			);
		}
	}
}

