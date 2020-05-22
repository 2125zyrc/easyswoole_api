<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/12
 * Time: 4:28 PM
 */

namespace App\Config;


class Sys extends EvConfig
{
    public static function init() :array {
        return [
            'token_secret'=>'ev_token_secret_key!!!',
            'token_expire_time'=> time() + 3600,
        ];
    }
}