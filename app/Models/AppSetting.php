<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'unit',
        'description',
        'is_active',
    ];
}