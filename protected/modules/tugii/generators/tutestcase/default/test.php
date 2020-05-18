<?php

/**
 * This is the template for generating a TEST controller class file.
 */
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use app\modules\tugii\generators\tutestcase;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
$modelClass = StringHelper::basename($generator->modelRealClass);
$controllerClass = StringHelper::basename($generator->controllerClass);
$url = Inflector::camel2id($modelClass);

echo "<?php\n";
?>
<?php if ($generator->module != null) : ?>
    namespace <?= $generator->module ?> ;
<?php endif; ?>

class <?= $controllerClass ?> 

{
public $id;
protected $data = [ ];

public function _data(){
<?php
if ($generator->getTableSchema()) {
    $fieldMatch = '/^(id|updated_on|update_time|actual_start|actual_end|password|passcode|activation_key|created_on|create_time|created_by)/i';

    foreach ($generator->getTableSchema()->columns as $column) {
        $attribute = $column->name;

        if (preg_match($fieldMatch, $attribute))
            continue;


        if ($column->allowNull) {
            echo "			/* \$this->data['$attribute'] = " . $generator->getFieldtestdata($column) . "; */\n";
        } else {
            echo "			\$this->data['$attribute'] = " . $generator->getFieldtestdata($column) . ";\n";
        }
    }
}
?>
}

public function _before(AcceptanceTester $I) 
{
Helper::login($I);
}
public function _after(AcceptanceTester $I) {}

public function IndexWorks(AcceptanceTester $I) 
{
$I->amOnPage ( '/<?= $generator->module . $url ?>/index' );
$I->canSeeResponseCodeIs(200);
$I->seeElement ( '.grid-view' );
$this->_data();
}
public function AddFormCanBeSubmittedEmpty(AcceptanceTester $I) 
{
$I->amOnPage ( '/<?= $generator->module . $url ?>/add' );
$I->seeElement ( '#<?= $url ?>-form' );
$I->amGoingTo ( 'add form without credentials' );
$I->click ( '#<?= $url ?>-form-submit' );
$I->canSeeResponseCodeIs(200);
$I->expectTo ( 'see validations errors' );
$req = $I->grabMultiple ( '.required' );
$count = count ( $req );
$I->seeNumberOfElements ( '.has-error', $count );
}
public function AddWorksWithData(AcceptanceTester $I) 
{
$I->amOnPage ( '/<?= $generator->module . $url ?>/add' );
$I->seeElement ( '#<?= $url ?>-form' );
$I->amGoingTo ( 'add form with right data' );

<?php
if ($generator->getTableSchema()) {
    $fieldMatch = '/^(id|updated_on|update_time|actual_start|actual_end|password|passcode|activation_key|created_on|create_time|created_by)/i';

    foreach ($generator->getTableSchema()->columns as $column) {
        $attribute = $column->name;

        if (preg_match($fieldMatch, $attribute))
            continue;


        if ($column->allowNull) {
            echo "			/*" . $generator->generateActiveField($column) . "; */\n";
        } else {
            echo "			" . $generator->generateActiveField($column) . ";\n";
        }
    }
}
?>
$I->click ( '#<?= $url ?>-form-submit' );
$I->canSeeResponseCodeIs(200);
$I->dontseeElement ( '#<?= $url ?>-form' );
$I->see (array_key_exists('title',$this->data)?$this->data['title']:'', 'h1'  );
$I->seeElement ( '.table-bordered');
$this->id = $I->grabFromCurrentUrl ( '/[=\/](\d+)/' );
}

public function ViewWorks(AcceptanceTester $I) 
{
$I->amOnPage ( '/<?= $generator->module . $url ?>/' . $this->id );
$I->amGoingTo ( 'View <?= $url ?> details' );
$I->canSeeResponseCodeIs(200);
$I->see ( array_key_exists('title',$this->data)?$this->data['title']:'', 'h1' );
$I->seeElement ( '.table-bordered');
}
public function UpdateWorks(AcceptanceTester $I) 
{
$I->amOnPage ( '/<?= $generator->module . $url ?>/update/'. $this->id  );
$I->seeElement ( '#<?= $url ?>-form' );
$I->amGoingTo ( 'add form with right data' );

<?php
if ($generator->getTableSchema()) {
    $fieldMatch = '/^(id|updated_on|update_time|actual_start|actual_end|password|passcode|activation_key|created_on|create_time|created_by)/i';

    foreach ($generator->getTableSchema()->columns as $column) {
        $attribute = $column->name;

        if (preg_match($fieldMatch, $attribute))
            continue;

        if ($column->allowNull) {
            echo "			/*" . $generator->generateActiveField($column) . "; */\n";
        } else {

            echo "			" . $generator->generateActiveField($column) . ";\n";
        }
    }
}
?>
$I->click ( '#<?= $url ?>-form-submit' );
$I->canSeeResponseCodeIs(200);
$I->dontseeElement ( '#<?= $url ?>-form' );
$I->see (array_key_exists('title',$this->data)?$this->data['title']:'', 'h1'  );
$I->seeElement ( '.table-bordered');
}
public function DeleteWorks(AcceptanceTester $I) 
{
$I->sendAjaxPosSRequest ( '/<?= $generator->module . $url ?>/delete/' . $this->id );
$I->expectTo ( 'delete <?= $url ?> works' );
$I->amOnPage ( '/<?= $generator->module . $url ?>/' . $this->id );
$I->canSeeResponseCodeIs(404);
}

<?php
$actions = $generator->getActions($generator->modelClass);
foreach ($actions as $action) {

    if (in_array($action, [
                'Index',
                'Add',
                'View',
                'Update',
                'Delete',
                'Ajax',
                'Pdf'
            ]))
        continue;
    ?>

    public function <?= $action ?>Works(AcceptanceTester $I) 
    {
    $I->amOnPage ( '/<?= $generator->module . $url ?>/<?= Inflector::camel2id($action) ?>' . $this->id );
    $I->amGoingTo ( 'check  <?= $action ?> works ' );
    $I->canSeeResponseCodeIs(200);
    //$I->see ( 'title', 'h1' );
    }

<?php } ?>

}
