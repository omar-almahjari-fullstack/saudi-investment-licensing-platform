<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $table = 'contact_infos';
    protected $fillable = ['email','phone','whatsapp','address','updated_by','updated_by_uuid'];
}
