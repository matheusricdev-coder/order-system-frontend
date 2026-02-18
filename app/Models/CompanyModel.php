<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class CompanyModel extends Model
{
    protected $table = 'companies';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'trade_name'];
}
