<?php


use core\Controller;

class Admin_ImageController extends Controller {


    public function listAction() {
        $user = M('User')->current();
        if (!$user || $user['id'] > 10) {
            $this->redirect('/');
            return;
        }

        $state = $this->get('state', -1);
        $page = (int)$this->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        $size = (int)$this->get('size', 100);
        if ($size < 1) {
            $size = 10;
        }

        if ($state >= 0) {
            $cond = array('state' => (int)$state);
        } else {
            $cond = null;
        }

        $images = M('Image')->findBy(null, $page, $size);

        $this->render('admin/image/list', array(
            'images' => $images,
            'page' => $page
        ));
    }

    public function generateCodeAction() {
        $user = M('User')->current();
        if (!$user || $user['id'] > 10) {
            $this->redirect('/');
            return;
        }

        $this->renderJson(M('InvitationCode')->generate(10));
    }

    public function verifyAction() {
        $id = (int)$this->get('id');
        $img = M('Image')->find(array('id' => $id));

        if (!$img) {
            $this->renderJson(null, 'image not found', 10);
            return;
        }


        $img = M('Image')->changeState($img);
        $this->renderJson(image_state($img));
    }
}