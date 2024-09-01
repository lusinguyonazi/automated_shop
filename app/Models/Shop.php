<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
     protected $fillable = [
          'suid', 'name', 'city', 'business_type_id', 'business_sub_type_id', 'subscription_type_id', 'business_sub_type_id',
    ];

    public function users()
    {
      return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function customers()
    {
      return $this->hasMany(Customer::class);
    }

    public function sales()
    {
      return $this->hasMany(AnSale::class);
    }

    public function costs()
    {
        return $this->hasMany(AnCost::class);
    }
    

    public function categories()
    {
      return $this->hasMany(Category::class)->with(['children']);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
                        ->withPivot('product_id', 'shop_id', 'in_stock', 'reorder_point', 'buying_per_unit', 'price_per_unit', 'wholesale_price', 'barcode', 'description', 'location', 'product_no')
                        ->withTimestamps()
                        ->orderBy('name', 'asc');
    }

    public function invoices()
    {
      return $this->hasMany(Invoice::class);
    }

    public function suppliers()
    {
      return $this->hasMany(Supplier::class);
    }

    public function businessType()
    {
      return $this->belongsTo(BusinessType::class);
    }

    public function subscriptionType()
    {
      return $this->belongsTo(SubscriptionType::class);
    }


    public function services()
    {
      return $this->belongsToMany(Service::class)
                        ->withPivot('price', 'description', 'active_for_sale')
                        ->withTimestamps()
                        ->orderBy('name', 'asc');
    }
    public function rawMaterials()
    {
      return $this->belongsToMany(RawMaterial::class)
                        ->withPivot('in_store', 'unit_cost', 'reorder_point', 'description' , 'is_deleted' , 'shop_id')
                        ->withTimestamps()
                        ->orderBy('name', 'asc');
    }


    public function packingMaterials()
    {
      return $this->belongsToMany(PackingMaterial::class)
                        ->withPivot('in_store', 'unit_cost', 'description' , 'is_deleted' , 'shop_id')
                        ->withTimestamps()
                        ->orderBy('name', 'asc');
    }


    public function mroItems()
    {
      return $this->hasMany(MROItem::class);
                        
    }


    public function mro()
    {
      return $this->hasMany(Mro::class);
    }

    public function mroUse(){
        return $this->hasMany(MroUse::class);
    }

    public function rmuse(){
      return $this->hasMany(RmUse::class);
    }

    public function pmuse(){
      return $this->hasMany(PmUse::class);
    }

    public function stores()
    {
      return $this->hasMany(Store::class);
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.Shop.' . $this->id;
    }

     public function bankDetails()
    {
        return $this->hasMany(BankDetail::class);
    }

}
