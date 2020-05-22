<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/4/19
 * Time: 11:21 AM
 */

namespace App\WebSocket;


use App\Servers\User;
use EasySwoole\Pool\Manager;
use EasySwoole\Redis\Redis;

class WebSocketEvent
{

    static function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        $fd = $request->fd;
        echo '连接上了：'.$fd;
    }

    static function onClose(\swoole_server $server, int $fd, int $reactorId)
    {
        go(function () use($fd){
            $redisPool = Manager::getInstance()->get('redis');

            $redisPool->invoke(function (Redis $redis) use ($fd){
                $isExist =  $redis->hExists(User::ONLINE_LIST,$fd);
                //不存在 映射登录账号
                if($isExist){
                    $redis->hDel(User::ONLINE_LIST,$fd);
                }
            });
        });

        echo '关闭了'.$fd;

    }

}