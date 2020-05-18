<?php

namespace app\components\notice;

use app\components\SBaseWidget;
use app\models\Notice;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is just an example.
 */
class Notices extends SBaseWidget {

    public $model;
    public $disabled = false;

    protected function getRecentNotices() {

        $query = Notice::findActive()->orderBy('id DESC');

        if ($query->count() == 0)
            return null;

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }

    public function run() {
        if ($this->disabled)
            return; // Do nothing

        if (\Yii::$app->user->isGuest)
            return;

        if ($this->model == null)
            $this->model = Yii::$app->user->identity;

        $notices = $this->getRecentNotices();
        if ($notices == null)
            return;

        return $this->render('notices', [
                    'notices' => $notices
        ]);
    }

}
