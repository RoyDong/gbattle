<?php


use model\Base as Model;

class UserController extends BaseController {

    public function signupAction() {
        $email = $this->get('email');
        $pass = $this->get('pass');

        $userService = \service\User::instance();
        if ($userService->signup($email, $pass)) {
            $userService->rememberMe();
            $this->redirect('/home');
        } else {
            $this->redirect('/');
        }
    }

    public function signinAction() {
        $email = $this->get('email');
        $pass = $this->get('pass');

        $user = Model::instance('User')->find(['email' => $email]);
        if (!$user) {
            $this->redirect('/');
            return;
        }

        $userService = \service\User::instance();
        if ($userService->checkPass($user, $pass)) {
            $userService->signin($user);
            $userService->rememberMe();
            $this->redirect('/home');
        } else {
            $this->redirect('/');
        }
    }

    public function signoutAction() {
        service\User::instance()->signout();
        $this->redirect('/');
    }

    public function homeAction() {
        allow_or_go('user', '/');
        $user = \service\User::instance()->current();
        $this->render('user/home', array('user' => $user));
    }
}