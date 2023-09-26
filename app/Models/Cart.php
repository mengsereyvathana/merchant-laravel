<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\products;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'tbl_cart';
    public function products(){
        return $this->belongsTo(products::class,'product_id'); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc  
      }
}
