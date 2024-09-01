<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EfdmsZReportPayment extends Model
{

    protected $fillable = [
        'efdms_z_report_id', 'pmttype', 'pmtamount',
    ];
}
