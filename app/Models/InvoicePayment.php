<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pro_invoice_id', 'pay_date',   'amount', 'pay_mode', 'bank_name', 'bank_branch', 'cheque_no',
    ];

    public function proInvoice()
    {
        return $this->belongsTo(ProInvoice::class);
    }
}
