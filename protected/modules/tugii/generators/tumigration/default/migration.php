<?php
/**
 * This is the template for generating the model class of a specified table.
 */
/* @var $this yii\web\View */
/* @var $generator spanjeta\tugii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
echo "<?php\n";
?>




use <?= 'yii\db\Migration' ?>;

class <?= $migrateName ?> extends <?= 'Migration' ?>
{
public function safeUp()
{
<?php if ($sql_up): ?>
    $this->execute("<?php echo ($sql_up); ?>");
<?php endif; ?>
}

public function safeDown()
{

<?php if (!$enableDown): ?>
    echo "<?php echo $migrateName; ?> migrating down by doing nothing....\n";
<?php else: ?>
    $this->execute("<?php echo ($sql_down); ?>");
<?php endif; ?>

}
}