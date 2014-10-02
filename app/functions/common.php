<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


use model\Image;

function name_or_email($user) {
    return empty($user['name']) ? $user['email'] : $user['name'];
}


function allow_or_go($role, $url) {
    if (!M('User')->isGranted($role)) {
        header('Location: '.$url);
        exit;
    }
}

function work_image($image) {
    return '/img/'.$image['md5'].'.'.$image['ext'];
}

function avatar($avatar) {
    return '/img/'.$avatar['md5'].'.'.$avatar['ext'];
}

function image_state($image) {
    if ($image['state'] == Image::STATE_UNVERIFIED) {
        return '未审核';
    }
    if ($image['state'] == Image::STATE_VERIFIED) {
        return '已审核';
    }
}