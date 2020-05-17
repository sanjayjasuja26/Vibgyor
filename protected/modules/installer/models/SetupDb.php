<?php

namespace app\modules\installer\models;

use yii\base\Model;

class SetupDb extends Model {

    public $username;
    public $password = '';
    public $host = 'localhost';
    public $db_name;
    public $table_prefix = 'tbl_';
    public $port = '3306';

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {

        return [
            [
                [
                    'username',
                    'db_name'
                ],
                'required'
            ],
            [
                [
                    'port',
                    'password',
                    'table_prefix'
                ],
                'safe'
            ],
        ];
    }

}
