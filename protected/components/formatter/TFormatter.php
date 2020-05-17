<?php

namespace app\components\formatter;

use yii\i18n\Formatter;

class TFormatter extends Formatter {

    public function init() {
        parent::init();
        $this->nullDisplay = '';
    }

    public function asTime($value, $format = null) {
        $out = parent::asTime($value, $format);
        if ($out == '5:30' || $out == '5:30:00 AM') {
            $out = $this->nullDisplay;
        }
        return $out;
    }

}
