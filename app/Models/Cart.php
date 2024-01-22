<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\Product;

class Cart extends Model
{
      use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'qty',
        'unit_price',
        'total',
    ];
      public function products()
      {
        return $this->belongsTo(Product::class, 'product_id'); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc
      }
}
