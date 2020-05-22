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

class FilterZerofill implements FilterInterface
{
    public static function run(Column $column)
    {
        call_user_func(['EasySwoole\\DDL\\Filter\\Zerofill\\Filter' . ucfirst($column->getColumnType()), 'run'], $column);
    }
}