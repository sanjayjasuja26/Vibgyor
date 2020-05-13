<?php
/**
*@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
*@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
*/

/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
namespace app\modules\tugii\controllers;

use Yii;
use yii\web\Controller;
use app\modules\tugii\models\CrudForm;
use app\modules\tugii\components\AppData;
use app\modules\tugii\components\AppFile;
use app\modules\tugii\models\ModelForm;
class DefaultController extends \yii\gii\controllers\DefaultController {
	public function actionIndex() {
		return $this->render ( 'index' );
	}
	/**
	 *
	 * @return string
	 */
	public function actionProcess($post) {
		$list = [ ];
		$db = $post ['db_connection'];
		$model_override = (isset ( $post ['override_models'] )) ? TRUE : FALSE;
		$controller_override = (isset ( $post ['override_controllers'] )) ? TRUE : FALSE;
		/* */
		$appData = new AppData ( $db );
		$appData->models_path = $post ['models_path'];
		$appData->models_search_path = $post ['models_search_path'];
		$appData->controller_path = $post ['controllers_path'];
		
		if ($model_override) {
			$string = trim ( $post ['exclude_models'] );
			$string = preg_replace ( '/[^\w|,]/', '', $string );
			$array = explode ( ',', $string );
			$list = $appData->runModels ( TRUE, $array );
		} else {
			$list = $appData->runModels ( FALSE, [ ] );
		}
		if ($controller_override) {
			$string = trim ( $post ['exclude_controllers'] );
			$string = preg_replace ( '/[^\w|,]/', '', $string );
			$array = explode ( ',', $string );
			$list = $appData->runCrud ( TRUE, $array );
		} else {
			$list = $appData->runCrud ( FALSE, [ ] );
		}
		
		return $this->render ( 'process', [ 
				'list' => $list 
		] );
	}
	public function actionCruds() {
		if (isset ( $_POST ['CrudForm'] )) {
			$post = $_POST ['CrudForm'];
			$list = [ ];
			$db = $post ['db_connection'];
			$model_override = (isset ( $post ['override_models'] )) ? TRUE : FALSE;
			$controller_override = (isset ( $post ['override_controllers'] )) ? TRUE : FALSE;
			/* */
			$appData = new AppData ( $db );
			$appData->models_path = $post ['models_path'];
			$appData->models_search_path = $post ['models_search_path'];
			$appData->controller_path = $post ['controllers_path'];
			
			if ($controller_override) {
				$string = trim ( $post ['exclude_controllers'] );
				$string = preg_replace ( '/[^\w|,]/', '', $string );
				$array = explode ( ',', $string );
				$list = $appData->runCrud ( TRUE, $array );
			} else {
				$list = $appData->runCrud ( FALSE, [ ] );
			}
			
			return $this->render ( 'process', [ 
					'list' => $list 
			] );
		}
		$model = new CrudForm ();
		$model->db_connection = 'db';
		
		$basePath = str_replace ( '/vendor/yiisoft/yii2', '', AppFile::useBackslash ( Yii::getAlias ( '@app' ) ) );
		if (is_dir ( $basePath . '/models' )) {
			$model->models_path = 'app\models';
			if (! is_dir ( $basePath . '/models/search' ))
				mkdir ( $basePath . '/models/search' );
			$model->models_search_path = 'app\models\search';
		} else {
			$model->models_path = 'common\models';
			if (! is_dir ( Yii::getAlias ( '@common' ) . '\models\search' ))
				mkdir ( Yii::getAlias ( '@common' ) . '\models\search' );
			$model->models_search_path = 'common\models\search';
		}
		
		if (is_dir ( $basePath . '/controllers' ))
			$model->controllers_path = 'app\controllers';
		else
			$model->controllers_path = 'frontend\controllers';
		$model->exclude_controllers = 'Migration';
		
		return $this->render ( 'crud-form', [ 
				'model' => $model 
		] );
	}
	public function actionModels() {
		if (isset ( $_POST ['ModelForm'] )) {
			$post = $_POST ['ModelForm'];
			$list = [ ];
			$db = $post ['db_connection'];
			$model_override = (isset ( $post ['override_models'] )) ? TRUE : FALSE;
			
			$appData = new AppData ( $db );
			$appData->models_path = $post ['models_path'];
			$appData->models_search_path = $post ['models_search_path'];
			
			if ($model_override) {
				$string = trim ( $post ['exclude_models'] );
				$string = preg_replace ( '/[^\w|,]/', '', $string );
				$array = explode ( ',', $string );
				$list = $appData->runModels ( TRUE, $array );
			} else {
				$list = $appData->runModels ( FALSE, [ ] );
			}
			
			return $this->render ( 'process', [ 
					'list' => $list 
			] );
		}
		$model = new ModelForm ();
		$model->db_connection = 'db';
		
		$basePath = str_replace ( '/vendor/yiisoft/yii2', '', AppFile::useBackslash ( Yii::getAlias ( '@app' ) ) );
		if (is_dir ( $basePath . '/models' )) {
			$model->models_path = 'app\models';
			if (! is_dir ( $basePath . '/models/search' ))
				mkdir ( $basePath . '/models/search' );
			$model->models_search_path = 'app\models\search';
		} else {
			$model->models_path = 'common\models';
			if (! is_dir ( Yii::getAlias ( '@common' ) . '\models\search' ))
				mkdir ( Yii::getAlias ( '@common' ) . '\models\search' );
			$model->models_search_path = 'common\models\search';
		}
		
		$model->exclude_models = 'User, Migration';
		
		return $this->render ( 'model-form', [ 
				'model' => $model 
		] );
	}
}
