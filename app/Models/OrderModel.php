<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class OrderModel extends Model
{
    protected $table = 'orders';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'status',
    ];
}
