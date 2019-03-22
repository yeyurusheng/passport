<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckLogin
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
       // $token=$request->session()->get('u_token');
        $uid = $_COOKIE['uid'];
        $token = $_COOKIE['token'];
        if(isset($uid) && isset($token)){
            $redis_key =  $redis_key = "redis:login:token:".$uid;
            $r_token = Redis::get($redis_key);
            if ($r_token==$token){
                // TODO Token验证成功
            }else{
                // TODO Token验证失败
                header('Refresh:2;url=/login');
                echo 'token 错误';
            }
        }
        return $next($request);
    }
}
