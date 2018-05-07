<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrdersGoods extends Model
{
    //
    protected $fillable=[
      'order_id','goods_id','amount','amount','goods_price','goods_name','goods_img'
    ];
}
