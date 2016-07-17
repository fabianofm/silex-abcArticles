<?php
/**
 * Silex - abcArticles: A small web app.
 *
 * @author Fabiano Monteiro <fabianophp@hotmail.com>
 *
 */

#ARTICLES
$app->get('/articles', function() use($app){

  if (!$app['security.authorization_checker']->isGranted('ROLE_USER')) {
    $query_restricted = "WHERE restricted='N'";
  }
  $articles = $app['db']->fetchAll("SELECT * FROM articles $query_restricted ");
  return $app['twig']->render('pages/show-article.html.twig', Array(
    'articles' => $articles,
  ));
})->bind('articles');
#END/ARTICLES
#ARTICLE Single
$app->get('/article/{id}', function($id) use($app){

  if (!$app['security.authorization_checker']->isGranted('ROLE_USER')) {
    $query_restricted = "restricted='N' AND";
  }
  $single = $app['db']->fetchAssoc("SELECT * FROM articles WHERE $query_restricted id = ?", array((int) $id));

  $previous = $app['db']->fetchColumn("SELECT id FROM articles WHERE $query_restricted id < ? ORDER BY id DESC LIMIT 1", array((int) $id));
  ($previous)? $single['previous']=$previous : null;

  $next = $app['db']->fetchColumn("SELECT id FROM articles WHERE $query_restricted id > ? ORDER BY id ASC LIMIT 1", array((int) $id));
  ($next)? $single['next']=$next : null;

  return $app['twig']->render('pages/show-article.html.twig', Array(
    'single' => $single,
  ));
})->bind('article');
#END/ARTICLE Single
