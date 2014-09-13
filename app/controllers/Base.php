<?php

class BaseController extends Yaf\Controller_Abstract {

    protected $yafAutoRender = false;

    protected $layout = '';

    public function get($name, $default = null) {
        $val = $this->getRequest()->getParam($name, $default);
        if ($val != null) {
            return $val;
        }
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return $default;
    }

    public function render($tpl, array $vars = array()) {
        $view = $this->getView();
        $view->render($tpl, $vars);
        $this->getResponse()->setBody($view->getContent());
    }

    public function renderJson($data = null, $msg = 'done', $code = 0) {
        $json = json_encode(['msg' => $msg, 'code' => $code, 'data' => $data], 
                JSON_UNESCAPED_UNICODE);
        header('Content-Type: application/json');
        $this->getResponse()->setBody($json);
    }
}
