<?php


namespace model;


class Base {
    /**
     * mysql database connection
     */

    /**
     * data table used as table table or cache key prefix
     * @var string
     */
    private static $pdo;


    /**
     * 
     * @return \PDO
     */
    public static function PDO() {
        if (empty(Base::$pdo)) {
            $db = \Yaf\Application::app()->getConfig()->db;
            if (!$db) {
                throw new Exception('db config not found');
            }
            Base::$pdo = new \PDO($db->get('dsn'), $db->get('user'), $db->get('pass'));
        }
        return Base::$pdo;
    }

    protected static $models = [];

    public static function instance($name) {
        if (empty(Base::$models[$name])) {
            $class = '\model\\' . $name;
            Base::$models[$name] = new $class;
        }
        return Base::$models[$name];
    }


    private static $stmts = array();

    public static function buffer($stmt) {
        Base::$stmts[] = $stmt;
    }

    public static function flush() {
        foreach(Base::$stmts as $stmt) {
            $stmt->execute();
        }
    }

    protected $table;

    protected function getInsertStmt($data) {
        $cols = array();
        $vals = array();
        $placeHolders = array();
        foreach ($data as $col => $val) {
            $cols[] = $col;
            $vals[] = $val;
            $placeHolders[] = '?';
        }
        $sql = 'INSERT INTO `' . $this->table
                . '` (`' . implode('`,`', $cols) . '`) VALUES ('
                . implode(',', $placeHolders) . ')';
        $stmt = Base::PDO()->prepare($sql);
        foreach($vals as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }
        return $stmt;
    }

    protected function getUpdateStmt($data, $where, $params = array()) {
        $vals = array();
        $sql = 'UPDATE `' . $this->table . '` SET ';
        foreach ($data as $col => $val) {
            $sql .= '`' . $col . '`= ? ,';
            $vals[] = $val;
        }
        $sql[strlen($sql) - 1] = ' ';
        $stmt = Base::PDO()->prepare($sql . 'WHERE ' . $where);
        foreach(array_merge($vals, $params) as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }
        return $stmt;
    }

    public function find($cond) {
        $cols = array();
        $vals = array();
        foreach($cond as $key => $val) {
            $cols[] = "`$key` = ?";
            $vals[] = $val;
        }

        $sql = "SELECT * FROM `{$this->table}` WHERE " . implode(' AND ', $cols);
        $stmt = Base::PDO()->prepare($sql);
        foreach($vals as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }

        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public function findAll($cond, $limit, $offset) {
        $cols = array();
        $vals = array();
        foreach($cond as $key => $val) {
            $cols[] = "`$key` = ?";
            $vals[] = $val;
        }

        $sql = "SELECT * FROM `{$this->table}` WHERE " .
                implode(' AND ', $cols) . " limit $offset, $limit";
        $stmt = Base::PDO()->prepare($sql);
        foreach($vals as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
