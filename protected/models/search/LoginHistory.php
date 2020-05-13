<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\models\search;

use app\models\LoginHistory as LoginHistoryModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LoginHistory represents the model behind the search form about `app\models\LoginHistory`.
 */
class LoginHistory extends LoginHistoryModel {
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'id',
								'user_id',
								'state_id',
								'type_id' 
						],
						'integer' 
				],
				[ 
						[ 
								'user_ip',
								'user_agent',
								'failer_reason',
								'code',
								'created_on' 
						],
						'safe' 
				] 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios ();
	}
	public function beforeValidate() {
		return true;
	}
	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params        	
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params) {
		$query = LoginHistoryModel::find ();
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query,
				'sort' => [ 
						'defaultOrder' => [ 
								'id' => SORT_DESC 
						] 
				] 
		] );
		
		if (! ($this->load ( $params ) && $this->validate ())) {
			return $dataProvider;
		}
		
		$query->andFilterWhere ( [ 
				'id' => $this->id,
				'user_id' => $this->user_id,
				'state_id' => $this->state_id,
				'type_id' => $this->type_id,
				'created_on' => $this->created_on 
		] );
		
		$query->andFilterWhere ( [ 
				'like',
				'user_ip',
				$this->user_ip 
		] )->andFilterWhere ( [ 
				'like',
				'user_agent',
				$this->user_agent 
		] )->andFilterWhere ( [ 
				'like',
				'failer_reason',
				$this->failer_reason 
		] )->andFilterWhere ( [ 
				'like',
				'code',
				$this->code 
		] );
		
		return $dataProvider;
	}
}
