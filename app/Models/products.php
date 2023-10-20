<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\category;
use App\Models\Cart;
use App\Models\orderDetail;

class products extends Model
{
    use HasFactory;
    protected $table = 'tbl_products';
    // public $timestamp = false;
    // protected $guarded = ['updated_at'];
    protected $fillable = ['name', 'price', 'image', 'color', 'description', 'ram', 'storage', 'buy', 'margin', 'stock', 'category_id', 'action'];
    public function category()
    {
        return $this->belongsTo(category::class, 'category_id'); //this products fectch witch on of category
    }
    public function cart()
    {
        return $this->hasMany(Cart::class); //this products_id has many cart 
    }
    public function orderDetail()
    {
        return $this->hasMany(orderDetail::class, 'products_id'); //this products_id has many cart 
    }
}
