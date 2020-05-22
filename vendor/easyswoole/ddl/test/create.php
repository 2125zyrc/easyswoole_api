<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2019/9/4
 * Time: 23:46
 */

namespace EasySwoole\DDL\Test;

require_once 'vendor/autoload.php';

use EasySwoole\DDL\Blueprint\Table;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

$stuQql = DDLBuilder::table('student', function (Table $table) {
    $table->setIfNotExists()->setTableComment('学生表');   //设置表名称/
    $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);//设置表字符集
    $table->colInt('stu_id')->setIsAutoIncrement()->setIsPrimaryKey()->setIsUnsigned()->setColumnComment('学生ID');  //创建stu_id设置主键并自动增长
    $table->colVarChar('stu_name', 30)->setColumnComment('学生姓名');
    $table->colChar('sex', 1)->setColumnComment('性别：1男，2女')->setDefaultValue(1);
    $table->colDate('birthday')->setIsNotNull(false)->setColumnComment('出生日期');
    $table->colInt('created_at', 10)->setColumnComment('创建时间');
    $table->colInt('updated_at', 10)->setColumnComment('更新时间');
    $table->indexNormal('stu_name_index', 'stu_name')->setIndexComment('学生姓名--普通索引');//设置索引
});
echo $stuQql . "\r\n";

$courseSql = DDLBuilder::table('course', function (Table $table) {
    $table->setIfNotExists()->setTableComment('课程表');   //设置表名称/
    $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);//设置表字符集
    $table->colInt('id', 3)->setIsPrimaryKey()->setIsAutoIncrement()->setIsUnsigned()->setZeroFill()->setColumnComment('课程id');
    $table->colVarChar('course_name', 100)->setColumnComment('课程名称');
    $table->colChar('status', 1)->setDefaultValue(1)->setColumnComment('课程状态：1正常，0隐藏');
    $table->colInt('created_at', 10)->setColumnComment('创建时间');
    $table->colInt('updated_at', 10)->setColumnComment('更新时间');
    $table->indexUnique('course_name_index', 'course_name')->setIndexComment('课程名称--唯一索引');//设置索引
});
echo $courseSql . "\r\n";

$scoreSql = DDLBuilder::table('score', function (Table $table) {
    $table->setIfNotExists()->setTableComment('成绩表');   //设置表名称/
    $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);//设置表字符集
    $table->colInt('id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('自增ID');
    $table->colInt('stu_id')->setIsUnsigned()->setColumnComment('学生id');
    $table->colInt('course_id')->setIsUnsigned()->setZeroFill()->setColumnComment('课程id');
    $table->colFloat('score', 3, 1)->setColumnComment('成绩');
    $table->colInt('created_at', 10)->setColumnComment('创建时间');
});
echo $scoreSql;
//以下是输出sql语句
/*
CREATE TABLE IF NOT EXISTS `student` (
  `stu_id` int UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '学生ID',
  `stu_name` varchar(30) NOT NULL COMMENT '学生姓名',
  `sex` char(1) NOT NULL DEFAULT 1 COMMENT '性别：1男，2女',
  `birthday` date NOT NULL COMMENT '出生日期',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  INDEX `stu_name_index` (`stu_name`) COMMENT '学生姓名--普通索引'
)
ENGINE = INNODB DEFAULT COLLATE = 'utf8mb4_general_ci' COMMENT = '学生表';

CREATE TABLE IF NOT EXISTS `course` (
  `id` int(3) UNSIGNED ZEROFILL NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '课程id',
  `course_name` varchar(100) NOT NULL COMMENT '课程名称',
  `status` char(1) NOT NULL DEFAULT 1 COMMENT '课程状态：1正常，0隐藏',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  UNIQUE INDEX `course_name_index` (`course_name`) COMMENT '课程名称--唯一索引'
)
ENGINE = INNODB DEFAULT COLLATE = 'utf8mb4_general_ci' COMMENT = '课程表';

CREATE TABLE IF NOT EXISTS `score` (
  `id` int UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '自增ID',
  `stu_id` int UNSIGNED NOT NULL COMMENT '学生id',
  `course_id` int UNSIGNED ZEROFILL NOT NULL COMMENT '课程id',
  `score` float(3,1) NOT NULL COMMENT '成绩',
  `created_at` int(10) NOT NULL COMMENT '创建时间'
)
ENGINE = INNODB DEFAULT COLLATE = 'utf8mb4_general_ci' COMMENT = '成绩表';

*/
