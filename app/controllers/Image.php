<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use core\Controller;

class ImageController extends Controller {


    public function uploadAction() {
        if (isset($_FILES['image'])) {
            $file = $_FILES['image'];
            if ($file['error'] == 0 && $file['size'] <= 1024 * 1024 * 4) {
                $md5 = md5_file($file['tmp_name']);
                move_uploaded_file($file['tmp_name'], APP_PATH.'/public/img/'.$md5);
            }
        }
    }
}
