<?php

/**
 * This is the model class for table "tbl_media_file".
 *
 * @property integer $id
 * @property string $title
 * @property string $file
 * @property string $size
 * @property string $extension
 * @property integer $model_id
 * @property string $model_type
 * @property integer $state_id
 * @property integer $type_id
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by_id
 * @property string $createBy
 
 * === Related data ===
 * @property User $createUser
 */
namespace app\modules\media\models;

use app\models\User;
use Yii;
use yii\web\UploadedFile;
use yii\imagine\Image;
use yii\helpers\Html;

class File extends \app\components\SActiveRecord
{

    const TYPE_IMAGE = 0;

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

    public static function getCreateUserOptions()
    {
        return [
            "TYPE1",
            "TYPE2",
            "TYPE3"
        ];
        // return ArrayHelper::Map ( CreateUser::findActive ()->all (), 'id', 'title' );
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
        return '{{%media_file}}';
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
                    'title',
                    'file',
                    'size',
                    'extension',
                    'model_type',
                    'type_id',
                    'created_on',
                    'updated_on',
                    'createBy'
                ],
                'required'
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
                    'created_on',
                    'size',
                    'model_id',
                    'updated_on'
                ],
                'safe'
            ],
            [
                [
                    'title',
                    'extension',
                    'model_type',
                    'createBy'
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
                    'title',
                    'file',
                    'size',
                    'extension',
                    'model_type',
                    'createBy'
                ],
                'trim'
            ],
            // [
            // [
            // 'file'
            // ],
            // 'file',
            // 'skipOnEmpty' => true
            // ],
            [
                [
                    'file',
                    'thumb_file'
                ],
                'file',
                'extensions' => 'jpeg, gif, png',
                'on' => [
                    'insert',
                    'update'
                ]
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

    function behaviors()
    {
        return [];
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
            'file' => Yii::t('app', 'File'),
            'size' => Yii::t('app', 'Size'),
            'extension' => Yii::t('app', 'Extension'),
            'model_id' => Yii::t('app', 'Model'),
            'model_type' => Yii::t('app', 'Model Type'),
            'state_id' => Yii::t('app', 'State'),
            'type_id' => Yii::t('app', 'Type'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_on' => Yii::t('app', 'Updated On'),
            'created_by_id' => Yii::t('app', 'Created User'),
            'createBy' => Yii::t('app', 'Create By')
        ];
    }

    public function setModelId($id)
    {
        $this->model_id = $id;
    }

    public function getModelId()
    {
        return $this->model_id;
    }

    public function setModelType($type)
    {
        $this->model_type = $type;
    }

    public function getModelType()
    {
        return $this->model_type;
    }

    public function setTypeId($type)
    {
        $this->type_id = $type;
    }

    public function getTypeId()
    {
        return $this->type_id;
    }

    public function setCreatedUser($userId)
    {
        $this->created_by_id = $userId;
    }

    public function getCreatedUser()
    {
        return $this->created_by_id;
    }

    public static function getFileType($type)
    {
        switch ($type) {
            case "image/jpeg":
                return "jpeg";
                break;
            default:
                return "jpg";
                break;
        }
    }

    public function uploadImageByFile($file, $model = null, $createUserId = null, $typeId = self::TYPE_IMAGE)
    {
        if ($file instanceof UploadedFile) {
            $extension = "jpg";
            if (isset($file->extension)) {
                $extension = $file->extension;
            } elseif (isset($file->type)) {
                $extension = self::getFileType($file->type);
            }
            
            $filename = UPLOAD_PATH . rand() . '-' . time() . '.' . $extension;
            $thumbFile = UPLOAD_THUMB_PATH . 'thumb_200*200-' . rand() . '-' . time() . '.png';
            
            if (file_exists($filename))
                unlink($filename);
            
            if (file_exists($thumbFile))
                unlink($thumbFile);
            
            $file->saveAs($filename);
            
            if (is_file($filename)) {
                Image::thumbnail($filename, 200, 200)->save($thumbFile, [
                    'quality' => 80
                ]);
                $this->thumb_file = basename($thumbFile);
            }
            
            if (! empty($model)) {
                $this->model_type = get_class($model);
                $this->model_id = $model->id;
            }
            if (empty($createUserId)) {
                $this->created_by_id = (isset(\Yii::$app->user) ? \Yii::$app->user->id : null);
                $this->createBy = (isset(\Yii::$app->user) ? \Yii::$app->user->identity->getFullName() : 'Guest');
            } else {
                $this->created_by_id = $createUserId;
                $user = User::findOne($createUserId);
                if (! empty($user)) {
                    $this->createBy = $user->getFullName();
                } else {
                    $this->createBy = 'Guest';
                }
            }
            $this->type_id = $typeId;
            $this->title = $file->name;
            $this->extension = $file->extension;
            $this->size = $file->size;
            $this->file = basename($filename);
            return $this;
        }
        return false;
    }

    public function deleteImage($id, $type = self::TYPE_IMAGE)
    {
        $file = self::find()->where([
            'id' => $id,
            'type_id' => $type
        ])->one();
        
        if (! empty($file)) {
            $filename = UPLOAD_PATH . $file->file;
            if (is_file($filename))
                unlink($filename);
            $thumb = UPLOAD_THUMB_PATH . $file->thumb_file;
            if (is_file($thumb))
                unlink($thumb);
        }
        
        return true;
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
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
            'createUser',
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
        $json['title'] = $this->title;
        $json['file'] = $this->file;
        $json['size'] = $this->size;
        $json['extension'] = $this->extension;
        $json['model_id'] = $this->model_id;
        $json['model_type'] = $this->model_type;
        $json['state_id'] = $this->state_id;
        $json['type_id'] = $this->type_id;
        $json['created_on'] = $this->created_on;
        $json['created_by_id'] = $this->created_by_id;
        $json['createBy'] = $this->createBy;
        if ($with_relations) {
            // createUser
            $list = $this->createUser;
            
            if (is_array($list)) {
                $relationData = [];
                foreach ($list as $item) {
                    $relationData[] = $item->asJson();
                }
                $json['createUser'] = $relationData;
            } else {
                $json['CreateUser'] = $list;
            }
        }
        return $json;
    }

    public function getUrl($action = 'view', $id = null)
    {
        $params = [
            '/media/' . $this->getControllerID() . '/' . $action
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
        return Yii::$app->geSUrlManager()->createAbsoluteUrl($params, true);
    }

    public static function findImage($model, $all = false)
    {
        if ($all) {
            return self::findAll([
                'model_id' => $model->id,
                'model_type' => $model::className()
            ]);
        } else {
            return self::findOne([
                'model_id' => $model->id,
                'model_type' => $model::className()
            ]);
        }
    }

    public static function getImage($model, $options = [], $thumb = true, $defaultImg = 'default.png')
    {
        $opt = [
            'class' => 'img-responsive'
        ];
        $arr = [];
        if (! empty($options)) {
            $arr = $options;
        }
        
        $file = self::findImage($model);
        
        if (! empty($file) && ! empty($file->file)) {
            return Html::img([
                '/media/file/image',
                'id' => $file->id,
                'thumb' => $thumb
            ], $arr);
        } else {
            return Html::img(\Yii::$app->view->theme->getUrl('/img/') . $defaultImg, $arr);
        }
    }
}
