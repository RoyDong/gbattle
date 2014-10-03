<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



namespace model;

use core\Model;

class Work extends Model {

    public function __construct() {
        $this->table = 'work';
    }

    public function add($uid, $tid, $content) {
        $work = $this->find(['user_id' => $uid, 'item_id' => $tid]);
        if (!$work) {
            $work = array(
                'user_id' => $uid,
                'item_id' => $tid,
                'created' => date('Y-m-d H:i:s'),
                'updated' => date('Y-m-d H:i:s'),
            );

            $stmt = $this->getInsertStmt($work);
            $stmt->execute();
            $work['id'] = Model::PDO()->lastInsertId();
        }
        if ($work['id'] <= 0) {
            return false;
        }
        $img = $this->instance('Image')->save($content, $work['id']);
        if($img['id'] <= 0){
            return false;
        }
        if ($work['cover_image_id'] <= 0) {
            $work['cover_image_id'] = (int)$img['id'];
            $this->save($work);
        }
        return true;
    }

    public function save($work) {
        $stmt = $this->getUpdateStmt($work, '`id` = ?', [$work['id']]);
        $stmt->execute();
        return true;
    }

    public function works($uid) {
        $works = $this->findAll(['user_id' => $uid], 1000, 0);
        return $this->info($works);
    }

    public function all($page, $size = 100) {
        $works = $this->findAll(null, $size, ($page - 1) * $size);
        return $this->info($works);
    }

    public function get($wid) {
        $work = $this->find(['id' => $wid]);
        return $this->info(array($work))[0];
    }

    private function info($works) {
        $series = $this->instance('Series')->all();
        $items = $this->instance('Item')->all();

        $imgIds = array();
        $userIds = array();
        foreach($works as &$work) {
            $item = $items[$work['item_id']];
            $imgIds[] = $work['cover_image_id'];
            $userIds[] = $work['user_id'];
            $work['item'] = $item;
            $work['series'] = $series[$item['series_id']];
        }

        $images = $this->instance('Image')->findByIds($imgIds);
        foreach($works as &$work) {
            $work['cover'] = $images[$work['cover_image_id']];
        }

        $users = $this->instance('User')->findByIds($userIds);
        foreach($works as &$work) {
            $work['user'] = $users[$work['user_id']];
        }
        return $works;
    }

    public function vote($wid, $ip) {
        $ipVote = $this->instance('IpVote')->find(array(
            'work_id' => $wid,
            'ip' => ip2long($ip),
        ));

        if ($ipVote) {
            return false;
        } else {
            $this->instance('IpVote')->insert($wid, $ip, 0);
        }

        $sql = "update `{$this->table}` set vote = vote + 1 where id = ?";
        $stmt = Model::PDO()->prepare($sql);
        $stmt->bindValue(1, $wid);
        $stmt->execute();
        $work = $this->find(['id' => $wid]);
        return $work;
    }
}