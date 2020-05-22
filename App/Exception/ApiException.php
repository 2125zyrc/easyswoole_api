<?php
/**
 * Created by PhpStorm.
 * User: samsun
 * Date: 2020/5/21
 * Time: 9:14 PM
 */

namespace App\Exception;


use EasySwoole\Http\Exception\Exception;
use Throwable;

class ApiException extends Exception
{
    protected $message = 'base error';
    protected $code = 200;

    public function __construct(string $message = "", int $code=0, Throwable $previous = null)
    {
        $message = $message ?: $this->message;
        $code = $code ?: $this->code;
        parent::__construct($message, $code, $previous);
    }
}