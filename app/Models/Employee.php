<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'fname',
        'lname',
        'gender',
        'address',
        'mobile',
        'email',
        'account_number',
        'account_name',
        'bank_name',
        'position_id',
        'paye',
        'sss',
        'his',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
