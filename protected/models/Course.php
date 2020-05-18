<?php

 

/**
* This is the model class for table "tbl_course".
*
    * @property integer $id
    * @property string $title
    * @property string $description
    * @property integer $state_id
    * @property integer $type_id
    * @property string $created_on
    * @property string $updated_on
    * @property integer $created_by_id

* === Related data ===
    * @property Courseofferdbycollege[] $courseofferdbycolleges
    * @property User $createdBy
    * @property Parentinfo[] $parentinfos
    * @property Studentinfo[] $studentinfos
    */

namespace app\models;

use Yii;
use app\models\Courseofferdbycollege;
use app\models\User;
use app\models\Parentinfo;
use app\models\Studentinfo;

use yii\helpers\ArrayHelper;

class Course extends \app\components\SActiveRecord
{
	public  function __toString()
	{
		return (string)$this->title;
	}
	const STATE_INACTIVE 	= 0;
	const STATE_ACTIVE	 	= 1;
	const STATE_DELETED 	= 2;

	public static function getStateOptions()
	{
		return [
				self::STATE_INACTIVE		=> "New",
				self::STATE_ACTIVE 			=> "Active" ,
				self::STATE_DELETED 		=> "Archived",
		];
	}
	public function getState()
	{
		$list = self::getStateOptions();
		return isset($list [$this->state_id])?$list [$this->state_id]:'Not Defined';

	}
	public function getStateBadge()
	{
		$list = [
				self::STATE_INACTIVE 		=> "primary",
				self::STATE_ACTIVE 			=> "success" ,
				self::STATE_DELETED 		=> "danger",
		];
		return isset($list[$this->state_id])?\yii\helpers\Html::tag('span', $this->getState(), ['class' => 'label label-' . $list[$this->state_id]]):'Not Defined';
	}


		public static function getTypeOptions()
	{
		return ["TYPE1","TYPE2","TYPE3"];
		//return ArrayHelper::Map ( Type::findActive ()->all (), 'id', 'title' );

	}

	 	public function getType()
	{
		$list = self::getTypeOptions();
		return isset($list [$this->type_id])?$list [$this->type_id]:'Not Defined';

	}
				public function beforeValidate()
	{
		if($this->isNewRecord)
		{
				if ( !isset( $this->created_on )) $this->created_on = date( 'Y-m-d H:i:s');
				if ( !isset( $this->updated_on )) $this->updated_on = date( 'Y-m-d H:i:s');
				if ( !isset( $this->created_by_id )) $this->created_by_id = Yii::$app->user->id;
			}else{
					$this->updated_on = date( 'Y-m-d H:i:s');
			}
		return parent::beforeValidate();
	}


	/**
	* @inheritdoc
	*/
	public static function tableName()
	{
		return '{{%course}}';
	}

	/**
	* @inheritdoc
	*/
	public function rules()
	{
		return [
            [['title', 'description', 'created_on', 'created_by_id'], 'required'],
            [['description'], 'string'],
            [['state_id', 'type_id', 'created_by_id'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['title'], 'string', 'max' => 256],
            [['created_by_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by_id' => 'id']],
            [['title'], 'trim'],
            [['state_id'], 'in', 'range' => array_keys(self::getStateOptions())],
            [['type_id'], 'in', 'range' => array_keys (self::getTypeOptions())]
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
    public function getCourseofferdbycolleges()
    {
    	return $this->hasMany(Courseofferdbycollege::className(), ['course_id' => 'id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getCreatedBy()
    {
    	return $this->hasOne(User::className(), ['id' => 'created_by_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getParentinfos()
    {
    	return $this->hasMany(Parentinfo::className(), ['course_id' => 'id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getStudentinfos()
    {
    	return $this->hasMany(Studentinfo::className(), ['course_id' => 'id']);
    }
    public static function getHasManyRelations()
    {
    	$relations = [];
		$relations['Courseofferdbycolleges'] = ['courseofferdbycolleges','Courseofferdbycollege','id','course_id'];
		$relations['Parentinfos'] = ['parentinfos','Parentinfo','id','course_id'];
		$relations['Studentinfos'] = ['studentinfos','Studentinfo','id','course_id'];
		return $relations;
	}
    public static function getHasOneRelations()
    {
    	$relations = [];
		$relations['created_by_id'] = ['createdBy','User','id'];
		return $relations;
	}

	public function beforeDelete() {
		//Courseofferdbycollege::deleteRelatedAll(['course_id'=>$this->id]);
		//Parentinfo::deleteRelatedAll(['course_id'=>$this->id]);
		//Studentinfo::deleteRelatedAll(['course_id'=>$this->id]);
		return parent::beforeDelete ();
	}

    public function asJson($with_relations=false)
	{
		$json = [];
			$json['id'] 	= $this->id;
			$json['title'] 	= $this->title;
			$json['description'] 	= $this->description;
			$json['state_id'] 	= $this->state_id;
			$json['type_id'] 	= $this->type_id;
			$json['created_on'] 	= $this->created_on;
			$json['created_by_id'] 	= $this->created_by_id;
			if ($with_relations)
		    {
				// courseofferdbycolleges
				$list = $this->courseofferdbycolleges;

				if ( is_array($list))
				{
					$relationData = [];
					foreach( $list as $item)
					{
						$relationData [] 	= $item->asJson();
					}
					$json['courseofferdbycolleges'] 	= $relationData;
				}
				else
				{
					$json['Courseofferdbycolleges'] 	= $list;
				}
				// createdBy
				$list = $this->createdBy;

				if ( is_array($list))
				{
					$relationData = [];
					foreach( $list as $item)
					{
						$relationData [] 	= $item->asJson();
					}
					$json['createdBy'] 	= $relationData;
				}
				else
				{
					$json['CreatedBy'] 	= $list;
				}
				// parentinfos
				$list = $this->parentinfos;

				if ( is_array($list))
				{
					$relationData = [];
					foreach( $list as $item)
					{
						$relationData [] 	= $item->asJson();
					}
					$json['parentinfos'] 	= $relationData;
				}
				else
				{
					$json['Parentinfos'] 	= $list;
				}
				// studentinfos
				$list = $this->studentinfos;

				if ( is_array($list))
				{
					$relationData = [];
					foreach( $list as $item)
					{
						$relationData [] 	= $item->asJson();
					}
					$json['studentinfos'] 	= $relationData;
				}
				else
				{
					$json['Studentinfos'] 	= $list;
				}
			}
		return $json;
	}
	
	
}
