<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Investment;
use App\Models\License;

class DataController extends Controller
{
    public function createUser(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'national_id' => 'nullable|string',
        ]);

        $id = (string) Str::uuid();
        $data['id'] = $id;
        $data['created_at'] = now();

        $user = User::create($data);
        return response()->json($user, 201);
    }

    public function getUsers()
    {
        return response()->json(User::all());
    }

    public function createInvestment(Request $request)
    {
        $data = $request->validate([
            'investor_id' => 'nullable|string|exists:investors,id',
            'user_id' => 'nullable|string|exists:users,id',
            'amount' => 'required|numeric',
            'investment_type' => 'required|string',
            'duration_months' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $id = (string) Str::uuid();
        $data['id'] = $id;
        // compute profit_rate and expected_return using simple rules (mirror backend)
        $amount = floatval($data['amount']);
        if($data['investment_type'] === 'small'){
            // find closest predefined small package
            $packages = [1000,2500,3800,5000,7200,9600,10000,15000,20000,25000,30000,35000,40000,45000];
            $closest = collect($packages)->sortBy(function($p) use($amount){ return abs($p - $amount); })->first();
            $data['profit_rate'] = 5; // fallback
            if($closest >= 1000) $data['profit_rate'] = 15; // simplified
        } else {
            // large
            if($amount >= 100000) $data['profit_rate'] = 50;
            elseif($amount >= 75000) $data['profit_rate'] = 48;
            else $data['profit_rate'] = 45;
        }

        $data['expected_return'] = $amount * ($data['profit_rate'] / 100);
        $data['created_at'] = now();

        // Resolve investor_id: prefer provided investor_id, else map from user_id
        if (empty($data['investor_id']) && !empty($data['user_id'])) {
            $invRow = \App\Models\Investor::where('user_id', $data['user_id'])->first();
            if ($invRow) { $data['investor_id'] = $invRow->id; }
        }
        unset($data['user_id']); // investments link to investors now
        unset($data['investment_type']); // not stored anymore in table

        $inv = Investment::create($data);
        return response()->json($inv, 201);
    }

    public function getInvestments()
    {
        return response()->json(Investment::all());
    }

    public function getUserInvestments($user_id)
    {
    $inv = \App\Models\Investor::where('user_id', $user_id)->first();
    if(!$inv) return response()->json([]);
    return response()->json(Investment::where('investor_id', $inv->id)->get());
    }

    public function createBusinessLicense(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|string|exists:users,id',
            'business_name' => 'required|string',
            'business_type' => 'required|string',
            'activity_description' => 'nullable|string',
            'location' => 'nullable|string'
        ]);

        $data['id'] = (string) Str::uuid();
        $data['created_at'] = now();

        $lic = License::create($data);
        return response()->json($lic, 201);
    }

    public function getBusinessLicenses()
    {
        return response()->json(License::all());
    }
}
