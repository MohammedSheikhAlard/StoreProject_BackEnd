<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'product_picture',
        'price',
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }
}
