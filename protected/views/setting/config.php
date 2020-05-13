<?php
use yii\helpers\Html;
use app\components\TActiveForm;
use app\models\Setting;

/* @var $this yii\web\View */
/* @var $model app\models\Setting */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="wrapper">
	<div class="user-index">
		<div class=" panel ">
			<div class="setting-index">
				<?= \app\components\PageHeader::widget(); ?>
  			</div>
		</div>
		<div class="panel panel-margin">
			<div class="panel-body">

    			<?php
							$form = TActiveForm::begin ( [ 
									'layout' => 'horizontal',
									'enableClientValidation' => true,
									'enableAjaxValidation' => true,
									'id' => 'setting-form' 
							] );
							?>

				<div class="form-group" id="configValue">
					<div id="configValueContainer">
						<div class="col-md-12 config-value single-config-1">
							<div class="form-group">
								<div class="col-md-3">
									<input type="text" required id="setting-keyName-1"
										class="form-control settingKey" name="Setting[keyName][1]"
										placeholder="Name">
									<p class="text-danger"><?= \Yii::t("app", "Only Character or Underscore is allowed.") ?></p>
								</div>
								<div class="col-md-3">
									<?= $form->field($model, 'keyType[1]', ['template' => '{input}{error}'])->dropDownList($model->getTypeOptions(), ['id' => 'setting-keyType-1', 'class' => 'setKeyType', 'data-id' => 1])->label(false) ?>
								</div>
								<div class="col-md-3" id="setting-keyValue-container-1">
									<input type="text" required id="setting-keyValue-1"
										class="form-control" name="Setting[keyValue][1]"
										placeholder="Value">
								</div>
								<div class="col-md-3">
									<input type="checkbox" id="setting-keyRequired-1" class=""
										name="Setting[keyRequired][1]"> Required
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12 text-right">
						<a class="btn btn-primary add-more" href="javascript:;"> <i
							class="fa fa-plus"></i>
						</a>
        				<?= Html::submitButton(Yii::t('app', 'Save'), ['name'=>'setting-button','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    				</div>
				</div>

				<?php TActiveForm::end(); ?>
			</div>
		</div>
	</div>
</div>

<script>
$(document).on("change", ".setKeyType", function (e) {
	var type = $(this).val();
	var dataId = $(this).attr("data-id");
	var html = setKeyType(type, dataId);
	$("#setting-keyValue-container-"+dataId).html(html);
});

$(".add-more").on("click", function () {
	var id = $(".config-value").length;
	$("#configValueContainer").append(renderHtml (parseInt(id) +1));
});

$(document).on("click", ".delete-field",function () {
	var id = $(this).attr("data-id");
	$(".single-config-"+id).remove();
});

$(document).on("keyup", ".settingKey",function (e) {
	var check = /^[_a-zA-Z]*$/;
	if (!check.test($(this).val())) {
		alert("<?= \Yii::t('app', 'Only Character or Underscore is allowed.'); ?>");
		$(this).val("");
		e.preventDefault();
	}
});

function renderHtml (id) {
	var html = '<div class="col-md-12 config-value single-config-'+id+'"><div class="form-group">';
	html += '<div class="col-md-3">';
	html += '<input type="text" required id="setting-keyName-'+id+'" class="form-control settingKey" name="Setting[keyName]['+id+']" placeholder="Name">';
	html += '<p class="text-danger"><?= \Yii::t("app", "Only Character or Underscore is allowed.") ?></p></div>';
	
	
	html += '<div class="col-md-3"><div class="form-group field-setting-keyType-'+id+' has-success">';
	html += '<select id="setting-keyType-'+id+'" class="setKeyType" name="Setting[keyType]['+id+']" data-id="'+id+'" aria-invalid="false">';
		<?php foreach ( $model->getTypeOptions() as $key => $value) { ?>
	html += '<option value="<?= $key ?>"><?= $value ?></option>';
		<?php } ?>
	html += '</select></div></div>';
	
	html += '<div class="col-md-3"  id="setting-keyValue-container-'+id+'">';
	html += '<input type="text" required id="setting-keyValue-'+id+'" class="form-control" name="Setting[keyValue]['+id+']" placeholder="Value"></div>';
	html += '<div class="col-md-2"><input type="checkbox" id="setting-keyRequired-'+id+'" class="" name="Setting[keyRequired]['+id+']"> Required</div>';
	html += '<div class="col-md-1 text-right"><a class="btn btn-primary delete-field" data-id="'+id+'" href="javascript:;"> <i class="fa fa-minus"></i></a></div>';
	html += '</div></div>';
	return html;
}

function setKeyType(type, dataId) {
	var html = "";
	switch(type) {
		case "<?= Setting::KEY_TYPE_STRING ?>":
				html += '<input type="text" required="" id="setting-keyValue-'+dataId+'" class="form-control" name="Setting[keyValue]['+dataId+']" placeholder="Value">';
			break;
		case "<?= Setting::KEY_TYPE_BOOL ?>":
				html += '<input type="checkbox" id="setting-keyValue-'+dataId+'" class="form-control" name="Setting[keyValue]['+dataId+']" placeholder="Value">';
			break;
		case "<?= Setting::KEY_TYPE_INT ?>":
				html += '<input type="number" id="setting-keyValue-'+dataId+'" class="form-control" name="Setting[keyValue]['+dataId+']" placeholder="Value">';
			break;
		case "<?= Setting::KEY_TYPE_EMAIL ?>":
			html += '<input type="email" id="setting-keyValue-'+dataId+'" class="form-control" name="Setting[keyValue]['+dataId+']" placeholder="Value">';
		break;
	}	
	return html;
}
</script>