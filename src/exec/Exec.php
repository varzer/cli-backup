<?php

namespace eco\exec;

use eco\controller\BackupSqlStruct;
use eco\controller\Crontab;
use eco\cli\Console;


class Exec
{
    //T_POWER_STORE_
    //T_TEMPERATURE_STORE_

    public static function execBackupSql()
    {

        try
        {

            $backup = new BackupSqlStruct();
            $backup->execBackup();

        }
        catch ( \Exception $e )
        {

            echo $e->getMessage();

        }

    }

    public static function execCrontab()
    {

        $crontab = new Crontab();
        while ( $res = $crontab->execCrontab() )
        {
            // 延时1秒再执行一次
            echo '正在执行定时任务的第:', $res['id'], "\n";
            echo '当前备份的数据表是:', $res['table_name'], "\n";
            echo '当前备份数据条:', $res['row'], "\n";
            Console::log( '执行中...', 60, 2 );
            // 延时 500毫秒
            usleep( 500000 );
        }

        echo '定时任务全部执行完毕!', "\n";
    }
}
