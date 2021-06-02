#一.
    PS : 把需配备的数据[#1.1]备份到[#1.2]

    #1.1 配置./config/ServiceConfig.php
		说明#远程mysql必须开放3306端口,数据库只读权限
        服务端 
        配置项
        [
            // mysql用户名
            const USER = 'readUser';
            // mysql密码
            const PASS = 'PWD';
            // mysql主机地址
            const HOST = '192.168.1.222';
            // 数据库名
            const DB_NAME = 'econew';
        ]

    #1.2 配置./config/LocalConfig.php
        配置项:
        客户端
        [
            // mysql用户名
            const USER = 'root';
            // mysql密码
            const PASS = 'root';
            // mysql主机地址
            const HOST = 'localhost';
        ]

    #1.3 [可选配置]./config/SqlConfig.php


#二.
    #2.1 在当前目录下执行 php cmd  
    #2.2 执行 php -i 把[#1.1]的sql结构备份到[#1.2],并生成环境并接着执行2.3  
    #2.3 执行 php -c 执行定时器,开始备份数据  

	 #-----------------------------------------------------------
	#|                          参数说明                   
	#|                                                    
	#|                                                    
	#|        1.开始之前请先配置./config/目录下的配置文件       
	#|                                                   
	#|      2.配置完./config/目录下服务端和客户端配置文件后  
	#|  
	#|  
	#|             3.请在命令行下执行  php cmd -i 或 -c  
	#|  
	#|                -i  把远程服务器数据备份到本地服务器  
	#|  
	#|                -c  执行定时器任务
	#| 
	#|		先执行 php cmd -i 再执行  php cmd -c
	#|   
	#|  
	#|        *备注:请确定远程MySql服务器3306端口处于开放状态  
	#|  
	#|     *备注:请确定备份数据的MySql服务器3306端口处于开放状态  
	#|  
	 #-------------------------------------------------------------
