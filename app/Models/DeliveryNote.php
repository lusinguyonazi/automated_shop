<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory;

     protected $fillable = [
        'an_sale_id', 'user_id', 'shop_id', 'note_no', 'comments',
    ];
}
