<?php

namespace app\components;

use yii\web\Session;

class TSession extends Session {

    public function init() {
        $cookiePath = '/';
        $path = \Yii::$app->request->baseUrl;
        if (!empty($path)) {
            $cookiePath = $path;
        }
        $this->setCookieParams([
            'httponly' => true,
            'path' => $cookiePath
        ]);
        $this->name = '_session_' . \Yii::$app->id;
        $savePath = \Yii::$app->runtimePath . DIRECTORY_SEPARATOR . 'sessions';
        if (!is_dir($savePath)) {
            mkdir($savePath, FILE_MODE, true);
        }
        $this->savePath = $savePath;
        parent::init();
    }

}
