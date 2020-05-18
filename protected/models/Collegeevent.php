<?php

 

/**
* This is the model class for table "tbl_college_event".
*
    * @property integer $id
    * @property string $title
    * @property string $description
    * @property string $start_on
    * @property string $end_on
    * @property integer $college_id
    * @property integer $state_id
    * @property integer $type_id
    * @property string $created_on
    * @property string $updated_on
    * @property integer $created_by_id

* === Related data ===
    * @property User $college
    * @property User $createdBy
    */

namespace app\models;

use Yii;
use app\models\User;

use yii\helpers\ArrayHelper;

class Collegeevent extends \app\components\SActiveRecord
{
	public  function __toString()
	{
		return (string)$this->title;
	}
public static function getCollegeOptions()
	{
		return ["TYPE1","TYPE2","TYPE3"];
		//return ArrayHelper::Map ( College::findActive ()->all (), 'id', 'title' );

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
		return '{{%college_event}}';
	}

	/**
	* @inheritdoc
	*/
	public function rules()
	{
		return [
            [['title', 'start_on', 'end_on', 'college_id', 'state_id', 'created_on', 'created_by_id'], 'required'],
            [['start_on', 'end_on', 'created_on', 'updated_on'], 'safe'],
            [['college_id', 'state_id', 'type_id', 'created_by_id'], 'integer'],
            [['title', 'description'], 'string', 'max' => 255],
            [['college_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['college_id' => 'id']],
            [['created_by_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by_id' => 'id']],
            [['title', 'description'], 'trim'],
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
				    'start_on' => Yii::t('app', 'Start On'),
				    'end_on' => Yii::t('app', 'End On'),
				    'college_id' => Yii::t('app', 'College'),
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
    public function getCollege()
    {
    	return $this->hasOne(User::className(), ['id' => 'college_id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getCreatedBy()
    {
    	return $this->hasOne(User::className(), ['id' => 'created_by_id']);
    }
    public static function getHasManyRelations()
    {
    	$relations = [];
		return $relations;
	}
    public static function getHasOneRelations()
    {
    	$relations = [];
		$relations['college_id'] = ['college','User','id'];
		$relations['created_by_id'] = ['createdBy','User','id'];
		return $relations;
	}

	public function beforeDelete() {
		return parent::beforeDelete ();
	}

    public function asJson($with_relations=false)
	{
		$json = [];
			$json['id'] 	= $this->id;
			$json['title'] 	= $this->title;
			$json['description'] 	= $this->description;
			$json['start_on'] 	= $this->start_on;
			$json['end_on'] 	= $this->end_on;
			$json['college_id'] 	= $this->college_id;
			$json['state_id'] 	= $this->state_id;
			$json['type_id'] 	= $this->type_id;
			$json['created_on'] 	= $this->created_on;
			$json['created_by_id'] 	= $this->created_by_id;
			if ($with_relations)
		    {
				// college
				$list = $this->college;

				if ( is_array($list))
				{
					$relationData = [];
					foreach( $list as $item)
					{
						$relationData [] 	= $item->asJson();
					}
					$json['college'] 	= $relationData;
				}
				else
				{
					$json['College'] 	= $list;
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
			}
		return $json;
	}
	
	
}
