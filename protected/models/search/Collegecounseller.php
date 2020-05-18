<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Collegecounseller as CollegecounsellerModel;

/**
 * Collegecounseller represents the model behind the search form about `app\models\Collegecounseller`.
 */
class Collegecounseller extends CollegecounsellerModel {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'college_id', 'state_id', 'type_id', 'created_by_id'], 'integer'],
            [['full_name', 'email', 'contact_no', 'created_on', 'updated_on'], 'safe'],
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
        $query = CollegecounsellerModel::find();

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
            'college_id' => $this->college_id,
            'state_id' => $this->state_id,
            'type_id' => $this->type_id,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
            'created_by_id' => $this->created_by_id,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'contact_no', $this->contact_no]);

        return $dataProvider;
    }

}
