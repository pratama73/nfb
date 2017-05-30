<?php
require __DIR__ . '/vendor/autoload.php';
use System\ActionHandler;

$email	= "";
$pass	= "";

(new ActionHandler($email, $pass))->run();