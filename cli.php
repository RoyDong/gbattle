<?php
define('APP_PATH', __DIR__); 
(new Yaf\Application(APP_PATH . '/conf/app.ini'))
    ->bootstrap()
    ->getDispatcher()
    ->dispatch(new Yaf\Request\Simple());
