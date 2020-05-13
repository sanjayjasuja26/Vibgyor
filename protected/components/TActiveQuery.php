<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\components;

use Yii;
use yii\db\ActiveQuery;

class TActiveQuery extends ActiveQuery
{

    public function my($attribbute = 'created_by_id')
    {
        list ($table, $alias) = $this->getTableNameAndAlias();

        return $this->andWhere([
            $alias . '.' . $attribbute => Yii::$app->user->id
        ]);
    }

    public function active($state_id = 1, $stateAttribbute = 'state_id')
    {
        list ($table, $alias) = $this->getTableNameAndAlias();

        return $this->andWhere([
            $alias . '.' . $stateAttribbute => $state_id
        ]);
    }

    public function andState($state_id, $stateAttribbute = 'state_id')
    {
        list ($table, $alias) = $this->getTableNameAndAlias();
        $state_id = is_array($state_id) ? $state_id : [
            $state_id
        ];

        return $this->andWhere([
            'in',
            $alias . '.' . $stateAttribbute,
            $state_id
        ]);
    }

    public function orState($state_id, $stateAttribbute = 'state_id')
    {
        list ($table, $alias) = $this->getTableNameAndAlias();
        $state_id = is_array($state_id) ? $state_id : [
            $state_id
        ];
        return $this->andWhere([
            'in',
            $alias . '.' . $stateAttribbute,
            $state_id
        ]);
    }
}
