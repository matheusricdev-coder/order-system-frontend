<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function gallery(): HasMany
    {
        return $this->hasMany(ProductGalleryModel::class, 'product_id')
            ->orderBy('position');
    }
}
