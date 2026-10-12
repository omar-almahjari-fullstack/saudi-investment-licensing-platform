<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReminder extends Model
{
    use HasFactory;

    protected $table = 'payment_reminders';
    protected $fillable = ['investment_id', 'admin_id', 'note', 'amount'];

    public function investment()
    {
        return $this->belongsTo(\App\Models\Investment::class, 'investment_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id', 'id');
    }
}
