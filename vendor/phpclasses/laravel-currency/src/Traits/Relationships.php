<?php
namespace RaggiTech\Laravel\Currency\Traits;

use RaggiTech\Laravel\Currency\Currency;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Relationships{
    
    /**
     * Get List OF Currencies
     */
    public function currencies()
    {
        return $this->morphMany(Currency::class, 'model');
        // return Currency::getCurrency($this->getModelType(), $this->{$this->getModelKey()}, 'USD');
    }
}