<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id','name', 'contact_no', 'email', 'address', 'country_code', 'supplier_for', 'time_created',
    ];

    public function shops()
    {
        return $this->belongsToMany(Shop::class);
    }

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }
}
