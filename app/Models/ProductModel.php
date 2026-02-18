<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class ProductModel extends Model
{
    protected $table = 'products';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'price_amount',
        'price_currency',
        'category_id',
        'company_id',
    ];
}
