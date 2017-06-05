<?php

namespace System;

use Facebook\Facebook;

date_default_timezone_set("Asia/Jakarta");

/**
 *
 * @author	Ammar Faizi	<ammarfaizi2@gmail.com>
 *
 */

class ActionHandler
{
	const MDATA = "/data/";

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
		$this->fb      	= new Facebook($email, $pass, $user);
		$this->data	 	= data.self::MDATA;
		is_dir($this->data) or mkdir($this->data);
	}

	/**
	 *
	 * @param	array
	 */
	public function set_id($id_list)
	{
		$this->id_list = $id_list;
	}


	public function run_1()
	{
		$this->id_list	= shuffle($this->id_list);
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

	/**
	 *
	 * @param	string	$url
	 * @return	array
	 */
	private function addFriend($url)
	{
		$src = $this->fb->get_page($url);

		if (strpos($src, "/a/mobile/friends/profile_add_friend.php")!==false) {
			$a = explode("/a/mobile/friends/profile_add_friend.php", $src, 2);
			$a = explode("\"", $a[1], 2);
			$src = $this->fb->get_page("https://m.facebook.com/a/mobile/friends/profile_add_friend.php".html_entity_decode($a[0], ENT_QUOTES, 'UTF-8'), null, array(CURLOPT_REFERER=>$url));
			$r = array(true, "Permintaan terkirim");
		} else
		if (strpos($src, "removefriend.php")!==false) {
			$r = array(false, "Sudah berteman.");
		} else {
			$r = array(false, "Tidak dapat mengirim permintaan pertemanan.");
		}


		return array(
					"add"  => $r[0],
					"msg"  => $r[1],
					"time" => date("d m Y h:i:s A")
				);
	}
}