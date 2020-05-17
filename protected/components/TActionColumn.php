<?php

namespace app\components;

use Yii;
use yii\helpers\Html;

class TActionColumn extends \yii\grid\ActionColumn {

    public $showModal;

    function init() {
        $this->showModal = \Yii::$app->params['useCrudModals'];
        parent::init();
        $this->initDefaultButtons();
        $this->urlCreator = function ($action, $model, $key, $index) {
            return $model->getUrl($action);
        };
    }

    protected function initDefaultButtons() {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('app', 'view'),
                    'aria-label' => Yii::t('app', 'View'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-success btn-green',
                    'data-id' => $model->id
                        ], $this->buttonOptions);
                if ($this->showModal) {
                    $options = array_merge($options, [
                        'class' => 'showActionModalButton btn btn-success',
                        'value' => $url
                    ]);
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', false, $options);
                }
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('app', 'Update'),
                    'aria-label' => Yii::t('app', 'Update'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-info btn-blue-info',
                    'data-id' => $model->id
                        ], $this->buttonOptions);
                if ($this->showModal) {
                    $options = array_merge($options, [
                        'class' => 'showActionModalButton btn btn-success',
                        'value' => $url
                    ]);
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', false, $options);
                }
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('app', 'Delete'),
                    'aria-label' => Yii::t('app', 'Delete'),
                    'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'data-url' => $url,
                    'class' => 'btn btn-danger btn-red',
                    'data-id' => $model->id
                        ], $this->buttonOptions);
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
            };
        }
    }

}
