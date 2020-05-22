<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/4/29
 * Time: 9:25 PM
 */

namespace App\WebSocket\Controller;


use EasySwoole\Pool\Manager;
use EasySwoole\Redis\Redis;
use http\Env\Request;

class User extends Base
{
    /**
     * 登录
     */
    public function login(){
        go(function (){
            $args = $this->caller()->getArgs();

            $client = $this->caller()->getClient();
            $phone = $args['phone'];

            $redisPool = Manager::getInstance()->get('redis');
            $redisPool->invoke(function (Redis $redis) use ($phone,$client){
               $isExist =  $redis->hExists(\App\Servers\User::ONLINE_LIST,$client->getFd());
               //不存在 映射登录账号
               if(!$isExist){
                   $redis->hSet(\App\Servers\User::ONLINE_LIST,$client->getFd(),$phone);
               }
            });
        });
    }


}