<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable=[
      'order_code','order_birth_time','order_status','shop_id','shop_name','shop_img','provence','city','area','order_address','name','tel','user_id'
    ];
}
