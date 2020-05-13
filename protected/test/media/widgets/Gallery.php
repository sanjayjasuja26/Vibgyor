<?php
namespace app\modules\media\widgets;

use app\modules\media\models\File;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

// echo Gallery::widget([
// 'images' => [
// [
// 'url' => 'http://demo.michaelsoriano.com/images/photodune-174908-rocking-the-night-away-xs.jpg',
// 'title' => 'Hello'
// ]
// ]
// ])
class Gallery extends Widget
{

    const BOOTSTRAP_PHOTO_GALLER = 'bootstrap-photo-gallery';

    public $layout = null;

    public $delete = true;

    public $model;

    public $dataProvider;

    /*
     * array of images or model with arribute
     *
     * 'images' => [
     * [
     * 'thumb' => 'http://demo.michaelsoriano.com/images/photodune-174908-rocking-the-night-away-xs.jpg',
     * 'url' => 'http://demo.michaelsoriano.com/images/photodune-174908-rocking-the-night-away-xs.jpg',
     * 'title' => 'Hello',
     * 'deleteUrl' => [],
     * 'id' => ''
     * ]
     * ]
     *
     * OR
     *
     * 'images' => [
     * 'model' => 'app\modules\media\models\file',
     * 'attribute' => 'file' (By default)
     * ]
     *
     */
    public $images;

    public $id = "bootstrap-photo-gallery";

    public $class;

    public $options;

    public function init()
    {
        parent::init();
        if (empty($this->layout))
            $this->layout = self::BOOTSTRAP_PHOTO_GALLER;
        
        $this->imageModal();
        $this->getLayout();
    }

    public function imageModal()
    {
        if (empty($this->images) && empty($this->dataProvider)) {
            $this->createDataProvider();
            $this->images = $this->getData();
        } elseif (! empty($this->dataProvider)) {
            $this->images = $this->getData();
        }
        
        return $this->images;
    }

    protected function getData()
    {
        $images = [];
        foreach ($this->dataProvider->models as $image) {
            $images[] = [
                'thumb' => \Yii::$app->urlManager->createAbsoluteUrl([
                    '/media/file/image',
                    'id' => $image->id
                ]),
                'url' => \Yii::$app->urlManager->createAbsoluteUrl([
                    '/media/file/image',
                    'id' => $image->id,
                    'thumb' => false
                ]),
                'deleteUrl' => \Yii::$app->urlManager->createAbsoluteUrl([
                    '/media/file/delete',
                    'id' => $image->id
                ]),
                'title' => isset($image['title']) ? $image['title'] : "Image",
                'id' => $image->id
            ];
        }
        return $images;
    }

    protected function createDataProvider()
    {
        if (! empty($this->model->id)) {
            $query = File::find()->where([
                'model_id' => $this->model->id,
                'model_type' => $this->model::className()
            ]);
        } else {
            $query = File::find();
        }
        
        return $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);
    }

    private function getLayout()
    {
        switch ($this->layout) {
            case self::BOOTSTRAP_PHOTO_GALLER:
                echo $this->render('_thumbnail_gallery', [
                    'delete' => $this->delete,
                    'model' => $this->model,
                    'images' => $this->images,
                    'id' => $this->id,
                    'class' => $this->class,
                    'options' => $this->options
                ]);
                break;
        }
    }
}
