<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\components;

use app\models\Setting;
use yii\base\Component;
use yii\base\UnknownPropertyException;
use yii\helpers\Json;

class Settings extends Component
{

	protected $model;

	public $cache = 'cache';

	public $frontCache;

	private $_data = null;

	public $modelClass = 'app\models\Setting';

	public $isConfig = true;

	public function init()
	{
		parent::init();
		$this->model = new $this->modelClass();
		if (is_string($this->cache))
		{
			$this->cache = \Yii::$app->get($this->cache, false);
		}
		if (is_string($this->frontCache))
		{
			$this->frontCache = \Yii::$app->get($this->frontCache, false);
		}
		$this->createProperty();
	}

	public function __get($key)
	{
		if ($this->_data !== null)
		{
			if (array_key_exists($key, $this->_data))
			{
				return $this->_data[$key];
			} else
			{
				throw new UnknownPropertyException("Getting unknown property: " . get_class() . "::" . $key);
			}
		} else
			return false;
	}

	public function setValue($key, $value, $defaultKey = 'appConfig', $title = "Config")
	{
		$model = $this->modelClass::findOne([
			'key' => $defaultKey
		]);
		$json = [];
		if (empty($model))
		{
			$model = new $this->modelClass();
			$model->key = $defaultKey;
			$model->title = $title;
			if (! $model->save())
			{
				\Yii::$app->session->setFlash('error', $model->getErrorString());
			}
		}
		if (! empty($model->value))
		{
			$json = json_decode($model->value, true);
		}
		if (! array_key_exists($key, $json))
		{
			if (is_array($value))
			{
				$json[$key] = $value;
			} else
			{
				$json[$key] = [
					'type' => $this->modelClass::KEY_TYPE_STRING,
					'value' => $value,
					'required' => false
				];
			}

			$model->value = json_encode($json);

			if (! $model->save())
			{
				\Yii::$app->session->setFlash('error', $model->getErrorString());
			}
			return $model;
		}
	}

	public function getValue($key, $default = null, $defaultKey = 'appConfig')
	{
		$model = $this->modelClass::findOne([
			'key' => $defaultKey
		]);
		$val = "";
		if (! empty($model))
		{
			$jsonArray = json_decode($model->value, true);

			if (array_key_exists($key, $jsonArray))
			{
				$val = $jsonArray[$key]['value'];
			}
		}
		if (empty($val))
		{
			$set = $this->setValue($key, $default, $defaultKey);
			$jsonArray = json_decode($set->value, true);
			$val = $jsonArray[$key]['value'];
		}

		return $val;
	}

	protected function createProperty()
	{
		$configrations = Setting::find()->all();
		if ($configrations)
			foreach ($configrations as $config)
			{
				$setConfig = new \stdClass();
				$setConfig->value = (object) (Json::decode($config->value));

				$setConfig->id = $config->id;
				$setConfig->key = $config->key;
				$setConfig->title = $config->title;
				$setConfig->asArray = Json::decode($config->value, true);

				$keyValue = new \stdClass();
				if ($setConfig->asArray)
				{
					foreach ($setConfig->asArray as $key => $val)
					{
						$keyValue->{$key} = $val['value'];
					}
				}

				$setConfig->config = $keyValue;
				$this->_data[$config->key] = $setConfig;
			}
	}
}
