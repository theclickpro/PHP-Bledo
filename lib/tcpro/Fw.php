<?php
namespace tcpro;

class Fw
{
	public static $baseUrl = null;
	public static $defaultController = 'index';
	public static $defaultAction = 'index';

	public static $controllerUrlKey = '_controller';
	public static $controllerNamespace = '\controller';

	public static $viewDirectory = 'view';


	public static $actionMethodUrlKey = '_action';

	public static $classExt = '.php';

	public static $friendlyUrl = true;

	public static $Controller = null;


	private function __construct()
	{
		//throw new \tcpro\fw\Exception('Private Constructor');
	}

	public static function getActionName($fullActionName=false)
	{
		$action = empty($_GET[self::$actionMethodUrlKey]) ? 'index' : strtolower($_GET[self::$actionMethodUrlKey]);

		if ($fullActionName)
		{
			$action .= 'Action';
		}

		return $action;
	}

	public static function getControllerName($properName=false)
	{
		$controller = empty($_GET[self::$controllerUrlKey]) ? 'index' : $_GET[self::$controllerUrlKey];

		if ($properName)
		{
			$controller = ucfirst($controller);
		}

		return $controller;
	}

	protected function arrayStripSlashes($array)
	{
		return is_array($array) ? array_map(array('Fw', 'arrayStripSlashes'), $array) : stripslashes($array);
	}

	/**
	  * Disables Magic quotes if enabled
	  */
	public static function disableMagicQuotes()
	{
		if (get_magic_quotes_gpc())
		{
			if (!self::$friendlyUrl)
			{
				$_GET	= self::arrayStripSlashes($_GET);
			}

			$_COOKIE	= self::arrayStripSlashes($_COOKIE);
			$_FILES		= self::arrayStripSlashes($_FILES);
			$_POST		= self::arrayStripSlashes($_POST);
			$_REQUEST	= self::arrayStripSlashes($_REQUEST);
		}
	}

	
	/**
	  * Returns URI without query string
	  */
	public static function getRequestPath()
	{
		$pos = strstr($_SERVER['REQUEST_URI'], '?');
		if ($pos !== false)
		{
			return substr_replace($_SERVER['REQUEST_URI'], '', $pos);
		}

		return $_SERVER['REQUEST_URI'];
	}

	/**
	  * Reconstruct Friendly links
	  */
	public static function handleFriendlyUrl()
	{
		if (self::$friendlyUrl == true)
		{
			$url = trim(self::getRequestPath(), '/');
			$urlArr = explode('/', $url);

			if (isset($urlArr[0]))
			{
				$_GET[self::$controllerUrlKey] = urldecode($urlArr[0]);
				unset($urlArr[0]);
			}
			if (isset($urlArr[1]))
			{
				$_GET[self::$actionMethodUrlKey] = urldecode($urlArr[1]);
				unset($urlArr[1]);
			}

			$urlArr = array_values($urlArr);
			$j = count($urlArr);
			for($i=0; $i < $j; $i+=2)
			{
				@$_GET[$urlArr[$i]] = urldecode($urlArr[$i+1]);
			}
		}
	}
	
	public static function baseUrl()
	{
		if (self::$baseUrl)
		{
			return self::$baseUrl;
		}

		$url = $_SERVER['SCRIPT_NAME'];
		$arr = explode('/', $url);
		$script = array_pop($arr);
		$url = str_replace('/'.$script, '', $url);
		self::$baseUrl = $url;
		return $url;
	}

	public static function sessionStart()
	{
		session_start();
	}


	public static function handleController()
	{
		/*
		 * Run requested module
		 *
		 * clals names must start with upper case letter
		 *
		 */
		$controller = self::getControllerName(true);

		//make sure controller call is alpha numeric
		if (preg_match('/[^A-Za-z0-9]/', $controller))
		{
			throw new \tcpro\fw\Exception($controller. ' Controller not found');
		}


		// Add namespace to plain class name
		$controller = self::$controllerNamespace. '\\'.  $controller;

		/*
		 * Construct Controller
		 */
		try
		{
			if (!class_exists($controller, true))
			{
				$errorController = self::$controllerNamespace. '\\Error';
				self::$Controller = new $errorController();
				self::$Controller->handleError(new Exception($controller . ' Controller Not Found'));
			}
			else
			{
				self::$Controller = new $controller();
			}

		}
		catch(Exception $e)
		{
			$errorController = self::$controllerNamespace. '\\Error';
			self::$Controller = new $errorController();
			self::$Controller->handleError(new Exception($controller . ' Controller Not Found'));
		}
	}

	public static function run()
	{
		self::baseUrl(); // set baseurl


		self::disableMagicQuotes();
		self::handleFriendlyUrl();
		self::sessionStart();

		self::handleController();

		self::$Controller->runController();
	}


	/**
	 * Returns an array given a url no matter the format
	 * 
	 * @param string $url
	 * @return array
	 */
	public static function parseUrl($url)
	{
		// init
		$get = array();

		// provided url is friendly or not ?
		if (strstr($url, '&') || strstr($url, '?'))
		{
			$friendly = false;
		}
		else
		{
			$friendly = true;
		}


		// parse urls
		if ($friendly)
		{
			$url = str_replace(self::$baseUrl, '', $url);
			$url = trim($url, '/');
			$urlArr = explode('/', $url);

			if (!empty($urlArr[0]))
			{
				$get[self::$controllerUrlKey] = urldecode($urlArr[0]);
				unset($urlArr[0]);
			}
			else
			{
				return $get; //empty array
			}

			if (isset($urlArr[1]))
			{
				$get[self::$actionMethodUrlKey] = urldecode($urlArr[1]);
				unset($urlArr[1]);
			}

			$urlArr = array_values($urlArr);
			$j = count($urlArr);
			for($i=0; $i < $j; $i+=2)
			{
				@$get[$urlArr[$i]] = urldecode($urlArr[$i+1]);
			}
		}
		else
		{
			$url = str_replace(array('&amp;', '?'), array('&',''), $url);
			parse_str($url, $get);
		}

		//return array
		return $get;
	}

	 /**
         * Helper Alias function to Fw::href()
         *
         * @param string $controller
         * @param string $action
         * @param array $params
         * @return string
         */
        public static function buildUrl($controller='index', $action='index', $params=array(), $glue='&')
        {
                if (self::$friendlyUrl)
                {
                        $url = self::baseUrl().'/'.$controller.'/'.$action;
                        foreach ($params as $k => $v)
                        {
				if ($k == self::$controllerUrlKey || $k == self::$actionMethodUrlKey || empty($v))
				{
					continue;
				}

                                $url .= '/'.urlencode($k).'/'.urlencode($v);
                        }

                        return $url;
                }

                $url = array();

                $url[self::$controllerUrlKey] = $controller;
                $url[self::$actionMethodUrlKey] = $action;
                $url = array_merge($url, $params);

                return self::baseUrl().'/index.php?'.http_build_query($url, 'n', $glue);
        }



	/**
	 * Parses URL and redirects based on
	 * Friendly URL value
	 *
	 * @param string $url
	 */
	public static function redirect($url)
	{
		$urlArr = self::parseUrl($url);

		//$retUrl = self::buildUrl($urlArr, '&');
		$retUrl = self::buildUrl(@$urlArr[self::$controllerUrlKey], @$urlArr[self::$actionMethodUrlKey], $urlArr, '&');

		header('Location: '. $retUrl);
		exit;
	}

	/**
	 * Parses and converts URL based on
	 * Friendly URL or not
	 *
	 * @param string $url
	 * @return string
	 */
	public static function href($url)
	{
		$urlArr = self::parseUrl($url);

		$retUrl = self::buildUrl($urlArr[self::$controllerUrlKey], $urlArr[self::$actionMethodUrlKey], $urlArr, '&amp;');

		return $retUrl;
	}

	/**
	 * Raw Redirect.  Useful for 3rd party redirects
	 * that don't need to be parsed
	 *
	 * @param string $url
	 */
	public static function rawRedirect($url)
	{
		header('Location: '. $url);
		exit;
	}
}
