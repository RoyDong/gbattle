<?php


use Yaf\Controller_Abstract as Controller;
use model\Base as Model;

class BaseController extends Controller {

    protected $yafAutoRender = false;

    public function get($name, $default = null) {
        $val = $this->getRequest()->getParam($name, $default);
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

    /**
     * 
     * @param string $name
     * @return Model
     */
    public function model($name) {
        return Model::instance($name);
    }

    public function render($tpl, array $vars = array()) {
        $view = $this->getView();
        $view->render($tpl, $vars);
        $this->getResponse()->setBody($view->getContent());
    }

    public function renderJson($data = null, $msg = 'ok', $code = 0) {
        $json = json_encode(['msg' => $msg, 'code' => $code, 'data' => $data], 
                JSON_UNESCAPED_UNICODE);
        header('Content-Type: application/json');
        $this->getResponse()->setBody($json);
    }
}
