<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentsController extends Controller
{
    public function index()
    {
        // Only allow visitors with a session or authenticated user to view documents page
        $currentUserId = \Illuminate\Support\Facades\Auth::id() ?: session('user_id');
        if (!$currentUserId) {
            // Redirect guests to the login page with intended URL (do not allow access to /documents)
            return redirect()->guest(route('auth.login.form'));
        }

        // Avoid selecting the binary 'content' column and be defensive about metadata columns
        // Build the select list dynamically so the page won't break if migrations haven't been run yet.
        $select = [
            'documents.id',
            'documents.user_id',
            'documents.filename',
            'documents.path',
            'documents.type',
        ];

        // Add metadata columns only if they exist in the schema (prevents SQL errors)
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('documents', 'size_bytes')) {
                $select[] = 'documents.size_bytes';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('documents', 'mime_type')) {
                $select[] = 'documents.mime_type';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('documents', 'status')) {
                $select[] = 'documents.status';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('documents', 'uploaded_at')) {
                $select[] = 'documents.uploaded_at';
            }
        } catch (\Throwable $_) {
            // If the DB connection or schema introspection fails, continue with the base columns only.
        }

        // Always include timestamps and user info
        $select[] = 'documents.created_at';
        $select[] = 'documents.updated_at';
        $select[] = 'users.full_name as user_full_name';
        $select[] = 'users.email as user_email';

        $docs = DB::table('documents')
            ->leftJoin('users','documents.user_id','=','users.id')
            ->orderByDesc('documents.created_at')
            ->select($select)
            ->paginate(20);
        return view('documents', compact('docs'));
    }

    public function store(Request $request)
    {
        // Backwards-compatible insertion when path is provided (non-file upload)
        $data = $request->validate([
            // accept numeric or string user_id; we'll cast to string before inserting
            'user_id' => 'required|exists:users,id',
            'filename' => 'required|string',
            'path' => 'sometimes|string',
            'type' => 'nullable|string',
            'size_bytes' => 'sometimes|nullable|integer',
            'mime_type' => 'sometimes|nullable|string',
            'status' => 'sometimes|nullable|string',
            'uploaded_at' => 'sometimes|nullable|date',
        ]);
        $now = now();
        DB::table('documents')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => (string) $data['user_id'],
            'filename' => $data['filename'],
            'path' => $data['path'] ?? null,
            'type' => $data['type'] ?? null,
            'size_bytes' => $data['size_bytes'] ?? null,
            'mime_type' => $data['mime_type'] ?? null,
            'status' => $data['status'] ?? null,
            'uploaded_at' => $data['uploaded_at'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        return back()->with('status','document_created');
    }

    /**
     * Accept multipart upload and store file under storage/app/public/documents/{user_id}/
     * Returns JSON with inserted document id and public url
     */
    public function upload(Request $request)
    {

        // Defensive: ensure PHP actually received a file. If post_max_size was exceeded
        // PHP may drop the file from the request entirely.
        if (!$request->hasFile('file')) {
            return response()->json(['ok' => false, 'message' => 'لم يتم تحميل ملف. تأكد أن حجم الملف ضمن حدود الخادم (php.ini: post_max_size, upload_max_filesize)'], 422);
        }

        // Validate input and enforce max file size (100MB) and allowed mime types
        try {
            $data = $request->validate([
                    // accept numeric or string user_id; we'll prefer server session when present
                    'user_id' => 'sometimes|nullable|exists:users,id',
                // allow pdf and common image types. max in kilobytes.
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:102400', // max 100MB (value in kilobytes)
                'type' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return the first validation error message from the validator so the client
            // sees a specific reason (size vs mime vs required)
            $msg = $e->validator->errors()->first() ?: 'حجم الملف كبير أو نوع الملف غير مدعوم';
            return response()->json(['ok' => false, 'message' => $msg], 422);
        }

        // Ensure uploader is authenticated or has a session; prefer server-side session over client-sent user_id
        $currentUserId = \Illuminate\Support\Facades\Auth::id() ?: session('user_id');
        if (!$currentUserId) {
            return response()->json(['ok' => false, 'message' => 'غير مسجل الدخول'], 401);
        }

        $file = $request->file('file');

        // Additional server-side size check in bytes (defensive)
        $maxBytes = 100 * 1024 * 1024; // 100MB
        $size = $file->getSize();
        if ($size !== null && $size > $maxBytes) {
            return response()->json(['ok' => false, 'message' => 'حجم الملف أكبر من الحد المسموح (100MB)'], 413);
        }

            // Prefer server-side current user id. If client supplied a user_id, accept it only
            // when it matches the session/auth id; otherwise ignore it.
            $userId = !empty($data['user_id']) ? $data['user_id'] : $currentUserId;
            if ($userId !== $currentUserId) {
                // security: never allow uploading on behalf of another user
                $userId = $currentUserId;
            }

        // store in public disk under documents/{user_id}
        try {
            $path = $file->store("documents/{$userId}", 'public');
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'فشل تخزين الملف'], 500);
        }

        // Insert metadata to DB (disk-only storage). Avoid writing binary content to DB to prevent DB bloat.
        $now = now();
        $id = (string) Str::uuid();
        try {
            DB::table('documents')->insert([
                'id' => $id,
                'user_id' => (string) $userId,
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $data['type'] ?? null,
                'size_bytes' => $size,
                'mime_type' => $file->getClientMimeType(),
                'status' => 'completed',
                'uploaded_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (\Throwable $e) {
            // If DB insert fails, attempt to remove the stored file to avoid orphaned files
            try { \Illuminate\Support\Facades\Storage::disk('public')->delete($path); } catch (\Throwable $_) {}
            return response()->json(['ok' => false, 'message' => 'فشل حفظ بيانات الملف'], 500);
        }

        $url = Storage::url($path);
        return response()->json([
            'ok' => true,
            'id' => $id,
            'url' => $url,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $size,
        ]);
    }

    /**
     * Submit all required documents for the current user for review.
     * Expects AJAX POST. Verifies required document types exist, marks them as 'submitted'
     * and returns JSON with a friendly Arabic message.
     */
    public function submit(Request $request)
    {
        $currentUserId = \Illuminate\Support\Facades\Auth::id() ?: session('user_id');
        if (!$currentUserId) {
            return response()->json(['ok' => false, 'message' => 'غير مسجل الدخول'], 401);
        }

        // list of required document types (must match client-side ids)
        $required = ['identity_front','identity_back','bank_statement','salary_certificate'];

        // count how many required docs the user has uploaded
        $have = DB::table('documents')
            ->where('user_id', (string) $currentUserId)
            ->whereIn('type', $required)
            ->whereNotNull('path')
            ->pluck('type')
            ->unique()
            ->values()
            ->toArray();

        $missing = array_values(array_diff($required, $have));
        if (!empty($missing)) {
            return response()->json(['ok' => false, 'message' => 'يجب رفع جميع المستندات المطلوبة قبل الإرسال', 'missing' => $missing], 422);
        }

        // mark these documents as submitted for review
        DB::table('documents')
            ->where('user_id', (string) $currentUserId)
            ->whereIn('type', $required)
            ->update(['status' => 'submitted', 'updated_at' => now()]);

        return response()->json(['ok' => true, 'message' => 'تم إرسال المستندات للمراجعة']);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'filename' => 'sometimes|string',
            'type' => 'sometimes|nullable|string',
        ]);
        DB::table('documents')->where('id',$id)->update($data + ['updated_at'=>now()]);
        return back()->with('status','document_updated');
    }

    public function destroy($id)
    {
        DB::table('documents')->where('id',$id)->delete();
        return back()->with('status','document_deleted');
    }
}
