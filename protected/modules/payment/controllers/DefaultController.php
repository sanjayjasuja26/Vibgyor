<?php
namespace app\modules\payment\controllers;

use app\components\SController;
use app\models\User;
use app\modules\payment\models\Gateway;
use app\modules\payment\models\Transaction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Default controller for the `payment` module
 */
class DefaulSController extends SController
{

    /**
     * Renders the index view for the module
     *
     * @return string
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'index'
                ],
                'rules' => [
                    [
                        'actions' => [
                            'pay',
                            'success'
                        ],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return User::isAdmin();
                        }
                    ],
                    [
                        'actions' => [
                            'pay',
                            'success'
                        ],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return User::isManager();
                        }
                    ],
                    [
                        'actions' => [
                            'index',
                            'success',
                            'pay'
                        ],
                        'allow' => true,
                        'roles' => [
                            '*',
                            '@',
                            '?'
                        ]
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => [
                        'post'
                    ]
                ]
            ]
        ];
    }

    public function actionIndex($a = null, $c = "USD")
    {
        $this->layout = "guest-main";
        
        $gateways = Gateway::findActive()->where([
            'state_id' => Gateway::STATE_ACTIVE
        ])
            ->groupBy('type_id')
            ->all();
        
        return $this->render('index', [
            'amount' => $a,
            'currency' => $c,
            'gateways' => $gateways
        ]);
    }

    public function actionSuccess($id)
    {
        $this->layout = "guest-main";
        $model = Transaction::findOne($id);
        return $this->render('success', [
            'model' => $model
        ]);
    }

    protected function updateMenuItems($model = null)
    {}
}
