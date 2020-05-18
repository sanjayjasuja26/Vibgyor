<?php
/**
*@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
*@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
*/

 
namespace app\modules\tugii\generators\tumigration;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use app\components\SActiveRecord;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 */
class Generator extends \yii\gii\Generator {
	public $sql_up;
	public $sql_down;
	public $enableDown;
	public $clearCache;
	public $clearAssets;
	public $migrateName;
	public $migrateTimestamp;
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'TuGii Migration Generator';
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDescription() {
		return 'This generator generates Migration.';
	}
	
	/**
	 * @inheritdoc
	 */
	public function requiredTemplates() {
		return [ 
				'migrate.php' 
		];
	}
	public function rules() {
		return [ 
				[ 
						[ 
								'migrateName',
								'sql_up',
								'sql_down' 
						],
						'filter',
						'filter' => 'trim' 
				],
				[ 
						[ 
								'migrateName',
								'sql_up' 
						],
						'required' 
				],
				[ 
						[ 
								'migrateName' 
						],
						'match',
						'pattern' => '/^\w+$/',
						'message' => 'Only word characters are allowed.' 
				],
				
				[ 
						[ 
								'clearCache',
								'clearAssets',
								'enableDown' 
						],
						'boolean' 
				] 
		];
	}
	public function attributeLabels() {
		return array_merge ( parent::attributeLabels (), [ 
				'migrateName' => 'Migration Name',
				'sql_up' => 'Safe Up SQL code',
				'sql_down' => 'Safe Down SQL code',
				'clearCache' => 'Clear Cache',
				'clearAssets' => 'Clear Assets',
				'enableDown' => 'Enable Down SQL ' 
		] );
	}
	
	/**
	 * @inheritdoc
	 */
	public function stickyAttributes() {
		return array_merge ( parent::stickyAttributes (), [ 
				'migrateName',
				'sql_up',
				'sql_down',
				'clearCache',
				'clearAssets',
				'enableDown' 
		] );
	}
	public function init() {
		parent::init ();
		
		$this->migrateTimestamp = 'm' . date ( 'ymd_Hmi_' );
	}
	/**
	 * @inheritdoc
	 */
	public function generate() {
		$migrateName = $this->migrateTimestamp . $this->migrateName;
		
		$files = [ ];
		$params = [ 
				'migrateName' => $migrateName,
				'sql_up' => $this->sql_up,
				'sql_down' => $this->sql_down,
				'clearCache' => $this->clearCache,
				'clearAssets' => $this->clearAssets,
				'enableDown' => $this->enableDown 
		];
		
		$files [] = new CodeFile ( Yii::getAlias ( '@app/migrations' ) . '/' . $migrateName . '.php', $this->render ( 'migration.php', $params ) );
		
		return $files;
	}
	public function hints() {
		return [ 
				'migrateName' => 'Migration should be only latter',
				'sql_up' => 'SQL code.',
				'sql_down' => 'SQL code for migration rollback',
				'clearCache' => 'Is Cache needs to be flushed',
				'clearAssets' => 'Is Assest needs to be cleared',
				'enableDown' => 'Does this migration can be rollbacked' 
		];
	}
}
