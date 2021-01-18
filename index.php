<?php

use Laminas\Mvc\Application;

\define('BASE_PATH', __DIR__);

\chdir(BASE_PATH);

require_once BASE_PATH . '/vendor/autoload.php';

$app = Application::init(require BASE_PATH . '/config/app.config.php');

$app->run();
