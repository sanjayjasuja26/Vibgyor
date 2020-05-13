<?php

namespace app\modules\payment\assets;

use yii\web\AssetBundle;

class PaymentAsset extends AssetBundle {
	public $sourcePath = '@app/modules/payment/themes/css';
	public $css = [ 
			'payment.css' 
	];
	public $js = [ ];
	public $depends = [ 
			'yii\web\JqueryAsset' 
	];
}
