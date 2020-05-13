<?php
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
namespace app\modules\api2\controllers;

use app\models\Log;
use app\models\LoginForm;
use app\models\User;
use app\modules\api2\components\ApiTxController;
use Yii;
use app\modules\api2\models\DeviceDetail;
use app\models\Purpose;
use app\models\Page;
use app\models\Feedback;
use app\models\ContactForm;
use app\models\EmailQueue;
use app\models\HomeValue;
use app\models\HomeType;
use app\models\ProfessionType;
use app\models\Professional;
use app\models\LockedUser;
use app\models\ComplaintResolve;
use app\modules\api2\components\TPagination;
use app\models\InitiateForm;
use app\models\SearchRecord;
use app\models\SubscriptionPlan;
use app\models\FeaturedImage;
use app\models\HomeLoans;
use app\models\HomeInspection;
use app\models\TitleAgent;
use app\models\Chatmessage;
use app\models\Chatresponse;
use app\modules\notification\models\Notification;
use app\models\Chatmedia;
use yii\web\UploadedFile;
use app\models\Budget;
use app\models\CreditScore;
use app\models\DownPayment;
use app\models\PropertyType;
use app\models\Representation;
use app\models\TimePeriod;
use app\models\AreaCode;
use yii\db\Expression;
use app\models\BlogPost;
use net\authorize\api\contract\v1\SubscriptionPaymentType;
use app\models\AvailableTime;
use app\models\City;

/**
 * UserController implements the API actions for User model.
 */
class UserController extends ApiTxController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'login',
            'signup',
            'recover',
            'job-type',
            'page',
            'search-professional',
            'search-agent',
            'add-feedback',
            'contact-us',
            'add-home-value',
            'home-type',
            'profession-type',
            'featured-image'
        
        ];
        $behaviors['authenticator']['optional'] = [
            'get-featured-image',
            'professional-profile',
            'budget-list',
            'credit-list',
            'downpayment-list',
            'property-type-list',
            'representation-list',
            'time-period-list',
            'blog-list',
            'fix-appointment',
            'get-time-slots',
            'state-list'
        
        ];
        
        return $behaviors;
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['get'] = [
            'GET'
        ];
        return $verbs;
    }

    /**
     * Displays a single User model.
     *
     * @return mixed
     */
    public function actionGetProfile()
    {
        $model = User::find()->where([
            'id' => \Yii::$app->user->id
        ])->one();
        if (! empty($model)) {
            $data['status'] = self::API_OK;
            $data['details'] = $model->asJson();
        } else {
            $data['error'] = \Yii::t('app', 'No User Found');
        }
        $this->response = $data;
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        $this->modelClass = "app\models\User";
        return $this->txSave();
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $data = [];
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            
            if ($model->save()) {
                $data['status'] = self::API_OK;
                $data['detail'] = $model;
            } else {
                $data['error'] = $model->flattenErrors;
            }
        } else {
            $data['error_post'] = 'No Data Posted';
        }
        $this->response = $data;
    }

    public function actionUpdateProfile()
    {
        $data = [];
        $model = \Yii::$app->user->identity;
        $professional = Professional::find()->where([
            'created_by_id' => $model->id
        ])->one();
        $featured_image = FeaturedImage::find()->where([
            'created_by_id' => \Yii::$app->user->id
        ])->all();
        $area_codes = AreaCode::find()->where([
            'created_by_id' => \Yii::$app->user->id
        ])->all();
        
        if (! empty($model) && ! empty($professional)) {
            $professional->scenario = 'update-profile';
            $model->scenario = 'update-profile';
            
            $post = Yii::$app->request->post();
            
            $old_image = $model->profile_file;
            $db = \Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                if ($model->load($post) && $professional->load($post)) {
                    
                    $model->profile_file = $old_image;
                    $model->saveUploadedFile($model, 'profile_file', $old_image);
                    if ($model->save()) {
                        if ($professional->save()) {
                            if (! empty($area_codes)) {
                                foreach ($area_codes as $area) {
                                    $area->delete();
                                }
                            }
                            $zip = explode(',', $post['AreaCode']['zip_code']);
                            foreach ($zip as $key => $val) {
                                $area_code = new AreaCode();
                                if (! empty($val)) {
                                    $area_code->zip_code = $val;
                                    $area_code->created_by_id = \Yii::$app->user->id;
                                    $area_code->save();
                                }
                            }
                            $data['status'] = self::API_OK;
                            $data['detail'] = $model->asJson();
                            $data['msg'] = yii::t('app', 'Professional Updated Successfully');
                            $transaction->commit();
                        } else {
                            $data['error'] = $professional->getErrors();
                        }
                    } else {
                        $data['error'] = $model->getErrors();
                    }
                } else {
                    $data['error'] = \Yii::t('app', 'No Data Posted');
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->getSession()->setFlash('error', Yii::t('app', "Error !! ") . $e->getMessage());
            }
        } else {
            $data['error'] = \Yii::t('app', 'No User Found');
        }
        $this->response = $data;
    }

    public function actionGetTimeSlots($date, $id)
    {
        $out = [];
        $day = date('D', strtotime($date));
        switch ($day) {
            case 'Sun':
                $day_id = 0;
                break;
            case 'Mon':
                $day_id = 1;
                break;
            case 'Tue':
                $day_id = 2;
                break;
            case 'Wed':
                $day_id = 3;
                break;
            case 'Thu':
                $day_id = 4;
                break;
            case 'Fri':
                $day_id = 5;
                break;
            case 'Sat':
                $day_id = 6;
                break;
            default:
                $data['msg'] = 'Enter Valid Date';
        }
        if (! empty($day_id)) {
            $models = AvailableTime::find()->where([
                'day_id' => $day_id,
                'created_by_id' => $id
            ])->all();
            
            if (! empty($models)) {
                foreach ($models as $i => $time) {
                    
                    $starttime = $time['start_time'];
                    $endtime = $time['end_time'];
                    $duration = '30'; // split by 30 mins
                    
                    $array_of_time = array();
                    $start_time = strtotime($starttime); // change to strtotime
                    $end_time = strtotime($endtime); // change to strtotime
                    
                    $add_mins = $duration * 60;
                    
                    while ($start_time <= $end_time) {
                        $out[] = [
                            'id' => date("h:i A", $start_time),
                            'name' => date("h:i A", $start_time)
                        ];
                        $array_of_time[] = date("h:i", $start_time);
                        $start_time += $add_mins; // to check endtie=me
                    }
                    
                    if ($i == 0) {
                        $selected = $time['start_time'];
                    }
                }
                $data['status'] = self::API_OK;
            }
        }
        $data['time_slots'] = ! empty($out) ? $out : (object) [];
        $this->response = $data;
    }

    public function actionFixAppointment($to_user_id)
    {
        $model = new ContactForm();
        
        if ($model->load(Yii::$app->request->getBodyParams())) {
            $agent = User::find()->where([
                'id' => $to_user_id
            ])->one();
            $sub = 'New Appointment: ' . $model->subject;
            $from = \Yii::$app->params['no-replyEmail'];
            $message = \yii::$app->view->renderFile('@app/mail/sendApointmentMail.php', [
                'user' => $model,
                'agent' => $agent
            ]);
            if (! empty($to_user_id)) {
                
                EmailQueue::add([
                    'to' => $agent->email,
                    'from' => $from,
                    'subject' => $sub,
                    'html' => $message
                
                ], true);
            }
            $data['status'] = self::API_OK;
            $data['msg'] = \Yii::t('app', 'We have received your appointment request. Our representative will contact you soon.');
        } else {
            $data['error'] = \Yii::t('app', 'Something went wrong');
        }
        $this->response = $data;
    }

    public function actionUpdateSocial()
    {
        $data = [];
        
        $model = Professional::find()->where([
            
            'created_by_id' => \Yii::$app->user->id
        ])->one();
        $model->scenario = 'update-social';
        $post = Yii::$app->request->post();
        
        if ($model->load($post)) {
            
            if ($model->save()) {
                $data['status'] = self::API_OK;
                $data['detail'] = $model->asJson();
                $data['msg'] = yii::t('app', 'Social Link Updated Successfully');
            } else {
                $data['error'] = $model->getErrors();
            }
        } else {
            $data['error_post'] = 'No Data Posted';
        }
        
        $this->response = $data;
    }

    public function actionSubscriptionPlans($page = null)
    {
        $data = [];
        
        $query = SubscriptionPlan::find();
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '10',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        
        $data = $pagination->serialize($dataProvider);
        $data['status'] = self::API_OK;
        $this->response = $data;
    }

    public function actionSignup()
    {
        $data = [];
        $model = new User();
        $newModel = new Professional();
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->role_id == User::ROLE_PROFESSIONAL) {
                    if (! $newModel->load(Yii::$app->request->post())) {
                        $data['error'] = "Data not posted.";
                        return $this->response = $data;
                    }
                }
                $email_identify = User::findByUsername($model->email);
                if (empty($email_identify)) {
                    $password = $model->password;
                    $model->setPassword($model->password);
                    $model->state_id = User::STATE_INACTIVE;
                    $model->email_verified = User::EMAIL_NOT_VERIFIED;
                    if (! empty($_FILES)) {
                        $model->saveUploadedFile($model, 'profile_file');
                    }
                    if ($model->save()) {
                        
                        if ($model->role_id == User::ROLE_PROFESSIONAL) {
                            $newModel->created_by_id = $model->id;
                            if (! $newModel->save()) {
                                $data['error'] = $newModel->getErrorsString();
                            } else {
                                $data['status'] = self::API_OK;
                                $data['user_detail'] = $model->asJson();
                                $transaction->commit();
                            }
                        } else {
                            $data['status'] = self::API_OK;
                            $data['user_detail'] = $model->asJson();
                            $transaction->commit();
                        }
                        $model->sendRegistrationMailtoAdmin();
                        $model->sendRegistrationMailtoUser($model, $password);
                    } else {
                        
                        $data['error'] = $model->getErrorsString();
                    }
                } else {
                    $data['error'] = \yii::t('app', "Email already exists.");
                }
            } else {
                $data['error'] = "Data not posted.";
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $data['error'] = "Error !! " . $e->getMessage();
        }
        $this->response = $data;
    }

    public function actionCheck()
    {
        $data = [];
        $deviceToken = DeviceDetail::find()->where([
            'created_by_id' => \Yii::$app->user->id
        ])->one();
        if (! empty($deviceToken)) {
            if ($deviceToken->load(Yii::$app->request->post())) {
                if ($deviceToken->save()) {
                    $data['status'] = self::API_OK;
                } else {
                    $data['error'] = $deviceToken->getErrorString;
                }
            } else {
                $data['error'] = \yii::t('app', "No data posted");
            }
        } else {
            $data['error'] = \yii::t('app', "No device token found");
        }
        
        $this->response = $data;
    }

    public function actionProfessionalProfile($id = null)
    {
        $data = [];
        if (! empty($id)) {
            $model = User::find()->where([
                'id' => $id
            ])->one();
        } else {
            $model = User::find()->where([
                'id' => \Yii::$app->user->id
            ])->one();
        }
        if (! empty($model)) {
            $data['status'] = self::API_OK;
            $data['details'] = $model->asJson();
        } else {
            $data['error'] = \yii::t('app', "No Professional found");
        }
        
        $this->response = $data;
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
                    
                    $user->generateAccessToken();
                    $user->save(false, [
                        'access_token'
                    ]);
                    
                    $data['status'] = self::API_OK;
                    $data['access-token'] = $user->access_token;
                    (new DeviceDetail())->appData($model);
                    $data['detail'] = $model->asJson();
                    $data['user_detail'] = $user->asJson();
                } else {
                    $data['error'] = $model->getErrorsString();
                }
            } else {
                $data['error'] = ' Incorrect Email';
            }
        } else {
            $data['error'] = "No data posted.";
        }
        $this->response = $data;
    }

    public function actionLogout()
    {
        $data = [];
        $user = \Yii::$app->user->identity;
        if (\Yii::$app->user->logout()) {
            $user->access_token = '';
            $user->save(false, [
                'access_token'
            ]);
            (new DeviceDetail())->deleteOldAppData($user->id);
            $data['status'] = self::API_OK;
        }
        
        $this->response = $data;
    }

    public function actionSearchProfessional($page = null)
    {
        $data = [];
        $post = \Yii::$app->request->getBodyParams();
        $type = ! empty($post['User']['type']) ? $post['User']['type'] : '';
        $name = ! empty($post['User']['name']) ? $post['User']['name'] : '';
        
        if (! empty($post)) {
            if (! empty($type)) {
                $subquery = Professional::find()->select('created_by_id')
                    ->where([
                    'purpose_id' => $type
                ])
                    ->andWhere([
                    'OR',
                    [
                        'plan_id' => SubscriptionPlan::PLAN_ELITE,
                        'plan_id' => SubscriptionPlan::PLAN_PREMIUM
                    ]
                ])
                    ->column();
                
                $query = User::find()->where([
                    'IN',
                    'id',
                    $subquery
                ]);
            } elseif (! empty($name)) {
                $subquery = Professional::find()->select('created_by_id')
                    ->where([
                    'IN',
                    'plan_id',
                    [
                        SubscriptionPlan::PLAN_ELITE,
                        SubscriptionPlan::PLAN_PREMIUM
                    
                    ]
                
                ])
                    ->column();
                
                $query = User::find()->where([
                    'like',
                    'full_name',
                    $name
                ])->andWhere([
                    'IN',
                    'id',
                    $subquery
                ]);
            }
            $query = $query->andWhere([
                'role_id' => User::ROLE_PROFESSIONAL,
                'is_locked' => User::IS_NOT_LOCKED
            ]);
            
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => '10',
                    'page' => $page
                ]
            ]);
            
            $pagination = new TPagination();
            $pagination->params = [
                true
            ];
            $data = $pagination->serialize($dataProvider);
            $data['status'] = self::API_OK;
        } else {
            $data['error'] = \yii::t('app', 'No Data Posted');
        }
        
        $this->response = $data;
    }

    public function actionUpdateTimeSlots()
    {
        $data = [];
        $days = AvailableTime::find()->where([
            'created_by_id' => \Yii::$app->user->id
        ])->all();
        
        $post = \Yii::$app->request->getBodyParams();
        if (! empty($days)) {
            foreach ($days as $day) {
                $day->delete();
            }
        }
        
        if (! empty($post['days'])) {
            $days = json_decode($post['days'], true);
            
            foreach ($days as $day) {
                foreach ($day as $dayVal => $timeSlot) {
                    if (! empty($timeSlot)) {
                        foreach ($timeSlot as $time) {
                            $model = new AvailableTime();
                            $model->day_id = $dayVal;
                            $model->start_time = $time['start_time'];
                            $model->end_time = $time['end_time'];
                            $model->note = $time['notes'];
                            $model->created_by_id = \Yii::$app->user->id;
                            $model->state_id = AvailableTime::STATE_ACTIVE;
                            $model->save();
                        }
                    }
                }
            }
            $data['status'] = self::API_OK;
            $data['msg'] = 'Day Slots added successfully';
        } else {
            $data['error'] = 'No Data Posted';
        }
        $this->response = $data;
    }

    public function actionSearchAgent($page = null)
    {
        $data = [];
        $post = \Yii::$app->request->post();
        
        switch ($post['User']['pro-type']) {
            case Professional::TYPE_REAL_ESTATE_AGENT:
                $model = new SearchRecord();
                
                break;
            case Professional::TYPE_LENDER:
                $model = new HomeLoans();
                $model->state_id == User::STATE_ACTIVE;
                break;
            case Professional::TYPE_HOME_INSPECTOR:
                $model = new HomeInspection();
                $model->state_id == User::STATE_ACTIVE;
                break;
            case Professional::TYPE_TITLE_AGENT:
                $model = new \app\models\TitleAgent();
                $model->state_id == User::STATE_ACTIVE;
                break;
            default:
                
                $data['error'] = \yii::t('app', 'Firstly select any profession type');
                return $this->response = $data;
        }
        
        if ($model->load($post)) {
            if ($model->save()) {
                
                if (! empty($post['User']['type'])) {
                    $expression = new Expression('  CONCAT(",", `licence`, ",") REGEXP ",(' . $post['User']['city'] . ')," ');
                    $subquery = Professional::find()->select('created_by_id')
                        ->where([
                        'profession_type_id' => $post['User']['pro-type']
                    
                    ])
                        ->andWhere([
                        'OR',
                        [
                            'plan_id' => SubscriptionPlan::PLAN_ELITE
                        ],
                        [
                            
                            'plan_id' => SubscriptionPlan::PLAN_PREMIUM
                        ]
                    
                    ])
                        ->andWhere($expression);
                    
                    switch ($post['User']['pro-type']) {
                        case Professional::TYPE_REAL_ESTATE_AGENT:
                            
                            $expression1 = new Expression('  CONCAT(",", `property_type_id`, ",") REGEXP ",(' . $model->property_type_id . ')," ');
                            $expression2 = new Expression('  CONCAT(",", `budget_id`, ",") REGEXP ",(' . $model->budget_id . ')," ');
                            $expression3 = new Expression('  CONCAT(",", `time_period_id`, ",") REGEXP ",(' . $model->time_period_id . ')," ');
                            $subquery = $subquery->andWhere($expression1)
                                ->andWhere($expression2)
                                ->andWhere($expression3)
                                ->column();
                            
                            break;
                        case Professional::TYPE_LENDER:
                            $expression1 = new Expression('  CONCAT(",", `property_type_id`, ",") REGEXP ",(' . $model->property_type_id . ')," ');
                            $expression2 = new Expression('  CONCAT(",", `budget_id`, ",") REGEXP ",(' . $model->budget_id . ')," ');
                            $expression3 = new Expression('  CONCAT(",", `down_payment_id`, ",") REGEXP ",(' . $model->down_payment_id . ')," ');
                            $expression4 = new Expression('  CONCAT(",", `credit_score_id`, ",") REGEXP ",(' . $model->credit_score_id . ')," ');
                            $subquery = $subquery->andWhere($expression1)
                                ->andWhere($expression2)
                                ->andWhere($expression3)
                                ->andWhere($expression4)
                                ->column();
                            
                            break;
                        case Professional::TYPE_HOME_INSPECTOR:
                            $expression1 = new Expression('  CONCAT(",", `property_type_id`, ",") REGEXP ",(' . $model->property_type_id . ')," ');
                            $expression2 = new Expression('  CONCAT(",", `budget_id`, ",") REGEXP ",(' . $model->budget_id . ')," ');
                            $expression3 = new Expression('  CONCAT(",", `time_period_id`, ",") REGEXP ",(' . $model->time_period_id . ')," ');
                            $subquery = $subquery->andWhere($expression1)
                                ->andWhere($expression2)
                                ->andWhere($expression3)
                                ->column();
                            
                            break;
                        case Professional::TYPE_TITLE_AGENT:
                            $expression1 = new Expression('  CONCAT(",", `property_type_id`, ",") REGEXP ",(' . $model->property_type_id . ')," ');
                            $expression2 = new Expression('  CONCAT(",", `representation_id`, ",") REGEXP ",(' . $model->representation_id . ')," ');
                            $expression3 = new Expression('  CONCAT(",", `time_period_id`, ",") REGEXP ",(' . $model->time_period_id . ')," ');
                            $subquery = $subquery->andWhere($expression1)
                                ->andWhere($expression2)
                                ->andWhere($expression3)
                                ->column();
                            break;
                        default:
                            $data['error'] = \Yii::t('app', 'Please Select Pro Type First');
                            return $this->response = $data;
                    }
                }
                if (! empty($subquery)) {
                    $sub = [];
                    foreach ($subquery as $val) {
                        $search_pro = Professional::find()->where([
                            'created_by_id' => $val
                        ])->one();
                        
                        if (! empty($search_pro->purpose_id)) {
                            if ($search_pro->purpose_id != Professional::PRO_TYPE_BOTH) {
                                if ($search_pro->purpose_id == $post['User']['type']) {
                                    $sub[] = $val;
                                }
                            } else {
                                $sub[] = $val;
                            }
                        }
                    }
                }
                
                $query = User::find()->where([
                    'role_id' => User::ROLE_PROFESSIONAL,
                    'is_locked' => User::IS_NOT_LOCKED
                
                ]);
                
                if (! empty($post['User']['type'])) {
                    if (! empty($sub)) {
                        $query = $query->andWhere([
                            'in',
                            'id',
                            $sub
                        ]);
                    } else {
                        $query = $query->andWhere([
                            'in',
                            'id',
                            $subquery
                        ]);
                    }
                }
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => '10',
                        'page' => $page
                    ]
                ]);
                
                $pagination = new TPagination();
                
                $data = $pagination->serialize($dataProvider);
                $data['status'] = self::API_OK;
            } else {
                
                $data['error'] = $model->getErrorsString();
            }
        } else {
            $data['error'] = \yii::t('app', 'No Data Posted');
        }
        
        $this->response = $data;
    }

    public function actionJobType()
    {
        $data = [];
        $model = Purpose::find()->all();
        if (! empty($model)) {
            foreach ($model as $type) {
                $list[] = $type->asJson();
            }
            $data['status'] = self::API_OK;
            $data['detail'] = $list;
        } else {
            
            $data['error'] = yii::t('app', 'No Type Found');
        }
        $this->response = $data;
    }

    public function actionHomeType()
    {
        $data = [];
        $model = HomeType::find()->select([
            'id',
            'title',
            'font_icon'
        ])->all();
        if (! empty($model)) {
            foreach ($model as $type) {
                $list[] = $type->asJson();
            }
            $data['status'] = self::API_OK;
            $data['detail'] = $list;
        } else {
            
            $data['error'] = yii::t('app', 'No Type Found');
        }
        $this->response = $data;
    }

    public function actionProfessionType()
    {
        $data = [];
        $model = ProfessionType::find()->select([
            'id',
            'title',
            'image_file'
        ])->all();
        if (! empty($model)) {
            foreach ($model as $type) {
                $list[] = $type->asJson();
            }
            $data['status'] = self::API_OK;
            $data['detail'] = $list;
        } else {
            
            $data['error'] = yii::t('app', 'No Type Found');
        }
        $this->response = $data;
    }

    public function actionAddFeedback()
    {
        $data = [];
        $model = new Feedback();
        $post = \Yii::$app->request->post();
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($model->load($post)) {
                $model->state_id = User::STATE_ACTIVE;
                if ($model->save()) {
                    if ($model->type_id == Feedback::TYPE_COMPLAINT) {
                        if (! empty($model->agent_id)) {
                            $user = User::find()->where([
                                'id' => $model->agent_id,
                                'is_locked' => User::IS_NOT_LOCKED
                            ])->one();
                            if (! empty($user)) {
                                $user->is_locked = User::IS_LOCKED;
                                if ($user->updateAttributes([
                                    'is_locked'
                                ])) {
                                    $locked = new LockedUser();
                                    $locked->professional_id = $user->id;
                                    $locked->complaint_date = date('Y-m-d');
                                    if ($locked->save()) {
                                        
                                        $data['status'] = self::API_OK;
                                        $data['msg'] = \Yii::t('app', 'Feedback added successfully. Your complaint will be resolved within 30 days');
                                        $data['detail'] = $model->asJson();
                                        $transaction->commit();
                                    } else {
                                        $data['msg'] = $locked->getErrorsString();
                                    }
                                } else {
                                    $data['error'] = $user->getErrorsString();
                                }
                            } else {
                                $data['error'] = \Yii::t('app', 'No Agent Found');
                            }
                        } else {
                            $data['status'] = self::API_OK;
                            $data['msg'] = \Yii::t('app', 'Feedback added successfully.');
                            $data['detail'] = $model->asJson();
                            $transaction->commit();
                        }
                    } else {
                        $data['status'] = self::API_OK;
                        $data['msg'] = \Yii::t('app', 'Feedback added successfully.');
                        $data['detail'] = $model->asJson();
                        $transaction->commit();
                    }
                } else {
                    $data['error'] = $model->getErrorsString();
                }
            } else {
                
                $data['error'] = yii::t('app', 'No Data Posted');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->getSession()->setFlash('error', Yii::t('app', "Error !! ") . $e->getMessage());
        }
        
        $this->response = $data;
    }

    public function actionAddHomeValue()
    {
        $data = [];
        $model = new HomeValue();
        $post = \Yii::$app->request->post();
        $user = new User();
        if ($model->load($post)) {
            $model->state_id = User::STATE_ACTIVE;
            if ($model->save()) {
                
                try {
                    $user->sendHomeValueInfoToAgent($model);
                } catch (\Exception $e) {
                    
                    echo $e->getMessage();
                }
                
                // try {
                // $user->sendHomeValueInfoToAgent($model);
                // }catch{
                
                // }
                $data['status'] = self::API_OK;
                $data['msg'] = \Yii::t('app', 'HomeValue added successfully');
                $data['detail'] = $model->asJson();
            } else {
                $data['error'] = $model->getErrorsString();
            }
        } else {
            
            $data['error'] = yii::t('app', 'No Data Posted');
        }
        $this->response = $data;
    }

    public function actionComplaintResolve()
    {
        $data = [];
        $model = new ComplaintResolve();
        $post = \Yii::$app->request->post();
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($model->load($post)) {
                $complaint = Feedback::find()->where([
                    'agent_id' => \Yii::$app->user->id,
                    'type_id' => Feedback::TYPE_COMPLAINT
                ])->one();
                if (! empty($complaint)) {
                    $model->complaint_id = $complaint->id;
                    if ($model->save()) {
                        $user = User::find()->where([
                            'id' => $complaint->agent_id,
                            'is_locked' => User::IS_LOCKED
                        ])->one();
                        if (! empty($user)) {
                            $user->is_locked = User::IS_NOT_LOCKED;
                            if ($user->updateAttributes([
                                'is_locked'
                            ])) {
                                $locked = LockedUser::find()->where([
                                    'professional_id' => $user->id,
                                    'state_id' => LockedUser::STATE_ACTIVE
                                
                                ])->one();
                                if (! empty($locked)) {
                                    $locked->state_id = LockedUser::STATE_INACTIVE;
                                    if ($locked->updateAttributes([
                                        'state_id'
                                    ])) {
                                        $user->sendResolveMailToClient($model, $complaint);
                                        $user->sendResolveMailToAdmin($model, $complaint);
                                        $data['status'] = self::API_OK;
                                        $data['msg'] = \Yii::t('app', 'Client Resolve send successfully');
                                        $data['detail'] = $model->asJson();
                                        $transaction->commit();
                                    }
                                } else {
                                    $data['error'] = \Yii::t('app', 'No Locked Agent Found');
                                }
                            } else {
                                $data['error'] = $user->getErrors();
                            }
                        } else {
                            $data['error'] = \Yii::t('app', "You don't have any complaint pending");
                        }
                    } else {
                        $data['error'] = $model->getErrorsString();
                    }
                } else {
                    $data['error'] = \Yii::t('app', 'No Complaint Found');
                }
            } else {
                
                $data['error'] = yii::t('app', 'No Data Posted');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->getSession()->setFlash('error', Yii::t('app', "Error !! ") . $e->getMessage());
        }
        
        $this->response = $data;
    }

    public function actionUserRequest($page = null)
    {
        $data = [];
        $query = HomeValue::find()->where([
            'professional_id' => \Yii::$app->user->id
        ]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '10',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        
        $data = $pagination->serialize($dataProvider);
        $data['status'] = self::API_OK;
        
        $this->response = $data;
    }

    public function actionFeedbackList($page = null)
    {
        $data = [];
        $query = Feedback::find()->where([
            'agent_id' => \Yii::$app->user->id
        ]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '10',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        
        $data = $pagination->serialize($dataProvider);
        $data['status'] = self::API_OK;
        
        $this->response = $data;
    }

    public function actionContactUs()
    {
        $data = [];
        $model = new ContactForm();
        
        if ($model->load(Yii::$app->request->post())) {
            $sub = 'New Contact: ' . $model->subject;
            $from = $model->email;
            $message = \yii::$app->view->renderFile('@app/mail/contact.php', [
                'user' => $model
            ]);
            EmailQueue::sendEmailToAdmins([
                'from' => $from,
                'subject' => $sub,
                'html' => $message
            ], true);
            
            $data['msg'] = \Yii::t('app', 'Warm Greetings!! Thank you for contacting us. We have received your request. Our representative will contact you soon.');
            $data['status'] = self::API_OK;
        } else {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'No Data Posted'));
        }
        
        $this->response = $data;
    }

    public function actionInitiate($id, $type)
    {
        $data = [];
        $model = new InitiateForm();
        
        if ($model->load(Yii::$app->request->post())) {
            
            if ($type == InitiateForm::FEEDBACK) {
                $url = Yii::$app->urlManager->createAbsoluteUrl([
                    'site/feedback'
                ]);
                
                $subject = \Yii::t('app', 'Feedback Mail');
                
                $message = \yii::$app->view->renderFile('@app/mail/sendInitiateFeedbackMail.php', [
                    'info' => $model,
                    'url' => $url
                ]);
            } else {
                $url = Yii::$app->urlManager->createAbsoluteUrl([
                    'agent/profile',
                    'id' => $id
                ]);
                
                $subject = \Yii::t('app', 'Request Appointment Mail');
                
                $message = \yii::$app->view->renderFile('@app/mail/sendRequestApointmentMail.php', [
                    'info' => $model,
                    'url' => $url
                ]);
            }
            
            $user = User::find()->select('email')
                ->where([
                'id' => $id
            ])
                ->one();
            
            if ($model->sendMail($user->email, $message, $subject)) {
                $data['msg'] = \Yii::t('app', 'Mail has been send successfully');
                $data['status'] = self::API_OK;
            } else {
                $data['error'] = \Yii::t('app', 'Something went wrong');
            }
        } else {
            $data['error'] = \Yii::t('app', 'No Data Posted');
        }
        
        $this->response = $data;
    }

    public function actionChangePassword()
    {
        $data = [];
        $data['post'] = $_POST;
        $model = User::findOne([
            'id' => \Yii::$app->user->identity->id
        ]);
        
        $newModel = new User([
            'scenario' => 'api-changepassword'
        ]);
        if ($newModel->load(Yii::$app->request->post()) && $newModel->validate()) {
            
            $model->setPassword($newModel->newPassword);
            if ($model->save()) {
                $data['status'] = self::API_OK;
                $data['msg'] = \Yii::t('app', 'Password Changed Successfully');
            } else {
                $data['error'] = \Yii::t('app', 'Incorrect Password');
            }
        } else {
            $data['error'] = \Yii::t('app', 'No data Posted');
        }
        $this->response = $data;
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
        $this->response = $data;
    }

    public function actionPage($type)
    {
        $data = [];
        $model = Page::find()->where([
            'type_id' => $type
        ])->all();
        if (! empty($model)) {
            foreach ($model as $type) {
                $list[] = $type->asJson();
            }
            $data['status'] = self::API_OK;
            $data['detail'] = $list;
        } else {
            $data['error'] = yii::t('app', 'No Page Found');
        }
        
        $this->response = $data;
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionDeleteProfile()
    {
        $data = [];
        $model = Yii::$app->user->identity;
        if (! empty($model)) {
            $model->profile_file = null;
            if ($model->save(false, [
                'profile_file'
            ])) {
                if (is_file(UPLOAD_PATH . $model->profile_file))
                    unlink(UPLOAD_PATH . $model->profile_file);
                $data['status'] = self::API_OK;
                $data['detail'] = $model->asJson();
            } else {
                $data['error'] = $model->getErrorsString;
            }
        } else {
            $data['error'] = Yii::t('app', 'No data posted');
        }
        $this->response = $data;
    }

    public function actionRecover()
    {
        $data = [];
        $model = new User();
        $emailQueue = new EmailQueue();
        $post = \Yii::$app->request->bodyParams;
        if (isset($post['User']['email'])) {
            $email = trim($post['User']['email']);
            $user = User::findOne([
                'email' => $email
            ]);
            
            if ($user) {
                $user->generatePasswordResetToken();
                if (! $user->updateAttributes([
                    'activation_key'
                ])) {
                    throw new \Exception(Yii::t('app', "Cant Generate Authentication Key"));
                }
                $user->sendRecoverMailtoUser();
                
                $data['success'] = Yii::t('app', 'Please check your email to reset your password');
                $data['status'] = self::API_OK;
                $data['recover-email'] = $user->email;
            } else {
                $data['error'] = Yii::t('app', 'Email is not registered');
            }
        } else {
            $data['error'] = Yii::t('app', 'Please enter Email Address');
        }
        $this->response = $data;
    }

    public function actionAddFeaturedImage()
    {
        $data = [];
        $model = new FeaturedImage();
        $post = \Yii::$app->request->post();
        if ($model->load($post)) {
            if (! empty($_FILES)) {
                $model->saveUploadedFile($model, 'image');
            }
            if ($model->save()) {
                $data['status'] = self::API_OK;
                $data['msg'] = \Yii::t('app', 'Featured Image Added Successfully');
            }
        } else {
            $data['error'] = Yii::t('app', 'No data posted');
        }
        $this->response = $data;
    }

    public function actionUpdateFeaturedImage($id)
    {
        $data = [];
        $model = FeaturedImage::find()->where([
            'id' => $id
        ])->one();
        if (! empty($model)) {
            $old_image = $model->image;
            $post = \Yii::$app->request->post();
            if ($model->load($post)) {
                if (! empty($_FILES)) {
                    $model->saveUploadedFile($model, 'image', $old_image);
                }
                if ($model->save()) {
                    $data['status'] = self::API_OK;
                    $data['msg'] = \Yii::t('app', 'Featured Image Updated Successfully');
                }
            } else {
                $data['error'] = Yii::t('app', 'No data posted');
            }
        } else {
            $data['error'] = Yii::t('app', 'No Professional posted');
        }
        $this->response = $data;
    }

    public function actionGetFeaturedImage($id = null, $page = null)
    {
        $data = [];
        if (! empty($id)) {
            $query = FeaturedImage::find()->where([
                'created_by_id' => $id
            ]);
        } else {
            $query = FeaturedImage::find()->where([
                'created_by_id' => \Yii::$app->user->id
            ]);
        }
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '10',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        
        $data = $pagination->serialize($dataProvider);
        $data['status'] = self::API_OK;
        $this->response = $data;
    }

    public function actionFeaturedImage($id)
    {
        $data = [];
        
        $model = FeaturedImage::find()->where([
            'id' => $id
        ])->one();
        
        if (! empty($model)) {
            $data['detail'] = $model->asJson();
            $data['status'] = self::API_OK;
        } else {
            $data['error'] = \Yii::t('app', 'No Featured Image Found');
        }
        $this->response = $data;
    }

    public function actionDeleteFeaturedImage($id)
    {
        $data = [];
        $model = FeaturedImage::find()->where([
            'id' => $id
        ])->one();
        if (! empty($model)) {
            if ($model->delete()) {
                $data['status'] = self::API_OK;
                $data['msg'] = \Yii::t('app', 'Featured Image deleted successfully');
            } else {
                $data['error'] = $model->getErrors();
            }
        } else {
            $data['error'] = \Yii::t('app', 'No Featured Image Found');
        }
        $this->response = $data;
    }

    public function actionSendMessage()
    {
        $data = [];
        $post = \Yii::$app->request->post();
        $model = new Chatmessage();
        if ($model->load($post)) {
            $model->type_id = Chatmessage::TYPE_MESSAGE;
            $model->from_user_id = \Yii::$app->user->id;
            $model->from_user_name = \Yii::$app->user->identity->full_name;
            if ($model->save()) {
                $response = new Chatresponse();
                $response->message_id = $model->id;
                $response->type_id = Chatresponse::TYPE_READ;
                $response->created_by_id = $model->from_user_id;
                $response->save();
                
                $responseModal = new Chatresponse();
                $responseModal->message_id = $model->id;
                $responseModal->type_id = Chatresponse::TYPE_NOT_READ;
                $responseModal->created_by_id = $model->to_user_id;
                $responseModal->save();
                
                if (Notification::create($param = [
                    'to_user_id' => $model->to_user_id,
                    'created_by_id' => $model->from_user_id,
                    'title' => $model->message,
                    'model' => $model
                ])) {
                    $data['data'] = $model->asJson();
                    $data['status'] = self::API_OK;
                } else {
                    $data['error'] = 'Error';
                }
            } else {
                $data['error'] = $model->getErrorsString();
            }
        } else {
            $data['error'] = \Yii::t('app', 'No Data Posted');
        }
        
        $this->response = $data;
    }

    public function actionReceiveMessage($to_user_id)
    {
        $data = [];
        
        $response = Chatresponse::find()->select('message_id')->where([
            'created_by_id' => \Yii::$app->user->id,
            'type_id' => Chatresponse::TYPE_NOT_READ
        ]);
        
        $messages = Chatmessage::find()->where([
            'in',
            'id',
            $response
        ])
            ->andWhere([
            'from_user_id' => $to_user_id
        ])
            ->all();
        
        foreach ($messages as $message) {
            $chatResponse = Chatresponse::updateAll([
                'type_id' => Chatresponse::TYPE_READ
            ], [
                'created_by_id' => \Yii::$app->user->id,
                'message_id' => $message->id
            ]);
            $data['list'][] = $message->asJson(true);
        }
        
        if (! empty($data['list'])) {
            $data['status'] = self::API_OK;
        }
        
        $this->response = $data;
    }

    public function actionGetMessage($id)
    {
        $response = [];
        
        $messages = Chatmessage::find()->select('id, to_user_name, type_id, from_user_name, to_user_id, from_user_id, created_on, message')
            ->where([
            'from_user_id' => \Yii::$app->user->id,
            'to_user_id' => $id
        ])
            ->orWhere([
            'from_user_id' => $id,
            'to_user_id' => \Yii::$app->user->id
        ])
            ->all();
        
        $user = User::findOne($id);
        
        $response['toUser'] = $user->messageJson();
        $response['fromUser'] = \Yii::$app->user->identity->messageJson();
        
        foreach ($messages as $message) {
            $chatResponse = Chatresponse::updateAll([
                'type_id' => Chatresponse::TYPE_READ
            ], [
                'created_by_id' => \Yii::$app->user->id,
                'message_id' => $message->id
            ]);
            $response['list'][] = $message->asJson(true);
        }
        
        $response['status'] = self::API_OK;
        
        $this->response = $response;
    }

    public function actionUserSearch($name, $page = null)
    {
        $response = [];
        
        if (! empty($name)) {
            
            $chat = Chatmessage::find()->select('from_user_id')
                ->where([
                'to_user_id' => \Yii::$app->user->id
            ])
                ->column();
            $query = User::find()->where([
                'like',
                'full_name',
                $name
            ])
                ->andWhere([
                '!=',
                'id',
                \Yii::$app->user->id
            ])
                ->andWhere([
                'IN',
                'id',
                $chat
            ]);
            
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => '10',
                    'page' => $page
                ]
            ]);
            
            $pagination = new TPagination();
            
            $response = $pagination->serialize($dataProvider);
            $response['status'] = self::API_OK;
        } else {
            $response['error'] = \yii::t('app', 'Add name in search field');
        }
        
        $this->response = $response;
    }

    public function actionUpload($toId)
    {
        $image = UploadedFile::getInstanceByName('attachment');
        $response = [];
        
        $toUser = User::findOne($toId);
        if (! empty($image) && ! empty($toUser)) {
            $message = new Chatmessage();
            $message->type_id = Chatmessage::TYPE_ATTACHMENT;
            $message->to_user_id = $toId;
            $message->message = '';
            $message->to_user_name = $toUser->getFullName();
            $message->from_user_id = \Yii::$app->user->id;
            $message->from_user_name = \Yii::$app->user->identity->getFullName();
            
            if ($message->save()) {
                $model = new Chatmedia();
                $model->message_id = $message->id;
                $model->uploadImageByFile($image);
                
                if (! $model->save()) {
                    $response['error'] = $model->getErrorsString();
                } else {
                    $chatresponse = new Chatresponse();
                    $chatresponse->message_id = $message->id;
                    $chatresponse->type_id = Chatresponse::TYPE_READ;
                    $chatresponse->created_by_id = $message->from_user_id;
                    $chatresponse->save();
                    
                    $responseModal = new Chatresponse();
                    $responseModal->message_id = $message->id;
                    $responseModal->type_id = Chatresponse::TYPE_NOT_READ;
                    $responseModal->created_by_id = $message->to_user_id;
                    $responseModal->save();
                    
                    $file = UPLOAD_PATH . $model->file;
                    if (! is_file($file)) {
                        $response['error'] = \Yii::t('app', 'Invalid File');
                        return $this->response = $response;
                    }
                    if ($model->type_id == Chatmedia::TYPE_DOC) {
                        $title = \Yii::t('app', 'New Document File Received');
                    } elseif ($model->type_id == Chatmedia::TYPE_IMAGE) {
                        $title = \Yii::t('app', 'New Image File Received');
                    } else {
                        $title = \Yii::t('app', 'New Video or Audio File Received');
                    }
                    if (Notification::create($param = [
                        'to_user_id' => $message->to_user_id,
                        'created_by_id' => $message->from_user_id,
                        'title' => $title,
                        'model' => $message
                    ])) {
                        $response['status'] = self::API_OK;
                        $response['data'] = $message->asJson(true);
                    } else {
                        $data['error'] = 'Error';
                    }
                }
            } else {
                $response['error'] = $message->getErrorsString();
            }
        }
        $this->response = $response;
    }

    public function actionChatList($page = null)
    {
        $response = [];
        
        $to = Chatmessage::find()->select('to_user_id')
            ->where([
            'to_user_id' => \Yii::$app->user->id
        ])
            ->orWhere([
            'from_user_id' => \Yii::$app->user->id
        ])
            ->column();
        
        $from = Chatmessage::find()->select('from_user_id')
            ->where([
            'to_user_id' => \Yii::$app->user->id
        ])
            ->orWhere([
            'from_user_id' => \Yii::$app->user->id
        ])
            ->column();
        
        $chat = array_unique(array_merge($to, $from));
        
        $query = User::find()->where([
            '!=',
            'id',
            \Yii::$app->user->id
        ])->andWhere([
            'IN',
            'id',
            $chat
        ]);
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '10',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
    }

    public function actionBudgetList($id, $page = null)
    {
        $response = [];
        $query = Budget::find()->where([
            'type_id' => $id
        ]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '25',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        $pagination->function = 'asJson';
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
    }

    public function actionCreditList($id, $page = null)
    {
        $response = [];
        $query = CreditScore::find()->where([
            'type_id' => $id
        ]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '25',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        $pagination->function = 'asJson';
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
    }

    public function actionDownpaymentList($id, $page = null)
    {
        $response = [];
        $query = DownPayment::find()->where([
            'type_id' => $id
        ]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '25',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        $pagination->function = 'asJson';
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
    }

    public function actionPropertyTypeList($id, $page = null)
    {
        $response = [];
        $query = PropertyType::find()->where([
            'type_id' => $id
        ]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '25',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        $pagination->function = 'asJson';
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
    }

    public function actionRepresentationList($id, $page = null)
    {
        $response = [];
        $query = Representation::find()->where([
            'type_id' => $id
        ]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '25',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        $pagination->function = 'asJson';
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
    }

    public function actionTimePeriodList($id, $page = null)
    {
        $response = [];
        $query = TimePeriod::find()->where([
            'type_id' => $id
        ]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '25',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        $pagination->function = 'asJson';
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
    }

    public function actionAddBlogs()
    {
        $response = [];
        $model = new BlogPost();
        $post = \Yii::$app->request->post();
        $model->state_id = BlogPost::STATE_INACTIVE;
        if ($model->load($post)) {
            if (! empty($_FILES)) {
                $model->saveUploadedFile($model, 'image_file');
            }
            
            if ($model->save()) {
                $response['status'] = self::API_OK;
                $response['details'] = $model->asJson();
            } else {
                $response['error'] = $model->getErrorsString();
            }
        }
        
        $this->response = $response;
    }

    public function actionUpdateBlogs($id)
    {
        $response = [];
        $model = BlogPost::findOne($id);
        $old = $model->image_file;
        $post = \Yii::$app->request->post();
        if ($model->load($post)) {
            if (! $model->saveUploadedFile($model, 'image_file', $old)) {
                $model->image_file = $old;
            }
            if ($model->save()) {
                $response['status'] = self::API_OK;
                $response['details'] = $model->asJson();
            } else {
                $response['error'] = $model->getErrorsString();
            }
        }
        
        $this->response = $response;
    }

    public function actionBlogList($id = null, $page = null)
    {
        $response = [];
        if (! empty($id)) {
            $query = BlogPost::find()->where([
                'created_by_id' => $id
            ])->orderBy('id DESC');
        } else {
            $query = BlogPost::find()->where([
                'state_id' => BlogPost::STATE_ACTIVE
            ])->orderBy('id DESC');
        }
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '25',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        $pagination->function = 'asJson';
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
        
        $this->response = $response;
    }

    public function actionStateList($page = null)
    {
        $response = [];
        
        $query = City::findActive();
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => '25',
                'page' => $page
            ]
        ]);
        
        $pagination = new TPagination();
        $pagination->function = 'asJson';
        $response = $pagination->serialize($dataProvider);
        $response['status'] = self::API_OK;
        $this->response = $response;
        
        $this->response = $response;
    }
}
