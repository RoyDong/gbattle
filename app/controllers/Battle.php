<?php


class BattleController extends BaseController {

    public function listAction() {
        $this->render('battle/list', ['name' => 'Roy']);
    }
}