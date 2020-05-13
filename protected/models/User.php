<?php

/**
*@author     : Sanjay Jasuja< sanjayjasuja26@gmail.com >
*/

/**
 * This is the model class for table "tbl_user".
 *
 * @property integer $id
 * @property string $full_name
 * @property string $email
 * @property string $password
 * @property string $date_of_birth
 * @property integer $gender
 * @property string $about_me
 * @property string $contact_no
 * @property string $address
 * @property string $latitude
 * @property string $longitude
 * @property string $city
 * @property string $country
 * @property string $zipcode
 * @property string $language
 * @property string $profile_file
 * @property integer $tos
 * @property integer $role_id
 * @property integer $state_id
 * @property integer $type_id
 * @property string $last_visit_time
 * @property string $last_action_time
 * @property string $last_password_change
 * @property integer $login_error_count
 * @property string $activation_key
 * @property string $timezone
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by_id === Related data ===
 * @property LoginHistory[] $loginHistories
 */
namespace app\models;

use Yii;
use yii\web\NotFoundHttpException;
use \thyseus\files\behaviors\HasFilesBehavior;
use app\modules\page\models\Page;

class User extends \app\components\TActiveRecord implements \yii\web\IdentityInterface
{

    public $search;

    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_BANNED = 2;

    const STATE_DELETED = 4;

    const MALE = 0;

    const FEMALE = 1;

    const ROLE_ADMIN = 0;

    const ROLE_COLLEGE = 1;

    const ROLE_UNIVERSITY = 2;
        
    const ROLE_STUDENT = 3;
    
    const ROLE_PARENT = 4;

    const TYPE_ON = 0;

    const TYPE_OFF = 1;

    const EMAIL_NOT_VERIFIED = 0;

    const EMAIL_VERIFIED = 1;

    const LAYOUT_MAIN = 'main';

    const LAYOUT_GUEST_MAIN = 'guest-main';

    const SCENARIO_CHANGEPASSWORD = 'changepassword';

    const SCENARIO_UPDATE = 'update';

    const SCENARIO_SIGNUP = 'signup';

    const SCENARIO_TOKEN_REQUEST = 'token_request';

    const SCENARIO_ADD = 'add';

    const SCENARIO_ADD_ADMIN = 'add-admin';

    const SCENARIO_RESETPASSWORD = 'resetpassword';

    public $confirm_password;

    public $newPassword;

    public $oldPassword;

    public function behaviors()
    {
        return [
            HasFilesBehavior::class
        ];
    }

    public static function getGenderOptions($id = null)
    {
        $list = array(
            self::MALE => "Male",
            self::FEMALE => "Female"
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }

    public static function getRoleOptions($id = null)
    {
        $list = array(
            self::ROLE_ADMIN => "Admin",
            self::ROLE_COLLEGE => "College",
            self::ROLE_UNIVERSITY => "University",
            self::ROLE_STUDENT => "Student",
            self::ROLE_PARENT => "Parent"
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }

    public function __toString()
    {
        return (string) $this->full_name;
    }

    public static function getStateOptions()
    {
        return [
            self::STATE_INACTIVE => "Inactive",
            self::STATE_ACTIVE => "Active",
            self::STATE_BANNED => "Banned",
            self::STATE_DELETED => "Deleted"
        ];
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }

    public function getStateBadge()
    {
        $list = [
            self::STATE_INACTIVE => "info",
            self::STATE_ACTIVE => "success",
            self::STATE_BANNED => "warning",
            self::STATE_DELETED => "danger"
        ];
        // return isset($list[$this->state_id])?\yii\helpers\Html::tag('span', $this->getState(), ['class' => 'badge bg-' . $list[$this->state_id]]):'Not Defined';
        return isset($list[$this->state_id]) ? \yii\helpers\Html::tag('span', $this->getState(), [
            'class' => 'label label-' . $list[$this->state_id]
        ]) : 'Not Defined';
    }

    public static function getTypeOptions()
    {
        return [
            "TYPE1",
            "TYPE2",
            "TYPE3"
        ];
    }

    public function getType()
    {
        $list = self::getTypeOptions();
        return isset($list[$this->type_id]) ? $list[$this->type_id] : 'Not Defined';
    }

    public function getFullName()
    {
        return $this->full_name;
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (! isset($this->created_on))
                $this->created_on = date('Y-m-d H:i:s');
            if (! isset($this->updated_on))
                $this->updated_on = date('Y-m-d H:i:s');
            if (! isset($this->created_by_id))
                $this->created_by_id = Yii::$app->user->id;
            $this->generateAccessToken();
        } else {
            $this->updated_on = date('Y-m-d H:i:s');
        }

        return parent::beforeValidate();
    }

    /**
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'full_name' => Yii::t('app', 'Full Name'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'date_of_birth' => Yii::t('app', 'Date Of Birth'),
            'gender' => Yii::t('app', 'Gender'),
            'about_me' => Yii::t('app', 'About Me'),
            'contact_no' => Yii::t('app', 'Contact No'),
            'alternate_contact_no' => Yii::t('app', 'Alternate Contact No'),
            'address' => Yii::t('app', 'Address'),
            'latitude' => Yii::t('app', 'Latitude'),
            'longitude' => Yii::t('app', 'Longitude'),
            'city' => Yii::t('app', 'City'),
            'country' => Yii::t('app', 'Country'),
            'zipcode' => Yii::t('app', 'Zipcode'),
            'language' => Yii::t('app', 'Language'),
            'profile_file' => Yii::t('app', 'Profile File'),
            'tos' => Yii::t('app', 'Tos'),
            'role_id' => Yii::t('app', 'Role'),
            'state_id' => Yii::t('app', 'State'),
            'type_id' => Yii::t('app', 'Type'),
            'last_visit_time' => Yii::t('app', 'Last Visit Time'),
            'last_action_time' => Yii::t('app', 'Last Action Time'),
            'last_password_change' => Yii::t('app', 'Last Password Change'),
            'login_error_count' => Yii::t('app', 'Login Error Count'),
            'activation_key' => Yii::t('app', 'Activation Key'),
            'timezone' => Yii::t('app', 'Timezone'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_on' => Yii::t('app', 'Updated On'),
            'created_by_id' => Yii::t('app', 'Created By')
        ];
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoginHistories()
    {
        return $this->hasMany(LoginHistory::className(), [
            'user_id' => 'id'
        ]);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasMany(Page::className(), [
            'created_by_id' => 'id'
        ]);
    }

    public static function getHasManyRelations()
    {
        $relations = [];
        $relations['created_by_id'] = [
            'templates',
            'Template',
            'id'
        ];
        $relations['user_id'] = [
            'loginHistories',
            'LoginHistory',
            'id'
        ];

        return $relations;
    }

    public static function getHasOneRelations()
    {
        $relations = [];
        return $relations;
    }

    public function getLoginUrl()
    {
        return Yii::$app->urlManager->createAbsoluteUrl([
            'user/login'
        ]);
    }

    public function getVerified()
    {
        return Yii::$app->urlManager->createAbsoluteUrl([
            'user/confirm-email',
            'id' => $this->activation_key
        ]);
    }

    public function sendVerificationMailtoUser()
    {
        $sub = "Welcome! You new account is ready " . \Yii::$app->params['company'];
        $to = $this->email;
        $message = \yii::$app->view->renderFile('@app/mail/verification.php', [
            'user' => $this
        ]);
        $admin = self::find()->where([
            'role_id' => self::ROLE_ADMIN
        ])->one();
        if (! empty($admin)) {
            $from = $admin->email;
            EmailQueue::sendEmailToAdmins([
                'from' => $from,
                'subject' => $sub,
                'html' => $message
            ], true);
        }
    }

    public function sendEmail()
    {
        return EmailQueue::add([
            'to' => $this->email,
            'subject' => "Recover Your Account at: " . \Yii::$app->params['company'],
            'view' => 'passwordResetToken',
            'viewArgs' => [
                'user' => $this
            ]
        ], true);
    }

    public function sendRegistrationMailtoUser($model)
    {
        $email = $model->email;
        $view = 'sendPassword';
        $sub = "Welcome! You new account is ready " . \Yii::$app->params['company'];
        EmailQueue::add([
            'to' => $email,
            'from' => \Yii::$app->params['adminEmail'],
            'subject' => $sub,
            'view' => 'sendPassword',
            'viewArgs' => [
                'user' => $model,
                'pass' => $model->password
            ]
        ], true);
    }

    public function sendRegistrationMailtoAdmin()
    {
        $sub = 'New User Registerd Successfully';
        $from = $this->email;
        EmailQueue::sendEmailToAdmins([
            'from' => $from,
            'subject' => $sub,
            'view' => 'verification',
            'viewArgs' => [
                'user' => $this
            ]
        ], true);
    }

    public function beforeDelete()
    {
        if ($this->id == \Yii::$app->user->id)
            return false;

        // if (class_exists("app\modules\api\models\AuthSession")) {
        // app\modules\api\models\AuthSession::deleteRelatedAll([
        // 'created_by_id' => $this->id
        // ]);
        // }
        Page::deleteRelatedAll([
            'created_by_id' => $this->id
        ]);
        Comment::deleteRelatedAll([
            'created_by_id' => $this->id
        ]);
        Notice::deleteRelatedAll([
            'created_by_id' => $this->id
        ]);
        return parent::beforeDelete();
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['register'] = [
            'full_name',
            'email',
            'confirm_password',
            'password'
        ];

        $scenarios['add-admin'] = [
            'full_name',
            'email',
            'confirm_password',
            'activation_key',
            'role_id',
            'state_id',
            'created_on',
            'password'
        ];

        $scenarios['update'] = [
            'full_name',
            'email',
            'password',
            'role_id',
            'state_id'
        ];

        $scenarios['add'] = [
            'full_name',
            'email',
            'password',
            'role_id',
            'state_id'
        ];

        $scenarios['signup'] = [
            'full_name',
            'email',
            'password',
            'role_id',
            'confirm_password'
        ];
        $scenarios['changepassword'] = [
            'newPassword',
            'oldPassword',
            'confirm_password'
        ];
        $scenarios['resetpassword'] = [
            'password',
            'confirm_password'
        ];

        $scenarios['token_request'] = [
            'email'
        ];

        return $scenarios;
    }

    /**
     *
     * @inheritdoc
     */
    /**
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'full_name',
                    'email',
                    // 'password',
                    'state_id',
                    'created_on'
                ],
                'required'
            ],
            [
                [
                    'email',
                    'password',
                    'confirm_password',
                    'full_name'
                ],
                'required',
                'on' => 'add-admin'
            ],
            [
                [
                    'email'
                ],
                'required',
                'on' => 'token_request'
            ],
            [
                'email',
                'unique'
            ],
            [
                [
                    'newPassword',
                    'confirm_password'
                ],
                'required',
                'on' => 'changepassword'
            ],
            [
                [
                    'full_name',
                    'email',
                    'role_id',
                    'password',
                    'confirm_password'
                ],
                'required',
                'on' => 'signup'
            ],
            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'password',
                'message' => "Passwords don't match",
                'on' => [
                    'signup'
                ]
            ],
            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'newPassword',
                'message' => "Passwords don't match",
                'on' => [
                    'changepassword'
                ]
            ],
            [
                'confirm_password',
                'compare',
                'compareAttribute' => 'newPassword',
                'message' => "Passwords don't match",
                'on' => [
                    'resetpassword'
                ]
            ],
            [
                [
                    'password',
                    'confirm_password'
                ],
                'required',
                'on' => 'resetpassword'
            ],
            [
                [
                    'password',
                    'newPassword'
                ],
                'app\components\TPasswordValidator'
            ],
            [
                [
                    'full_name'
                ],
                'match',
                'pattern' => '/^[a-zA-Z ]*$/'
            ],

            [

                'email',
                'email'
            ],

            [
                [
                    'search',
                    'date_of_birth',
                    'last_visit_time',
                    'last_action_time',
                    'last_password_change',
                    'created_on',
                    'updated_on'
                ],
                'safe'
            ],
            [
                [
                    'gender',
                    'tos',
                    'role_id',
                    'email_verified',
                    'state_id',
                    'type_id',
                    'login_error_count',
                    'created_by_id'
                ],
                'integer'
            ],
            [
                [
                    'full_name',
                    'email',
                    'about_me',
                    'contact_no',
                    'city',
                    'country',
                    'zipcode',
                    'language',
                    'profile_file',
                    'timezone'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'full_name',
                    'email',
                    'about_me',
                    'contact_no',
                    'city',
                    'country',
                    'zipcode',
                    'language',
                    'profile_file',
                    'timezone',
                    'access_token',
                    'activation_key',
                    'address',
                    'latitude',
                    'longitude'
                ],
                'trim'
            ],
            [
                [
                    'password',
                    'activation_key'
                ],
                'string',
                'max' => 128
            ],
            [
                [
                    'address',
                    'latitude',
                    'longitude'
                ],
                'string',
                'max' => 512
            ]
        ];
    }

    public function asJson($with_relations = false)
    {
        $json = [];
        $json['id'] = $this->id;
        $json['full_name'] = $this->full_name;
        $json['email'] = $this->email;
        $json['date_of_birth'] = $this->date_of_birth;
        $json['gender'] = $this->gender;
        $json['about_me'] = $this->about_me;
        $json['contact_no'] = $this->contact_no;
        $json['alternate_contact_no'] = $this->alternate_contact_no;
        
        $json['address'] = $this->address;
        $json['latitude'] = $this->latitude;
        $json['longitude'] = $this->longitude;
        $json['city'] = $this->city;
        $json['country'] = $this->country;
        $json['zipcode'] = $this->zipcode;
        $json['language'] = $this->language;
        if (isset($this->profile_file))
            $json['profile_file'] = \Yii::$app->urlManager->createAbsoluteUrl('user/profile-image');
        $json['tos'] = $this->tos;
        $json['role_id'] = $this->role_id;
        $json['state_id'] = $this->state_id;
        $json['type_id'] = $this->type_id;
        $json['last_visit_time'] = $this->last_visit_time;
        $json['last_action_time'] = $this->last_action_time;
        $json['last_password_change'] = $this->last_password_change;
        $json['login_error_count'] = $this->login_error_count;
        $json['timezone'] = $this->timezone;
        $json['created_on'] = $this->created_on;
        $json['created_by_id'] = $this->created_by_id;
        $json['access_token'] = $this->access_token;
        if ($with_relations) {}
        return $json;
    }

    /**
     *
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
            'state_id' => [
                self::STATE_ACTIVE
            ]
        ]);
    }

    /**
     *
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token
        ]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne([
            'email' => $username,
            'state_id' => self::STATE_ACTIVE
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token
     *            password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (! static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'activation_key' => $token,
            'state_id' => self::STATE_ACTIVE
        ]);
    }

    public static function randomPassword($count = 8)
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $count; $i ++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token
     *            password reset token
     * @return boolean
     */
    public function getResetUrl()
    {
        return Yii::$app->urlManager->createAbsoluteUrl([
            'user/resetpassword',
            'token' => $this->activation_key
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     *
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     *
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->activation_key;
    }

    /**
     *
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $this->hashPassword($password);
    }

    public function hashPassword($password)
    {
        $password = utf8_encode(Yii::$app->security->generatePasswordHash(yii::$app->name . $password));
        return $password;
    }

    /**
     * Validates password
     *
     * @param string $password
     *            password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (defined('DISABLED_PASSWORD_MATCH')) {
            return true;
        }

        if (defined('ALLOW_PASSWORD_CONVERT')) {
            if (strlen($this->password) == 32) {
                $match = ($this->password == md5($password));
                if ($match) {
                    $this->setPassword($password);
                    $this->save(false, [
                        'password'
                    ]);
                    return true;
                }
                return false;
            }
        }

        return Yii::$app->security->validatePassword(yii::$app->name . $password, utf8_decode($this->password));
    }

    /**
     * convert normal password to hash password before saving it to database
     */

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->activation_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->activation_key = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->activation_key = null;
    }

    public static function isStudent()
    {  
        $user = Yii::$app->user->identity;
        if ($user == null)
            return false;
        return ($user->isActive() && $user->role_id == User::ROLE_STUDENT );
    }
    
    public static function isParent()
    {  
        $user = Yii::$app->user->identity;
        if ($user == null)
            return false;
        return ($user->isActive() && $user->role_id == User::ROLE_PARENT );
    } 
    
    public static function isCollege()
    {  
        $user = Yii::$app->user->identity;
        if ($user == null)
            return false;
        return ($user->isActive() && $user->role_id == User::ROLE_COLLEGE );
    }
    public static function isUniversity()
    {  
        $user = Yii::$app->user->identity;
        if ($user == null)
            return false;
        return ($user->isActive() && $user->role_id == User::ROLE_UNIVERSITY );
    }

    public static function isAdmin()
    {
        $user = Yii::$app->user->identity;
        if ($user == null)
            return false;

        return ($user->isActive() && $user->role_id == User::ROLE_ADMIN);
    }

    public static function isGuest()
    {
        if (Yii::$app->user->isGuest) {
            return true;
        }
        return false;
    }

    public function sendProfileImage()
    {
        $user = Yii::$app->user->identity;
        $image_path = UPLOAD_PATH . $user->profile_file;

        if (! isset($user->profile_file) || ! file_exists($image_path))
            throw new NotFoundHttpException(Yii::t('app', "File not found"));

        return \yii::$app->response->sendFile($image_path, $user->profile_file);
    }

    public static function getUserById($id)
    {
        $user = User::find()->where([
            'id' => $id
        ]);
        return $user;
    }

    public static function findByEmail($email)
    {
        return static::findOne([
            'email' => $email
        ]);
    }

    
    public static function IsEmailVerified($email)
    {
       $model =  static::findOne([
            'email' => $email
        ]);
        if($model->email_verified == User::EMAIL_VERIFIED) {
            return True;
        }
       return false;
    }
    
    public function isActive()
    {
        return ($this->state_id == User::STATE_ACTIVE);
    }

    public function getProfileImage()
    {
        $user = Yii::$app->user->identity;
        $image_path = UPLOAD_PATH . $user->profile_file;

        if (! isset($user->profile_file) || ! file_exists($image_path))
            throw new NotFoundHttpException(Yii::t('app', "File not found"));

        return \yii::$app->response->sendFile($image_path, $user->profile_file);
    }
    public function isAllowed()
    {
      
        if (User::isAdmin())
            return true;
            
            if ($this instanceof User)
            {
                return ($this->id == Yii::$app->user->id);
            }
            if ($this->hasAttribute('created_by_id'))
            {
                return ($this->created_by_id == Yii::$app->user->id);
            }
            
            if ($this->hasAttribute('user_id'))
            {
                return ($this->user_id == Yii::$app->user->id);
            }
            
            return false;
    }
}