<?php
/**
 *@copyright : Amusoftech Pvt. Ltd. < www.amusoftech.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
namespace app\components\gdpr;

use yii\web\Cookie;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\TBaseWidget;
use app\components\gdpr\assets\GdprAsset;

class Gdpr extends TBaseWidget
{

    private $name = "gdpr_";

    public $privacylink = '/site/privacy';

    public $description = "We use cookies to ensure that we give you the best experience on our website. By using this site, you agree to our use of cookies {privacy}.";

    public function init()
    {
        GdprAsset::register(\Yii::$app->getView());
        $this->name .= \Yii::$app->id;
        $this->description = str_replace('{privacy}', Html::a('Find out more', Url::toRoute([
            $this->privacylink
        ])), $this->description);
        parent::init();
    }

    public function renderHtml()
    {
        $isSet = \Yii::$app->request->cookies->getValue($this->name, false);
        if ($isSet !== false)
            return;
        
        $post = \Yii::$app->request->post('accept');
        
        if ($post) {
            $cookie = new Cookie([
                'name' => $this->name,
                'value' => $post,
                'expire' => time() + 86400 * 365,
                'domain' => \Yii::$app->request->serverName
            ]);
            \Yii::$app->response->cookies->add($cookie);
            \Yii::$app->controller->redirect(\Yii::$app->request->referrer);
        }
        echo $this->render('gdpr', [
            'description' => $this->description
        ]);
    }
}