<?php

namespace app\controllers;

use app\components\SActiveForm;
use app\components\SController;
use app\models\EmailQueue;
use app\models\User;
use app\models\search\EmailQueue as EmailQueueSearch;
use Yii;
use app\components\filters\AccessControl;
use app\components\filters\AccessRule;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * EmailQueueController implements the CRUD actions for EmailQueue model.
 */
class EmailQueueController extends SController {

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
                            'index',
                            'add',
                            'view',
                            'update',
                            'delete',
                            'ajax',
                            'clear',
                            'mass'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isAdmin();
                        }
                    ],
                    [
                        'actions' => [
                            'index',
                            'ajax',
                            'view',
                            'show'
                        ],
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                    ]
                ]
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => [
                        'post'
                    ]
                ]
            ]
        ];
    }

    /**
     * actionMass delete in mass as items are checked
     *
     * @param string $action
     * @return string
     */
    public function actionMass($action = 'delete') {
        \Yii::$app->response->format = 'json';
        $response['status'] = 'NOK';
        $status = EmailQueue::massDelete();
        if ($status == true) {
            $response['status'] = 'OK';
        }
        return $response;
    }

    public function actionClear($truncate = true) {
        $query = EmailQueue::find();
        foreach ($query->each() as $model) {
            $model->delete();
        }
        if ($truncate) {
            EmailQueue::truncate();
        }
        \Yii::$app->session->setFlash('success', 'Done !!!');
        return $this->redirect([
                    'index'
        ]);
    }

    /**
     * Lists all EmailQueue models.
     *
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EmailQueueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->updateMenuItems();
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single EmailQueue model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        $this->updateMenuItems($model);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                        'model' => $model
            ]);
        }
        return $this->render('view', [
                    'model' => $model
        ]);
    }

    /**
     * Creates a new EmailQueue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    /*
     * public function actionAdd()
     * {
     * $model = new EmailQueue();
     * $model->loadDefaultValues();
     * $model->state_id = EmailQueue::STATE_PENDING;
     * $post = \yii::$app->request->post();
     * if (\yii::$app->request->isAjax && $model->load($post)) {
     * \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
     * return SActiveForm::validate($model);
     * }
     * if ($model->load($post) && $model->save()) {
     * return $this->redirect([
     * 'view',
     * 'id' => $model->id
     * ]);
     * }
     * $this->updateMenuItems();
     * return $this->render('add', [
     * 'model' => $model
     * ]);
     * }
     */

    /**
     * Updates an existing EmailQueue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        $post = \yii::$app->request->post();
        if (\yii::$app->request->isAjax && $model->load($post)) {
            \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }
        if ($model->load($post) && $model->save()) {
            return $this->redirect([
                        'view',
                        'id' => $model->id
            ]);
        }
        $this->updateMenuItems($model);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                        'model' => $model
            ]);
        }
        return $this->render('update', [
                    'model' => $model
        ]);
    }

    /**
     * Deletes an existing EmailQueue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);

        $model->delete();
        return $this->redirect([
                    'index'
        ]);
    }

    public function actionShow($id) {
        $model = $this->findModel($id, false);
        if ($model == null)
            throw new NotFoundHttpException('The requested page does not exist.');
        echo $model->message;
        exit();
    }

    /**
     * Finds the EmailQueue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return EmailQueue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $accessCheck = true) {
        if (($model = EmailQueue::findOne($id)) !== null) {

            if ($accessCheck && !($model->isAllowed()))
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));

            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function updateMenuItems($model = null) {
        switch (\Yii::$app->controller->action->id) {

            case 'add': {
                    $this->menu['manage'] = array(
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ]
                            // 'visible' => User::isAdmin ()
                    );
                }
                break;
            case 'index': {
                    $this->menu['clear'] = array(
                        'label' => '<span class="glyphicon glyphicon-remove"></span>',
                        'title' => Yii::t('app', 'Clear'),
                        'url' => [
                            'clear'
                        ]
                            // 'visible' => User::isAdmin ()
                    );
                }
                break;
            case 'update': {
                    $this->menu['manage'] = array(
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ]
                            // 'visible' => User::isAdmin ()
                    );
                }
                break;
            default:
            case 'view': {
                    $this->menu['manage'] = array(
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ]
                            // 'visible' => User::isAdmin ()
                    );
                    if ($model != null) {
                        $this->menu['delete'] = array(
                            'label' => '<span class="glyphicon glyphicon-trash"></span>',
                            'title' => Yii::t('app', 'Delete'),
                            'url' => [
                                'delete',
                                'id' => $model->id
                            ]
                                // 'visible' => User::isAdmin ()
                        );
                    }
                }
        }
    }

}
