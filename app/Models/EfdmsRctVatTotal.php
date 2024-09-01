<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EfdmsRctVatTotal extends Model
{

    protected $fillable = [
        'efdms_rct_info_id', 'vatrate', 'netamount', 'taxamount',
    ];
}
