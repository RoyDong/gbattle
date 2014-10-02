<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace model;

use core\Model;

class Item extends Model {

    public function __construct() {
        $this->table = 'item';
    }

    public function all() {
        $items = $this->instance('Item')->findAll(null, 1000, 0);
        $all = array();
        foreach($items as $item) {
            $all[$item['id']] = $item;
        }
        return $all;
    }
}