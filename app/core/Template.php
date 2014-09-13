<?php

namespace core;

use Yaf\View_Interface;
use core\Exception;


class Template implements View_Interface {

    const ACTION_BLOCK = 0;


    private $ext;

    private $dir;

    private $vars = array();

    private $content;

    private $blocks = array();

    private $child = true;

    public $layout;

    public function __construct($ext = 'phtml') {
        $this->ext = $ext;
        $this->vars['t'] = $this;
    }

    public function __get($name) {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }
    }

    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    public function getScriptPath() {
        return $this->dir;
    }

    public function setScriptPath($dir) {
        $this->dir = $dir;
    }

    public function getContent() {
        if ($this->layout) {
            $this->child = false;
            $this->render($this->layout);
        }
        return $this->content;
    }

    public function assign($name, $value = '') {
        $this->vars[$name] = $value;
    }

    public function display($tpl, $vars = array()) {
        $this->render($tpl, $vars);
        echo $this->content;
    }

    public function render($tpl, $vars = array()) {
        $this->vars = array_merge($this->vars, $vars);
        $this->renderTpl($tpl);

        if ($this->layout) {
            $this->child = false;
            $this->renderTpl($this->layout);
        }
    }

    private function renderTpl($tpl) {
        ob_start();
        extract($this->vars);
        require $this->dir . '/' . $tpl . '.' . $this->ext;
        if (count($this->block_names) > 0) {
            throw new Exception('some blocks not end');
        }
        $this->content = ob_get_contents();
        ob_end_clean();
    }

    public function contain($tpl, $vars = array()) {
        extract($this->vars);
        extract($vars);
        require $this->dir . '/' . $tpl . '.' . $this->ext;
    }

    private $block_names = array();

    private $actions = array();

    public function block($name) {
        array_push($this->block_names, $name);
        array_push($this->actions, self::ACTION_BLOCK);
        ob_start();
    }

    public function endBlock() {
        $name = array_pop($this->block_names);
        if ($name) {
            if ($this->child) {
                $content = trim(ob_get_contents());
                ob_end_clean();
                $this->blocks[$name] = $content;
            } else if (isset($this->blocks[$name])) {
                ob_end_clean();
                echo $this->blocks[$name];
            } else {
                ob_end_flush();
            }
            $this->cur_block = '';
        } else {
            throw new Exception('no starting block');
        }
    }

    public function end() {
        switch (array_pop($this->actions)) {
            case self::ACTION_BLOCK:
               $this->endBlock();
                break;
            default :
                throw new Exception('missing starting pair');
        }
    }
}
