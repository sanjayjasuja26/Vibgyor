<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\components;

use yii\web\Request;

class TRequest extends Request
{

    public $parsers = [
        'application/json' => 'yii\web\JsonParser'
    ];

    public function init()
    {
        parent::init();
        $this->enableCsrfValidation = defined('YII_TEST') ? false : true;
        $this->cookieValidationKey .= md5(\Yii::$app->id . $_SERVER['SERVER_ADDR']);
        $this->csrfParam = '_csrf_' . \Yii::$app->id;
        $path = $this->baseUrl;
        if (! empty($path)) {
            $this->csrfCookie['path'] = $path;
        }
    }
}