<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SuccessStoriesController extends Controller
{
    public function index()
    {
        $stories = DB::table('success_stories')->orderByDesc('created_at')->paginate(12);
        return view('success-stories', compact('stories'));
    }

    public function store(Request $request) { abort(405); }
    public function update(Request $request, $id = null) { abort(405); }
    public function destroy($id = null) { abort(405); }
}
