<?php

/**
 * This is the model class for table "tbl_payment_transaction".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $amount
 * @property string $value
 * @property integer $gateway_type
 * @property integer $payment_status
 * @property string $created_on
 */
namespace app\modules\payment\models;

use Yii;

class Transaction extends \app\components\SActiveRecord
{

    public $url;

    public function __toString()
    {
        return (string) $this->transaction_id;
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (! isset($this->created_on))
                $this->created_on = date('Y-m-d H:i:s');
        } else {}
        return parent::beforeValidate();
    }

    const PAYMENT_STATUS_NEW = 0;

    const PAYMENT_STATUS_PENDING = 1;

    const PAYMENT_STATUS_SUCCESS = 2;

    const PAYMENT_STATUS_CANCEL = 3;

    const PAYMENT_STATUS_FAIL = 4;

    public function getPaymentState()
    {
        return [
            self::PAYMENT_STATUS_NEW => \Yii::t('app', 'NEW'),
            self::PAYMENT_STATUS_PENDING => \Yii::t('app', 'PENDING'),
            self::PAYMENT_STATUS_CANCEL => \Yii::t('app', 'CANCEL'),
            self::PAYMENT_STATUS_SUCCESS => \Yii::t('app', 'SUCCESS'),
            self::PAYMENT_STATUS_FAIL => \Yii::t('app', 'FAILURE')
        ];
    }

    public function getState()
    {
        $list = $this->getPaymentState();
        return isset($list[$this->payment_status]) ? $list[$this->payment_status] : 'NEW';
    }

    public function getPaymentStateBadge()
    {
        $list = [
            self::PAYMENT_STATUS_NEW => "info",
            self::PAYMENT_STATUS_PENDING => "primary",
            self::PAYMENT_STATUS_CANCEL => "warning",
            self::PAYMENT_STATUS_SUCCESS => "success",
            self::PAYMENT_STATUS_FAIL => "danger"
        ];
        return isset($list[$this->payment_status]) ? \yii\helpers\Html::tag('span', ($this->getPaymentState()[$this->payment_status]), [
            'class' => 'label label-' . $list[$this->payment_status]
        ]) : 'Not Defined';
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment_transaction}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'amount',
                    'currency'
                ],
                'required'
            ],
            [
                [
                    'gateway_type',
                    'payment_status',
                    'to_user_id',
                    'created_by_id'
                ],
                'integer'
            ],
            [
                [
                    'created_on',
                    'description',
                    'transaction_id',
                    'value',
                    'charge_id'
                
                ],
                'safe'
            ],
            [
                [
                    'name',
                    'email',
                    'amount'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'name',
                    'email',
                    'amount'
                ],
                'trim'
            ],
            [
                [
                    'name'
                ],
                'app\components\SNameValidator'
            ],
            [
                [
                    'email'
                ],
                'email'
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
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'amount' => Yii::t('app', 'Amount'),
            'value' => Yii::t('app', 'Value'),
            'gateway_type' => Yii::t('app', 'Gateway Type'),
            'payment_status' => Yii::t('app', 'Payment Status'),
            'created_on' => Yii::t('app', 'Created On')
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

    public function asJson($with_relations = false)
    {
        $json = [];
        $json['id'] = $this->id;
        $json['name'] = $this->name;
        $json['email'] = $this->email;
        $json['amount'] = $this->amount;
        $json['value'] = $this->value;
        $json['gateway_type'] = $this->gateway_type;
        $json['payment_status'] = $this->payment_status;
        $json['created_on'] = $this->created_on;
        if ($with_relations) {}
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

    public function getPaymentResponse()
    {
        return json_decode($this->value, true);
    }
}
