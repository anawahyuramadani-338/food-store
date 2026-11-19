<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject; // Tambahkan ini
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements JWTSubject // Tambahkan implements JWTSubject
{
    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Dapatkan identifier yang akan disimpan di dalam claim JWT.
     * 
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Kembalikan array key value yang berisi custom claims.
     * 
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
