<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\Order;
use  App\Models\Product;

class OrderDetail extends Model
{
  use HasFactory;

  protected $fillable = [
    'order_id',
    'product_id',
    'qty',
    'unit_price',
    'discount',
    'total',
  ];

  public function order()
  {
    return $this->belongsTo(Order::class, 'order_id');
  }
  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
