<?php

namespace app\modules\tugii\generators\tucrud;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\helpers\FileHelper;

/**
 * Generates CRUD
 *
 * @property array $columnNames Model column names. This property is read-only.
 * @property string $controllerID The controller ID (without the module ID prefix). This property is
 *           read-only.
 * @property array $searchAttributes Searchable attributes. This property is read-only.
 * @property boolean|\yii\db\TableSchema $tableSchema This property is read-only.
 * @property string $viewPath The action view file path. This property is read-only.
 */
class Generator extends \yii\gii\generators\crud\Generator {

    public $modelClass;
    public $moduleID;
    public $controllerClass;
    public $baseControllerClass = 'app\components\TController';
    public $indexWidgetType = 'grid';
    public $searchModelClass;
    public $_models;
    public $_modulemodels = [];
    public $enablePjax = true;
    public $enableI18N = true;

    public function beforeValidate() {

        $modelClassPath = StringHelper::dirname($this->modelClass);
        $modelClass = StringHelper::basename($this->modelClass);
        $this->searchModelClass = $modelClassPath . "\\search\\" . $modelClass;
        $searchpath = StringHelper::dirname(Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php')));

        if (!file_exists($searchpath)) {
            mkdir($searchpath);
        }
        if (empty($this->controllerClass)) {
            // $nsControllerClass = 'app\controllers';
            $nsControllerClass = str_replace('models', 'controllers', $modelClassPath);
            $this->controllerClass = $nsControllerClass . "\\" . $modelClass . 'Controller';
        }
        if (empty($this->moduleID) && strstr($this->modelClass, 'modules')) {
            $modulePath = StringHelper::dirname($modelClassPath);
            $this->moduleID = StringHelper::basename($modulePath);
        }

        $module = empty($this->moduleID) ? Yii::$app : Yii::$app->getModule($this->moduleID);
        if ($module == null) {
            $this->addError('moduleID', 'Module not enabled , please check config/web ');
            return false;
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'TuGii CRUD Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription() {
        return 'This generator generates a controller and views that implement CRUD (Create, Read, Update, Delete)
            operations for the specified data model.';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates() {
        return [
            'controller.php'
        ];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes() {
        return array_merge(parent::stickyAttributes(), [
            'baseControllerClass',
            // 'moduleID',
            'indexWidgetType'
        ]);
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

    /**
     * Checks if model ID is valid
     */
    public function validateModuleID() {
        if (!empty($this->moduleID)) {
            $module = Yii::$app->getModule($this->moduleID);
            if ($module === null) {
                $this->addError('moduleID', "Module '{$this->moduleID}' does not exist.");
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function generate() {
        // die ('generate');
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');
        $files = [
            new CodeFile($controllerFile, $this->render('controller.php'))
        ];

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        return $files;
    }

    /**
     *
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID() {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, - 10);

        return Inflector::camel2id($class);
    }

    /**
     *
     * @return string the action view file path
     */
    public function getViewPath() {
        $module = empty($this->moduleID) ? Yii::$app : Yii::$app->getModule($this->moduleID);

        return $module->getViewPath() . '/' . $this->getControllerID();
    }

    public function getNameAttribute() {
        foreach ($this->getColumnNames() as $name) {
            if (!strcasecmp($name, 'name') || !strcasecmp($name, 'title')) {
                return $name;
            }
        }
        /* @var $class \yii\db\ActiveRecord */
        $class = $this->modelClass;
        $pk = $class::primaryKey();

        return $pk[0];
    }

    /**
     * Generates code for active field
     *
     * @param string $attribute
     * @return string
     */
    public function generateActiveField($attribute) {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field(\$model, '$attribute')->passwordInput()";
            } else {
                return "\$form->field(\$model, '$attribute')";
            }
        }
        $column = $tableSchema->columns[$attribute];

        if ($column->phpType === 'boolean' || $column->dbType == 'tinyint(1)') {
            return "\$form->field(\$model, '$attribute')->checkbox()" . "//\$form->field(\$model, '$attribute')->widget(kartik\widgets\SwitchInput::className(),[])";
        } elseif ($column->type === 'text' || preg_match('/^description$/i', $column->name)) {
            return " \$form->field(\$model, '$attribute')->widget ( app\components\SRichTextEditor::className (), [ 'options' => [ 'rows' => 6 ],'preset' => 'basic' ] );" . " //\$form->field(\$model, '$attribute')->textarea(['rows' => 6]);";
        } elseif ($column->type === 'date' || $column->type === 'datetime') {
            return "\$form->field(\$model, '$attribute')->widget(yii\jui\DatePicker::className(),
			[
					//'dateFormat' => 'php:Y-m-d',
	 				'options' => [ 'class' => 'form-control' ],
	 				'clientOptions' =>
	 				[
			//'minDate' => 0,
			'changeMonth' => true,'changeYear' => true ] ])";
        } elseif ($column->type === 'time') {
            return "\$form->field(\$model, '$attribute')->widget(kartik\widgets\TimePicker::className(),[])";
        } elseif (preg_match('/^status|status_id|state|state_id$/i', $column->name)) {
            return "\$form->field(\$model, '$attribute')->dropDownList(\$model->getStateOptions(), ['prompt' => ''])";
        } elseif (preg_match('/_id$/i', $column->name)) {
            return "\$form->field(\$model, '$attribute')->dropDownList(\$model->get" . $this->getCamelCaseColumn($column->name) . "Options(), ['prompt' => ''])";
        } elseif (preg_match('/^file|_file$/i', $column->name)) {
            return "\$form->field(\$model, '$attribute')->fileInput()";
        } elseif (preg_match('/_list$/i', $column->name)) {
            return "\$form->field(\$model, '$attribute')->widget(kartik\widgets\Select2::className(),['data' => array_merge(['' => ''], \$model->\$attribute),])";
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "\$form->field(\$model, '$attribute')->dropDownList(" . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ", ['prompt' => ''])";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "\$form->field(\$model, '$attribute')->$input()";
            } else {
                return "\$form->field(\$model, '$attribute')->$input(['maxlength' => $column->size])";
            }
        }
    }

    /**
     * Generates code for active search field
     *
     * @param string $attribute
     * @return string
     */
    public function generateActiveSearchField($attribute) {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false) {
            return "\$form->field(\$model, '$attribute')";
        }
        $column = $tableSchema->columns[$attribute];
        if ($column->phpType === 'boolean') {
            return "\$form->field(\$model, '$attribute')->checkbox()";
        } else {
            return "\$form->field(\$model, '$attribute')";
        }
    }

    /**
     * Generates column format
     *
     * @param \yii\db\ColumnSchema $column
     * @return string
     */
    public function generateColumnFormat($column) {
        if ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return 'ntext';
        } elseif ($column->type === 'date') {
            return 'date';
        } elseif ($column->type === 'datetime' || stripos($column->name, '_time') !== false) {
            return 'datetime';
        } elseif ($column->type === 'time') {
            return 'time';
        } elseif (stripos($column->name, 'email') !== false) {
            return 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            return 'url';
        } else {
            return 'text';
        }
    }

    public function generateDetailViewColumn($column) {
        $format = $this->generateColumnFormat($column);

        if ($column->phpType === 'boolean') {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif ($column->type === 'text' || preg_match('/^description$/i', $column->name)) {
            return "'" . $column->name . ":html" . "'";
        } elseif ($column->type === 'date' || $column->type === 'datetime') {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif ($column->type === 'time') {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif (preg_match('/^status|status_id|state|state_id$/i', $column->name)) {
            return "[" . "
			'attribute' => '$column->name',
			'format'=>'raw',
			'value' => \$model->getStateBadge()," . "]";
        } elseif (preg_match('/^type|type_id$/i', $column->name)) {
            return "[" . "
			'attribute' => '$column->name',
			'value' => \$model->get" . $this->getCamelCaseColumn($column->name) . "(),
			" . "]";
        } elseif (preg_match('/^file|_file$/i', $column->name)) {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif (preg_match('/_list$/i', $column->name)) {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif ($column->phpType !== 'string' || $column->size === null) {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } else {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        }
    }

    public function generateGridViewColumn($column) {
        $format = $this->generateColumnFormat($column);

        if ($column->phpType === 'boolean') {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif ($column->type === 'text' || preg_match('/^description$/i', $column->name)) {
            return "'" . $column->name . ":html" . "'";
        } elseif ($column->type === 'date' || $column->type === 'datetime') {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif ($column->type === 'time') {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif (preg_match('/^status|status_id|state|state_id$/i', $column->name)) {
            return "[" . "
			'attribute' => '$column->name','format'=>'raw','filter'=>isset(\$searchModel)?\$searchModel->getStateOptions():null,
			'value' => function (\$data) {" . " return \$data->getStateBadge(); " . " }," . "]";
        } elseif (preg_match('/^type|type_id$/i', $column->name)) {
            return "[" . "'attribute' => '$column->name','filter'=>isset(\$searchModel)?\$searchModel->get" . $this->getCamelCaseColumn($column->name) . "Options():null,
			'value' => function (\$data) {" . " return \$data->get" . $this->getCamelCaseColumn($column->name) . "(); " . " }," . "]";
        } elseif (preg_match('/^file|_file$/i', $column->name)) {
            return "[" . "'attribute' => '$column->name','filter'=>\$searchModel->getFileOptions(),
			'value' => function (\$data) {" . " return \$data->getFileOptions(\$data->$column->name); " . " }," . "]";
        } elseif (preg_match('/^created_by|user_id$/i', $column->name)) {
            return "[" . "
			'attribute' => '$column->name',
			'format'=>'raw',
			'value' => function (\$data) {" . " return \$data->getRelatedDataLink('$column->name'); " . " }," . "]";
        } elseif (preg_match('/_list$/i', $column->name)) {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } elseif ($column->phpType !== 'string' || $column->size === null) {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        } else {
            return "'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
        }
    }

    /**
     * Generates validation rules for the search model.
     *
     * @return array the generated validation rules
     */
    public function generateSearchRules() {
        if (($table = $this->getTableSchema()) === false) {
            return [
                "[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"
            ];
        }
        $types = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    /**
     *
     * @return array searchable attributes
     */
    public function getSearchAttributes() {
        return $this->getColumnNames();
    }

    /**
     * Generates the attribute labels for the search model.
     *
     * @return array the generated attribute labels (name => label)
     */
    public function generateSearchLabels() {
        /* @var $model \yii\base\Model */
        $model = new $this->modelClass();
        $attributeLabels = $model->attributeLabels();
        $labels = [];
        foreach ($this->getColumnNames() as $name) {
            if (isset($attributeLabels[$name])) {
                $labels[$name] = $attributeLabels[$name];
            } else {
                if (!strcasecmp($name, 'id')) {
                    $labels[$name] = 'ID';
                } else {
                    $label = Inflector::camel2words($name);
                    if (!empty($label) && substr_compare($label, ' id', - 3, 3, true) === 0) {
                        $label = substr($label, 0, - 3) . ' ID';
                    }
                    $labels[$name] = $label;
                }
            }
        }

        return $labels;
    }

    /**
     * Generates search conditions
     *
     * @return array
     */
    public function generateSearchConditions() {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model \yii\base\Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n" . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions) . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

    /**
     * Generates URL parameters
     *
     * @return string
     */
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
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
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
     * Generates parameter tags for phpdoc
     *
     * @return array parameter tags for phpdoc
     */
    public function generateActionParamComments() {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), - 2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return [
                '@param ' . $table->columns[$pks[0]]->phpType . ' $id'
            ];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
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
            $model = new $class();

            return $model->attributes();
        }
    }

    protected function getModels() {
        if ($this->_models == null) {
            $files = scandir(Yii::getAlias('@app/models'));
            foreach ($files as $file) {
                if ($file[0] !== '.') {
                    $fileClassName = substr($file, 0, strpos($file, '.'));
                    // if (class_exists($fileClassName) && is_subclass_of($fileClassName, 'SActiveRecord')) {

                    $this->_models[] = 'app\\models\\' . $fileClassName;
                    // }
                }
            }
        }
        return $this->_models;
    }

    protected function getModulesModels() {
        if ($this->_modulemodels == null) {
            $files = FileHelper::findFiles(Yii::getAlias('@app/modules'), [
                        'recursive' => true,
                        'only' => [
                            '*/models/*.php'
                        ]
            ]);

            foreach ($files as $file) {
                if (strstr($file, 'tugii'))
                    continue;
                $file = substr($file, strlen(Yii::getAlias('@app/modules/')));
                $fileClassName = substr($file, 0, strpos($file, '.'));
                $fileClassName = 'app\\modules\\' . str_replace('/', '\\', $fileClassName);

                if (class_exists($fileClassName) /* && is_subclass_of ( $fileClassName, 'app\\componenets\\SActiveRecord', true ) */) {

                    $this->_modulemodels[] = $fileClassName;
                }
            }
        }
        $all = array_merge($this->getModels(), $this->_modulemodels);

        return $all;
    }

    /**
     * @inheritdoc
     */
    public function autoCompleteData() {
        return [
            'modelClass' => $this->getModulesModels()
        ];
    }

    public function getCamelCaseColumn($columnName) {
        if (!empty($columnName) && substr_compare($columnName, '_id', - 3, 3, true) == 0) {
            $columnName = substr($columnName, 0, - 3);
        }
        $columnName = Inflector::id2camel($columnName, '_');
        return $columnName;
    }

}
