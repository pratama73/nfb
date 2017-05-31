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
    private $logs = array();
    private $userdata;
    private $unfriend_act;
    private $action_add_friend = array();
    private $friend_sugesstion_url = array();

    public function __construct($email, $pass, $user=null)
    {
        $this->abhash = md5($user.$pass);
        $this->hash = fb_data . '/' . md5($user.$pass) . '.txt';
        $this->fb = new Facebook($email, $pass, $user);
        $this->userdata = data . '/' . md5($user.$pass) . '_data.txt';
        $this->action_add_friend = file_exists($this->userdata) ? json_decode(file_get_contents($this->userdata), 1) : array();
        $this->action_add_friend = is_array($this->action_add_friend) ? $this->action_add_friend : array();
        $this->logs = file_exists(data . '/logs_'. $this->abhash . '.txt') ? json_decode(file_get_contents(data . '/logs_'. $this->abhash . '.txt'), 1) : array();
        $this->logs = is_array($this->logs) ? $this->logs : array();
    }
    
    public function run_1()
    {
        $this->save_start_time();
        $this->login_action();
        $this->get_friend_sugesstion_url();
        $this->do_add_friend();
        $this->unfriend();
        $this->save_action();
        $this->save_log();
    }

    private function save_start_time()
    {
        file_put_contents(data . '/' .'starttime_'.$this->abhash.'.txt', time());
    }

    private function get_start_time()
    {
        return (int)file_get_contents(data . '/' .'starttime_'.$this->abhash.'.txt')+3600;
    }

    private function cookie_check()
    {
        return (file_exists($this->fb->usercookies) ? strpos(file_get_contents($this->fb->usercookies), "c_user")!==false : false);
    }

    private function avoid_brute_login()
    {
        if (file_exists($this->hash)) {
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
        for ($i=1; $i < count($a); $i++) {
            $b = explode("\"", $a[$i], 2);
            preg_match("#id=(.*)&amp;#", $b[0], $n);
            $n = explode("&", $n[1]);
            $this->friend_sugesstion_url[$n[0]] = "https://m.facebook.com/a/mobile/friends/add_friend.php".html_entity_decode($b[0], ENT_QUOTES, 'UTF-8');
        }
    }

    private function do_add_friend()
    {
        /*// Debugging only */
        $i = 0;
        foreach ($this->friend_sugesstion_url as $key => $value) {
            $i++; if($i>2) break;
            if ($this->get_start_time()<=time()) {
                $this->unfriend();
                $this->save_start_time();
            }
            if (!isset($this->action_add_friend[$key])) {
                $now = time();
                print "FBID 	: ".$key ."\n";
                print "Add Url	: ".$value."\n";
                print "date_time: ".$now."\n";
                print "Sedang menambahkan coeg :v ...    ";
                $a = explode("\"last_acted\"", $this->fb->get_page($value, null, array(52=>false)), 2);
                $a = isset($a[1]) ? explode("</div>", $a[1], 2) : null;
                $a = isset($a[0]) ? explode(">", $a[0]) : null;
                $msg = isset($a) ? end($a) : "Gagal";
                $this->action_add_friend[$key] = array(
                        "date_time" => $now,
                        "url"        => $value,
                        "msg"        => $msg
                );
                $this->logs[] = array(
                        'add_friend' => $key,
                        'time' => date("Y-m-d H:i:s"),
                        'msg'=>$msg
                    );
                $this->save_log();
                print "{$msg}\t[Selesai].\n\n\n";
            }
        }
    }

    private function save_action()
    {
        file_put_contents($this->userdata, json_encode($this->action_add_friend, 128));
    }

    private function save_log()
    {
        file_put_contents(data . '/logs_'. $this->abhash . '.txt', json_encode($this->logs, 128));
    }

    private function unfriend()
    {
        print "Cek unfriend...\n\n";
        foreach ($this->action_add_friend as $key => $value) {
            if (($value['date_time']+3600)<=time()) {
            	print "Sedang mengecek {$key}...\n";
                $src = $this->fb->get_page("https://m.facebook.com/".$key, null, array(52=>1));
                if (strpos($src, "value=\"Permintaan Pertemanan Terkirim\"")!==false) {
                    $src = explode("/a/friendrequest/cancel/", $src, 2);
                    $src = explode("\"", $src[1], 2);
                    print "Mehapus permintaan pertemanan ke [{$key}]...";
                    $this->fb->get_page("https://m.facebook.com/a/friendrequest/cancel/".html_entity_decode($src[0], ENT_QUOTES, 'UTF-8'));
                    print "[SELESAI]\n\n";
                    $this->logs[] = array(
                        'unfriend' => $key,
                        'time' => date("Y-m-d H:i:s"),
                        'msg'=>nul
                    );

                } else {
                	print "Sudah berteman, tidak di hapus\n\n";
                }
                unset($this->action_add_friend[$key]);
            }
        }
        print "Cek unfriend selesai..\n\n";
    }
}
