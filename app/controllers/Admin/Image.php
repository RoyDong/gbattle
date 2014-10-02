<?php


use core\Controller;

class Admin_ImageController extends Controller {


    public function listAction() {
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

        $images = M('Image')->findByWid(0, $page, $size);

        $this->render('admin/image/list', array(
            'images' => $images,
            'page' => $page
        ));
    }

    public function generateCodeAction() {
        $this->renderJson(M('InvitationCode')->generate(10));
    }
}