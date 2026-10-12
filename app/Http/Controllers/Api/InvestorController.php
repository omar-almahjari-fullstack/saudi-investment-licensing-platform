<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InvestorController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : null;

        $user = User::create($data);

        return response()->json(['user' => $user], 201);
    }
}
