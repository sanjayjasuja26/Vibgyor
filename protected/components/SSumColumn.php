<?php

namespace app\components;

use yii\grid\DataColumn;

class SSumColumn extends DataColumn {

    public function getDataCellValue($model, $key, $index) {
        $value = parent::getDataCellValue($model, $key, $index);
        if (is_numeric($value)) {
            $this->footer += $value;
        }

        return $value;
    }

}
