<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'district_id',
        'city_id',
        'name',
        'zip_code',
    ];
}
