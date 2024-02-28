<?php

use Twix\Twix;

$appRoot = __DIR__ . '/../';

require_once $appRoot . 'vendor/autoload.php';

Twix::boot($appRoot)
    ->http()
    ->run();

exit;