<?php


namespace service;

use Yaf\Session;
use Yaf\Application;

use core\Exception;
use model\Base as Model;

class User {

    private static $instance;

    /**
     * 
     * @return User
     */
    public static function instance() {
        if (empty(User::$instance)) {
            User::$instance = new User;
        }
        return User::$instance;
    }


    protected $security;

    protected $current;

    protected $errors = array();

    public function __construct() {
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

    public function exists($email) {
        $user = Model::instance('User')->find(['email' => $email]);
        return $user ? 1 : 0;
    }

    public function signup($email, $pass) {
        if ($this->exists($email)) {
            $this->errors[] = 'user exists';
            return false;
        }

        if (!$email) {
            $this->errors[] = 'email error';
            return false;
        }

        if (!$pass) {
            $this->errors[] = 'pass error';
            return false;
        }

        $time = new \DateTime;
        $salt = uniqid('', true);
        $user = array(
            'email' => $email,
            'pass' => sha1($salt.$pass),
            'salt' => $salt,
            'updated' => $time->format('Y-m-d H:i:s'),
            'created' => $time->format('Y-m-d H:i:s'),
        );

        $uid = Model::instance('User')->insert($user);
        if ($uid) {
            unset($user['salt']);
            unset($user['pass']);
            $user['uid'] = $uid;
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
}