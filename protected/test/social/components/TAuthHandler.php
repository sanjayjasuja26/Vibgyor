<?php
namespace app\modules\social\components;

use app\modules\social\models\User as SocialUser;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use app\models\User;
use yii\helpers\VarDumper;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class TAuthHandler
{

    /**
     *
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        $attributes = $this->client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'emails.0.value');
        $id = ArrayHelper::getValue($attributes, 'id');
        $fullName = ArrayHelper::getValue($attributes, 'name.givenName');
        $profilePic = ArrayHelper::getValue($attributes, 'profile-pic');
        
        if (! $profilePic) {
            $profilePic = ArrayHelper::getValue($attributes, 'public-profile-url');
        }
        if (! $email) {
            $email = ArrayHelper::getValue($attributes, 'email');
        }
        if (! $fullName) {
            $fullName = ArrayHelper::getValue($attributes, 'name');
        }
        if (! $fullName) {
            $firstName = ArrayHelper::getValue($attributes, 'first-name');
            $lastName = ArrayHelper::getValue($attributes, 'last-name');
            $fullName = $firstName . " " . $lastName;
        }
        /* @var Auth $auth */
        
        $auth = SocialUser::find()->where([
            'social_provider' => $this->client->getId(),
            'social_user_id' => $id
        ])
            ->one();
        
        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                /**
                 *
                 * @var User $user
                 */
                $user = User::findOne([
                    'id' => $auth->user_id
                ]);
                if ($user) {
                    \Yii::$app->user->login($user, 3600 * 24 * 30);
                } else {
                    $user = $this->saveUser($email, $fullName);
                    
                    if ($user) {
                        \Yii::$app->user->login($user, 3600 * 24 * 30);
                    }
                }
            } else { // signup
                $user = User::find()->where([
                    'email' => $email
                ])->one();
                if ($email != null && $user == null) {
                    
                    $user = $this->saveUser($email, $fullName);
                }
                if (! ($user->hasErrors())) {
                    $auth = new SocialUser();
                    $auth->user_id = $user->id;
                    $auth->social_provider = $this->client->getId();
                    $auth->social_user_id = (string) $id;
                    
                    
                    
                    if (! $auth->save()) {
                       /*  VarDumper::dump($auth);
                        die(); */
                        Yii::$app->getSession()->setFlash('error', 
                            Yii::t('app', 'Unable to save {client} account: {errors}', [
                                'client' => $this->client->getTitle(),
                                'errors' => json_encode($auth->getErrors())
                            ])
                        );
                    }
                    
                    \Yii::$app->user->login($user, 3600 * 24 * 30);
                } else {
                 
                    Yii::$app->getSession()->setFlash('error', 
                        Yii::t('app', 'Unable to save user: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($user->getErrors())
                        ])
                    );
                }
            }
        } else { // user already logged in
            if (! $auth) { // add auth provider
                $auth = new SocialUser();
                $auth->user_id = \Yii::$app->user->id;
                $auth->social_provider = $this->client->getId();
                $auth->social_user_id = (string) $id;
                if ($auth->save()) {
                    /** @var User $user */
                    $user = $auth->user;
                    $this->updateUserInfo($user);
                    Yii::$app->getSession()->setFlash('success', 
                        Yii::t('app', 'Linked {client} account.', [
                            'client' => $this->client->getTitle()
                        ])
                    );
                } else {
                    Yii::$app->getSession()->setFlash('error', 
                        Yii::t('app', 'Unable to link {client} account: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($auth->getErrors())
                        ])
                    );
                }
            } else { // there's existing auth
                Yii::$app->getSession()->setFlash('error', 
                    Yii::t('app', 'Unable to link {client} account. There is another user using it.', [
                        'client' => $this->client->getTitle()
                    ])
                );
            }
        }
    }

    public function saveUser($email, $fullName)
    {
        $password = Yii::$app->security->generateRandomString(8);
        $user = new User();
        $user->full_name = $fullName;
        $user->email = $email;
        $user->email_verified = true;
        $user->setPassword($password);
        $user->role_id = User::ROLE_USER;
        $user->state_id = User::STATE_ACTIVE;
        $user->created_on = date('Y-m-d H:i:s');
        // $user->generateAuthKey();
        // $user->generatePasswordResetToken();
        $user->save();
        return $user;
    }

    /**
     *
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {
        $attributes = $this->client->getUserAttributes();
        $github = ArrayHelper::getValue($attributes, 'login');
        if ($user->github === null && $github) {
            $user->github = $github;
            $user->save();
        }
    }
}