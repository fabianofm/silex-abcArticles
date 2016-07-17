<?php
/**
  * Silex - abcArticles: A small web app.
  *
  * @author Fabiano Monteiro <fabianophp@hotmail.com>
  *
  */

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__ . '/../src/UserProvider.php';

$app = new Silex\Application();

require __DIR__ . '/../src/App.php';
require __DIR__ . '/../src/Controllers/Controllers.php';

$app['debug'] = true;
$app->run();
