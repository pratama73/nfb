<?php

$a = scandir(__DIR__ . "/action");
unset($a[0], $a[1]);

foreach ($a as $val) {
	exec("c:/xampp/php/php.exe action/".$val);
}