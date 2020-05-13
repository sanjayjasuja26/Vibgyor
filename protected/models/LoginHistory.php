<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/

/**
 * This is the model class for table "tbl_login_history".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $user_ip
 * @property string $user_agent
 * @property string $failer_reason
 * @property integer $state_id
 * @property integer $type_id
 * @property string $code
 * @property string $created_on === Related data ===
 * @property User $user
 */
namespace app\models;

use Yii;
use yii\helpers\VarDumper;

class LoginHistory extends \app\components\TActiveRecord
{

    public static function add($success = true, $user = null, $failer_reason = null)
    {
        $model = new LoginHistory();
        $model->user_id = $user != null ? $user->id : 1;
        $model->user_ip = \Yii::$app->request->getUserIP();
        $model->user_agent = \Yii::$app->request->getUserAgent();
        $model->code = \Yii::$app->request->referrer;
        $model->type_id = \Yii::$app->request->isAjax ? 1 : 0;
        $model->state_id = $success ? self::STATE_SUCCESS : self::STATE_FAILED;
        $model->failer_reason = is_array($failer_reason) ? json_encode($failer_reason) : $failer_reason;
        if (! $model->save()) {
            VarDumper::dump($model->errors);
            if (YII_DEBUG)
                exit();
        }
    }

    public function __toString()
    {
        return (string) $this->user_id;
    }

    const STATE_FAILED = 0;

    const STATE_SUCCESS = 1;

    public static function getStateOptions()
    {
        return [
            self::STATE_FAILED => "Failed",
            self::STATE_SUCCESS => "Success"
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
            self::STATE_FAILED => "primary",
            self::STATE_SUCCESS => "success"
        ];
        return isset($list[$this->state_id]) ? \yii\helpers\Html::tag('span', $this->getState(), [
            'class' => 'label label-' . $list[$this->state_id]
        ]) : 'Not Defined';
    }

    public static function getTypeOptions()
    {
        return [
            "Web",
            "Ajax",
            "API"
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'user_id'
        ]);
    }

    public function getType()
    {
        $list = self::getTypeOptions();
        return isset($list[$this->type_id]) ? $list[$this->type_id] : 'Not Defined';
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (! isset($this->user_id))
                $this->user_id = Yii::$app->user->id;
            if (! isset($this->created_on))
                $this->created_on = date('Y-m-d H:i:s');
        } else {}
        return parent::beforeValidate();
    }

    /**
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%login_history}}';
    }

    /**
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'user_ip',
                    'user_agent',
                    'state_id',
                    'type_id',
                    'created_on'
                ],
                'required'
            ],
            [
                [
                    'user_id',
                    'state_id',
                    'type_id'
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
                    'state_id'
                ],
                'in',
                'range' => array_keys(LoginHistory::getStateOptions())
            ],
            [
                [
                    'type_id'
                ],
                'in',
                'range' => array_keys(LoginHistory::getTypeOptions())
            ],
            [
                [
                    'user_ip',
                    'user_agent',
                    'failer_reason',
                    'code'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'user_ip',
                    'user_agent',
                    'failer_reason',
                    'code'
                ],
                'trim'
            ],
            [
                [
                    'user_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'user_id' => 'id'
                ]
            ]
        ];
    }

    /**
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'user_ip' => Yii::t('app', 'User Ip'),
            'user_agent' => Yii::t('app', 'User Agent'),
            'failer_reason' => Yii::t('app', 'Failer Reason'),
            'state_id' => Yii::t('app', 'State'),
            'type_id' => Yii::t('app', 'Type'),
            'code' => Yii::t('app', 'Code'),
            'created_on' => Yii::t('app', 'Create Time')
        ];
    }

    public static function getHasManyRelations()
    {
        $relations = [];
        return $relations;
    }

    public static function getHasOneRelations()
    {
        $relations = [];
        $relations['user_id'] = [
            'user',
            'User',
            'id'
        ];
        return $relations;
    }

    public function asJson($with_relations = false)
    {
        $json = [];
        $json['id'] = $this->id;
        $json['user_id'] = $this->user_id;
        $json['user_ip'] = $this->user_ip;
        $json['user_agent'] = $this->user_agent;
        $json['failer_reason'] = $this->failer_reason;
        $json['state_id'] = $this->state_id;
        $json['type_id'] = $this->type_id;
        $json['code'] = $this->code;
        $json['created_on'] = $this->created_on;

        return $json;
    }
    public function isAllowed()
    {
        
        if (User::isAdmin())
            return true;
            
            if ($this instanceof User)
            {
                return ($this->id == Yii::$app->user->id);
            }
            if ($this->hasAttribute('created_by_id'))
            {
                return ($this->created_by_id == Yii::$app->user->id);
            }
            
            if ($this->hasAttribute('user_id'))
            {
                return ($this->user_id == Yii::$app->user->id);
            }
            
            return false;
    }
}