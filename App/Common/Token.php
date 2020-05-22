<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/12
 * Time: 4:25 PM
 */

namespace App\Common;
use App\Config\EvConfig;
use App\Exception\TokenException;
use EasySwoole\Jwt\Exception;
use EasySwoole\Jwt\Jwt;


class Token
{
    /**
     * 生成token
     * @param $data 需要生成token的数据
     * @return string
     */
    public static function encry($data){
        $secret = EvConfig::getConf('sys','token_secret');
        $expireTime = EvConfig::getConf('sys','token_expire_time');


        $jwtObject = Jwt::getInstance()->setSecretKey($secret)->publish();
        $jwtObject->setAlg('HMACSHA256');
        $jwtObject->setExp($expireTime);
        $jwtObject->setData($data);
        return $jwtObject->__toString();
    }

    public static function decode($token){
        try{
            $secret = EvConfig::getConf('sys','token_secret');
            $jwtObject = Jwt::getInstance()->setSecretKey($secret)->decode($token);
            $status = $jwtObject->getStatus();
            switch ($status)
            {
                case  1:
                    return $jwtObject->getData();
                    break;
                case  -1:
                    throw new TokenException('Token无效');
                case  -2:
                    throw new TokenException('Token过期');
            }
        }catch (Exception $e){
            throw new TokenException($e->getMessage());
        }
    }
}