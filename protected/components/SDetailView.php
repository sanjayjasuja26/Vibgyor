<?php

namespace app\components;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

class SDetailView extends DetailView {

    public $columns = null;
    public $responsive = true;
    public $template = "<th>{label}</th><td  colspan = {cols}>{value}</td>";
    public $templateRow = "<tr>{data}</tr>";

    public function init() {
        parent::init();

        if (!isset($this->columns)) {
            if (count($this->attributes) < 8) {
                $this->columns = 1;
            } else {
                $this->columns = 2;
            }
        }
    }

    protected function renderAttribute2($attribute, $index, $cols) {
        if (is_string($this->template)) {
            return strtr($this->template, [
                '{label}' => $attribute['label'],
                '{value}' => $this->formatter->format($attribute['value'], $attribute['format']),
                '{cols}' => $cols
            ]);
        } else {
            return call_user_func($this->template, $attribute, $index, $this);
        }
    }

    public function run() {
        $rows = [];
        $i = 0;
        $j = 0;
        $data2 = [];

        foreach ($this->attributes as $attribute) {
            $data = $this->renderAttribute2($attribute, $i++, 1);
            $len = strlen(strip_tags($data));

            if ($len > 100) {
                $data = $this->renderAttribute2($attribute, $i++, $this->columns * 2 - 1);
                $rows[$j++] = strtr($this->templateRow, [
                    '{data}' => $data
                ]);
            } else {
                $data2[] = $data;

                if (count($data2) >= $this->columns) {
                    $rows[$j++] = strtr($this->templateRow, [
                        '{data}' => implode('', $data2)
                    ]);
                    $data2 = [];
                }
            }
        }
        $rows[$j++] = strtr($this->templateRow, [
            '{data}' => implode('', $data2)
        ]);

        $tag = ArrayHelper::remove($this->options, 'tag', 'table');
        if ($this->responsive)
            echo "<div class='table-responsive'>";
        echo Html::tag($tag, implode("\n", $rows), $this->options);
        if ($this->responsive)
            echo "</div>";
    }

}
