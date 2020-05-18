<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use app\components\useraction\UserAction;
use app\modules\comment\widgets\CommentsWidget;
/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

/*$this->title =  $model->label() .' : ' . $model-><?= $generator->getNameAttribute() ?>; */
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = (string)$model;
?>

<div class="wrapper">
    <div class=" panel ">

        <div
            class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view panel-body">
            <?= "<?php echo " ?> \app\components\PageHeader::widget(['model'=>$model]); ?>



        </div>
    </div>

    <div class=" panel ">
        <div class=" panel-body ">
            <?= "<?php echo " ?>\app\components\SDetailView::widget([
            'id'	=> '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-detail-view',
            'model' => $model,
            'options'=>['class'=>'table table-bordered'],
            'attributes' => [
            <?php
            $classname = $generator->modelClass;
            $hasOneRelations = $classname::getHasOneRelations();

            if (($tableSchema = $generator->getTableSchema()) === false) {
                foreach ($generator->getColumnNames() as $name) {

                    if (isset($hasOneRelations [$name]))
                        $name = $hasOneRelations [$name] [0];
                    if (preg_match('/^(description|content|password|activation_key)/i', $name))
                        echo "           /* '" . $name . "',*/\n";
                    else
                        echo "            '" . $name . "',\n";
                }
            } else {

                foreach ($tableSchema->columns as $column) {
                    if (isset($hasOneRelations [$column->name])) {
                        $column_out = "[" . "
			'attribute' => '$column->name',
			'format'=>'raw',
			'value' => \$model->getRelatedDataLink('$column->name'),
			" . "]";
                    } else {
                        $column_out = $generator->generateDetailViewColumn($column);
                    }
                    if (preg_match('/^(description|content|password|activation_key)/i', $column->name))
                        echo "            /*" . $column_out . ",*/\n";
                    else
                        echo "            " . $column_out . ",\n";
                }
            }
            ?>
            ],
            ]) ?>


<?php
echo "<?php  ";

foreach ($tableSchema->columns as $column) {
    $column_out = $generator->generateDetailViewColumn($column);
    if (preg_match('/^(description|content)/i', $column->name))
        echo 'echo $model->' . $column->name . ';';
}
?>?>


            <?= "<?php" ?>
            echo UserAction::widget ( [
            'model' => $model,
            'attribute' => 'state_id',
            'states' => $model->getStateOptions ()
            ] );
            ?>

        </div>
    </div>
<?php
$classname = $generator->modelClass;

if (count($classname::getHasManyRelations()) != 0) {
    ?>



        <div class=" panel ">
            <div class=" panel-body ">
                <div
                    class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-panel">

    <?php
    echo "<?php\n";
    ?>
                    $this->context->startPanel();
                    <?php
                    foreach ($classname::getHasManyRelations() as $field => $relationClass) {
                        ?>
                        $this->context->addPanel('<?= ucfirst($relationClass [0]) ?>', '<?= $relationClass [0] ?>', '<?= $relationClass [1] ?>',$model);
                        <?php
                    }
                    ?>

                    $this->context->endPanel();
                    ?>
                </div>
            </div>
        </div>
    <?php
}
?>

    <div class=" panel ">
        <div class=" panel-body ">

    <?= "<?php echo " ?>CommentsWidget::widget(['model'=>$model]); ?>
        </div>
    </div>
</div>
