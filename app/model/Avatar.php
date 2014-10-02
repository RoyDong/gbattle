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

    public function save($content, $uid) {
        $sha1 = sha1($content);
        $img = $this->find(array('sha1' => $sha1));
        if ($img) {
            return $img;
        }
        try {
            $imagic = new \Imagick;
        } catch (Exception $e) {
            return false;
        }
        $imagic->readimageblob($content);
        $ext = strtolower($imagic->getimageformat());
        if (!$this->isAllowedFormat($ext)) {
            return false;
        }
        file_put_contents(APP_PATH . '/public/img/avatar/'. $sha1 .'.'.$ext, $content);
        $img = array(
            'user_id' => $uid,
            'sha1' => $sha1,
            'ext' => $ext,
            'created' => date('Y-m-d H:i:s', time()),
        );
        $stmt = $this->getInsertStmt($img);
        $stmt->execute();
        $img['id'] = Model::PDO()->lastInsertId();
        return $img;
    }

    public function isAllowedFormat($ext) {
        return in_array($ext, array('jpeg', 'png', 'jpg'));
    }

    public function getUrl($id) {
        $img = $this->find(array('id' => $id));
        return '/img/avatar/' . $img['sha1'] . '.' . $img['ext'];
    }
}