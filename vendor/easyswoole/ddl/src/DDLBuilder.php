<?php

namespace EasySwoole\DDL;

use EasySwoole\DDL\Blueprint\Table;

/**
 * 语句生成器
 * Class DDLBuilder
 * @package EasySwoole\DDL
 */
class DDLBuilder
{
    /**
     * 生成建表语句
     * @param string $table 表名称
     * @param callable $callable 在闭包中描述创建过程
     * @return string 返回生成的DDL语句
     */
    static function table($table, callable $callable)
    {
        $blueprint = new Table($table);
        $callable($blueprint);
        return $blueprint->__createDDL();
    }
}