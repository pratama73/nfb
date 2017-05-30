<?php

namespace System;

use System\Facebook;

/**
* @author	Ammar Faizi	<ammarfaizi2@gmail.com>
* Script iki digawe angel banget :v
* Jangan lupa bahagia kawanku :v
*/

class ActionHandler
{
	private $fb;
	private $hash;
	private $userdata;
	private $action_add_friend = array();
	private $friend_sugesstion_url = array();

	public function __construct($email, $pass, $user=null)
	{
		date_default_timezone_set("Asia/Jakarta");
		$this->hash = fb_data . '/' . md5($user.$pass) . '.txt';
		$this->fb = new Facebook($email, $pass, $user);
		$this->userdata = data . '/' . $this->hash . '_data.txt';
	}
	
	public function run()
	{
		$this->login_action();
		$this->get_friend_sugesstion_url();
		$this->save_action();
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

	private function get_friend_sugesstion_url()
	{
		# $a = file_get_contents('a.tmp');
		$a = $this->fb->get_page("https://m.facebook.com/friends/center/suggestions");
		$a = explode("/a/mobile/friends/add_friend.php", $a);
		$this->friend_sugesstion_url = array();
		for ($i=1; $i < count($a); $i++) { 
			$b = explode("\"", $a[$i], 2);
			preg_match("#id=(.*)&amp;#", $b[0], $n);
			$n = explode("&", $n[1]);
			$this->friend_sugesstion_url[$n[0]] = "https://m.facebook.com/a/mobile/friends/add_friend.php".html_entity_decode($b[0], ENT_QUOTES, 'UTF-8');
		}
		print_r($this->friend_sugesstion_url);
	}

	private function do_add_friend()
	{
		/*// Debugging only */
		$i = 0;
		foreach ($this->friend_sugesstion_url as $key => $value) {
			$i++; if($i>5) break;
			if (!isset($this->action_add_friend[$key])) {
				$this->get_page($value, null, array(52=>false));
				$this->action_add_friend[$key] = array(
						"time_add" => time()
				);
			}
		}
	}

	private function save_action()
	{
		print_r($this->action_add_friend);
		file_put_contents($this->userdata, json_encode($this->action_add_friend, 128));
	}
}
