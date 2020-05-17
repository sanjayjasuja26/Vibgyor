<?php

namespace app\components;

use app\base\TBaseActiveRecord;
use app\models\Feed;
use app\modules\file\models\File;
use app\models\User;
use app\modules\comment\models\Comment;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;

/**
 * This is the generic model class
 */
class TActiveRecord extends TBaseActiveRecord {

    protected $_controllerId = null;

    public static function findActive($state_id = 1) {
        return Yii::createObject(ActiveQuery::class, [
                    get_called_class()
                ])->andWhere([
                    'state_id' => $state_id
        ]);
    }

    public static function label($n = 1) {
        $className = Inflector::camel2words(StringHelper::basename(get_called_class()));
        if ($n == 2)
            return Inflector::pluralize($className);
        return $className;
    }

    public function __toString() {
        return $this->label(1);
    }

    public function getStateBadge() {
        return '';
    }

    public static function getStateOptions() {
        return [];
    }

    public function isAllowed() {
        if (method_exists(get_parent_class(), 'isAllowed')) {
            return parent::isAllowed();
        }
        if (User::isAdmin())
            return true;

        if ($this instanceof User) {
            return ($this->id == Yii::$app->user->id);
        }
        if ($this->hasAttribute('created_by_id')) {
            return ($this->created_by_id == Yii::$app->user->id);
        }

        if ($this->hasAttribute('user_id')) {
            return ($this->user_id == Yii::$app->user->id);
        }

        return false;
    }

    public function saveUploadedFile($model, $attribute = 'image_file', $old = null) {
        $uploaded_file = UploadedFile::getInstance($model, $attribute);
        if ($uploaded_file != null) {
            $path = UPLOAD_PATH;
            $filename = $path . str_replace('/', '-', \yii::$app->controller->id) . '-' . time() . '-' . $attribute . '-user_id_' . Yii::$app->user->id . '.' . $uploaded_file->extension;
            if (is_file($filename))
                unlink($filename);
            if (!empty($old) && is_file(UPLOAD_PATH . $old))
                unlink(UPLOAD_PATH . $old);
            $uploaded_file->saveAs($filename);
            $model->$attribute = basename($filename);
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes) {
        $this->processFeed($insert, $changedAttributes);

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     *
     * @param
     *        	insert
     *        	changedAttributes
     */
    protected function processFeed($insert, $changedAttributes) {
        $msg = 'Modified ' . $this->label() . ' ';

        if ($insert)
            $msg = 'Added new ' . $this->label() . ' : ' . $this->linkify();

        if ($this->hasAttribute('id')) {
            $this->updateFeeds($msg);
        }
    }

    public function beforeDelete() {
        if (!parent::beforeDelete()) {
            return false;
        }

        if ($this->hasAttribute('id')) {

            Comment::deleteRelatedAll(array(
                'model_id' => $this->id,
                'model_type' => get_class($this)
            ));
            Feed::deleteRelatedAll(array(
                'model_id' => $this->id,
                'model_type' => get_class($this)
            ));

            File::deleteRelatedAll(array(
                'model_id' => $this->id,
                'model_type' => get_class($this)
            ));
        }
        return true;
    }

    public function updateFeeds($content) {
        if ($this instanceof Feed || \Yii::$app instanceof yii\console\Application)
            return;

        return Feed::add($this, $content);
    }

    public function updateHistory($comment) {
        $model = new Comment();
        $model->model_type = get_class($this);
        $model->model_id = $this->id;
        $model->comment = $comment;
        $model->state_id = Comment::STATE_ACTIVE;
        if (!$model->save()) {
            VarDumper::dump($model->errors);
            return false;
        }
        return true;
    }

    public function getControllerID() {
        if (empty($this->_controllerId)) {
            $admin = '';
            if (!(\Yii::$app instanceof yii\console\Application) && Yii::$app->user->isAdminMode) {
                $adminPath = Yii::$app->controller->module->basePath . DIRECTORY_SEPARATOR . 'controllers/admin';
                if (is_dir($adminPath)) {
                    $admin = 'admin/';
                }
            }
            $modelClass = get_class($this);
            $pos = strrpos($modelClass, '\\');
            $class = substr($modelClass, $pos + 1);
            $this->_controllerId = $admin . Inflector::camel2id($class);
        }
        return $this->_controllerId;
    }

    public function getUrl($action = 'view', $id = null) {
        $params = [
            $this->getControllerID() . '/' . $action
        ];
        if ($id != null) {
            if (is_array($id))
                $params = array_merge($params, $id);
            else
                $params['id'] = $id;
        } elseif ($this->hasAttribute('id')) {
            $params['id'] = $this->id;
        }
        $params['title'] = (string) $this;
        return Yii::$app->getUrlManager()->createAbsoluteUrl($params, true);
    }

    public function linkify($title = null, $controller = null, $action = 'view') {
        if ($title == null)
            $title = (string) $this;
        return Html::a($title, $this->getUrl($action, $controller));
    }

    public function getErrorsString() {
        $out = '';
        if ($this->errors != null)
            foreach ($this->errors as $err) {
                $out .= implode(',', $err);
            }
        return $out;
    }

    public static function getHasOneRelations() {
        $relations = [];
        return $relations;
    }

    public function getRelatedDataLink($key) {
        $hasOneRelations = get_called_class()::getHasOneRelations();
        if (isset($hasOneRelations[$key])) {
            $relation = $hasOneRelations[$key][0];
            if (isset($this->$relation))
                return $this->$relation->linkify();
        }
        return $this->$key;
    }

    public static function deleteRelatedAll($query = []) {
        $models = self::find()->where($query);
        foreach ($models->each() as $model) {
            $model->delete();
        }
    }

    public static function my($attribute = 'created_by_id') {
        return Yii::createObject(ActiveQuery::class, [
                    get_called_class()
                ])->andWhere([
                    $attribute => \Yii::$app->user->id
        ]);
    }

    public function setEncryptedPassword($password) {
        $this->password = utf8_encode(\Yii::$app->security->encryptByPassword($password, \Yii::$app->id));
    }

    public function getDecryptedPassword() {
        $new = \Yii::$app->getSecurity()->decryptByPassword(utf8_decode($this->password), \Yii::$app->id);
        return $new;
    }

    public function isActive() {
        return ($this->state_id == $this::STATE_ACTIVE);
    }

    public static function truncate() {
        $table = get_called_class()::tableName();

        \Yii::$app->db->createCommand()
                ->checkIntegrity(false)
                ->execute();

        echo "Cleaning " . $table . PHP_EOL;
        \Yii::$app->db->createCommand()
                ->truncateTable($table)
                ->execute();

        \Yii::$app->db->createCommand()
                ->checkIntegrity(true)
                ->execute();
    }

    public function checkRelatedData($models = null) {
        if ($models == null)
            $models = get_class()::getHasOneRelations();
        foreach ($models as $key => $class) {
            $class = is_array($class) ? $class[1] : $class;
            if ($class::find()->count() == 0) {
                $this->addError($key, $class::label() . ' atleast 1 record required');
            }
        }
    }

    /**
     * Get number of records created in each month
     *
     * @param integer $state
     * @param integer $created_by_id
     * @param string $dateAttribute
     * @return number[]
     */
    public static function monthly($state = null, $created_by_id = null, $dateAttribute = 'created_on') {
        $date = new \DateTime(date('Y-m'));

        $date->modify('-1 year');

        $count = [];
        $query = self::find();
        for ($i = 1; $i <= 12; $i++) {
            $date->modify('+1 months');
            $month = $date->format('Y-m');

            $query->where([
                'like',
                $dateAttribute,
                $month
            ]);

            if ($created_by_id !== null) {
                $query->andWhere([
                    'created_by_id' => $created_by_id
                ]);
            }

            if ($state !== null) {
                $state = is_array($state) ? $state : [
                    $state
                ];
                $query->andWhere([
                    'in',
                    'state_id',
                    $state
                ]);
            }
            $count[$month] = (int) $query->count();
        }
        return $count;
    }

    public static function daily($state = null, $created_by_id = null, $dateAttribute = 'created_on') {
        $date = new \DateTime();
        $date->modify('-30 days');

        $count = [];
        $query = self::find();
        for ($i = 1; $i <= 30; $i++) {
            $date->modify('+1 days');
            $day = $date->format('m-d');

            $query->where([
                'like',
                $dateAttribute,
                $day
            ]);

            if ($created_by_id !== null) {
                $query->andWhere([
                    'created_by_id' => $created_by_id
                ]);
            }

            if ($state !== null) {
                $state = is_array($state) ? $state : [
                    $state
                ];
                $query->andWhere([
                    'in',
                    'state_id',
                    $state
                ]);
            }
            $count[$day] = (int) $query->count();
        }
        return $count;
    }

    public function getFeeds() {
        return $this->hasMany(Feed::class, [
                    'model_id' => 'id'
                ])->andWhere([
                    'model_type' => get_called_class()
        ]);
    }

    public static function log($strings) {
        if (php_sapi_name() == "cli") {
            echo $strings . PHP_EOL;
        } else {
            Yii::debug($strings);
        }
    }

    /**
     * Get current loggedin User
     *
     * @return number|string|number
     */
    public static function getCurrentUser() {
        if (\Yii::$app instanceof yii\console\Application)
            return 1;
        return Yii::$app->user->id;
    }

    /**
     *
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null) {
        if (!parent::save($runValidation, $attributeNames)) {
            self::log($this->getErrorsString());
            return false;
        }

        return true;
    }

}
