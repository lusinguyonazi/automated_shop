<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'expense_type', 'amount', 'no_days', 'wht_rate', 
    ];
}
