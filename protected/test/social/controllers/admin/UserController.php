<?php

namespace app\modules\social\controllers\admin;

use app\modules\social\controllers\UserController as BaseUser;
use app\modules\social\components\TAuthHandler;
use yii\filters\AccessControl;
use yii\filters\AccessRule;

/**
 * ProviderController implements the CRUD actions for Provider model.
 */
class UserController extends BaseUser {

    public $layout = "main";

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className()
                ],
                'rules' => [
                    [
                        'actions' => [
                            'onAuthSuccess',
                            'auth'
                        ],
                        'allow' => true,
                        'roles' => [
                            '?',
                            '*'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function actions() {
        return [
            'auth' => [
                'class' => 'app\modules\social\components\TAuthAction',
                'successCallback' => [
                    $this,
                    'onAuthSuccess'
                ]
            ]
        ];
    }

    public function onAuthSuccess($client) {
        (new TAuthHandler($client))->handle();
    }

    protected function updateMenuItems($model = null) {
        
    }

}
