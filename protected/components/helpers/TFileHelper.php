<?php

namespace app\modules\server\helpers;

use yii\base\Exception;
use yii\helpers\FileHelper;

/**
 * Setup Commands for first time
 *
 * @author shiv
 *        
 */
class TFileHelper extends FileHelper {

    public static function getTempDirectory() {
        $tmpDir = \Yii::$app->runtimePath . '/tmp';

        if (!is_dir($tmpDir) && (!@mkdir($tmpDir) && !is_dir($tmpDir))) {
            throw new Exception('temp directory does not exist');
        }

        return $tmpDir;
    }

    public static function getTempFile($prefix = 'temp') {
        $tmpDir = \Yii::$app->runtimePath . '/tmp';

        if (!is_dir($tmpDir) && (!@mkdir($tmpDir) && !is_dir($tmpDir))) {
            throw new \yii\web\NotFoundHttpException('temp directory does not exist');
        }

        return tempnam($tmpDir, $prefix);
    }

}
