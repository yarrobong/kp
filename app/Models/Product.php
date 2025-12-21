<?php

namespace App\Models;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'category',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrl()
    {
        if ($this->image && file_exists(public_path($this->image))) {
            return $this->image;
        }
        return '/css/placeholder-product.svg';
    }
}
