<?php

namespace app\components;

use app\base\SBaseController;
use app\models\User;
use app\modules\seo\models\Seo;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\jui\Tabs;
use yii\web\HttpException;
use yii\web\View;

class SController extends SBaseController {

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ]
        ];
    }

    public static function cleanRuntimeDir($dir, $delete = false) {
        if (is_dir($dir)) {
            $objects = scandir($dir);

            $objects = FileHelper::findFiles($dir);
            foreach ($objects as $object) {
                if (unlink($object)) {
                    Yii::$app->session->setFlash('runtime_clean', Yii::t('app', 'Runtime cleaned'));
                }
            }
            reset($objects);

            if ($delete) {
                FileHelper::removeDirectory($dir);
            }
        }
    }

    public function cleanAssetsDir() {
        $assetsDirs = glob(Yii::getAlias($this->assetsDir) . '/*', GLOB_ONLYDIR);
        foreach ($assetsDirs as $dir) {
            if (in_array(basename($dir), $this->ignoreDirs)) {
                continue;
            }
            if (!$this->dryRun) {
                FileHelper::removeDirectory($dir);
            }
        }
        Yii::$app->session->setFlash('assets_clean', Yii::t('app', 'Assets cleaned'));
    }

    public function processSEO($model = null) {
        if ($model != null && $model instanceof SActiveRecord && !$model->isNewRecord) {

            $this->_pageCaption = Html::encode(strip_tags($model));
            if ($model->hasAttribute('content'))
                $this->_pageDescription = StringHelper::truncateWords(strip_tags($model->content), 150);
            else if ($model->hasAttribute('description'))
                $this->_pageDescription = StringHelper::truncateWords(strip_tags($model->description), 150);
            else
                $this->_pageDescription = "";
        } elseif (empty($this->_pageCaption)) {
            $this->_pageCaption = \Yii::$app->id;
            if ($this->id == 'site' && $this->action->id != 'index') {
                $this->_pageCaption = Inflector::camel2words($this->action->id) . ' | ' . $this->_pageCaption;
            } elseif ($this->action->id == 'index' && $this->id == 'site') {
                $this->_pageCaption = $this->_pageCaption;
            } else {
                $this->_pageCaption = Inflector::pluralize(Inflector::camel2words(Yii::$app->controller->id)) . '-' . Inflector::camel2words($this->action->id) . ' | ' . $this->_pageCaption;

                if (isset($this->module) && $this->module->id != \Yii::$app->id)
                    $this->_pageCaption = Inflector::camel2words($this->module->id) . ' | ' . $this->_pageCaption;
            }
        }

        if (\yii::$app->getModule('seo')) {
            $seo = Seo::getMeta($model, $this->id, $this->action->id);
            if ($seo != null) {
                $this->_pageCaption = $seo->title;
                $this->_pageDescription = $seo->description;
                $this->_pageKeywords = $seo->keywords;
            }
        }
        if (!empty($this->_pageDescription))
            $this->_pageDescription = preg_replace("/[\\n\\r]+/", "", StringHelper::truncateWords(strip_tags($this->_pageDescription), 150)); // str_replace(PHP_EOL, '', StringHelper::truncateWords(strip_tags($model->content), 150));

        $this->getView()->registerMetaTag([
            'name' => 'description',
            'content' => $this->_pageDescription
        ]);
        // hide site's keywords
        if (!empty($this->_pageKeywords))
            $this->getView()->registerMetaTag([
                'name' => 'keywords',
                'content' => $this->_pageKeywords
            ]);
        $this->getView()->registerMetaTag([
            'name' => 'author',
            'content' => $this->_author
        ]);

        $this->getView()->title = $this->_pageCaption;

        $this->getView()->registerLinkTag([
            'rel' => 'canonical',
            'href' => Url::canonical()
        ]);
        if (\yii::$app->getModule('seo')) {
            Seo::addAnalyticsCode();
        }
        if (\yii::$app->getModule('social')) {

            \app\modules\social\widgets\SocialShare::widget([
                'buttons' => [
                    'linkedin' => [
                        'label' => false,
                        'options' => [
                            'class' => 'fa fa-linkedin linkedin'
                        ]
                    ],
                    'facebook' => [
                        'label' => false,
                        'options' => [
                            'class' => 'fa fa-facebook facebook'
                        ]
                    ],
                    'googleplus' => [
                        'label' => false,
                        'options' => [
                            'class' => 'fa fa-google-plus google'
                        ]
                    ],
                    'twitter' => [
                        'label' => false,
                        'options' => [
                            'class' => 'fa fa-twitter twitter'
                        ]
                    ],
                    'whatsapp' => [
                        'label' => false,
                        'options' => [
                            'class' => 'fa fa-whatsapp whatsapp'
                        ]
                    ]
                ],
                'url' => Url::canonical(),
                // 'imageUrl' => $model->getImageUrl(),
                'title' => $this->_pageCaption,
                'description' => $this->_pageDescription,
                'options' => [
                    'class' => 'footer-social-menu list-inline pull-right'
                ]
            ]);
        }
    }

    protected function checkIPAccess() {
        $ip = Yii::$app->getRequest()->getUserIP();
        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        Yii::warning('Access to Gii is denied due to IP address restriction. The requested IP is ' . $ip, __METHOD__);

        return false;
    }

    protected function checkDomain() {
        if (YII_ENV == 'prod') {

            if (strcasecmp(\Yii::$app->params['domain'], \Yii::$app->request->hostName) != 0) {
                $this->redirect("http://" . \Yii::$app->params['domain']);
                return false;
            }
        }
        return true;
    }

    public function beforeAction($action) {
        // Validate domain name
        if (isset(\Yii::$app->params['domain']) && !$this->checkDomain()) {
            return false;
        }

        if (!file_exists(DB_CONFIG_FILE_PATH)) {
            if ($this->module->id != 'installer') {
                $this->redirect([
                    "/installer"
                ]);
                return false;
            }
        }

        if (User::find()->count() == 0 && \Yii::$app->controller->id != 'user' && $action != 'add-admin') {
            return $this->redirect([
                        '/user/add-admin'
            ]);
        }

        return parent::beforeAction($action);
    }

    public function startPanel($name = 'tabpanel1') {
        $this->tabs_name = $name;
        $this->tabs_data = [];
    }

    public function addPanelPage($title, $model, $view, $addMenu = true) {
        $this->tabs_data[] = [
            'label' => $title,
            /*  'url' => [
              "/project/ajax-view?view=" . $view . "&id=" . $model->id
              ], */
            'content' => $this->renderPartial($view, [
                'model' => $model
            ]),
            'active' => count($this->tabs_data) == 0 ? true : false
        ];
    }

    public function addPanel($title, $objects, $relation, $model = null, $module = null, $addMenu = true, $addAction = null) {
        $view = Inflector::camel2id($relation);

        if ($objects) {
            if ($objects instanceof ActiveDataProvider)
                $dataProvider = $objects;
            elseif ($objects instanceof ActiveQuery)
                $dataProvider = new ActiveDataProvider([
                    'query' => $objects
                ]);
            else {
                $function = 'get' . ucfirst($objects);
                $query = $model->$function();

                if ($query == null) {
                    throw new HttpException(403, Yii::t('app', 'You are not allowed to access objects:' . $objects));
                }

                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'sort' => [
                        'defaultOrder' => [
                            'id' => SORT_DESC
                        ]
                    ]
                ]);

                $modelClass = $dataProvider->query->modelClass;

                if (strstr($modelClass, 'modules')) {
                    $len = strpos($modelClass, 'modules') + strlen('modules') + 1;
                    $module = substr($modelClass, $len, strpos($modelClass, 'models') - $len - 1);
                }

                $type = get_class($model);

                // $content = $this->renderPartial('/'.$view.'/_grid',['dataProvider'=>$dataProvider,'searchModel'=> null]);

                if (isset($module))
                    $view = $module . '/' . $view;
                $this->tabs_data[] = [
                    'label' => $title . ' [' . $dataProvider->totalCount . ']',
                    'url' => [
                        "/$view/ajax",
                        'type' => "$type",
                        'function' => "$objects",
                        'id' => $model->id,
                        'addMenu' => $addMenu,
                        'action' => $addAction
                    ],
                    'active' => count($this->tabs_data) == 0 ? true : false
                ];
            }
        }
    }

    public function endPanel() {
        $id = 'project-' . $this->tabs_name;

        echo Tabs::widget([
            'items' => $this->tabs_data,
            'options' => [
                'id' => $id,
                'class' => 'ui-tabs ui-widget ui-widget-content',
                'style' => "display:none;"
            ] /*
                 * 'itemOptions' => [
                 * 'class' => 'ui-state-default ui-corner-top',
                 * ]
                 */
        ]);
        $this->getView()->registerJs("$( '#$id').show()", View::POS_READY, 'project-tabs');
    }

    public function actionAjax($type, $id, $function, $grid = '_ajax-grid', $addMenu = true, $action = null) {
        $model = $type::findOne([
                    'id' => $id
        ]);
        if (!empty($model)) {

            if (!($model->isAllowed()))
                throw new \yii\web\HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));
            $function = 'get' . ucfirst($function);
            $query = $model->$function();
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ]
            ]);
            $menu = [];
            if ($model && $addMenu) {

                if ($action != null) {
                    if (strstr($action, '/')) {
                        $menu['url'] = Url::toRoute($action, [
                                    'id' => $model->id
                        ]);
                    } else {
                        $menu['url'] = $model->getUrl($action);
                    }
                } else {
                    $linkModel = new $query->modelClass();
                    $action = 'add';
                    $menu['url'] = $linkModel->getUrl($action, $model->id);
                }
                $menu['label'] = '<i class="fa fa-plus"></i> <span></span>';
                $menu['htmlOptions'] = [
                    'class' => 'btn btn-success pull-right',
                    'title' => $action
                ];
            }
            return $this->renderAjax($grid, [
                        'dataProvider' => $dataProvider,
                        'searchModel' => null,
                        'id' => $id,
                        'menu' => $menu
            ]);
        }
    }

    public function render($view, $params = []) {
        if (array_key_exists('model', $params)) {
            $this->processSEO($params['model']);
        } else
            $this->processSEO();

        if (Yii::$app->user->isAdminMode) {
            $viewPath = $this->getViewPath();
            $file = ltrim($view, '/') . '.' . $this->getView()->defaultExtension;
            $path = $viewPath . '/' . $file;
            if (!is_file($path)) {
                $path2 = str_replace('admin/', '', $path);
                $path3 = substr($path, strpos($path, 'admin/') + 6);

                if (is_file($path2))
                    $view = '/' . dirname($path3) . '/' . ltrim($view, '/');
            }
        }

        return parent::render($view, $params);
    }

    protected function updateMenuItems($model = null) {
        switch (\Yii::$app->controller->action->id) {

            default:
            case 'view': {
                    $this->menu['add'] = array(
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'Add'),
                        'url' => [
                            'add'
                        ],
                        'visible' => User::isAdmin()
                    );
                    $this->menu['manage'] = array(
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => 'Manage',
                        'url' => array(
                            'index'
                        ),
                        'visible' => User::isAdmin()
                    );
                }
                break;

            case 'index': {
                    $this->menu['add'] = array(
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'Add'),
                        'url' => [
                            'add'
                        ],
                        'visible' => User::isAdmin()
                    );
                    $this->menu['manage'] = array(
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => 'Manage',
                        'url' => array(
                            'index'
                        ),
                        'visible' => User::isAdmin()
                    );
                    $this->menu['clear'] = array(
                        'label' => '<span class=" glyphicon glyphicon-remove"></span>',
                        'title' => Yii::t('app', 'Clear'),
                        'url' => [
                            'clear'
                        /* 'id' => $model->id */
                        ],
                        'visible' => User::isAdmin()
                    );
                }
                break;
        }
    }

}
