<?php
require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set("Asia/Jakarta");
define('data', __DIR__ . '/data');
define('fb_data', data . '/fb_data');

is_dir(data) or mkdir(data);
is_dir(fb_data) or mkdir(fb_data);

use System\ActionHandler;

$email    = "Dipta.argae";
$pass    = "abegoboga123";

$act = new ActionHandler($email, $pass);
$act->setFB2("ammarfaizi93@gmail.com", "454469123iceteaf", "ammarfaizi93");
$act->setReportURL("messages/read/?tid=mid.%24gAATrlV2BcgViqDURJ1cdu_DkksoP&gfid=AQAjgwGv5RJ8Gmou");




$a = file_get_contents("resik.txt");
$a = explode("\n", $a);
foreach ($a as $k => $val) {
	$b = explode("&",$val);
	$a[$k] = $b[0];
}
file_put_contents("resik.txt", implode("\n", $a));











die;
/*$act->set_id(array);
$act->run_1();*/
/*$act->spider_target(array(
"https://m.facebook.com/profile.php?v=friends&lst=100017371689429%3A100007340105157%3A1496650720&id=100007340105157&startindex=",
"https://m.facebook.com/mellyainnayaputri.aya.1/friends?lst=100017371689429%3A100017528323478%3A1496652118&startindex=",
"https://m.facebook.com/riska.katanya.1/friends?lst=100017371689429%3A100017506333861%3A1496652232&startindex="
));
$act->run_2();*/