<?php

namespace app\modules\social\components;

use app\modules\social\models\search\Provider;
use Yii;
use yii\authclient\widgets\AuthChoiceAsset;
use yii\authclient\widgets\AuthChoiceItem;
use yii\authclient\widgets\AuthChoiceStyleAsset;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class TAuthChoice extends Widget {
	public $authClientCollection = "app\modules\social\components\TCollection";
	
	/**
	 *
	 * @var string name of the auth client collection application component.
	 *      This component will be used to fetch services value if it is not set.
	 */
	public $clientCollection = 'authClientCollection';
	
	/**
	 *
	 * @var string name of the GET param , which should be used to passed auth client id to URL
	 *      defined by [[baseAuthUrl]].
	 */
	public $clientIdGetParamName = 'authclient';
	
	/**
	 *
	 * @var array the HTML attributes that should be rendered in the div HTML tag representing the container element.
	 * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	 */
	public $options = [ ];
	
	/**
	 *
	 * @var array additional options to be passed to the underlying JS plugin.
	 */
	public $clientOptions = [ ];
	
	/**
	 *
	 * @var bool indicates if popup window should be used instead of direct links.
	 */
	public $popupMode = true;
	
	/**
	 *
	 * @var bool indicates if widget content, should be rendered automatically.
	 *      Note: this value automatically set to 'false' at the first call of [[createClientUrl()]]
	 */
	public $autoRender = true;
	
	/**
	 *
	 * @var bool indicates if widget content, Display in Format.
	 *      If this is false it means social icon render without <ul>
	 *      some format listed here [list, raw]
	 */
	public $format = 'list';
	private $_formatList = [ 
			'list',
			'raw' 
	];
	
	// $template= [
	// 'facebook' => '<div><a href="{url}">Facebook</a></div>'
	// ];
	public $template;
	public $icons;
	
	/**
	 *
	 * @var array configuration for the external clients base authentication URL.
	 */
	private $_baseAuthUrl = [ 
			'social/user/auth' 
	];
	
	/**
	 *
	 * @var ClientInterface[] auth providers list.
	 */
	private $_clients;
	public function run() {
		$content = '';
		$content .= $this->renderMainContent ();
		$content .= Html::endTag ( 'div' );
		return $content;
	}
	
	/**
	 * Renders the main content, which includes all external services links.
	 *
	 * @return string generated HTML.
	 */
	protected function renderMainContent() {
		$items = [ ];
		$html = '';
		if (in_array ( $this->format, $this->_formatList )) {
			if ($this->format == 'list') {
				foreach ( $this->getClients () as $externalService ) {
					$items [] = Html::tag ( 'li', $this->clientLink ( $externalService ) );
				}
				$html = Html::tag ( 'ul', implode ( '', $items ), [ 
						'class' => 'auth-clients' 
				] );
			} else if ($this->format == 'raw') {
				$providers = $this->getClients ();
				if (is_array ( $this->template )) {
					foreach ( $this->template as $key => $icon ) {
						if (isset ( $providers [strtolower ( $key )] )) {
							$html .= str_replace ( "{url}", $this->createClientUrl ( $providers [strtolower ( $key )] ), $icon );
						} else {
							throw new \InvalidArgumentException ( " {$key} Client not configured from admin panel." );
						}
					}
				} else if (is_string ( $this->template )) {
					if (strpos ( $this->template, "{icons}" )) {
						if ($this->icons) {
							if (is_array ( $this->icons )) {
								foreach ( $this->icons as $key => $icon ) {
									if (isset ( $providers [strtolower ( $key )] )) {
										$html .= str_replace ( "{url}", $this->createClientUrl ( $providers [strtolower ( $key )] ), $icon );
									} else {
										throw new \InvalidArgumentException ( " {$key} Client not configured from admin panel." );
									}
								}
							} else {
								throw new \InvalidArgumentException ( "icons only accepts array, " . gettype ( $this->icons ) . " is given." );
							}
						} else {
							foreach ( $this->getClients () as $externalService ) {
								$items [] = Html::tag ( 'li', $this->clientLink ( $externalService ) );
							}
							$html = Html::tag ( 'ul', implode ( '', $items ), [ 
									'class' => 'auth-clients' 
							] );
							$html = str_replace ( "{icons}", $html, $this->template );
						}
					} else {
						throw new \InvalidArgumentException ( "icons position not set." );
					}
				} else {
					throw new \InvalidArgumentException ( " template Argument , " . gettype ( $this->template ) . " type does't support." );
				}
			}
		} else {
			throw new \InvalidArgumentException ( " {$this->format} format dosen't support." );
		}
		
		return $html;
	}
	public function dbClients() {
		$authCollection = new TCollection ();
		$dbClients = (new Provider ())->getDbClients ();
		$clients = [ ];
		if ($dbClients) {
			foreach ( $dbClients as $db ) {
				if ($provider = $authCollection->hasProvider ( $db->provider_type )) {
					$clients [strtolower ( $db->getClient () )] = (new Provider ())->getProviderClient ( $provider, $db );
				}
			}
		}
		return $clients;
	}
	
	/**
	 *
	 * @param ClientInterface[] $clients
	 *        	auth providers
	 */
	public function setClients(array $clients) {
		$this->_clients = $clients;
	}
	
	/**
	 *
	 * @return ClientInterface[] auth providers
	 */
	public function getClients() {
		if ($this->_clients === null) {
			$this->_clients = $this->defaultClients ();
		}
		return $this->_clients;
	}
	
	/**
	 *
	 * @param array $baseAuthUrl
	 *        	base auth URL configuration.
	 */
	public function setBaseAuthUrl(array $baseAuthUrl) {
		$this->_baseAuthUrl = $baseAuthUrl;
	}
	
	/**
	 *
	 * @return array base auth URL configuration.
	 */
	public function getBaseAuthUrl() {
		if (! is_array ( $this->_baseAuthUrl )) {
			$this->_baseAuthUrl = $this->defaultBaseAuthUrl ();
		}
		
		return $this->_baseAuthUrl;
	}
	
	/**
	 * Returns default auth clients list.
	 *
	 * @return ClientInterface[] auth clients list.
	 */
	protected function defaultClients() {
		/* @var $collection \yii\authclient\Collection */
		$collection = new TCollection ();
		$collection->setClients ( $this->dbClients () );
		return $collection->getClients ();
	}
	
	/**
	 * Composes default base auth URL configuration.
	 *
	 * @return array base auth URL configuration.
	 */
	protected function defaultBaseAuthUrl() {
		$baseAuthUrl = [ 
				Yii::$app->controller->getRoute () 
		];
		$params = Yii::$app->getRequest ()->getQueryParams ();
		unset ( $params [$this->clientIdGetParamName] );
		$baseAuthUrl = array_merge ( $baseAuthUrl, $params );
		
		return $baseAuthUrl;
	}
	
	/**
	 * Outputs client auth link.
	 *
	 * @param ClientInterface $client
	 *        	external auth client instance.
	 * @param string $text
	 *        	link text, if not set - default value will be generated.
	 * @param array $htmlOptions
	 *        	link HTML options.
	 * @return string generated HTML.
	 * @throws InvalidConfigException on wrong configuration.
	 */
	public function clientLink($client, $text = null, array $htmlOptions = []) {
		$viewOptions = $client->getViewOptions ();
		
		if (empty ( $viewOptions ['widget'] )) {
			if ($text === null) {
				$text = Html::tag ( 'span', '', [ 
						'class' => 'auth-icon ' . $client->getName () 
				] );
			}
			if (! isset ( $htmlOptions ['class'] )) {
				$htmlOptions ['class'] = $client->getName ();
			}
			if (! isset ( $htmlOptions ['title'] )) {
				$htmlOptions ['title'] = $client->getTitle ();
			}
			Html::addCssClass ( $htmlOptions, [ 
					'widget' => 'auth-link' 
			] );
			
			if ($this->popupMode) {
				if (isset ( $viewOptions ['popupWidth'] )) {
					$htmlOptions ['data-popup-width'] = $viewOptions ['popupWidth'];
				}
				if (isset ( $viewOptions ['popupHeight'] )) {
					$htmlOptions ['data-popup-height'] = $viewOptions ['popupHeight'];
				}
			}
			return Html::a ( $text, $this->createClientUrl ( $client ), $htmlOptions );
		}
		
		$widgetConfig = $viewOptions ['widget'];
		if (! isset ( $widgetConfig ['class'] )) {
			throw new InvalidConfigException ( 'Widget config "class" parameter is missing' );
		}
		/* @var $widgetClass Widget */
		$widgetClass = $widgetConfig ['class'];
		if (! (is_subclass_of ( $widgetClass, AuthChoiceItem::className () ))) {
			throw new InvalidConfigException ( 'Item widget class must be subclass of "' . AuthChoiceItem::className () . '"' );
		}
		unset ( $widgetConfig ['class'] );
		$widgetConfig ['client'] = $client;
		$widgetConfig ['authChoice'] = $this;
		return $widgetClass::widget ( $widgetConfig );
	}
	
	/**
	 * Composes client auth URL.
	 *
	 * @param ClientInterface $client
	 *        	external auth client instance.
	 * @return string auth URL.
	 */
	public function createClientUrl($client) {
		$this->autoRender = false;
		$url = $this->getBaseAuthUrl ();
		$url [$this->clientIdGetParamName] = $client->getId ();
		
		return Url::to ( $url );
	}
	
	/**
	 * Initializes the widget.
	 */
	public function init() {
		$view = Yii::$app->getView ();
		if ($this->popupMode) {
			AuthChoiceAsset::register ( $view );
			if (empty ( $this->clientOptions )) {
				$options = '';
			} else {
				$options = Json::htmlEncode ( $this->clientOptions );
			}
			$view->registerJs ( "jQuery('#" . $this->getId () . "').authchoice({$options});" );
		} else {
			AuthChoiceStyleAsset::register ( $view );
		}
		$this->options ['id'] = $this->getId ();
		echo Html::beginTag ( 'div', $this->options );
	}
}