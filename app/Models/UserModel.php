<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

final class UserModel extends Authenticatable
{
    protected $table = 'users';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'surname',
        'birth_date',
        'email',
        'phone',
        'password',
        'cpf',
        'active',
        'company_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'active' => 'boolean',
        ];
    }
}
