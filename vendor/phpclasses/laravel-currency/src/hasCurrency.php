<?php

namespace RaggiTech\Laravel\Currency;

use RaggiTech\Laravel\Currency\Currency;
use RaggiTech\Laravel\Currency\Exceptions\{NotAllowedCurrencyException, UnknownCurrencyException};
use RaggiTech\Laravel\Currency\Traits\{Info, Relationships, Scopes};

trait hasCurrency
{
    use Info, Relationships, Scopes;

    private function getCurrency(?string $currency = null)
    {
        $default    = config('RaggiTech.currency.default', 'USD');
        $only       = config('RaggiTech.currency.only', []);

        if (!$currency) $currency = $default;
        if (!empty($only) && !in_array($currency, $only)) throw new NotAllowedCurrencyException('This currency has not been allowed.');
        if (!in_array($currency, array_keys(Currency::list())))  throw new UnknownCurrencyException('Unknown Currency.');

        $currency = $this->currencies->where('currency_type', $currency)->first();
        if (!$currency) return null;

        $currency->currency = Currency::list()[$currency->currency_type];
        return $currency;
    }

    /**
     * Get Currency
     */
    public function currency(?string $currency = null)
    {
        $model = $this->getCurrency($currency);
        if (!$model) return null;

        return ReadableDecimal($model->currency_amount, $model->currency['decimals']);
    }

    /**
     * Get Currency With Symbol
     */
    public function currencyWithSymbol(?string $currency = null)
    {
        $model = $this->getCurrency($currency);
        if (!$model) return null;

        $symbol = $model->currency['symbol'];
        $pre_symbol = $model->currency['pre_symbol'];

        $amount = ReadableDecimal($model->currency_amount, $model->currency['decimals']);
        if ($pre_symbol) {
            return $symbol . $amount;
        } else {
            return $amount . ' ' . $symbol;
        }
    }

    /**
     * Get Currency With Code
     */
    public function currencyWithCode(?string $currency = null)
    {
        $model = $this->getCurrency($currency);
        if (!$model) return null;

        return ReadableDecimal($model->currency_amount, $model->currency['decimals']) . ' ' . $model->currency_type;
    }

    /**
     * Create/Update Currency
     */
    public function setCurrency(float $amount, ?string $currency = null)
    {
        $currency   = $currency ?? config('RaggiTech.currency.default', 'USD');
        $model      = $this->getCurrency($currency);

        if (!$model) {
            $obj = new Currency();
            $obj->user_id               = auth()->check() ? auth()->user()->id : null;
            $obj->model_type            = $this->getModelType();
            $obj->model_id              = $this->{$this->getModelKey()};
            $obj->currency_type         = $currency;
            $obj->currency_amount       = $amount;

            return $obj->save();
        } else {
            // Update
            return $model->update(['currency_amount' => $amount]);
        }
    }

    /**
     * Delete Currency
     */
    public function deleteCurrency(string $currency)
    {
        $model      = $this->getCurrency($currency);
        if (!$model) return null;

        return $model->delete();
    }

    /**
     * Clear Currencies
     */
    public function clearCurrencies()
    {
        return $this->currencies()->delete();
    }
}
