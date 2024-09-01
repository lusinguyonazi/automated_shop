<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'supplier_id', 'invoice_no', 'amount', 'currency', 'defcurr', 'ex_rate', 'receipt_no', 'payment', 'adjustment', 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
