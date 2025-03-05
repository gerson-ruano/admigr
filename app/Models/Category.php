<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    protected $fillable = ['name', 'status', 'slug', 'image'];
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function statusDescription(): HasOne
    {
        return $this->hasOne(CatItem::class, 'code', 'status')->where('category', 'status');
    }
}
