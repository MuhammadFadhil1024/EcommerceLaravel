<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'full_address', 'phone_number', 'postal_code', 'province', 'city', 'type_of_address', 'is_primary'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
