<?php

namespace App\Http\Controllers;

use App\Model\ShopDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    //
    public function shops()
    {
        $shops = DB::table('shop_details')->get();
        foreach ($shops as $shop) {
            $shop->distance = 637;
            $shop->estimate_time = 30;
            $shop->service_code = $shop->service_rating;
            $shop->foods_code = $shop->foods_rating;
        }
        return $shops;
    }

    public function detail(Request $request)
    {
        $id = $request->id;
        $shop = DB::table('shop_details')
            ->where('id', $id)->first();
        //食物分类
        $foodCategories = DB::table('food_categories')
            ->where('shop_detail_id', $id)->get();

        foreach ($foodCategories as $foodCategory) {

            $foods = DB::table('foods')
                ->where([
                    ['shop_detail_id', $id],
                    ['food_category_id', $foodCategory->id]
                ])->get();

            foreach ($foods as $food) {
                $food->goods_id = $food->id;
                $food->goods_name = $food->name;
                $food->goods_price = $food->price;
                $food->goods_img = $food->food_img;
                $foodCategory->goods_list[] = $food;
            }

        }

        $shop->commodity = $foodCategories;

        $shop->evaluate = [
            [
                "user_id" => 12344,
                "username" => "w******k",
                "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                "time" => "2017-2-22",
                "evaluate_code" => 1,
                "send_time" => 30,
                "evaluate_details" => "不怎么好吃"
            ],
            [
                "user_id" => 12344,
                "username" => "w******k",
                "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                "time" => "2017-2-22",
                "evaluate_code" => 4.5,
                "send_time" => 30,
                "evaluate_details" => "很好吃"
            ],
            [
                "user_id" => 12344,
                "username" => "w******k",
                "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                "time" => "2017-2-22",
                "evaluate_code" => 5,
                "send_time" => 30,
                "evaluate_details" => "很好吃"
            ],
            [
                "user_id" => 12344,
                "username" => "w******k",
                "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                "time" => "2017-2-22",
                "evaluate_code" => 4.7,
                "send_time" => 30,
                "evaluate_details" => "很好吃"
            ],
            [
                "user_id" => 12344,
                "username" => "w******k",
                "user_img" => "http=>//www.homework.com/images/slider-pic4.jpeg",
                "time" => "2017-2-22",
                "evaluate_code" => 5,
                "send_time" => 30,
                "evaluate_details" => "很好吃"
            ]
        ];

        return json_encode($shop);





    }
}
