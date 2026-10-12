<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function index()
    {
        $accounts = DB::table('platform_accounts')->orderBy('type')->orderBy('name')->get();
        $transactions = DB::table('platform_transactions')
            ->leftJoin('payments','platform_transactions.payment_id','=','payments.id')
            ->select('platform_transactions.*','payments.transaction_reference as payment_ref')
            ->orderByDesc('platform_transactions.transaction_date')
            ->take(50)->get();
        return view('admin.ledger.index', compact('accounts','transactions'));
    }
}
