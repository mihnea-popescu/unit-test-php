<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'stock',
        'price',
        'sale_price'
    ];

    /**
     * Get the correct price of the product
     * Between sale price and regular price
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->sale_price ? ($this->sale_price <= $this->price ? $this->sale_price : $this->price) : $this->price;
    }
}
