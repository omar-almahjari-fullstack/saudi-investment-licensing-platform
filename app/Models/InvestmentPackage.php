<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentPackage extends Model
{
    protected $table = 'investment_packages';
    protected $fillable = ['amount','profit_rate','name','duration_months','description','is_active'];

    protected $casts = [
        'amount' => 'float',
        'profit_rate' => 'float',
        'duration_months' => 'integer',
        'is_active' => 'boolean',
    ];
}
