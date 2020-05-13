<?php

/**
 * @copyright Copyright &copy; Odai Alali 2014
 * @package yii2-toastr
 * @version 0.1-dev
 */
namespace app\components\toster;

use yii\web\AssetBundle;

/**
 * Description of ToastrAsset
 *
 * @author Odai Alali <odai.alali@gmail.com>
 */
class ToastrAsset extends AssetBundle {
	public $sourcePath = '@webroot/protected/components/toster/assets';
	public $css = [ 
			'css/jquery.toast.css' 
	];
	public $js = [ 
			'js/jquery.toast.js' 
	];
	public $depends = [ 
			'yii\web\JqueryAsset' 
	];
}
