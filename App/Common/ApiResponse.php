<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/13
 * Time: 11:06 AM
 */

namespace App\Common;



class ApiResponse
{

    private $statusCode = 200;

    private $response = null;

    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @param $statusCode 设置返回状态码  像更具体的10001这种就不封装了
     * @return $this
     */
    public function setCode($statusCode){
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * 成功
     * @param string $msg
     * @param string $result
     * @return bool
     */
    public  function success($msg='', $result='success'){
        return $this->writeJson($msg,$result);
    }

    /**
     * 失败
     * @param string $msg
     * @param string $result
     * @return bool
     */
    public  function error($msg='', $result='error'){
        return $this->writeJson($msg, $result);
    }

    /**
     * 这里把easyswoole中writeJson方法拿过来
     * @param null $result
     * @param null $msg
     * @return bool
     */
    public function writeJson($msg = null, $result = null)
    {

        if (!$this->response->isEndResponse()) {
            $data = Array(
                "code" => $this->statusCode,
                "result" => $result,
                "msg" => $msg
            );
            $this->response->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response->withStatus($this->statusCode);
            return true;
        } else {
            return false;
        }
    }
}