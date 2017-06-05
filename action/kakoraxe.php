<?php
require __DIR__ . '/../vendor/autoload.php';
define('data', realpath(__DIR__ . '/../data'));
define('fb_data', data . '/fb_data');
is_dir(data) or mkdir(data);
is_dir(fb_data) or mkdir(fb_data);




date_default_timezone_set("Asia/Jakarta");

use System\ActionHandler;

$email    = "kakoraxe@refurhost.com";
$pass     = "kakoraxe6152";


	require __DIR__ . '/../fb2handler.php';
	$act = new ActionHandler($email, $pass);
	$act->setFB2($email2, $pass2, $user2);
	$act->setReportURL($report_url);
	$act->setTargetLink($target);
	$act->run_1();
