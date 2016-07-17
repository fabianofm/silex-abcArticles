<?php
/**
 * Silex - abcArticles: A small web app.
 *
 * @author Fabiano Monteiro <fabianophp@hotmail.com>
 *
 */

use Doctrine\DBAL\Schema\Table;

//CREATE Tables
$users = new Table('users');
$users->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$users->addColumn('username', 'string', array('length' => 30));
$users->addColumn('password', 'string', array('length' => 255));
$users->addColumn('roles', 'string', array('length' => 100));
$users->addUniqueIndex(array('username'));
$users->setPrimaryKey(array('id'));
$schema->createTable($users);

$articles = new Table('articles');
$articles->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$articles->addColumn('title', 'string', array('length' => 90));
$articles->addColumn('img', 'string', array('length' => 50, 'notnull' => false));
$articles->addColumn('content', 'text', array());
$articles->addColumn('author', 'string', array('length' => 30));
$articles->addColumn('restricted', 'string', array('length' => 1));
$articles->addColumn('created', 'datetime', array());
$articles->addColumn('updated', 'datetime', array());
$articles->setPrimaryKey(array('id'));
$schema->createTable($articles);

//INSERT Users
$app['db']->insert('users', array(
    'username' => 'root',
    'password' => $app['security.encoder.digest']->encodePassword(321, ''),
    'roles' => 'ROLE_ADMIN'
));
$app['db']->insert('users', array(
    'username' => 'userx',
    'password' => $app['security.encoder.digest']->encodePassword(321, ''),
    'roles' => 'ROLE_USER'
));

//INSERT Articles
$articles = [
    [
        'title' => 'A Lorem ipsum dolor sit amet',
        'img' => null,
        'content' => '<p>A Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sed malesuada tortor, non sagittis risus. Cras congue gravida condimentum. Nunc sed tristique ante, ac dictum tellus. Quisque tincidunt metus felis, et malesuada nulla fringilla in. Aliquam erat volutpat. Morbi convallis rhoncus quam eget commodo. Aenean efficitur nec ex ut mollis. Aenean vehicula velit quam, vitae lacinia purus sollicitudin ornare. Ut in pharetra libero. Suspendisse consectetur ante in venenatis porttitor. Maecenas sagittis molestie facilisis. Nam quis sem erat.
    <br />Donec quis elementum augue, nec congue turpis. Etiam ac sagittis nunc. Proin id magna quis libero euismod molestie sed quis ex. Nulla id eros sit amet lectus dictum fringilla ac quis risus. Nullam eu aliquam elit. Maecenas sed leo ut massa aliquet bibendum. Sed eu ultrices massa. Nam nec purus sed nibh pretium convallis. Morbi auctor quam et neque eleifend, id faucibus metus convallis. Etiam at est dapibus, sodales augue vitae, consectetur diam.</p>',
        'author' => 'Lorem Author A',
        'restricted' => 'N',
        'created' => '2016-06-26 21:30:00',
        'updated' => '2016-06-26 21:30:00'
    ],
    [
        'title' => 'B Lorem ipsum dolor sit amet',
        'img' => 'img-1.jpg',
        'content' => '<p>B Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sed malesuada tortor, non sagittis risus. Cras congue gravida condimentum. Nunc sed tristique ante, ac dictum tellus. Quisque tincidunt metus felis, et malesuada nulla fringilla in. Aliquam erat volutpat. Morbi convallis rhoncus quam eget commodo. Aenean efficitur nec ex ut mollis. Aenean vehicula velit quam, vitae lacinia purus sollicitudin ornare. Ut in pharetra libero. Suspendisse consectetur ante in venenatis porttitor. Maecenas sagittis molestie facilisis. Nam quis sem erat.
    <br />Donec quis elementum augue, nec congue turpis. Etiam ac sagittis nunc. Proin id magna quis libero euismod molestie sed quis ex. Nulla id eros sit amet lectus dictum fringilla ac quis risus. Nullam eu aliquam elit. Maecenas sed leo ut massa aliquet bibendum. Sed eu ultrices massa. Nam nec purus sed nibh pretium convallis. Morbi auctor quam et neque eleifend, id faucibus metus convallis.<br />Etiam at est dapibus, sodales augue vitae, consectetur diam.</p>',
        'author' => 'Lorem Author B',
        'restricted' => 'Y',
        'created' => '2016-06-26 21:31:00',
        'updated' => '2016-06-26 21:31:00'
    ],
    [
        'title' => 'C Lorem ipsum dolor sit amet',
        'img' => 'img-1.jpg',
        'content' => '<p>C Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sed malesuada tortor, non sagittis risus. Cras congue gravida condimentum. Nunc sed tristique ante, ac dictum tellus. Quisque tincidunt metus felis, et malesuada nulla fringilla in. Aliquam erat volutpat. Morbi convallis rhoncus quam eget commodo. Aenean efficitur nec ex ut mollis. Aenean vehicula velit quam, vitae lacinia purus sollicitudin ornare.<br />Ut in pharetra libero. Suspendisse consectetur ante in venenatis porttitor. Maecenas sagittis molestie facilisis. Nam quis sem erat.
    <br />Donec quis elementum augue, nec congue turpis. Etiam ac sagittis nunc. Proin id magna quis libero euismod molestie sed quis ex. Nulla id eros sit amet lectus dictum fringilla ac quis risus. Nullam eu aliquam elit. Maecenas sed leo ut massa aliquet bibendum. Sed eu ultrices massa. Nam nec purus sed nibh pretium convallis. Morbi auctor quam et neque eleifend, id faucibus metus convallis. Etiam at est dapibus, sodales augue vitae, consectetur diam.</p>',
        'author' => 'Lorem Author C',
        'restricted' => 'N',
        'created' => '2016-06-26 21:32:00',
        'updated' => '2016-06-26 21:32:00'
    ],
];
foreach ($articles as $article) {
    $app['db']->insert('articles', $article);
}
