<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id','user_id', 'business_name', 'business_type', 'activity_description', 'location', 'status', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
