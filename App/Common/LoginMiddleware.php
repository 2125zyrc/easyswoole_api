<?php
/**
 * Created by PhpStorm.
 * User: samsun
 * Date: 2020/5/22
 * Time: 10:38 AM
 */

namespace App\Common;


trait LoginMiddleware
{
    protected $user =[];
    //路由前缀
    protected $prefixRouter = '/api';
    //不需要登录的路由
    protected $openRouter=[
        '/user/login',
        '/user/reg'
    ];

    /**
     * 是否需要登录
     * @return bool
     */
    public function isNeedLogin(){
        //拼接路由前缀
        $router = array_map(function ($v){
            return  $this->prefixRouter.$v;
        },$this->openRouter);

        //需要验证登录
        if(!in_array($this->request()->getUri()->getPath(), $router)){
            $this->checkToken();
        }
        return false;
    }

    //验证token
    public function checkToken(){
        $params = $this->request()->getRequestParam();
        $token = $params['token'] ?? '';
        //验证token
        $data = Token::decode($token);
        //如果正确赋值给全局
        $this->user = $data;
        return true;
    }
}