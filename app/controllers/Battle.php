<?php


use model\Base as Model;

class BattleController extends BaseController {

    public function listAction() {
        $pdo = Model::PDO();



        $this->render('battle/list', ['name' => 'Roy']);
    }
}