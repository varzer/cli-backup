<?php
namespace eco\controller;

use eco\db\LocalSql;
use eco\db\ServiceSql;

class Crontab
{
    // 服务器mysql单例连接
    private static $serviceSql = null;
    // 客户端mysql 单例连接
    private static $clinetSql = null;


    public function __construct()
    {
        self::$serviceSql = ServiceSql::getInstance();
        self::$clinetSql = LocalSql::getInstance();
    }

    // 服务端的query对象
    protected function serviceQuery($sql)
    {
        return self::$serviceSql->query($sql);
    }

    // 客户端的query对象
    protected function clientQuery($sql)
    {
        return self::$clinetSql->query($sql);
    }


    // 读取本地crontab任务
    protected function readLocalCrontabSql()
    {
        $crontabSql = 'select * from `crontab` where `status` = -1 limit 1';
        $result = $this->clientQuery($crontabSql);
        $data = $result->fetch(\PDO::FETCH_ASSOC);
        unset($data['status']);
        return $data;
    }

    // 读取服务器数据
    // 读取定时任务中需要查询的服务器数据
    // 生成可执行的sql语句
    protected function readServiceData($res)
    {
        $result = $this->serviceQuery($res['sql']);
        $insertSql = "insert into `{$res['table_name']}` values";

        while ($data = $result->fetch(\PDO::FETCH_NUM))
        {
            $len = count($data);

            $insertSql .= "(";
            for ($i=0;$i<$len;$i++)
            {
                if ($i==($len-1))
                {
                    $insertSql .= "'".$data[$i]."'";
                }else {
                    $insertSql .= "'".$data[$i]."',";
                }
            }
            $insertSql .="),";
        }

        $insertSql = \rtrim($insertSql,',');

        return [
                'sql'=>$insertSql,
                'id'=>(int)$res['id'],
            ];
    }

    // 在客户端上执行改sql
    protected function execLocalSql($data)
    {
        $rowCount = self::$clinetSql->exec($data['sql']);

        // 写入成功,更新定时器
        if ($rowCount)
        {
            $sql = "update `crontab` set `status` = 1 where `id` = {$data['id']}";
            return self::$clinetSql->exec($sql)?$rowCount:false;
        }
        return false;
    }

    // 执行crontab
    public function execCrontab()
    {
        // 读取要执行的定时任务
        $crontabData = $this->readLocalCrontabSql();

        // 如果还有定时器任务
        if ($crontabData)
        {
            // 读取定时任务中需要查询的服务器数据
            $serviceData = $this->readServiceData($crontabData);

            // 把服务上的数据写入到本地服务器
            $res = $this->execLocalSql($serviceData);

            if ($res)
            {
                return [
                    'id'=>$crontabData['id'],
                    'table_name'=>$crontabData['table_name'],
                    'row'=>$res
                ];
            }
        }

        return false;
    }

}
