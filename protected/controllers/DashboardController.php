<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\controllers;

use app\components\TController;
use app\models\User;
 use app\components\filters\AccessControl;
use app\models\Setting;

class DashboardController extends TController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'default-data'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isAdmin() || User::isCollege() || User::isUniversity() ;
                        }
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $this->updateMenuItems();
        $smtpConfig = isset(\Yii::$app->settings) ? \Yii::$app->settings->smtp : null;
        if (empty($smtpConfig)) {
            Setting::setDefaultConfig();
        }
        return $this->render('index');
    }

    public static function MonthlySignups()
    {
        $date = new \DateTime();
        $date->modify('-12  months');
        $count = array();
        for ($i = 1; $i <= 12; $i ++) {
            $date->modify('+1 months');
            $month = $date->format('Y-m');

            $count[$month] = (int) User::find()->where([
                'like',
                'created_on',
                $month
            ])
                ->andWhere([
                '!=',
                'role_id',
                User::ROLE_ADMIN
            ])
                ->count();
        }
        return $count;
    }

    public function actionDefaultData()
    {
        Setting::setDefaultConfig();
        $msg = 'Done !! Setting reset succefully!!!';
        \Yii::$app->session->setFlash('success', $msg);
        return $this->redirect(\Yii::$app->request->referrer);
    }
}
