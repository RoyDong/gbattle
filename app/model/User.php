<?php


namespace model;

use Yaf\Application;
use Yaf\Session;
use core\Model;

class User extends Model {

    protected $security;

    protected $current;

    protected $errors = array();

    public function __construct() {
        $this->table = 'user';
        $this->security = Application::app()->getConfig()->security;
        if (empty($this->security)) {
            throw new Exception('security config not found');
        }
    }

    public function current() {
        if (empty($this->current)) {
            $session = Session::getInstance();
            $uid = (int)$session->get($this->security->session_key);
            if ($uid > 0){
                $user = Model::instance('User')->find(['id' => $uid]);
            }
            $user = $this->getUserFromCookie();
            if ($user) {
                $this->current = $user;
                if ($uid <= 0) {
                    $session->set($this->security->session_key, $user);
                }
            }
        }
        return $this->current;
    }

    public function getUserFromCookie() {
        $rmKey = $this->security->remember_me->key;
        if (isset($_COOKIE[$rmKey])) {
            $key = $_COOKIE[$rmKey];
            $info = explode('-', $key); 
            if (count($info) == 3) {
                $user = Model::instance('User')->find(['id' => (int)$info[2]]);
                $time = (int)$info[1];
                $duration = (int)$this->security->remember_me->duration;
                if ($user && $key === $this->createAuthKey($user, $time)
                        && $time + $duration >= time()) {

                    return $user;
                }
            }
        }
        return null;
    }

    public function createAuthKey($user, $expire) {
        return sha1($user['pass'].$user['salt'].$expire).'-'.$expire.'-'.$user['id'];
    }

    public function exists($name) {
        $user = Model::instance('User')->find(['name' => $name]);
        return $user ? 1 : 0;
    }

    public function signup($name, $pass, $code) {
        if ($this->exists($name)) {
            $this->errors[] = 'user exists';
            return false;
        }

        if (!$name) {
            $this->errors[] = 'name error';
            return false;
        }

        if (!$pass) {
            $this->errors[] = 'pass error';
            return false;
        }

        $code = $this->instance('InvitationCode')->find(array(
            'code' => $code,
            'state' => InvitationCode::STATE_AVAILABLE,
        ));

        if (!$code) {
            return false;
        }

        $time = new \DateTime;
        $salt = uniqid('', true);
        $user = array(
            'name' => $name,
            'pass' => sha1($salt.$pass),
            'salt' => $salt,
            'updated' => $time->format('Y-m-d H:i:s'),
            'created' => $time->format('Y-m-d H:i:s'),
        );

        $stmt = $this->getInsertStmt($user);
        $stmt->execute();
        $uid = Model::PDO()->lastInsertId();
        if ($uid) {
            $user['id'] = $uid;
            return $user;
        }

        $this->errors[] = 'server error';
        return false;
    }

    public function checkPass($user, $pass) {
        return $user && sha1($user['salt'].$pass) === $user['pass'];
    }


    public function signin($user) {
        $this->current = $user;
        Session::getInstance()->set($this->security->session_key, $user['id']);
    }

    public function rememberMe($user = null) {
        if (empty($user)) {
            $user = $this->current;
        }
        if ($user) {
            $time = time();
            $duration = (int)$this->security->remember_me->duration;
            setcookie($this->security->remember_me->key, 
                    $this->createAuthKey($user, $time), 
                    $time + $duration, '/', null, null, true);
        }
    }

    public function signout() {
        $this->current = null;
        Session::getInstance()->set($this->security->session_key, 0);
        setcookie($this->security->remember_me->key, '', 
                time() - 1, '/', null, null, true);
    }

    public function error() {
        return array_pop($this->errors);
    }

    private $role;

    public function isGranted($role) {
        if ($this->current()) {
            $this->role = 'user';
        }
        return $role === $this->role;
    }

    public function save($user) {
        $stmt = $this->getUpdateStmt($user, '`id` = ?', array($user['id']));
        $stmt->execute();
        return true;
    }

    public function findByIds($ids) {
        $sql = 'select * from `'.$this->table.'` where `id` in ('.implode(',', $ids).')';
        $stmt = Model::PDO()->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $return = array();
        foreach($users as $user) {
            $return[$user['id']] = $user;
        }
        return $return;
    }
}