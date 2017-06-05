<?php

namespace System;

use Facebook\Facebook;

/**
 *
 * @author	Ammar Faizi	<ammarfaizi2@gmail.com>
 *
 */

class ActionHandler
{
	/**
	 *
	 * @var	Facebook\Facebook
	 */
	private $fb;

	/**
	 *
	 * @var	array
	 */
	private $id_list = array();

	/**
	 *
	 * Constructor
	 *
	 * @param	string	$email
	 * @param	string	$pass
	 * @param	string	$user
	 */
	public function __construct($email, $pass, $user=null)
	{
		$this->fb = new Facebook($email, $pass, $user);
		
	}

	public function run_1()
	{
		if (!$this->fb->check_login()) {
			$this->fb->login();
		}
		foreach ($this->id_list as $val) {
			$a = $this->checkProfile($val) and print $a or print "false";
			print "\n";
		}
	}

	/**
	 *
	 * @param	string	$id
	 * @return	mixed
	 */
	private function checkProfile($id)
	{
		$this->fb->get_page("https://www.facebook.com/".trim($id), null, array(52=>0));
		if (isset($this->fb->curl_info['redirect_url']) && !empty($this->fb->curl_info['redirect_url'])) {
			return str_replace("https://www.", "https://m.", $this->fb->curl_info['redirect_url']);
		} else {
			return false;
		}
	}

}