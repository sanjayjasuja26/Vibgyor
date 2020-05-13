<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/

/**
 * This is the model class for table "tbl_email_queue".
 *
 * @property integer $id
 * @property string $from_email
 * @property string $to_email
 * @property string $message
 * @property string $subject
 * @property string $date_published
 * @property string $last_attempt
 * @property string $date_sent
 * @property integer $attempts
 * @property integer $state_id
 * @property integer $email_id
 * @property integer $project_id
 *
 */
namespace app\models;

class EmailQueue extends \app\components\TEmailQueue
{

    public function isAllowed()
    {
        if (User::isAdmin())
            return true;

        if ($this instanceof User) {
            return ($this->id == \Yii::$app->user->id);
        }
        if ($this->hasAttribute('created_by_id')) {
            return ($this->created_by_id == \Yii::$app->user->id);
        }

        if ($this->hasAttribute('user_id')) {
            return ($this->user_id == \Yii::$app->user->id);
        }

        return false;
    }
}
