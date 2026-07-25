<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'province_id',
        'name',
    ];

    public function cities()
    {
        return $this->hasMany(City::class, 'province_id', 'province_id');
    }
}
