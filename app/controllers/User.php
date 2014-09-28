<?php

use core\Controller;

class UserController extends Controller {

    public function signupAction() {
        $name = $this->get('name');
        $pass = $this->get('pass');
        $url = $this->get('rt', '/');

        $userModel = $this->model('User');
        if ($user = $userModel->signup($name, $pass)) {
            $userModel->signin($user);
            $userModel->rememberMe();
        }

        $this->redirect($url);
    }

    public function signinAction() {
        $name = $this->get('name');
        $pass = $this->get('pass');
        $url = $this->get('rt', '/');

        $user = Model::instance('User')->find(['name' => $name]);
        if ($user) {
            $userService = \service\User::instance();
            if ($userService->checkPass($user, $pass)) {
                $userService->signin($user);
                $userService->rememberMe();
                $this->redirect('/home');
            }
        }

        $this->redirect($url);
    }

    public function signoutAction() {
        $this->model('User')->signout();
        $this->redirect('/');
    }

    public function homeAction() {
        allow_or_go('user', '/');
        $user = $this->model('User')->current();
        $this->render('user/home', array('user' => $user));
    }

    public function editAction() {
        allow_or_go('user', '/');
    }
}