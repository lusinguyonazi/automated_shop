<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EfdmsZReportVatTotal extends Model
{

    protected $fillable = [
        'efdms_z_report_id', 'vatrate', 'netamount', 'taxamount',
    ];
}
