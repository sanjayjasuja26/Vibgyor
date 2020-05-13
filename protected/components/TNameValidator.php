<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\components;

use yii\validators\Validator;

class TNameValidator extends Validator {
    public $pattern = "/^[A-Za-z.]+((\s)?([A-Za-z])+)*$/" ;
	public function validateAttribute($model, $attribute) {
	    if (!defined('MIGRATION_IN_PROGRESS') && ! preg_match ( $this->pattern, $model->$attribute ))
			$model->addError ( $attribute, $model->getAttributeLabel ( $attribute ) . ' is invalid.' );
	}
}