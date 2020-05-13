<?php
$fp = fopen(__DIR__ . '/timer.lock', 'w+');

if (! flock($fp, LOCK_EX | LOCK_NB)) {
    die("Timer lock pending");
}
require 'common.php';

$config = require (DB_CONFIG_PATH . '/console.php');

require (VENDOR_PATH . 'autoload.php');
require (VENDOR_PATH . 'yiisoft/yii2/Yii.php');

$application = new yii\console\Application($config);

function dlog($str)
{
    echo $str . PHP_EOL;
}

function cmd($application, $cmd)
{
    try {
        $application->runAction($cmd);
    } catch (\Exception $ex) {
        dlog('Command Failed:' . $cmd);
        dlog($ex->getMessage());
        dlog($ex->getTraceAsString());
    }
}

cmd($application, 'timer/email');

flock($fp, LOCK_UN);

fclose($fp);
