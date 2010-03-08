<?php
namespace tcpro\fw;

class View
{
	/**
	 * Holds all the tag values
	 *
	 * @var array[string]mixed
	 */
	public $_tagValues = array();
	
	/**
	 * Holds sub templates
	 *
	 * @var array[string]mixed
	 */
	public $_subTpls = array();

	/**
	 * access to properties
	 *
	 * @param string $k
	 * @return mixed
	 */
	function __get($k)
	{
		if(array_key_exists($k, $this->_tagValues))
		{
			return $this->_tagValues[$k];
		}

		return null;
	}

	/**
	 * Sets properties
	 *
	 * @param string $k
	 * @param mixed $v
	 */
	function __set($k, $v)
	{
		$this->_tagValues[$k] = $v;
		unset($this->_subTpls[$k]);
	}
	
	/**
	 * Process sub templates
	 *
	 */
	function processSubTpls()
	{
		foreach ($this->_subTpls as $k => $f)
		{
			$this->_tagValues[$k] = $this->fetch($f);
		}
		$this->_subTpls = array();
	}

	/**
	 * Fetches template file
	 *
	 * @param string $viewFile /path/to/file_name
	 * @return string
	 */
	function fetch($viewFile=null)
	{
		ob_start();
		if (empty($viewFile))
		{
			throw new \tcpro\fw\view\Exception("file name is required");
		}

		include($viewFile);


		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Echoes template
	 *
	 * @param string $viewFile /path/to/file_name
	 */
	function render($viewFile)
	{
		$this->processSubTpls();
		echo $this->fetch($viewFile);
	}

	/**
	 * Deprecated... use properties directly. this method is here for
	 * compatibility with Framework 1
	 *
	 * @param string $k Property/Tag name
	 * @param mixed $v Value
	 */
	function assign($k, $v)
	{
		$this->_tagValues[$k] = $v;
		unset($this->_subTpls[$k]);
	}
	
	/**
	 * Appends to an already existing string
	 *
	 * @param string $k Property/Tag name
	 * @param string $v Value
	 */
	function append($k, $v)
	{
		@$this->_tagValues[$k] .= $v;
		unset($this->_subTpls[$k]);
	}
	
	/**
	 * Prepends to an already existing string
	 *
	 * @param string $k Property/Tag name
	 * @param string $v Value
	 */
	function prepend($k, $v)
	{
		$this->_tagValues[$k] = $v . @$this->_tagValues[$k];
		unset($this->_subTpls[$k]);
	}

	/**
	 * Assigns the content of a template to a tag/property
	 *
	 * @param string $k Property/Tag name
	 * @param string $v /path/to/template.php
	 */
	function assignTpl($k, $v)
	{
		$this->_subTpls[$k] = $v;
		unset($this->_tagValues[$k]);
	}

	
	/**
	 * Helper Alias function to \tcpro\Fw::href() 
	 *
	 * @param string $str Local URL
	 * @return string
	 */
	function href($str)
	{
		return \tcpro\Fw::href($str);
	}
}
