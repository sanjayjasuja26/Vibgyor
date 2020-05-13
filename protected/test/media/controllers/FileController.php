<?php
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
namespace app\modules\media\controllers;

use app\components\TActiveForm;
use app\components\TController;
use app\models\User;
use app\modules\media\models\File;
use app\modules\media\models\search\File as FileSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * FileController implements the CRUD actions for File model.
 */
class FileController extends TController
{

    public function behaviors()
    {
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
                            'delete',
                            'delete-file',
                            'ajax',
                            'upload',
                            'mass',
                            'file',
                            'upload',
                            'file',
                            'image',
                            'send',
                            'delete-file',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isAdmin() || User::isManager();
                        }
                    ],
                    [
                        'actions' => [
                            'upload',
                            'image',
                            'file',
                            'send'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return (User::isCompanyAdmin() || User::isCompanyManager() || User::isCompanyPrescriber());
                        }
                    ],
                    [
                        'actions' => [
                            'upload',
                            'image',
                            'file',
                            'send',
                            'delete-file'
                        ],
                        'allow' => true,
                        'roles' => [
                            '?',
                            '@',
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
                    ]
                ]
            ]
        ];
    }

    /*
     * public function beforeAction($action)
     * {
     * if ($action->id == 'upload') {
     * $this->enableCsrfValidation = false;
     * }
     *
     * return parent::beforeAction($action);
     * }
     */
    public function actionUpload($modelId, $modelType, $createUserId = null, $typeId = File::TYPE_IMAGE)
    {
        \Yii::$app->response->format = 'json';
        
        $images = UploadedFile::getInstancesByName('qqfile');
        $response = [
            'success' => false
        ];
        if (! empty($images)) {
            foreach ($images as $image) {
                $model = new File();
                $model->model_type = $modelType;
                $model->model_id = $modelId;
                
                if (empty($createUserId)) {
                    $model->created_by_id = (isset(\Yii::$app->user) ? \Yii::$app->user->id : null);
                    $model->createBy = (isset(\Yii::$app->user) ? \Yii::$app->user->identity->full_name : 'Guest');
                } else {
                    $model->created_by_id = $createUserId;
                    $user = User::findOne($createUserId);
                    if (! empty($user)) {
                        $model->createBy = $user->full_name;
                    } else {
                        $model->createBy = 'Guest';
                    }
                }
                $model->type_id = $typeId;
                
                $model->uploadImageByFile($image);
                
                if (! $model->save()) {
                    // print_r($model->errors);
                    $response['error'] = $model->getErrorsString();
                } else {
                    $response['success'] = true;
                }
            }
            return $response;
        }
        return $response;
    }

    /**
     * Lists all File models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->updateMenuItems();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new File model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new File([
            'scenario' => 'insert'
        ]);
        $model->loadDefaultValues();
        $model->state_id = File::STATE_ACTIVE;
        $post = \yii::$app->request->post();
        if (\yii::$app->request->isAjax && $model->load($post)) {
            \yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return TActiveForm::validate($model);
        }
        if ($model->load($post)) {
            
            // print_r($_FILES);exit;
            
            if ($model->save()) {
                return $this->redirect([
                    'view',
                    'id' => $model->id
                ]);
            }
        }
        $this->updateMenuItems();
        return $this->render('add', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing File model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if ( !empty($model->thumb_file) && is_file(UPLOAD_THUMB_PATH . $model->thumb_file) ) {
            unlink(UPLOAD_THUMB_PATH . $model->thumb_file);
        }
        if ( !empty($model->file) && is_file(UPLOAD_PATH . $model->file) ) {
            unlink(UPLOAD_PATH . $model->file);
        }
        
        $model->delete();
        
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionImage($id, $thumb = true)
    {
        $model = $this->findModel($id);
        
        if ($thumb) {
            $file = UPLOAD_THUMB_PATH . $model->thumb_file;
        } else {
            $file = UPLOAD_PATH . $model->file;
        }
        if (! is_file($file)) {
            return false;
        }
        return \Yii::$app->response->sendFile($file);
    }

    public function actionSend($file)
    {
        $file = UPLOAD_PATH . $file;
        if (! is_file($file)) {
            return false;
        }
        \Yii::$app->response->sendFile($file);
    }

    public function actionDeleteFile($model, $id, $attribute = 'file')
    {
        \Yii::$app->response->format = 'json';
        $response = [
            'success' => false
        ];
        $modelData = $model::findOne($id);
        if ($modelData) {
            $path = UPLOAD_PATH . $file;
            if (file_exists($path)) {
                unlink($path);
            }
            $modelData->delete();
            $response['success'] = true;
        }
        return $response;
    }

    /**
     * Finds the File model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return File the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $accessCheck = false)
    {
        if (($model = File::findOne($id)) !== null) {
            if ($accessCheck)
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));
            
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function updateMenuItems($model = null)
    {}
}
