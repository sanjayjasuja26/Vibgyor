<?php

namespace app\modules\installer\command;

class RemoveController extends InstallController {

    public function actionIndex() {
        return $this->actionRemove();
    }

    public function actionModule() {
        return $this->actionRemoveModule();
    }

}
