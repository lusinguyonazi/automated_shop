<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceServitem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pro_invoice_id', 'product_id', 'repeatition', 'cost_per_unit',
    ];

    public function proInvoice()
    {
        return $this->belongsTo(ProInvoice::class);
    }

    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
