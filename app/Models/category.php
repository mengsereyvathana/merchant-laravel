<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\products;

class category extends Model
{
    use HasFactory;
    protected $table = 'tbl_category';
    public function products(){
        return $this->hasMany(products::class); //this category_id has many products
    }

}
