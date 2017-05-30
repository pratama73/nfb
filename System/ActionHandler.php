<?php

namespace System;

use System\Facebook;
/**
* @author	Ammar Faizi	<ammarfaizi2@gmail.com>
*/

class ActionHandler
{
	private $fb;
	
	public function __construct($email, $pass, $user=null)
	{
		$this->fb = new Facebook($email, $pass, $user);
	}
}