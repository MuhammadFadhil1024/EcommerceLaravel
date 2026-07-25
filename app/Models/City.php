<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'city_id',
        'province_id',
        'name',
        'zip_code',
    ];
}
