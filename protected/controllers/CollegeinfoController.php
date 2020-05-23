<?php

/**
 * @author :Sanjay Jasuja < sanjayjasuja26@gmail.com >
 */

namespace app\controllers;

use Yii;
use app\models\Collegeinfo;
use app\models\search\Collegeinfo as CollegeinfoSearch;
use app\components\SController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use app\models\User;
use yii\web\HttpException;
use app\components\SActiveForm;

/**
 * CollegeinfoController implements the CRUD actions for Collegeinfo model.
 */
class CollegeinfoController extends SController {

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
                            'clear'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isAdmin();
                        }
                    ],
                    [
                        'actions' => [
                            'index',
                            'add',
                            'view',
                            'update',
                            'delete',
                            'ajax',
                            'mass'
                        ],
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                    ],
                    [
                        'actions' => [
                            'view',
                        ],
                        'allow' => true,
                        'roles' => [
                            '?',
                            '*'
                        ]
                    ]
                ]
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => [
                        'post'
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all Collegeinfo models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CollegeinfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->updateMenuItems();
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Collegeinfo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id, false);
        $this->updateMenuItems($model);
        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new Collegeinfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd() {
        $model = new Collegeinfo();
        $model->loadDefaultValues();
        $model->state_id = Collegeinfo::STATE_ACTIVE;
        $post = \yii::$app->request->post();
        if (\yii::$app->request->isAjax && $model->load($post)) {
            \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }
        if ($model->load($post) && $model->save()) {
            return $this->redirect($model->getUrl());
        }
        $this->updateMenuItems();
        return $this->render('add', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing Collegeinfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
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
            return $this->redirect($model->getUrl());
        }
        $this->updateMenuItems($model);
        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Collegeinfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);

        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Truncate an existing Collegeinfo model.
     * If truncate is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionClear($truncate = true) {
        $query = Collegeinfo::find();
        foreach ($query->each() as $model) {
            $model->delete();
        }
        if ($truncate) {
            Collegeinfo::truncate();
        }
        \Yii::$app->session->setFlash('success', 'Collegeinfo Cleared !!!');
        return $this->redirect([
                    'index'
        ]);
    }

    /**
     * Finds the Collegeinfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Collegeinfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $accessCheck = true) {
        if (($model = Collegeinfo::findOne($id)) !== null) {

            if ($accessCheck && !($model->isAllowed()))
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));

            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function updateMenuItems($model = null) {

        switch (\Yii::$app->controller->action->id) {

            case 'add' : {
                    $this->menu ['manage'] = [
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ],
                            //	'visible' => User::isAdmin ()
                    ];
                }
                break;
            case 'index' : {
                    $this->menu ['add'] = [
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'Add'),
                        'url' => [
                            'add'
                        ],
                            //	'visible' => User::isAdmin ()
                    ];
                    $this->menu['clear'] = [
                        'label' => '<span class=" glyphicon glyphicon-remove"></span>',
                        'title' => Yii::t('app', 'Clear'),
                        'url' => [
                            'clear'
                        ],
                        'visible' => User::isAdmin()
                    ];
                }
                break;
            case 'update' : {
                    $this->menu ['add'] = [
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'add'),
                        'url' => [
                            'add'
                        ],
                            //	'visible' => User::isAdmin ()
                    ];
                    $this->menu ['manage'] = [
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ],
                            //	'visible' => User::isAdmin ()
                    ];
                }
                break;
            default :
            case 'view' : {
                    $this->menu ['manage'] = [
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ],
                            //	'visible' => User::isAdmin ()
                    ];
                    if ($model != null) {
                        $this->menu ['update'] = [
                            'label' => '<span class="glyphicon glyphicon-pencil"></span>',
                            'title' => Yii::t('app', 'Update'),
                            'url' => $model->getUrl('update'),
                                //		'visible' => User::isAdmin ()
                        ];
                        $this->menu ['delete'] = [
                            'label' => '<span class="glyphicon glyphicon-trash"></span>',
                            'title' => Yii::t('app', 'Delete'),
                            'url' => $model->getUrl('delete')
                                //	    'visible' => User::isAdmin ()
                        ];
                    }
                }
        }
    }

}
