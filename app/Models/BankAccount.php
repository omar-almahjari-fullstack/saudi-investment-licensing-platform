<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'account_name',
        'bank_name',
        'account_number',
        'iban',
        'swift',
    'reference_code',
        'instructions',
        'ask_sender',
        'ask_datetime',
        'require_receipt',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'ask_sender' => 'boolean',
        'ask_datetime' => 'boolean',
        'require_receipt' => 'boolean',
        'is_active' => 'boolean',
    ];
}
