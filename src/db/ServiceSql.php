<?php
namespace eco\db;

use eco\config\ServiceConfig;
use eco\cli\Console;

class ServiceSql
{
    private static $instance = null;

    private function __construct(){}
    private function __clone(){}

    public static function getInstance()
    {
        if (self::$instance)
        {
            return self::$instance;
        }

        try{
            self::$instance = new \PDO(ServiceConfig::DSN,ServiceConfig::USER,
                        ServiceConfig::PASS,ServiceConfig::PARAMS);
        }catch(\Exception $e){
            throw new \Exception(Console::log("数据库连接失败!\n\n请检查服务端配置文件!",1,2));
        }

        return self::$instance;
    }

}
