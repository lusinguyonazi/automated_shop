<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_note_id', 'quantity', 'price_per_unit', 'price', 'discount', 'product_id', 'shop_id',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
