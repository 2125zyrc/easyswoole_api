<?php
/**
 * Created by PhpStorm.
 * User: xcg
 * Date: 2019/10/17
 * Time: 10:03
 */

namespace EasySwoole\DDL\Filter;


use EasySwoole\DDL\Blueprint\Column;
use EasySwoole\DDL\Contracts\FilterInterface;

class FilterUnsigned implements FilterInterface
{
    public static function run(Column $column)
    {
        call_user_func(['EasySwoole\\DDL\\Filter\\Unsigned\\Filter' . ucfirst($column->getColumnType()), 'run'], $column);
    }
}