<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'basic_unit',
    ];



    public function pmItems()
    {
        return $this->hasMany(PmItem::class);
    }
}
