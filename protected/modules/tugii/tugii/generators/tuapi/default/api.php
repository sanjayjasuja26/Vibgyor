<?php
/**
*@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
*@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
*/
/**
 * This is the template for generating a CRUD controller class file.
 */
use yii\filters\AccessControl;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename ( $generator->controllerClass );
$modelClass = StringHelper::basename ( $generator->modelClass );
/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey ();
$urlParams = $generator->generateUrlParams ();
$actionParams = $generator->generateActionParams ();

echo "<?php\n";
?>
 
namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use yii\rest\ActiveController;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use <?= ltrim($generator->modelClass, '\\') ?>;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->baseControllerClass, '\\') ?>;

/**
 * <?= $controllerClass ?> implements the API actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n"?>
{
  public function behaviors()
	{
		return [
				'access' => [
						'class' => AccessControl::className(),
						'ruleConfig' => [
								'class' => AccessRule::className(),
						],
						'rules' => [
								[
										'actions' => ['index','add','get','update','delete'],
										'allow' => true,
										'roles' => ['@'],
								],
								[
										'actions' => ['index','get','update'],
										'allow' => true,
										'roles' => ['?', '*'],
								],
						],
				],
		];
	}
  
    
    
    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {

   		return $this->txindex ("<?= $generator->modelClass ?>" );

    }

    /**
     * Displays a single <?= $generator->modelClass ?> model.
     * @return mixed
     */
    public function actionGet(<?= $actionParams ?>)
    {
        return $this->txget ( $id,"<?= $generator->modelClass ?>" );
	 }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd()
    {
    return $this->txSave("<?= $generator->modelClass ?>");
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
    		$data = [ ];
			$model=$this->findModel($id);	
	        if ($model->load(Yii::$app->request->post())) {
	            
	            if ($model->save()) {

	            $data ['status'] = self::API_OK;

				$data ['detail'] = $model;
	                
	                
	            } else {
	                $data['error'] = $model->flattenErrors;
	            }
	        } else {
	            $data['error_post'] = 'No Data Posted';
	        }
	        
	        return $this->sendResponse ( $data );
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionDelete(<?= $actionParams ?>)
    {
    return $this->txDelete( $id,"<?= $generator->modelClass ?>" );
       
    }

   
}
