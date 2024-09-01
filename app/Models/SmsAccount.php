<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id','username', 'password',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function senderIds()
    {
        return $this->hasMany(SenderId::class);
    }
}
