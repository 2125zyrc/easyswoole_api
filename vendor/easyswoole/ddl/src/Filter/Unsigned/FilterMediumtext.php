<?php
/**
 * Created by PhpStorm.
 * User: xcg
 * Date: 2019/10/16
 * Time: 16:57
 */

namespace EasySwoole\DDL\Filter\Unsigned;


use EasySwoole\DDL\Blueprint\Column;
use EasySwoole\DDL\Contracts\FilterInterface;

class FilterMediumtext implements FilterInterface
{
    public static function run(Column $column)
    {
        if ($column->getUnsigned()) {
            throw new \InvalidArgumentException('col ' . $column->getColumnName() . ' type mediumtext no require unsigned ');
        }
    }

}