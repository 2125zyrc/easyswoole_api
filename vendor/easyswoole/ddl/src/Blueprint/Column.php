<?php

namespace EasySwoole\DDL\Blueprint;

use EasySwoole\DDL\Enum\DataType;
use EasySwoole\DDL\Filter\FilterLimit;
use EasySwoole\DDL\Filter\FilterUnsigned;
use EasySwoole\DDL\Filter\FilterZerofill;
use InvalidArgumentException;

/**
 * 字段构造器
 * Class Column
 * @package EasySwoole\DDL\Blueprint
 */
class Column
{

    protected $columnName;     // 字段名称
    protected $columnType;     // 字段类型
    protected $columnLimit;    // 字段限制 如 INT(11) / decimal(10,2) 括号部分
    protected $columnComment;  // 字段注释
    protected $columnCharset;  // 字段编码
    protected $isBinary;         // 二进制 (只允许在字符串类型上设置)
    protected $isUnique;         // 唯一的 (请勿重复设置索引UNI)
    protected $unsigned;         // 无符号 (仅数字类型可以设置)
    protected $zeroFill;         // 零填充 (仅数字类型可以设置)
    protected $defaultValue;     // 默认值 (非TEXT/BLOB可以设置)
    protected $isNotNull = true; // 字段可空 (默认已经设置全部字段不可空)
    protected $autoIncrement;    // 字段自增 (仅数字类型可以设置)
    protected $isPrimaryKey;     // 主键字段 (请勿重复设置索引PK)

    /**
     * Column constructor.
     * @param string $columnName 列的名称
     * @param string $columnType 列的类型
     */
    function __construct(string $columnName, string $columnType)
    {
        $this->setColumnName($columnName);
        $this->setColumnType($columnType);
    }

    /**
     * 设置字段名称
     * @param string $name 字段名称
     * @return Column
     */
    function setColumnName(string $name): Column
    {
        $name = trim($name);
        if (empty($name)) {
            throw new InvalidArgumentException('The column name cannot be empty');
        }
        $this->columnName = $name;
        return $this;
    }

    /**
     * 设置字段类型
     * @param string $type
     * @return Column
     */
    function setColumnType(string $type): Column
    {
        $type = trim($type);
        if (!DataType::isValidValue($type)) {
            throw new InvalidArgumentException('The column type ' . $type . ' is invalid');
        }
        $this->columnType = $type;
        return $this;
    }

    /**
     * 设置字段列宽限制
     * @param integer|array $limit
     * @return Column
     */
    function setColumnLimit($limit): Column
    {
        // TODO 暂未做规范判断
        // 此处根据类型的不同实际上还应该判断 TEXT/BLOB 不可能存在limit
        // 另外数字类型如 INT DisplayWidth < 256 | DECIMAL (1,5) 总精度必须大于小数部分精度等限制
        $this->columnLimit = $limit;
        return $this;
    }

    /**
     * 设置字段备注
     * @param string $comment
     * @return Column
     */
    function setColumnComment(string $comment): Column
    {
        $this->columnComment = $comment;
        return $this;
    }

    /**
     * 设置字段编码
     * @param string $charset
     * @return Column
     */
    function setColumnCharset(string $charset): Column
    {
        $this->columnCharset = $charset;
        return $this;
    }

    /**
     * 是否零填充
     * @param bool $enable
     * @return Column
     */
    function setZeroFill(bool $enable = true): Column
    {
        $this->zeroFill = $enable;
        return $this;
    }

    /**
     * 是否无符号
     * @param bool $enable
     * @return Column
     */
    function setIsUnsigned(bool $enable = true): Column
    {
        // TODO 暂未做规范判断
        // 同样需要做规范判断 字段为文本/日期时间/BLOB时不能设置为无符号
        $this->unsigned = $enable;
        return $this;
    }

    /**
     * 字段默认值
     * @param $value
     * @return Column
     */
    function setDefaultValue($value): Column
    {
        // TODO 暂未做规范判断
        // 同样需要做规范判断 字段为文本/BLOB时不能设置默认值
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * 设置不可空
     * @param bool $enable
     * @return Column
     */
    function setIsNotNull(bool $enable = true): Column
    {
        $this->isNotNull = $enable;
        return $this;
    }

    /**
     * 是否值自增
     * @param bool $enable
     * @return Column
     */
    function setIsAutoIncrement(bool $enable = true): Column
    {
        // TODO 暂未做规范判断
        // 同样需要做规范判断 只有数字类型才允许自增
        $this->autoIncrement = $enable;
        return $this;
    }

    /**
     * 是否二进制
     * 在字符列上设置了二进制会使得该列严格区分大小写
     * @param bool $enable
     * @return Column
     */
    function setIsBinary(bool $enable = true): Column
    {
        // TODO 暂未做规范判断
        // 同样需要做规范判断 只有字符串类型才允许二进制
        $this->isBinary = $enable;
        return $this;
    }

    /**
     * 直接在字段上设置PK
     * 请不要和索引互相重复设置
     * @param bool $enable
     * @return Column
     */
    function setIsPrimaryKey(bool $enable = true): Column
    {
        $this->isPrimaryKey = $enable;
        return $this;
    }

    /**
     * 字段设置为Unique
     * 请不要和索引互相重复设置
     * @param bool $enable
     * @return Column
     */
    function setIsUnique(bool $enable = true): Column
    {
        $this->isUnique = $enable;
        return $this;
    }

    /**
     * 处理字段的默认值
     * @return bool|string
     */
    private function parseDefaultValue()
    {
        // AUTO_INCREMENT 和默认值不能同时出现
        if ($this->autoIncrement) {
            return false;
        }
        // 如果当前允许NULL值 而没有设置默认值 那么默认就为NULL
        if (!$this->isNotNull && ($this->defaultValue == null || $this->defaultValue == 'NULL')) {
            return 'NULL';
        }

        // 否则字段是不允许NULL值的 如果默认值是文本应该加入引号
        if (is_string($this->defaultValue)) {
            return "'" . $this->defaultValue . "'";
        } else if (is_bool($this->defaultValue)) {  // 布尔类型强转0和1
            return $this->defaultValue ? '1' : '0';
        } else if (is_null($this->defaultValue)) {
            return false;
        } else {  // 其他类型强转String
            return (string)$this->defaultValue;
        }
    }

    /**
     * 处理字段的类型宽度限制
     * @return bool|string
     */
    private function parseColumnLimit()
    {
        // 是一个数组需要用逗号接起来
        if (is_array($this->columnLimit)) {
            return "(" . implode(',', $this->columnLimit) . ")";
        }
        // 是一个数字可以直接设置在类型后面
        if (is_numeric($this->columnLimit)) {
            return "(" . $this->columnLimit . ")";
        }
        // 否则没有设置
        return false;
    }

    /**
     * 处理数据类型
     * @return string
     */
    private function parseDataType()
    {
        $columnLimit = $this->parseColumnLimit();
        $columnType = $this->columnType;
        if ($columnLimit) {
            $columnType .= $columnLimit;
        }
        return $columnType;
    }


    /**
     * 创建DDL
     * 带下划线的方法请不要外部调用
     * @return string
     */
    function __createDDL(): string
    {
        FilterLimit::run($this);//检测limit是否合法
        FilterUnsigned::run($this); //检测无符号类型
        FilterZerofill::run($this); //检测是否补充长度
        $default = $this->parseDefaultValue();
        $columnCharset = $this->columnCharset ? explode('_', $this->columnCharset)[0] : false;
        return implode(' ',
            array_filter(
                [
                    '`' . $this->columnName . '`',
                    (string)$this->parseDataType(),
                    $this->isBinary ? 'BINARY' : null,
                    $this->columnCharset ? 'CHARACTER SET ' . strtoupper($columnCharset) . ' COLLATE ' . strtoupper($this->columnCharset) : null,
                    $this->unsigned ? 'UNSIGNED' : null,
                    $this->zeroFill ? 'ZEROFILL' : null,
                    $this->isUnique ? 'UNIQUE' : null,
                    $this->isNotNull ? 'NOT NULL' : 'NULL',
                    $default !== false ? 'DEFAULT ' . $default : null,
                    $this->isPrimaryKey ? 'PRIMARY KEY' : null,
                    $this->autoIncrement ? 'AUTO_INCREMENT' : null,
                    $this->columnComment ? sprintf("COMMENT '%s'", addslashes($this->columnComment)) : null
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

    /**
     * @return mixed
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @return mixed
     */
    public function getColumnType()
    {
        return $this->columnType;
    }

    /**
     * @return mixed
     */
    public function getColumnLimit()
    {
        return $this->columnLimit;
    }

    /**
     * @return mixed
     */
    public function getColumnComment()
    {
        return $this->columnComment;
    }

    /**
     * @return mixed
     */
    public function getColumnCharset()
    {
        return $this->columnCharset;
    }

    /**
     * @return mixed
     */
    public function getIsBinary()
    {
        return $this->isBinary;
    }

    /**
     * @return mixed
     */
    public function getIsUnique()
    {
        return $this->isUnique;
    }

    /**
     * @return mixed
     */
    public function getUnsigned()
    {
        return $this->unsigned;
    }

    /**
     * @return mixed
     */
    public function getZeroFill()
    {
        return $this->zeroFill;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function getIsNotNull(): bool
    {
        return $this->isNotNull;
    }

    /**
     * @return mixed
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @return mixed
     */
    public function getIsPrimaryKey()
    {
        return $this->isPrimaryKey;
    }
}
