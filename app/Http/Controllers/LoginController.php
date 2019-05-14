<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

Class LoginController extends Controller
{
    //注册
    public function res(Request $request)
    {
        $user_name = $request->input('user_name');
        $user_pwd = $request->input('user_pwd');
//        $user_name='zhangyang';
//        $user_pwd='123456';
        $res = DB::table('login')->where(['user_name' => $user_name])->first();
        if ($res) {
            return json_encode(['msg' => '用户名已存在', 'status' => 0], JSON_UNESCAPED_UNICODE);
        } else {
            $arr = [
                'user_name' => $user_name,
                'user_pwd' => $user_pwd
            ];
            $data = DB::table('login')->insert($arr);
            if ($data) {
                return json_encode(['msg' => '注册成功', 'status' => 1], JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode(['msg' => '注册失败', 'status' => 0], JSON_UNESCAPED_UNICODE);
            }
        }
    }
    //登录
    public function login(Request $request){
        $user_name=$request->input('user_name');
        $user_pwd=$request->input('user_pwd');
        $res=DB::table('login')->where(['user_name'=>$user_name,'user_pwd'=>$user_pwd])->first();

        if($res){
            return json_encode(['msg'=>'登陆成功','status'=>1],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(['msg'=>'用户名或者密码错误','status'=>0],JSON_UNESCAPED_UNICODE);
        }
    }

}