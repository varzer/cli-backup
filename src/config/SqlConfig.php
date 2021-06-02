<?php
namespace eco\config;

class SqlConfig
{
    // 备份sql文件的目录
    const DIR_NAME = 'BACKUP\\SqlFile';
    // 备份定时任务sql文件目录
    const CRONTAB_FILE = 'BACKUP\\Crontab';
    // 生成的SQL文件名
    // 文件名为:数据库名.SQL
    const SQL_FILE_NAME = ServiceConfig::DB_NAME.'.SQL';
    // 每次,读取写入5000行数据
    const READ_COUNT = 5000;
}
