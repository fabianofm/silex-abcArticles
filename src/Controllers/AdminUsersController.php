<?php
/**
 * Silex - abcArticles: A small web app.
 *
 * @author Fabiano Monteiro <fabianophp@hotmail.com>
 *
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


$app->get('/admin', function () use ($app) {

    $users = $app['db']->fetchAll("SELECT * FROM users ORDER BY id DESC");
    //the form
    return $app['twig']->render('pages/admin.html.twig', array(
        'users' => $users,
    ));
})->bind('admin');

#ADMIN - ADD USER
$app->match('/admin/add/user', function (Request $request) use ($app) {

    $msgi = array();
    //Create form widget
    $form = $app['form.factory']->createBuilder('form', $data)
        ->add('name', 'text', array(
            'label' => 'Nome: ',
            'attr' => array('class' => 'form-control', 'placeholder' => 'Your name'),
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 4)))
        ))
        ->add('password', 'password', array(
            'label' => 'Password: ',
            'attr' => array('class' => 'form-control', 'placeholder' => 'Your password'),
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 6)))
        ))
        ->add('admin', 'checkbox', array(
            'label' => 'Admin?',
            'required' => false,
        ))->getForm();

    //Post
    if ('POST' == $request->getMethod()) {
        $form->bind($request);
        //$data = array('admin' => false);

        if ($form->isValid()) {
            $data = $form->getData();
            $password = $app['security.encoder.digest']->encodePassword($data['password'], '');
            $role = ($data['admin'] == true) ? 'ROLE_ADMIN' : 'ROLE_USER';

            try {
                $app['db']->insert('users', array(
                    'username' => $data['name'],
                    'password' => $password,
                    'roles' => $role
                ));
                //return $app->redirect('/admin');
                $msgi[] = "Added: " . $data['name'];

            } catch (Exception $e) {
                $msgi[] = "Sorry the user already exists on this system.";
            }
        }
    }
    //the form
    return $app['twig']->render('pages/admin-users.html.twig', array(
        'form' => $form->createView(),
        'msgi' => $msgi,
    ));
})->bind('add-user');
#END/ADMIN - ADD USER
#ADMIN - UPDATE USER
$app->match('/admin/update/user/{id}', function (Request $request, $id) use ($app) {

    $msgi = array();

    $select_user = $app['db']->fetchAssoc("SELECT username,roles FROM users WHERE id = ?", array((int)$id));
    $data = array(
        'name' => $select_user['username'],
        'admin' => ($select_user['roles'] == "ROLE_ADMIN") ? true : false,
    );
    //Create update form widget
    $form_update = $app['form.factory']->createBuilder('form', $data)
        ->add('name', 'text', array(
            'label' => 'Nome: ',
            'attr' => array('class' => 'form-control', 'placeholder' => 'Your name'),
            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 4)))
        ))
        ->add('password', 'password', array(
            'label' => 'Password: ',
            'attr' => array('class' => 'form-control', 'placeholder' => 'Keep your password'),
            'required' => false,
        ))
        ->add('admin', 'checkbox', array(
            'label' => 'Admin?',
            'required' => false,
        ))->getForm();

    //Post Update
    if ('POST' == $request->getMethod()) {
        $form_update->bind($request);

        if ($form_update->isValid()) {
            $data = $form_update->getData();

            $role = ($data['admin'] == true) ? 'ROLE_ADMIN' : 'ROLE_USER';

            try {
                if (empty($data['password'])) {
                    $sql = "UPDATE users SET username = ?, roles = ? WHERE id = ?";
                    $app['db']->executeUpdate($sql, array($data['name'], $role, (int)$id));
                } else {
                    $password = $app['security.encoder.digest']->encodePassword($data['password'], '');
                    $sql = "UPDATE users SET username = ?, password = ?, roles = ? WHERE id = ?";
                    $app['db']->executeUpdate($sql, array($data['name'], $password, $role, (int)$id));
                }
                throw new Exception("Updated: " . $select_user['username']);
            } catch (Exception $e) {
                $msgi[] = $e->getMessage();
            }
        }
    }
    //the form
    return $app['twig']->render('pages/admin-users.html.twig', array(
        'form' => $form_update->createView(),
        'msgi' => $msgi,
    ));
})->assert('id', '\d+')
    ->bind('update-user');
#END/ADMIN - UPDATE USER
#ADMIN - REMOVE USER
$app->match('/admin/remove/user/{id}-{name}', function ($id, $name) use ($app) {

    $msgi = array();

    try {
        //Is Admin
        if ($id == 1) {
            throw new Exception("Error Processing Request: Deletion not allowed.");
        } else {
            $sql = "DELETE FROM users WHERE id = ?";
            $app['db']->executeUpdate($sql, array($id));

            throw new Exception("Removed: " . $name);
            return $app->redirect('/admin');
        }
    } catch (Exception $e) {
        $msgi[] = $e->getMessage();
    }
    return $app['twig']->render('pages/admin-users.html.twig', array(
        'msgi' => $msgi,
    ));
})->assert('id', '\d+')
    ->bind('remove-user');
#END/ADMIN - REMOVE USER
