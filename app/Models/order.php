<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\orderDetail;
use  App\Models\User;

class order extends Model
{
  use HasFactory;
  protected $table = 'tbl_order';
  public function orderDetail()
  {
    return $this->hasMany(orderDetail::class, 'order_id'); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc  
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id'); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc  
  }
}
