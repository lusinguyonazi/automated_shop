<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSubType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'name_sw', 'description', 'description_sw',
    ];

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }
}
