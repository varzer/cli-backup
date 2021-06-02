<?php
namespace eco\db;

use eco\config\LocalConfig;
use eco\cli\Console;

class LocalSql
{
    protected static $instance = null;

    private function __construct(){}
    private function __clone(){}

    public static function getInstance()
    {
        if (self::$instance)
        {
            return self::$instance;
        }

        try {
            self::$instance = new \PDO(LocalConfig::DSN,LocalConfig::USER,
                        LocalConfig::PASS,LocalConfig::PARAMS);
        } catch (\Exception $e) {
            throw new \Exception(Console::log("数据库连接失败!\n\n请检查本地配置文件!\n",1,2));
        }

        return self::$instance;
    }

}
