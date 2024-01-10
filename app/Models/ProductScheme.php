<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductScheme extends Model
{
    use HasFactory;

    protected $table = 'product_schemes';

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
