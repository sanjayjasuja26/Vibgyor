<?php

/**
 * This is the template for generating a CRUD controller class file.
 */
use app\models\User;
use yii\filters\AccessControl;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
    use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
    use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use app\models\User;
use yii\web\HttpException;
use app\components\SActiveForm;
/**
* <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
*/
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
public function behaviors() {
return [
'access' => [
'class' => AccessControl::className (),
'ruleConfig' => [
'class' => AccessRule::className ()
],
'rules' => [
[
'actions' => [
'clear'
],
'allow' => true,
'matchCallback' => function () {
return User::isAdmin();
}
],
[
'actions' => [
'index',
'add',
'view',
'update',
'delete',
'ajax',
'mass'
],
'allow' => true,
'roles' => [
'@'
]
],
[
'actions' => [

'view',
],
'allow' => true,
'roles' => [
'?',
'*'
]
]
]
],
'verbs' => [
'class' => \yii\filters\VerbFilter::className (),
'actions' => [
'delete' => [
'post'
],
]
]
];
}


/**
* Lists all <?= $modelClass ?> models.
* @return mixed
*/
public function actionIndex()
{
<?php if (!empty($generator->searchModelClass)): ?>
    $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $this->updateMenuItems();
    return $this->render('index', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    ]);
<?php else: ?>
    $dataProvider = new ActiveDataProvider([
    'query' => <?= $modelClass ?>::find(),
    ]);
    $this->updateMenuItems();
    return $this->render('index', [
    'dataProvider' => $dataProvider,
    ]);
<?php endif; ?>
}

/**
* Displays a single <?= $modelClass ?> model.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return mixed
*/
public function actionView(<?= $actionParams ?>)
{
$model = $this->findModel(<?= $actionParams ?>);
$this->updateMenuItems($model);
return $this->render('view', ['model' => $model]);

}

/**
* Creates a new <?= $modelClass ?> model.
* If creation is successful, the browser will be redirected to the 'view' page.
* @return mixed
*/
public function actionAdd()
{
$model = new <?= $modelClass ?>();
$model->loadDefaultValues();
$model->state_id = <?= $modelClass ?>::STATE_ACTIVE;
$post = \yii::$app->request->post ();
if (\yii::$app->request->isAjax && $model->load ( $post )) {
\yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
return SActiveForm::validate ( $model );
}
if ($model->load($post) && $model->save()) {
return $this->redirect($model->getUrl());
}
$this->updateMenuItems();
return $this->render('add', [
'model' => $model,
]);

}

/**
* Updates an existing <?= $modelClass ?> model.
* If update is successful, the browser will be redirected to the 'view' page.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return mixed
*/
public function actionUpdate(<?= $actionParams ?>)
{
$model = $this->findModel(<?= $actionParams ?>);

$post = \yii::$app->request->post ();
if (\yii::$app->request->isAjax && $model->load ( $post )) {
\yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
return SActiveForm::validate ( $model );
}
if ($model->load($post) && $model->save()) {
return $this->redirect($model->getUrl());
}
$this->updateMenuItems($model);
return $this->render('update', [
'model' => $model,
]);

}

/**
* Deletes an existing <?= $modelClass ?> model.
* If deletion is successful, the browser will be redirected to the 'index' page.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return mixed
*/
public function actionDelete(<?= $actionParams ?>)
{
$model = $this->findModel(<?= $actionParams ?>);

$model->delete();
return $this->redirect(['index']);
}
/**
* Truncate an existing <?= $modelClass ?> model.
* If truncate is successful, the browser will be redirected to the 'index' page.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return mixed
*/
public function actionClear($truncate = true)
{
$query = <?= $modelClass ?>::find();
foreach ($query->each() as $model) {
$model->delete();
}
if ($truncate) {
<?= $modelClass ?>::truncate();
}
\Yii::$app->session->setFlash('success', '<?= $modelClass ?> Cleared !!!');
return $this->redirect([
'index'
]);
}

/**
* Finds the <?= $modelClass ?> model based on its primary key value.
* If the model is not found, a 404 HTTP exception will be thrown.
* <?= implode("\n     * ", $actionParamComments) . "\n" ?>
* @return <?= $modelClass ?> the loaded model
* @throws NotFoundHttpException if the model cannot be found
*/
protected function findModel(<?= $actionParams ?>, $accessCheck = true)
{
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition [] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {

if ($accessCheck && ! ($model->isAllowed ()))
throw new HttpException ( 403, Yii::t ( 'app', 'You are not allowed to access this page.' ) );

return $model;
} else {
throw new NotFoundHttpException('The requested page does not exist.');
}
}
protected function updateMenuItems($model = null) {

switch (\Yii::$app->controller->action->id) {

case 'add' :
{
$this->menu ['manage'] = [
'label' => '<span class="glyphicon glyphicon-list"></span>',
'title' => Yii::t ( 'app', 'Manage' ),
'url' => [
'index'
],
//	'visible' => User::isAdmin ()
];
}
break;
case 'index' :
{
$this->menu ['add'] = [
'label' => '<span class="glyphicon glyphicon-plus"></span>',
'title' => Yii::t ( 'app', 'Add' ),
'url' => [
'add'
],
//	'visible' => User::isAdmin ()
];
$this->menu['clear'] = [
'label' => '<span class=" glyphicon glyphicon-remove"></span>',
'title' => Yii::t('app', 'Clear'),
'url' => [
'clear'
],
'visible' => User::isAdmin()
];
}
break;
case 'update' :
{
$this->menu ['add'] = [
'label' => '<span class="glyphicon glyphicon-plus"></span>',
'title' => Yii::t ( 'app', 'add' ),
'url' => [
'add'
],
//	'visible' => User::isAdmin ()
];
$this->menu ['manage'] = [
'label' => '<span class="glyphicon glyphicon-list"></span>',
'title' => Yii::t ( 'app', 'Manage' ),
'url' => [
'index'
],
//	'visible' => User::isAdmin ()
];
}
break;
default :
case 'view' :
{
$this->menu ['manage'] = [
'label' => '<span class="glyphicon glyphicon-list"></span>',
'title' => Yii::t ( 'app', 'Manage' ),
'url' => [
'index'
],
//	'visible' => User::isAdmin ()
];
if ($model != null)
{
$this->menu ['update'] = [
'label' => '<span class="glyphicon glyphicon-pencil"></span>',
'title' => Yii::t ( 'app', 'Update' ),
'url' => $model->getUrl('update'),
//		'visible' => User::isAdmin ()
];
$this->menu ['delete'] = [
'label' => '<span class="glyphicon glyphicon-trash"></span>',
'title' => Yii::t ( 'app', 'Delete' ),
'url' => $model->getUrl('delete')
//	    'visible' => User::isAdmin ()
];
}
}
}

}
}
