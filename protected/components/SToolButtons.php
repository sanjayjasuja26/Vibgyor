<?php

namespace app\components;

use Closure;
use Yii;
use yii\helpers\Html;

class SToolButtons extends SBaseWidget {

    public $title;
    public $htmlOptions;

    /**
     *
     * @var string Current action id.
     */
    public $actionId;

    /**
     *
     * @var array List of buttons actions. ('index', 'add', 'update', 'delete')
     *     
     */
    public $actions = [
        'add' => '<span class="glyphicon glyphicon-plus"></span>',
        'update' => '<span class="glyphicon glyphicon-pencil"></span>',
        'view' => '<span class="glyphicon glyphicon-eye-open"></span>',
        'delete' => '<span class="glyphicon glyphicon-trash"></span>'
    ];

    /**
     *
     * @var Closure Function can check whether action exist or not.
     */
    public $hasAction;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        if (!$this->actionId) {
            $this->actionId = Yii::$app->controller->action->id;
        }
    }

    /**
     * @inheritdoc
     */
    public function run() {
        echo '<div class="text-right">';

        if (is_array(\Yii::$app->controller->menu))
            foreach (\Yii::$app->controller->menu as $key => $menu) {

                if (!$this->hasAction($menu['url']))
                    continue;

                if (isset($this->actions[$key])) {

                    if ($key == 'clean') {

                        $menu['class'] = 'btn btn-warning';
                    } elseif ($key == 'delete') {

                        $menu['class'] = 'btn btn-danger';

                        $menu['label'] = $this->actions[$key];
                        $menu['htmlOptions']['data'] = [
                            'method' => 'POST',
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this ?')
                        ];
                    }
                    if (!isset($menu['label'])) {
                        $menu['label'] = $this->actions[$key];
                    }
                }
                $visible = true;
                if (isset($menu['visible']))
                    if ($menu['visible'] == true)
                        $visible = true;
                    else
                        $visible = false;
                $this->htmlOptions = [
                    'class' => isset($menu['class']) ? $menu['class'] : 'btn btn-warning',
                    'title' => isset($menu['title']) ? $menu['title'] : $menu['label'],
                    'id' => isset($menu['id']) ? $menu['id'] : "tool-btn-" . $key
                ];
                if (isset($menu['htmlOptions']))
                    $this->htmlOptions = array_merge($menu['htmlOptions'], $this->htmlOptions);
                if ($visible)
                    echo ' ' . Html::a($menu['label'], $menu['url'], $this->htmlOptions);
            }
        echo '</div>';
    }

    /**
     * Returns a value indicating whether a controller action is defined.
     *
     * @param string $id
     *            Action id
     * @return bool
     */
    protected function hasAction($url) {

        /*
         * if ( is_array($url))
         * if ( $id == 'manage') $id = 'index';
         *
         * $actionMap = ->actions ();
         *
         * if (isset ( $actionMap [$id] )) {
         * return true;
         * } else {
         * $action = Inflector::id2camel($id);
         * $methodName = 'action' . ucfirst($action);
         * if (method_exists ( Yii::$app->controller, $methodName )) {
         * return true;
         * }
         * }
         *
         * return false;
         */
        return true;
    }

}
