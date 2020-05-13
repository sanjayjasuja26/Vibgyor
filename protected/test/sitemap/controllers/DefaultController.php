<?php
namespace app\modules\sitemap\controllers;

/**
 * Default controller for the `sitemap` module
 */
use app\components\TController;
use Yii;
use yii\filters\AccessControl;
use app\models\User;
use yii\helpers\Url;

class DefaultController extends TController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'robots'
                        ],
                        'allow' => true,
                        'roles' => [
                            '*',
                            '?',
                            '@'
                        ]
                    ],
                    [
                        'actions' => [
                            'test'
                        ],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return User::isAdmin();
                        }
                    ]
                ]
            ]
        
        ];
    }

    public function actionIndex()
    {
        $module = $this->module;
        
        if (! $sitemapData = $module->cacheProvider->get($module->cacheKey)) {
            
            $urls = $module->buildSitemap();
            
            $sitemapData = $this->renderPartial('index', [
                'urls' => $urls
            ]);
            
            $module->cacheProvider->set($module->cacheKey, $sitemapData, $module->cacheExpire);
        }
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/xml');
        if ($module->enableGzip) {
            $sitemapData = gzencode($sitemapData);
            $headers->add('Content-Encoding', 'gzip');
            $headers->add('Content-Length', strlen($sitemapData));
        }
        return $sitemapData;
    }

    public function actionRobots()
    {
        $sitemapData = $this->renderPartial('robots', [
            'sitemap' => Url::toRoute([
                '/sitemap/default/index'
            ], true)
        ]);
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/plain');
        
        return $sitemapData;
    }

    public function actionTest()
    {
        set_time_limit(0);
        $this->layout = 'guest-main';
        $module = $this->module;
        $urls = $module->buildSitemap();
        
        return $this->render('test', [
            'urls' => $urls
        ]);
    }
}
