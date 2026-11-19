<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'customer_id',
        'snap_token',
        'courier',
        'courier_service',
        'courier_cost',
        'weight',
        'name',
        'phone',
        'province_id',
        'city_id',
        'address',
        'grand_total',
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}