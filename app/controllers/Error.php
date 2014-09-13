<?php

class ErrorController extends BaseController {

    public function errorAction($exception) {
        $this->renderJson(null, $exception->getMessage(), $exception->getCode());
    }
}
