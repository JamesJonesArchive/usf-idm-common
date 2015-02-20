<?php
use \USF\auth\SlimAuthMiddleware;
use \USF\IdM\SlimLogMiddleware;

require_once('vendor/autoload.php');

$app = new \Slim\Slim();

//Get app environment variables
$app->environment['auth.config.cas'] = array ('environment' => 'development');
//Authenticate requests to /api/* with CAS and permit all users
$app->environment['auth.interceptUrlMap'] = ['GET' => ['/**' => ['authN' => 'CAS', 'authZ' => 'permitAll']]];

$app->environment['log.config'] = [
    ['name'=>'audit', 'type' => 'file', 'config' => ['log_location' => '/tmp/audit.out'], 'processor' => 'introspection'],
    ['name'=>'debug', 'type' => 'file', 'default' => true, 'config' => ['log_level' => 'debug', 'log_location' => '/tmp/debug.out']],
    ['name'=>'audit', 'type' => 'file', 'config' => ['log_location' => '/tmp/err.out', 'log_level' => 'error']]
];

//Add the Logging Middleware
$app->add(new SlimLogMiddleware());

//Add the Auth Middleware
$app->add(new SlimAuthMiddleware());

$app->get('/foo', function () use ($app) {
    echo "Hello ".$app->environment['principal.name']. "<br />";
    print_r($app->environment['principal.attributes']);

    $app->log->debug->warn($app->environment['principal.name']);

    //This should go to /tmp/audit.out
    $app->log->audit->warn("test",$app->environment['principal.attributes']);

    //This is using the default logger and should go to /tmp/debug.out
    $app->log->alert('this is an alert!');

    //This should go to /tmp/audit.out AND /tmp/err.out
    $app->log->audit->error('This is an error!');
});
$app->run();
