<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'position',
        'as_default',
        'type', //0 - real, 1 - virtual
        'virtual_amount'
    ];
}
