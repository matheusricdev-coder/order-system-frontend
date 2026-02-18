<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class StockModel extends Model
{
    protected $table = 'stocks';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'product_id',
        'quantity_total',
        'quantity_reserved',
    ];
}
