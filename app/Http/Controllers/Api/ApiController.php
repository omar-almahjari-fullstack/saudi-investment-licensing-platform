<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_users' => 378,
            'total_licenses' => 743,
            'success_rate' => 98,
            'support_hours' => '24/7'
        ]);
    }

    public function investmentPackages()
    {
        return response()->json([
            'small_packages' => [
                ['amount' => 1000, 'profit_rate' => 5],
                ['amount' => 5000, 'profit_rate' => 6],
                ['amount' => 10000, 'profit_rate' => 7],
                ['amount' => 20000, 'profit_rate' => 8],
            ],
            'large_packages' => [
                ['amount' => 50000, 'profit_rate' => 10],
            ]
        ]);
    }
}
