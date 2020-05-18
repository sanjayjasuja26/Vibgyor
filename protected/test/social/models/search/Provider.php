<?php

namespace app\modules\social\models\search;

use app\modules\social\models\Provider as ProviderModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Provider represents the model behind the search form about `app\models\Provider`.
 */
class Provider extends ProviderModel {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'id',
                    'state_id',
                    'type_id',
                    'created_by_id'
                ],
                'integer'
            ],
            [
                [
                    'title',
                    'provider_type',
                    'client_id',
                    'client_secret_key',
                    'created_on',
                    'updated_on'
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
        return Model::scenarios();
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
        $query = ProviderModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
                ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'state_id' => $this->state_id,
            'type_id' => $this->type_id,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
            'created_by_id' => $this->created_by_id
        ]);

        $query->andFilterWhere([
            'like',
            'title',
            $this->title
        ])->andFilterWhere([
            'like',
            'provider_type',
            $this->provider_type
        ])->andFilterWhere([
            'like',
            'client_id',
            $this->client_id
        ])->andFilterWhere([
            'like',
            'client_secret_key',
            $this->client_secret_key
        ]);

        return $dataProvider;
    }

}
