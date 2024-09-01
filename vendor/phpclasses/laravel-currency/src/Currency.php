<?php

namespace RaggiTech\Laravel\Currency;

use Illuminate\Database\Eloquent\Model;
use Config;

class Currency extends Model
{
    public static $currencies = [];
    public $currency = [];

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'model_type', 'model_id', 'currency_type', 'currency_amount'];

    public static function __callStatic($method, $parameters)
    {
        switch ($method) {
            case 'list':
                if (empty(self::$currencies)) self::$currencies = require __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'currencies.php';
                return self::$currencies;
                break;
            case 'setDefault':
                config(['RaggiTech.currency.default' => $parameters[0]]);
                break;
            case 'setOnly':
                config(['RaggiTech.currency.only' => $parameters[0]]);
                break;
        }
    }

    public function __toString()
    {
        return (string) $this->currency_amount;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }
}
