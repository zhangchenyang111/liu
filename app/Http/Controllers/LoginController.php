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
        $data=[
            'user_name'=>$user_name,
            'user_pwd'=>$user_pwd
        ];
        $api_url='http://39.107.72.115:8096/res';
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$api_url);
        curl_setopt($ch,CURLOPT_POST,0);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
//        curl_setopt($ch,CURLOPT_HTTPHEADER,[
//            'Content-Type:text/plain'
//        ]);
        $response=curl_exec($ch);
        $error=curl_errno($ch);
        if($error){
            $response=[
                'errno'=>5001,
                'msg'=>'curl err:'.$error
            ];
        }
        curl_close($ch);
        return $response;
    }
    //登录
    public function login(Request $request){
        $user_name=$request->input('user_name');
        $user_pwd=$request->input('user_pwd');
//        $user_name=123;
//        $user_pwd=123;
        $api_url="http://39.107.72.115:8096/login?user_name=$user_name&user_pwd=$user_pwd";
        return $this->urlget($api_url);

    }

    public function urlget($url){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $response=curl_exec($ch);
        $error=curl_errno($ch);
        if($error){
            $response=[
                'errno'=>5001,
                'msg'=>'curl err:'.$error
            ];
        }
         curl_close($ch);
        return $response;

    }
    //定义token值
    public function loginToken($user_id){
        return substr(sha1($user_id.time() .Str::random(10)),5,15);
    }
    //个人中心
    public function center(Request $request){
        $user_id=$_GET['user_id'];
        $token=$_GET['token'];
        $url="http://39.107.72.115:8096/center?user_id=$user_id";
        return $this->urlget($url);

    }


}