<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'basic_unit','image'
    ];

    public function saleItem()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function shops()
    {
        return $this->belongstoMany(Shop::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }
}
