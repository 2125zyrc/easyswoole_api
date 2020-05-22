<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/12
 * Time: 4:28 PM
 */

namespace App\Config;


class UserTag extends EvConfig
{
    public static function init() :array {
        return [
            '码农',
            '主管',
            '产品',
            '运营',
            'boss',
            '啥也不是'
        ];
    }
}