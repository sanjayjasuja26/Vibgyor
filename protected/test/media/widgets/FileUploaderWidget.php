<?php
/**
 * @copyright Copyright (c) 2014 karpoff
 * @link https://github.com/karpoff/yii2-crop-image-upload
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace app\modules\media\widgets;

use app\modules\media\assets\ImageUploadAsset;
use yii\base\Widget;

/**
 * CropImageUpload renders a jCrop plugin for image crop.
 *
 * @see http://deepliquid.com/content/Jcrop.html
 * @link https://github.com/karpoff/yii2-crop-image-upload
 * @package karpoff\icrop
 */

/**
 *
 * var
 * manualUploader = new qq.FineUploader({
 * element: document.getElementById("fineuploader-container"),
 * request: {
 * endpoint: "/vendor/fineuploader/php-traditional-server/endpoint.php"
 * },
 * deleteFile: {
 * enabled: true,
 * endpoint: "/vendor/fineuploader/php-traditional-server/endpoint.php"
 * },
 * chunking: {
 * enabled: true,
 * concurrent: {
 * enabled: true
 * },
 * success: {
 * endpoint: "/vendor/fineuploader/php-traditional-server/endpoint.php?done"
 * }
 * },
 * resume: {
 * enabled: true
 * },
 * retry: {
 * enableAuto: true,
 * showButton: true
 * }
 * });
 *
 * @param $options['template'] =
 *            qq-template-gallery, qq-template-manual-trigger
 * @param string $options['url']
 *            = Url for upload files
 * @param array $options['extensions']
 *            = validation for file extension ['jpeg', 'jpg', 'gif', 'png']
 * @param string $options['limit']
 *            = File limit
 * @param string $options['size']
 *            = File size
 */
class FileUploaderWidget extends Widget
{

    public $id = 'file-uploader';

    public $model;

    public $createUserId = null;

    public $typeId = null;

    public $options;

    /**
     *
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        $assets = ImageUploadAsset::register($view);
        $this->options = [
            'assets' => $assets
        ];
        
        if (! isset($this->options['url'])) {
            $this->options['url'] = \Yii::$app->urlManager->createAbsoluteUrl([
                '/media/file/upload',
                'modelId' => $this->model->id,
                'modelType' => $this->model::className()
            ]);
        }
        if (! isset($this->options['deleteUrl'])) {
            $this->options['deleteUrl'] = \Yii::$app->urlManager->createAbsoluteUrl([
                '/media/file/delete-file'
            ]);
        }
        if (! isset($this->options['template'])) {
            $this->options['template'] = 'qq-template-gallery';
        }
        $this->renderHtml();
    }

    public function renderHtml()
    {
        echo $this->render('_upload_drag_drop_view', [
            'id' => $this->id,
            'options' => $this->options
        ]);
    }
} 