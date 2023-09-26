<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class scheme extends Model
{
    use HasFactory;
    protected $table = 'tbl_scheme';  
    public function users(){
        return $this->hasMany(User::class); //this scheme_id has many products
    }

}
