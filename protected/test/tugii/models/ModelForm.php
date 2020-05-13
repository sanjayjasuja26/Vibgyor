<?php
/**
*@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
*@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
*/
 

namespace app\modules\tugii\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Model;

/**
 * Class ModelForm
 */
class ModelForm extends Model {
	public $db_connection;
	public $models_path;
	public $models_search_path;
	public $override_models = true;
	public $exclude_models = true;
	public $exclude_controllers = 'User';
}
