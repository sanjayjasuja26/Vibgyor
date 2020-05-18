<?php

namespace app\modules\tugii\generators\tutestcase;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\helpers\FileHelper;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @since 2.0
 */
class Generator extends \yii\gii\Generator {

    public $modelClass;
    public $controllerClass;
    public $actions;
    public $_models;
    public $_controllers;
    public $_modulecontrollers;
    public $module;
    public $controllerPath = 'tests\acceptance';

    public function beforeValidate() {
        $modelClassPath = StringHelper::dirname($this->modelClass);

        $modelClass = StringHelper::basename($this->modelClass);

        if (empty($this->controllerClass)) {
            $nsControllerClass = '';
            $this->controllerClass = str_replace('Controller', 'AcceptanceCest', $modelClass);
        }

        if (strstr($modelClassPath, 'modules')) {
            $this->module = StringHelper::basename(StringHelper::dirname($modelClassPath));

            $this->controllerPath = str_replace('moduleName', $this->module, '@app/modules/moduleName/tests/acceptance');

            $this->controllerPath = Yii::getAlias($this->controllerPath);

            if (!file_exists($this->controllerPath))
                mkdir($this->controllerPath);
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'TuGii TEST Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription() {
        return 'This generator generates Test Case.';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates() {
        return [
            'test.php'
        ];
    }

    public function attributeLabels() {
        return [
            'modelClass' => 'Controller Class',
            'controllerClass' => 'Test Controller Class',
            'viewPath' => 'View Path'
        ];
    }

    public function rules() {
        return [
            [
                [
                    'controllerClass',
                    'modelClass'
                ],
                'filter',
                'filter' => 'trim'
            ],
            [
                [
                    'modelClass',
                    'controllerClass'
                ],
                'required'
            ],
            [
                [
                    'modelClass',
                    'controllerClass'
                ],
                'match',
                'pattern' => '/^[\w\\\\]*$/',
                'message' => 'Only word characters and backslashes are allowed.'
            ],
            /* 		[
              [
              'modelClass'
              ],
              'validateClass',
              'params' => [
              'extends' => BaseActiveRecord::className ()
              ]
              ], */
            [
                [
                    'controllerClass'
                ],
                'match',
                'pattern' => '/Cest$/',
                'message' => 'Cest class name must be suffixed with "Cest".'
            ],
            [
                [
                    'controllerClass'
                ],
                'match',
                'pattern' => '/(^|\\\\)[A-Z][^\\\\]+Cest$/',
                'message' => 'Cest class name must start with an uppercase letter.'
            ]
        ];
    }

    public function hints() {
        return array_merge(parent::hints(), [
            'modelClass' => 'This is the Test Model Class.
				 You should provide a fully qualified class name, e.g., <code>app\controllers\PosSController</code>.',
            'controllerClass' => 'Test Cest Class
				 You should provide a fully qualified class name, e.g., <code>PosTAcceptanceCest</code>.',
            'actions' => 'Test Cest Actions.'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generate() {
        $files = [];

        $controllerFile = $this->controllerPath . '/' . $this->controllerClass . '.php';

        $files = [
            new CodeFile($controllerFile, $this->render('test.php'))
        ];

        return $files;
    }

    /**
     * Generates action parameters
     *
     * @return string
     */
    public function generateActionParams() {
        return 'AcceptanceTester $I';
    }

    public function getModelRealClass() {
        $class = str_replace([
            'controllers',
            'Controller'
                ], [
            'models',
            ''
                ], $this->modelClass);
        return $class;
    }

    /**
     *
     * @return array model column names
     */
    public function getColumnNames() {

        /* @var $class ActiveRecord */
        $class = $this->modelRealClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema()->getColumnNames();
        } else {
            /* @var $model \yii\base\Model */
            $model = new $class();

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
        $class = $this->modelRealClass;
        if (is_subclass_of($class, 'yii\db\ActiveRecord')) {
            return $class::getTableSchema();
        } else {
            return false;
        }
    }

    public function getFieldtestdata($column) {
        if (strtoupper($column->dbType) == 'TINYINT(1)' || strtoupper($column->dbType) == 'BIT' || strtoupper($column->dbType) == 'BOOL' || strtoupper($column->dbType) == 'BOOLEAN') {
            return "\Helper::faker()->boolean";
        } else if (strtoupper($column->dbType) == 'DATE' || strtoupper($column->dbType) == 'DATETIME') {
            $datetime = date('Y-m-d H:i:s');
            return $datetime;
        } else if (strtoupper($column->dbType) == 'TIME') {
            return date('H:i:s');
        } elseif (preg_match('/^status|status_id|state|state_id$/i', $column->name)) {
            return "0";
        } elseif (preg_match('/^type|type_id$/i', $column->name)) {
            return "0";
        } elseif (preg_match('/^email$/i', $column->name)) {
            return "\Helper::faker()->email";
        } elseif (preg_match('/_name/i', $column->name)) {
            return "\Helper::faker()->name"; // "Jhon Smith";
        } elseif (preg_match('/_id$/i', $column->name)) {
            return "1";
        } else if (stripos($column->dbType, 'text') !== false) { // Start of CrudCode::generateActiveField code.
            return "\Helper::faker()->text";
        } else {
            return "\Helper::faker()->text(10)";
        }
    }

    public function findRelation($modelClass, $column) {
        if (!$column->isForeignKey)
            return null;
        $relations = ActiveRecord::model($modelClass)->relations();
        // Find the relation for this attribute.
        foreach ($relations as $relationName => $relation) {
            // For attributes on this model, relation must be BELONGS_TO.
            if ($relation[0] == ActiveRecord::BELONGS_TO && $relation[2] == $column->name) {
                return array(
                    $relationName, // the relation name
                    $relation[0], // the relation type
                    $relation[2], // the foreign key
                    $relation[1]
                ); // the related active record class name
            }
        }
        // None found.
        return null;
    }

    public function getCurrentid($modelClass) {
        $model = new $modelClass();
        $model = $modelClass::findOne([
                    $model->attributes('id')
        ]);
        if ($model) {

            return $model;
        } else {
            return 0;
        }
    }

    public function getActions($modelClass) {
        $actions = [];

        $methods = (new \ReflectionClass($modelClass))->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (StringHelper::startsWith($method->name, 'action') && $method->name != 'actions') {
                $functionName = substr($method->name, strlen('action'));
                $actions[] = $functionName;
            }
        }
        /*
         * echo "<pre>";
         * VarDumper::dump($actions);
         */

        return $actions;
    }

    public function generateActiveField($column) {
        $modelClass = StringHelper::basename($this->modelClass);
        $modelClass = str_replace('Controller', '', $modelClass);
        $field = $modelClass . '[' . $column->name . ']';

        if ($column->phpType === 'boolean') {
            return "\$I->checkOption ('$field',false)";
        } elseif ($column->type === 'text' || preg_match('/^description$/i', $column->name)) {
            return "\$I->fillField ('$field',\$this->data['$column->name'] )";
        } elseif ($column->type === 'date' || $column->type === 'datetime') {
            return "\$I->fillField ('$field',\$this->data['$column->name'])";
        } elseif ($column->type === 'time') {
            return "\$I->fillField ('$field',\$this->data['$column->name'])";
        } elseif (preg_match('/^status|status_id|state|state_id$/i', $column->name)) {
            return "\$I->selectOption ('$field',\$this->data['$column->name'])";
        } elseif (preg_match('/^type|type_id$/i', $column->name)) {
            return "\$I->selectOption ('$field',\$this->data['$column->name'])";
        } elseif (preg_match('/^file|_file$/i', $column->name)) {
            return "\$I->attachFile('$field','')";
        } else {

            return "\$I->fillField ('$field',\$this->data['$column->name'])";
        }
    }

    protected function getModels() {
        if ($this->_models == null) {
            $files = scandir(Yii::getAlias('@app/controllers'));
            foreach ($files as $file) {
                if ($file[0] !== '.') {
                    $fileClassName = 'app\\controllers\\' . substr($file, 0, strpos($file, '.'));

                    if (class_exists($fileClassName) /* && is_subclass_of ( $fileClassName, 'app\\componenets\\SActiveRecord', true ) */) {

                        $this->_models[] = $fileClassName;
                    }
                }
            }
        }
        return $this->_models;
    }

    protected function geSControllers() {
        if ($this->_controllers == null) {
            $files = scandir(Yii::getAlias('@app/controllers'));
            foreach ($files as $file) {
                if ($file[0] !== '.') {
                    $fileClassName = 'app\\controllers\\' . substr($file, 0, strpos($file, '.'));

                    if (class_exists($fileClassName) /* && is_subclass_of ( $fileClassName, 'app\\componenets\\SActiveRecord', true ) */) {

                        $this->_controllers[] = $fileClassName;
                    }
                }
            }
        }
        return $this->_controllers;
    }

    protected function getModulesControllers() {
        if ($this->_modulecontrollers == null) {
            $files = FileHelper::findFiles(Yii::getAlias('@app/modules'), [
                        'recursive' => true,
                        'only' => [
                            '*Controller.php'
                        ]
            ]);

            foreach ($files as $file) {
                if (strstr($file, 'tugii'))
                    continue;
                $file = substr($file, strlen(Yii::getAlias('@app/modules/')));
                $fileClassName = substr($file, 0, strpos($file, '.'));
                $fileClassName = 'app\\modules\\' . str_replace('/', '\\', $fileClassName);

                if (class_exists($fileClassName) /* && is_subclass_of ( $fileClassName, 'app\\componenets\\SActiveRecord', true ) */) {

                    $this->_modulecontrollers[] = $fileClassName;
                }
            }
        }
        $all = array_merge($this->geSControllers(), $this->_modulecontrollers);

        return $all;
    }

    /**
     * @inheritdoc
     */
    public function autoCompleteData() {
        return [
            'modelClass' => $this->getModulesControllers()
                // 'controllerClass' => $this->geSControllers()
        ];
    }

}
