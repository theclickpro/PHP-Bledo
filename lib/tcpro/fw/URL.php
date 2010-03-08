<?php
namespace tcpro\fw;
class URL
{
	private static $baseUrl = null;
	private static $friendlyUrl = true;
	private static $controllerUrlKey = '_controller';
	private static $actionMethodUrlKey = '_action';


	private $controller;
	private $action;
	private $params;

	public function __construct($controller='index', $action='index', $params=array())
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->params = $params;
	}

	public function __toString()
	{
		return self::href($this->controller, $this->action, $this->params);
	}


	/**
	  * Returns URL formated for redirects
	  *
	  * @param string $controller
	  * @param string $action
	  * @param array[string]string $params
	  * @return string
	  */
	public function rd($controller='index', $action='index', $params=array())
	{
		return self::buildUrl($controller, $action, $params, '&');
	}


	/**
	  * Returns URL formated for redirects
	  *
	  * @param string $controller
	  * @param string $action
	  * @param array[string]string $params
	  * @return string
	  */
	public static function href($controller='index', $action='index', $params=array())
	{
		return self::buildUrl($controller, $action, $params, '&amp;');
	}


	 /**
         * Helper Alias function to \tcpro\Fw::href()
         *
         * @param string $controller
         * @param string $action
         * @param array[string]string $params
         * @param string $glue Defaults to '&'
         * @return string
         */
        public static function buildUrl($controller='index', $action='index', $params=array(), $glue='&')
        {
                if (self::$friendlyUrl)
                {
                        $url = self::getBaseUrl().'/'.$controller.'/'.$action;
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

                return self::getBaseUrl().'/index.php?'.http_build_query($url, 'n', $glue);
        }

	/**
	  *
	  * @return string
	  */
	public function getBaseUrl()
	{
		if (self::$baseUrl)
		{
			return self::$baseUrl;
		}

		$url = $_SERVER['SCRIPT_NAME'];
		$arr = explode('/', $url);
		$script = array_pop($arr);
		$url = mb_str_replace('/'.$script, '', $url);
		self::$baseUrl = $url;
		return $url;
	}

	/**
	  * @param string $url
	  */
	public function setBaseUrl($url)
	{
		self::$baseUrl = $url;
	}

	/**
	  * @param bool $friendly
	  */
	public function setFriendlyUrl($friendly=true)
	{
		self::$friendlyUrl = $friendly;

	}
}

