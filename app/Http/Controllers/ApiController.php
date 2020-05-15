<?php

namespace App\Http\Controllers;

use App\Model\Address;
use App\Model\Foods;
use App\Model\Order;
use App\Model\OrdersGoods;
use App\Model\ShopAccount;
use App\Model\ShopCart;
use App\Model\ShopDetail;
use App\Sms;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
{
    //列表接口返回
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

    //商家详情接口返回
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

    //短信验证码接口返回
    public function sms(Request $request)
    {
        $tel = $request->tel;

        if (!preg_match("/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$/", $tel)){
            return response()->json(["status"=>'false',"message"=>'手机号码不正确']);
        }
        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "";
        $accessKeySecret = "";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        //验证码
        $code=mt_rand(100000,999999);

        $params['TemplateParam'] = Array (

            "code" => $code,
//            "product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new Sms();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
//        $content->Message=='OK';
//        $code = 123456;
        if ($content->Message=='OK') {
            //放在redis里面
            Redis::setex($tel, 60 * 10, $code);
            return response()->json(["status" => 'true', "message" => '发送验证码成功,十分钟内有效' . $code]);
        } else {
            return response()->json(["status" => 'false', "message" => '发送验证失败']);
        }

    }

    //注册接口
    public function regist(Request $request)
    {
        //redis的验证码

        $code = Redis::get($request->tel);
        //判断验证码是否正确
        if ($request->sms != $code) {
            return response()->json([
                "status" => 'false',
                "message" => '验证码不正确']);
        }
        $validator = Validator::make($request->all(), [
            'username'=>'required',
            'password'=>'required|min:6',
            'tel' => [
                'unique:users',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$/']
        ], [
            'username.required'=>'姓名不能为空',
            'password.required'=>'密码不能为空',
            'password.min'=>'密码最小为6位',
            'tel.unique' => '手机号码已经注册',
            'tel.regex' => '手机号码格式不正确',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'false', 'message' => $validator->errors()->first()]);
        }
        //保存数据库
        User::create([
            'name' => $request->username,
            'tel' => $request->tel,
            'password' => bcrypt($request->password),
        ]);
        return '{
      "status": "true",
      "message": "注册成功"
    }';

    }

    //登录接口
    public function loginCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'password' => 'required'
        ], [
            'name.required' => '请添写手机号',
            'password.required' => '请添写密码',
        ]);
        //如果有错
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->errors()->first()]);
        }
        //验证密码
        if (Auth::attempt(['tel' => $request->name, 'password' => $request->password, 'status'=>1])) {
            return response()->json([
                "status" => "true",
                "message" => "登录成功",
                "user_id" => Auth::user()->id,
                "username" => Auth::user()->name
            ]);
        } else {
            return response()->json([
                "status" => "false",
                "message" => "账号或密码错误,或账号被禁用",
            ]);
        }
    }

    //修改密码接口
    public function changePassword(Request $request)
    {
        //验证
        $validator = Validator::make($request->all(), [
            'oldPassword'=>'required',
            'newPassword'=>'required|min:6',

        ], [
            'oldPassword.required'=>'原密码不能为空',
            'newPassword.required'=>'新密码不能为空',
            'newPassword.min'=>'新密码最小为6位'

        ]);
        //如果验证不过返回消息
        if ($validator->fails()) {
            return response()->json(['status' => 'false', 'message' => $validator->errors()->first()]);
        }

        $id = Auth::user()->id;
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;
        $res = DB::table('users')->where('id', $id)->first();
        //验证密码
        if (!Hash::check($oldPassword, $res->password)) {
            return response()->json([
                'status' => 'false',
                'message' => '原密码不正确'
            ]);
        }
        //修改密码
        DB::table('users')
            ->where('id',$id)
            ->update([
           'password'=>bcrypt($newPassword)
        ]);
        //返回提示
        return response()->json([
            'status'=>'true',
            'message'=>'修改成功'
        ]);
    }

    //忘记密码
    public function forgetPassword(Request $request)
    {
        /**
         * tel: 手机号
         * sms: 短信验证码
         * password: 密码
         */
        //验证
        $validator = Validator::make($request->all(), [
            'Password'=>'required|min:6',

        ], [
            'Password.required'=>'密码不能为空',
            'Password.min'=>'密码最小为6位'

        ]);
        //如果验证不过返回消息
        if ($validator->fails()) {
            return response()->json(['status' => 'false', 'message' => $validator->errors()->first()]);
        }

        $user = DB::table('users')
            ->where('tel', $request->tel)
            ->first();
        //不存在这个用户
        if (!$user) {
            return response()->json([
                'status' => 'false',
                'message' => '用户不存在'
            ]);
        }

        //redis里的验证码
        $code = Redis::get($request->tel);
        //判断验证码是否正确
        if ($request->sms!=$code){
            return response()->json([
                'status'=>'false',
                'message'=>'验证码错误'
            ]);
        }
        User::where('tel', $request->tel)
            ->update(['password' => bcrypt($request->password)]);
        //返回成功的json
        return response()->json([
            'status' => 'true',
            'message' => '重置密码成功'
        ]);
    }

    //地址列表接口
    public function addressList()
    {
        $id = Auth::user()->id;
        $addresses = Address::where('user_id', $id)->get();
        return $addresses;
    }

    //添加地址接口
    public function addAddress(Request $request)
    {
        $partten = '/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$/';
        if (!preg_match($partten, $request->tel)) {
            return response()->json([
                "status" => "false",
                "message" => "请添写正确的手机号码"
            ]);
        }
        Address::create([
            'provence' => $request->provence,
            'city' => $request->city,
            'area' => $request->area,
            'detail_address' => $request->detail_address,
            'name' => $request->name,
            'tel' => $request->tel,
            'user_id' => Auth::user()->id,
        ]);
        return response()->json([
            "status" => "true",
            "message" => "添加成功"
        ]);

    }

    //修改保存地址接口
    public function address(Request $request, Address $address)
    {
        return $address->find($request->id);
    }

    //修改保存地址接口
    public function editAddress(Request $request)
    {
        Address::where('id', $request->id)
            ->update([
                'provence' => $request->provence,
                'city' => $request->city,
                'area' => $request->area,
                'detail_address' => $request->detail_address,
                'name' => $request->name,
                'tel' => $request->tel,
            ]);
        return response()->json([
            "status" => "true",
            "message" => "修改成功"
        ]);
    }

    //保存购物车接口
    public function addCart(Request $request)
    {
        $goodsList = $request->input()['goodsList'];
        $goodsCount = $request->input()['goodsCount'];
        ShopCart::where('user_id', Auth::user()->id)->delete();
        foreach ($goodsList as $key => $gId) {

            ShopCart::create([
                'goods_id' => $gId,
                'goods_count' => $goodsCount[$key],
                'user_id' => Auth::user()->id,
            ]);
        }

        return response()->json([
            "status" => "true",
            "message" => "添加商品成功"
        ]);
    }

    //获取购物车数据接口
    public function cart(Request $request)
    {
        $carts = DB::table('shop_carts')->where('user_id', Auth::user()->id)->get();
        $json = [];
        $json['totalCost'] = 0;
        foreach ($carts as $cart) {
            $food = Foods::where('id', $cart->goods_id)->first();
            $json['goods_list'][] = [
                'goods_id' => "$cart->goods_id",
                'goods_name' => $food->name,
                'goods_img' => $food->food_img,
                'amount' => $cart->goods_count,
                'goods_price' => $food->price,
            ];
            $json['totalCost'] += $cart->goods_count * $food->price;
        }
//        $json['goodsList']=$goodsList;
        return response()->json($json);

    }

    //生成订单接口
    public function addOrder(Request $request)
    {
        $order_code=date('Ymd').uniqid();  //订单编码

        $carts=ShopCart::where('user_id',Auth::user()->id)->get();    //购物车的商品
        $goodsList=[];         //商品列表
        foreach ($carts as $cart){
            $good=Foods::find($cart->goods_id);
            $good['amount']=$cart->goods_count;  //把商品数量放入goodsList 方便算总账
            $goodsList[]=$good;                //所有商品详情
        }

        $goodsId=$carts[0]['goods_id'];   //商品id
        $shopId=Foods::where('id',$goodsId)->first()->shop_detail_id;  //店铺id
        $shop=ShopDetail::find($shopId);  //店铺详情

        $addressId=$request->input()['address_id'];   //地址id
        $address=Address::find($addressId);    //地址详情

        DB::transaction(function () use ($goodsList,$shop,$address,$order_code){            //开启事务

            $order_birth_time=time();  //订单生成时间

            $order=Order::create([                 //生成订单信息
                'order_code' =>$order_code,
                'order_birth_time'=>$order_birth_time,
                'order_status'=>0,
                'shop_id'=>$shop->id,
                'shop_name'=>$shop->shop_name,
                'shop_img'=>$shop->shop_img,
                'provence'=>$address->provence,
                'city'=>$address->city,
                'area'=>$address->area,
                'order_address'=>$address->detail_address,
                'name'=>$address->name,
                'tel'=>$address->tel,
                'user_id'=>$address->user_id
            ]);
            foreach ($goodsList as $good){
                OrdersGoods::create([
                    'order_id'=>$order->id,
                    'goods_id'=>$good->id,
                    'amount'=>$good->amount,
                    'goods_price'=>$good->price,
                    'goods_name'=>$good->name,
                    'goods_img'=>$good->food_img,
                ]);
            }
        });

            $order_id=DB::table('orders')->where('order_code',$order_code)->first()->id;
            //给用户发送下单成功短信
            $this->order_sms();
            //给商家发送邮件
            $email=ShopAccount::find($shopId)->email;
            Mail::send('new_order',[],function ($message) use ($email){
                $message->to($email)->subject('新的订单');
            });

            return response()->json([
                'status'=>'true',
                'message'=>'生成订单成功',
                'order_id'=>$order_id
            ]);
    }
    
    //下单成功给用户发送短信方法
    public function order_sms()
    {
        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "";
        $accessKeySecret = "";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = Auth::user()->tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项



        $params['TemplateParam'] = Array (

            "name" => '',
//            "product" => ""
        );

        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new Sms();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );

//
    }

    //订单详情接口
    public function order(Request $request)
    {
        $order=Order::find($request->id);    //需要返回的json字符串
        //order的状态
        $order->order_status=$order->order_status==0?'代付款':'已付款';
        //拼接生成时间
        $order->order_birth_time=date('Y-m-d H:i:s',$order->order_birth_time);
        //拼接地址
        $order->order_address=$order->provence.$order->city.$order->area.$order->order_address;
        //购物车的所有商品详情
        $goodsList=OrdersGoods::where('order_id',$order->id)->get();  //所有商品详情
        //拼接商品列表
        $order->goods_list=$goodsList;
        $order_price=0;                 //订单总价
        foreach ($goodsList as $good){
            $order_price+=$good->goods_price*$good->amount;   //算总价
        }
        $order['order_price']=$order_price;
        return $order;
    }

    //订单列表接口
    public function orderList()
    {
        $orderList=[];
        $orders=Order::where('user_id',Auth::user()->id)->get();
        foreach ($orders as $order){
         //需要返回的json字符串
        //拼接生成时间
        $order->order_birth_time=date('Y-m-d H:i:s',$order->order_birth_time);
        //order的状态
        $order->order_status=$order->order_status==0?'代付款':'已付款';
        //拼接地址
        $order->order_address=$order->provence.$order->city.$order->area.$order->order_address;
        //购物车的所有商品详情
        $goodsList=OrdersGoods::where('order_id',$order->id)->get();  //所有商品详情
        //拼接商品列表
        $order->goods_list=$goodsList;
        $order_price=0;                 //订单总价
        foreach ($goodsList as $good){
            $order_price+=$good->goods_price*$good->amount;   //算总价
        }
        $order->order_price=$order_price;

        $orderList[]=$order;
        }
        return $orderList;
    }

}
