<?php
namespace app\modules\notification\widgets;

use app\modules\notification\assets\NotificationAsset;
use app\modules\notification\models\Notification;
use yii\base\Widget;
use yii\helpers\Url;

class NotificationWidget extends Widget
{

    public $browserMode = false;

    public $id = "notification-container";

    public $class = 'dropdown one-icon mega-li';

    public $options;

    public function init()
    {
        parent::init();
        
        $this->getView()->registerAssetBundle(NotificationAsset::class);
    }

    public function run()
    {
        $this->renderHtml();
    }

    public function renderHtml()
    {
        $url = Url::toRoute([
            '/notification/notification/notify'
        ]);
        $url_home = Url::toRoute([
            '/notification/notification'
        ], true);
        
        $count = Notification::find()->where([
            'is_read' => Notification::IS_NOT_READ,
            'to_user_id' => \Yii::$app->user->id
        ])->count();
        
        $mode = $this->browserMode? 1:0;
        $js = "";
        
        if ($this->browserMode)
            $js .= "
        function onShowNotification () {
            console.log('notification is shown!');
        }
    
        function onCloseNotification () {
            console.log('notification is closed!');
        }
    
        function onClickNotification () {
            console.log('notification was clicked!');
            window.location.href ='$url_home';
        }
    
        function onErrorNotification () {
            console.error('Error showing notification. You may need to request permission.');
        }
    
        function onPermissionGranted () {
            console.log('Permission has been granted by the user');
            //doNotification('OK');
        }
    
        function onPermissionDenied () {
            console.warn('Permission has been denied by the user');
        }
    
        function doNotification (response) {
            message = 'You have notifications.';
            
           var myNotification = new Notify('" . \Yii::$app->name . "' , {
                body: message,
                tag: response.count,
                notifyShow: onShowNotification,
                notifyClose: onCloseNotification,
                notifyClick: onClickNotification,
                notifyError: onErrorNotification,
                timeout: 5
            });
    
            myNotification.show(); 
            console.log('notification was :' +message);
        }
    
        if (!Notify.needsPermission) {
        } else if (Notify.isSupported()) {
            Notify.requestPermission(onPermissionGranted, onPermissionDenied);
        }
        ";
        
        $js .= "

        var count = $count;
        setInterval(function() {  
                $.ajax({
                    url : '$url',
                    success : function (response) {
                        if( response.status = 200 ) {
                            var html = '';    
                            $.each(response.data, function (key, value) {
                                html += value;
                            } );
                            $('.message-center-{$this->id}').empty();  
                            $('.message-center-{$this->id}').append(html);    

                            $('.notiCount-{$this->id}').empty();
                            $('.notiCount-{$this->id}').append(response.count);
                            if ( count != response.count)
                            {
                                if ($mode) doNotification(response);
                                count = response.count;
                            }

                        }
                    }
                });
            }, 3000);";
        
        $this->getView()->registerJs($js);
        
        echo $this->render('_notification', [
            'id' => $this->id,
            'class' => $this->class,
            'count' =>$count
        ]);
    }
}
