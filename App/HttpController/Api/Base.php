<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/12
 * Time: 4:09 PM
 */

namespace App\HttpController\Api;


use App\Common\LoginMiddleware;
use EasySwoole\Http\AbstractInterface\Controller;

class Base extends Controller
{
    use LoginMiddleware;

    function onRequest(?string $action): ?bool
    {
        if(!parent::onRequest($action)){
            return false;
        }
        $this->isNeedLogin();
        return true;
    }

    /**
     * 成功json
     * @param array $data
     * @param string $msg
     * @param int $code
     */
    function success($data=[], $msg="success", $code=200){
        $this->writeJson($code, $data, $msg);

    }

    /**
     * 失败json
     * @param array $data
     * @param string $msg
     * @param int $code
     */
    function error($data=[], $msg="error", $code=200){
        $this->writeJson($code, $data, $msg);
    }
}