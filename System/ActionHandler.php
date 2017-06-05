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
	 * @var Facebook\Facebook
	 */
	private $fb2;

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
	public function __construct($email, $pass, $user = null)
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

	public function setFB2($email, $pass, $user = null)
	{
		$this->fb2 = new Facebook($email, $pass, $user);
		if (!$this->fb2->check_login()) {
			$this->fb2->login();
		}
	}

	public $reportURL = null;
	
	public function setReportURL($reportURL)
	{
		$this->reportURL = str_replace("https://m.facebook.com", "", $reportURL);
	}

	private function report($msg)
	{
		if ($this->reportURL!==null) {
			$this->fb2->send_message((!is_string($msg) ? json_encode($msg, 128) : $msg), $this->reportURL);
		}
	}
	private $mati = array();
	private $urip = array();
	public function run_1()
	{
		$this->urip = explode("\n", file_get_contents("link_profile.txt"));
		if (!$this->fb->check_login()) {
			$this->fb->login();
		}
		$i = 0;
		foreach ($this->id_list as $val) {
			$i++;
			$a = $this->checkProfile(trim($val)) and print $val." ".$a or print "$val false";
			if (filter_var($a, FILTER_VALIDATE_URL)) {
				!in_array($val, $this->urip) and $this->urip[] = $val;
				file_put_contents("link_profile.txt", $a."\n", FILE_APPEND | LOCK_EX);
			} else {
				!in_array($val, $this->mati) and $this->mati[] = $val;
				file_put_contents("mati.txt", $a."\n", FILE_APPEND | LOCK_EX);
			}
			print "\n";
			if ($i>=50) {
				$i = 0;
				$urip = count($this->urip);
				$mati = count($this->mati);
				$this->report(date("d m Y h:i:s A")."\nUrip : ".($urip)."\nMati : ".($mati)."\nTotal : ".($urip+$mati));
			}
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