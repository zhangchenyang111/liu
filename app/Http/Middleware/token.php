<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Closure;

class token
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//
        $user_id=$_GET['user_id'];
        $token=$_GET['token'];
//        echo $user_id;die;
//         echo 111;
            $redis_token_key='login_token:user_id:'.$user_id;
            $tokenredis=Redis::get($redis_token_key);
//            echo $tokenredis;die;

            if($token!= $tokenredis){
                die('无效的token');
            }
        return $next($request);

    }
}
