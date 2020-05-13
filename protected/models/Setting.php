<?php

/**
 * This is the model class for table "tbl_setting".
 *
 * @property integer $id
 * @property string $key
 * @property string $title
 * @property string $value
 * @property string $type_id
 * @property integer $state_id
 * @property integer $created_by_id
 */
namespace app\models;

use Yii;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\base\InvalidConfigException;

class Setting extends \app\components\TActiveRecord
{

    public function __toString()
    {
        return (string) $this->title;
    }

    public $keyName;

    public $keyType;

    public $keyValue;

    public $keyRequired;

    const KEY_TYPE_STRING = 0;

    const KEY_TYPE_BOOL = 1;

    const KEY_TYPE_INT = 2;

    const KEY_TYPE_EMAIL = 3;

    const KEY_TYPE_TIME = 4;

    const KEY_TYPE_DATE = 5;

    const KEY_TYPE_TEXT = 6;

    public static function getDefaultConfig()
    {
        return [
            'appConfig' => [
                'title' => Yii::t('app', 'App Configration'),
                'value' => [
                    'companyUrl' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => 'https://www.toxsl.com',
                        'required' => true
                    ],
                    'company' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => 'ToXSL Technologies',
                        'required' => true
                    ],
                    'companyEmail' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => 'admin@toxsl.in',
                        'required' => true
                    ],
                    'companyContactEmail' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => 'admin@toxsl.in',
                        'required' => false
                    ],
                    'companyContactNo' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => '9569127788',
                        'required' => false
                    ],
                    'companyAddress' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => 'C-127, 2nd floor, Phase 8, Industrial Area, Sector 72, Mohali, Punjab',
                        'required' => false
                    ],
                    'loginCount' => [
                        'type' => self::KEY_TYPE_INT,
                        'value' => 2,
                        'required' => false
                    ]
                ]
            ],
            'smtp' => [
                'title' => Yii::t('app', 'SMTP Configration'),
                'value' => [
                    'host' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => '',
                        'required' => true
                    ],
                    'username' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => '',
                        'required' => true
                    ],
                    'password' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => '',
                        'required' => true
                    ],
                    'port' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => '',
                        'required' => true
                    ],
                    'encryption' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => '',
                        'required' => false
                    ]
                ]
            ],
            'firebaseSettings' => [
                'title' => Yii::t('app', 'Firebase Configration'),
                'value' => [
                    'authKey' => [
                        'type' => self::KEY_TYPE_STRING,
                        'value' => '',
                        'required' => true
                    ],
                
                ]
            ]
        ];
    }

    public static function getDefault($key)
    {
        $setting = self::getDefaultConfig();
        if (isset($setting[$key])) {
            return $setting[$key];
        } else {
            throw new InvalidConfigException("$key invalid Configartion.");
        }
    }

    public static function getTypeOptions()
    {
        return [
            self::KEY_TYPE_STRING => 'String',
            self::KEY_TYPE_BOOL => 'Boolean',
            self::KEY_TYPE_INT => 'Integer',
            self::KEY_TYPE_EMAIL => 'Email',
            self::KEY_TYPE_TIME => 'Time',
            self::KEY_TYPE_DATE => 'Date',
            self::KEY_TYPE_TEXT => 'Text'
        ];
    }

    public function getType()
    {
        $list = self::getTypeOptions();
        return isset($list[$this->type_id]) ? $list[$this->type_id] : 'Not Defined';
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

    public static function getCreatedByOptions()
    {
        return [
            "TYPE1",
            "TYPE2",
            "TYPE3"
        ];
        // return ArrayHelper::Map ( CreatedBy::findActive ()->all (), 'id', 'title' );
    }

    public function getCreatedBy()
    {
        $list = self::getCreatedByOptions();
        return isset($list[$this->created_by_id]) ? $list[$this->created_by_id] : 'Not Defined';
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (! isset($this->created_by_id))
                $this->created_by_id = Yii::$app->user->id;
        } else {}
        return parent::beforeValidate();
    }

    /**
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
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
                    'key',
                    'title'
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
                    'state_id',
                    'type_id',
                    'created_by_id'
                ],
                'integer'
            ],
            [
                [
                    'key',
                    'title'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'key',
                    'title'
                ],
                'trim'
            ],
            [
                [
                    'value',
                    'keyName',
                    'keyType',
                    'keyRequired',
                    'keyValue'
                ],
                'safe'
            ],
            [
                [
                    'type_id'
                ],
                'in',
                'range' => array_keys(self::getTypeOptions())
            ],
            [
                [
                    'state_id'
                ],
                'in',
                'range' => array_keys(self::getStateOptions())
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
            'key' => Yii::t('app', 'Key'),
            'title' => Yii::t('app', 'Title'),
            'value' => Yii::t('app', 'Value'),
            'type_id' => Yii::t('app', 'Type'),
            'state_id' => Yii::t('app', 'State'),
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

    public function asJson($with_relations = false)
    {
        $json = [];
        $json['id'] = $this->id;
        $json['key'] = $this->key;
        $json['title'] = $this->title;
        $json['value'] = $this->value;
        $json['type_id'] = $this->type_id;
        $json['state_id'] = $this->state_id;
        $json['created_by_id'] = $this->created_by_id;
        if ($with_relations) {}
        return $json;
    }

    public static function generateField($key, $field)
    {
        $html = "";
        if (is_array($field)) {
            $required = (isset($field['required']) && ($field['required'] != false)) ? "required" : '';
            $value = isset($field['value']) ? $field['value'] : '';
            $typeRequired = (! empty($required)) ? true : false;
            if (isset($field['type'])) {
                switch ($field['type']) {
                    case self::KEY_TYPE_BOOL:
                        $checked = ($value) ? "checked = checked" : '';
                        $html .= "<input type='checkbox' $checked " . $required . " value='" . $value . "' class='form-control' name='Setting[keyValue][" . $key . "][value]'>";

                        $html .= "<input type='hidden' value='" . self::KEY_TYPE_BOOL . "' name='Setting[keyValue][" . $key . "][type]'>";
                        $html .= "<input type='hidden' value='" . $typeRequired . "' name='Setting[keyValue][" . $key . "][required]'>";

                        break;
                    case self::KEY_TYPE_STRING:
                        $html .= "<input type='text' " . $required . " value='" . $value . "' class='form-control' name='Setting[keyValue][" . $key . "][value]' placeholder='" . Inflector::titleize($key) . "'>";

                        $html .= "<input type='hidden' value='" . self::KEY_TYPE_STRING . "' name='Setting[keyValue][" . $key . "][type]'>";
                        $html .= "<input type='hidden' value='" . $typeRequired . "' name='Setting[keyValue][" . $key . "][required]'>";

                        break;
                    case self::KEY_TYPE_INT:
                        $html .= "<input type='number' " . $required . " value='" . $value . "' class='form-control' name='Setting[keyValue][" . $key . "][value]' placeholder='" . Inflector::titleize($key) . "'>";

                        $html .= "<input type='hidden' value='" . self::KEY_TYPE_INT . "' name='Setting[keyValue][" . $key . "][type]'>";
                        $html .= "<input type='hidden' value='" . $typeRequired . "' name='Setting[keyValue][" . $key . "][required]'>";

                        break;
                    case self::KEY_TYPE_EMAIL:
                        $html .= "<input type='email' " . $required . " value='" . $value . "' name='Setting[keyValue][" . $key . "][value]' class='form-control' placeholder='" . Inflector::titleize($key) . "'>";

                        $html .= "<input type='hidden' value='" . self::KEY_TYPE_EMAIL . "' name='Setting[keyValue][" . $key . "][type]'>";
                        $html .= "<input type='hidden' value='" . $typeRequired . "' name='Setting[keyValue][" . $key . "][required]'>";

                        break;

                    case self::KEY_TYPE_DATE:

                        $html .= "<input type='date' " . $required . " value='" . $value . "' name='Setting[keyValue][" . $key . "][value]' class='form-control' placeholder='" . Inflector::titleize($key) . "'>";

                        $html .= "<input type='hidden' value='" . self::KEY_TYPE_DATE . "' name='Setting[keyValue][" . $key . "][type]'>";
                        $html .= "<input type='hidden' value='" . $typeRequired . "' name='Setting[keyValue][" . $key . "][required]'>";

                        break;

                    case self::KEY_TYPE_TIME:

                        $html .= "<input type='time' " . $required . " value='" . $value . "' name='Setting[keyValue][" . $key . "][value]' class='form-control' placeholder='" . Inflector::titleize($key) . "'>";

                        $html .= "<input type='hidden' value='" . self::KEY_TYPE_TIME . "' name='Setting[keyValue][" . $key . "][type]'>";
                        $html .= "<input type='hidden' value='" . $typeRequired . "' name='Setting[keyValue][" . $key . "][required]'>";

                        break;

                    case self::KEY_TYPE_TEXT:

                        $html .= "<textarea rows='4' cols='50' {$required} name='Setting[keyValue][{$key}][value]' class='form-control'>{$value}</textarea>";

                        $html .= "<input type='hidden' value='" . self::KEY_TYPE_TEXT . "' name='Setting[keyValue][{$key}][type]'>";
                        $html .= "<input type='hidden' value='{$typeRequired}' name='Setting[keyValue][{$key}][required]'>";

                        break;

                    default:
                        $html .= "<input type='text' " . $required . " value='" . $value . "' name='Setting[keyValue][" . $key . "][value]' class='form-control' placeholder='" . Inflector::titleize($key) . "'>";

                        $html .= "<input type='hidden' value='" . self::KEY_TYPE_STRING . "' name='Setting[keyValue][" . $key . "][type]'>";
                        $html .= "<input type='hidden' value='" . $typeRequired . "' name='Setting[keyValue][" . $key . "][required]'>";

                        break;
                }
            } else {
                $html .= "<input type='text' " . $required . " value='" . $value . "' name='Setting[keyValue][" . $key . "][value]' class='form-control' placeholder='" . Inflector::titleize($key) . "'>";

                $html .= "<input type='hidden' value='" . self::KEY_TYPE_STRING . "' name='Setting[keyValue][" . $key . "][type]'>";
                $html .= "<input type='hidden' value='" . $typeRequired . "' name='Setting[keyValue][" . $key . "][required]'>";
            }
        } else {
            $html .= "<input type='text' class='form-control' name='Setting[keyValue][" . $field . "][value]' placeholder='" . Inflector::titleize($field) . "'>";

            $html .= "<input type='hidden' value='" . self::KEY_TYPE_STRING . "' name='Setting[keyValue][" . $key . "][type]'>";
            $html .= "<input type='hidden' value='" . false . "' name='Setting[keyValue][" . $key . "][required]'>";
        }
        return $html;
    }

    public static function setDefaultConfig()
    {
        $data = self::getDefaultConfig();
        foreach ($data as $key => $value) {
            $model = self::findOne([
                'key' => $key
            ]);
            if (empty($model)) {
                $model = new self();
                $model->scenario = "default";
                $model->key = $key;
                $model->title = $value['title'];
                $model->value = Json::encode($value['value']);
            } else {
                $save = json_decode($model->value, true);
                $static = $value['value'];
                foreach ($save as $key => $value) {
                    if (! array_key_exists($key, $static)) {
                        unset($save[$key]);
                    }
                }
                foreach ($static as $key => $value) {
                    if (! array_key_exists($key, $save)) {
                        $save[$key] = $value;
                    }
                }
                $model->value = Json::encode($save);
            }

            if (! $model->save()) {
                \Yii::$app->session->setFlash('error', "Error! " . $model->errors);
            }
        }
    }

    public static function checkKeyType($type, $value)
    {
        switch ($type) {
            case self::KEY_TYPE_BOOL:
                if (! empty($value)) {
                    $val = \yii\helpers\Html::tag('span', 'ON', [
                        'class' => 'label label-success'
                    ]);
                } else {
                    $val = \yii\helpers\Html::tag('span', 'OFF', [
                        'class' => 'label label-danger'
                    ]);
                }
                break;
            default:
                $val = $value;
                break;
        }
        return $val;
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