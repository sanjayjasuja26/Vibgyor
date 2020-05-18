<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

/* $this->title = <?= $generator->generateString('Add') ?>;*/
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Add') ?>;
?>

<div class="wrapper">
    <div class="panel">

        <div
            class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">
            <?= "<?= " ?> \app\components\PageHeader::widget(); ?>
        </div>

    </div>

    <div class="content-section clearfix panel">

        <?= "<?= " ?>$this->render ( '_form', [ 'model' => $model ] )?></div>
</div>


