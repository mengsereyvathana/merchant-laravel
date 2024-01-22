<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Scheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'scheme_price',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
