<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file|max:10240']);

        $path = $request->file('file')->store('uploads', 'public');

        $userId = $request->input('user_id') ?? $request->session()->get('user_id');
        if(!$userId){
            return response()->json(['error' => 'user_id not provided and no session user'], 422);
        }

        $doc = Document::create([
            'id' => (string) Str::uuid(),
            // ensure user_id is stored as a string
            'user_id' => (string) $userId,
            'filename' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'type' => $request->input('type'),
            'created_at' => now(),
        ]);

        return response()->json(['document' => $doc], 201);
    }
}
