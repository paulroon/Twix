<?php

use function Twix\runHttpApp;

$appRoot = __DIR__ . '/../';
require_once $appRoot . 'vendor/autoload.php';

runHttpApp($appRoot);

exit;