<?php

/**
 * @copyright Copyright &copy; Odai Alali 2014
 * @package yii2-toastr
 * @version 0.1-dev
 */
namespace app\components\toster;

use app\components\toster\ToastrAsset;
use yii\helpers\Json;

/**
 * Description of Toastr
 *
 * @author Odai Alali <odai.alali@gmail.com>
 */
class Toastr extends \yii\base\Widget {
	/*
	 * $options = [
	 * text: "Don't forget to star the repository if you like it.", // Text that is to be shown in the toast
	 * heading: 'Note', // Optional heading to be shown on the toast
	 * icon: 'warning', // Type of toast icon
	 * showHideTransition: 'fade', // fade, slide or plain
	 * allowToastClose: true, // Boolean value true or false
	 * hideAfter: 3000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
	 * stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
	 * position: 'bottom-left', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values
	 *
	 *
	 *
	 * textAlign: 'left', // Text alignment i.e. left, right or center
	 * loader: true, // Whether to show loader or not. True by default
	 * loaderBg: '#9ec600', // Background color of the toast loader
	 * beforeShow: function () {}, // will be triggered before the toast is shown
	 * afterShown: function () {}, // will be triggered after the toat has been shown
	 * beforeHide: function () {}, // will be triggered before the toast gets hidden
	 * afterHidden: function () {} // will be triggered after the toast has been hidden
	 * ];
	 */
	public $options = [ ];
	public function init() {
		parent::init ();
		ToastrAsset::register ( $this->getView () );
	}
	public function run() {
		parent::run ();
		$this->registerNotification ();
	}
	protected function registerNotification() {
		$view = $this->getView ();
		if ($this->options !== false) {
			$js = "$.toast(" . Json::encode ( $this->options ) . ")";
			if (isset ( $js )) {
				$view->registerJs ( $js );
			}
		}
	}
}
