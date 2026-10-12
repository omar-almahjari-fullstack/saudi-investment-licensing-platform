<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorEarning extends Model
{
    public $table = 'investor_earnings';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id','investment_id','investor_id','month_number','profit_amount','created_at'];
}
