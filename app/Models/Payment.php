<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number', 'code', 'transaction_id', 'amount_paid', 'status',
    ];


    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
