<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\OrderDetail;
use  App\Models\User;

class Order extends Model
{
  use HasFactory;

    protected $fillable = [
        'invoice',
        'user_id',
        'address_id',
        'pay_by',
        'status',
    ];

  public function orderDetail()
  {
    return $this->hasMany(OrderDetail::class, 'order_id'); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id'); //this products_id fetch the products  ,product_id fro call product id in prducts table compare with produtc
  }
}
