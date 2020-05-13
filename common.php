<?php
date_default_timezone_set('Asia/Kolkata');

define('PROJECT_ID', 'Vibgyor');  //TODO:must change
define('PROJECT_NAME', 'Vibgyor'); //TODO:must change

//defined('YII_ENV') or define('YII_ENV', 'prod');
defined('YII_ENV') or define('YII_ENV', 'dev');

if (YII_ENV == 'dev') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    // remove the following lines when in production mode
    defined('YII_DEBUG') or define('YII_DEBUG', true);
}

defined('DATECHECK') or define('DATECHECK', "2019-12-31");
defined('VERSION') or define('VERSION', "1.0");
defined('FILE_MODE') or define('FILE_MODE', 0755);

defined('UPLOAD_PATH') or define('UPLOAD_PATH', dirname(__FILE__) . '/protected/uploads/');
defined('MODULE_PATH') or define('MODULE_PATH', dirname(__FILE__) . '/protected/modules/');
defined('UPLOAD_THUMB_PATH') or define('UPLOAD_THUMB_PATH', dirname(__FILE__) . '/protected/uploads/thumb/');

defined('TMP_PATH') or define('TMP_PATH', sys_get_temp_dir());
defined('BASE_PATH') or define('BASE_PATH', dirname(__FILE__) . '/protected');
defined('PROTECTED_PATH') or define('PROTECTED_PATH', dirname(__FILE__) . '/protected');
defined('VENDOR_PATH') or define('VENDOR_PATH', __DIR__ . '/vendor/');
define('RUNTIME_PATH', BASE_PATH . '/runtime');

// db config path setting
defined('DB_CONFIG_PATH') or define('DB_CONFIG_PATH', dirname(__FILE__) . '/protected/config/');
defined('DB_CONFIG_FILE_PATH') or define('DB_CONFIG_FILE_PATH', DB_CONFIG_PATH . 'db-' . YII_ENV . '.php');
defined('DB_BACKUP_FILE_PATH') or define('DB_BACKUP_FILE_PATH', dirname(__FILE__) . '/protected/db/');

// create directories if required
if (! is_dir(UPLOAD_PATH))
    @mkdir(UPLOAD_PATH, FILE_MODE, true);
if (! is_dir(UPLOAD_THUMB_PATH))
    @mkdir(UPLOAD_THUMB_PATH, FILE_MODE, true);
if (! is_dir(DB_CONFIG_PATH))
    @mkdir(DB_CONFIG_PATH);
if (! is_dir(DB_BACKUP_FILE_PATH))
    @mkdir(DB_BACKUP_FILE_PATH);
if (! is_dir(RUNTIME_PATH))
    @mkdir(RUNTIME_PATH ,FILE_MODE, true);
if (! is_dir(dirname(__FILE__) . '/assets'))
    @mkdir(dirname(__FILE__) . '/assets', FILE_MODE, true);
