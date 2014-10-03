<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



namespace model;

use core\Model;

class Image extends Model {

    const STATE_UNVERIFIED = 0;

    const STATE_VERIFIED = 1;


    public function __construct() {
        $this->table = 'image';
    }

    public function save($content, $wid) {
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
        file_put_contents(APP_PATH . '/public/img/work/'. $sha1 .'.'.$ext, $content);
        $img = array(
            'work_id' => $wid,
            'sha1' => $sha1,
            'ext' => $ext,
            'created' => date('Y-m-d H:i:s', time()),
        );
        $stmt = $this->getInsertStmt($img);
        $stmt->execute();
        $img['id'] = Model::PDO()->lastInsertId();
        return $img;
    }

    public function changeState($img) {
        $img['state'] = 1 - $img['state'];
        $stmt = $this->getUpdateStmt($img, '`id` = ?', array($img['id']));
        $stmt->execute();
        return $img;
    }

    public function isAllowedFormat($ext) {
        return in_array($ext, array('jpeg', 'png', 'jpg'));
    }

    public function getUrl($id) {
        $img = $this->find(array('id' => $id));
        return $this->url($img);
    }

    public function url($img) {
        return '/img/work/' . $img['sha1'] . '.' . $img['ext'];
    }

    public function findByIds($ids) {
        $sql = 'select * from `'.$this->table.'` where `id` in ('.implode(',', $ids).')';
        $stmt = Model::PDO()->prepare($sql);
        $stmt->execute();
        $imgs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $return = array();
        foreach($imgs as $img) {
            $img['url'] = $this->url($img);
            $return[$img['id']] = $img;
        }
        return $return;
    }

    public function findBy($cond, $page = 1, $size = 20) {
        $images = $this->findAll($cond, $size, ($page - 1) * $size);
        foreach($images as &$image) {
            $image['url'] = $this->url($image);
        }
        return $images;
    }
}