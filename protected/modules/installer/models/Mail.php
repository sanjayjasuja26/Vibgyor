<?php

namespace app\modules\installer\models;

use yii\base\Model;

class Mail extends Model {

    public $username = 'username';
    public $password = '';
    public $host = 'smtp.gmail.com';
    public $port = '25';
    public $encryption = 'tls';
    public $is_mail_prod = '0';

    const IS_MAIL = 1;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {
        return [
            [
                [
                    'host',
                    'username'
                ],
                'required',
                'when' => function ($model) {
                    return $model->is_mail_prod == 1;
                },
                'whenClient' => "function (attribute, value) { return $('#mail-button').val() == '1'; }"
            ],
            [
                [
                    'is_mail_prod',
                    'port',
                    'password'
                ],
                'safe'
            ]
        ];
    }

    public function attributeLabels() {
        return [
            'is_mail_prod' => 'Create Mail Configuration'
        ];
    }

}
