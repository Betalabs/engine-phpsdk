<?php

require_once __DIR__ .'/../../vendor/autoload.php';
require_once 'Listener.php';

$container = \DI\ContainerBuilder::buildDevContainer();

$listener = $container->get(\Betalabs\Engine\Listener::class);
$listener->listen();