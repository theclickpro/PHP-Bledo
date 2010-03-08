<?php
namespace tcpro\fw;

use \tcpro\Fw as Fw;

class Controller
{
	/**
	 * \tcpro\fw\View Class
	 *
	 * @var \tcpro\fw\View
	 */
	public $view = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// initialize view
		$this->initView();

		$actionView = Fw::getControllerName() .'/'. Fw::getActionName() .'.php';
		$actionFile = Fw::$viewDirectory . '/' . $actionView;
		if (is_file($actionFile))
		{
			$this->view->assignTpl('main',  $actionFile);
		}
	}
	
	public function runController()
	{
		/*
		 * Run Actiokn
		 */
		$action = Fw::getActionName(true);
		if (!method_exists(Fw::$Controller, $action))
		{
			throw new \tcpro\fw\Exception($action.' does not exist', 404);
		}

		$this->$action(); // run action
		$this->show(); // show xhtml
	}

	/**
	 * Init View Class
	 */
	protected function initView()
	{
		$this->view		= new \tcpro\fw\View();
		$this->view->title	= '';
		$this->view->main	= '';
		$this->view->head	= '';
	}


	/**
	 * Empty index action
	 *
	 */
	public function indexAction()
	{
	}


	/**
	 * Renders the page
	 * 
	 */
	private function show()
	{
		$this->view->processSubTpls();
		$this->view->render(Fw::$viewDirectory . '/template.php');
	}

}
