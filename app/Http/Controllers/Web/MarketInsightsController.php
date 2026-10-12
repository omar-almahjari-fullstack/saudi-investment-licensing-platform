<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MarketInsightsController extends Controller
{
    public function index()
    {
        $indicators = DB::table('market_indicators')->orderByDesc('created_at')->limit(10)->get();
        $sectors = DB::table('market_sectors')->orderByDesc('updated_at')->limit(12)->get();
        // only show news that haven't expired (expires_at is null or in the future)
        $news = DB::table('market_news')
            ->where(function($q){
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        $trends = DB::table('market_trends')->orderByDesc('created_at')->limit(10)->get();
        return view('market-insights', compact('indicators','sectors','news','trends'));
    }

    public function store(Request $request) { abort(405); }
    public function update(Request $request, $id = null) { abort(405); }
    public function destroy($id = null) { abort(405); }
}
