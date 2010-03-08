<?php
namespace controller;

class Index extends \tcpro\fw\Controller
{
	public function indexAction()
	{
		$this->view->main = 'Hello World!';
	}
}
