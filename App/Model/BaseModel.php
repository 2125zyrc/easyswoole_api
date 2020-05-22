<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/19
 * Time: 11:54 AM
 */

namespace App\Model;


use EasySwoole\ORM\AbstractModel;

class BaseModel extends AbstractModel
{
    protected $autoTimeStamp = true; //是否开启自动时间戳，默认值 false
    protected $createTime = 'created';
    protected $updateTime = 'updated';
}