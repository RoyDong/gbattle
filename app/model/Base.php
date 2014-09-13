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
    protected $table;
    protected $entities = [];
    private static $pdo;
    private static $sqlBuffer = [];

    public static function buffer($sql) {
        Base::$sqlBuffer[] = $sql;
    }

    public static function flush() {
        if (count(Base::$sqlBuffer)) {
            Base::getPdo()->exec(implode(';', Base::$sqlBuffer) . ';');
            Base::$sqlBuffer = [];
        }
    }

    /**
     * 
     * @return \PDO
     */
    protected static function getPdo() {
        if (!Base::$pdo) {
            $db = \Yaf\Registry::get('db');
            Base::$pdo = new \PDO($db->get('dsn'), $db->get('user'), $db->get('passwd'));
        }

        return Base::$pdo;
    }

    protected static $models = [];

    public static function getInstance($name) {
        if (empty(Base::$models[$name])) {
            $class = '\model\\' . $name;
            Base::$models[$name] = new $class;
        }

        return Base::$models[$name];
    }

    protected function setEntity($key, $entity) {
        $this->entities[$key] = $entity;
    }

    protected function getEntity($key) {
        return isset($this->entities[$key]) ? $this->entities[$key] : null;
    }

    /**
     * insert data
     * @param array $data
     * @return int 
     */
    public function insert($data, $buffer = false) {
        $sql = $this->getInsertSql($data);

        if ($buffer) {
            Base::buffer($sql);

            return 0;
        }

        Base::getPdo()->exec($sql);

        return Base::getPdo()->lastInsertId();
    }

    protected function getInsertSql($data) {
        $columns = $values = array();

        foreach ($data as $column => $value) {
            $columns[] = $column;
            $values[] = $value;
        }

        return 'insert into `' . $this->table
                . '` (`' . implode('`,`', $columns) . '`) values ("'
                . implode('","', $values) . '")';
    }

    /**
     * update data 
     * @param array $data
     * @param string $where
     */
    public function update($data, $where) {
        Base::buffer($this->getUpdateSql($data, $where));
    }

    protected function getUpdateSql($data, $where) {
        $sql = 'UPDATE `' . $this->table . '` SET ';

        foreach ($data as $column => $value)
            $sql .= '`' . $column . '`="' . $value . '",';

        $sql[strlen($sql) - 1] = ' ';

        return $sql . 'WHERE ' . $where;
    }

    protected function delete($where, $limit = '0,1') {
        $sql = 'delete * from `' . $this->table . '` where ' . $where . ' ' . $limit;
        Base::buffer($sql);
    }

    public function fetch($criteria) {
        $sql = 'select * from `' . $this->table . '` where 1=1';
        foreach ($criteria as $key => $value)
            $sql .= " and `$key`='$value'";
        $sql .= ' limit 0,2';

        return Base::getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAll($criteria, $order = null, $limit = 0, $offset = 0) {
        $sql = 'select * from `' . $this->table . '` where 1=1';

        if ($criteria)
            foreach ($criteria as $key => $value)
                $sql .= " and `$key`='$value'";

        if (is_array($order)) {
            list($column, $value) = each($order);
            $sql .= " order by $column $value";
        }

        if ($limit)
            $sql .= ' limit ' . $offset . ', ' . $limit;

        return Base::getPdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function count($where = '1=1') {
        $sql = 'select count(*) c from `' . $this->table . '` where ' . $where;

        return Base::getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC)['c'];
    }
}
