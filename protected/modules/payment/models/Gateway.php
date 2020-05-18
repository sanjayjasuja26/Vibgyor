<?php

/**
 * This is the model class for table "tbl_payment_gateway".
 *
 * @property integer $id
 * @property string $title
 * @property string $value
 * @property integer $mode
 * @property integer $state_id
 * @property integer $type_id
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by_id
 */
namespace app\modules\payment\models;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;

class Gateway extends \app\components\SActiveRecord
{

    public function __toString()
    {
        return (string) $this->title;
    }

    public static function gatewayFormFields()
    {
        return GatewaySetting::gatewayFormFields();
    }

    public static function gatewayForm($type)
    {
        $list = self::gatewayFormFields();
        return isset($list[$type]) ? $list[$type] : [];
    }

    public static function getTypeOptions()
    {
        return GatewaySetting::getGatewayOptions();
    }

    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    public static function getStateOptions()
    {
        return [
            self::STATE_INACTIVE => \Yii::t('app', 'InActive'),
            self::STATE_ACTIVE => \Yii::t('app', 'Active')
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
            self::STATE_ACTIVE => "success"
        ];
        return isset($list[$this->state_id]) ? \yii\helpers\Html::tag('span', $this->getState(), [
            'class' => 'label label-' . $list[$this->state_id]
        ]) : 'Not Defined';
    }

    public function getType()
    {
        $list = self::getTypeOptions();
        return isset($list[$this->type_id]) ? $list[$this->type_id] : 'Not Defined';
    }

    public function getModeOptions()
    {
        return GatewaySetting::getModeOptions();
    }

    public function getMode()
    {
        $list = self::getModeOptions();
        return isset($list[$this->mode]) ? $list[$this->mode] : 'Not Defined';
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
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment_gateway}}';
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
                    'mode',
                    'type_id',
                    'title',
                    'created_by_id'
                ],
                'required'
            ],
            [
                [
                    'value'
                ],
                'string'
            ],
            [
                [
                    'mode',
                    'state_id',
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
                    'title'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'title'
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
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'value' => Yii::t('app', 'Value'),
            'mode' => Yii::t('app', 'Mode'),
            'state_id' => Yii::t('app', 'State'),
            'type_id' => Yii::t('app', 'Payment Gateway'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_on' => Yii::t('app', 'Updated On'),
            'created_by_id' => Yii::t('app', 'Created By')
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
        return $relations;
    }

    public function beforeDelete()
    {
        return parent::beforeDelete();
    }

    public function asJson()
    {
        $json = [];
        $json['id'] = $this->id;
        $json['title'] = $this->title;
        $json['value'] = Json::decode($this->value);
        $json['mode'] = $this->mode;
        $json['state_id'] = $this->state_id;
        $json['type_id'] = $this->type_id;
        $json['created_on'] = $this->created_on;
        $json['created_by_id'] = $this->created_by_id;
        return $json;
    }

    public function getUrl($action = 'view', $id = NULL)
    {
        $params = [
            'payment/' . $this->geSControllerID() . '/' . $action
        ];
        $params['id'] = $this->id;
        
        // add the title parameter to the URL
        if ($this->hasAttribute('title'))
            $params['title'] = $this->title;
        else
            $params['title'] = (string) $this;
        
        return Yii::$app->geSUrlManager()->createAbsoluteUrl($params, true);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->state_id == self::STATE_ACTIVE) {
            $findOldSettings = self::find()->where([
                'type_id' => $this->type_id
            ])
                ->andWhere([
                'not in',
                'id',
                $this->id
            ])
                ->all();
            
            if ($findOldSettings) {
                foreach ($findOldSettings as $oldSetting) {
                    $oldSetting->state_id = self::STATE_INACTIVE;
                    $oldSetting->save(false, [
                        'state_id'
                    ]);
                }
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public static function activeGatways()
    {
        return self::find()->where([
            'state_id' => self::STATE_ACTIVE
        ])->all();
    }

    public static function gateway($key)
    {
        $gateway = self::find()->where([
            'state_id' => self::STATE_ACTIVE,
            'type_id' => $key
        ])->one();
        if ($gateway) {
            return Json::decode($gateway->value, false);
        } else {
            throw new \Exception("$key Gateway setting not found. Please add or activate $key Gateway setting");
        }
    }

    public function gatewaySettings()
    {
        return Json::decode($this->value);
    }

    public function payNowUrl()
    {
        return Url::to('payment/' . strtolower($this->getType()) . '/paynow');
    }
}