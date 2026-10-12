<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // use string primary key (UUID) coming from original Mongo data
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // enable timestamps since migration includes them
    public $timestamps = true;

    // include password so seed/create can set it
    protected $fillable = [
        'id', 'full_name', 'username', 'email', 'password', 'role', 'is_active', 'created_at', 'updated_at',
        // identity canonical fields
        'identity_image', 'identity_image_front', 'identity_image_back',
        // verification fields
        'verification_code', 'verification_token', 'email_verified_at', 'phone', 'mobile'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function investor()
    {
        return $this->hasOne(Investor::class, 'user_id');
    }
}
