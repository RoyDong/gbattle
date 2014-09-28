<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



namespace model;

use core\Model;

class Avatar extends Model {


    public function __construct() {
        $this->table = 'avatar';
    }

    public function insert($md5, $uid, $ext) {

    }
}