<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id','user_id', 'filename', 'path', 'type', 'created_at'];

    // Ensure user_id is always cast to string when using Eloquent
    protected $casts = [
        'user_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
