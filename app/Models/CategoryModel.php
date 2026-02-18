<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class CategoryModel extends Model
{
    protected $table = 'categories';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'name'];
}
