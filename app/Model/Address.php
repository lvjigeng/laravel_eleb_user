<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    protected $fillable=[
        'provence','city', 'area','detail_address', 'name','tel','user_id'
    ];
}
