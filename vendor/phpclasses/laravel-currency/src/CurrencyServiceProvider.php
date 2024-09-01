<?php

namespace RaggiTech\Laravel\Currency;

use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/currency.php' => config_path('RaggiTech/currency.php'),
        ], 'laravel-currency');

        $this->publishes([
            __DIR__ . '/database/migrations/currencies.stub' => database_path(
                sprintf('migrations/%s_create_currencies_table.php', date('Y_m_d_His'))
            ),
        ], 'laravel-currency');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/currency.php',
            'laravel-currency'
        );
    }
}
