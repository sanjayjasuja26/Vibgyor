<?php
/**
 * @link https://github.com/yiimaker/yii2-social-share
 * @copyright Copyright (c) 2017 Yii Maker
 * @license BSD 3-Clause License
 */
namespace app\modules\social\widgets;

use app\modules\social\assets\SocialIconsAsset;
use app\modules\social\socialshare\configurators\Configurator;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * Widget for rendering the share links.
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 1.0
 */
class SocialShare extends Widget
{

    /**
     *
     * @var string|array|ConfiguratorInterface
     */
    public $configurator;

    /**
     * Absolute URL to the page.
     *
     * @var string
     */
    public $url = '';

    /**
     * Title for share.
     *
     * @var string
     */
    public $title = '';

    /**
     * Description for share.
     *
     * @var string
     */
    public $description = '';

    /**
     * Absolute URL to the image for share.
     *
     * @var string
     */
    public $imageUrl = '';

    public $buttons = [];

    public $options = [];

    /**
     * Special properties for specific driver.
     *
     * @var array
     *
     * @since 1.4.0
     */
    public $driverProperties = [];

    /**
     * HTML options for links container tag.
     * If you won't to use it - set `tag` option to `false`.
     *
     * @var array
     */
    public $containerOptions = [
        'tag' => 'ul',
        'class' => 'social-share'
    ];

    /**
     * HTML options for link container tag.
     * If you won't to use it - set `tag` option to `false`.
     *
     * @var array
     */
    public $linkContainerOptions = [
        'tag' => 'li'
    ];

    /**
     * Initialize the widget: gets configurator instance,
     * sets [[url]] property if empty.
     * Triggers [[EVENT_INIT]] event after initialization.
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->configurator = new Configurator([
            'socialNetworks' => $this->buttons
        ]);
        
        if (empty($this->url)) {
            $this->url = Url::to('', true);
        }
        
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // if ($this->enableDefaultIcons()) {
        $this->getView()->registerAssetBundle(SocialIconsAsset::class);
        // }
        
        if ($this->isSeoEnabled()) {
            echo '<!--noindex-->';
        }
        
        $containerTag = ArrayHelper::remove($this->containerOptions, 'tag', false);
        
        if ((array) $this->options) {
            foreach ($this->options as $key => $option) {
                if ($key == 'class') {
                    $this->containerOptions['class'] = $this->containerOptions['class'] . ' ' . $this->options['class'];
                } else {
                    $this->containerOptions[$key] = $option;
                }
            }
        }
        
        if ($containerTag) {
            echo Html::beginTag($containerTag, $this->containerOptions);
        }
        
        $wrapTag = ArrayHelper::remove($this->linkContainerOptions, 'tag', false);
        foreach ($this->getLinkList() as $link) {
            echo $wrapTag ? Html::tag($wrapTag, $link, $this->linkContainerOptions) : $link;
        }
        
        if ($containerTag) {
            echo Html::endTag($containerTag);
        }
        
        if ($this->isSeoEnabled()) {
            echo '<!--/noindex-->';
        }
    }

    /**
     *
     * @return bool
     */
    final protected function enableDefaultIcons()
    {
        return $this->configurator instanceof Configurator && $this->configurator->enableDefaultIcons;
    }

    /**
     *
     * @return bool
     *
     * @since 1.4.1
     */
    final protected function isSeoEnabled()
    {
        return $this->configurator instanceof Configurator && $this->configurator->enableSeoOptions;
    }

    /**
     * Build label for driver.
     *
     * @param array $driverConfig
     * @param string $defaultLabel
     *
     * @return string
     */
    protected function getLinkLabel($driverConfig, $defaultLabel)
    {
        return $this->enableDefaultIcons() ? Html::tag('i', '', [
            'class' => $this->configurator->getIconSelector($driverConfig['class'])
        ]) : (isset($driverConfig['label']) ? $driverConfig['label'] : $defaultLabel);
    }

    /**
     * Creates driver instance.
     *
     * @param array $config
     *            Configuration for driver.
     *            
     * @throws \yii\base\InvalidConfigException
     */
    private function createDriver($config)
    {
        $fullConfig = ArrayHelper::merge([
            'class' => $config['class'],
            'url' => $this->url,
            'title' => $this->title,
            'description' => $this->description,
            'imageUrl' => $this->imageUrl
        ], isset($config['config']) ? $config['config'] : [], isset($this->driverProperties[$config['class']]) ? $this->driverProperties[$config['class']] : []);
        
        return Yii::createObject($fullConfig);
    }

    /**
     * Combine global and custom HTML options.
     *
     * @param array $driverConfig
     *
     * @return array
     */
    private function combineOptions($driverConfig)
    {
        $options = isset($driverConfig['options']) ? $driverConfig['options'] : [];
        
        $globalOptions = $this->configurator->getOptions();
        if (empty($globalOptions)) {
            return $options;
        }
        
        if (isset($options['class'])) {
            Html::addCssClass($globalOptions, $options['class']);
            unset($options['class']);
        }
        
        return ArrayHelper::merge($globalOptions, $options);
    }

    /**
     * Returns array with share links in <a> HTML tag.
     *
     * @return array
     *
     * @throws \yii\base\InvalidConfigException
     */
    private function getLinkList()
    {
        $linkList = [];
        
        foreach ($this->configurator->getSocialNetworks() as $key => $socialNetwork) {
            if (isset($socialNetwork['class'])) {
                $linkOptions = $this->combineOptions($socialNetwork);
                $linkOptions['href'] = $this->createDriver($socialNetwork)->getLink();
                $linkList[] = Html::tag('a', $this->getLinkLabel($socialNetwork, Inflector::camel2words($key)), $linkOptions);
            }
        }
        
        return $linkList;
    }
}
