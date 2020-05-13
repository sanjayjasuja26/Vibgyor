<?php
/**
 *@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
 *@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
 */
namespace app\modules\tugii\generators\tumodel;

use Yii;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;

class Generator extends \yii\gii\generators\model\Generator
{

    public $db = 'db';

    public $ns = 'app\models';

    public $tableName;

    public $modelClass;

    public $baseClass = 'app\components\TActiveRecord';

    public $generateLabelsFromComments = false;

    public $useTablePrefix = true;

    public $enableI18N = true;

    public $moduleName;

    public $appNs = 'app\models';

    /**
     *
     * @inheritdoc
     */
    public function getName()
    {
        return 'TuGii Model Generator';
    }

    /**
     *
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates two ActiveRecord class for the specified database table. An empty one you can extend and a Base one which is the same as the original model generatior.';
    }

    /**
     *
     * @inheritdoc
     */
    public function autoCompleteData()
    {
        $db = $this->getDbConnection();
        if ($db !== null) {
            return [
                'tableName' => function () use ($db) {
                    return $db->getSchema()->getTableNames();
                }
            ];
        } else {
            return [];
        }
    }

    /**
     *
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return [
            'model.php'
        ];
    }

    /**
     *
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), [
            // 'ns',
            'db',
            'baseClass',
            'generateRelations',
            'generateLabelsFromComments',
            'queryNs',
            'queryBaseClass'
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                [
                    'db',
                    'ns',
                    'tableName',
                    'modelClass',
                    'baseClass',
                    'moduleName',
                    'queryNs',
                    'queryClass',
                    'queryBaseClass'
                ],
                'filter',
                'filter' => 'trim'
            ],
            [
                [
                    'ns',
                    'queryNs'
                ],
                'filter',
                'filter' => function ($value) {
                    return trim($value, '\\');
                }
            ],
            
            [
                [
                    'db',
                    'ns',
                    'tableName',
                    'baseClass',
                    'queryNs',
                    'queryBaseClass'
                ],
                'required'
            ],
            [
                [
                    'db',
                    'moduleName',
                    'modelClass',
                    'queryClass'
                ],
                'match',
                'pattern' => '/^\w+$/',
                'message' => 'Only word characters are allowed.'
            ],
            [
                [
                    'ns',
                    'baseClass',
                    'queryNs',
                    'queryBaseClass'
                ],
                'match',
                'pattern' => '/^[\w\\\\]+$/',
                'message' => 'Only word characters and backslashes are allowed.'
            ]
        
        ]);
    }

    public function beforeValidate()
    {
        if (empty($this->modelClass)) {
            $this->modelClass = $this->generateClassName($this->tableName);
        }
        if (! empty($this->moduleName)) {
            $this->ns = str_replace('moduleName', $this->moduleName, 'app\\modules\\moduleName\\models');
        }
        return parent::beforeValidate();
    }

    protected function generateClassName($tableName, $useSchemaName = null)
    {
        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }
        
        $schemaName = '';
        $fullTableName = $tableName;
        if (($pos = strrpos($tableName, '.')) !== false) {
            if (($useSchemaName === null && $this->useSchemaName) || $useSchemaName) {
                $schemaName = substr($tableName, 0, $pos) . '_';
            }
            $tableName = substr($tableName, $pos + 1);
        }
        
        $db = $this->getDbConnection();
        $patterns = [];
        $prefix = $db->tablePrefix ;//. '_' . $this->moduleName;
        $patterns[] = "/^{$prefix}(.*?)$/";
        $patterns[] = "/^(.*?){$prefix}$/";
        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }
        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }
        
        $className = str_replace($this->moduleName . '_', '', $className);
        
        return $this->classNames[$fullTableName] = Inflector::id2camel($schemaName . $className, '_');
    }

    protected function generateRelationsList()
    {
        if ($this->generateRelations === self::RELATIONS_NONE) {
            return [];
        }
        
        $db = $this->getDbConnection();
        
        $relations = [];
        foreach ($this->getSchemaNames() as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    
                    $fks = array_keys($refs);
                    $fks_val = array_values($refs);
                    $refClassName = $this->generateClassName($refTable);
                    
                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relations[$table->fullName]['hasOne'][$fks[0]] = [
                        $relationName,
                        $refClassName,
                        $fks_val[0]
                    ];
                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs);
                    
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][$hasMany ? 'hasMany' : 'hasOne'][$relationName] = [
                        $relationName,
                        $className,
                        $fks_val[0],
                        $fks[0]
                    ];
                }
                
                if (($junctionFks = $this->checkJunctionTable($table)) === false) {
                    continue;
                }
                
                $relations = $this->generateManyManyRelationsList($table, $junctionFks, $relations);
            }
        }
        
        if ($this->generateRelations === self::RELATIONS_ALL_INVERSE) {
            return $this->addInverseRelations($relations);
        }
        
        return $relations;
    }

    public function getCamelCaseColumn($columnName)
    {
        if (! empty($columnName) && substr_compare($columnName, '_id', - 3, 3, true) == 0) {
            $columnName = substr($columnName, 0, - 3);
        }
        $columnName = Inflector::id2camel($columnName, '_');
        return $columnName;
    }

    private function generateManyManyRelationsList($table, $fks, $relations)
    {
        $db = $this->getDbConnection();
        
        foreach ($fks as $pair) {
            
            list ($firstKey, $secondKey) = $pair;
            $table0 = $firstKey[0];
            $table1 = $secondKey[0];
            unset($firstKey[0], $secondKey[0]);
            $className0 = $this->generateClassName($table0);
            $className1 = $this->generateClassName($table1);
            $table0Schema = $db->getTableSchema($table0);
            $table1Schema = $db->getTableSchema($table1);
            
            $link = $this->generateRelationLink(array_flip($secondKey));
            $viaLink = $this->generateRelationLink($firstKey);
            $relationName = $this->generateRelationName($relations, $table0Schema, key($secondKey), true);
            $relations[$table0Schema->fullName]['hasMany'][$firstKey[0]] = [
                $relationName,
                $className1,
                true
            ];
            
            $link = $this->generateRelationLink(array_flip($firstKey));
            $viaLink = $this->generateRelationLink($secondKey);
            $relationName = $this->generateRelationName($relations, $table1Schema, key($firstKey), true);
            $relations[$table1Schema->fullName]['hasMany'][$firstKey[0]] = [
                $relationName,
                $className0,
                $firstKey[1]
            ];
        }
        
        return $relations;
    }

    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            if ($this->generateLabelsFromComments && ! empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (! strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                
                if (! empty($label) && substr_compare($label, ' id', - 3, 3, true) == 0) {
                    $label = substr($label, 0, - 3);
                }
                $labels[$column->name] = $label;
            }
        }
        return $labels;
    }

    /**
     *
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        
        $relations = $this->generateRelations();
        
        $relationsList = $this->generateRelationsList();
        
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'relationsList' => isset($relationsList[$tableName]) ? $relationsList[$tableName] : []
            ];
            $files[] = new CodeFile(Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php', $this->render('model.php', $params));
            // query :
            if ($queryClassName) {
                $params = [
                    'className' => $queryClassName,
                    'modelClassName' => $modelClassName
                ];
                $files[] = new CodeFile(Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php', $this->render('query.php', $params));
            }
        }
        
        return $files;
    }

    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (! $column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }
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
                case 'double': // Schema::TYPE_DOUBLE, which is available since Yii 2.0.3
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }
        
        $rules = parent::generateRules($table);
        
        $all = [];
        foreach ($lengths as $length => $columns) {
            $all = array_merge($all, $columns);
        }
        if (! empty($all))
            $rules[] = "[['" . implode("', '", $all) . "'], 'trim']";
        
        foreach ($table->columns as $column) {
            if (preg_match('/^(email)$/i', $column->name)) {
                $rules[] = "[['" . $column->name . "'], 'email' ]";
            }
            if (preg_match('/(name)$/i', $column->name)) {
                $rules[] = "[['" . $column->name . "'], 'app\components\TNameValidator' ]";
            }
            if (preg_match('/(password)$/i', $column->name)) {
                $rules[] = "[['" . $column->name . "'],'app\components\TPasswordValidator','length' => 8  ]";
            }
            if (preg_match('/^(status_id|state_id)/i', $column->name)) {
                $rules[] = "[['" . $column->name . "'], 'in', 'range' => array_keys(self::getStateOptions())]";
            }
            if (preg_match('/(type_id)$/i', $column->name)) {
                $rules[] = "[['" . $column->name . "'], 'in', 'range' => array_keys (self::get" . $this->getCamelCaseColumn($column->name) . "Options())]";
            }
            if (preg_match('/(file)$/i', $column->name)) {
                $rules[] = "[['" . $column->name . "'],'file',
						'skipOnEmpty' => true,
						'extensions' => 'png, jpg,jpeg' ]";
            }
        }
        
        return $rules;
    }
}
