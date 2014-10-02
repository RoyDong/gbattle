<?php

class Bootstrap extends Yaf\Bootstrap_Abstract {

	public function _init(Yaf\Dispatcher $dispatcher) {
        $conf = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('conf', $conf);
        $dispatcher->getRouter()->addConfig($conf->routes);

        Yaf\Loader::import(APP_PATH . '/app/core/main.php');
        foreach(glob(APP_PATH.'/app/functions/*.php') as $file) {
            Yaf\Loader::import($file);
        }

		$dispatcher->registerPlugin(new MainPlugin);
    }

    public function _initView(Yaf\Dispatcher $dispatcher) {
        $view = new core\Template();
        $view->setScriptPath(APP_PATH . '/app/views');
        $dispatcher->setView($view);
    }
}
