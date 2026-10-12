<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorProfile extends Model
{
    protected $table = 'investor_profiles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id','user_id','nationality','city','investor_type','expected_amount','investment_duration','marketing_opt_in'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
