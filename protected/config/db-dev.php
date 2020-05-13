<?php
				return [
				'class' => 'yii\db\Connection',
				'dsn' => 'mysql:host=localhost;dbname=vibgyor_db',
				'emulatePrepare' => true,
				'username' => 'root',
				'password' => '',
				'charset' => 'utf8',
				'tablePrefix' => 'tbl_',
                'enableSchemaCache' => true,
                'schemaCacheDuration' => 3600,
                'schemaCache' => 'cache',
				];