<?php
/**
 * Created by PhpStorm.
 * UserModel: samsun
 * Date: 2020/5/19
 * Time: 11:55 AM
 */

namespace App\Model\User;


use App\Model\BaseModel;
use App\Utils\Tools;

class UserModel extends BaseModel
{
    protected $tableName = 'user';

    public function role()
    {
        return $this->hasMany(UserRoleModel::class, null, 'id', 'user_id');
    }

    /**
     * 密码修改器
     * @param $value
     * @return string
     */
    protected function setPasswordAttr($value)
    {
        return Tools::encry($value);
    }

    /**
     * 根据id查询信息
     * @param $id
     * @return $this|array|bool|null
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public static function getById($id){
        $data = self::create()->with('role')
            ->field('id,phone,username,created,updated')
            ->where([
                'id'=>$id
            ])->get();
        //获取角色id
        $roleIdArr = array_column($data->role,'role_id');
        $roleNameArr = [];
        if(!empty($roleIdArr)){ //必须要判断一下 否则报错
            $roleNameArr =  RoleModel::create()->where([
                'id'  => [$roleIdArr, 'in'],
            ])->column('name');
        }
        $data->role = $roleNameArr;
        return $data;

    }

    /**
     * 根据手机号查询
     * @param $phone
     * @return bool
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public static function isExistsByPhone($phone){
        $row = self::create()
            ->field('id')
            ->where([
                'phone'=>$phone
            ])->get();
        if($row->id){
            return true;
        }
        return false;
    }

    /**
     * 根据账号密码查询是否存在
     * @param array $params
     * @return array|bool
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public static function isExistsByAccount(array $params){
        $row = self::findByAccount($params);
        if(!$row->id){
            return false;
        }
        return $row->toArray();//如果为空的时候不能直接toArray() 有空的朋友去github提一下
    }

    /**
     *  根据账号密码查询
     * @param array $params
     * @return $this|array|bool|null
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public static function findByAccount(array $params){
       return self::create()
           ->field('id,username,phone')
           ->where([
            'phone'=>$params['phone'],
            'password'=>Tools::encry($params['password'])
        ])->get();
    }
}