<?php

namespace App;

use App\Enums\UseIngredientType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use CastsEnums;

    protected $enumCasts = [
        'use_type' => UseIngredientType::class,
    ];

    protected $fillable = [
        'title',
        'mass',
        'quantity',
        'price',
        'use_type',
    ];
}
