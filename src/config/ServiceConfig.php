<?php

namespace eco\config;

class ServiceConfig
{
    // 服务器参数配置

    // mysql用户名
    const USER = 'root_setme';
    // mysql密码
    const PASS = '32226660';
    // mysql主机地址
    const HOST = '192.168.31.100';
    // 数据库名
    const DB_NAME = 'thtest';
    const DSN     = 'mysql:host=' . ServiceConfig::HOST . ';dbname=' . ServiceConfig::DB_NAME;
    const PARAMS  = [
        \PDO::ATTR_PERSISTENT   => true,//持久连接
        \PDO::ATTR_ORACLE_NULLS => true,//在获取数据时将空字符串转换成 SQL 中的 NULL
        //\PDO::CASE_LOWER => true,//强制列名小写
    ];
}
