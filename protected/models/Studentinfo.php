<?php

/**
 * This is the model class for table "tbl_student_info".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $course_id
 * @property string $f_name
 * @property string $m_name
 * @property integer $caste
 * @property integer $current_studies
 * @property integer $state_id
 * @property integer $type_id
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by_id

 * === Related data ===
 * @property Course $course
 * @property User $createdBy
 * @property User $user
 */

namespace app\models;

use Yii;
use app\models\Course;
use app\models\User;
use yii\helpers\ArrayHelper;

class Studentinfo extends \app\components\SActiveRecord {

    const SCENARIO_ADD = 'add';
    const SCENARIO_UPDATE = 'update';

    public function __toString() {
        return (string) $this->user_id;
    }

    public static function getUserOptions() {
        return ["TYPE1", "TYPE2", "TYPE3"];
        //return ArrayHelper::Map ( User::findActive ()->all (), 'id', 'title' );
    }

    public static function getCourseOptions() {
        return ["TYPE1", "TYPE2", "TYPE3"];
        //return ArrayHelper::Map ( Course::findActive ()->all (), 'id', 'title' );
    }

    const STATE_INACTIVE = 0;
    const STATE_ACTIVE = 1;
    const STATE_DELETED = 2;

    public static function getStateOptions() {
        return [
            self::STATE_INACTIVE => "New",
            self::STATE_ACTIVE => "Active",
            self::STATE_DELETED => "Archived",
        ];
    }

    public function getState() {
        $list = self::getStateOptions();
        return isset($list [$this->state_id]) ? $list [$this->state_id] : 'Not Defined';
    }

    public function getStateBadge() {
        $list = [
            self::STATE_INACTIVE => "primary",
            self::STATE_ACTIVE => "success",
            self::STATE_DELETED => "danger",
        ];
        return isset($list[$this->state_id]) ? \yii\helpers\Html::tag('span', $this->getState(), ['class' => 'label label-' . $list[$this->state_id]]) : 'Not Defined';
    }

    public static function getTypeOptions() {
        return ["TYPE1", "TYPE2", "TYPE3"];
        //return ArrayHelper::Map ( Type::findActive ()->all (), 'id', 'title' );
    }

    public function getType() {
        $list = self::getTypeOptions();
        return isset($list [$this->type_id]) ? $list [$this->type_id] : 'Not Defined';
    }

    public function beforeValidate() {
        if ($this->isNewRecord) {
            if (!isset($this->user_id))
                $this->user_id = Yii::$app->user->id;
            if (!isset($this->created_on))
                $this->created_on = date('Y-m-d H:i:s');
            if (!isset($this->updated_on))
                $this->updated_on = date('Y-m-d H:i:s');
            if (!isset($this->created_by_id))
                $this->created_by_id = Yii::$app->user->id;
        } else {
            $this->updated_on = date('Y-m-d H:i:s');
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%student_info}}';
    }

    public function scenarios() {
        $scenarios = parent::scenarios();

        $scenarios['add'] = [
            'user_id',
            'state_id',
            'created_on',
            'created_by_id'
        ];

        $scenarios['update'] = [
            'course_id',
            'f_name',
            'm_name',
            'caste',
            'current_studies',
            'state_id',
            'created_on',
            'created_by_id'
        ];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'user_id',
                    'state_id',
                    'created_on',
                    'created_by_id'
                ],
                'required',
                'on' => 'add'
            ],
            [
                [
                    'course_id',
                    'f_name',
                    'm_name',
                    'caste',
                    'current_studies',
                    'state_id',
                    'created_by_id'
                ],
                'required',
                'on' => 'update'
            ],
            [['user_id', 'course_id', 'caste', 'current_studies', 'state_id', 'type_id', 'created_by_id'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['f_name', 'm_name'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['created_by_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['f_name', 'm_name'], 'trim'],
            [['f_name'], 'app\components\SNameValidator'],
            [['m_name'], 'app\components\SNameValidator'],
            [['state_id'], 'in', 'range' => array_keys(self::getStateOptions())],
            [['type_id'], 'in', 'range' => array_keys(self::getTypeOptions())]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'course_id' => Yii::t('app', 'Course'),
            'f_name' => Yii::t('app', 'F Name'),
            'm_name' => Yii::t('app', 'M Name'),
            'caste' => Yii::t('app', 'Caste'),
            'current_studies' => Yii::t('app', 'Current Studies'),
            'state_id' => Yii::t('app', 'State'),
            'type_id' => Yii::t('app', 'Type'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_on' => Yii::t('app', 'Updated On'),
            'created_by_id' => Yii::t('app', 'Created By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse() {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function getHasManyRelations() {
        $relations = [];
        return $relations;
    }

    public static function getHasOneRelations() {
        $relations = [];
        $relations['course_id'] = ['course', 'Course', 'id'];
        $relations['created_by_id'] = ['createdBy', 'User', 'id'];
        $relations['user_id'] = ['user', 'User', 'id'];
        return $relations;
    }

    public function beforeDelete() {
        return parent::beforeDelete();
    }

    public function asJson($with_relations = false) {
        $json = [];
        $json['id'] = $this->id;
        $json['user_id'] = $this->user_id;
        $json['course_id'] = $this->course_id;
        $json['f_name'] = $this->f_name;
        $json['m_name'] = $this->m_name;
        $json['caste'] = $this->caste;
        $json['current_studies'] = $this->current_studies;
        $json['state_id'] = $this->state_id;
        $json['type_id'] = $this->type_id;
        $json['created_on'] = $this->created_on;
        $json['created_by_id'] = $this->created_by_id;
        if ($with_relations) {
            // course
            $list = $this->course;

            if (is_array($list)) {
                $relationData = [];
                foreach ($list as $item) {
                    $relationData [] = $item->asJson();
                }
                $json['course'] = $relationData;
            } else {
                $json['Course'] = $list;
            }
            // createdBy
            $list = $this->createdBy;

            if (is_array($list)) {
                $relationData = [];
                foreach ($list as $item) {
                    $relationData [] = $item->asJson();
                }
                $json['createdBy'] = $relationData;
            } else {
                $json['CreatedBy'] = $list;
            }
            // user
            $list = $this->user;

            if (is_array($list)) {
                $relationData = [];
                foreach ($list as $item) {
                    $relationData [] = $item->asJson();
                }
                $json['user'] = $relationData;
            } else {
                $json['User'] = $list;
            }
        }
        return $json;
    }

}
