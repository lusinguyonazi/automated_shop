<?php
namespace RaggiTech\Laravel\Currency\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;

trait Scopes{

    /**
     * Has Currency
     */
    public function scopeWithoutCurrencies(Builder $query)
    {
        return $query->whereDoesntHave('currencies');
    }

    /**
     * Has Currency
     */
    public function scopeWithCurrency(Builder $query, ?string $currency = null)
    {
        $currency = $currency ?: 'USD';

        return $query->whereHas('currencies', function (Builder $q) use ($currency) {
            $q->where('currency_type', $currency);
        });
    }

    /**
     * Has OneOrMany Currencies
     */
    public function scopeWithAnyCurrency(Builder $query, array $currencies)
    {
        return $query->whereHas('currencies', function (Builder $q) use ($currencies) {
            $q->whereIn('currency_type', $currencies);
        });
    }

}