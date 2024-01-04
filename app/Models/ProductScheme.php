<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductScheme extends Model
{
    use HasFactory;
    protected $table = 'tbl_product_price_scheme';
    public function products(){
        return $this->belongsTo(Product::class,'product_id'); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc
    }
}
