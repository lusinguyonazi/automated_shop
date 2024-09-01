<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['parent_id', 'shop_id', 'name', 'description',];

    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function parent() {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() {
        return $this->hasMany(self::class, 'parent_id','id');
    }

    public function getParentsAttribute()
    {
        $parents = collect([]);

        $parent = $this->parent;

        while(!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents;
    }
    
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function catProducts()
    {
        $products = [];
        
        $this->children()->each(function($child, $key) use (&$products){

            if ($child->children) {
                $child->children()->each(function($ch, $key) use (&$products){
                    if ($ch->children) {
                        $ch->children()->each(function($c, $key) use (&$products){
                            array_push($products, $c->products);
                        });
                    }
                    array_push($products, $ch->products);
                });
            }
            array_push($products, $child->products);
        });

        return collect($products);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class , 'category_product', 'category_id', 'product_id')->withTimestamps()->orderBy('name', 'asc');;
    }
}
