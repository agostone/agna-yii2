<?php

define('AGNA_VENDOR_PATH', __DIR__ . '/../vendor');

$file = __DIR__.'/../vendor/autoload.php';

if (!file_exists($file)) {
    throw new \RuntimeException('Install composer dependencies to run test suite.');
}

// Adding test paths to Agna namespace
/* @var $loader \Composer\Autoload\ClassLoader */
$loader = require $file;
$loader->add('Agna\\', __DIR__ . '/../tests');

require_once __DIR__ . '/../library/Agna/Yii2/Yii2.php';