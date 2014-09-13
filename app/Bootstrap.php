<?php

class Bootstrap extends Yaf\Bootstrap_Abstract {

	public function _init(Yaf\Dispatcher $dispatcher) {
        $conf = Yaf\Application::app()->getConfig();
        $dispatcher->getRouter()->addConfig($conf->routes);

        $view = new core\Template($conf->application->view->ext);
        $view->setScriptPath(APP_PATH . '/app/views');
        $dispatcher->setView($view);
    }

	public function _initPlugin(Yaf\Dispatcher $dispatcher) {
		$dispatcher->registerPlugin(new MainPlugin);
	}
}
