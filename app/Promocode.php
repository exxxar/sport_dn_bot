<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $fillable = [
        'code',
        'activated',
        'user_id',
    ];

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
