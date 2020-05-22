<?php
/**
 * Created by PhpStorm.
 * User: samsun
 * Date: 2020/5/21
 * Time: 8:37 PM
 */

namespace App\Validate;

use App\Exception\ValidateException;
use EasySwoole\Validate\Validate;
trait UserValidate
{
    public function validateAccount($params){
        $validator = new Validate();
        $validator->addColumn('phone', '手机号')->required('手机号不能为空');
        $validator->addColumn('password', '密码')->required('密码不能为空');
        if(!$validator->validate($params)){
            throw new ValidateException($validator->getError()->__toString());
        }
        return true;
    }

}