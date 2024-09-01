<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeSetting extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id', 'code_type', 'code_length', 'height', 'width', 'showcode',];

    
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
