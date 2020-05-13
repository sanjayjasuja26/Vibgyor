<?php

/**
 * This is the model class for table "ha_logins".
 *
 * @property integer $id
 * @property string $userId
 * @property string $loginProvider
 * @property string $loginProviderIdentifier
 * @property integer $user_id

 * === Related data ===
 * @property User $user
 */
namespace app\models;

use Yii;

class Halogins extends \app\components\TActiveRecord
{

    public function __toString()
    {
        return (string) $this->userId;
    }

    public static function getUserOptions()
    {
        return [
            "TYPE1",
            "TYPE2",
            "TYPE3"
        ];
        // return ArrayHelper::Map ( User::findActive ()->all (), 'id', 'title' );
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (! isset($this->user_id))
                $this->user_id = Yii::$app->user->id;
        } else {}
        return parent::beforeValidate();
    }

    /**
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ha_logins';
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
                    'userId',
                    'loginProvider',
                    'loginProviderIdentifier'
                ],
                'required'
            ],
            [
                [
                    'user_id'
                ],
                'integer'
            ],
            [
                [
                    'userId',
                    'loginProvider'
                ],
                'string',
                'max' => 50
            ],
            [
                [
                    'loginProviderIdentifier'
                ],
                'string',
                'max' => 100
            ],
            [
                [
                    'loginProvider',
                    'loginProviderIdentifier'
                ],
                'unique',
                'targetAttribute' => [
                    'loginProvider',
                    'loginProviderIdentifier'
                ]
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
            ],
            [
                [
                    'userId',
                    'loginProvider',
                    'loginProviderIdentifier'
                ],
                'trim'
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
            'userId' => Yii::t('app', 'User'),
            'loginProvider' => Yii::t('app', 'Login Provider'),
            'loginProviderIdentifier' => Yii::t('app', 'Login Provider Identifier'),
            'user_id' => Yii::t('app', 'User')
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

    public function beforeDelete()
    {
        return parent::beforeDelete();
    }

    public function asJson($with_relations = false)
    {
        $json = [];
        $json['id'] = $this->id;
        $json['userId'] = $this->userId;
        $json['loginProvider'] = $this->loginProvider;
        $json['loginProviderIdentifier'] = $this->loginProviderIdentifier;
        $json['user_id'] = $this->user_id;
        if ($with_relations) {
            // user
            $list = $this->user;

            if (is_array($list)) {
                $relationData = [];
                foreach ($list as $item) {
                    $relationData[] = $item->asJson();
                }
                $json['user'] = $relationData;
            } else {
                $json['User'] = $list;
            }
        }
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
