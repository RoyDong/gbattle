<?php


namespace model;

class User extends Base {



    public function __construct() {
        $this->table = 'user';
    }

    public function insert($user) {
        $stmt = $this->getInsertStmt($user);
        $stmt->execute();
        $uid = Base::PDO()->lastInsertId();
        return $uid;
    }
}