<?php

namespace app\modules\payment\models;

use app\modules\payment\models\Transaction;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PaymentTransaction represents the model behind the search form about `app\models\PaymentTransaction`.
 */
class TransactionSearch extends Transaction {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'id',
                    'gateway_type',
                    'payment_status'
                ],
                'integer'
            ],
            [
                [
                    'name',
                    'email',
                    'amount',
                    'currency',
                    'transaction_id',
                    'value',
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
        $query = Transaction::find();

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
            'gateway_type' => $this->gateway_type,
            'payment_status' => $this->payment_status,
            'created_on' => $this->created_on
        ]);

        $query->andFilterWhere([
            'like',
            'name',
            $this->name
        ])->andFilterWhere([
            'like',
            'email',
            $this->email
        ])->andFilterWhere([
            'like',
            'amount',
            $this->amount
        ])->andFilterWhere([
            'like',
            'value',
            $this->value
        ]);

        return $dataProvider;
    }

}
