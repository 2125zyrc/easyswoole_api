<?php
/**
 * Created by PhpStorm.
 * User: samsun
 * Date: 2020/5/21
 * Time: 5:06 PM
 */

namespace App\Model\User;


use EasySwoole\ORM\AbstractModel;

class UserRoleModel extends AbstractModel
{
    protected $tableName = 'user_role';

    public function permission()
    {
        return $this->hasMany(UserRoleModel::class, null, 'id', 'user_id');
    }


    public function getPermissionByUserId($userId){
        $reletion = self::create()->with('permission')
            ->where('user_id',$userId)
            ->get();

    }
}