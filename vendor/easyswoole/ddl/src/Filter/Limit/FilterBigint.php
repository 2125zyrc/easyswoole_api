<?php
/**
 * Created by PhpStorm.
 * User: xcg
 * Date: 2019/10/16
 * Time: 14:44
 */

namespace EasySwoole\DDL\Filter\Limit;


use EasySwoole\DDL\Blueprint\Column;
use EasySwoole\DDL\Contracts\FilterInterface;

class FilterBigint implements FilterInterface
{

    public static function run(Column $column)
    {
        if ($column->getColumnLimit() < 0 || $column->getColumnLimit() > 255) {
            throw new \InvalidArgumentException('col ' . $column->getColumnName() . ' type bigint(limit), limit must be range 1 to 255');
        }
    }

    public static function limit(Column $column)
    {
        if ($column->getColumnLimit() < 0 || $column->getColumnLimit() > 255) {
            throw new \InvalidArgumentException('col ' . $column->getColumnName() . ' type bigint(limit), limit must be range 1 to 255');
        }
    }

    public static function unsigned(Column $column)
    {

    }

    public static function zerofill(Column $column)
    {

    }
}
