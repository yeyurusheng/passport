<?php

namespace App\Http\Controllers\Pass;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class PassPcController extends Controller
{
    /** 注册页面 */
    public function register(){
        $recurl =  urldecode( $_GET['recurl'] ?? env('PASS_LOGIN'));
        $data = [
            'recurl' => $recurl
        ];
        return view('pass.register',$data);
    }

    /** 注册 */
    public function doreg(Request $request)
    {
        $u_name = $request->input('u_name');
        $pwd1 = $request->input('pwd');
        $pwd2 = $request->input('pwd2');
        $age = $request->input('age');
        $email = $request->input('email');
        $recurl = urldecode($request->input('recurl') ?? env('PASS_LOGIN'));
        if ($pwd2 != $pwd1) {
            echo '密码与确认密码不一致';
            header('refresh:2,/register');
        }
        $pwd = password_hash($pwd1, PASSWORD_BCRYPT);
        $data = [
            'u_name' => $u_name,
            'pwd' => $pwd,
            'age' => $age,
            'email' => $email,
            'time' => time()
        ];

        $where = ['u_name' => $u_name];
        $res = UserModel::where($where)->first();
        if ($res) {
            echo '账号已存在';
            header('refresh:2,/register');
        } else {
            $list = UserModel::insert($data);
            if ($list) {
                echo '注册成功';
                setcookie('list', $list, time() + 86400, '/', 'tactshan.com', false, true);
                header('refresh:2,/login');
            } else {
                echo '注册失败';
                header('refresh:2,/register');
            }
        }
    }

    /** 登录页面 */
    public function login(){
        $recurl = urldecode( $_GET['recurl'] ?? env('PASS_LOGIN'));
        $data = [
            'recurl' => $recurl
        ];
        return view('pass.login',$data);
    }

    /** 登录 */
    public function dologin(Request $request){
        $u_name = $request->input('u_name');
        $pwd = $request->input('pwd');
        $recurl = urldecode($request->input('recurl') ?? env('PASS_LOGIN'));
        $add=UserModel::where(['u_name'=>$u_name])->first();
        if(empty($add)){
            echo '账号不存在';
        }
        if(password_verify($pwd,$add->pwd)){
            $token = substr(md5(time().mt_rand(1,99999)),10,10);
            setcookie('uid',$add->uid,time()+86400,'/','tactshan.com',false,true);
            setcookie('token',$token,time()+86400,'/','tactshan.com',false,true);
            $request->session()->put('u_token',$token);
            $request->session()->put('uid',$add->uid);
            //存数据到redis
            $redis_key = "redis:login:token:".$add->uid;
            Redis::set($redis_key,$token);
            Redis::expire($redis_key,7200);

            echo '登陆成功';
            header('refresh:2;url='.$recurl);
        }else{
            echo '密码错误';
        };
    }
    public function quit(){
        $uid = $_COOKIE['uid'];
        $redis_key = "redis:login:token:".$uid;
        Redis::del($redis_key);
        setcookie('uid','',time()-1,'/','tactshan.com',false,true);
        $a = setcookie('token','',time()-1,'/','tactshan.com',false,true);
        if($a){
            echo '退出成功';
        }
        header("refresh:2,./pclogin");
    }
}
