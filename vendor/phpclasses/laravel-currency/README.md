# [RaggiTech] Laravel >= 6.0 - Currency.

#  [![Latest Stable Version](https://poser.pugx.org/raggitech/laravel-currency/v/stable)](https://packagist.org/packages/raggitech/laravel-currency) [![Total Downloads](https://poser.pugx.org/raggitech/laravel-currency/downloads)](https://packagist.org/packages/raggitech/laravel-currency) [![License](https://poser.pugx.org/raggitech/laravel-currency/license)](https://packagist.org/packages/raggitech/laravel-currency)

#### Laravel Currency provides a quick and easy methods with 150+ Currency.

###### Example:

```php
// Create/Update Currency
$product->setCurrency(15.59, 'USD');

// Retrieve Currency's Value
echo $product->currency('USD'); 			// 15.59
echo $product->currencyWithSymbol('USD'); 	// $15.59
echo $product->currencyWithCode('USD'); 	// 15.59 USD
```



## Install

Install the latest version using [Composer](https://getcomposer.org/):

```bash
$ composer require raggitech/laravel-currency
```

then publish the migration & config files
```bash
$ php artisan vendor:publish --tag=laravel-currency
$ php artisan migrate
```



## Usage

- [Configurations](#config)
- [Currencies List](#list)
- [Create/Update](#cu)
- [Retrieve Value](#get)
- [Delete & Clear](#dc)
- [Relationship](#relationship)
- [Scopes](#scopes)
- [Creator](#u)



<a name="config"></a>

#### Configurations
Default Currency & Only List
```php
use RaggiTech\Laravel\Currency\Currency;

Currency::setDefault('USD'); // Setting USD as a default currency.
Currency::setOnly(['USD', 'EGP']); // Allow using only USD, EGP.
```




<a name="list"></a>

#### Currencies List
```php
$list = currenciesList();
/**
*	"USD" => "US Dollar"
* 	"CAD" => "Canadian Dollar"
* 	"EUR" => "Euro"
* 	"AED" => "United Arab Emirates Dirham"
* 	...
*/
```




<a name="cu"></a>

#### Create / Update Currency's Value
```php
$product->setCurrency(15.59, 'USD');
```




<a name="get"></a>

#### Retrieve Currency's Value
```php
// 15.59 if USD is the default currency
// NULL if there's no value for the default currency.
echo $product->currency();

echo $product->currency('USD'); 			// 15.59		|| NULL
echo $product->currencyWithSymbol('USD'); 	// $15.59		|| NULL
echo $product->currencyWithCode('USD'); 	// 15.59 USD	|| NULL
```



<a name="dc"></a>

#### Delete a single currency || Clear all model's currencies
```php
$product->deleteCurrency('EGP'); 	// Delete EGP Currency
$product->clearCurrencies();		// Clear all currencies
```




<a name="relationship"></a>

#### Relationship 
```php
$product->currencies; 	// All currencies list of a single model
```




<a name="scopes"></a>

#### Scopes 
```php
// Get every element has no currency.
$p1 = Product::withoutCurrencies()->get();

// Get every element has EGP currency.
$p2 = Product::withCurrency('EGP')->get();

// Get every element has [EGP or USD or all] currency.
$p3 = Product::withAnyCurrency(['EGP', 'USD'])->get();
```



<a name="u"></a>

#### Creator
```php
// Get User Model
$product->currency()->user;
```




## License

[MIT license](LICENSE.md)