<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/13
 * Time: 10:54 AM
 */

namespace App\Exception;
use App\Common\ApiResponse;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class ExceptionHandler
{

    public static function handle( \Throwable $exception, Request $request, Response $response )
    {
        if($exception instanceof ApiException){
            return (new ApiResponse($response))->setCode($exception->getCode())->error($exception->getMessage());
        }else{
            var_dump($exception->getMessage());
            return (new ApiResponse($response))->setCode(500)->error($exception->getMessage());
        }
    }

}