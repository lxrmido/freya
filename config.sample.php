<?php
/**
 * 全局配置
 */
#############################################
define('TIME_ZONE', 'Asia/Shanghai');
# MySQL服务器、用户名、密码
define('SQL_SVR', 'localhost');    
define('SQL_USR', 'marvin');        
define('SQL_PWD', 'marvin');    
define('SQL_DB', 'freya');    

define('MD5_SALT', 'freya');

# 是否强制所有PHP类都只位于class目录下
define('FORCE_AUTOLOAD', false);
# cache
define('RUNTIME_DIR_CACHE',  'cache/');
# 模板路径
define('RUNTIME_DIR_TPL',  'tpl/');
# 静态文件
define('RUNTIME_DIR_STATIC', 'static/');
# DATA路径
define('RUNTIME_DIR_DATA', 'data/');
define('RUNTIME_DIR_CONTROLLER', 'controllers/');
define('FREYA_SESSION_PREFIX', 'freya_');

# 是否开启REWRITE
define('RUNTIME_REWRITE', false);

define('WEBSITE_URL_ROOT',  'http://l/freya');
define('WEBSITE_URL_DATA', WEBSITE_URL_ROOT . '/' . RUNTIME_DIR_DATA);

define('FREYA_DEBUG', true);
define('DB_DEBUG', true);

define('COMPILE_LESS', true);
define('STATIC_VERSION', '0826');

define('STARTUP_TIMESTAMP', 1425139200);
#############################################

# TPL
define('TPL_DEFAULT_TITLE', 'Freya');
