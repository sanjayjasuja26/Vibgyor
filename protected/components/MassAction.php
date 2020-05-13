<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class MassAction extends Widget
{

    /**
     * public function actionMass($action = 'delete')
     * {
     * \Yii::$app->response->format = 'json';
     * $response['status'] = 'NOK';
     * $Ids = Yii::$app->request->post('ids', []);
     * $status = YourModel::massDelete($Ids);
     * if ($status == true) {
     * $response['status'] = 'OK';
     * }
     * return $response;
     * }
     *
     * echo MassAction::widget([
     * 'url' => Url::toRoute([
     * '/page/mass'
     * ]),
     * 'grid_id' => 'page-grid-view',
     * 'pjax_grid_id' => 'page-pjax-grid'
     * ]);
     *
     * @var string
     */
    public $visible = true;

    public $url;

    public $confirmMsg = 'Do you really want to delete these items?';

    public $grid_id;

    public $pjax_grid_id;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $this->view->registerJs($this->js());
        $this->renderHtml();
    }

    public function renderHtml()
    {
        if ($this->visible == false) {
            return;
        }
        
        echo Html::a('<span class="fa fa-trash text-white"></span>', 'javascript:;', [
            'class' => 'multiple-delete btn btn-info',
            'id' => "bulk-delete"
        ]);
    }

    protected function js()
    {
        $js = <<<JS
        $('#bulk-delete').click(function(e) {
    		e.preventDefault();
    		 var keys = $("#{$this->grid_id}").yiiGridView('getSelectedRows');
    		 if ( keys != '' ) {
    			var ok = confirm("{$this->confirmMsg}");
    			if( ok ) {
    				$.ajax({
    					url  : "{$this->url}",
    					type : 'POST',
    					data : {
    						ids : keys
    					},
    					success : function( response ) {
    						if ( response.status == 'OK' ) {
    							 $('#error_flash').show();
            							 $.pjax.reload({container: "#{$this->pjax_grid_id}"});
            						} else {
            							 $('#error_flash').hide();
            						}
            					}
            			     });
            			}
            		 } else {
            			alert('Please select items to delete');
            		 }
            	});
JS;
        return $js;
    }
}