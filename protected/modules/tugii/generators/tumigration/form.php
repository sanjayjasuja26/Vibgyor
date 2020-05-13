<?php
/**
*@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
*@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
*/
 
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */
echo $form->field ( $generator, 'migrateName' );
echo $form->field ( $generator, 'sql_up' )->textarea ();
echo $form->field ( $generator, 'enableDown' )->checkbox ();
echo $form->field ( $generator, 'sql_down' )->textarea ();
echo $form->field ( $generator, 'clearCache' )->checkbox ();
echo $form->field ( $generator, 'clearAssets' )->checkbox ();

?>
