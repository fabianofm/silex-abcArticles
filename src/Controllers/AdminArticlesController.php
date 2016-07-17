<?php
/**
 * Silex - abcArticles: A small web app.
 *
 * @author Fabiano Monteiro <fabianophp@hotmail.com>
 *
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

#ADD ARTICLE
$app->match('/admin/add/article', function (Request $request) use ($app) {
    $msgi = array();
    //Create form widget
    $form = $app['form.factory']->createBuilder('form')
        ->add('title', 'text', array(
            'label' => 'Title: ',
            'attr' => array('class' => 'form-control', 'placeholder' => ''),
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('max' => 90)))
        ))
        ->add('img', 'text', array(
            'label' => 'Image: ',
            'attr' => array('class' => 'form-control', 'placeholder' => 'e.g.: img-1.jpg Put image in \'web/assets/img\''),
            'constraints' => array(new Assert\Length(array('max' => 50))),
            'required' => false,
        ))
        ->add('content', 'textarea', array(
            'label' => 'Content: ',
            'attr' => array('class' => 'form-control', 'placeholder' => ''),
            'constraints' => array(new Assert\NotBlank())
        ))
        ->add('author', 'text', array(
            'label' => 'Author: ',
            'attr' => array('class' => 'form-control', 'placeholder' => ''),
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('max' => 30)))
        ))
        ->add('created', 'datetime', array(
            'data' => new \DateTime(),
        ))
        ->add('restricted', 'checkbox', array(
            'label' => 'Restricted?',
            'required' => false,
        ))->getForm();

    // Post
    if ('POST' == $request->getMethod()) {
        $form->bind($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $restricted = ($data['restricted'] == true) ? 'Y' : 'N';

            try {
                $app['db']->insert('articles', array(
                    'title' => $data['title'],
                    'img' => $data['img'],
                    'content' => $data['content'],
                    'author' => $data['author'],
                    'created' => date_format($data['created'], "Y-m-d H:i:s"),
                    'restricted' => $restricted
                ));
                //return $app->redirect('admin');
                $msgi[] = "Added: " . $data['title'];

            } catch (Exception $e) {
                $msgi[] = $e->getMessage();
            }
        }
    }
    //the form
    return $app['twig']->render('pages/admin-articles.html.twig', array(
        'form' => $form->createView(),
        'msgi' => $msgi,
    ));
})->bind('add-article');
#END/ADD ARTICLE

#UPDATE ARTICLE
$app->match('/admin/update/article/{id}', function (Request $request, $id) use ($app) {
    $msgi = array();

    $select_article = $app['db']->fetchAssoc("SELECT * FROM articles WHERE id = ?", array((int)$id));
    $data = array(
        'title' => $select_article['title'],
        'img' => $select_article['img'],
        'content' => $select_article['content'],
        'author' => $select_article['author'],
        'updated' => $select_article['updated'],
        'restricted' => ($select_article['restricted'] == "Y") ? true : false,
    );

    //Create form widget
    $form_update = $app['form.factory']->createBuilder('form', $data)
        ->add('title', 'text', array(
            'label' => 'Title: ',
            'attr' => array('class' => 'form-control', 'placeholder' => ''),
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('max' => 60)))
        ))
        ->add('img', 'text', array(
            'label' => 'Image: ',
            'attr' => array('class' => 'form-control', 'placeholder' => ''),
            'constraints' => array(new Assert\Length(array('max' => 50))),
            'required' => false,
        ))
        ->add('content', 'textarea', array(
            'label' => 'Content: ',
            'attr' => array('class' => 'form-control', 'placeholder' => ''),
            //'constraints' => array(new Assert\NotBlank())
        ))
        ->add('author', 'text', array(
            'label' => 'Author: ',
            'attr' => array('class' => 'form-control', 'placeholder' => ''),
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('max' => 30)))
        ))
        ->add('updated', 'datetime', array(
            'data' => new \DateTime(),
        ))
        ->add('restricted', 'checkbox', array(
            'label' => 'Restricted?',
            'required' => false,
        ))->getForm();

    //Post
    if ('POST' == $request->getMethod()) {
        $form_update->bind($request);

        if ($form_update->isValid()) {
            $data = $form_update->getData();

            $restricted = ($data['restricted'] == true) ? 'Y' : 'N';

            try {
                $sql = "UPDATE articles
                SET title=?,img=?,content=?,author=?,updated=?,restricted=?
                WHERE id = ?";
                $app['db']->executeUpdate($sql,
                    array(
                        $data['title'],
                        $data['img'],
                        $data['content'],
                        $data['author'],
                        date_format($data['updated'], "Y/m/d H:i:s"),
                        $restricted,
                        (int)$id)
                );
                //return $app->redirect('/admin');
                $msgi[] = "Updated: " . $data['title'];

            } catch (Exception $e) {
                $msgi[] = $e->getMessage();
                $app['monolog']->addDebug(sprintf('Caught Error: %s', $e->getMessage()));
            }
        }
    }
    //the form
    return $app['twig']->render('pages/admin-articles.html.twig', array(
        'form_update' => $form_update->createView(),
        'msgi' => $msgi,
    ));
})->assert('id', '\d+')
    ->bind('update-article');
#END/UPDATE ARTICLE
#REMOVE ARTICLE
$app->match('/admin/remove/article/{id}-{title}', function ($id, $title) use ($app) {

    $msgi = array();
    try {
        $sql = "DELETE FROM articles WHERE id = ?";
        $app['db']->executeUpdate($sql, array($id));

        $msgi[] = "Removed: " . $title;

        //return $app->redirect('/admin');
    } catch (Exception $e) {
        $msgi[] = $e->getMessage();
    }
    return $app['twig']->render('pages/admin-articles.html.twig', array(
        'msgi' => $msgi,
    ));
})->assert('id', '\d+')
    ->bind('remove-article');
#END/REMOVE ARTICLE
