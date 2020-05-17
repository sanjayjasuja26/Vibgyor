<?php

namespace app\components;

use Mpdf\Mpdf;
use yii\helpers\ArrayHelper;

class TPdfWriter extends Mpdf {

    public $data = [];

    public function __construct($data = null) {
        $this->data['tempDir'] = \Yii::getAlias('@runtime');

        if (!empty($data) && is_array($data)) {

            $this->data = ArrayHelper::merge($this->data, $data);
        }

        parent::__construct($this->data);
        $this->SetProtection([
            'print'
        ]);
    }

}

?>