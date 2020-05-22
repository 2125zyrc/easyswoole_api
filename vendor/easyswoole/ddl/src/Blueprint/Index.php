<?php

namespace EasySwoole\DDL\Blueprint;

use \EasySwoole\DDL\Enum\Index as IndexType;
use InvalidArgumentException;

/**
 * 索引构造器
 * Class Index
 * @package EasySwoole\DDL\Blueprint
 */
class Index
{
    protected $indexName;     // 索引名称
    protected $indexType;     // 索引类型 NORMAL PRI UNI FULLTEXT
    protected $indexColumns;  // 被索引的列 字符串或数组(多个列)
    protected $indexComment;  // 索引注释

    /**
     * Index constructor.
     * @param string|null $indexName 不设置索引名可以传入NULL
     * @param string $indexType 传入类型常量
     * @param string|array $indexColumns 传入索引字段
     */
    function __construct(?string $indexName, $indexType, $indexColumns)
    {
        $this->setIndexName($indexName);
        $this->setIndexType($indexType);
        $this->setIndexColumns($indexColumns);
    }

    /**
     * 设置索引名称
     * @param string $name
     * @return Index
     */
    function setIndexName(?string $name = null): Index
    {
        $name = is_string($name) ? trim($name) : null;
        $this->indexName = $name;
        return $this;
    }

    /**
     * 设置索引类型
     * @param string $type
     * @return Index
     */
    function setIndexType(string $type): Index
    {
        $type = trim($type);
        if (!IndexType::isValidValue($type)) {
            throw new InvalidArgumentException('The index type ' . $type . ' is invalid');
        }
        $this->indexType = $type;
        return $this;
    }

    /**
     * 设置索引字段
     * @param string|array $columns 可以设置字符串和数组
     * @return Index
     */
    function setIndexColumns($columns): Index
    {
        $this->indexColumns = $columns;
        return $this;
    }

    /**
     * 设置索引备注
     * @param string $comment
     * @return Index
     */
    function setIndexComment(string $comment): Index
    {
        $this->indexComment = $comment;
        return $this;
    }

    /**
     * 组装索引字段名
     * @return string
     */
    function parseIndexColumns()
    {
        $columnDDLs = [];
        $indexColumns = $this->indexColumns;
        if (is_string($indexColumns)) {
            $indexColumns = array($indexColumns);
        }
        foreach ($indexColumns as $indexedColumn) {
            $columnDDLs[] = '`' . $indexedColumn . '`';
        }
        return '(' . implode(',', $columnDDLs) . ')';
    }

    /**
     * 生成索引DDL结构
     * 带有下划线的方法请不要自行调用
     * @return string
     */
    function __createDDL()
    {
        $indexPrefix = [
            IndexType::NORMAL   => 'INDEX',
            IndexType::UNIQUE   => 'UNIQUE INDEX',
            IndexType::PRIMARY  => 'PRIMARY KEY',
            IndexType::FULLTEXT => 'FULLTEXT INDEX',
        ];
        return implode(' ',
            array_filter(
                [
                    $indexPrefix[$this->indexType],
                    $this->indexName !== null ? '`' . $this->indexName . '`' : null,
                    $this->parseIndexColumns(),
                    $this->indexComment ? "COMMENT '" . addslashes($this->indexComment) . "'" : null
                ]
            )
        );
    }

    /**
     * 转化为字符串
     * @return string
     */
    function __toString()
    {
        return $this->__createDDL();
    }
}