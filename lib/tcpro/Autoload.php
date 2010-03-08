<?php
namespace tcpro;
class Autoload
{
	public static function load($class)
	{
		$class = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $class), '//');
		@include($class.'.php');
	}
}

/*
 *    Autoload Framework
 */
spl_autoload_register(array('\tcpro\Autoload', 'load'));

