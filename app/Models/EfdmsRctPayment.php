<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EfdmsRctPayment extends Model
{
    protected $fillable = [
        'efdms_rct_info_id', 'pmttype', 'pmtamount',
    ];
}
