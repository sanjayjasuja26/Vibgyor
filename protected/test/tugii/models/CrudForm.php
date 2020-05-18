<?php

namespace app\modules\tugii\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Model;

class CrudForm extends Model {

    public $db_connection;
    public $models_path;
    public $models_search_path;
    public $controllers_path;
    public $override_controllers = true;
    public $exclude_controllers = 'User';

}
