<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatItem extends Model
{
    use HasFactory;

    protected $table = 'cat_items'; // Nombre de la tabla

    protected $fillable = ['category', 'code', 'description'];
}
