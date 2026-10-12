<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Auth;

class PlatformSettingController extends Controller
{
    // Return the single platform settings row (or empty)
    public function show(Request $request)
    {
        $row = PlatformSetting::orderByDesc('id')->first();
        return response()->json($row ? $row->toArray() : []);
    }

    // Update or create the platform settings (admin only)
    public function update(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Optional permission check using AdminController helper if available
        try{
            if (method_exists(\App\Http\Controllers\AdminController::class, 'hasPerm')){
                $admin = app()->make('\App\Http\Controllers\AdminController');
                if (method_exists($admin, 'hasPerm') && !$admin->hasPerm('manage_settings')){
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
            }
    } catch(\Throwable $e){ }

        $data = $request->validate([
            'name' => 'sometimes|nullable|string|max:255',
            'url' => 'sometimes|nullable|string|max:255',
            'locale' => 'sometimes|nullable|string|max:10',
            'description' => 'sometimes|nullable|string|max:2000',
        ]);

        $existing = PlatformSetting::orderByDesc('id')->first();
        if ($existing) {
            $existing->fill($data);
            $existing->save();
            $row = $existing;
        } else {
            $data['created_by'] = Auth::check() ? Auth::id() : null;
            $row = PlatformSetting::create($data);
        }

        return response()->json(['message' => 'تم حفظ إعدادات المنصة', 'data' => $row]);
    }
}
