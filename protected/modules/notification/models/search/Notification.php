<?php
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
namespace app\modules\notification\models\search;

use app\modules\notification\models\Notification as NotificationModel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Notification represents the model behind the search form about `app\modules\notification\models\Notification`.
 */
class Notification extends NotificationModel
{

    /**
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'model_id',
                    'state_id',
                    'type_id',
                    'to_user_id',
                    'created_by_id'
                ],
                'integer'
            ],
            [
                [
                    'title',
                    'description',
                    'model_type',
                    'is_read',
                    'created_on'
                ],
                'safe'
            ]
        ];
    }

    /**
     *
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function beforeValidate()
    {
        return true;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $flag = false)
    {
        $query = NotificationModel::find();
        if ($flag == true) {
            $query = $query->where([
                'to_user_id' => \Yii::$app->user->id
            ]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);
        
        if (! ($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'id' => $this->id,
            'model_id' => $this->model_id,
            'state_id' => $this->state_id,
            'type_id' => $this->type_id,
            'created_on' => $this->created_on,
            'to_user_id' => $this->to_user_id,
            'created_by_id' => $this->created_by_id
        ]);
        
        $query->andFilterWhere([
            'like',
            'title',
            $this->title
        ])
            ->andFilterWhere([
            'like',
            'description',
            $this->description
        ])
            ->andFilterWhere([
            'like',
            'model_type',
            $this->model_type
        ])
            ->andFilterWhere([
            'like',
            'is_read',
            $this->is_read
        ]);
        
        return $dataProvider;
    }
}
