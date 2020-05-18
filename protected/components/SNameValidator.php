<?php

namespace app\components;

use yii\validators\Validator;

class SNameValidator extends Validator {

    public $pattern = "/^[A-Za-z.]+((\s)?([A-Za-z])+)*$/";

    public function validateAttribute($model, $attribute) {
        if (!defined('MIGRATION_IN_PROGRESS') && !preg_match($this->pattern, $model->$attribute))
            $model->addError($attribute, $model->getAttributeLabel($attribute) . ' is invalid.');
    }

}
