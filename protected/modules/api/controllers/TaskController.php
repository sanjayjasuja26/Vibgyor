<?php
namespace app\modules\api\controllers;

use app\models\File;
use app\models\Project;
use app\models\Task;
use app\modules\comment\models\Comment;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use app\models\User;
use app\modules\notification\models\Notification;

/**
 * TaskController implements the API actions for Task model.
 */
class TaskController extends ApiTxController
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
                            'assign-task',
                            'user-task',
                            'state',
                            'comment'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isUser();
                        }
                    ],
                    [
                        'actions' => [
                            'index',
                            'add',
                            'get',
                            'update',
                            'delete',
                            'assign-task',
                            'user-task',
                            'state',
                            'comment'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isAdmin();
                        }
                    ],
                    [
                        'actions' => [
                            'index',
                            'get'
                        ],
                        'allow' => true,
                        'roles' => [
                            '?',
                            '@'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all Task models.
     *
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        $data = [];
        $model = new Task();
        if ($id != null) {
            $model = Task::my()->where([
                'id' => $id
            ])->one();
            if (! empty($model)) {
                $data['status'] = self::API_OK;
                $data['detail'] = $model->asJson();
                $this->setResponse($data);
            } else {
                $data['error'] = \Yii::t('app', 'No Task Found');
                $data['status'] = self::API_NOK;
            }
            return $this->sendResponse();
        }
        return $this->txindex("app\models\search\Task");
    }

    /**
     * Displays a single app\models\Task model.
     *
     * @return mixed
     */
    public function actionGet($id)
    {
        return $this->txget($id, "app\models\Task");
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        return $this->txSave("app\models\Task");
    }

    /**
     * Updates an existing Task model.
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

    public function actionAssignTask($id)
    {
        $data = [];
        $model = Project::findOne($id);
        if (! empty($model)) {
            $task_count = Task::find()->where([
                'project_id' => $id
            ])->count();
            if ($model->target_count > $task_count) {
                $task = Task::find()->where([
                    'created_by_id' => \Yii::$app->user->id,
                    'project_id' => $id
                ])->one();
                if (empty($task)) {
                    $task = new Task();
                    $task->expired_time = date('Y-m-d H:i:s', strtotime("+1 day"));
                    $task->title = $model->title;
                    $task->description = $model->description;
                    $task->project_id = $id;
                    $task->task_time = $model->task_time;
                    $task->start_date = date('Y-m-d H:i:s');
                    $task->end_date = date('Y-m-d H:i:s', strtotime("+1 day"));
                    $task->state_id = Task::STATE_ACTIVE;
                    $task->created_by_id = \Yii::$app->user->id;
                    if ($task->save()) {
                        $task_count = Task::find()->where([
                            'project_id' => $id
                        ])->count();
                        if ($model->target_count == $task_count) {
                            $model->state_id = Project::STATE_COMPLETE;
                            $model->save();
                        }
                        $data['status'] = self::API_OK;
                        $data['detail'] = $task->asJson();
                    }
                } else {
                    $data['status'] = self::API_NOK;
                    $data['error_post'] = 'You already have this task';
                }
            } else {
                $data['status'] = self::API_NOK;
                $data['error_post'] = 'This task is already completed.';
            }
        } else {
            $data['status'] = self::API_NOK;
            $data['error_post'] = 'No Task Found';
        }
        
        return $this->sendResponse($data);
    }

    public function actionUserTask($page, $type = null)
    {
        $data = [];
        if ($type == null) {
            $query = Task::find()->where([
                'created_by_id' => \Yii::$app->user->id
            ]);
        } else {
            $query = Task::find()->where([
                'created_by_id' => \Yii::$app->user->id,
                'state_id' => $type
            ]);
        }
        $query = $query->orderBy([
            'state_id' => SORT_ASC,
            'id' => SORT_DESC
        ]);
        
        $data = Project::sendApiDataInList($query);
        
        return $this->sendResponse($data);
    }

    public function actionState($id, $type)
    {
        $data = [];
        $task = Task::findOne($id);
        if (! empty($task)) {
            if (in_array($type, [
                Task::STATE_INREVIEW,
                Task::STATE_CANCEL,
                Task::STATE_EXPIRED
            ])) {
                $task->state_id = $type;
                $task->save(false, [
                    'state_id'
                ]);
                $data['status'] = self::API_OK;
                $data['detail'] = $task->asJson();
            } else {
                $data['error'] = 'Operation Not Allowed';
            }
        } else {
            $data['error'] = 'No Task Found';
            $data['status'] = self::API_NOK;
        }
        return $this->sendResponse($data);
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        return $this->txDelete($id, "app\models\Task");
    }

    public function actionComment($id)
    {
        $data = [];
        $model = $this->findModel($id, false);
        $comment = new Comment();
        $post = \Yii::$app->request->post();
        $data['file'] = $_FILES;
        
        if (! empty($post)) {
            $model->load($post);
            
            /*
             * if (isset($_FILES['Comment'])) {
             * $files = $_FILES['Comment'];
             * if (! is_array($_FILES['Comment'])) {
             * $files = [
             * $files
             * ];
             * }
             *
             * foreach ($files as $file) {
             * $uploaded_file = UploadedFile::getInstance($file, 'file');
             * if ($uploaded_file != null) {
             * $file = File::add($model, $uploaded_file);
             * }
             * if ($file) {
             * $comment->comment = 'File uploaded ' . $file->name;
             * $comment->model_type = get_class($model);
             * $comment->model_id = $model->id;
             * $comment->state_id = 0;
             * }
             * if (! $comment->save()) {
             * VarDumper::dump($comment->errors);
             * }
             * }
             * }
             */
            if (! empty($model->work_done)) {
                $comment = new Comment();
                $comment->comment = $model->work_done;
                $comment->model_type = get_class($model);
                $comment->model_id = $model->id;
                $comment->state_id = 0;
                if (! $comment->save()) {
                    VarDumper::dump($comment->errors);
                }
            }
            $model->updateAttributes([
                'work_done'
            ]);
            $model->state_id = Task::STATE_INREVIEW;
            $model->updateAttributes([
                'state_id'
            ]);
            $user = $model->createdBy;
            if ($user->push_enabled){
                Notification::create([
                    'to_user_id' => $user->id,
                    'created_by_id' => $model->project->createdBy->id,
                    'model' => $model,
                    'title' => 'Task [ ' . $model->title . ' ] is moved to ' . $model->state . ' State.'
                ]);
            }
            $data['status'] = self::API_OK;
            $data['detail'] = $model->asJson();
        }
        return $this->sendResponse($data);
    }
}
