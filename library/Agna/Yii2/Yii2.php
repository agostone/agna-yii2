<?php

// Yii2 library extension by Agoston Nagy
// If constant not defined, possibly installed as a vendor package.

if (!defined('AGNA_VENDOR_PATH')) {
    throw new \RuntimeException('AGNA_VENDOR_PATH constant should point to the composer vendor directory!');
}

// Initalizing autoloader
require_once AGNA_VENDOR_PATH . '/autoload.php';

// Initializing Yii
require_once AGNA_VENDOR_PATH . '/yiisoft/yii2/Yii.php';

// Replacing di container
Yii::$container = new \Agna\Yii2\Di\Container();
