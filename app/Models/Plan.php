<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'price', 'max_links', 'highlighted', 'active', 'cta', 'features', 'sort_order'];

    protected function casts(): array
    {
        return [
            'features'    => 'array',
            'highlighted' => 'boolean',
            'active'      => 'boolean',
            'price'       => 'decimal:2',
        ];
    }
}
