<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function name_or_email($user) {
    return empty($user['name']) ? $user['email'] : $user['name'];
}


function allow_or_go($role, $url) {
    if (!G('srv/user')->isGranted($role)) {
        header('Location: '.$url);
        exit;
    }
}