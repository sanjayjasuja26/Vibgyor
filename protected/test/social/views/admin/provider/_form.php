<?php
use yii\helpers\Html;
use app\components\SActiveForm;
use app\modules\social\models\Provider;

/* @var $this yii\web\View */
/* @var $model app\models\Provider */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="panel">
	<div class="panel-header">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" class="tabClick"
				data-client="<?= Provider::PROVIDER_FACEBOOK ?>"
				data-value="facebook" data-number="2" href="#home"><i
					class="fa fa-facebook"></i>&nbsp;&nbsp;&nbsp;Facebook</a></li>
			<li><a data-toggle="tab" class="tabClick" data-value="Google"
				data-client="<?= Provider::PROVIDER_GOOGLE ?>" data-number="1"
				href="#menu1"><i class="fa fa-google-plus"></i>&nbsp;&nbsp;&nbsp;Google
					+</a></li>
			<li><a data-toggle="tab" class="tabClick" data-value="Twitter"
				data-client="<?= Provider::PROVIDER_TWITTER ?>" data-number="7"
				href="#menu2"><i class="fa fa-twitter"></i>&nbsp;&nbsp;&nbsp;Twitter</a></li>
			<li><a data-toggle="tab" class="tabClick" data-value="Linkedin"
				data-client="<?= Provider::PROVIDER_LINKEDIN ?>" data-number="5"
				href="#menu3"><i class="fa fa-linkedin"></i>&nbsp;&nbsp;&nbsp;Linkedin</a></li>
			<li><a data-toggle="tab" class="tabClick" data-value="Github"
				data-client="<?= Provider::PROVIDER_GITHUB ?>" data-number="3"
				href="#menu4"><i class="fa fa-github"></i>&nbsp;&nbsp;&nbsp;Github</a></li>
			<li><a data-toggle="tab" class="tabClick" data-value="Google Hybrid"
				data-client="<?= Provider::PROVIDER_GOOGLEHYBRID ?>" data-number="4"
				href="#menu5"><i class="fa fa-google"></i>&nbsp;&nbsp;&nbsp;Google
					Hybrid</a></li>
			<li><a data-toggle="tab" class="tabClick"
				data-client="<?= Provider::PROVIDER_LIVE ?>"
				data-value="Microsoft(Live)" data-number="6" href="#menu6"> <i
					class="fa fa-windows"></i>&nbsp;&nbsp;&nbsp;Microsoft(Live)
			</a></li>
		</ul>

	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-6">
				<div class="tab-content">
					<div id="home" class="tab-pane fade in active">
						<div class="row">
							<div class="col-sm-12">
								<div id="home" class="tab-pane fade in active">
									<h3>Facebook</h3>
									<p>
										Redirect link/Callback Url:-<b><i> <?=$model->getDomainUrl(Provider::PROVIDER_FACEBOOK);?></i></b>
									</p>
									<p>
										Go to <a title="click here for link"
											href="https://developers.facebook.com/apps" target="_blank">
											https://developers.facebook.com/apps</a> and create a new
										application by clicking "Create New App".
									</p>
									<p>Put your website domain in the Site Url field.</p>
									<p>Once you have registered, copy and past the created
										application credentials (App ID and Secret) into config</p>
									<p>You also need to add a valid Oauth redirect URI
										(https://developers.facebook.com/docs/facebook-login/security/).
									</p>

									<p>
										Please refer this <a title="click here for link"
											href="https://hybridauth.github.io/hybridauth/userguide.html"
											target="_blank"> link</a>
									</p>
								</div>
							</div>

						</div>
					</div>
					<div id="menu1" class="tab-pane fade">
						<h3>Google</h3>
						<p>
							Redirect link/Callback Url:-<b><i> <?=$model->getDomainUrl(Provider::PROVIDER_GOOGLE);?></i></b>
						</p>

						<p>
							Go to the <a title="click here for link"
								href="      https://accounts.google.com/signin/v2/identifier?service=cloudconsole&passive=1209600&osid=1&continue=https%3A%2F%2Fconsole.developers.google.com%2Fapis%2Flibrary%3Fproject%3D_%26ref%3Dhttps%3A%2F%2Fhybridauth.github.io%2Fhybridauth%2Fuserguide%2FIDProvider_info_Google.html&followup=https%3A%2F%2Fconsole.developers.google.com%2Fapis%2Flibrary%3Fproject%3D_%26ref%3Dhttps%3A%2F%2Fhybridauth.github.io%2Fhybridauth%2Fuserguide%2FIDProvider_info_Google.html&flowName=GlifWebSignIn&flowEntry=ServiceLogin
"
								target="_blank"> Google Developers Console</a>.
						</p>
						<p>From the project drop-down, select a project, or create a new
							one.</p>
						<p>Enable the Google+ API service:</p>
						<p>In the list of Google APIs, search for the Google+ API service.</p>
						<p>Select Google+ API from the results list.</p>
						<p>Press the Enable API button.</p>
						<p>When the process completes, Google+ API appears in the list of
							enabled APIs. To access, select API Manager on the left sidebar
							menu, then select the Enabled APIs tab.</p>
						<p>In the sidebar under "API Manager", select Credentials.</p>
						<p>In the Credentials tab, select the New credentials drop-down
							list, and choose OAuth client ID.</p>
						<p>From the Application type list, choose the Web application.</p>
						<p>Enter a name and provide this URL as Authorized redirect URIs:
							http://mywebsite.com/path_to_hybridauth/?hauth.done=Google then
							select Create.</p>
						<p>Once you have registered, copy and past the created application
							credentials (Client ID and Secret) into config</p>
						<p>
							Please refer this <a title="click here for link"
								href="https://hybridauth.github.io/hybridauth/userguide.html"
								target="_blank"> link</a>
						</p>

					</div>
					<div id="menu2" class="tab-pane fade">
						<h3>Twitter</h3>
						<p>
							Redirect link/Callback Url:-<b><i> <?=$model->getDomainUrl(Provider::PROVIDER_TWITTER);?></i></b>
						</p>


						<p>
							Go to <a title="click here for link"
								href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps:</a>
							and create a new application.
						</p>
						<p>Fill out any required fields such as the application name and
							description.</p>
						<p>Put your website domain in the Website field.</p>
						<p>Provide this URL as the Callback URL for your application:
							(http://mywebsite.com/path_to_hybridauth/?hauth.done=Twitter).</p>
						<p>Once you have registered, copy and past the created application
							credentials (Consumer Key and Secret) into config .</p>
						<p>
							Please refer this <a title="click here for link"
								href="https://hybridauth.github.io/hybridauth/userguide.html"
								target="_blank"> link</a>
						</p>

					</div>
					<div id="menu3" class="tab-pane fade">
						<h3>Linkedin</h3>
						<p>
							Developer <a title="click here for link"
								href="https://www.linkedin.com/start/join?session_redirect=https%3A%2F%2Fwww.linkedin.com%2Fdeveloper%2Fapps&trk=login_reg_redirect"
								target="_blank"> Site</a>
						</p>
						<p>
							Redirect link/Callback Url:-<b><i> <?=$model->getDomainUrl(Provider::PROVIDER_LINKEDIN);?></i></b>
						</p>
						<p>
							create an application. If you have an existing application,
							select it to modify its settings using this <a
								title="click here for link"
								href="https://hybridauth.github.io/hybridauth/userguide/IDProvider_info_LinkedIn.html"
								target="_blank"> link</a>
						</p>
						<p>Once you save your configuration, your application will be
							assigned a unique "Client ID" (otherwise known as Consumer Key or
							API key) and "Client Secret" value. Make note of these values —
							you will need to integrate them into the configuration files or
							the actual code of your application</p>
						<p>
							Please refer this <a title="click here for link"
								href="https://hybridauth.github.io/hybridauth/userguide.html"
								target="_blank"> link</a>
						</p>
					</div>
					<div id="menu4" class="tab-pane fade">
						<h3>Github</h3>
						<p>
							Redirect link/Callback Url:-<b><i> <?=$model->getDomainUrl(Provider::PROVIDER_GITHUB);?></i></b>
						</p>
						<p>
							Create and register an OAuth App under your personal account
							using this <a title="click here for link"
								href="https://github.com/login?return_to=https%3A%2F%2Fgithub.com%2Fsettings%2Fapplications%2Fnew"
								target="_blank"> link</a>
						</p>
						<p>In the upper-right corner of any page, click your profile
							photo, then click Settings.</p>
						<p>In the left sidebar, click Developer settings.</p>
						<p>In the left sidebar, click OAuth Apps.</p>
						<p>Click New OAuth App.</p>
						<p>
							<b>Note: If you haven't created an app before, this button will
								say, Register a new application.</b>
						</p>
						<p>In "Application name", type the name of your app.</p>
						<p>In "Homepage URL", type the full URL to your app's website.</p>
						<p>Optionally, in "Application description", type a description of
							your app that users will see.</p>
						<p>In "Authorization callback URL", type the callback URL of your
							app.</p>
						<p>
							<b>Click Register application.</b>
						</p>

						<p>
							Please refer this <a title="click here for link"
								href="https://hybridauth.github.io/hybridauth/userguide.html"
								target="_blank"> link</a>
						</p>
					</div>
					<div id="menu5" class="tab-pane fade">
						<h3>Google Hybrid</h3>
						<p>
							Redirect link/Callback Url:-<b><i> <?=$model->getDomainUrl(Provider::PROVIDER_GOOGLEHYBRID);?></i></b>
						</p>
						<p>
							Please refer this <a title="click here for link"
								href="https://hybridauth.github.io/hybridauth/userguide.html"
								target="_blank"> link</a>
						</p>
					</div>
					<div id="menu6" class="tab-pane fade">
						<h3>Microsoft(Live)</h3>

						<p>
							Sign in to the Windows Live <a title="click here for link"
								href="https://go.microsoft.com/fwlink/?LinkId=193157"
								target="_blank"> application management site</a>
							(https://apps.dev.microsoft.com/).
						</p>
						<p>
							Click <b>Add an app</b> on this page.
						</p>
						<p>Type an application name. This is the name that users will see
							in the Windows Live user interface (UI). The application name
							should include your company name or the name of your web site.</p>
						<p>
							Generate <b>Application</b> Secret by clicking <b>Generate New
								Password</b>.
						</p>
						<p>
							Select <b>Web</b> as a platform by clicking <b>Add Platform</b>.
						</p>
						<p>Provide this URL as Redirect URLs:
							https://mywebsite.com/path_to_hybridauth/live.php</p>
						<p>Click Save. This action registers your application.</p>

						<p>
							Redirect link/Callback Url:-<b><i> <?=$model->getDomainUrl(Provider::PROVIDER_LIVE);?></i></b>
						</p>
						<p>
							Please refer this <a title="click here for link"
								href="https://hybridauth.github.io/hybridauth/userguide.html"
								target="_blank"> link</a>
						</p>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
  	<?php
			$form = SActiveForm::begin ( [ 
					'id' => 'social-provider-form' 
			] );
			?>
		 <?php
			
			echo $form->field ( $model, 'title' )->textInput ( [ 
					'maxlength' => 256 
			] )?>
	 		
	 		<div style="display: none;">
		 <?php
			
			echo $form->field ( $model, 'provider_type' )->dropDownList ( $model->getClientOptions () )?>
	 		</div>
		 <?php
			
			echo $form->field ( $model, 'client_id' )->textInput ()?>

		 <?php
			
			echo $form->field ( $model, 'client_secret_key' )->textInput ( [ 
					'maxlength' => 255 
			] )?>

		 <?php
			
			echo $form->field ( $model, 'state_id' )->dropDownList ( $model->getStateOptions () )?>
	   <div class="form-group">
					<div class=" bottom-admin-button btn-space-bottom text-right">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', 'Submit') : Yii::t('app', 'Update'), ['id' => 'social-provider-form-submit','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>
				</div>

    <?php
				
				SActiveForm::end ();
				?>
  </div>


		</div>
	</div>
</div>
<div class="panel-body"></div>
<script>
$(".tabClick").click(function () {
     var value = $(this).data('value');
     var type = $(this).data('client');
     $("#provider-provider_type").val(type);
     $("#socialprovider-title").val(value);
});
</script>