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


$act->set_id(
explode("\n","78202902654611
120490921783007
267530960288518
119097048581841
107248099777348
165173200627349
209928782799402
221286188281145
1020220494711014
227652507680463
174035676313122
216151822176307
240410056360943
138644529960585
163261137489348
145992512445609
266115497102772"));


$act->run_1();