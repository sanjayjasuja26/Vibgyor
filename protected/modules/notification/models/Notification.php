<?php

/**
 * This is the model class for table "tbl_notification".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $model_id
 * @property string $model_type
 * @property integer $is_read
 * @property integer $state_id
 * @property integer $type_id
 * @property string $created_on
 * @property integer $to_user_id
 * @property integer $created_by_id
 */
namespace app\modules\notification\models;

use app\models\EmailQueue;
use app\models\User;
use Yii;
use app\modules\api2\models\DeviceDetail;

class Notification extends \app\components\SActiveRecord
{


    public function __toString()
    {
        return (string) $this->title;
    }

    public static function getModelOptions()
    {
        return [
            "TYPE1",
            "TYPE2",
            "TYPE3"
        ];
        // return ArrayHelper::Map ( Model::findActive ()->all (), 'id', 'title' );
    }

    public function getModel()
    {
        $list = self::getModelOptions();
        return isset($list[$this->model_id]) ? $list[$this->model_id] : 'Not Defined';
    }

    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_DELETED = 2;

    public static function getStateOptions()
    {
        return [
            self::STATE_INACTIVE => "New",
            self::STATE_ACTIVE => "Active",
            self::STATE_DELETED => "Archived"
        ];
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }

    public function getStateBadge()
    {
        $list = [
            self::STATE_INACTIVE => "primary",
            self::STATE_ACTIVE => "success",
            self::STATE_DELETED => "danger"
        ];
        return isset($list[$this->state_id]) ? \yii\helpers\Html::tag('span', $this->getState(), [
            'class' => 'label label-' . $list[$this->state_id]
        ]) : 'Not Defined';
    }

    const IS_NOT_READ = 0;

    const IS_READ = 1;

    public static function getIsReadOptions()
    {
        return [
            self::IS_NOT_READ => "Not Read",
            self::IS_READ => "Read"
        ];
    }

    public function getIsRead()
    {
        $list = self::getIsReadOptions();
        return isset($list[$this->is_read]) ? $list[$this->is_read] : 'Not Defined';
    }

    public function getIsReadBadge()
    {
        $list = [
            self::IS_READ => "success",
            self::IS_NOT_READ => "danger"
        ];
        return isset($list[$this->is_read]) ? \yii\helpers\Html::tag('span', $this->getIsRead(), [
            'class' => 'label label-' . $list[$this->is_read]
        ]) : 'Not Defined';
    }

    public static function getTypeOptions()
    {
        return [
            "TYPE1",
            "TYPE2",
            "TYPE3"
        ];
        // return ArrayHelper::Map ( Type::findActive ()->all (), 'id', 'title' );
    }

    public function getType()
    {
        $list = self::getTypeOptions();
        return isset($list[$this->type_id]) ? $list[$this->type_id] : 'Not Defined';
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (! isset($this->created_on))
                $this->created_on = date('Y-m-d H:i:s');
            if (! isset($this->to_user_id))
                $this->to_user_id = Yii::$app->user->id;
            if (! isset($this->created_by_id))
                $this->created_by_id = Yii::$app->user->id;
        } else {}
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'title',
                    'model_id',
                    'model_type',
                    'created_on'
                ],
                'required'
            ],
            [
                [
                    'description'
                ],
                'string'
            ],
            [
                [
                    'model_id',
                    'is_read',
                    'state_id',
                    'type_id',
                    'to_user_id',
                    'created_by_id'
                ],
                'integer'
            ],
            [
                [
                    'created_on'
                ],
                'safe'
            ],
            [
                [
                    'title'
                ],
                'string',
                'max' => 1024
            ],
            [
                [
                    'model_type'
                ],
                'string',
                'max' => 256
            ],
            [
                [
                    'title',
                    'model_type',
                    'is_read'
                ],
                'trim'
            ],
            [
                [
                    'state_id'
                ],
                'in',
                'range' => array_keys(self::getStateOptions())
            ],
            [
                [
                    'type_id'
                ],
                'in',
                'range' => array_keys(self::getTypeOptions())
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'model_id' => Yii::t('app', 'Model'),
            'model_type' => Yii::t('app', 'Model Type'),
            'is_read' => Yii::t('app', 'Is Read'),
            'state_id' => Yii::t('app', 'State'),
            'type_id' => Yii::t('app', 'Type'),
            'created_on' => Yii::t('app', 'Created On'),
            'to_user_id' => Yii::t('app', 'To User'),
            'created_by_id' => Yii::t('app', 'Created By')
        ];
    }

    public function getToUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'to_user_id'
        ]);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), [
            'id' => 'created_by_id'
        ]);
    }

    public static function getHasManyRelations()
    {
        $relations = [];
        return $relations;
    }

    public static function getHasOneRelations()
    {
        $relations = [];
        return $relations;
    }

    public function beforeDelete()
    {
        return parent::beforeDelete();
    }

    public function asJson($with_relations = false)
    {
        $json = [];
        $json['id'] = $this->id;
        $json['title'] = $this->title;
        $json['description'] = $this->description;
        $json['model_id'] = $this->model_id;
        $json['model_type'] = $this->model_type;
        $json['is_read'] = $this->is_read;
        $json['state_id'] = $this->state_id;
        $json['type_id'] = $this->type_id;
        $json['created_on'] = $this->created_on;
        $json['to_user_id'] = $this->to_user_id;
        $json['created_by_id'] = $this->created_by_id;
        if ($with_relations) {}
        return $json;
    }

    public function getUrl($action = 'view', $id = null)
    {
        $params = [
            '/notification/' . $this->geSControllerID() . '/' . $action
        ];
        if ($id != null)
            $params['id'] = $id;
        else
            $params['id'] = $this->id;
        // add the title parameter to the URL
        if ($this->hasAttribute('title'))
            $params['title'] = $this->title;
        else
            $params['title'] = (string) $this;
        return Yii::$app->getUrlManager()->createAbsoluteUrl($params, true);
    }

    public function isAllowed()
    {
        if (\Yii::$app->user->identity->role_id == User::ROLE_MANAGER) {
            if (\Yii::$app->controller->action->id == 'delete') {
                return false;
            }
            
            return true;
        }
        if ($this->to_user_id == \Yii::$app->user->id) {
            return true;
        }
        return parent::isAllowed();
    }

    /**
     * Create Notification
     *
     * Use :
     *
     * Notification::create([
     * 'model' => $model,
     * 'to_user_id' => 3,
     * 'title' => 'Lead Reminder',
     * 'created_by_id' => $model->id
     * ]);
     */
    public static function create($param = [], $sendEmailNow = false)
    {
        $notification = new self();
        $notification->to_user_id = $param['to_user_id'];
        $notification->created_by_id = $param['created_by_id'];
        $notification->title = \Yii::t('app', 'New Message Received');
        $notification->description = $param['title'];
        $notification->model_id = $param['model']->id;
        $notification->model_type = get_class($param['model']);
        $notification->is_read = Notification::IS_NOT_READ;
        if ($notification->save()) {
            $notification->sendNotificationOnApp();
            if ($sendEmailNow)
                EmailQueue::add([
                    'model' => $param['model'],
                    'from' => \Yii::$app->params['adminEmail'],
                    'to' => $notification->toUser->email,
                    'subject' => 'A new task is added',
                    'view' => 'sendProjecSRequest',
                    'viewArgs' => [
                        'model' => $param['model'],
                        'user' => $notification->toUser
                    ]
                ]);
            
            return true;
        }
    }

    /**
     * Check if notification send or not
     *
     * Use :
     *
     * Notification::create([
     * 'model' => $model,
     * 'to_user_id' => 3,
     * 'title' => 'Lead Reminder',
     * 'created_by_id' => $model->id
     * ]);
     */
    public static function isNotify($param = [])
    {
        $notification = self::find()->select([
            'id'
        ])
            ->where([
            'model_id' => $param['model']->id,
            'model_type' => get_class($param['model']),
            'to_user_id' => $param['to_user_id'],
            'created_by_id' => $param['created_by_id']
        ])
            ->one();
        return $notification && true;
    }

    public function sendNotificationOnApp()
    {
        if (\Yii::$app->getModule('notification')->isNotify === false) {
            return;
        }
        $androidtoken = [];
        $iostoken = [];
        $tokens = "";
        $data = [];
        $sent_user = \Yii::$app->user->identity;
        
        $data['controller'] = \yii::$app->controller->id;
        $data['action'] = \yii::$app->controller->action->id;
        $data['message'] = $this->description;
        $data['user_id'] = $this->to_user_id;
        $data['from_user'] = $sent_user->full_name;
        
        if (! empty($sent_user->profile_file)) {
            $data['profile_file'] = \yii::$app->urlManager->createAbsoluteUrl([
                'file/files',
                'file' => $sent_user->profile_file
            ]);
        }
        $data['detail'] = $this->asJson();
        $user = User::findOne($this->to_user_id);
        if (! empty($user)) {
            
            $tokens = $user->authSessions;
            
            if (count($tokens) > 0) {
                foreach ($tokens as $token) {
                    if ($token->device_type == 1) {
                        $androidtoken[] = $token->device_token;
                    }
                    if ($token->device_type == 2)
                        $iostoken[] = $token->device_token;
                }
                if (! empty($androidtoken)) {
                    
                    try {
                        $datas = Yii::$app->firebase->sendDataMessage($androidtoken, $data);
                    } catch (\Exception $e) {
                        \Yii::error(\yii\helpers\VarDumper::dumpAsString('android NOTIFICATION SEND ERRROR'));
                        \Yii::error(\yii\helpers\VarDumper::dumpAsString($e->getMessage()));
                    }
                }
                
                if (! empty($iostoken)) {
                    $apns = \Yii::$app->apns;
                    $out = '';
                    foreach ($iostoken as $tokn) {
                        try {
                            $out = $apns->send($tokn, $data['message'], $data, [
                                'sound' => 'default',
                                'badge' => 1
                            ]);
                        } catch (\Exception $e) {
                            \Yii::error('Apple Push ERROR' . \yii\helpers\VarDumper::dumpAsString($e->getMessage()));
                        }
                        
                        return $out ? true : false;
                    }
                }
            }
        }
    }
}