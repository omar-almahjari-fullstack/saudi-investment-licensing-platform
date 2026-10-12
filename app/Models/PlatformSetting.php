<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $table = 'platform_settings';
    protected $fillable = ['name','url','locale','description','created_by'];
}
