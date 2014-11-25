<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * functions shuld be in root namespace
 */

namespace {

    use core\Model;
    use Yaf\Registry;

    function G($name) {
        return Registry::get($name);
    }

    function S($name, $value) {
        Registry::set($name, $value);
    }

    function M($name) {
        return Model::instance($name);
    }
}

namespace core {

    use Yaf\Controller_Abstract;

    class Controller extends Controller_Abstract {

        protected $yafAutoRender = false;

        public function get($name, $default = null) {
            $val = $this->getRequest()->getParam($name);
            if ($val != null) {
                return $val;
            }
            if (isset($_POST[$name])) {
                return $_POST[$name];
            }
            if (isset($_GET[$name])) {
                return $_GET[$name];
            }
            return $default;
        }

        public function render($tpl, array $vars = array()) {
            $view = $this->getView();
            $view->render($tpl, $vars);
            $this->getResponse()->setBody($view->getContent());
        }

        public function renderJson($data = null, $msg = 'ok', $code = 0) {
            $json = json_encode(['msg' => $msg, 'code' => $code, 'data' => $data], JSON_UNESCAPED_UNICODE);
            header('Content-Type: application/json');
            $this->getResponse()->setBody($json);
        }

    }

    class Model {
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
            if (empty(Model::$pdo)) {
                $db = G('db');
                if (!$db) {
                    throw new Exception('db config not found');
                }
                Model::$pdo = new \PDO($db->get('dsn'), $db->get('user'), $db->get('pass'));
            }
            return Model::$pdo;
        }

        protected static $models = [];

        public static function instance($name) {
            if (empty(Model::$models[$name])) {
                $class = '\model\\' . $name;
                Model::$models[$name] = new $class;
            }
            return Model::$models[$name];
        }

        private static $stmts = array();

        public static function buffer($stmt) {
            Model::$stmts[] = $stmt;
        }

        public static function flush() {
            foreach (Model::$stmts as $stmt) {
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
            $stmt = Model::PDO()->prepare($sql);
            foreach ($vals as $i => $val) {
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
            $stmt = Model::PDO()->prepare($sql . 'WHERE ' . $where);
            foreach (array_merge($vals, $params) as $i => $val) {
                $stmt->bindValue($i + 1, $val);
            }
            return $stmt;
        }

        public function find($cond) {
            $cols = array();
            $vals = array();
            foreach ($cond as $key => $val) {
                $cols[] = "`$key` = ?";
                $vals[] = $val;
            }

            $sql = "SELECT * FROM `{$this->table}` WHERE " . implode(' AND ', $cols);
            $stmt = Model::PDO()->prepare($sql);
            foreach ($vals as $i => $val) {
                $stmt->bindValue($i + 1, $val);
            }

            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function findAll($cond, $limit, $offset) {
            $cols = array();
            $vals = array();
            $sql = "SELECT * FROM `{$this->table}`";
            if (count($cond) > 0) {
                foreach ($cond as $key => $val) {
                    $cols[] = "`$key` = ?";
                    $vals[] = $val;
                }
                
                $sql = "$sql WHERE " . implode(' AND ', $cols);
            }

            $sql = "$sql limit $offset, $limit";
            $stmt = Model::PDO()->prepare($sql);
            foreach ($vals as $i => $val) {
                $stmt->bindValue($i + 1, $val);
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
}
