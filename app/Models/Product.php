<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "product";

    protected $guarded = [''];

    public function categories()
    {
        return $this->hasMany(ProductCategory::class);
    }
}
