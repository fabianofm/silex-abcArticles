<?php
/**
 * Silex - abcArticles: A small web app.
 *
 * @author Fabiano Monteiro <fabianophp@hotmail.com>
 *
 */
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/ErrorController.php';
require __DIR__ . '/ShowArticlesController.php';
require __DIR__ . '/AdminUsersController.php';
require __DIR__ . '/AdminArticlesController.php';

//short controllers:
#LOGIN
$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('pages/login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');
#END/LOGIN

#INDEX
$app->get('/', function () use ($app) {
    return $app['twig']->render('pages/index.html.twig');
})->bind('homepage');
#END/INDEX
#RESTRICTED Content on this page is restricted to registered members.
$app->get('/restricted', function () use ($app) {
    return $app['twig']->render('pages/restricted.html.twig');
})->bind('restricted');
#END/RESTRICTED
#ANONYMOUS AND ALL USERS
$app->get('/about', function () use ($app) {
    return $app['twig']->render('pages/about.html.twig');
})->bind('about');
#END/ANONYMOUS
