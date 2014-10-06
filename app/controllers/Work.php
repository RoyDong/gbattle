<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


use core\Controller;

use model\Image;

class WorkController extends Controller {

    public function uploadAction() {
        $url = $this->get('rt', '/');
        $user = M('User')->current();
        if (!$user) {
            $this->redirect($url);
            return;
        }

        $tid = (int)$this->get('item');
        $item = M('Item')->find(['id' => $tid]);
        if (!$item) {
            $this->redirect($url);
            return;
        }

        if (isset($_FILES['image'])) {
            $file = $_FILES['image'];
            if ($file['error'] == 0) {
                $content = file_get_contents($file['tmp_name']);
                M('Work')->add((int)$user['id'], $tid, $content);
            }
        }

        $this->redirect($url);
    }
    
    public function showAction() {
        allow_or_go('user', '/');
        $wid = (int)$this->get('wid');

        $images = M('Image')->findBy(['work_id' => $wid]);
        $this->render('work/show', array(
            'images' => $images,
        ));
    }

    public function voteAction() {
        $wid = $this->get('wid');

        $request = $this->getRequest();
        if ($request->getMethod() == "POST") {
            $ip = $_SERVER['REMOTE_ADDR'];
            $work = M('Work')->vote($wid, $ip);
            if ($work) {
                $this->renderJson($work['vote']);
            } else {
                $this->renderJson($work['vote'], 'voted', 100);
            }
            return;
        }
        $images = M('Image')->findBy(array(
            'work_id' => $wid,
            'state' => Image::STATE_VERIFIED,
        ));
        $this->render('work/vote', array(
            'work' => M('Work')->get($wid),
            'images' => $images,
        ));
    }
}
