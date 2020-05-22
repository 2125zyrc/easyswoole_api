<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/12
 * Time: 5:24 PM
 */

namespace App\Utils;


use App\Config\EvConfig;
use EasySwoole\Utility\Random;

class Tools
{
    /**
     * 以'.'来找出数组中的值
     * @param array $arr
     * @param string $str
     * @return array|mixed
     */
    public static function dots(array $arr, string $str){
        $dotArr = explode('.',$str);
        $res = $arr;
        foreach ($dotArr as $v){
            if(!isset($res[$v])){
                $res = [];
                break;
            }
            $res  = $res[$v];
        }
        return $res;
    }

    /**
     * 加密
     */
    public static function encry(string $str, $secret=''): string {
        return md5($secret.$str);
    }

    /**
     * 获取名称前缀
     */
    public static function getNamePre(string $str) :string {
        $tagArr = EvConfig::getConf('user_tag');
        $tag = Random::arrayRandOne($tagArr);
        return $tag.'_'.$str;
    }

}