<?php
namespace app\modules\social\socialshare\configurators;

use app\modules\social\drivers\Facebook;
use app\modules\social\drivers\Gmail;
use app\modules\social\drivers\GooglePlus;
use app\modules\social\drivers\LinkedIn;
use app\modules\social\drivers\Pinterest;
use app\modules\social\drivers\Telegram;
use app\modules\social\drivers\Tumblr;
use app\modules\social\drivers\Twitter;
use app\modules\social\drivers\Vkontakte;
use app\modules\social\drivers\WhatsApp;
use app\modules\social\drivers\Yahoo;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Configurator for social network drivers.
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 1.0
 */
class Configurator extends BaseObject implements ConfiguratorInterface
{

    /**
     * Configuration of social network drivers.
     *
     * @var array
     */
    public $socialNetworks = [];

    /**
     * CSS options for share links.
     *
     * @var array
     */
    public $options = [];

    /**
     * Enable SEO options for share links.
     *
     * @var bool
     */
    public $enableSeoOptions = true;

    /**
     * HTML attributes from this option will be applied if `enableSeoOptions` is true.
     *
     * @var array
     */
    public $seoOptions = [];

    /**
     * Enable default icons instead labels for social networks.
     *
     * @var bool
     */
    public $enableDefaultIcons = false;

    /**
     * Configuration of icons for social network drivers.
     *
     * @var array
     */
    public $icons = [];

    /**
     *
     * @var array
     */
    private $_defaultIconsMap = [
        Vkontakte::class => 'si si-vk',
        Facebook::class => 'si si-facebook',
        Twitter::class => 'si si-twitter',
        GooglePlus::class => 'si si-google-plus',
        LinkedIn::class => 'si si-linkedin',
        Pinterest::class => 'si si-pinterest',
        Telegram::class => 'si si-telegram',
        WhatsApp::class => 'si si-whatsapp',
        Gmail::class => 'si si-gmail',
        Tumblr::class => 'si si-tumblr',
        Yahoo::class => 'si si-yahoo'
    ];

    /**
     * Set default values for special link options.
     */
    public function init()
    {
        $this->socialClass();
        if (empty($this->seoOptions)) {
            $this->seoOptions = [
                'target' => '_blank',
                'rel' => 'noopener'
            ];
        }
        if ($this->enableDefaultIcons) {
            $this->icons = ArrayHelper::merge($this->_defaultIconsMap, $this->icons);
        }
    }

    public function socialClass()
    {
        $list = [];
        foreach ($this->socialNetworks as $key => $val) {
            switch (strtolower($key)) {
                case "facebook":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = Facebook::class;
                    break;
                case "twitter":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = Twitter::class;
                    break;
                case "googleplus":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = GooglePlus::class;
                    break;
                case "vkontakte":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = Vkontakte::class;
                    break;
                case "linkedin":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = LinkedIn::class;
                    break;
                case "pinterest":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = Pinterest::class;
                    break;
                case "telegram":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = Telegram::class;
                    break;
                case "whatsapp":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = WhatsApp::class;
                    break;
                case "gmail":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = Gmail::class;
                    break;
                case "tumblr":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = Tumblr::class;
                    break;
                case "yahoo":
                    $list[strtolower($key)] = $val;
                    $list[strtolower($key)]['class'] = Yahoo::class;
                    break;
            }
        }
        return $this->socialNetworks = $list;
    }

    /**
     * @inheritdoc
     */
    public function getSocialNetworks()
    {
        return $this->socialNetworks;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->enableSeoOptions ? ArrayHelper::merge($this->options, $this->seoOptions) : $this->options;
    }

    /**
     * Returns icon selector by driver name.
     *
     * @param string $driverName
     *
     * @return string
     */
    public function getIconSelector($driverName)
    {
        return isset($this->icons[$driverName]) ? $this->icons[$driverName] : '';
    }
}
