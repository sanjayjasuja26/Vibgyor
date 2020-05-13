<?php
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
namespace app\modules\notification\controllers\admin;

use app\modules\notification\controllers\NotificationController as BaseNotification;
use app\models\User;
use app\modules\notification\models\Notification;
use app\modules\notification\models\search\Notification as NotificationSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * NotificationController implements the CRUD actions for Notification model.
 */
class NotificationController extends BaseNotification
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
                            'view',
                            'delete',
                            'notify',
                            'ajax',
                            'mass'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isManager();
                        }
                    ],
                    [
                        'actions' => [
                            'delete',
                            'mass',
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
                            'view'
                        ],
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                    ],
                    [
                        'actions' => [
                            'notify'
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

    /**
     * Lists all Notification models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotificationSearch();
        
        if (User::isManager())
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        else
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        
        $this->updateMenuItems();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionNotify()
    {
        \Yii::$app->response->format = 'json';
        $response = [
            'status' => '400',
            'count' => 0
        ];
        $count = Notification::find()->where([
            'is_read' => Notification::IS_NOT_READ,
            'to_user_id' => \Yii::$app->user->id
        ])->count();
        if (! empty($count)) {
            $notifications = Notification::find()->where([
                'is_read' => Notification::IS_NOT_READ,
                'to_user_id' => \Yii::$app->user->id
            ])
                ->limit('20')
                ->all();
            
            if ($notifications) {
                foreach ($notifications as $notification) {
                    $url = Url::toRoute([
                        '/notification/notification/view',
                        'id' => $notification->id
                    ]);
                    $time = \Yii::$app->formatter->asRelativeTime(strtotime($notification->created_on));
                    $description = StringHelper::truncate($notification->description, 600, '...');
                    
                    $response['data'][$notification->id] = "<a class='content' data-id='{$notification->id}' href='$url'>
					   <div class='notification-item'>
    						<h4 class='item-title'> $notification->title   <spna class='pull-right'> $time </span></h4>
    						<p class='item-info'>$description</p>
					   </div>
				    </a>";
                }
            }
            $response['status'] = 200;
            $response['count'] = $count;
        }
        
        return $response;
    }

    public function actionClear($truncate = true)
    {
        $query = Notification::find();
        foreach ($query->each() as $model) {
            $model->delete();
        }
        if ($truncate) {
            Notification::truncate();
        }
        \Yii::$app->session->setFlash('success', 'Done !!!');
        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Displays a single Notification model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        $model->is_read = Notification::IS_READ;
        $model->save(false, [
            'is_read'
        ]);
        $this->updateMenuItems($model);
        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Notification model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $model->delete();
        return $this->redirect([
            'index'
        ]);
    }

    /**
     * Finds the Notification model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Notification the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $accessCheck = true)
    {
        if (($model = Notification::findOne($id)) !== null) {
            
            if ($accessCheck && ! ($model->isAllowed()))
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));
            
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function updateMenuItems($model = null)
    {
        switch (\Yii::$app->controller->action->id) {
            
            case 'index':
                {
                    $this->menu['clear'] = array(
                        'label' => '<span class=" glyphicon glyphicon-remove"></span>',
                        'title' => Yii::t('app', 'Clear'),
                        'url' => [
                            'clear'
                            /* 'id' => $model->id */
                        ],
                        'visible' => User::isAdmin()
                    );
                }
                break;
            case 'view':
                {
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
