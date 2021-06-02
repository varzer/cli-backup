<?php
namespace eco\controller;

use eco\db\ServiceSql;
use eco\db\LocalSql;
use eco\config\SqlConfig;
use eco\config\ServiceConfig;
use eco\config\LocalConfig;
use eco\cli\Console;

/**
 * 备份远程sql服务器 数据库数据表结构
 * 并保存在本地sql服务器上
 */

class BackupSqlStruct
{
    // 服务器mysql单例连接
    private static $serviceSql = null;
    // 客户端mysql 单例连接
    private static $clinetSql = null;
    // 服务器数据库中表的数量
    private static $tableLen = 0;
    // 服务器数据库中所有表名
    private static $tableName = [];
    // 服务器数据库中表所对应的记录条数
    private static $tableCount = [];
    // SQl文档的完整路径
    private static $fileName = SqlConfig::DIR_NAME."\\".SqlConfig::SQL_FILE_NAME;


    public function __construct()
    {
        self::$serviceSql = ServiceSql::getInstance();
        self::$tableName = $this->getSqlBackResult("show tables");
        self::$tableLen = count(self::$tableName);
    }

    // 执行1条sql,保存PDOStatement object
    protected function savePdoObject($sql)
    {
        return self::$serviceSql->query($sql);
    }

    // 返回受上一个 SQL 语句影响的行数
    // 获取sql语句返回受影响的行数
    protected function getSqlBackRowCount($sql)
    {
        $result = $this->savePdoObject($sql);

        return $result->rowCount();
    }

    // 获取sql返回的结果集
    protected function getSqlBackResult($sql)
    {
        $result = $this->savePdoObject($sql);

        $data = [];

        while($row = $result->fetch(\PDO::FETCH_NUM)){
            $data[] = $row[0];
        }

        return $data;
    }

    // 获取表的sql语句
    protected function getTableSql($sql)
    {
        $result = $this->savePdoObject($sql);

        return $result->fetchAll(\PDO::FETCH_NUM)[0][1];
    }

    // 生成SQL文件
    public function createSqlFile()
    {

        // 1.自检如果自检成功
        // 则存在该SQL文档
        // 不生执行生成SQL文档
        if ( !$this->checkSqlFile() )
        {
            // 2.不存在则执行生成SQL文档
            $talSql = [];
            $i = 0;

            // 生成创建数据表语句
            for (;$i<self::$tableLen;$i++)
            {
                $sql = 'show create table '.ServiceConfig::DB_NAME.'.'.self::$tableName[$i];
                $tabSql[$i] = $this->getTableSql($sql).";\n\n";
                echo self::$tableName[$i]." 表结构SQL语句生成中...\n";
            }

            // 如果该文件目录不存在
            // 生成文件目录
            $this->createFiled(SqlConfig::DIR_NAME);

            // 临时需要$i和++$i的下标
            $j = $i;

            // 创建定时任务执行表
            $tabSql[$j] = "create table if not exists `crontab`(\n";
            $tabSql[$j] .= "`id` int unsigned auto_increment key ,\n";
            $tabSql[$j] .= "`sql` varchar(255) unique not null comment '要执行的sql语句',\n";
            $tabSql[$j] .= "`table_name` varchar(50) not null comment '要存入的表',\n";
            $tabSql[$j] .= "`status` tinyint default -1 comment '-1未执行的sql语句,1已执行语句'\n";
            $tabSql[$j] .= ")engine = MyISAM character set 'utf8' comment '定时任务表';\n\n";


            // 自定义验证检测标志位数据
            $tabSql[++$j] = "--".$i;

            try {
                \file_put_contents(self::$fileName,$tabSql);
            } catch (\Exception $e) {
                $errmsg = self::$fileName . "生成失败!\n";
                throw new \Exception(Console::log($errmsg,1,2));
            }

            $text = self::$fileName . " 生成完毕!\n";
            Console::log($text);
        }else{
            $text = self::$fileName ." 存在!\n";
            Console::log($text);
        }

    }

    // 生成文件夹
    protected function createFiled($file)
    {
        if (!\file_exists($file))
        {
            mkdir($file,0755,true);
        }
    }

    // 自检sql文件是否生成完整
    // 如果不完整则重新生成
    protected function checkSqlFile()
    {
        // 1.检测SQL文档是否存在
        $checkMark = null;
        if(!\is_file( self::$fileName ))
        {
            return false;
        }

        // 2.读取SQL文档最后一行
        // 的最后3个字节检测标志位
        try {
            $fpHandle = fopen(self::$fileName,'r');
            $res = fseek($fpHandle,-3,SEEK_END);
            if ( $res === -1 )
            {
                return false;
            }
            $checkMark = (int)\fread($fpHandle,3);
            \fclose($fpHandle);
        } catch (\Exception $e) {
            return false;
        }

        // 3.对比检测标志和数据表的数量是否相等
        if ( $checkMark === self::$tableLen )
        {
            return true;
        }

        return false;
    }

    // 把生成的sql文件在本地执行生成同样的库和表
    public function createServiceSql()
    {
        // 1.自检
        /*
        if (!$this->checkSqlFile())
        {
            $errmsg = "自检完成!\n";
            $errmsg .= "请先调用BackupSqlStruct::createSqlFile()方法\n";
            $errmsg .= "生成服务端SQL结构\n";
            throw new \Exception($errmsg);
        }
        */
       // 第一次执行的时候,在生成中sql中已经自检过了
       // 所以这里不用自检了
       // 如果单独用这个方法,就把自检打开
        // 2.创建数据库
        $this->createDataBase();
        // 3.导入数据库表结构
        $this->importSqlTable();
        // 把sql文档改名
        Console::log('数据库数据表创建完毕!');

    }

    // 创建和服务器同名的数据库
    protected function createDataBase()
    {
        // 生成创建数据库语句
        $sql = "create database if not exists `";
        $sql .= ServiceConfig::DB_NAME;
        $sql .= "` default character set 'utf8'";

        try {
            $pdo = new \PDO(LocalConfig::DSN_CREATE,LocalConfig::USER,LocalConfig::PASS);
            $result = $pdo->exec($sql);
            unset($pdo);
        } catch (\Exception $e) {
            throw new \Exception(Console::log("数据库创建失败!",1,2));
        }
    }

    // 导入数据库表结构
    protected function importSqlTable()
    {
        $sql = \file_get_contents(self::$fileName);
        self::$clinetSql = LocalSql::getInstance();
        self::$clinetSql->exec($sql);
    }

    // 生成服务器数据库所有表的sql查询定时语句
    public function createCrontabSql()
    {
        // 方法一:
        // 1.生成服务器数据库所有表的sql查询定时语句
        // 2.检测生成sql文件的标志位
        // 3.把sql文件的sql语句写入定时任务数据表中
        // 4.删除sql文件
        // 5.开启执行定时任务

        // 方法二:
        // 1.生成服务器数据库所有表的sql查询定时语句
        // 2.把生成的sql语句写入定时任务表
        // 3.检测定时任务表中语句与服务器总条数语句是否一致
        // 4.开启定时任务

        for ($i=0;$i<self::$tableLen;$i++)
        {

            // 方法二:
            // 1.生成服务器数据库所有表的sql查询定时语句
            // 2.把生成的sql语句写入定时任务表
            $this->createSqlSelectFile($i);
        }

        Console::log('写入完毕!',1);
    }

    // 获取服务器当前数据表的总行数
    protected function getServiceTableCount($i)
    {
        $sql = 'select count(*) from '.self::$tableName[$i];
        $len = (int)$this->getSqlBackResult($sql)[0];
        // self::$tableCount[self::$tableName[$i]] = $len;
        return $len;
    }

    // 生成SQL查询语句文件
    protected function createSqlSelectFile($i=0)
    {
        //当前页
        $page = 0;

        $len = $this->getServiceTableCount($i);

        // 如果数据表中无数据
        // 则不生成sql文档
        if (!$len)
        {
            return false;
        }

        // 总页数
        $countPage = (int)ceil($len / SqlConfig::READ_COUNT);

        // 写入数据库定时任务数据库
        $insertSql = "insert `crontab` (`sql`,`table_name`) values";

        for (;$page<$countPage;$page++){
            // 方法二:
            $readSql = 'select * from `';
            $readSql .= self::$tableName[$i];
            $readSql .= "` LIMIT ";
            $readSql .= $page*SqlConfig::READ_COUNT;
            $readSql .= ',';
            $readSql .= SqlConfig::READ_COUNT;

            // 测试语句加\n方便调试
            // $insertSql .= "('".$readSql."','".self::$tableName[$i]."'),\n";
            $insertSql .= "('".$readSql."','".self::$tableName[$i]."'),";
        }

        $insertSql = rtrim($insertSql,',');

        // 写入定时任务表中
        self::$clinetSql->exec($insertSql);
        echo self::$tableName[$i]." SQL语句写入中...\n";

    }

    public function execBackup()
    {
        // 请获取服务器上的SQL
        // 并且在本地生成SQL文件
        $this->createSqlFile();

        // 获取生成的sql文件
        // 并且创建该环境
        $this->createServiceSql();

        // 获取服务器数据表行数
        $this->createCrontabSql();
    }

}
