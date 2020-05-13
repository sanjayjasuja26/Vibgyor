<?php
/**
 *@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
 *@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
 */
use yii\helpers\VarDumper;

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

 

/**
* This is the model class for table "<?=$tableName?>".
*
<?php

$columns = $tableSchema->columns;
?>
<?php

foreach ($columns as $column) :
    ?>
    * @property <?="{$column->phpType} \${$column->name}\n"?>
<?php
endforeach
;
?>
<?php

if (! empty($relations)) :
    ?>

* === Related data ===
    <?php
    
    foreach ($relations as $name => $relation) :
        ?>
* @property <?=$relation [1] . ($relation [2] ? '[]' : '') . ' $' . lcfirst ( $name ) . "\n"?>
    <?php
    endforeach
    ;
    ?>
<?php endif;

?>
*/

namespace <?=$generator->ns?>;

use Yii;
<?php
$hasDone = [];
foreach ($relations as $name => $relation) {
    if (! in_array($relation[1], $hasDone)) {
        echo "use $generator->appNs\\$relation[1];\n";
    }
    $hasDone[] = $relation[1];
}
?>

use yii\helpers\ArrayHelper;

class <?=$className?> extends <?='\\' . ltrim ( $generator->baseClass, '\\' ) . "\n"?>
{
<?php
$representing = array_keys($columns)[1];
foreach ($columns as $column) {
    if (preg_match('/^(name|title)/i', $column->name))
        $representing = $column->name;
}
?>
	public  function __toString()
	{
		return (string)$this-><?=$representing?>;
	}
<?php

foreach ($columns as $column) :
    if (preg_match('/^(status_id|state_id)/i', $column->name)) :
        ?>
	const STATE_INACTIVE 	= 0;
	const STATE_ACTIVE	 	= 1;
	const STATE_DELETED 	= 2;

	public static function getStateOptions()
	{
		return [
				self::STATE_INACTIVE		=> "New",
				self::STATE_ACTIVE 			=> "Active" ,
				self::STATE_DELETED 		=> "Archived",
		];
	}
	public function getState()
	{
		$list = self::getStateOptions();
		return isset($list [$this-><?=$column->name?>])?$list [$this-><?=$column->name?>]:'Not Defined';

	}
	public function getStateBadge()
	{
		$list = [
				self::STATE_INACTIVE 		=> "primary",
				self::STATE_ACTIVE 			=> "success" ,
				self::STATE_DELETED 		=> "danger",
		];
		return isset($list[$this-><?=$column->name?>])?\yii\helpers\Html::tag('span', $this->getState(), ['class' => 'label label-' . $list[$this-><?=$column->name?>]]):'Not Defined';
	}


	<?php endif;
    
    if (preg_match('/(_id)$/i', $column->name)) :
        
        if (! preg_match('/(state_id|created_by)/i', $column->name)) {
            
            $key = $generator->getCamelCaseColumn($column->name);
            
            ?>public static function get<?=$key?>Options()
	{
		return ["TYPE1","TYPE2","TYPE3"];
		//return ArrayHelper::Map ( <?= $key ?>::findActive ()->all (), 'id', 'title' );

	}

	<?php if ( !isset($relations[$key]) ){?>
 	public function get<?=$key?>()
	{
		$list = self::get<?=$key?>Options();
		return isset($list [$this-><?=$column->name?>])?$list [$this-><?=$column->name?>]:'Not Defined';

	}
	<?php }?>
	<?php }?>
	<?php endif;
    
    if (preg_match('/(_on|_date|_time|user_id|manager_id|created_by)/i', $column->name)) {
        $valid_columns[] = $column;
    }
endforeach
;
?>
<?php

if (! empty($valid_columns)) :
    ?>public function beforeValidate()
	{
		if($this->isNewRecord)
		{
	<?php
    
    foreach ($valid_columns as $column) :
        ?>
	<?php
        
        if (preg_match('/(updated_on|approve_time|update_time|create_time|created_on)/i', $column->name)) {
            ?>		if ( !isset( $this-><?php
            
            echo $column->name?> )) $this-><?php
            
            echo $column->name?> = date( 'Y-m-d H:i:s');
	<?php
        }
        if (preg_match('/(manager_id|user_id|created_by)/i', $column->name)) {
            ?>		if ( !isset( $this-><?php
            
            echo $column->name?> )) $this-><?php
            
            echo $column->name?> = Yii::$app->user->id;
	<?php
        }
        if (preg_match('/(_date)/i', $column->name)) {
            ?>		if ( !isset( $this-><?php
            
            echo $column->name?> )) $this-><?php
            
            echo $column->name?> = date( 'Y-m-d');
	<?php
        }
    endforeach
    ;
    ?>
		}else{
	<?php
    
    foreach ($valid_columns as $column) :
        ?>
	<?php
        
        if (preg_match('/(updated_on|approve_time|update_time)/i', $column->name)) {
            ?>		$this-><?php
            
            echo $column->name?> = date( 'Y-m-d H:i:s');
<?php
        }
    endforeach
    ;
    ?>
		}
		return parent::beforeValidate();
	}
<?php endif;

?>


	/**
	* @inheritdoc
	*/
	public static function tableName()
	{
		return '<?=$generator->generateTableName ( $tableName )?>';
	}
<?php

if ($generator->db !== 'db') :
    ?>

    /**
    * @return \yii\db\Connection the database connection used by this AR class.
    */
    public static function getDb()
    {
    	return Yii::$app->get('<?=$generator->db?>');
    }
<?php endif;

?>

	/**
	* @inheritdoc
	*/
	public function rules()
	{
		return [<?="\n            " . implode ( ",\n            ", $rules ) . "\n        "?>];
	}

	/**
	* @inheritdoc
	*/


	public function attributeLabels()
	{
		return [
		<?php

foreach ($labels as $name => $label) :
    ?>
		    <?="'$name' => " . $generator->generateString ( $label ) . ",\n"?>
		<?php
endforeach
;
?>
		];
	}
<?php

foreach ($relations as $name => $relation) :
    ?>

    /**
    * @return \yii\db\ActiveQuery
    */
    public function get<?=$name?>()
    {
    	<?=$relation [0] . "\n"?>
    }
<?php
endforeach
;
?>
<?php

?>
    public static function getHasManyRelations()
    {
    	$relations = [];
<?php

if (isset($relationsList['hasMany']))
    foreach ($relationsList['hasMany'] as $key => $relation) :
        ?>
		$relations['<?=$key?>'] = ['<?=lcfirst ( $relation [0] )?>','<?=$relation [1]?>','<?=$relation [2]?>','<?=$relation [3]?>'];
<?php
    endforeach
;
?>
		return $relations;
	}
    public static function getHasOneRelations()
    {
    	$relations = [];
<?php

if (isset($relationsList['hasOne']))
    foreach ($relationsList['hasOne'] as $key => $relation) :
        ?>
		$relations['<?=$key?>'] = ['<?=lcfirst ( $relation [0] )?>','<?=$relation [1]?>','<?=$relation [2]?>'];
<?php
    endforeach
;
?>
		return $relations;
	}

	public function beforeDelete() {
<?php

if (isset($relationsList['hasMany']))
    foreach ($relationsList['hasMany'] as $key => $relation) :
        ?>
		//<?=$relation [1]?>::deleteRelatedAll(['<?=$relation [3]?>'=>$this-><?=$relation [2]?>]);
<?php
    endforeach
;
?>
		return parent::beforeDelete ();
	}

    public function asJson($with_relations=false)
	{
		$json = [];
<?php

foreach ($columns as $column) :
    ?>
<?php
    
    if (strpos($column->name, 'file')) {
        ?>		if(isset($this-><?php
        
        echo $column->name?>))
			$json['<?php
        
        echo $column->name?>'] 	= \Yii::$app->createAbsoluteUrl('<?php
        
        echo strtolower($className)?>/download',array('file'=>$this-><?php
        
        echo $column->name?>));
<?php
    } else {
        $match = '/^(updated_on|update_time|actual_start|actual_end|password|passcode|activation_key)/i';
        if (! preg_match($match, $column->name)) {
            ?>			$json['<?php
            
            echo $column->name?>'] 	= $this-><?php
            
            echo $column->name?>;
<?php
        }
    }
    ?>
<?php
endforeach
;
?>
			if ($with_relations)
		    {
<?php

foreach ($relations as $name => $relation) :
    ?>
				// <?=lcfirst($name)?>

				$list = $this-><?=lcfirst($name)?>;

				if ( is_array($list))
				{
					$relationData = [];
					foreach( $list as $item)
					{
						$relationData [] 	= $item->asJson();
					}
					$json['<?=lcfirst($name)?>'] 	= $relationData;
				}
				else
				{
					$json['<?=$name?>'] 	= $list;
				}
<?php
endforeach
;
?>
			}
		return $json;
	}
	
	<?php if ($generator->moduleName != null) : ?>
	public function getUrl($action = 'view', $id = null)
    {
        $params = [
            '/<?= $generator->moduleName?>/' . $this->getControllerID() . '/' . $action
        ];
        if ($id != null)
            $params['id'] = $id;
        else
            $params['id'] = $this->id;
        // add the title parameter to the URL
        if ($this->hasAttribute('title'))
            $params['title'] = $this->title;
        else
            $params['title'] = (string) $this;
        return Yii::$app->getUrlManager()->createAbsoluteUrl($params, true);
    }
	<?php endif;?>

}
