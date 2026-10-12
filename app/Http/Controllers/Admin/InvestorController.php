<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Investor;
use App\Models\Investment;

class InvestorController extends Controller
{
    // Search investors and return a Blade partial (for AJAX)
    public function search(Request $request)
    {
        if (!Auth::check() || !Auth::user()->role || !in_array(Auth::user()->role, ['admin','staff'])) {
            return response('Unauthorized', 401);
        }
        $q = trim((string)$request->query('query', ''));
        $limit = (int)$request->query('limit', 100);
        $qb = Investor::orderByDesc('created_at');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('full_name', 'like', '%' . $q . '%')
                  ->orWhere('email', 'like', '%' . $q . '%')
                  ->orWhere('phone', 'like', '%' . $q . '%')
                  ->orWhere('national_id', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%');
            });
        }
        $investors = $qb->take($limit)->get();
        return view('admin.partials.investors_table', compact('investors'));
    }

    // Show an investor detail page (admin-facing)
    public function show($id)
    {
        if (!Auth::check() || !Auth::user()->role || !in_array(Auth::user()->role, ['admin','staff'])) {
            return redirect()->route('admin.login');
        }
        $investor = Investor::find($id);
        if (!$investor) abort(404);
        $investments = Investment::where('investor_id', $investor->id)->orderByDesc('created_at')->get();
        return view('admin.investors.show', compact('investor','investments'));
    }
}
