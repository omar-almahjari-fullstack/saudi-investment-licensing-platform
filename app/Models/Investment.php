<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Investment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id','investor_id','package_id','amount','profit_rate','expected_return','duration_months','risk_level','notes','status','created_at'];

    protected $dates = ['created_at','approved_at'];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
