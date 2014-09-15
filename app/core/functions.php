<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function G($name) {
    return Yaf\Registry::get($name);
}

function S($name, $value) {
    Yaf\Registry::set($name, $value);
}