<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    protected $table = 'investors';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'id','user_id','full_name','email','phone','national_id','bank_account','identity_image',
        'identity_image_front','identity_image_back',
        'nationality','city','investor_type','expected_amount','investment_duration','investment_size','notes',
        'marketing_opt_in'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
