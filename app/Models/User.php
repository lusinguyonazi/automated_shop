<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 
        'last_name',
        'phone',
        'email',
        'password',
        'country_code',
        'dial_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


     public function sales()
    {
        return $this->hasMany(Sale::class);
    }


    public function businnessArea()
    {
        return $this->belongsTo(BusinessArea::class);
    }

    public function shops()
    {
       return $this->belongsToMany(Shop::class)->withPivot('is_default')->withTimestamps();
    }

    public function costs()
    {
        return $this->hasMany(AnCost::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function visits()
    {
        return $this->hasMany(CustomerVisit::class);
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function stores()
    {
       return $this->belongsToMany(Store::class)->withPivot('is_default')->withTimestamps();
    }

    public function productMade()
    {
        return $this->hasMany(ProductMadeApiTemp::class);
    }
}
