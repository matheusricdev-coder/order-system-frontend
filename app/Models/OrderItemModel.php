<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class OrderItemModel extends Model
{
    protected $table = 'order_items';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'quantity',
        'unit_price_amount',
        'unit_price_currency',
    ];
}
