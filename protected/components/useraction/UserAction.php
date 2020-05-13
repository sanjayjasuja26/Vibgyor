<?php
namespace app\components\useraction;

use app\components\TBaseWidget;
use app\models\User;
use Yii;

class UserAction extends TBaseWidget
{

    public $model;

    public $attribute;

    public $states;

    public $actions;

    public $allowed;

    public $buttons;

    public $visible;

    public $title;

    public $style = 'button-action';

    public function getButtonColor($id)
    {
        $list = [
            'New' => "btn-primary",
            'Active' => "btn-success",
            'Deleted' => "btn-danger"
        ];

        if (isset($list[$id]))
            return $list[$id];
        return "btn-primary";
    }

    public function init()
    {
        if (! isset($this->visible)) {
            $this->visible = $this->model->isAllowed();
        }
        if (! $this->model->hasAttribute($this->attribute)) {
            $this->visible = false;
            return;
        }
        if (empty($this->actions))
            $this->actions = $this->states;

        if (empty($this->allowed)) {
            $this->allowed = $this->actions;
            $this->allowed[$this->model->{$this->attribute}] = null;
            $this->allowed = array_filter($this->allowed);
        }
        if (method_exists($this->model, 'getActionOptions')) {
            $this->buttons = $this->model->getActionOptions();
        } else {
            $this->buttons = $this->actions;
        }

        $this->title = 'Change State';

        parent::init();
    }

    public function renderHtml()
    {
        if ($this->visible == false)
            return;

        if (isset($_POST['workflow'])) {
            $submit = trim($_POST['workflow']);
            $state_list = $this->states;
            $actions = $this->actions;
            $allowed = $this->allowed;

            $state_id = $submit; // array_search($submit, $actions);

            $ok = array_search($submit, $allowed);

            if ($ok >= 0 && $state_id >= 0 && $state_id != $this->model->{$this->attribute}) {
                $old_state = $state_list[$this->model->{$this->attribute}];
                $new_state = $state_list[$state_id];

                $this->model->{$this->attribute} = $state_id;
                if ($this->model->isAllowed() && $this->model->save()) {
                    \Yii::$app->session->setFlash('user-action', 'State Changed.');
                    $msg = 'State Changed : ' . $old_state . ' to ' . $new_state;
                    $this->model->updateHistory($msg);
                    \Yii::$app->session->setFlash('user-action', $msg);
                    
                    \Yii::$app->controller->redirect($this->model->getUrl());
                } else {
                    $error = 'You are not allowed to perform this operation.' . $this->model->getErrorsString();

                    \Yii::$app->session->setFlash('user-action', $error);
                }
            }
        }

        
        if (! empty($this->model))
            echo $this->render($this->style, [
                'model' => $this->model,
                'allowed' => $this->allowed,
                'buttons' => $this->buttons,
                'attribute' => $this->attribute,
                'title' => $this->title
            ]);
    }
}