<?php

/**
 * @copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
 * @author     : Ram mohamad Singh< er.amudeep@gmail.com >
 */

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model {

    public $username;
    public $password;
    public $rememberMe = true;
    private $_user = false;
    public $device_token;
    public $device_type;

    public function asJson() {
        $Json = [];
        $Json ['username'] = $this->username;
        $Json ['device_token'] = $this->device_token;
        $Json ['device_type'] = $this->device_type;
        return $Json;
    }

    /**
     *
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // username and password are both required
            [
                [
                    'username',
                    'password'
                ],
                'required'
            ],
            [
                [
                    'username',
                    'password'
                ],
                'trim'
            ],
            [
                [
                    'username'
                ],
                'email',
                'message' => 'Email is not a valid email address.'
            ],
            // rememberMe must be a boolean value
            [
                'rememberMe',
                'boolean'
            ],
            [
                [
                    'device_token',
                    'device_type'
                ],
                'safe'
            ]
        ];
    }

    public function validatePassword($user) {
        if (!$this->hasErrors()) {
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Incorrect username or password.');
            }
        }
        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user) {
                if (!$user->isActive()) {
                    $this->addError('username', 'User is ' . $user->state);
                } elseif (!$user->validatePassword($this->password)) {
                    $this->addError('password', 'Incorrect username or password.');
                }
                if (!$user->IsEmailVerified()) {
                    $this->addError('username', 'Error !! Please verfiy your email first.');
                }
                if (!$this->hasErrors()) {
                    LoginHistory::add(true, $user, null);
                    return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
                }
            } else {
                $this->addError('username', 'Incorrect Email.');
            }
            LoginHistory::add(false, $user, $this->errors);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    public function getErrorsString() {
        $out = '';
        if ($this->errors != null)
            foreach ($this->errors as $err) {
                $out .= implode(',', $err);
            }
        return $out;
    }

}
