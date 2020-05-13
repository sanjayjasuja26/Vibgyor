<?php
namespace app\modules\sitemap;

/**
 * sitemap module definition class
 */
use app\components\TModule;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\helpers\Url;

class Module extends TModule
{

    public $controllerNamespace = 'app\modules\sitemap\controllers';

    /**
     *
     * @var int
     */
    public $cacheExpire = YII_DEBUG ? 0 : 3600;

    /**
     *
     * @var Cache|string
     */
    public $cacheProvider = 'cache';

    /**
     *
     * @var string
     */
    public $cacheKey = 'sitemap';

    /**
     *
     * @var boolean Use php's gzip compressing.
     */
    public $enableGzip = false;

    /**
     *
     * @var array
     */
    public $models = [];

    /**
     *
     * @var array
     */
    public $urls = [];

    const CHANGEFREQ_ALWAYS = 'always';

    const CHANGEFREQ_HOURLY = 'hourly';

    const CHANGEFREQ_DAILY = 'daily';

    const CHANGEFREQ_WEEKLY = 'weekly';

    const CHANGEFREQ_MONTHLY = 'monthly';

    const CHANGEFREQ_YEARLY = 'yearly';

    const CHANGEFREQ_NEVER = 'never';

    const BATCH_MAX_SIZE = 1000;

    const PRIORITY = 0.5;

    /**
     *
     * @var callable
     */
    public $dataClosure;

    /**
     *
     * @var string|bool
     */
    public $defaultChangefreq = self::CHANGEFREQ_WEEKLY;

    /**
     *
     * @var float|bool
     */
    public $defaultPriority = self::PRIORITY;

    /**
     *
     * @var callable
     */
    public $scope;

    public function init()
    {
        parent::init();
        if (is_string($this->cacheProvider)) {
            $this->cacheProvider = \Yii::$app->{$this->cacheProvider};
        }
        
        if (! $this->cacheProvider instanceof Cache) {
            throw new InvalidConfigException('Invalid `cacheKey` parameter was specified.');
        }
        if (YII_DEBUG)
            $this->cacheProvider->delete($this->cacheKey);
        
        $this->layoutPath = \Yii::$app->view->theme->basePath . '/views/layouts/';
        
        $this->scope = function ($model) {
            $model->select([
                'id',
                'title',
                'created_on'
            ]);
        };
        
        $this->dataClosure = function ($model) {
            return [
                'loc' => $model->getUrl(),
                'lastmod' => strtotime($model->created_on),
                'changefreq' => $this->defaultChangefreq,
                'priority' => $this->defaultPriority
            ];
        };
        if (! is_callable($this->dataClosure)) {
            throw new InvalidConfigException('Sitemap::$dataClosure isn\'t callable.');
        }
    }

    public static function getRules()
    {
        return [
            
            [
                'pattern' => 'sitemap',
                'route' => 'sitemap/default/index',
                'suffix' => '.xml'
            ],
            [
                'pattern' => 'robots',
                'route' => 'sitemap/default/robots',
                'suffix' => '.txt'
            ]
        
        ];
    }

    /**
     * Build and cache a site map.
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function buildSitemap()
    {
        $urls = [];
        
        foreach ($this->urls as $route) {
            $route['loc'] = Url::toRoute($route['loc']);
            $urls[] = $route;
        }
        
        foreach ($this->models as $modelName) {
            /**
             *
             * @var behaviors\SitemapBehavior $model
             */
            if (is_array($modelName)) {
                $model = $modelName['class'];
            } else {
                $model = $modelName;
            }
            $data = $this->generateSiteMap($model);
            
            $urls = array_merge($urls, $data);
        }
        
        return $urls;
    }

    public function generateSiteMap($model)
    {
        $result = [];
        $n = 0;
        
        if (method_exists($model, 'sitemap')) {
            $query = $model::sitemap();
        } else {
            $query = $model::find();
            
            // if ($model->hasAttribute('state_id') && defined("$model::STATE_ACTIVE")) {
            $query->where([
                'state_id' => 1
            ]);
            // }
            $query->orderBy([
                'id' => SORT_DESC
            ]);
        }
        
        foreach ($query->each(self::BATCH_MAX_SIZE) as $modelData) {
            $urlData = call_user_func($this->dataClosure, $modelData);
            
            if (empty($urlData)) {
                continue;
            }
            
            $result[$n]['loc'] = $urlData['loc'];
            $result[$n]['lastmod'] = $urlData['lastmod'];
            
            if (isset($urlData['changefreq'])) {
                $result[$n]['changefreq'] = $urlData['changefreq'];
            } elseif ($this->defaultChangefreq !== false) {
                $result[$n]['changefreq'] = $this->defaultChangefreq;
            }
            
            if (isset($urlData['priority'])) {
                $result[$n]['priority'] = $urlData['priority'];
            } elseif ($this->defaultPriority !== false) {
                $result[$n]['priority'] = $this->defaultPriority;
            }
            
            if (isset($urlData['news'])) {
                $result[$n]['news'] = $urlData['news'];
            }
            if (isset($urlData['images'])) {
                $result[$n]['images'] = $urlData['images'];
            }
            
            ++ $n;
        }
        
        return $result;
    }
}
