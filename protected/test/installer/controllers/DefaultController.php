<?php
/**
 *@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
 *@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
 */
namespace app\modules\installer\controllers;

use app\models\User;
use app\modules\installer\helpers\InstallerHelper;
use app\modules\installer\models\SetupDb;
use app\modules\installer\models\SystemCheck;
use Exception;
use Yii;
use yii\web\Controller;
use app\modules\installer\models\Mail;

/**
 * Default controller for the `install` module
 */
class DefaultController extends Controller
{

    public $setup;

    public $setupDone = false;

    public function beforeAction($action)
    {
        $this->setup = new SetupDb();
        
        $this->setup->db_name = Yii::$app->request->get('db_name', Yii::$app->id);
        $this->setup->username = Yii::$app->request->get('username', 'root');
        $this->setup->password = Yii::$app->request->get('password', '');
        $this->setup->host = Yii::$app->request->get('host', '127.0.0.1');
        
        if (Yii::$app->request->get('db_name')) {
            $this->setupDone = true;
        }
        if (file_exists(DB_CONFIG_FILE_PATH . '.setup')) {
            $dbconfig = include (DB_CONFIG_FILE_PATH . '.setup');
            $this->setup->username = $dbconfig['username'];
            $this->setup->password = $dbconfig['password'];
            if (preg_match('/dbname=(.*)$/', $dbconfig['dsn'], $matches)) {
                // VarDumper::dump($matches);
                $this->setup->db_name = $matches[1];
                $this->setupDone = true;
            }
        }
        
        return parent::beforeAction($action);
    }

    /**
     * Renders the index view for the module
     *
     * @return string
     */
    public function actionIndex()
    {
        if ($this->setupDone) {
            return $this->handleSetup($this->setup);
        }
        return $this->render('index');
    }

    public function actionGo()
    {
        $checks = SystemCheck::getResults($this->module, false);
        
        $hasError = $checks['summary']['errors'];
        
        // Render template
        return $this->render('check', [
            'checks' => $checks['requirements'],
            'hasError' => $hasError
        ]);
    }

    public function checkDB($model)
    {
        $dbValid = true;
        // Connect to MySQL
        $link = @mysqli_connect($model->host, $model->username, $model->password);
        if (! $link) {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            exit();
        }
        
        // Make my_db the current database
        $db_selected = @mysqli_select_db($link, $model->db_name);
        
        if (! $db_selected) {
            $dbValid = false;
            // If we couldn't, then it either doesn't exist, or we can't see it.
            $sql = 'CREATE DATABASE ' . $model->db_name;
            
            if (@mysqli_query($link, $sql)) {
                echo "Database my_db created successfully\n";
                $dbValid = true;
            } else {
                echo 'Error creating database: ' . mysqli_error($link) . "\n";
            }
        }
        
        @mysqli_close($link);
        return $dbValid;
    }

    /*
     * STEP 2
     * CONFIGURE THE DATABASE FILE
     * Setupdb
     */
    public function actionStep2()
    {
        $model = new SetupDb();
        $mail = new Mail();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $model->setAttributes($_POST['SetupDb']);
            if ($mail->load($post)) {
                $mail->setAttributes($_POST['Mail']);
                $this->handleSetup($model, $mail);
                // $this->handleMailerProd($mail);
            }
        }
        
        return $this->render('database', [
            'model' => $model,
            'mail' => $mail
        ]);
    }

    public function handleSetup($model, $mail = null)
    {
        if ($this->checkDB($model)) {
            $success = true;
            try {
                
                $db = \Yii::$app->set('db', [
                    'class' => 'yii\db\Connection',
                    'dsn' => "mysql:host=$model->host;dbname=$model->db_name",
                    'emulatePrepare' => true,
                    'username' => $model->username,
                    'password' => $model->password,
                    'charset' => 'utf8',
                    'tablePrefix' => $model->table_prefix
                ]);
            } catch (Exception $e) {
                $success = false;
            }
        }
        
        if ($success) {
            
            $text_file = "<?php
				return [
				'class' => 'yii\db\Connection',
				'dsn' => 'mysql:host=$model->host;dbname=$model->db_name',
				'emulatePrepare' => true,
				'username' => '$model->username',
				'password' => '$model->password',
				'charset' => 'utf8',
				'tablePrefix' => '$model->table_prefix',
                'enableSchemaCache' => true,
                'schemaCacheDuration' => 3600,
                'schemaCache' => 'cache',
				];";
            
            try {
                
                InstallerHelper::setCookie();
                
                file_put_contents(DB_CONFIG_FILE_PATH, $text_file);
                
                $message = InstallerHelper::execSqlFiles($this->module->sqlfile);
                
                if ($message != 'NOK') {
                    if ($mail != null && $mail->is_mail_prod == Mail::IS_MAIL) {
                        $this->handleMailerProd($mail);
                    }
                    
                    $count = User::find()->count();
                    if ($count > 0) {
                        return $this->redirect([
                            '/user/login'
                        ]);
                    } else {
                        return $this->redirect([
                            '/user/add-admin'
                        ]);
                    }
                } else {
                    unlink(DB_CONFIG_FILE_PATH);
                    \Yii::$app->session->setFlash('error', $message);
                }
            } catch (Exception $e) {
                
                unlink(DB_CONFIG_FILE_PATH);
                \Yii::$app->session->setFlash('error', 'Unable to setup Database.');
                // echo $e->getTraceAsString ();
            }
        } else {
            \Yii::$app->session->setFlash('error', 'database not ready');
        }
    }

    function handleMailerProd($mail)
    {
        if (isset(\Yii::$app->setting)) {
            // TODO Create mail config if setting component exist
        } else {
            $textFile = "<?php
                        return [
                            'class' => 'yii\swiftmailer\Mailer',
                            'transport' => [
                                'class' => 'Swift_SmtpTransport',
                                'host' => '{host}',
                                'username' => '{username}',
                                'password' => '{password}',
                                'port' => '{port}',
                                'encryption'=>'{encryption}',
                                'streamOptions' => [
                                    'ssl' => [
                                        'verify_peer' => false,
                                        'verify_peer_name' => false,
                                        'allow_self_signed' => true
                                    ]
                                ]
                            ],
                        ];";
            
            $textFile = str_replace([
                '{host}',
                '{username}',
                '{password}',
                '{port}',
                '{encryption}'
            ], [
                $mail->host,
                $mail->username,
                $mail->password,
                $mail->port,
                $mail->encryption
            ], $textFile);
            
            file_put_contents(DB_CONFIG_PATH . '/mailer-prod.php', $textFile);
        }
    }
}
