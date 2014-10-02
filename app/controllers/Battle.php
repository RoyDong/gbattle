<?php

use core\Controller;

class BattleController extends Controller {

    public function listAction() {
        $page = (int)$this->get('page');
        if ($page < 1) {
            $page = 1;
        }

        $this->render('battle/list', array(
            'works' => M('Work')->all($page),
        ));
    }
}