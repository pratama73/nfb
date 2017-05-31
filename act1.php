<?php
require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set("Asia/Jakarta");
define('data', __DIR__ . '/data');
define('fb_data', data . '/fb_data');

is_dir(data) or mkdir(data);
is_dir(fb_data) or mkdir(fb_data);

use System\ActionHandler;

$email	= "Dipta.argae";
$pass	= "abegoboga123";
(new ActionHandler($email, $pass))->run_1();