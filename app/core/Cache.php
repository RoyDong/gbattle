<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



class Cache {

    const CACHE_MEM = 0;

    const CACHE_REDIS = 1;

    public static function instance($name = Cache::CACHE_FILE) {

    }
}