<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

Class Index extends Controller
{
    //对称解密
    public function dec(){
       $str=file_get_contents('php://input');
       echo 'base64加密值:'.$str; echo '</br>';
       $method='AES-256-CBC';
       $key='hhyyzz';
       $option=OPENSSL_RAW_DATA;
       $iv='aassddffgghhjjk1';
       $mi=base64_decode($str);
       echo "密文:".$mi; echo '</br>';
       $de_data=openssl_decrypt($mi,$method,$key,$option,$iv);
       echo  '数据：'.$de_data;

    }
    //非对称解密
    public function dec_no(){
        $str=file_get_contents('php://input');
        echo 'base64加密值:'.$str; echo '</br>';
        $mi=base64_decode($str);
        echo "密文:".$mi; echo '</br>';
        $pk=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($mi,$dec_data,$pk);
        echo  '数据：'.$dec_data;
    }
    //验证签名
    public function sign(){
        echo '<pre>'.print_r($_GET);echo '</br>';
        $str=file_get_contents('php://input');
        echo 'json'.$str; echo '</br>';
        $de_sign=$_GET['sign'];
        $pk=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        $rs=openssl_verify($str,base64_decode($de_sign),$pk);
//        var_dump($rs);
        if($rs != 1){
            echo '失败';
        }else{
            echo '成功';
        }
    }





    //注册
    public function res(){
        $str=file_get_contents('php://input');
        $mi=base64_decode($str);
        $pk=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($mi,$dec_data,$pk);
        $data=json_decode($dec_data,true) ;
        $email=$data['email'];
        $res=DB::table('login')->where(['email'=>$email])->first();
        if($res){
            $arr=[
                'code'=>504,
                'msg'=>'邮箱已存在'
            ];
            die(json_encode($arr));
        }
        $password=password_hash($data['password'],PASSWORD_BCRYPT);
        $arr=[
            'user_name'=>$data['user_name'],
            'password'=>$password,
            'email'=>$email
        ];
        $res2=DB::table('login')->insert($arr);
        if($res2){
            $arr=[
                'code'=>500,
                'msg'=>'注册成功'
            ];
            die(json_encode($arr,JSON_UNESCAPED_UNICODE));
//            header('Refresh:2;url=http://www.api.com/login');
        }else{
            $arr=[
                'code'=>501,
                'msg'=>'注册失败'
            ];
            die(json_encode($arr,JSON_UNESCAPED_UNICODE));
        }
    }
    //登陆
    public function login()
    {
        $str = file_get_contents('php://input');
        $mi = base64_decode($str);
        $pk = openssl_get_publickey('file://' . storage_path('app/keys/public.pem'));
        openssl_public_decrypt($mi, $dec_data, $pk);
        $data = json_decode($dec_data, true);
        $email = $data['email'];
        $password = $data['password'];
        $data=DB::table('login')->where(['email'=>$email])->first();
        if($data){
            //用库存在
            if(password_verify($password,$data->password)){
                //登陆逻辑
                $token=$this->loginToken($data->user_id);
                $user_id=$data->user_id;
                $key='token'.$user_id;
                Cache::set($key,$token,60*60*24*7);
                //Cache::get($key);
//               print_r($token);die;
//                $redis_token_key='login_token:user_id:'.$data->user_id;
//                Redis::set($redis_token_key,$token);
//                Redis::expire($redis_token_key,604800);
//                setcookie('token',$token,time()+200,'/','api.com',false,true);
//                setcookie('user_id',$data->user_id,time()+200,'/','api.com',false,true);
                //生成token
                $arr=[
                    'code'=>0,
                    'msg'=>'ok',
                    'data'=>$token
                ];
                die(json_encode($arr));
            }else{
                //登录失败
                $arr=[
                    'code'=>50001,
                    'msg'=>'密码不正确'
                ];
                die(json_encode($arr,JSON_UNESCAPED_UNICODE));
            }
        }else{
            //用户不存在
            $arr=[
                'code'=>50002,
                'msg'=>'用户不存在'
            ];
            die(json_encode($arr,JSON_UNESCAPED_UNICODE));
        }

    }
    //定义token值
    public function loginToken($uid){

        return substr(sha1($uid.time() .Str::random(10)),5,15);
    }
}
