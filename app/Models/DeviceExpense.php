<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id', 'an_cost_id',
    ];
}
