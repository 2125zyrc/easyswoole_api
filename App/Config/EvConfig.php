<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/12
 * Time: 4:31 PM
 */

namespace App\Config;
use App\Utils\Tools;
use EasySwoole\Utility\Str;

abstract class EvConfig
{
    //抽象init方法
    protected abstract static function init(): array ;

    /**
     * 获取配置接口
     * @param $className Config目录下的类的名称
     * @param string $key 返回数组中的键的名称 多位数组中用"."分开 例如key1.key2.key3
     * @return array|mixed
     */
    public final static function getConf($className,$key=''){
        $className = Str::studly($className); //转成大驼峰
        $class = __NAMESPACE__.'\\'.$className;
        if(!class_exists($class)){
            return [];
        }
        //调去子类的init方法
        $array = call_user_func(array($class,'init'));
        if(!$key){
            return $array;
        }
        return Tools::dots($array, $key);

    }

}