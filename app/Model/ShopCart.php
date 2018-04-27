<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShopCart extends Model
{
    //
    protected $fillable=[
        'goods_id','goods_count','user_id'
    ];
}
