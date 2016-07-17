<?php
/**
 * Silex - abcArticles: A small web app
 *
 * @author Fabiano Monteiro <fabianophp@hotmail.com>
 *
 */
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\FormServiceProvider;

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../resources/logs/app.log',
    'monolog.name' => 'abcArticles',
    'monolog.level' => 300 //Logger::WARNING
));
//$app['twig.options.cache'] = __DIR__ . '/../resources/cache/twig';
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.options' => array(
        'cache' => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true),
    'debug' => true,
    'twig.path' => __DIR__ . '/../web/views',
));
$app['twig']->addExtension(new Twig_Extensions_Extension_Text()); //for use truncante, etc
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'dbname' => 'abc_articles',
        'user' => 'root', 'password' => '',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
    ),
));
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'login_path' => array(
            'pattern' => '^/login$',
            'anonymous' => true),
        'default' => array(
            'pattern' => '^/.*$',
            'anonymous' => true,
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/login_check',),
            'logout' => array(
                'logout_path' => '/logout',
                'invalidate_session' => false),
            'users' => $app->share(function ($app) {
                return new UserProvider($app['db']);
            }),
        )
    ),
    'security.access_rules' => array(
        array('^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/restricted.*$', 'ROLE_USER'), // a simple example of restricted path to registered users
        array('^/admin.*$', 'ROLE_ADMIN'), //array('^/admin', 'ROLE_ADMIN', 'https'),
    ),
    'security.role_hierarchy' => array(
        // Admin is a user: Defining a role hierarchy allows to automatically grant users some additional roles.
        // Definindo uma hierarquia de funções permite conceder automaticamente os usuários algumas funções adicionais.
        'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'),
    )
));

$schema = $app['db']->getSchemaManager();
if (!$schema->tablesExist('users')) {
    require __DIR__ . '/../resources/db/schema.php';
}

