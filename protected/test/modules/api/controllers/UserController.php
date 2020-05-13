<?php
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
namespace app\modules\api\controllers;

use app\models\AuthSession;
use app\models\LoginForm;
use app\models\User;
use app\modules\api\controllers\ApiTxController;
use yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\ArrayHelper;
use app\models\Log;
use app\models\RewardRedeem;
use yii\data\ActiveDataProvider;
use app\models\Reward;
use yii\base\Exception;
use app\models\HaLogins;
use app\modules\affiliate\models\Code;
use yii\web\UploadedFile;
use yii\helpers\VarDumper;
use app\modules\affiliate\models\History;
use app\modules\notification\models\Notification;
use app\models\Project;
use app\modules\api\Api;
use yii\authclient\clients\Yandex;

/**
 * UserController implements the API actions for User model.
 */
class UserController extends ApiTxController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className()
                ],
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'check',
                            'get',
                            'update-profile',
                            'delete',
                            'view',
                            'add',
                            'logout',
                            'change-password',
                            'profile',
                            'add-log',
                            'reward-redeem',
                            'social-login',
                            'my-affiliate-code',
                            'image',
                            'notification-list',
                            'notification-setting',
                            'view-notification',
                            'delete-notification'
                        ],
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                    ],
                    [
                        'actions' => [
                            
                            'login',
                            'signup',
                            'recover',
                            'check',
                            'mode',
                            'beat',
                            'get',
                            'instagram',
                            'add-log',
                            'forget-password',
                            'social-login',
                            'my-affiliate-code',
                            'image'
                        ],
                        'allow' => true,
                        'roles' => [
                            '?',
                            '*'
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->txIndex("\app\models\search\User");
    }

    /**
     * Displays a single User model.
     *
     * @return mixed
     */
    public function actionGet($id)
    {
        return $this->txget($id, "app\models\User");
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        return $this->txSave("app\models\User");
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionUpdateProfile($id)
    {
        $data = [];
        $model = $this->findModel($id);
        
        /*
         * if (empty($model)) {
         * $data['error'] = 'No user found';
         * return $this->sendResponse($data);
         * }
         */
        $old_image = $model->profile_file;
        // $image = UploadedFile::getInstance($model, 'profile_file');
        
        if ($model->load(Yii::$app->request->post())) {
            $data['image'] = $_FILES;
            if (empty($_FILES)) {
                $model->profile_file = $old_image;
            }
            $model->saveUploadedFile($model, 'profile_file');
            
            if (! $model->save()) {
                $data['error'] = $model->getErrorsString();
                return $this->sendResponse($data);
            }
            $data['status'] = self::API_OK;
            $data['detail'] = $model->asJson();
        } else {
            $data['error_post'] = 'No Data Posted';
        }
        return $this->sendResponse($data);
    }

    public function actionProfile()
    {
        $data = [];
        $user = Yii::$app->user->id;
        $model = User::find()->where([
            'id' => $user
        ])->one();
        if (! empty($model)) {
            $data['status'] = self::API_OK;
            $data['list'] = $model->asJson();
        } else {
            $data['error'] = 'Profile Not Found';
        }
        return $this->sendResponse($data);
    }

    public function actionCheck()
    {
        $data = [];
        if (! \Yii::$app->user->isGuest) {
            $user = \Yii::$app->user->identity;
            $data['status'] = self::API_OK;
            $data['detail'] = $user->asJson();
        } else {
            
            $headers = getallheaders();
            $auth_code = isset($headers['auth_code']) ? $headers['auth_code'] : null;
            if ($auth_code == null)
                $auth_code = \Yii::$app->request->getQueryString('auth_code');
            if ($auth_code) {
                $auth_session = AuthSession::find()->where([
                    'auth_code' => $auth_code
                ])->one();
                if ($auth_session) {
                    $data['status'] = self::API_OK;
                    if (isset($_POST['AuthSession'])) {
                        $auth_session->device_token = $_POST['AuthSession']['device_token'];
                        if ($auth_session->save()) {
                            $data['auth_session'] = 'Auth Session updated';
                        } else {
                            $data['error'] = $auth_session->flattenErrors;
                        }
                    }
                } else
                    $data['error'] = 'session not found';
            } else {
                $data['error'] = 'Auth code not found';
                $data['auth'] = isset($auth_code) ? $auth_code : '';
            }
        }
        
        return $this->sendResponse($data);
    }

    public function actionSignup()
    {
        $data = [];
        $model = new User([
            'role_id' => User::ROLE_USER,
            'state_id' => User::STATE_ACTIVE
        ]);
        $login = new LoginForm();
        $model->loadDefaultValues();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $login->load($post);
            $email_identify = User::findByUsername($model->email);
            if (empty($email_identify)) {
                $code = isset($post['User']['affiliate_code']) ? $post['User']['affiliate_code'] : '';
                $model->setPassword($model->password);
                if (empty($code)) {
                    if ($model->save()) {
                        \Yii::$app->user->login($model, 3600 * 24 * 30);
                        $data['auth_code'] = AuthSession::newSession($login)->auth_code;
                        $data['status'] = self::API_OK;
                        $data['detail'] = $model->asJson();
                    }
                }
                if (! empty($code)) {
                    $history = new History();
                    $invite = Code::find()->where([
                        'code' => $code
                    ])->one();
                    if (! empty($invite)) {
                        if ($model->save()) {
                            \Yii::$app->user->login($model, 3600 * 24 * 30);
                        }
                        $history->code_id = $invite->id;
                        $history->created_on = date('Y-m-d H:i:s');
                        $history->created_by_id = $model->id; // \Yii::$app->user->id;
                        $history->updateAttributes([
                            'code_id',
                            'created_on',
                            'created_by_id'
                        ]);
                        $data['auth_code'] = AuthSession::newSession($login)->auth_code;
                        $data['status'] = self::API_OK;
                        $data['detail'] = $model->asJson();
                    } else {
                        $data['error'] = 'Affiliate Code is Not Valid';
                        $data['status'] = self::API_NOK;
                    }
                } else {
                    
                    $data['error'] = $model->errorsString;
                }
            } else {
                $data['error'] = "Email already exists.";
            }
        } else {
            $data['error'] = \Yii::t('app', 'No data posted');
        }
        
        return $this->sendResponse($data);
    }

    /**
     *
     * @return string|string[]|NULL[]
     */
    public function actionLogin()
    {
        $data = [];
        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post())) {
            $user = User::findByUsername($model->username);
            if ($user) {
                if ($model->login()) {
                    
                    $data['auth_code'] = AuthSession::newSession($model)->auth_code;
                    $data['detail'] = $user->asJson();
                    // $data['user_detail'] = $user->asJson();
                    
                    $data['status'] = self::API_OK;
                    // $data ['auth_code'] = AuthSession::newSession ( $model )->auth_code;
                    // $data ['detail'] = $model->asJson ();
                } else {
                    $data['error'] = 'Incorrect Password';
                }
            } else {
                $data['error'] = ' Incorrect Username';
            }
        } else {
            $data['error'] = "No data posted.";
        }
        
        return $this->sendResponse($data);
    }

    public function actionLogout()
    {
        $data = [];
        $auth = AuthSession::deleteOldSession(\Yii::$app->user->id);
        if (Yii::$app->user->logout())
            $data['status'] = self::API_OK;
        
        return $this->sendResponse($data);
    }

    public function actionChangePassword()
    {
        $data = [];
        $data['post'] = $_POST;
        $model = User::findOne([
            'id' => \Yii::$app->user->identity->id
        ]);
        
        $newModel = new User([
            'scenario' => 'changepassword'
        ]);
        if ($newModel->load(Yii::$app->request->post()) && $newModel->validate()) {
            $model->setPassword($newModel->newPassword);
            if ($model->save()) {
                $data['status'] = self::API_OK;
            } else {
                $data['error'] = 'Incorrect Password';
            }
        }
        return $this->sendResponse($data);
    }

    public function actionAddLog()
    {
        $data = [];
        $model = new Log();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $email = $model->email;
                $view = 'errorlog';
                $sub = "An Error/Crash was reported : " . \Yii::$app->params['company'];
                Yii::$app->mailer->compose([
                    'html' => 'errorlog'
                ], [
                    'user' => $model
                ])
                    ->setTo(\Yii::$app->params['adminEmail'])
                    ->setFrom(\Yii::$app->params['logEmail'])
                    ->setSubject($sub)
                    ->send();
            }
        }
        $data['status'] = self::API_OK;
        return $this->sendResponse($data);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        return $this->txDelete($id, "User");
    }

    public function actionForgetPassword()
    {
        $data = [];
        $model = new User();
        if (isset($_POST['User']['email'])) {
            $email = trim($_POST['User']['email']);
            $user = User::findOne([
                'email' => $email
            ]);
            if ($user) {
                $user->generatePasswordResetToken();
                $user->save();
                $model->sendEmail($user);
                $data['message'] = 'Please check your email to reset your password';
                $data['status'] = self::API_OK;
                $data['recover-email'] = $user->email;
            } else {
                $data['error'] = 'Email is not registered';
            }
        } else {
            $data['error'] = 'Please enter Email Address';
        }
        return $this->sendResponse($data);
    }

    public function actionRewardRedeem($id, $page = null)
    {
        $data = [];
        $query = RewardRedeem::find()->where([
            'created_by_id' => $id
        ]);
        $dataprovider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => $page
            
            ]
        ]);
        
        if (! empty($dataprovider->getCount() > 0)) {
            foreach ($dataprovider->models as $model) {
                $list[] = $model->asJson();
            }
            $data['user_points'] = \Yii::$app->user->identity->points;
            $data['pageSize'] = $dataprovider->pagination->pageSize;
            $data['pageCount'] = $dataprovider->pagination->pageCount;
            $data['status'] = self::API_OK;
            $data['list'] = $list;
        } else {
            $data['error'] = \Yii::t('app', 'No Recharge Found');
        }
        return $this->sendResponse($data);
    }

    public function actionMyAffiliateCode()
    {
        $data = [];
        $model = Code::find()->where([
            'user_id' => \Yii::$app->user->identity->id
        ])->one();
        if (empty($model)) {
            $model = new Code();
            $model->code = Yii::$app->getSecurity()->generateRandomString(10);
            $model->user_id = \Yii::$app->user->identity->id;
            $model->end_date = date('Y-m-d H:i:s', strtotime('+7 day'));
            if ($model->save()) {
                $data['status'] = self::API_OK;
                $data['details'] = [
                    'code' => $model->code
                ];
            }
        }
        if (! empty($model)) {
            $data['status'] = self::API_OK;
            $data['details'] = [
                'code' => $model->code
            ];
        }
        
        return $this->sendResponse($data);
    }

    public function actionSocialLogin()
    {
        $flag = false;
        $data = [];
        $params = \Yii::$app->request->bodyParams;
        // VarDumper::dump($params);exit;
        if (! empty($params['User'])) {
            $auth = HaLogins::find()->where([
                'userId' => $params['User']['userId']
            ])->one();
            if (empty($auth)) {
                if (! $params['User']['affiliate_code'] && $params['User']['affiliate_code_check'] != 1) {
                    $data['error'] = 'Enter Affiliate Code';
                    $data['status'] = self::API_NOK;
                    return $this->sendResponse($data);
                }
                $code = $params['User']['affiliate_code'];
                
                $full_name = $params['User']['full_name'];
                // $contact_no = $params['User']['contact_no'];
                $role_id = User::ROLE_USER;
                $email = $params['User']['email'];
                $id = $params['User']['userId'];
                $provider = $params['User']['provider'];
                $token = $params['LoginForm']['device_token'];
                $type = $params['LoginForm']['device_type'];
                $email_identify = '';
                // $contact_identify = '';
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    // check user exist through email
                    if (! empty($email)) {
                        $email_identify = User::findByUsername($email);
                    }
                    // check user exist through contact no
                    /*
                     * if (! empty($contact_no)) {
                     * $contact_identify = User::findByUsername($contact_no);
                     * }
                     */
                    
                    if (! empty($email_identify) || ! empty($contact_identify)) {
                        if (! empty($email_identify)) {
                            $user = $email_identify;
                        } else {
                            $user = $contact_identify;
                        }
                        
                        if (empty($user)) {
                            $data['error'] = "User not found";
                            return $this->sendResponse($data);
                        }
                        $user->password = $user->setPassword($id);
                    } else {
                        $user = new User([
                            'full_name' => $full_name,
                            // 'contact_no' => $contact_no,
                            'email' => $email,
                            'password' => md5($id),
                            'role_id' => $role_id
                        ]);
                        
                        if (! empty($params['img_url'])) {
                            $random = rand(0, 999) . 'user.jpg';
                            $user->profile_file = $random;
                            copy($params['img_url'], UPLOAD_PATH . $random);
                        }
                        $user->generatePasswordResetToken();
                        
                        $user->state_id = User::STATE_ACTIVE;
                    }
                    // $randomString = Yii::$app->getSecurity()->generateRandomString(6);
                    // $user->sharing_code = $randomString;
                    
                    /* Checking for valid Affiliate (Invitation Code */
                    if (! empty($code)) {
                        $history = new History();
                        $invite = Code::find()->where([
                            'code' => $code
                        ])->one();
                        if (empty($invite)) {
                            $data['error'] = 'Affiliate Code is Not Valid';
                            $data['status'] = self::API_NOK;
                            return $this->sendResponse($data);
                        }
                    }
                    
                    if (! $user->save()) {
                        $data['error'] = $user->getErrorsString();
                        $data['customError'] = "user entry";
                        return $this->sendResponse($data);
                    } else {
                        $flag = true;
                    }
                    $auth = new HaLogins([
                        'userId' => (string) $id,
                        'loginProvider' => $provider,
                        'loginProviderIdentifier' => md5($id),
                        'user_id' => $user->id
                    ]);
                    if (! $auth->save()) {
                        $data['customError'] = "auth entry";
                        $data['error'] = $auth->getErrorsString();
                        return $this->sendResponse($data);
                    } else {
                        $flag = true;
                    }
                    $login_form = new LoginForm();
                    if (! $login_form->load(\Yii::$app->request->post())) {
                        $data['customError'] = "post banned";
                        $data['error'] = "Data required for login can not be blank";
                        return $this->sendResponse($data);
                    } else {
                        $flag = true;
                    }
                    
                    /* login code */
                    
                    if ($flag) {
                        $transaction->commit();
                        \Yii::$app->user->login($user, 3600 * 24 * 30);
                        
                        $data['auth_code'] = AuthSession::newSession($login_form)->auth_code;
                        $data['is_login'] = "0";
                        
                        if (! empty($invite)) {
                            $history->code_id = $invite->id;
                            $history->created_on = date('Y-m-d H:i:s');
                            $history->created_by_id = \Yii::$app->user->id;
                            $history->save(false, [
                                'code_id',
                                'created_on',
                                'created_by_id'
                            ]);
                        }
                        
                        $data['status'] = self::API_OK;
                        $data['detail'] = $user->asJson();
                        $data['message'] = \yii::t('app', 'Signup');
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    $data['customError'] = "exceptionentry";
                    $data['error'] = $e->getMessage();
                }
            } else {
                $user_model = User::findOne([
                    'id' => $auth->user_id
                ]);
                if ($user_model->state_id == User::STATE_BANNED) {
                    $data['customError'] = "banned";
                    $data['error'] = 'Your account is blocked, Please contact KingTasker Admin';
                    return $this->sendResponse($data);
                }
                
                if ($user_model->state_id == User::STATE_INACTIVE) {
                    $data['customError'] = "inactive";
                    $data['error'] = yii::t('app', 'Your account is not verified by admin');
                    $data['id'] = $user_model->id;
                    return $this->sendResponse($data);
                }
                /*
                 * if ($user_model->role_id != $params['User']['role_id']) {
                 * $data['customError'] = "inactive";
                 * $data['error'] = yii::t('app', 'This account is already exist, user other account');
                 * return $this->sendResponse($data);
                 * }
                 */
                
                $user = $auth->user;
                if (empty($user)) {
                    $data['customError'] = "not found";
                    $data['error'] = "User not found";
                    return $this->sendResponse($data);
                }
                
                \Yii::$app->user->login($user, 3600 * 24 * 30);
                $login_form = new LoginForm();
                
                if (! $login_form->load(\Yii::$app->request->post())) {
                    $data['customError'] = "post banned";
                    $data['error'] = "Data required for login can not be blank";
                    return $this->sendResponse($data);
                }
                $data['auth_code'] = AuthSession::newSession($login_form)->auth_code;
                $data['is_login'] = "1";
                $data['detail'] = \Yii::$app->user->identity->asJson();
                $data['success'] = yii::t('app', 'Login Successfully');
                $data['status'] = self::API_OK;
            }
        } else {
            $data['error'] = 'No data posted';
            $data['$_POST'] = $_POST;
        }
        return $this->sendResponse($data);
    }

    public function actionNotificationList()
    {
        $data = [];
        $query = Notification::my('to_user_id')->orderBy('created_on DESC');
        $data = Notification::sendApiDataInList($query);
        return $this->sendResponse($data);
    }

    public function actionViewNotification($id)
    {
        $data = [];
        $notification = Notification::my('to_user_id')->andWhere([
            'id' => $id
        ])->one();
        if (! empty($notification)) {
            $notification->is_read = Notification::IS_READ;
            if ($notification->updateAttributes([
                'is_read'
            ])) {
                $data['status'] = self::API_OK;
                $data['details'] = $notification->asJson();
            }
            return $this->sendResponse($data);
        }
        $data['error'] = \Yii::t('app', 'No Notification Found');
        return $this->sendResponse($data);
    }

    public function actionNotificationSetting()
    {
        $data = [];
        $user = \Yii::$app->user->identity;
        $user->push_enabled = ($user->push_enabled == User::PUSH_ENABLE) ? User::PUSH_DISABLE : User::PUSH_ENABLE;
        if ($user->updateAttributes([
            'push_enabled'
        ])) {
            $data['status'] = self::API_OK;
            $data['message'] = "Notification is " . $user->getNotificationSetting();
            $data['details'] = $user->push_enabled;
        }
        return $this->sendResponse($data);
    }

    public function actionDeleteNotification($id = null, $ClearAll = false)
    {
        $data = [];
        $notification = Notification::my('to_user_id');
        if (! empty($notification)) {
            if ($id != null) {
                $notification = $notification->andWhere([
                    'id' => $id
                ])->one();
                $notification->delete();
            }
            if ($ClearAll == true) {
                foreach ($notification->each() as $model) {
                    $model->delete();
                }
            }
            $data['status'] = self::API_OK;
            $data['message'] = "Notification is deleted successfully";
            return $this->sendResponse($data);
        }
        $data['error'] = \Yii::t('app', 'No Notification Found');
        return $this->sendResponse($data);
    }
}
