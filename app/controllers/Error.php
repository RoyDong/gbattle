<?php

use core\Controller;

class ErrorController extends Controller {

    public function errorAction($exception) {
        $this->renderJson(null, $exception->getMessage(), $exception->getCode());
    }
}
