<?php
use yii\helpers\Html;
use app\components\SActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\media\models\File */
/* @var $form yii\widgets\ActiveForm */



?>
<header class="panel-heading">
    <?php echo strtoupper(Yii::$app->controller->action->id); ?>
</header>
<?php
echo app\modules\media\widgets\FileUploaderWidget::widget(['id' => 'abcd']);
?>

<?php
echo app\modules\media\widgets\FileUploaderWidget::widget(['id' => 'sdsd']);
?>