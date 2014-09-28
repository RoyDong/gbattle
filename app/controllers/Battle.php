<?php

use core\Controller;

class BattleController extends Controller {

    public function listAction() {
        $this->render('battle/list', ['name' => 'Roy']);
    }
}