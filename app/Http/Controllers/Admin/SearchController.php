<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Investment;
use App\Models\License;
use App\Models\InvestmentPackage;
use App\Models\Document;
use App\Models\BankAccount;

class SearchController extends Controller
{
    private function guardAdmin()
    {
        if (!Auth::check() || !Auth::user()->role || !in_array(Auth::user()->role, ['admin','staff'])) {
            return false;
        }
        return true;
    }

    public function users(Request $request)
    {
        if (!$this->guardAdmin()) return response('Unauthorized', 401);
        $q = trim((string)$request->query('query', ''));
        $limit = (int)$request->query('limit', 100);
        $qb = User::orderByDesc('created_at');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('full_name', 'like', '%' . $q . '%')
                  ->orWhere('username', 'like', '%' . $q . '%')
                  ->orWhere('email', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%');
            });
        }
        $users = $qb->take($limit)->get();
        return view('admin.partials.users_table', compact('users'));
    }

    public function investments(Request $request)
    {
        if (!$this->guardAdmin()) return response('Unauthorized', 401);
        $q = trim((string)$request->query('query', ''));
        $limit = (int)$request->query('limit', 100);
        $qb = Investment::orderByDesc('created_at');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('id', 'like', '%' . $q . '%')
                  ->orWhere('status', 'like', '%' . $q . '%')
                  ->orWhere('amount', 'like', '%' . $q . '%');
            });
        }
        $investments = $qb->take($limit)->get();
        return view('admin.partials.investments_table', compact('investments'));
    }

    public function licenses(Request $request)
    {
        if (!$this->guardAdmin()) return response('Unauthorized', 401);
        $q = trim((string)$request->query('query', ''));
        $limit = (int)$request->query('limit', 100);
        $qb = License::orderByDesc('created_at');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('business_name', 'like', '%' . $q . '%')
                  ->orWhere('business_type', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%');
            });
        }
        $licenses = $qb->take($limit)->get();
        return view('admin.partials.licenses_table', compact('licenses'));
    }

    public function packages(Request $request)
    {
        if (!$this->guardAdmin()) return response('Unauthorized', 401);
        $q = trim((string)$request->query('query', ''));
        $limit = (int)$request->query('limit', 100);
        $qb = InvestmentPackage::orderByDesc('id');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('name', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%')
                  ->orWhere('amount', 'like', '%' . $q . '%')
                  ->orWhere('profit_rate', 'like', '%' . $q . '%');
            });
        }
        $packages = $qb->take($limit)->get();
        return view('admin.partials.packages_table', compact('packages'));
    }

    public function documents(Request $request)
    {
        if (!$this->guardAdmin()) return response('Unauthorized', 401);
        $q = trim((string)$request->query('query', ''));
        $limit = (int)$request->query('limit', 100);
        $qb = Document::orderByDesc('created_at');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('filename', 'like', '%' . $q . '%')
                  ->orWhere('type', 'like', '%' . $q . '%')
                  ->orWhere('path', 'like', '%' . $q . '%');
            });
        }
        $documents = $qb->take($limit)->get();
        return view('admin.partials.documents_table', compact('documents'));
    }

    public function banks(Request $request)
    {
        if (!$this->guardAdmin()) return response('Unauthorized', 401);
        $q = trim((string)$request->query('query', ''));
        $limit = (int)$request->query('limit', 100);
        $qb = BankAccount::orderByDesc('id');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('account_name', 'like', '%' . $q . '%')
                  ->orWhere('bank_name', 'like', '%' . $q . '%')
                  ->orWhere('account_number', 'like', '%' . $q . '%')
                  ->orWhere('iban', 'like', '%' . $q . '%');
            });
        }
        $banks = $qb->take($limit)->get();
        return view('admin.partials.bank_accounts_table', ['banks' => $banks]);
    }
}
