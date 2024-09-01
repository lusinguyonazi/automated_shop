<?php

namespace Database\Factories;

use App\Models\CashIn;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Purchase::class;

    public function definition()
    {
        return [
            'shop_id' => 1, 
            'user_id' => 3, 
            'supplier_id' => 1, 
            'grn_no' => NULL, 
            'order_no' => NULL, 
            'delivery_note_no' => NULL, 
            'invoice_no' => NULL, 
            'total_amount' => 400000, 
            'amount_paid' => 200000, 
            'currency' => 'USD', 
            'defcurr' => 'USD', 
            'ex_rate' => 1.0000000, 
            'time_created' => now(), 
            'purchase_type' => 'cash',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
