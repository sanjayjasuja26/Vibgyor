<?php

/**
 * This is the model class for table "tbl_social_provider".
 *
 * @property integer $id
 * @property string $title
 * @property string $provider_type
 * @property string $client_id
 * @property string $client_secret_key
 * @property integer $state_id
 * @property integer $type_id
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by_id
 */
namespace app\modules\social\models;

use Yii;
use yii\components;

class Provider extends \app\components\TActiveRecord {
	const PROVIDER_GOOGLE = 1;
	const PROVIDER_FACEBOOK = 2;
	const PROVIDER_GITHUB = 3;
	const PROVIDER_GOOGLEHYBRID = 4;
	const PROVIDER_LINKEDIN = 5;
	const PROVIDER_LIVE = 6;
	const PROVIDER_TWITTER = 7;
	const PROVIDER_VKONTAKTE = 8;
	const PROVIDER_YANDEX = 9;
	public function __toString() {
		return ( string ) $this->title;
	}
	public static function getClientClasses() {
		return [ 
				self::PROVIDER_GOOGLE => 'Google',
				self::PROVIDER_GOOGLEHYBRID => 'GoogleHybrid',
				self::PROVIDER_FACEBOOK => 'Facebook',
				self::PROVIDER_GITHUB => 'GitHub',
				self::PROVIDER_LINKEDIN => 'LinkedIn',
				self::PROVIDER_LIVE => 'Live',
				self::PROVIDER_TWITTER => 'Twitter',
				self::PROVIDER_VKONTAKTE => 'VKontakte',
				self::PROVIDER_YANDEX => 'Yandex' 
		];
		// return ArrayHelper::Map ( Client::findActive ()->all (), 'id', 'title' );
	}
	public static function getClientOptions() {
		return [ 
				self::PROVIDER_FACEBOOK => \Yii::t ( 'app', 'Facebook' ),
				self::PROVIDER_GOOGLE => \Yii::t ( 'app', 'Google' ),
				self::PROVIDER_GITHUB => \Yii::t ( 'app', 'GitHub' ),
				self::PROVIDER_LINKEDIN => \Yii::t ( 'app', 'LinkedIn' ),
				self::PROVIDER_LIVE => \Yii::t ( 'app', 'Microsoft(Live)' ),
				self::PROVIDER_TWITTER => \Yii::t ( 'app', 'Twitter' ),
				self::PROVIDER_GOOGLEHYBRID => \Yii::t ( 'app', 'GoogleHybrid' ) 
		];
		// return ArrayHelper::Map ( Client::findActive ()->all (), 'id', 'title' );
	}
	public function getClient() {
		$list = self::getClientOptions ();
		return isset ( $list [$this->provider_type] ) ? $list [$this->provider_type] : 'Not Defined';
	}
	const STATE_INACTIVE = 0;
	const STATE_ACTIVE = 1;
	public static function getStateOptions() {
		return [ 
				self::STATE_INACTIVE => "InActive",
				self::STATE_ACTIVE => "Active" 
		];
	}
	public function getState() {
		$list = self::getStateOptions ();
		return isset ( $list [$this->state_id] ) ? $list [$this->state_id] : 'Not Defined';
	}
	public function getStateBadge() {
		$list = [ 
				self::STATE_ACTIVE => "success",
				self::STATE_INACTIVE => "danger" 
		];
		return isset ( $list [$this->state_id] ) ? \yii\helpers\Html::tag ( 'span', $this->getState (), [ 
				'class' => 'label label-' . $list [$this->state_id] 
		] ) : 'Not Defined';
	}
	public static function getTypeOptions() {
		return [ 
				"TYPE1",
				"TYPE2",
				"TYPE3" 
		];
		// return ArrayHelper::Map ( Type::findActive ()->all (), 'id', 'title' );
	}
	public function getType() {
		$list = self::getTypeOptions ();
		return isset ( $list [$this->type_id] ) ? $list [$this->type_id] : 'Not Defined';
	}
	public static function getCreatedByOptions() {
		return [ 
				"TYPE1",
				"TYPE2",
				"TYPE3" 
		];
		// return ArrayHelper::Map ( CreatedBy::findActive ()->all (), 'id', 'title' );
	}
	public function getCreatedBy() {
		$list = self::getCreatedByOptions ();
		return isset ( $list [$this->created_by_id] ) ? $list [$this->created_by_id] : 'Not Defined';
	}
	public function beforeValidate() {
		if ($this->isNewRecord) {
			if (! isset ( $this->created_on ))
				$this->created_on = date ( 'Y-m-d H:i:s' );
			if (! isset ( $this->updated_on ))
				$this->updated_on = date ( 'Y-m-d H:i:s' );
			if (! isset ( $this->created_by_id ))
				$this->created_by_id = Yii::$app->user->id;
		} else {
			$this->updated_on = date ( 'Y-m-d H:i:s' );
		}
		return parent::beforeValidate ();
	}
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%social_provider}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'title',
								'provider_type',
								'client_id',
								'client_secret_key',
								'created_by_id' 
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
								'updated_on' 
						],
						'safe' 
				],
				[ 
						[ 
								'title',
								'provider_type',
								'client_id' 
						],
						'string',
						'max' => 256 
				],
				[ 
						[ 
								'client_secret_key' 
						],
						'string',
						'max' => 255 
				],
				[ 
						[ 
								'title',
								'provider_type',
								'client_id',
								'client_secret_key' 
						],
						'trim' 
				],
				[ 
						[ 
								'state_id' 
						],
						'in',
						'range' => array_keys ( self::getStateOptions () ) 
				] 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [ 
				'id' => Yii::t ( 'app', 'ID' ),
				'title' => Yii::t ( 'app', 'Title' ),
				'provider_type' => Yii::t ( 'app', 'Provider' ),
				'client_id' => Yii::t ( 'app', 'Client Id' ),
				'client_secret_key' => Yii::t ( 'app', 'Client Secret Key' ),
				'state_id' => Yii::t ( 'app', 'State' ),
				'type_id' => Yii::t ( 'app', 'Type' ),
				'created_on' => Yii::t ( 'app', 'Created On' ),
				'updated_on' => Yii::t ( 'app', 'Updated On' ),
				'created_by_id' => Yii::t ( 'app', 'Created By' ) 
		];
	}
	public static function getHasManyRelations() {
		$relations = [ ];
		return $relations;
	}
	public static function getHasOneRelations() {
		$relations = [ ];
		return $relations;
	}
	public function beforeDelete() {
		return parent::beforeDelete ();
	}
	public function getDbClients() {
		return Provider::findAll ( [ 
				'state_id' => Provider::STATE_ACTIVE 
		] );
	}
	public function getUrl($action = 'view', $id = null) {
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
		$json ['title'] = $this->title;
		$json ['provider_type'] = $this->provider_type;
		$json ['client_id'] = $this->client_id;
		$json ['client_secret_key'] = $this->client_secret_key;
		$json ['state_id'] = $this->state_id;
		$json ['type_id'] = $this->type_id;
		$json ['created_on'] = $this->created_on;
		$json ['created_by_id'] = $this->created_by_id;
		if ($with_relations) {
		}
		return $json;
	}
	public function getProviderClient($provider, $db) {
		switch (strtolower ( $provider )) {
			case "twitter" :
				$dbClient = [ 
						'class' => 'yii\authclient\clients\Twitter',
						'attributeParams' => [ 
								'include_email' => 'true' 
						],
						'consumerKey' => $db->client_id,
						'consumerSecret' => $db->client_secret_key 
				];
				break;
			default :
				$dbClient = [ 
						'class' => "yii\authclient\clients\\$provider",
						'clientId' => $db->client_id,
						'clientSecret' => $db->client_secret_key 
				];
				break;
		}
		return $dbClient;
	}
	public function getDomainUrl($type) {
		$list = self::getClientClasses ();
		$client = strtolower ( $list [$type] );
		return Yii::$app->getUrlManager ()->createAbsoluteUrl ( [ 
				"social/user/auth?authclient={$client}" 
		] );
	}
}
