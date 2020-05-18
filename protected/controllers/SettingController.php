<?php

namespace app\controllers;

use app\components\SActiveForm;
use app\components\SController;
use app\models\Setting;
use app\models\User;
use Yii;
use app\components\filters\AccessControl;
use app\components\filters\AccessRule;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * SettingController implements the CRUD actions for Setting model.
 */
class SettingController extends SController {

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
                            'view',
                            'config',
                            'index',
                            'updateconfig',
                            'view',
                            'update',
                            'delete',
                            'ajax-update',
                            'ajax',
                            'mass'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isAdmin();
                        }
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
     * Lists all Setting models.
     *
     * @return mixed
     */
    public function actionIndex() {
        $model = Setting::find()->all();
        $this->updateMenuItems();
        return $this->render('index', [
                    'model' => $model
        ]);
    }

    /**
     * Displays a single Setting model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        $this->updateMenuItems($model);
        return $this->render('view', [
                    'model' => $model
        ]);
    }

    /**
     * Creates a new Setting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd() {
        $model = new Setting();
        $model->loadDefaultValues();
        $model->state_id = Setting::STATE_ACTIVE;
        $post = \yii::$app->request->post();
        if (\yii::$app->request->isAjax && $model->load($post)) {
            \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }
        if ($model->load($post)) {
            if ($model->save()) {
                return $this->redirect([
                            'config',
                            'id' => $model->id
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', "Error! " . $model->getErrorsString());
            }
        }
        $this->updateMenuItems();
        return $this->render('add', [
                    'model' => $model
        ]);
    }

    public function actionConfig($id) {
        $model = $this->findModel($id);
        $post = \yii::$app->request->post();
        if (\yii::$app->request->isAjax && $model->load($post)) {
            \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }
        if ($model->load($post)) {
            $config = [];
            if (!empty($post['Setting']['keyName'])) {
                foreach ($post['Setting']['keyName'] as $key => $name) {
                    $live[$name] = [
                        'type' => $post['Setting']['keyType'][$key],
                        'required' => (isset($post['Setting']['keyRequired']) && isset($post['Setting']['keyRequired'][$key])) ? $post['Setting']['keyRequired'][$key] : false,
                        'value' => isset($post['Setting']['keyValue'][$key]) ? $post['Setting']['keyValue'][$key] : false
                    ];
                }
            }
            $model->value = Json::encode($live);

            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'Setting Saved Successfully.'));
                return $this->redirect([
                            'index'
                ]);
            } else {
                Yii::$app->getSession()->setFlash('error', \Yii::t('app', "Error! ") . $model->getErrorsString());
            }
        }
        $this->updateMenuItems();
        return $this->render('config', [
                    'model' => $model
        ]);
    }

    /**
     * Updates an existing Setting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id, false);
        $post = \yii::$app->request->post();

        if ($model->load($post)) {
            if (!empty($model->keyValue)) {
                foreach ($model->keyValue as $key => $value) {
                    if (isset($value['type']) && ($value['type'] == Setting::KEY_TYPE_BOOL)) {
                        $val = isset($value['value']) ? 1 : 0;
                        $model->keyValue[$key]['value'] = $val;
                    }
                }
            }
            $model->value = Json::encode($model->keyValue);

            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Setting Updated Successfully.');
            } else {
                Yii::$app->getSession()->setFlash('error', "Error! " . $model->getErrorsString());
            }
        }
        return $this->redirect([
                    'index'
        ]);
    }

    public function actionAjaxUpdate($key) {
        $model = $this->findModel([
            'key' => $key
        ]);
        return $this->renderPartial('_ajax-update', [
                    'model' => $model,
                    'key' => $key
        ]);
    }

    /**
     * Deletes an existing Setting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->delete();
        if (\Yii::$app->request->isAjax) {
            return true;
        }
        Yii::$app->getSession()->setFlash('success', 'Setting Deleted Successfully.');
        return $this->redirect([
                    'index'
        ]);
    }

    public function actionUpdateconfig() {
        Setting::setDefaultConfig();
        return $this->redirect([
                    'index'
        ]);
    }

    /**
     * Finds the Setting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Setting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $accessCheck = true) {
        if (($model = Setting::findOne($id)) !== null) {

            if ($accessCheck && !($model->isAllowed()))
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));

            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function updateMenuItems($model = null) {
        switch (\Yii::$app->controller->action->id) {
            case 'index': {
                    $this->menu['add'] = array(
                        'label' => '<span class="fa fa-download"></span>',
                        'title' => Yii::t('app', 'Update'),
                        'url' => [
                            'updateconfig'
                        ],
                        'visible' => (User::isAdmin() && (YII_ENV == "dev"))
                    );
                }
                break;
        }
    }

}
