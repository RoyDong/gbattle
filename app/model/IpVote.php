<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace model;

use core\Model;

class IpVote extends Model {

    public function __construct() {
        $this->table = 'ip_vote';
    }


    public function insert($wid, $ip, $uid) {
        $data = array(
            'work_id' => $wid,
            'ip' => ip2long($ip),
            'user_id' => $uid,
            'created' => date('Y-m-d H:i:s'),
        );
        $stmt = $this->getInsertStmt($data);
        $stmt->execute();
        $data['id'] = Model::PDO()->lastInsertId();
        return $data;
    }
}