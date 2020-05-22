<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/12
 * Time: 4:11 PM
 */

namespace App\HttpController\Api;


use App\Common\Token;
use App\Model\User\UserModel;
use App\Utils\Tools;
use App\Validate\UserValidate;

class User extends Base
{
    use UserValidate;

    public function index()
    {
        $lists = UserModel::create()->get();
        $this->success($lists);
    }

    /**
     * 获取详情
     */
    public function detail(){
        $userId = $this->user['id'];
        $info = UserModel::getById($userId);
        $this->success($info);
    }

    /**
     *登录
     */
    public function login(){
        $params =  $this->request()->getRequestParam();
        $this->validateAccount($params); //验证参数
        $row = UserModel::isExistsByAccount($params);
        if(!$row){
            $this->error('账号或者密码不正确'); //这里也可以throw new apiException
        }else{
            $token = Token::encry($row);
            $this->success(['toekn'=>$token]);
        }
    }

    /**
     * 注册
     */
    public function reg(){
        $params =  $this->request()->getRequestParam();
        $this->validateAccount($params);//验证参数
        $row = UserModel::isExistsByPhone($params['phone']);
        if($row){
            $this->error('账号已经存在');
        }else{
            //保存信息
            $params['username'] = (isset($params['username']) && $params['username']) ? $params['username'] : Tools::getNamePre($params['phone']);
            UserModel::create()->data($params)->save();

            //查询账户信息
            $info = UserModel::findByAccount($params);
            //生成token
            $token = Token::encry($info);
            $this->success(['toekn'=>$token]);
        }
    }

}