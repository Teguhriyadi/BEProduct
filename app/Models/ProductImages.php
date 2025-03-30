<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    protected $table = "product_images";

    protected $guarded = [''];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
