<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMadeApiTemp extends Model
{
    use HasFactory;
    
     protected $fillable = [
      'user_id', 'shop_id', 'product_id', 'qty', 'cost_per_unit', 'packing_material_id' , 'name' , 'batch_no'
    ];

   public function user(){

      $this->belongsTo(User::class);
   }
}
