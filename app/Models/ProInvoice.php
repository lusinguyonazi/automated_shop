<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no', 'customer_id', 'shop_id', 'user_id', 'due_date', 'status', 'discount', 'shipping_cost', 'adjustment', 'notice', 'terms_and_conditions', 'time_created',
    ];


    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function customer()
    {
        return $this->belongsTo(customer::class);
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }
}
