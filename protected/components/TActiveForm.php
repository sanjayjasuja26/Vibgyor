<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\components;

use yii\bootstrap\ActiveForm;

class TActiveForm extends ActiveForm {
	public $enableAjaxValidation = true;
	public $enableClientValidation = true;
	public $options = [ 
			'enctype' => 'multipart/form-data' 
	];
	public $fieldClass = 'app\components\TActiveField';
}
