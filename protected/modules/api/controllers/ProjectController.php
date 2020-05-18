<?php
namespace app\modules\api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use app\models\search\Project;
use app\models\Project as ProjectModel;

/**
 * ProjecSController implements the API actions for Project model.
 */
class ProjecSController extends ApiTxController
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
                            'get',
                            'update',
                            'delete'
                        ],
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                    ],
                    [
                        'actions' => [
                            'index',
                            'get',
                            'update'
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

    /**
     * Lists all Project models.
     *
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        $data = [];
        $model = new Project();
        if ($id != null){
            $model = ProjectModel::findOne($id);
            if (!empty($model)){
               $data['status'] = self::API_OK;
               $data['detail'] = $model->asJson();
            }else {
                $data['error'] = \Yii::t('app', 'No New Task Found');
            }
            $this->setResponse($data);
            return $this->sendResponse();
        }
        $dataProvider = $model->searchNewProject(\Yii::$app->request->queryParams);
        if (! empty($dataProvider->getCount() > 0)) {
            $data['list'] = array_map(function ($data) {
                return $data->asJson();
            }, $dataProvider->getModels());
            $data['count'] = $dataProvider->getTotalCount();
            $data['page'] = $dataProvider->getPagination()->page + 1;
            $data['status'] = self::API_OK;
        } else {
            $data['error'] = \Yii::t('app', 'No New Task Found');
        }
        $this->setResponse($data);
        return $this->sendResponse();
    }

    /**
     * Displays a single app\models\Project model.
     *
     * @return mixed
     */
    public function actionGet($id)
    {
        return $this->txget($id, "app\models\Project");
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $data = [];
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            
            if ($model->save()) {
                
                $data['status'] = self::API_OK;
                
                $data['detail'] = $model;
            } else {
                $data['error'] = $model->flattenErrors;
            }
        } else {
            $data['error_post'] = 'No Data Posted';
        }
        
        return $this->sendResponse($data);
    }

    /**
     * Deletes an existing Project model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        return $this->txDelete($id, "app\models\Project");
    }
}
