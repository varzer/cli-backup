<?php

namespace eco\cli;

use eco\exec\Exec;

class Cli
{
    // 默认模式
    // 没有参数
    // 输出帮助文档
    // 执行把远程需要备份的数据库结构备份到本地或者远程数据库上
    const CLI_I = '-i';
    // 执行定时任务把远程数据备份到本地或者远程数据库上
    const CLI_C = '-c';
    // 一条龙自动化 -i -c
    const CLI_ALL = '-a';


    public static function exec($argc, $argv)
    {
        if ( $argc === 1 )
        {
            echo "\n";
            Console::log( '                                                        ', 60, 2 );
            Console::log( '                        参数说明                        ', 60, 2 );
            Console::log( '                                                        ', 60, 1 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '      1.开始之前请先配置./config/目录下的配置文件       ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '    2.配置完./config/目录下服务端和客户端配置文件后     ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '      3.请在命令行下执行  php cmd -a 或 -i 或 -c        ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '          -i  把远程服务器数据备份到本地服务器          ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '          -c  执行定时器任务                            ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '          -a  执行-i 和 -c 的功能                       ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '                                                        ', 60, 1 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '      *备注:请确定远程MySql服务器3306端口处于开放状态   ', 60, 2 );
            Console::log( '                                                        ', 60, 2 );
            Console::log( '   *备注:请确定备份数据的MySql服务器3306端口处于开放状态', 60, 2 );
            Console::log( '                                                        ', 60, 1 );
            Console::log( '                                                        ', 60, 2 );
            echo "\n";
        }

        if ( $argc === 2 )
        {
            if ( \strcmp( $argv[1], CLI::CLI_I ) == 0 )
            {
                Exec::execBackupSql();
            }
            if ( \strcmp( $argv[1], CLI::CLI_C ) == 0 )
            {
                Exec::execCrontab();
            }
            if ( \strcmp( $argv[1], CLI::CLI_ALL ) == 0 )
            {
                Exec::execBackupSql();
                Exec::execCrontab();
            }
        }
    }
}
