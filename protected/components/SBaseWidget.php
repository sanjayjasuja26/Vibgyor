<?php

namespace app\components;

use Yii;

class SBaseWidget extends \yii\base\Widget {

    public $route;
    public $params;
    public $visible;

    public function init() {
        parent::init();
        if (!isset($this->visible)) {
            $this->visible = true;
        }
    }

    public function run() {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        if ($this->visible) {
            $this->renderHtml();
        }
    }

    public function renderHtml() {
        
    }

}
