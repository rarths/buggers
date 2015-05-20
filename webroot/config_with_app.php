<?php
/**
 * Config file for pagecontrollers, creating an instance of $app.
 *
 */

// Get environment & autoloader.
require __DIR__.'/config.php'; 

// Create services and inject into the app. 
$di  = new \Anax\DI\CDIFactoryDefault();
$app = new \Anax\Kernel\CAnax($di);

// Start session 
$app->session();


// Create services and inject into the app. 
$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/database_mysql.php');
    $db->connect();
    return $db;
});

// Controllers
$di->set('AuthController', function() use ($di) {
    $controller = new \Anax\Auth\AuthController();
    $controller->setDI($di);
    return $controller;
});


$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});


$di->set('ReportsController', function() use ($di) {
    $controller = new \Anax\Reports\ReportsController();
    $controller->setDI($di);
    return $controller;
});


$di->set('CommentsController', function() use ($di) {
    $controller = new \Anax\Comments\CommentsController();
    $controller->setDI($di);
    return $controller;
});


$di->set('VotesController', function() use ($di) {
    $controller = new \Anax\Votes\VotesController();
    $controller->setDI($di);
    return $controller;
});


$di->set('TagsController', function() use ($di) {
    $controller = new \Anax\Tags\TagsController();
    $controller->setDI($di);
    return $controller;
});

// Other services
$di->set('sparkles', function () use ($di) {
    $sparkles = new \Anax\Sparkles\CSparkles();
    $sparkles->setDI($di);
    return $sparkles;
});


$di->set('textFilter', function() use ($di) {
    $textFilter = new \Mos\TextFilter\CTextFilter();
    return $textFilter;
});


$di->set('form', function () use ($di) {
    $form = new \Mos\HTMLForm\CForm();
    return $form;
});