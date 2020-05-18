<?php

namespace app\modules\social\models\search;

use app\models\User as UserModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SocialUser represents the model behind the search form about `app\models\SocialUser`.
 */
class User extends UserModel {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'id',
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
        $query = UserModel::find();

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
            'user_id' => $this->user_id
        ]);

        $query->andFilterWhere([
            'like',
            'social_user_id',
            $this->social_user_id
        ])->andFilterWhere([
            'like',
            'social_provider',
            $this->social_provider
        ])->andFilterWhere([
            'like',
            'loginProviderIdentifier',
            $this->loginProviderIdentifier
        ]);

        return $dataProvider;
    }

}
