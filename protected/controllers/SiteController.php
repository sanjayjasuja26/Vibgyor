<?php

/**
*@author     : Sanjay Jasuja< sanjayjasuja26@gmail.com >
*/
namespace app\controllers;

use app\components\SActiveForm;
use app\components\SController;
use app\models\ContactForm;
use app\models\EmailQueue;
use app\models\User;
use Yii;
 use app\components\filters\AccessControl;
use yii\web\Response;
use app\modules\page\models\Page;
use bizley\contenttools\actions\UploadAction;
use bizley\contenttools\actions\InsertAction;
use bizley\contenttools\actions\RotateAction;

class SiteController extends SController
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
                            'contact',
                            'about',
                            'privacy',
                            'terms',
                            'error',
                            'content-tools-image-upload',
                            'content-tools-image-insert',
                            'content-tools-image-rotate'
                        ],
                        'allow' => true,
                        'roles' => [
                            '*',
                            '@',
                            '?'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ],
            'content-tools-image-upload' => UploadAction::className(),
            'content-tools-image-insert' => InsertAction::className(),
            'content-tools-image-rotate' => RotateAction::className()
        ];
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        return $this->render('error', [
            'message' => $exception->getMessage(),
            'name' => 'Error'
        ]);
    }

    public function actionIndex()
    {
        $this->updateMenuItems();
        if (! \Yii::$app->user->isGuest) {
            if (User::isAdmin()) {
                $this->layout = User::LAYOUT_MAIN;
                return $this->redirect('dashboard');
            } else {
                $this->layout = User::LAYOUT_GUEST_MAIN;
                return $this->redirect('dashboard');
            }
        } else {
            $this->layout = User::LAYOUT_GUEST_MAIN;
            return $this->render('index');
        }
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $sub = 'New Contact: ' . $model->subject;
            $from = $model->email;
            $message = \yii::$app->view->renderFile('@app/mail/contact.php', [
                'user' => $model
            ]);
            EmailQueue::sendEmailToAdmins([
                'from' => $from,
                'subject' => $sub,
                'html' => $message
            ], true);
            \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'Warm Greetings!! Thank you for contacting us. We have received your request. Our representative will contact you soon.'));
            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model
        ]);
    }

    public function actionAbout()
    {
        $this->layout = User::LAYOUT_GUEST_MAIN;
        $model = Page::find()->where([
            'type_id' => Page::TYPE_ABOUT_US
        ])->one();
        return $this->render('about', [
            'model' => $model
        ]);
    }

    public function actionPrivacy()
    {
        $this->layout = User::LAYOUT_GUEST_MAIN;
        $model = Page::find()->where([
            'type_id' => Page::TYPE_PRIVACY
        ])->one();
        return $this->render('policy', [
            'model' => $model
        ]);
    }

    public function actionTerms()
    {
        $this->layout = User::LAYOUT_GUEST_MAIN;

        $model = Page::find()->where([
            'type_id' => Page::TYPE_TERM_CONDITION
        ])->one();
        return $this->render('term', [
            'model' => $model
        ]);
    }
}