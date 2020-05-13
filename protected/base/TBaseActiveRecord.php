<?php
/**
 *
*@author     : Sanjay Jasuja< sanjayjasuja26@gmail.com >
 *
 */
namespace app\base;

use app\models\User;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the generic model class
 */
class TBaseActiveRecord extends ActiveRecord
{

    public function isAllowed()
    {
        if (User::isAdmin())
            return true;
        if ($this instanceof User) {
            return ($this->id == Yii::$app->user->id);
        }

        return User::isUser();
    }

    public function displayImage($file, $options = [], $defaultImg = 'default.png', $isThumb = false)
    {
        $opt = [
            'class' => 'img-fluid',
            'id' => 'profile_file'
        ];

        $arr = array_merge($opt, $options);
        if ($isThumb) {
            $url = [
                '/file/file/thumbnail',
                'filename' => $file
            ];
        } else {
            $url = [
                '/file/file/files',
                'file' => $file
            ];
        }

        if (! empty($file) && file_exists(UPLOAD_PATH . '/' . $file)) {
            return Html::img($url, $arr);
        } else {
            return Html::img(\Yii::$app->view->theme->getUrl('/img/') . $defaultImg, $arr);
        }
    }

    public static function massDelete($action = 'delete')
    { 
        $Ids = \Yii::$app->request->post('ids', []);
        $response['status'] = 'OK';
        if (! empty($Ids)) {
            try {
                foreach ($Ids as $Id) {
                    $model = self::findOne($Id);
                    if (! empty($model) && ($model instanceof ActiveRecord)) {
                        if ($action == 'delete') {
                            if (($model instanceof User) && ($model->id == \Yii::$app->user->id)) {
                                throw new \Exception('Could not delete');
                            } else {
                                $model->delete();
                            }
                        } else {
                            throw new \Exception('Delete Action not performed');
                        }
                    }
                }
            } catch (\Exception $e) {
                $response['status'] = 'NOK';
                $response['error'] = $e->getMessage();
            }
        }
        \Yii::$app->response->format = 'json';
        return $response;
    }

}
