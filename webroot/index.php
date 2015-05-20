<?php
require __DIR__.'/config_with_app.php'; 

$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

$app->router->add('', function() use ($app) 
{
    $app->theme->setTitle("HEY BUGGERS");

    $app->dispatcher->forward([
        'controller' => 'reports',
        'action'     => 'list',
        'params'     => ['order' => 'created']
    ]);

    $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'listActive',
    ]);

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'listActive',
    ]);
});


$app->router->add('reports', function() use ($app) 
{
    $app->theme->setTitle("REPORTS");

    $app->dispatcher->forward([
        'controller' => 'reports',
        'action'     => 'list',
        'params'     => ['order' => 'votes',]
    ]);

    $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'listActive',
    ]);

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'listActive',
    ]);
});


$app->router->add('tags', function() use ($app) 
{
    $app->theme->setTitle("TAGS");

    $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'list',
    ]);

    $app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'listActive',
    ]);
});


$app->router->add('users', function() use ($app) 
{
    $app->theme->setTitle("USERS");

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'list',
    ]);

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'listActive',
    ]);
});


$app->router->add('register', function() use ($app) 
{
    $app->theme->setTitle("REGISTER USER");

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'add',
    ]);
});


$app->router->add('about', function() use ($app) 
{
    $app->theme->setTitle("ABOUT BUGGERS");

    $content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->add('buggers/page', [
        'title'     => "About",
        'class'     => 'about-page',
        'content'   => $content
    ]);
});


$app->router->add('login', function() use ($app) 
{
    $app->theme->setTitle("LOGIN");

    $app->dispatcher->forward([
        'controller' => 'auth',
        'action'     => 'loginForm',
    ]);
});


$app->router->add('logout', function() use ($app) 
{
    $app->theme->setTitle("LOGOUT");

    $app->dispatcher->forward([
        'controller' => 'auth',
        'action'     => 'logout',
    ]);
});

include __DIR__.'/db-setup.php'; 


$app->router->handle();
$app->theme->render();