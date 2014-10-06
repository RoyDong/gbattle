<?php

use core\Controller;

class UserController extends Controller {

    public function signupAction() {
        $name = $this->get('name');
        $pass = $this->get('pass');
        $code = $this->get('code');
        $url = $this->get('rt', '/');

        $userModel = M('User');
        if ($user = $userModel->signup($name, $pass, $code)) {
            $userModel->signin($user);
            $userModel->rememberMe();
        }

        $this->redirect($url);
    }

    public function signinAction() {
        $name = $this->get('name');
        $pass = $this->get('pass');
        $url = $this->get('rt', '/');

        $user = M('User')->find(['name' => $name]);
        if ($user) {
            $userModel = M('User');
            if ($userModel->checkPass($user, $pass)) {
                $userModel->signin($user);
                $userModel->rememberMe();
            }
        }
        $this->redirect($url);
    }

    public function signoutAction() {
        M('User')->signout();
        $this->redirect('/');
    }

    public function homeAction() {
        allow_or_go('user', '/');
        $user = M('User')->current();
        $user['avatar'] = M('Avatar')->getUrl($user['avatar_id']);
        $images = array();
        for($i = 0; $i < 30; $i++) {
            $images[] = $user['avatar'];
        }
        $this->render('user/home', array(
            'user' => $user,
            'images' => $images,
            'series' => M('Series')->allWithItem(),
            'works' => M('Work')->works($user['id']),
        ));
    }

    public function editAction() {
        allow_or_go('user', '/');
    }

    public function uploadAvatarAction() {
        $url = $this->get('rt', '/');

        if (isset($_FILES['image'])) {
            $file = $_FILES['image'];
            if ($file['error'] == 0) {
                $content = file_get_contents($file['tmp_name']);
                $user = M('User')->current();
                $img = M('Avatar')->save($content, $user['id']);
                if ($img) {
                    $user['avatar_id'] = $img['id'];
                    M('User')->save($user);
                }
            }
        }

        $this->redirect($url);
    }
}
