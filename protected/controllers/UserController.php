<?php

/**
 * @author     : Sanjay Jasuja< sanjayjasuja26@gmail.com >
 */

namespace app\controllers;

use app\components\SActiveForm;
use app\components\SController;
use app\models\LoginForm;
use app\models\User;
use app\models\search\User as UserSearch;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\components\filters\AccessRule;
use app\components\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends SController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className()
                ],
                'rules' => [
                    [
                        'actions' => [
                            'confirm-email',
                            'add',
                            'view',
                            'update',
                            'logout',
                            'changepassword',
                            'profile-image',
                            'toggle',
                            'dashboard'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isStudent() || User::isParent() || User::isCollege() || User::isUniversity();
                        }
                    ],
                    [
                        'actions' => [
                            'update-college-info',
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isCollege() || User::isUniversity();
                        }
                    ],
                    [
                        'actions' => [
                            'index',
                            'delete',
                            'add',
                            'view',
                            'update',
                            'delete',
                            'logout',
                            'changepassword',
                            'resetpassword',
                            'dashboard',
                            'profile-image',
                            'toggle',
                            'clear',
                            'recover',
                            'add-admin',
                            'mass'
                        ],
                        'allow' => true,
                        'matchCallback' => function () {
                            return User::isAdmin();
                        }
                    ],
                    [
                        'actions' => [
                            'login',
                            'signup',
                            'clear',
                            'resetpassword',
                            'recover',
                            'add-admin',
                            'confirm-email'
                        ],
                        'allow' => true,
                        'roles' => [
                            '?',
                            '*'
                        ]
                    ]
                ]
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => [
                        'post'
                    ]
                ]
            ]
        ];
    }

    /**
     * actionMass delete in mass as items are checked
     *
     * @param string $action
     * @return string
     */
    public function actionMass($action = 'delete') {
        \Yii::$app->response->format = 'json';
        $response['status'] = 'NOK';
        $status = User::massDelete();
        if ($status == true) {
            $response['status'] = 'OK';
        }
        return $response;
    }

    public function actionClear() {
        $runtime = Yii::getAlias('@runtime');
        $this->cleanRuntimeDir($runtime);
        $this->cleanAssetsDir();
        \Yii::$app->getSession()->setFlash('success', \Yii::t('app', "Cleared"));
        return $this->goBack();
    }

    public function actionIndex() {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->updateMenuItems();

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    public function actionView($id) {
        $model = $this->findModel($id);

        $this->updateMenuItems($model);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect($model->getUrl());
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                        'model' => $model
            ]);
        }
        return $this->render('view', [
                    'model' => $model
        ]);
    }

    public function actionAddAdmin() {
        $this->layout = User::LAYOUT_GUEST_MAIN;
        $count = User::find()->count();
        if ($count != 0) {
            return $this->redirect([
                        '/'
            ]);
        }
        $model = new User();
        $model->scenario = User::SCENARIO_ADD_ADMIN;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->role_id = User::ROLE_ADMIN;
            $model->state_id = User::STATE_ACTIVE;
            if ($model->validate()) {
                $model->setPassword($model->password);
                $model->generatePasswordResetToken();
                if ($model->save()) {
                    \Yii::$app->user->login($model);
                    \Yii::$app->getSession()->setFlash('success', \Yii::t('app', "Wellcome $model->full_name"));
                    return $this->goBack([
                                'dashboard/index'
                    ]);
                } else {
                    \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
                }
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        return $this->render('add-admin', [
                    'model' => $model
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd() {
        $this->layout = User::LAYOUT_MAIN;
        $post = Yii::$app->request->post();
        $model = new User();
        $model->role_id = User::ROLE_USER;
        $model->state_id = User::STATE_ACTIVE;
        $model->scenario = User::SCENARIO_SIGNUP;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }
        if ($model->load($post)) {
            $model->saveUploadedFile($model, 'profile_file');
            if ($model->validate()) {
                $model->scenario = User::SCENARIO_ADD;
                $model->generatePasswordResetToken();
                $model->sendRegistrationMailtoUser($model);
                $model->state_id = User::STATE_ACTIVE;
                $model->setPassword($model->password);
                if ($model->save()) {
                    \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'User Added Successfully.'));
                    return $this->redirect([
                                'view',
                                'id' => $model->id
                    ]);
                } else {
                    \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
                }
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        $this->updateMenuItems($model);
        return $this->render('add', [
                    'model' => $model
        ]);
    }

    public function actionRecover() {
        $this->layout = User::LAYOUT_GUEST_MAIN;
        $model = new User();
        $model->scenario = User::SCENARIO_TOKEN_REQUEST;
        if (isset($_POST['User'])) {
            $email = trim($_POST['User']['email']);
            if ($email != null) {

                $user = User::findOne([
                            'email' => $email
                ]);
                if ($user) {
                    $user->generatePasswordResetToken();
                    if (!$user->save(false, [
                                'activation_key'
                            ])) {
                        throw new \yii\base\Exception(\Yii::t('app', "Cant Generate Authentication Key"));
                    }
                    $user->sendEmail();
                    \Yii::$app->session->setFlash('success', \Yii::t('app', 'Please check your email to reset your password.'));
                    return $this->redirect([
                                '/user/login'
                    ]);
                } else {
                    \Yii::$app->session->setFlash('error', \Yii::t('app', 'Email is not registered.'));
                }
            } else {
                $model->addError('error', \Yii::t('app', 'Email cannot be blank'));
            }
        }
        $this->updateMenuItems($model);
        return $this->render('requestPasswordResetToken', [
                    'model' => $model
        ]);
    }

    public function actionResetpassword($token) {
        $this->layout = User::LAYOUT_GUEST_MAIN;
        $model = User::findByPasswordResetToken($token);
        if (!($model)) {
            \Yii::$app->session->setFlash('error', \Yii::t('app', 'This URL is expired.'));
        }
        $newModel = new User([
            'scenario' => User::SCENARIO_RESETPASSWORD
        ]);
        if ($newModel->load(Yii::$app->request->post()) && $newModel->validate() && $model) {

            $model->setPassword($newModel->password);
            $model->removePasswordResetToken();
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', \Yii::t('app', 'New password is saved successfully.'));
            } else {
                \Yii::$app->session->setFlash('error', \Yii::t('app', 'Error while saving new password.'));
            }
        }
        $this->updateMenuItems($model);
        return $this->render('resetpassword', [
                    'model' => $newModel
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $this->layout = User::LAYOUT_MAIN;
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;
        $post = \yii::$app->request->post();
        $old_image = $model->profile_file;
        $password = $model->password;

        if (Yii::$app->request->isAjax && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }

        if ($model->load($post)) {
            if (!empty($post['User']['password'])) {
                $model->setPassword($post['User']['password']);
            } else {
                $model->password = $password;
            }
            $model->profile_file = $old_image;
            $model->saveUploadedFile($model, 'profile_file', $old_image);
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', \Yii::t('app', 'User Updated successfully.'));
                return $this->redirect($model->getUrl());
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        $model->password = '';
        $this->updateMenuItems($model);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                        'model' => $model
            ]);
        }
        return $this->render('update', [
                    'model' => $model
        ]);
    }

    public function actionUpdateCollegeInfo($id) {
        $this->layout = User::LAYOUT_MAIN;
        $user_model = $this->findModel($id);
        $model = \app\models\Collegeinfo::find()->where(['user_id' => $id])->one();
        $model->scenario = \app\models\Collegeinfo::SCENARIO_UPDATE;
        $post = \yii::$app->request->post();


        if (Yii::$app->request->isAjax && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }

        if ($model->load($post)) {
            $model->created_by_id = Yii::$app->user->id;
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', \Yii::t('app', 'User Updated successfully.'));
                return $this->redirect($model->getUrl());
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        $this->updateMenuItems($model);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update-college-info', [
                        'model' => $model,
                        'user_model' => $user_model
            ]);
        }
        return $this->render('update-college-info', [
                    'model' => $model,
                    'user_model' => $user_model
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $this->updateMenuItems($model);

        if (\Yii::$app->user->id == $model->id || $model->role_id == User::ROLE_ADMIN) {
            \Yii::$app->session->setFlash('warrning', 'You are not allowed to perform this operation.');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        $model->delete();
        if (\Yii::$app->request->isAjax) {
            return true;
        }
        \Yii::$app->session->setFlash('success', \Yii::t('app', 'User Deleted successfully.'));
        return $this->redirect([
                    'index'
        ]);
    }

    public function actionConfirmEmail($id) {
        $user = User::find()->where([
                    'activation_key' => $id
                ])->one();
        if (!empty($user)) {
            $user->email_verified = User::EMAIL_VERIFIED;
            if ($user->save()) {
                if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
                    \Yii::$app->getSession()->setFlash('success', 'Congratulations! your account is verified');
                    return $this->redirect([
                                '/dashboard'
                    ]);
                }
            }
        } else {
            \Yii::$app->getSession()->setFlash('error', 'Token is Expired Please Resend Again');
            return $this->redirect([
                        'dashboard'
            ]);
        }
    }

    public function actionSignup() {
        $this->layout = User::LAYOUT_GUEST_MAIN;
        $model = new User([
            'scenario' => User::SCENARIO_SIGNUP
        ]);


        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return SActiveForm::validate($model);
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($model->load(Yii::$app->request->post())) {
                $model->state_id = User::STATE_ACTIVE;
                $model->email_verified = User::EMAIL_NOT_VERIFIED;
                if ($model->validate()) {
                    $model->scenario = User::SCENARIO_ADD;
                    $model->setPassword($model->password);
                    $model->generatePasswordResetToken();
                    if ($model->save()) {
                        if ($model->role_id == User::ROLE_PARENT) {
                            $parent_info_model = new \app\models\Parentinfo([
                                'scenario' => \app\models\Parentinfo::SCENARIO_ADD
                            ]);
                            if (!$parent_info_model->save()) {
                                \Yii::$app->getSession()->setFlash('error', "Error:" . $parent_info_model->getErrorsString());
                            }
                        } else if ($model->role_id == User::ROLE_STUDENT) {
                            $student_info_model = new \app\models\Studentinfo([
                                'scenario' => \app\models\Parentinfo::SCENARIO_ADD
                            ]);
                            $student_info_model->user_id = $model->id;
                            $college_model->state_id = \app\models\Parentinfo::STATE_ACTIVE;
                            $college_model->state_id = \app\models\Parentinfo::STATE_ACTIVE;
                            if (!$student_info_model->save()) {
                                \Yii::$app->getSession()->setFlash('error', "Error:" . $student_info_model->getErrorsString());
                            }
                        } else {
                            $college_model = new \app\models\Collegeinfo([
                                'scenario' => \app\models\Parentinfo::SCENARIO_ADD
                            ]);
                            $college_model->user_id = $model->id;

                            $college_model->state_id = \app\models\Parentinfo::STATE_ACTIVE;

                            if (!$college_model->save()) {
                                print_R($college_model->getErrorsString());
                                die;
                                \Yii::$app->getSession()->setFlash('error', "Error:" . $college_model->getErrorsString());
                            }
                        }
                        $model->sendVerificationMailtoUser();
                        \Yii::$app->getSession()->setFlash('success', "You have Signup Successfull.Please verfiy user email for account activation.");
                        $transaction->commit();
                        return $this->redirect([
                                    '/site/index'
                        ]);
                    } else {
                        \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
                    }
                } else {
                    \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
                }
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            \Yii::$app->getSession()->setFlash('error', "Error !!" . $e->getMessage());
        }
        return $this->render('signup', [
                    'model' => $model
        ]);
    }

    public function actionLogin() {
        $this->layout = User::LAYOUT_GUEST_MAIN;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $post = Yii::$app->request->post();

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if (User::IsEmailVerified($model->username)) {
                if ($model->login()) {
                    return $this->goBack([
                                'dashboard/index'
                    ]);
                } else {
                    \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
                }
            }
            \Yii::$app->getSession()->setFlash('error', "Error !! Please verfiy your email first.");
//            return $this->goBack([
//                        'user/login'
//            ]);
        }
        return $this->render('login', [
                    'model' => $model
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionChangepassword($id) {
        $this->layout = User::LAYOUT_MAIN;
        $model = $this->findModel($id);
        if (!($model->isAllowed()))
            throw new \yii\web\HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));

        $newModel = new User([
            'scenario' => User::SCENARIO_CHANGEPASSWORD
        ]);
        if (Yii::$app->request->isAjax && $newModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return SActiveForm::validate($newModel);
        }
        if ($newModel->load(Yii::$app->request->post()) && $newModel->validate()) {
            $model->setPassword($newModel->newPassword);
            if ($model->save(false, [
                        'password'
                    ])) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Password Changed successfully'));
                return $this->redirect([
                            'dashboard/index'
                ]);
            } else {
                \Yii::$app->getSession()->setFlash('error', "Error !!" . $model->getErrorsString());
            }
        }
        $this->updateMenuItems($model);
        return $this->render('changepassword', [
                    'model' => $newModel
        ]);
    }

    public function actionDashboard() {
        return $this->redirect([
                    'dashboard/index'
        ]);
    }

    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {

            if (!($model->isAllowed()))
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));

            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function updateMenuItems($model = null) {
        switch (\Yii::$app->controller->action->id) {

            case 'add': {
                    $this->menu['add'] = [
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ],
                        'visible' => User::isAdmin()
                    ];
                }
                break;
            case 'index': {
                    $this->menu['add'] = [
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'Add'),
                        'url' => [
                            'add'
                        ],
                        'visible' => User::isAdmin()
                    ];
                }

                break;
            case 'update': {
                    $this->menu['add'] = [
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'add'),
                        'url' => [
                            'add'
                        ],
                        'visible' => User::isAdmin()
                    ];
                }
                break;
            case 'view': {
                    if ($model != null && ($model->role_id != User::ROLE_ADMIN) && \Yii::$app->hasModule('shadow'))
                        $this->menu['shadow'] = [
                            'label' => '<span class="glyphicon glyphicon-refresh ">Shadow</span>',
                            'title' => Yii::t('app', 'Login as ' . $model),
                            'url' => [
                                '/shadow/session/login',
                                'id' => $model->id
                            ],
                            'visible' => User::isAdmin()
                        ];
                    $this->menu['add'] = [
                        'label' => '<span class="glyphicon glyphicon-plus"></span>',
                        'title' => Yii::t('app', 'Add'),
                        'url' => [
                            'add'
                        ],
                        'visible' => User::isAdmin()
                    ];
                    if ($model != null)
                        $this->menu['changepassword'] = [
                            'label' => '<span class="glyphicon glyphicon-paste"></span>',
                            'title' => Yii::t('app', 'changepassword'),
                            'url' => $model->getUrl('changepassword'),
                            'visible' => User::isAdmin()
                        ];
                    if ($model != null)
                        $this->menu['update'] = [
                            'label' => '<span class="glyphicon glyphicon-pencil"></span>',
                            'title' => Yii::t('app', 'Update'),
                            'url' => $model->getUrl('update'),
                            'visible' => User::isAdmin()
                        ];
                    $this->menu['manage'] = [
                        'label' => '<span class="glyphicon glyphicon-list"></span>',
                        'title' => Yii::t('app', 'Manage'),
                        'url' => [
                            'index'
                        ],
                        'visible' => User::isAdmin()
                    ];
                    if ($model != null)
                        $this->menu['delete'] = [
                            'label' => '<span class="glyphicon glyphicon-trash"></span>',
                            'title' => Yii::t('app', 'Delete'),
                            'url' => $model->getUrl('delete'),
                            'htmlOptions' => [
                                'data-method' => 'post'
                            ],
                            'visible' => User::isAdmin()
                        ];
                }
                break;
        }
    }

}
