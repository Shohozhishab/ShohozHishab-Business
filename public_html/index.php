<?php

// Check PHP version.
$minPhpVersion = '7.4'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    exit($message);
}

//Installation process (start)
session_start();
if (!isset($_SESSION['install'])) {
    $redirectInstallation = $_SERVER['REQUEST_URI'].'installation/index.php';
    $InstallPath = __DIR__."/installation";
    if(file_exists($InstallPath) && is_dir($InstallPath)) {
        header('Location:'.$redirectInstallation);
        die();
    }
}else{
    // config url make(start)
    $uri  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $uri .= $_SERVER['SERVER_NAME'];
    $uri .= $_SERVER['REQUEST_URI'];
    // config url make(end)




    // installation folder rename(start)
    if ($_SESSION['install'] == 1) {

        rename(realpath(dirname(__FILE__)).'/installation',realpath(dirname(__FILE__)).'/installed');
        // installation folder rename(end)


        //database update(start)
        $file = file(__DIR__.'../../app/Config/Database.php');
        $hostname = "\t\t\t'hostname' => '".$_SESSION['DB']['dbHost']."',";
        $username = "\t\t\t'username' => '".$_SESSION['DB']['dbUserName']."',";
        $password = "\t\t\t'password' => '".$_SESSION['DB']['dbPassword']."',";
        $database = "\t\t\t'database' => '".$_SESSION['DB']['dbName']."',";
        $host = 34;
        $user = 35;
        $pass = 36;
        $db = 37;
        foreach($file as $index => $line){
            if($index == $host){
                $file[$index] = $hostname . "\n";
            }
            if($index == $user){
                $file[$index] = $username . "\n";
            }
            if($index == $pass){
                $file[$index] = $password . "\n";
            }
            if($index == $db){
                $file[$index] = $database . "\n";
            }

        }
        $content = implode($file);
        file_put_contents(__DIR__.'../../app/Config/Database.php', $content);
        // database update(end)





        //config update(start)
        $key = "public \$baseURL";
        $filecon = file(__DIR__.'../../app/Config/App.php');
        $configdata = $key." = '".$uri."';";
        $conline = 26;
        foreach($filecon as $index => $line){
            if($index == $conline){
                $filecon[$index] = $configdata . "\n";
            }
        }
        $contentConfig = implode($filecon);
        file_put_contents(__DIR__.'../../app/Config/App.php', $contentConfig);


    }

}
session_destroy();
//Installation process (end)



// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
chdir(FCPATH);

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Load our paths config file
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Config\Paths();

// Location of the framework bootstrap file.
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Load environment settings from .env files into $_SERVER and $_ENV
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

/*
 * ---------------------------------------------------------------
 * GRAB OUR CODEIGNITER INSTANCE
 * ---------------------------------------------------------------
 *
 * The CodeIgniter class contains the core functionality to make
 * the application run, and does all of the dirty work to get
 * the pieces all working together.
 */

$app = Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */

$app->run();
