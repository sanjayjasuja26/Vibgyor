<?php

/**
 * This is the model class for table "tbl_social_user".
 *
 * @property integer $id
 * @property string $social_user_id
 * @property integer $user_id
 * @property string $social_provider
 * @property string $loginProviderIdentifier
 
 * === Related data ===
 * @property User $user
 */
namespace app\modules\social\models;

use Yii;

class User extends \app\components\TActiveRecord {
	public function __toString() {
		return ( string ) $this->social_user_id;
	}
	public static function getSocialUserOptions() {
		return [ 
				"TYPE1",
				"TYPE2",
				"TYPE3" 
		];
		// return ArrayHelper::Map ( SocialUser::findActive ()->all (), 'id', 'title' );
	}
	public function getSocialUser() {
		$list = self::getSocialUserOptions ();
		return isset ( $list [$this->social_user_id] ) ? $list [$this->social_user_id] : 'Not Defined';
	}
	public static function getUserOptions() {
		return [ 
				"TYPE1",
				"TYPE2",
				"TYPE3" 
		];
		// return ArrayHelper::Map ( User::findActive ()->all (), 'id', 'title' );
	}
	public function beforeValidate() {
		if ($this->isNewRecord) {
			if (! isset ( $this->social_user_id ))
				$this->social_user_id = Yii::$app->user->id;
			if (! isset ( $this->user_id ))
				$this->user_id = Yii::$app->user->id;
		} else {
		}
		return parent::beforeValidate ();
	}
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%social_user}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'social_user_id',
								'user_id',
								'social_provider' 
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
								'social_user_id',
								'social_provider',
								'loginProviderIdentifier' 
						],
						'string',
						'max' => 255 
				]/* ,
				[ 
						[ 
								'user_id' 
						],
						'exist',
						'skipOnError' => true,
						'targetClass' => User::className (),
						'targetAttribute' => [ 
								'user_id' => 'id' 
						] 
				], */,
				[ 
						[ 
								'social_user_id',
								'social_provider',
								'loginProviderIdentifier' 
						],
						'trim' 
				] 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [ 
				'id' => Yii::t ( 'app', 'ID' ),
				'social_user_id' => Yii::t ( 'app', 'Social User' ),
				'user_id' => Yii::t ( 'app', 'User' ),
				'social_provider' => Yii::t ( 'app', 'Social Provider' ),
				'loginProviderIdentifier' => Yii::t ( 'app', 'Login Provider Identifier' ) 
		];
	}
	
	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne ( User::className (), [ 
				'id' => 'user_id' 
		] );
	}
	public static function getHasManyRelations() {
		$relations = [ ];
		return $relations;
	}
	public static function getHasOneRelations() {
		$relations = [ ];
		$relations ['user_id'] = [ 
				'user',
				'User',
				'id' 
		];
		return $relations;
	}
	public function beforeDelete() {
		return parent::beforeDelete ();
	}
	public function getUrl($action = 'view', $id=null) {
		$params = [ 
				'social/' . $this->getControllerID () . '/' . $action 
		];
		$params ['id'] = $this->id;
		// add the title parameter to the URL
		if ($this->hasAttribute ( 'title' ))
			$params ['title'] = $this->title;
		else
			$params ['title'] = ( string ) $this;
		return Yii::$app->getUrlManager ()->createAbsoluteUrl ( $params, true );
	}
	public function asJson($with_relations = false) {
		$json = [ ];
		$json ['id'] = $this->id;
		$json ['social_user_id'] = $this->social_user_id;
		$json ['user_id'] = $this->user_id;
		$json ['social_provider'] = $this->social_provider;
		$json ['loginProviderIdentifier'] = $this->loginProviderIdentifier;
		if ($with_relations) {
			// user
			$list = $this->user;
			
			if (is_array ( $list )) {
				$relationData = [ ];
				foreach ( $list as $item ) {
					$relationData [] = $item->asJson ();
				}
				$json ['user'] = $relationData;
			} else {
				$json ['User'] = $list;
			}
		}
		return $json;
	}
}
