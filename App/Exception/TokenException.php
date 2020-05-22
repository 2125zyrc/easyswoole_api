<?php
/**
 * Created by PhpStorm.
 * User: samsun
 * Date: 2020/5/21
 * Time: 9:20 PM
 */

namespace App\Exception;


class TokenException extends ApiException
{
    protected $message = 'token error';
    protected $code = 403;
}