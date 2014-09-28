<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



namespace model;

use core\Model;

class Image extends Model {


    public function __construct() {
        $this->table = 'image';
    }

    public function insert($image) {
        $stmt = $this->getInsertStmt($user);
        $stmt->execute();
        $uid = Model::PDO()->lastInsertId();
        return $uid;
    }
}