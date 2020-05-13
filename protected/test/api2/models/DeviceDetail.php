<?php

/**
 * This is the model class for table "tbl_api_device_detail".
 *
 * @property integer $id
 * @property string $device_token
 * @property string $device_name
 * @property string $device_type
 * @property integer $type_id
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by_id
 
 * === Related data ===
 * @property User $createdBy
 */
namespace app\modules\api2\models;

use app\models\User;
use Yii;
use yii\web\HttpException;

class DeviceDetail extends \app\components\TActiveRecord
{

    public function __toString()
    {
        return (string) $this->device_token;
    }

    public static function getTypeOptions()
    {
        return [
            "TYPE1",
            "TYPE2",
            "TYPE3"
        ];
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
            if (! isset($this->updated_on))
                $this->updated_on = date('Y-m-d H:i:s');
            if (! isset($this->created_by_id))
                $this->created_by_id = Yii::$app->user->id;
        } else {
            $this->updated_on = date('Y-m-d H:i:s');
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%api_device_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'device_token',
                    'device_type',
                    'created_on'
                ],
                'required'
            ],
            [
                [
                    'type_id',
                    'created_by_id'
                ],
                'integer'
            ],
            [
                [
                    'created_on',
                    'updated_on'
                ],
                'safe'
            ],
            [
                [
                    'device_token',
                    'device_name',
                    'device_type'
                ],
                'string',
                'max' => 256
            ],
            [
                [
                    'created_by_id'
                ],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => [
                    'created_by_id' => 'id'
                ]
            ],
            [
                [
                    'device_token',
                    'device_name',
                    'device_type'
                ],
                'trim'
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
            'device_token' => Yii::t('app', 'Device Token'),
            'device_name' => Yii::t('app', 'Device Name'),
            'device_type' => Yii::t('app', 'Device Type'),
            'type_id' => Yii::t('app', 'Type'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_on' => Yii::t('app', 'Updated On'),
            'created_by_id' => Yii::t('app', 'Created By')
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
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
        $relations['created_by_id'] = [
            'createdBy',
            'User',
            'id'
        ];
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
        $json['device_token'] = $this->device_token;
        $json['device_name'] = $this->device_name;
        $json['device_type'] = $this->device_type;
        $json['type_id'] = $this->type_id;
        $json['created_on'] = $this->created_on;
        $json['created_by_id'] = $this->created_by_id;
        if ($with_relations) {
            // createdBy
            $list = $this->createdBy;
            
            if (is_array($list)) {
                $relationData = [];
                foreach ($list as $item) {
                    $relationData[] = $item->asJson();
                }
                $json['createdBy'] = $relationData;
            } else {
                $json['CreatedBy'] = $list;
            }
        }
        return $json;
    }
    
    public function appData($model)
    {
        $this->deleteOldAppData(Yii::$app->user->identity->id);
        
        $deviceDetail = new DeviceDetail();
        $deviceDetail->created_by_id = Yii::$app->user->identity->id;
        $deviceDetail->device_token = $model->device_token;
        $deviceDetail->device_type = $model->device_type;
        $deviceDetail->device_name = isset($model->device_type) ? $model->device_type : '';
        if ($deviceDetail->save()) {
            return $deviceDetail;
        }
        throw new HttpException(500, Yii::t('app', 'token not save'));
    }
    
    public function deleteOldAppData($id)
    {
        DeviceDetail::deleteAll([
            'created_by_id' => $id
        ]);
        return true;
    }
}
