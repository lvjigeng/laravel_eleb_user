<?php

namespace App\Http\Controllers;

use App\Model\Address;
use App\Model\Foods;
use App\Model\ShopCart;
use App\Model\ShopDetail;
use App\Sms;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    //短信接口返回
    public function sms(Request $request)
    {
        $tel=$request->tel;

//        if (!preg_match("/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$/", $tel)){
//            return response()->json(["status"=>'false',"message"=>'手机号码不正确']);
//        }
//        $params = array ();
//
//        // *** 需用户填写部分 ***
//        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
//        $accessKeyId = "LTAI30Q0ptAtAIA5";
//        $accessKeySecret = "wUPYvVcnBSQGQv0cQPnqnYEB2YEqSl";
//
//        // fixme 必填: 短信接收号码
//        $params["PhoneNumbers"] = $tel;
//
//        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
//        $params["SignName"] = "耕哥小吃";
//
//        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
//        $params["TemplateCode"] = "SMS_133795013";
//
//        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
//        //验证码
//        $code=mt_rand(100000,999999);
//
//        $params['TemplateParam'] = Array (
//
//            "code" => $code,
////            "product" => "阿里通信"
//        );
//
//        // fixme 可选: 设置发送短信流水号
////        $params['OutId'] = "12345";
//
//        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
////        $params['SmsUpExtendCode'] = "1234567";
//
//
//        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
//        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
//            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
//        }
//
//        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
//        $helper = new Sms();
//
//        // 此处可能会抛出异常，注意catch
//        $content = $helper->request(
//            $accessKeyId,
//            $accessKeySecret,
//            "dysmsapi.aliyuncs.com",
//            array_merge($params, array(
//                "RegionId" => "cn-hangzhou",
//                "Action" => "SendSms",
//                "Version" => "2017-05-25",
//            ))
//        // fixme 选填: 启用https
//        // ,true
//        );
//        $content->Message=='OK';
        $code=123456;
        if (1==1){
            //放在redis里面
            Redis::setex($tel,60*10,$code);
            return response()->json(["status"=>'true',"message"=>'发送验证码成功,十分钟内有效'.$code]);
        }else{
            return response()->json(["status"=>'false',"message"=>'发送验证失败']);
        }

    }
    //注册接口
    public function regist(Request $request)
    {
        //redis的验证码

        $code=Redis::get($request->tel);
        //判断验证码是否正确
        if ($request->sms!=$code){
            return response()->json([
                "status"=>'false',
                "message"=>'验证码不正确']);
        }
        $validator=Validator::make($request->all(),[
           'tel'=>[
               'unique:users',
               'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$/']
        ],[
            'tel.unique'=>'手机号码已经注册',
            'tel.regex'=>'手机号码格式不正确',

        ]);

        if($validator->fails()){
            return response()->json(['status'=>'false','message'=>$validator->errors()->first()]);
        }
        //保存数据库
        User::create([
            'name'=>$request->username,
            'tel'=>$request->tel,
            'password'=>bcrypt($request->password),
        ]);
        return '{
      "status": "true",
      "message": "注册成功"
    }';

    }
    //登录接口
    public function loginCheck(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>"required",
            'password'=>'required'
        ],[
            'name.required'=>'请添写手机号',
            'password.required'=>'请添写密码',
        ]);
        //如果有错
        if($validator->fails()){
            return response()->json([
                'status'=>'false',
                'message'=>$validator->errors()->first()]);
        }
        //验证密码
        if (Auth::attempt(['tel'=>$request->name,'password'=>$request->password])){
            return response()->json([
                "status"=>"true",
                "message"=>"登录成功",
                "user_id"=>Auth::user()->id,
                "username"=>Auth::user()->name
            ]);
        }
        else{
            return response()->json([
                "status"=>"false",
                "message"=>"账号或密码错误",
            ]);
        }
    }
    //修改密码接口
    public function changePassword(Request $request)
    {
       echo 1;
    }
    //忘记密码
    public function forgetPassword(Request $request)
    {
        /**
         * tel: 手机号
         * sms: 短信验证码
         * password: 密码
         */
       $user=DB::table('users')
           ->where('tel',$request->tel)
           ->first();
       //不存在这个用户
       if (!$user){
           return response()->json([
               'status'=>'false',
               'message'=>'用户不存在'
           ]);
       }

        //redis里的验证码
        $code=Redis::get($request->tel);
        //判断验证码是否正确
//        if ($request->sms!=$code){
//            return response()->json([
//                'status'=>'false',
//                'message'=>'验证码错误'
//            ]);
//        }
        User::where('tel',$request->tel)
            ->update(['password'=>bcrypt($request->password)]);
        //返回成功的json
        return response()->json([
           'status'=>'true',
            'message'=>'重置密码成功'
        ]);
    }
    //地址列表接口
    public function addressList()
    {
        $id=Auth::user()->id;
        $addresses=Address::where('user_id',$id)->get();
        return $addresses;
    }
    //添加地址接口
    public function addAddress(Request $request)
    {
//        dd($request->input());
        Address::create([
            'provence'=>$request->provence,
            'city'=>$request->city,
            'area'=>$request->area,
            'detail_address'=>$request->detail_address,
            'name'=>$request->name,
            'tel'=>$request->tel,
            'user_id'=>Auth::user()->id,
        ]);
        return response()->json([
            "status"=> "true",
            "message"=>"添加成功"
        ]);
        
    }
    //修改保存地址接口
    public function address(Request $request,Address $address)
    {
       return $address->find($request->id);
    }
    //修改保存地址接口
    public function editAddress(Request $request)
    {
        Address::where('id',$request->id)
            ->update([
            'provence'=>$request->provence,
            'city'=>$request->city,
            'area'=>$request->area,
            'detail_address'=>$request->detail_address,
            'name'=>$request->name,
            'tel'=>$request->tel,
        ]);
        return response()->json([
            "status"=> "true",
            "message"=>"修改成功"
        ]);
    }
    //保存购物车接口
    public function addCart(Request $request)
    {
        $goodsList=$request->input()['goodsList'];
        $goodsCount=$request->input()['goodsCount'];
        ShopCart::where('user_id',Auth::user()->id)->delete();
        foreach ($goodsList as $key=>$gId){

                ShopCart::create([
                    'goods_id'=>$gId,
                    'goods_count'=>$goodsCount[$key],
                    'user_id'=>Auth::user()->id,
                ]);
        }

        return response()->json([
            "status"=>"true",
            "message"=> "添加商品成功"
        ]);
    }
    //获取购物车数据接口
    public function cart(Request $request)
    {
        $carts=DB::table('shop_carts')->where('user_id',Auth::user()->id)->get();
        $json=[];
        $json['totalCost']=0;
        foreach ($carts as $cart){
            $food=Foods::where('id',$cart->goods_id)->first();
            $json['goods_list'][]=[
                'goods_id'=>"$cart->goods_id",
                'goods_name'=>$food->name,
                'goods_img'=>$food->food_img,
                'amount'=>$cart->goods_count,
                'goods_price'=>$food->price,
            ];
            $json['totalCost']+=$cart->goods_count*$food->price;
        }
//        $json['goodsList']=$goodsList;
        return response()->json($json);

    }



}
