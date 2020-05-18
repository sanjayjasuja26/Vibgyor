<?php
/**
*@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
*@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
*/

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
/**
 *
 * @var View $this
 * @var Generator $generator
 */

$urlParams = $generator->generateUrlParams ();
$nameAttribute = $generator->getNameAttribute ();

echo "<?php\n";
?>

use app\components\SGridView;
use <?=$generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView"?>;
<?=$generator->enablePjax ? 'use yii\widgets\Pjax;' : ''?>

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var <?=ltrim ( $generator->searchModelClass, '\\' )?> $searchModel
 */

?>
<?php $class=Inflector::camel2id ( StringHelper::basename ( $generator->modelClass ) );
$pjax=$class."-pjax-ajax-grid";
?>
<?=$generator->enablePjax ? "<?php Pjax::begin(['id'=>'".$pjax."','enablePushState'=>false,'enableReplaceState'=>false]); ?>" : ''?>

<?php

if ($generator->indexWidgetType === 'grid') :
	?>
    <?="<?php echo "?>SGridView::widget([
    	'id' => '<?=$class?>-ajax-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions'=>['class'=>'table table-bordered'],
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn','header'=>'<a>S.No.<a/>'],

<?php
	$modelClass = $generator->modelClass;
	$hasOneRelations = $modelClass::getHasOneRelations ();
	$count = 0;
	$fieldMatch = '/^(updated_on|update_time|actual_start|actual_end|password|passcode|activation_key)/i';
	if (($tableSchema = $generator->getTableSchema ()) === false) {

		foreach ( $generator->getColumnNames () as $name ) {
			if (preg_match ( $fieldMatch, $name ))
				echo "            // '" . $name . "',\n";

			elseif ($count < 8) {
				$count ++;
				echo "            '" . $name . "',\n";
			} else {
				echo "            // '" . $name . "',\n";
			}
		}
	} else {
		foreach ( $tableSchema->columns as $column ) {
			if (isset ( $hasOneRelations [$column->name] )) {
				$column_out = "[" . "
				'attribute' => '$column->name',
				'format'=>'raw',
				'value' => function (\$data) { return \$data->getRelatedDataLink('$column->name');  },
				" . "]";
			} else {
				$column_out = $generator->generateGridViewColumn ( $column );
			}

			if (preg_match ( $fieldMatch, $column->name ) || $column->type === 'text' || $column->allowNull)
				echo "            /* " . $column_out . ",*/\n";

			elseif ($count < 8) {
				$count ++;
				echo "            " . $column_out . ",\n";
			} else {
				echo "            /* " . $column_out . ",*/\n";
			}
		}
	}
	?>

            ['class' => 'app\components\SActionColumn','header'=>'<a>Actions</a>'],
        ],
    ]); ?>
<?php

else :
	?>

    <?="<?= "?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?=$nameAttribute?>), ['view', <?=$urlParams?>]);
        },
    ]) ?>
<?php

endif;
?>
<?=$generator->enablePjax ? '<?php Pjax::end(); ?>' : ''?>


