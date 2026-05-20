<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'address_id', 'reference_id', 'session_id',
        'total_payment', 'courier_cost', 'courier',
        'payment_date', 'expires_at',
        'status', 'payment_method', 'payment_url',
    ];

    protected $casts = [
        'total_payment' => 'decimal:2',
        'courier_cost' => 'decimal:2',
        'payment_date' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
