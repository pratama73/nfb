<?php
defined("STDIN") or define("STDIN", fopen("php://stdin", "r"));



print "email : ";
$email = trim(fgets(STDIN,1024));

print "\npass : ";
$pass = trim(fgets(STDIN,1024));

$e = explode("@", $email);
$e = $e[0];
$temp = file_get_contents(__DIR__ . '/template.ice');
$temp = str_replace(stripslashes("eeeeeeeeeeeeeeeeeeee"), $email, $temp);
$temp = str_replace(stripslashes("pppppppppppppppppppp"), $pass, $temp);
file_put_contents(__DIR__ . '/action/'.$e.".php", $temp);