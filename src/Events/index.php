<?php

require_once __DIR__ .'/../../vendor/autoload.php';
require_once 'Listener.php';

$listener = new \Betalabs\Engine\Listener();
$listener->listen();