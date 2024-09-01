<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use \Carbon\Carbon;
use Session;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\AnSaleItem;
use App\Models\ProdDamage;
use App\Models\TransferOrderItem;
use App\Models\SaleReturnItem;

class ProductsImport implements ToModel, WithHeadingRow, WithMultipleSheets, WithEvents
{
     use Importable, RegistersEventListeners;


    public static function beforeImport(BeforeImport $event)
    {
        $worksheet = $event->reader->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10

        if ($highestRow < 2) {
            $error = \Illuminate\Validation\ValidationException::withMessages([]);
            $failure = new Failure(1, 'rows', [0 => 'Now enough rows!']);
            $failures = [0 => $failure];
            throw new ValidationException($error, $failures);
        }
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $now = Carbon::now();
        if (array_key_exists('name', $row) && array_key_exists('basic_unit', $row) && array_key_exists('in_stock', $row) && array_key_exists('retail_price_per_unit', $row) && array_key_exists('buying_per_unit', $row)) {
                        
            if (is_null($row['name']) || is_null($row['basic_unit'])) {
                return null;
            }else{
                $product = Product::where('name', $row['name'])->where('basic_unit', $row['basic_unit'])->first();

                $buying_per_unit = (float)$row['buying_per_unit'];
                $price_per_unit = (float)$row['retail_price_per_unit'];
                $wholesaleprice = (float)$row['wholesale_price_per_unit'];
                $expire_date = $row['expire_date'];
                    
                $quantity_in = 0;
                if (!is_null($row['in_stock'])) {
                    $quantity_in = $row['in_stock'];
                }

                if (is_null($product)) {
                    $product = Product::create([
                        'name' => $row['name'],
                        'basic_unit' => $row['basic_unit']
                    ]);

                    if (!is_null($row['in_stock'])) {
                        $stock = Stock::create([
                            'shop_id' => $shop->id,
                            'product_id' => $product->id,
                            'quantity_in' => $quantity_in,
                            'buying_per_unit' => $buying_per_unit,
                            'source' => 'Purchased',
                            'expire_dates' => $expire_date,
                            'time_created' => $now
                        ]);

                        $shop_product = $shop->products()->where('product_id', $product->id)->first();
                        if (!is_null($shop_product)) {

                            $stock_in = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_in');
                            $sold = AnSaleItem::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_sold');
                            $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                            $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                            $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                            
                            $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered); 
                                         
                            $shop_product->pivot->in_stock = $instock;
                            $shop_product->pivot->save();
                        }
                        
                    }

                    $shopprod = $shop->products()->where('product_id', $product->id)->first();
                    if (is_null($shopprod)) {

                        $shop->products()->attach($product, ['in_stock' => $quantity_in, 'location' => $row['location'], 'product_no' => $row['product_no'], 'barcode' => $row['barcode'], 'buying_per_unit' => $buying_per_unit, 'price_per_unit' => $price_per_unit,  'wholesale_price' => $wholesaleprice, 'time_created' => $now]);
                        
                    }

                    $shop_product = $shop->products()->where('product_id', $product->id)->first();
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    }elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                                $shop_product->pivot->status = 'Low Stock';
                    }
                    $shop_product->pivot->save();

                    $prod_unit = new ProductUnit();
                    $prod_unit->shop_id = $shop->id;
                    $prod_unit->product_id = $product->id;
                    $prod_unit->unit_name = $product->basic_unit;
                    $prod_unit->is_basic = true;
                    $prod_unit->qty_equal_to_basic = 1;
                    $prod_unit->unit_price = $price_per_unit;
                    $prod_unit->save();
                    return $product;
                }else{
                    $stock = Stock::create([
                        'shop_id' => $shop->id,
                        'product_id' => $product->id,
                        'quantity_in' => $quantity_in,
                        'buying_per_unit' => $buying_per_unit,
                        'source' => 'Purchased',
                        'expire_dates' => $expire_date,
                        'time_created' => $now
                    ]);

                    $shop_product = $shop->products()->where('product_id', $product->id)->first();
                    if (!is_null($shop_product)) {

                        $stock_in = Stock::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_in');
                        $sold = AnSaleItem::where('product_id', $product->id)->where('shop_id', $shop->id)->where('is_deleted', false)->sum('quantity_sold');
                        $damaged = ProdDamage::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                        $tranfered =  TransferOrderItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                        $returned = SaleReturnItem::where('product_id', $product->id)->where('shop_id', $shop->id)->sum('quantity');
                                            
                        $instock = ($stock_in+$returned)-($sold+$damaged+$tranfered); 
                                         
                        $shop_product->pivot->in_stock = $instock;
                        $shop_product->pivot->save();
                    }

                    $shopprod = $shop->products()->where('product_id', $product->id)->first();
                    if (is_null($shopprod)) {

                            $shop->products()->attach($product, ['in_stock' => $quantity_in, 'location' => $row['location'], 'product_no' => $row['product_no'], 'barcode' => $row['barcode'], 'buying_per_unit' => $buying_per_unit, 'price_per_unit' => $price_per_unit,'wholesale_price' => $wholesaleprice, 'time_created' => $now]);
                        
                    }
                    
                    $shop_product = $shop->products()->where('product_id', $product->id)->first();
                    if ($shop_product->pivot->in_stock > $shop_product->pivot->reorder_point) {
                        $shop_product->pivot->status = 'In Stock';
                    }elseif ($shop_product->pivot->in_stock == 0) {
                        $shop_product->pivot->status = 'Out of Stock';
                    }elseif($shop_product->pivot->in_stock <= $shop_product->pivot->reorder_point && $shop_product->pivot->in_stock != 0){
                                $shop_product->pivot->status = 'Low Stock';
                    }
                    $shop_product->pivot->save();
                    
                    $prod_unit = new ProductUnit();
                    $prod_unit->shop_id = $shop->id;
                    $prod_unit->product_id = $product->id;
                    $prod_unit->unit_name = $product->basic_unit;
                    $prod_unit->is_basic = true;
                    $prod_unit->qty_equal_to_basic = 1;
                    $prod_unit->unit_price = $price_per_unit;
                    $prod_unit->save();
                    return $product;
                }
            }
        } else{
             return null;
        } 
    }

    public function sheets(): array
    {
        return [
            // Select by sheet index
            0 => new ProductsImport(),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'basic_unit' => 'required|string',
        ];
    }
}
