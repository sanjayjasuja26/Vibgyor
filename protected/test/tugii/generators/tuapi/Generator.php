<?php

namespace app\modules\tugii\generators\tuapi;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use app\components\SActiveRecord;
use app\modules\api\controllers\ApiTxController;
use yii\db\BaseActiveRecord;
use yii\helpers\StringHelper;
use app\models\Page;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 */
class Generator extends \yii\gii\Generator {

    public $modelClass;
    public $controllerClass;
    public $baseControllerClass = 'app\modules\api\controllers\ApiTxController';
    public $_models;

    public function beforeValidate() {
        $modelClassPath = StringHelper::dirname($this->modelClass);
        $modelClass = StringHelper::basename($this->modelClass);

        if (empty($this->controllerClass)) {
            $nsControllerClass = 'app\modules\api\controllers';
            $this->controllerClass = $nsControllerClass . "\\" . $modelClass . 'Controller';
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'TuGii API Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription() {
        return 'This generator generates Api.';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates() {
        return [
            'api.php'
        ];
    }

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'modelClass' => 'Model Class',
            'controllerClass' => 'Api Controller Class',
            'viewPath' => 'View Path',
            'baseControllerClass' => 'Base Controller Class',
                ]);
    }

    public function rules() {
        return array_merge(parent::rules(), [
            [
                [
                    'controllerClass',
                    'modelClass',
                    'baseControllerClass'
                ],
                'filter',
                'filter' => 'trim'
            ],
            [
                [
                    'modelClass',
                    'controllerClass',
                    'baseControllerClass'
                ],
                'required'
            ],
            [
                [
                    'modelClass',
                    'controllerClass',
                    'baseControllerClass'
                ],
                'match',
                'pattern' => '/^[\w\\\\]*$/',
                'message' => 'Only word characters and backslashes are allowed.'
            ],
            [
                [
                    'modelClass'
                ],
                'validateClass',
                'params' => [
                    'extends' => BaseActiveRecord::className()
                ]
            ],
            [
                [
                    'controllerClass'
                ],
                'match',
                'pattern' => '/Controller$/',
                'message' => 'Controller class name must be suffixed with "Controller".'
            ],
            [
                [
                    'controllerClass'
                ],
                'match',
                'pattern' => '/(^|\\\\)[A-Z][^\\\\]+Controller$/',
                'message' => 'Controller class name must start with an uppercase letter.'
            ],
            [
                [
                    'modelClass'
                ],
                'validateModelClass'
            ]
                ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes() {
        return array_merge(parent::stickyAttributes(), [
            'baseControllerClass'
                ]
        );
    }

    public function hints() {
        return array_merge(parent::hints(), [
            'modelClass' => 'This is the Api Model Class.
				 You should provide a fully qualified class name, e.g., <code>app\models\PosT</code>.',
            'controllerClass' => 'This is the Api Controller Class.
				 You should provide a fully qualified class name, e.g., <code>app\modules\api\controllers\PostController</code>.',
            'baseControllerClass' => 'This is the class that the new API controller class will extend from.
                You should provide a fully qualified class name, e.g., <code>app\modules\api\controllers\ApiTxController</code>.'
                ]
        );
    }

    /**
     * Checks if model class is valid
     */
    public function validateModelClass() {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();
        if (empty($pk)) {
            $this->addError('modelClass', "The table associated with $class must have primary key(s).");
        }
    }

    public function getControllerID() {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos), 1, -10);

        return Inflector::camel2id($class);
    }

    /**
     * @inheritdoc
     */
    public function generate() {
        $files = [];
        $controllerPath = 'protected\modules\api\controllers';
        $testPath = 'protected\modules\api\test';

        $pos = strrpos($this->controllerClass, '\\');
        $controllerclass = substr(substr($this->controllerClass, $pos + 1), 0, - 10);

        $controllerFile = $controllerPath . '\\' . $controllerclass . 'Controller.php';
        $testFile = Yii::getAlias($testPath . '\\' . $this->getControllerID() . '.php');
        $files = [
            new CodeFile($controllerFile, $this->render('api.php'))
        ];

        $files [] = new CodeFile($testFile, $this->render('apitest.php'));

        return $files;
    }

    public function getModelID() {
        $pos = strrpos($this->modelClass, '\\');
        $class = substr($this->modelClass, $pos + 1);

        return $class;
    }

    public function generateUrlParams() {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'id' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                    $params [] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params [] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }

    /**
     * Generates action parameters
     * 
     * @return string
     */
    public function generateActionParams() {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            return '$id';
        } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     *
     * @return array model column names
     */
    public function getColumnNames() {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema()->getColumnNames();
        } else {
            /* @var $model \yii\base\Model */
            $model = new $class ();

            return $model->attributes();
        }
    }

    /**
     * Returns table schema for current model class or false if it is not an active record
     * 
     * @return boolean|\yii\db\TableSchema
     */
    public function getTableSchema() {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema();
        } else {
            return false;
        }
    }

    public function getFieldtestdata($modelClass, $column) {
        if (strtoupper($column->dbType) == 'TINYINT(1)' || strtoupper($column->dbType) == 'BIT' || strtoupper($column->dbType) == 'BOOL' || strtoupper($column->dbType) == 'BOOLEAN') {
            return "\Faker\Factory::create()->boolean";
        } else if (strtoupper($column->dbType) == 'DATE' || strtoupper($column->dbType) == 'DATETIME') {
            return '"' . date('Y-m-d H:i:s') . '"';
        } else if (strtoupper($column->dbType) == 'TIME') {
            return '"' . date('H:i:s') . '"';
        } elseif (preg_match('/^status|status_id|state|state_id$/i', $column->name)) {
            return "0";
        } elseif (preg_match('/^type|type_id$/i', $column->name)) {
            return "0";
        } elseif (preg_match('/_name/i', $column->name)) {
            return "\Faker\Factory::create()->name"; //"Jhon Smith";
        } elseif (preg_match('/_id$/i', $column->name)) {
            return "1";
        } else if (stripos($column->dbType, 'text') !== false) { // Start of CrudCode::generateActiveField code.
            return "\Faker\Factory::create()->text";
        } else {
            return "\Faker\Factory::create()->text(10)";
        }
    }

    public function findRelation($modelClass, $column) {
        if (!$column->isForeignKey)
            return null;
        $relations = ActiveRecord::model($modelClass)->relations();
        // Find the relation for this attribute.
        foreach ($relations as $relationName => $relation) {
            // For attributes on this model, relation must be BELONGS_TO.
            if ($relation [0] == ActiveRecord::BELONGS_TO && $relation [2] == $column->name) {
                return array(
                    $relationName, // the relation name
                    $relation [0], // the relation type
                    $relation [2], // the foreign key
                    $relation [1]
                        ) // the related active record class name
                ;
            }
        }
        // None found.
        return null;
    }

    public function getCurrentid($modelClass) {
        $model = new $modelClass ();
        $model = $modelClass::findOne([
                    $model->attributes('id')
                ]);
        if ($model) {

            return $model;
        } else {
            return 0;
        }
    }

}
