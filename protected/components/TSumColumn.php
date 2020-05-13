<?php

namespace app\components;

use yii\grid\DataColumn;

/**
 *@copyright : Amusoftech Pvt. Ltd. < www.amusoftech.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
class TSumColumn extends DataColumn {
    public function getDataCellValue($model, $key, $index)
    {
        $value = parent::getDataCellValue($model, $key, $index);
        if ( is_numeric($value))
        {
            $this->footer += $value;
        }
        
        return $value;
    }
}