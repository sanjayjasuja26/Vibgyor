<?php

/**
 * @copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
 * @author     : Ram mohamad Singh< er.amudeep@gmail.com >
 */

namespace app\models\search;

use app\models\User as UserModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * User represents the model behind the search form about `app\models\User`.
 */
class User extends UserModel {

    /**
     *
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                [
                    'id',
                    'gender',
                    'tos',
                    'role_id',
                    'state_id',
                    'type_id',
                    'login_error_count',
                    'created_by_id'
                ],
                'integer'
            ],
            [
                [
                    'full_name',
                    'email',
                    'password',
                    'date_of_birth',
                    'about_me',
                    'contact_no',
                    'address',
                    'latitude',
                    'longitude',
                    'city',
                    'country',
                    'zipcode',
                    'language',
                    'profile_file',
                    'last_visit_time',
                    'last_action_time',
                    'last_password_change',
                    'activation_key',
                    'timezone',
                    'created_on',
                    'updated_on'
                ],
                'safe'
            ]
        ];
    }

    /**
     *
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
    public function search($params, $role_id = null) {
        $query = UserModel::find()->where([
            '!=',
            'role_id',
            User::ROLE_ADMIN
        ]);
        if (!empty($role_id)) {
            $query->andWhere(['role_id' => $role_id]);
        }
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
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'tos' => $this->tos,
            'role_id' => $this->role_id,
            'state_id' => $this->state_id,
            'type_id' => $this->type_id,
            'last_visit_time' => $this->last_visit_time,
            'last_action_time' => $this->last_action_time,
            'last_password_change' => $this->last_password_change,
            'login_error_count' => $this->login_error_count,
            'updated_on' => $this->updated_on,
            'created_by_id' => $this->created_by_id
        ]);

        $query->andFilterWhere([
                    'like',
                    'full_name',
                    $this->full_name
                ])
                ->andFilterWhere([
                    'like',
                    'email',
                    $this->email
                ])
                ->andFilterWhere([
                    'like',
                    'password',
                    $this->password
                ])
                ->andFilterWhere([
                    'like',
                    'about_me',
                    $this->about_me
                ])
                ->andFilterWhere([
                    'like',
                    'contact_no',
                    $this->contact_no
                ])
                ->andFilterWhere([
                    'like',
                    'address',
                    $this->address
                ])
                ->andFilterWhere([
                    'like',
                    'latitude',
                    $this->latitude
                ])
                ->andFilterWhere([
                    'like',
                    'longitude',
                    $this->longitude
                ])
                ->andFilterWhere([
                    'like',
                    'city',
                    $this->city
                ])
                ->andFilterWhere([
                    'like',
                    'country',
                    $this->country
                ])
                ->andFilterWhere([
                    'like',
                    'zipcode',
                    $this->zipcode
                ])
                ->andFilterWhere([
                    'like',
                    'language',
                    $this->language
                ])
                ->andFilterWhere([
                    'like',
                    'profile_file',
                    $this->profile_file
                ])
                ->andFilterWhere([
                    'like',
                    'activation_key',
                    $this->activation_key
                ])
                ->andFilterWhere([
                    'like',
                    'timezone',
                    $this->timezone
                ])
                ->andFilterWhere([
                    'like',
                    'created_on',
                    $this->created_on
        ]);

        return $dataProvider;
    }

}
