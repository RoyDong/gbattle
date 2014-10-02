<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


namespace model;

use core\Model;

class Series extends Model {

    public function __construct() {
        $this->table = 'series';
    }

    public function allWithItem() {
        $series = $this->findAll(null, 100, 0);
        $items = $this->instance('Item')->findAll(null, 1000, 0);
        $all = array();
        foreach($series as $val) {
            $all[$val['id']] = array(
                'name' => $val['name'],
                'fullname' => $val['fullname'],
                'desc' => $val['desc'],
                'items' => array()
            );
}
        foreach($items as $val) {
            if (isset($all[$val['series_id']])) {
                $all[$val['series_id']]['items'][$val['id']] = $val;
            }
        }
        return $all;
    }

    public function all() {
        $series = $this->findAll(null, 100, 0);
        $all = array();
        foreach($series as $v) {
            $all[$v['id']] = $v;
        }
        return $all;
    }
}