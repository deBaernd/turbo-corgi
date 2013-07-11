<?php
define("APP_PATH", "http://".$_SERVER['SERVER_NAME'] .$_SERVER['SCRIPT_NAME'] . "/../");
date_default_timezone_set('Europe/Berlin');

require 'lib/slim/Slim/Slim.php';
require 'lib/slim/Slim/View.php';
require 'lib/slim/Slim/Middleware.php';
require 'lib/slim-extras/Views/Twig.php';
require 'lib/activerecord/ActiveRecord.php';
require 'lib/strong/src/Strong/Strong.php';
require 'lib/strong/src/Strong/Provider.php';
require 'lib/slim-extras/Middleware/StrongAuth.php';

require 'app/Controller.php';
require 'app/auth/Bcrypt.php';
require 'app/auth/AuthProvider.php';

require 'config.php';

ActiveRecord\Config::initialize(function($cfg) {
	$cfg->set_model_directory('.');
	$cfg->set_connections(array('development' => DB_PROVIDER.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOSTNAME.'/'.DB_NAME));
});

session_start();

\Slim\Slim::registerAutoloader();

$twigView = new \Slim\Extras\Views\Twig();

\Slim\Extras\Views\Twig::$twigTemplateDirs = array(
    realpath(APP_PATH . '/app/view')
);

$app = new \Slim\Slim(array(
		'view' => $twigView,
		'log.level' => 4,
		'log.enabled' => true
));

$authConfig = array(
		'provider' => 'AuthProvider',
		'auth.type' => 'form',
		'login.url' => APP_PATH.'index.php/login',
		'security.urls' => array(
				array('path' => '/admin'),
		),
);

$app->add(new \Slim\Extras\Middleware\StrongAuth($authConfig));
$app->get('/', function(){
	print "Hello World!";
});

$app->run();
