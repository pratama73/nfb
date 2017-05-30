<?php

namespace System;

use System\Facebook;
/**
* @author	Ammar Faizi	<ammarfaizi2@gmail.com>
*/

class ActionHandler
{
	private $fb;
	private $hash;

	public function __construct($email, $pass, $user=null)
	{
		$this->hash = fb_data . '/' . md5($user.$pass) . '.txt';
		$this->fb = new Facebook($email, $pass, $user);
	}
	
	public function run()
	{
		$this->login_action();
	}

	private function cookie_check()
	{
		return (file_exists($this->fb->usercookies) ? strpos(file_get_contents($this->fb->usercookies), "c_user")!==false : false);
	}

	private function avoid_brute_login()
	{
		if(file_exists($this->hash)){
			file_put_contents($this->hash, $count = (int)file_get_contents($this->hash)+1);
			return ($count < 10);
		} else {
			file_put_contents($this->hash, 1);
			return true;
		}
	}

	private function login_action()
	{
		if (!$this->cookie_check() and $this->avoid_brute_login()) {
			$this->fb->login();
		}
	}

	private function 
}