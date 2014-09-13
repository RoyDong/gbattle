<?php

namespace service;

use Yaf\View_Interface as ViewInterface;


class Template implements ViewInterface {

    protected $ext;

    protected $dir;

    protected $vars;

    protected $content;

    public function __construct($ext = 'phtml', $vars = array()) {
        $this->ext = $ext;
        $this->vars = $vars;
    }

    public function __get($name) {
        return $this->vars[$name];
    }

    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    public function assign($name, $value = '') {
        $this->vars[$name] = $value;
    }

    public function display($tpl, $vars = array()) {
        echo $this->render($tpl, $vars);
    }

    public function render($tpl, $vars = array()) {
        ob_start();
        extract($this->vars);
        extract($vars);
        require $this->dir . '/' . $tpl . '.' . $this->ext;
        $this->content = ob_get_contents();
        ob_end_clean();

        return $this->content;
    }

    public function getScriptPath() {
        return $this->dir;
    }

    public function setScriptPath($dir) {
        $this->dir = $dir;
    }

    public function getContent() {
        return $this->content;
    }
}
