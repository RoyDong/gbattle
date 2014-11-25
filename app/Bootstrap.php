<?php

class Bootstrap extends Yaf\Bootstrap_Abstract {

	public function _init(Yaf\Dispatcher $dispatcher) {
        $conf = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('conf', $conf);

        $env = Yaf\Application::app()->environ();
        $routes = new Yaf\Config\Ini(APP_PATH . '/conf/routes.ini', $env);
        $dispatcher->getRouter()->addConfig($routes);

        $db = new Yaf\Config\Ini(APP_PATH . '/conf/db.ini', $env);
        Yaf\Registry::set('db', $db);

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
