<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'user_id', 'shop_id', 'credit_note_no',];


    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function noteItems()
    {
        return $this->hasMany(CreditNoteItem::class);
    }
}
