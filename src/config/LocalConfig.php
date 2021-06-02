<?php
namespace eco\config;

class LocalConfig
{
    // mysql用户名
    const USER = 'root';
    // mysql密码
    const PASS = 'root';
    // mysql主机地址
    const HOST = '127.0.0.1';
    // 数据库名
    // 同服务器上的数据库同名
    const DB_NAME = ServiceConfig::DB_NAME;
    const DSN = 'mysql:host='.LocalConfig::HOST.';dbname='.LocalConfig::DB_NAME;
    const DSN_CREATE = 'mysql:host='.LocalConfig::HOST;
    const PARAMS = [
        \PDO::ATTR_PERSISTENT   => true,//持久连接
        \PDO::ATTR_ORACLE_NULLS =>true,//在获取数据时将空字符串转换成 SQL 中的 NULL
        //\PDO::CASE_LOWER => true,//强制列名小写
    ];
}
