<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class PagesController extends Controller
{
    public function terms() { return view('pages.terms'); }
    public function privacy() { return view('pages.privacy'); }

    public function store() { abort(405); }
    public function update() { abort(405); }
    public function destroy() { abort(405); }
}
