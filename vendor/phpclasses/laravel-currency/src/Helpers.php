<?php

use RaggiTech\Laravel\Currency\Currency;

function currenciesList()
{
    $data = [];
    foreach (Currency::list() as $code => $info) {
        $data[$code] = $info['name'];
    }
    return $data;
}
