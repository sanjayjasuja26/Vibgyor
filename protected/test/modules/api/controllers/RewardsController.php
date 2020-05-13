<?php
namespace app\modules\api\controllers;

use app\models\Reward;
use app\models\RewardRedeem;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\VarDumper;
use yii\data\ActiveDataProvider;
use app\modules\notification\models\Notification;

/**
 * RewardsController implements the API actions for Reward model.
 */
class RewardsController extends ApiTxController
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
                            'get',
                            'update',
                            'delete',
                            'redeem',
                            'reward-redeem',
                            'get-operator'
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
     * Lists all Reward models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new \app\models\search\Reward();
        $dataProvider = $model->search(\Yii::$app->request->queryParams);
        
        if (! empty($dataProvider->getCount() > 0)) {
            $data['list'] = array_map(function ($data) {
                return $data->asJson();
            }, $dataProvider->getModels());
            $data['user_points'] = \Yii::$app->user->identity->points;
            $data['count'] = $dataProvider->getTotalCount();
            $data['page'] = $dataProvider->getPagination()->page + 1;
            $data['status'] = self::API_OK;
        } else {
            $data['error'] = \Yii::t('app', 'No reward available yet');
        }
        $this->setResponse($data);
        return $this->sendResponse();
        // $arr = $this->txindex("app\models\search\Reward");
    }

    /**
     * Displays a single app\models\Reward model.
     *
     * @return mixed
     */
    public function actionGet($id)
    {
        return $this->txget($id, "app\models\Reward");
    }

    /**
     * Creates a new Reward model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        return $this->txSave("app\models\Reward");
    }

    /**
     * Updates an existing Reward model.
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
     * Deletes an existing Reward model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        return $this->txDelete($id, "app\models\Reward");
    }

    public function actionRedeem($id)
    {
        $data = [];
        $reward = Reward::findOne($id);
        $rewardRedeem = new RewardRedeem();
        $userPoint = \Yii::$app->user->identity->points;
        $post = \Yii::$app->request->post();
        if ($post) {
            $rewardRedeem->load($post);
            if ($userPoint >= $reward->points) {
                $rewardRedeem->reward_id = $reward->id;
                $rewardRedeem->user_id = \Yii::$app->user->identity->id;
                $rewardRedeem->state_id = RewardRedeem::STATE_PENDING;
                
                if ($rewardRedeem->save()) {
                    $user = User::find()->where([
                        'id' => $rewardRedeem->user_id
                    ])->one();
                    $user->points = (int) ($user->points - $rewardRedeem->reward->points);
                    $user->save(false, [
                        'points'
                    ]);
                    $data['status'] = self::API_OK;
                    if ($user->push_enabled)
                        Notification::create([
                            'to_user_id' => $user->id,
                            'created_by_id' => $user->id,
                            'model' => $reward,
                            'title' => 'Congratulation! Your points are redeem successfully.'  
                        ]);
                } else {
                    $data['error'] = $rewardRedeem->getErrorsString();
                }
            } else {
                $data['error'] = "Your points are low to get this recharge.";
            }
        }
        
        return $this->sendResponse($data);
    }

    public function actionRewardRedeem($page = null)
    {
        $data = [];
        $query = RewardRedeem::find()->where([
            'created_by_id' => \Yii::$app->user->id
        ]);
        $dataprovider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page
            
            ]
        ]);
        
        if (! empty($dataprovider->getCount() > 0)) {
            foreach ($dataprovider->models as $model) {
                $list[] = $model->asJson(true);
            }
            // $data['user_points'] = \Yii::$app->user->identity->points;
            $data['pageSize'] = $dataprovider->pagination->pageSize;
            $data['pageCount'] = $dataprovider->pagination->pageCount;
            $data['status'] = self::API_OK;
            $data['list'] = $list;
        } else {
            $data['error'] = \Yii::t('app', 'No Redeem History Available');
        }
        $data['user_points'] = \Yii::$app->user->identity->points;
        return $this->sendResponse($data);
    }

    public function actionGetOperator()
    {
        $res = [];
        $results = RewardRedeem::getOperatorType();
        foreach ($results as $key => $value) {
            $res[] = [
                'id' => $key,
                'name' => $value
            ];
        }
        $data['status'] = self::API_OK;
        $data['detail'] = $res;
        return $this->sendResponse($data);
    }
}
