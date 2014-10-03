<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace model;

use core\Model;

class InvitationCode extends Model {

    const STATE_AVAILABLE = 0;

    const STATE_USED = 1;

    public function __construct() {
        $this->table = 'invitation_code';
    }

    public function generate($num) {
        $codes = array();
        for($i = 0; $i < $num; $i++) {
            $code = md5(time().uniqid(true));
            $data = array(
                'code' => $code,
                'state' => InvitationCode::STATE_AVAILABLE,
                'created' => date('Y-m-d H:i:s'),
            );
            $stmt = $this->getInsertStmt($data);
            $stmt->execute();
            $data['id'] = Model::PDO()->lastInsertId();
            $codes[] = $data;
        }
        return $codes;
    }
}