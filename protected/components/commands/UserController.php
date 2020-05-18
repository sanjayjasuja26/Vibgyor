<?php

namespace app\components\commands;

use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use app\components\SPasswordValidator;

class UserController extends Controller {

    public $dryrun = false;
    public $email = null;
    public $password = null;
    public $role_id = null;
    public $length = 8;

    public function options($actionID) {
        return [
            'dryrun',
            'email',
            'password',
            'role_id'
        ];
    }

    public function optionAliases() {
        return [
            'd' => 'dryrun',
            'e' => 'email',
            'p' => 'password',
            'r' => 'role_id'
        ];
    }

    /**
     * Reset password using email id
     *
     * @param
     *            -e -p
     * @example php console.php user/password -e=user@jischool.com -p=admin@123
     *         
     * @return number
     */
    public function actionPassword() {
        if (is_null($this->password)) {

            User::log('Password required ! (Hint -p=)');

            return ExitCode::NOUSER;
        }

        if (is_null($this->email)) {

            User::log('Email required ! (Hint -e=)');

            return ExitCode::DATAERR;
        }

        $model = User::findOne([
                    'email' => $this->email
        ]);

        if (is_null($model)) {

            User::log('User not found');

            return ExitCode::NOUSER;
        }

        $validator = new SPasswordValidator();

        $model->password = $this->password;

        $validator->validateAttribute($model, "password");

        if ($model->errors) {

            User::log($model->errorsString);

            return ExitCode::DATAERR;
        }

        $model->setPassword($this->password);

        if (!$model->save()) {

            User::log('Password not changed ');

            return ExitCode::DATAERR;
        }

        User::log($this->email . ' = Password successfully changed !');

        return ExitCode::OK;
    }

    /**
     * Update User Role Id using email id
     *
     * @param
     *            -e -r
     * @example php console.php user/role -e=user@jischool.com -r=2
     * @return number
     */
    public function actionRole() {
        foreach (USER::getRoleOptions() as $key => $val) {

            User::log("\n" . $val . ' Role-id is ' . $key . "\n");
        }

        if (is_null($this->email)) {

            User::log('Email required ! (Hint -e=)');

            return ExitCode::DATAERR;
        }

        $model = User::findOne([
                    'email' => $this->email
        ]);

        if (is_null($model)) {

            User::log('User not found');

            return ExitCode::NOUSER;
        }

        if (is_null($this->role_id)) {

            User::log('Current Role Id is ' . $model->role_id . "\n");
            User::log('Add correct Role id (Hint -r=)');


            return ExitCode::DATAERR;
        }

        if (is_null(ArrayHelper::getValue(USER::getRoleOptions(), $this->role_id))) {

            User::log('Please enter correct role id :');

            return ExitCode::DATAERR;
        }

        $model->role_id = $this->role_id;

        if (!$model->save()) {

            User::log('Please enter valid Role Id ');

            return ExitCode::DATAERR;
        }

        User::log($this->email . ' = Role Id successfully Updated !');

        return ExitCode::OK;
    }

}
