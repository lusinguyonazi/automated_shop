<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'mobile', 'location', 'installed', 'paid', 'comments'
    ];

  
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
