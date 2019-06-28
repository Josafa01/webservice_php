<?php
namespace Controllers;

use \Core\Controller;

class NotfoundController extends Controller 
{

	public function index() 
	{
		$this->return_json(array());
	}
}
