<?php

namespace app\components;

use app\models\User;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use app\modules\page\models\Page;

class PageWidget extends SBaseWidget {

    public $id;
    public $limit = 0;
    public $title;
    public $icon = "fa fa-tasks";
    public $color;
    public $title_tag = 'h3';
    public $title_class = 'title';
    public $para_class = '';
    public $span_class = '';
    // public $style="";
    public $colors = array(
        'blue',
        'red',
        'yellow',
        'green'
    );

    public function init() {
        parent::init();
        ob_start();
    }

    public function run() {
        $content = ob_get_clean();
        $page = null;
        if ($this->id != null)
            $page = Page::findOne($this->id);
        else if ($this->title != null)
            $page = Page::find()->Where([
                        'title' => $this->title
                    ])->one();

        if ($page != null) {
            $this->renderPage($page);
        } else {
            echo Html::tag($this->title_tag, $this->title, array(
                'class' => $this->title_class
            ));
            if (User::isAdmin()) {
                echo Html::tag('a', '<i class="fa fa-plus has-circle"></i>', array(
                    'href' => Url::to([
                        'page/add',
                        'title' => $this->title
                    ]),
                    'target' => '_blank'
                ));
            }
        }
    }

    public function renderPage($page) {
        switch ($page->type_id) {
            case Page::TYPE_ARTICLE:
                $this->renderPageArticle($page);
                break;
            case Page::TYPE_PARA:
                $this->renderPagePara($page);
                break;
            case Page::TYPE_LINE:
                $this->renderPageContent($page);
                break;
        }
    }

    public function renderPageArticle($page) {
        if ($this->title != "privacy policy" && $this->title != "About Us" && $this->title != "Terms of Services") {
            echo Html::tag('h3', $page->title, [
                'class' => 'title'
            ]);
        }
        $this->renderPageContent($page);
    }

    public function renderPagePara($page) {
        if ($page->url != null) {
            echo Html::tag($this->title_tag, $page->getTempUrl(), array(
                'class' => $this->title_class
            ));
        } else {
            echo Html::tag($this->title_tag, $page->title, array(
                'class' => $this->title_class
            ));
        }
        $this->renderPageContent($page);
    }

    public function renderPageContent(Page $page) {
        if ($this->limit != 0) {
            $description = StringHelper::truncate($page->description, $this->limit);
            echo \yii\helpers\HtmlPurifier::process($description);
        } else {
            echo \yii\helpers\HtmlPurifier::process($page->description);
        }
    }

}
