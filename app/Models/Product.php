<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Cart;
use App\Models\OrderDetail;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'image', 'color', 'description', 'ram', 'storage', 'buy', 'margin', 'stock', 'category_id', 'action'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'products_id');
    }
}
