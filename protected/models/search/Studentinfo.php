<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Studentinfo as StudentinfoModel;

/**
 * Studentinfo represents the model behind the search form about `app\models\Studentinfo`.
 */
class Studentinfo extends StudentinfoModel {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'user_id', 'course_id', 'caste', 'current_studies', 'state_id', 'type_id', 'created_by_id'], 'integer'],
            [['f_name', 'm_name', 'created_on', 'updated_on'], 'safe'],
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
        $query = StudentinfoModel::find();

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
            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'caste' => $this->caste,
            'current_studies' => $this->current_studies,
            'state_id' => $this->state_id,
            'type_id' => $this->type_id,
            'created_on' => $this->created_on,
            'updated_on' => $this->updated_on,
            'created_by_id' => $this->created_by_id,
        ]);

        $query->andFilterWhere(['like', 'f_name', $this->f_name])
                ->andFilterWhere(['like', 'm_name', $this->m_name]);

        return $dataProvider;
    }

}
