<?php
/**
 * Silex - abcArticles: A small web app.
 *
 * @author Fabiano Monteiro <fabianophp@hotmail.com>
 *
 */

use Symfony\Component\HttpFoundation\Response;

#ERROR PAGES - Check error in log file
$app->error(function (\Exception $e, $code) use ($app) {

    switch ($code) {
        case 403:
            $message = $app['twig']->render('exception/error403.html.twig');
            $app['monolog']->addDebug(sprintf('Caught Error: %s', $e->getMessage()));
            break;
        case 404:
            $message = $app['twig']->render('exception/error404.html.twig');
            $app['monolog']->addDebug(sprintf('Caught Error: %s', $e->getMessage()));
            break;
        default:
            $message = $app['twig']->render('exception/error500.html.twig');
            $app['monolog']->addDebug(sprintf('Caught Error: %s', $e->getMessage()));
    }
    return new Response($message, $code);
});
