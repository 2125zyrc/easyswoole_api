<?php
/**
 * Created by PhpStorm.
 * User: samsun
 * Date: 2020/5/21
 * Time: 9:20 PM
 */

namespace App\Exception;


class ValidateException extends ApiException
{
    protected $message = 'validate error';
    protected $code = 401;
}