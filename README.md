Silex - abcArticles: A small web app.
============================

This small web app with access control, is a project sample or a bootstrap [silex](http://silex.sensiolabs.org/) application.
Admin can add users and restricted or public articles.

Installation
------------
    -   curl -sS https://getcomposer.org/installer | php
        php composer.phar install
    -   db.options (src/App.php) - Change the database name, username and password.
    -   $app['twig.options.cache'] (src/App.php) - To enable caching, uncomment.
    -	Create and give permission to folders: 'resources/logs' and 'resources/cache/twig'
    -   To use the TinyMCE editor in textarea field, uncompress tinymce 4.4 * in 'web/assets/js/tinymce' 
    and uncomment the JS code(tinyMCE.init) of base.html.twig
    -   The structure of the tables are in the file (resources/db/schema.php). 
    They will be created automatically when running for the first time the application.

