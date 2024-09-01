<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmSupplierTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function rmpurchases (){

        return $this->belongsTo(RmPurchase::class);
    }
}
