<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\Order;
use  App\Models\Product;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'tbl_order_detail';
    protected $fillable = ['order_id','product_id','qty','unit_price','discount'];
    public function order(){
        return $this->belongsTo(Order::class); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc
      }
    public function product(){
        return $this->belongsTo(Product::class); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc
      }
}
