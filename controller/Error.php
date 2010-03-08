<?php
namespace controller;

class Error extends \tcpro\fw\Controller
{
	public function handleError(\Exception $e)
	{
		$this->view->main = $e->getMessage();
	}
}
