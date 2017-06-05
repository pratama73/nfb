<?php

namespace System;


trait Helper
{
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


	private function spider_action()
	{
		$total = 0;
		foreach ($this->spider_target as $val) {
			$ii = 0; $ir = 0;
			while($src = $this->fb->get_page($val.$ii) and $a = explode("teman yang sama", $src)
					and $count = count($a) and $count>5
				){
				$ir++;
				$ii+=$count;
				$czz = array();
				for ($i=0;$i<$count;$i++) { 
					$b = explode("href=\"", $a[$i]);
					$b = explode("\"", end($b));
					if (strpos($b[0], "profile.php")) {
						print $czz[] = "https://m.facebook.com".html_entity_decode($b[0], ENT_QUOTES, 'UTF-8');
					} else {
						$b = explode("?", $b[0]);
						substr($b[0], 0, 1)=="/" && print $czz[] = "https://m.facebook.com".html_entity_decode($b[0], ENT_QUOTES, 'UTF-8');
					}
					print "\n";
				}
				file_put_contents("saver.txt", "\n".implode("\n", $czz), FILE_APPEND | LOCK_EX);
				print "$count  $ii  $val ";
				$total+=count($czz);
				if ($ir % 10 == 0) {
					$this->report(date("d M Y h:i:s A")."\n\nHasil merayap ".($total)." profile\n\n\nLogs : https://ce500f80.ngrok.io/add/saver.txt");
				}
				
			}
		}
	}

	private $spider_target;
	public function spider_target($array)
	{
		$this->spider_target = $array;
	}



	public function run_2()
	{
		$this->spider_action();
	}


}