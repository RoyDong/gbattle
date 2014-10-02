<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use core\Controller;

class ImageController extends Controller {


    public function uploadWorkAction() {
        $url = $this->get('rt', '/');

        if (isset($_FILES['image'])) {
            $file = $_FILES['image'];
            if ($file['error'] == 0) {
                $tid = $this->get('item');
                $content = file_get_contents($file['tmp_name']);
                $user = M('User')->current();
                M('Image')->save($content, $user['id'], $tid);
            }
        }

        //$this->redirect($url);
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
